<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangTitipan;
use App\Models\Kategori;
use App\Models\Pegawai;
use App\Models\Penitip;
use App\Models\FotoBarang;
use App\Models\NotaPenitipan;
use Carbon\Carbon;
use Illuminate\Http\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use PDF;

class BarangTitipanController extends Controller
{

    public function showMobile(Request $request, $id = null)
    {
        try {
            // Mendapatkan query pencarian dan category_id dari request
            $search = $request->query('search');
            $categoryId = $request->query('category_id');

            // Menambahkan log untuk melihat nilainya
            Log::info('Search query: ' . ($search ?: 'Tidak ada nilai pencarian'));
            Log::info('Category ID: ' . ($categoryId ?: 'Tidak ada kategori'));

            if ($id) {
                // Jika ada ID, tampilkan detail produk
                $product = BarangTitipan::with('fotoBarang', 'kategori')
                    ->where('id_barang', $id)
                    ->where('status_barang', 'Tersedia') // Memastikan hanya menampilkan barang dengan status "Tersedia"
                    ->firstOrFail();

                return response()->json([
                    'success' => true,
                    'data' => $product
                ]);
            } else {
                if (empty($search)) {
                    $search = 'Tidak ada nilai pencarian';  // Menetapkan nilai default jika tidak ada pencarian
                }

                $productsQuery = BarangTitipan::with('fotoBarang', 'kategori');

                // Jika search tidak kosong, lakukan pencarian berdasarkan nama
                if ($search !== 'Tidak ada nilai pencarian') {
                    $productsQuery->where('nama_barang', 'like', "%{$search}%");
                }

                // Jika categoryId ada dan tidak sama dengan 'Semua', filter berdasarkan kategori
                if ($categoryId && $categoryId != 'Semua') {
                    $productsQuery->where('id_kategori', $categoryId);
                }

                // Menambahkan filter untuk hanya menampilkan barang dengan status "Tersedia"
                $productsQuery->where('status_barang', 'Tersedia');

                // Paginate the results
                $products = $productsQuery->paginate(10);  // Limiting results to 10 per page

                return response()->json([
                    'success' => true,
                    'data' => $products
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error fetching product: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Gagal mengambil produk.'
            ], 500);
        }
    }
    public function kirimNotifikasiMasaPenitipan()
    {
        $today = Carbon::today();
        $h3 = $today->copy()->addDays(3);

        $barangs = Barang::with('penitip')->whereNotNull('tanggal_akhir')->get();

        foreach ($barangs as $barang) {
            $penitip = $barang->penitip;

            if (!$penitip || !$penitip->fcm_token) {
                continue;
            }

            $tanggalAkhir = Carbon::parse($barang->tanggal_akhir);
            $sudahPerpanjang = $barang->status_perpanjangan == 1;

            if ($tanggalAkhir->isSameDay($h3) && !$barang->notifikasi_h3_terkirim) {
                $penitip->kirimNotifikasiMasaPenitipan('h-3', $barang->nama_barang, $barang->kode_barang, $sudahPerpanjang);

                $barang->notifikasi_h3_terkirim = true;
                $barang->save();
            }

            if ($tanggalAkhir->isSameDay($today) && !$barang->notifikasi_hari_h_terkirim) {
                $penitip->kirimNotifikasiMasaPenitipan('hari-h', $barang->nama_barang, $barang->kode_barang, $sudahPerpanjang);

                $barang->notifikasi_hari_h_terkirim = true;
                $barang->save();
            }
        }

        return response()->json(['message' => 'Notifikasi masa penitipan sudah dikirim']);
    }
    
    public function apishow($id)
    {
        $produk = BarangTitipan::with(['kategori','fotoBarang'])
                    ->findOrFail($id);

        return response()->json([
            'data' => $produk
        ]);
    }

    public function Show($id)
    {
        $produk = BarangTitipan::findOrFail($id);
        return view('produk.show', compact('produk'));
    }

    public function search(Request $request)
    {
        $query = $request->input('search');  // Ambil kata kunci pencarian dari parameter 'search'

        // Cari produk berdasarkan nama barang dan hanya menampilkan barang yang statusnya 'Tersedia'
        $produk = BarangTitipan::where('nama_barang', 'like', '%' . $query . '%')
            ->where('status_barang', 'Tersedia')  // Menambahkan filter untuk hanya barang yang tersedia
            ->get();

        // Mengirim data produk yang ditemukan ke view
        return view('kategori', compact('produk'));
    }

    public function indexPenitip(Request $request)
    {
        $penitip = auth()->guard('penitip')->user();

        $query = BarangTitipan::with('fotoBarang', 'kategori')
                    ->where('id_penitip', $penitip->id_penitip);

        $totalBarang = BarangTitipan::where('id_penitip', $penitip->id_penitip)->count();
        $totalBarangBelumLaku = BarangTitipan::where('id_penitip', $penitip->id_penitip)
                ->where('status_barang', 'Tersedia')
                ->count();


        if ($request->has('q') && $request->q != '') {
            $search = $request->q;
            $query->where('nama_barang', 'like', "%$search%");
        }

        $barangs = $query->paginate(10);

        return view('dashboardP', compact('barangs', 'totalBarang', 'totalBarangBelumLaku'));
    }

    public function perpanjang($id)
    {
        $barang = BarangTitipan::where('id_barang', $id)
            ->where('id_penitip', auth()->guard('penitip')->id())
            ->firstOrFail();

        if (!$barang->status_perpanjangan && Carbon::parse($barang->tanggal_akhir)->isPast()) {
            $barang->tanggal_akhir = Carbon::parse($barang->tanggal_akhir)->addDays(30);
            $barang->status_perpanjangan = true;
            $barang->save();

            return redirect()->back()->with('success', 'Perpanjangan masa penitipan berhasil dilakukan.');
        }

        return redirect()->back()->with('success', 'Barang sudah pernah diperpanjang atau belum melewati tanggal akhir.');
    }

    public function ambilBarang($id)
    {
        $barang = BarangTitipan::where('id_barang', $id)
                    ->where('id_penitip', auth()->guard('penitip')->id())
                    ->firstOrFail();

        if ($barang->status_barang === 'Tersedia') {
            $tanggalAkhir = \Carbon\Carbon::parse($barang->tanggal_akhir);
            $hariSekarang = now();
            $hariLewat = $hariSekarang->diffInDays($tanggalAkhir, false) * -1;
            $batasAmbil = 7;

            // Cek jika dalam window pengambilan 7 hari
            if ($hariLewat > 0 && $hariLewat <= $batasAmbil) {
                $barang->status_barang = 'Pengambilan Diproses';
                $barang->save();

                return redirect()->back()->with('success', 'Barang berhasil diambil kembali.');
            } else {
                return redirect()->back()->with('error', 'Waktu pengambilan sudah lewat.');
            }
        }

        return redirect()->back()->with('error', 'Status barang tidak memungkinkan untuk diambil.');
    }   
    
    // Daftar Barang Pengembalian
    public function daftarPengembalian(Request $request)
    {
        $query = BarangTitipan::with(['penitip'])
            ->where('status_barang', 'Pengambilan Diproses');

        $search = $request->input('search');
        if (!empty($search)) {
            $searchLower = strtolower(trim($search));

            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%$search%")
                    ->orWhere('id_barang', 'like', "%$search%")
                    ->orWhere('deskripsi', 'like', "%$search%")
                    ->orWhere('harga_jual', 'like', "%$search%")
                    ->orWhere('berat', 'like', "%$search%")
                    ->orWhere('tanggal_masuk', 'like', "%$search%")
                    ->orWhere('tanggal_akhir', 'like', "%$search%")
                    ->orWhere('tanggal_keluar', 'like', "%$search%")
                    ->orWhere('status_barang', 'like', "%$search%")
                    ->orWhere('garansi', 'like', "%$search%")
                    ->orWhere('tanggal_garansi', 'like', "%$search%")
                    ->orWhere('status_perpanjangan', 'like', "%$search%")
                    ->orWhere('barang_hunter', 'like', "%$search%")
                    ->orWhere('id_pegawai', 'like', "%$search%")
                    ->orWhereHas('penitip', function ($p) use ($search) {
                        $p->where('nama_penitip', 'like', "%$search%");
                    });
            });

            // Optional: filter berdasarkan nama bulan
            $bulanMap = [
                'januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4,
                'mei' => 5, 'juni' => 6, 'juli' => 7, 'agustus' => 8,
                'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12
            ];

            if (isset($bulanMap[$searchLower])) {
                $query->orWhereMonth('tanggal_masuk', $bulanMap[$searchLower]);
            }

            // Optional: filter berdasarkan X hari terakhir
            if (preg_match('/(\d+)\s*hari/', $searchLower, $matches)) {
                $jumlahHari = (int) $matches[1];
                $tanggalBatas = now()->subDays($jumlahHari);
                $query->orWhere('tanggal_masuk', '>=', $tanggalBatas);
            }

            // Optional: filter dengan format Kode Barang seperti L29, G10
            if (preg_match('/^([A-Za-z])(\d+)$/', $search, $match)) {
                $huruf = strtoupper($match[1]);
                $angka = $match[2];

                $query->orWhere(function ($q) use ($huruf, $angka) {
                    $q->where('id_barang', $angka)
                    ->whereRaw('UPPER(LEFT(nama_barang, 1)) = ?', [$huruf]);
                });
            }
        }

        $barang = $query->paginate(10)->appends($request->only('search'));

        return view('pegawai_gudang.pengembalianBarang.index', compact('barang'));
    }

    // Konfirmasi Pengembalian
    public function konfirmasiPengembalian($id_barang)
    {
        $barang = BarangTitipan::findOrFail($id_barang);

        // Update status barang
        $barang->update([
            'status_barang' => 'Diambil Kembali',
            'tanggal_keluar' => Carbon::now(),
        ]);

        return back()->with('success', 'Pengembalian barang berhasil dikonfirmasi!');
    }

    public function index(Request $request)
    {
        $query = BarangTitipan::with(['kategori', 'penitip', 'pegawaiQc', 'hunter']);

        $search = $request->search;
        $date   = $request->input('date');

        if (!empty($search)) {
            $searchLower = strtolower(trim($search));

            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%$search%")
                ->orWhere('id_barang', 'like', "%$search%")
                ->orWhere('deskripsi', 'like', "%$search%")
                ->orWhere('harga_jual', 'like', "%$search%")
                ->orWhere('berat', 'like', "%$search%")
                ->orWhere('tanggal_masuk', 'like', "%$search%")
                ->orWhere('tanggal_akhir', 'like', "%$search%")
                ->orWhere('tanggal_keluar', 'like', "%$search%")
                ->orWhere('status_barang', 'like', "%$search%")
                ->orWhere('garansi', 'like', "%$search%")
                ->orWhere('tanggal_garansi', 'like', "%$search%")
                ->orWhere('status_perpanjangan', 'like', "%$search%")
                ->orWhere('barang_hunter', 'like', "%$search%")
                ->orWhere('id_pegawai', 'like', "%$search%")
                ->orWhereHas('penitip', fn($p) => $p->where('nama_penitip', 'like', "%$search%"))
                ->orWhereHas('pegawaiQc', fn($qc) => $qc->where('nama_pegawai', 'like', "%$search%"))
                ->orWhereHas('hunter', fn($h) => $h->where('nama_pegawai', 'like', "%$search%"))
                ->orWhereHas('kategori', fn($p) => $p->where('nama_kategori', 'like', "%$search%"));
            });

            // 1. Filter berdasarkan nama bulan (ex: agustus)
            $bulanMap = [
                'januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4,
                'mei' => 5, 'juni' => 6, 'juli' => 7, 'agustus' => 8,
                'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12
            ];

            if (isset($bulanMap[$searchLower])) {
                $query->orWhereMonth('tanggal_masuk', $bulanMap[$searchLower]);
            }

            // 2. Filter berdasarkan X hari terakhir
            if (preg_match('/(\d+)\s*hari/', $searchLower, $matches)) {
                $jumlahHari = (int) $matches[1];
                $tanggalBatas = now()->subDays($jumlahHari);
                $query->orWhere('tanggal_masuk', '>=', $tanggalBatas);
            }

            if (preg_match('/^([A-Za-z])(\d+)$/', $search, $match)) {
                $huruf = strtoupper($match[1]);
                $angka = $match[2];

                $query->orWhere(function ($q) use ($huruf, $angka) {
                    $q->where('id_barang', $angka)
                    ->whereRaw('UPPER(LEFT(nama_barang, 1)) = ?', [$huruf]);
                });
            }
        }

        if ($date) {
            $query->whereDate('tanggal_masuk', $date);
        }

        $barang = $query->paginate(10)->appends($request->only(['search','date']));

        return view('pegawai_gudang.barangTitipan.index', compact('barang'));
    }

    public function cariPenitipForm(Request $request)
    {
        $penitip = [];

        if ($request->filled('search')) {
            $keyword = $request->search;
            $penitip = Penitip::where('id_penitip', 'like', "%$keyword%")
                        ->orWhere('nama_penitip', 'like', "%$keyword%")
                        ->orWhere('no_ktp', 'like', "%$keyword%")
                        ->orWhere('username', 'like', "%$keyword%")
                        ->orWhere('alamat', 'like', "%$keyword%")
                        ->orWhere('email', 'like', "%$keyword%")
                        ->get();
        }

        return view('pegawai_gudang.barangTitipan.cariPenitip', compact('penitip'));
    }

    // public function create($id_nota)
    // {
    //     $nota = NotaPenitipan::findOrFail($id_nota);
    //     $penitip = $nota->penitip;
    //     $kategori = Kategori::all();
    //     $pegawaiLogin = auth()->guard('pegawai')->user();
    //     $pegawaiQc = Pegawai::whereHas('jabatan', function ($q) {
    //         $q->where('nama_jabatan', 'Pegawai Gudang');
    //     })
    //     ->where('id_pegawai', '!=', $pegawaiLogin->id_pegawai)
    //     ->get();
    //     $pegawaiHunter = Pegawai::whereHas('jabatan', function ($q) {
    //         $q->where('nama_jabatan', 'Hunter');
    //     })->get();
    //     return view('pegawai_gudang.barangTitipan.create', compact('kategori', 'pegawaiQc', 'pegawaiHunter', 'penitip', 'pegawaiLogin', 'nota'));
    // }

    public function create(Request $request, $id_nota)
    {
        // $id_nota = $request->query('id_nota');
        
        // Ambil nota berdasarkan id_nota
        $nota = NotaPenitipan::findOrFail($id_nota);
        $penitip = $nota->penitip;
        $kategori = Kategori::all();
        $pegawaiLogin = auth()->guard('pegawai')->user();
        $pegawaiQc = Pegawai::whereHas('jabatan', function ($q) {
            $q->where('nama_jabatan', 'Pegawai Gudang');
        })
        ->where('id_pegawai', '!=', $pegawaiLogin->id_pegawai)
        ->get();

        $pegawaiHunter = Pegawai::whereHas('jabatan', function ($q) {
            $q->where('nama_jabatan', 'Hunter');
        })->get();

        // Ambil barang yang sudah ditambahkan dalam nota ini
        $barangNota = BarangTitipan::where('id_nota', $id_nota)->paginate(10);

        // dd($barangNota);

        return view('pegawai_gudang.barangTitipan.create', compact('kategori', 'pegawaiQc', 'pegawaiHunter', 'penitip', 'pegawaiLogin', 'nota', 'barangNota'));
    }

    public function createBlank()
    {
        return redirect()->route('pegawai_gudang.barangTitipan.cariPenitip');
    }

    public function store(Request $request)
    {
        // dd($request->all());
        if ($request->input('action') === 'finish') {
            $idNota = $request->input('id_nota');
            
            return redirect()
                ->route('pegawai_gudang.notaPenitipan.show', $idNota)
                ->with('success', 'Anda telah menyelesaikan penambahan barang. Berikut detail notanya.');
        }

        $request->validate([
            'id_nota' => 'required|exists:nota_penitipan,id_nota',
            'nama_barang' => 'required',
            'harga_jual' => 'required|numeric',
            'id_penitip' => 'required|exists:penitip,id_penitip',
            'id_kategori' => 'required',
            'id_qc_pegawai' => 'required',
            'berat' => 'required|numeric',
            'deskripsi' => 'required',
            'foto_barang' => 'required|array|min:2',
            'foto_barang.*' => 'image|mimes:jpg,jpeg,png|max:2048',

        ]);

        $nota = NotaPenitipan::findOrFail($request->id_nota);
        // $tanggalMasuk = Carbon::now();
        // $tanggalAkhir = $tanggalMasuk->copy()->addDays(30);

        $barang = new BarangTitipan([
            'nama_barang' => $request->nama_barang,
            'id_nota' => $request->id_nota,
            'id_penitip' => NotaPenitipan::find($request->id_nota)->id_penitip,
            'id_kategori' => $request->id_kategori,
            'id_qc_pegawai' => $request->id_qc_pegawai,
            'id_hunter' => $request->id_hunter,
            'barang_hunter' => $request->id_hunter ? 1 : 0,
            'id_pegawai' => auth()->guard('pegawai')->user()->id_pegawai,
            'berat' => $request->berat,
            'harga_jual' => $request->harga_jual,
            'deskripsi' => $request->deskripsi,
            'status_barang' => $request->status_barang,
            'status_perpanjangan' => $request->status_perpanjangan,
            'garansi' => $request->garansi,
            'tanggal_garansi' => $request->tanggal_garansi,
            'tanggal_masuk' => $nota->tanggal_penitipan,
            'tanggal_akhir' => \Carbon\Carbon::parse($nota->tanggal_penitipan)->addDays(30),
        ]);
        
        $barang->save();

        foreach ($request->file('foto_barang') as $index => $file) {
            $filename = time() . '_' . $index . '.' . $file->extension();
            $file->move(public_path('images/barang/'), $filename);

            FotoBarang::create([
                'id_barang' => $barang->id_barang,
                'nama_file' => $filename,
                'urutan' => $index + 1,
            ]);
        }

        // Tampilkan notifikasi sukses (optional)
        session()->flash('success', 'Barang berhasil ditambahkan.');

        // return redirect()->route('pegawai_gudang.barangTitipan.index')->with('success', 'Barang berhasil ditambahkan!');
        return redirect()->route('pegawai_gudang.barangTitipan.create', ['id_nota' => $request->id_nota])
                 ->with('success', 'Barang berhasil ditambahkan ke nota.');
    }

    public function edit(Request $request, $id)
    {
        $barang = BarangTitipan::with('nota')->findOrFail($id);
        $context = $request->query('context', 'detail');

        $penitip = $barang->penitip;

        $kategori = Kategori::all();
        $pegawaiLogin = auth()->guard('pegawai')->user();

        $pegawaiQc = Pegawai::whereHas('jabatan', function ($q) {
            $q->where('nama_jabatan', 'Pegawai Gudang');
        })
        ->where('id_pegawai', '!=', $pegawaiLogin->id_pegawai)
        ->get();

        $pegawaiHunter = Pegawai::whereHas('jabatan', function ($q) {
            $q->where('nama_jabatan', 'Hunter');
        })->get();

        $idNota = null;
        if ($context === 'create') {
            $idNota = $request->query('id_nota');
            // Validasi sederhana agar id_nota benar
            if (! $idNota || $barang->id_nota != $idNota) {
                abort(404, 'Nota tidak valid untuk barang ini.');
            }
        }

        return view('pegawai_gudang.barangTitipan.edit', compact('barang', 'penitip', 'kategori', 'pegawaiLogin', 'pegawaiQc', 'pegawaiHunter', 'context', 'idNota'));
    }

    public function update(Request $request, $id)
    {
        $barang = BarangTitipan::with('nota')->findOrFail($id);

        $jumlahFotoLama = $barang->fotoBarang()->count();
        $jumlahFotoDihapus = is_array($request->hapus_foto) ? count($request->hapus_foto) : 0;
        $jumlahFotoUpload = $request->hasFile('foto_barang') ? count($request->file('foto_barang')) : 0;

        $totalFotoSetelahUpdate = ($jumlahFotoLama - $jumlahFotoDihapus) + $jumlahFotoUpload;

        if ($totalFotoSetelahUpdate < 2) {
            return back()->withErrors(['foto_barang' => 'Total foto setelah update minimal harus 2'])->withInput();
        }

        $request->validate([
            'nama_barang' => 'required',
            'harga_jual' => 'required|numeric',
            'id_penitip' => 'required|exists:penitip,id_penitip',
            'id_kategori' => 'required',
            'id_qc_pegawai' => 'required',
            'berat' => 'required|numeric',
            'deskripsi' => 'required',
            'status_barang' => 'required',
            'status_perpanjangan' => 'required|boolean',
            'garansi' => 'required|boolean',
            'tanggal_masuk' => 'required|date',
            'tanggal_akhir' => 'required|date',
            'tanggal_garansi' => 'nullable|date',
            'id_hunter' => 'nullable|exists:pegawai,id_pegawai',
            'foto_barang.*' => 'image|mimes:jpg,jpeg,png|max:2048',
            'context' => 'required|in:create,detail',
            'id_nota' => 'required_if:context,create|exists:nota_penitipan,id_nota',
        ]);

        $context = $request->input('context');

        $idNota = $context === 'create' ? $request->input('id_nota') : null;

        // Hapus foto yang ditandai
        if ($request->filled('hapus_foto')) {
            $fotoIds = $request->input('hapus_foto');
            foreach ($fotoIds as $idFoto) {
                $foto = FotoBarang::find($idFoto);
                if ($foto && $foto->id_barang == $barang->id_barang) {
                    $path = public_path('images/barang/' . $foto->nama_file);
                    if (file_exists($path)) {
                        unlink($path);
                    }
                    $foto->delete();
                }
            }
        }

        $barang->update([
            'nama_barang' => $request->nama_barang,
            'id_penitip' => $request->id_penitip,
            'id_kategori' => $request->id_kategori,
            'id_qc_pegawai' => $request->id_qc_pegawai,
            'id_hunter' => $request->id_hunter,
            'barang_hunter' => $request->id_hunter ? 1 : 0,
            'id_pegawai' => $request->id_pegawai,
            'berat' => $request->berat,
            'harga_jual' => $request->harga_jual,
            'deskripsi' => $request->deskripsi,
            'status_barang' => $request->status_barang,
            'status_perpanjangan' => $request->status_perpanjangan,
            'garansi' => $request->garansi,
            'tanggal_garansi' => $request->tanggal_garansi,
            'tanggal_masuk' => $request->tanggal_masuk,
            'tanggal_akhir' => $request->tanggal_akhir,
        ]);

        if ($request->hasFile('foto_barang')) {
            foreach ($request->file('foto_barang') as $index => $file) {
                $filename = time() . '_' . $index . '.' . $file->extension();
                $file->move(public_path('images/barang/'), $filename);

                FotoBarang::create([
                    'id_barang' => $barang->id_barang,
                    'nama_file' => $filename,
                    'urutan' => $barang->fotoBarang()->count() + $index + 1,
                ]);
            }
        }

        if ($context === 'create') {
            // Kembali ke form tambah barang untuk nota yang sama
            return redirect()
                ->route('pegawai_gudang.barangTitipan.create', ['id_nota' => $idNota])
                ->with('success', 'Barang berhasil diperbarui. Kembali ke form tambah barang.');
        } else {
            // context == detail
            return redirect()
                ->route('pegawai_gudang.barangTitipan.showDetail', $barang->id_barang)
                ->with('success', 'Barang berhasil diperbarui.');
        }
        // return redirect()->route('pegawai_gudang.barangTitipan.index')->with('success', 'Barang berhasil diperbarui!');
        // return redirect()->route('pegawai_gudang.barangTitipan.showDetail', $barang->id_barang)
        //     ->with('success', 'Barang berhasil diperbarui!');
    }

    public function hapusFoto($id)
    {
        $foto = FotoBarang::findOrFail($id);

        $path = public_path('images/barang/' . $foto->nama_file);
        if (file_exists($path)) {
            unlink($path); // hapus file
        }

        $foto->delete(); // hapus record

        return back()->with('success', 'Foto berhasil dihapus.');
    }

    public function destroy($id)
    {
        $barang = BarangTitipan::findOrFail($id);
        $barang->delete();

        return redirect()->route('pegawai_gudang.barangTitipan.index')->with('success', 'Barang berhasil dihapus!');
    }

    public function showDetail($id)
    {
        $barang = BarangTitipan::with(['penitip', 'kategori', 'pegawaiQc', 'hunter'])->findOrFail($id);
        $nota = $barang->nota;
        $idNota  = $barang->nota->id_nota;
        return view('pegawai_gudang.barangTitipan.showDetail', compact('barang', 'nota', 'idNota'));
    }

    // public function showMobile(Request $request, $id = null)
    // {
    //     try {
    //         // Mendapatkan query pencarian dan category_id dari request
    //         $search = $request->query('search');
    //         $categoryId = $request->query('category_id');

    //         // Menambahkan log untuk melihat nilainya
    //         Log::info('Search query: ' . ($search ?: 'Tidak ada nilai pencarian'));
    //         Log::info('Category ID: ' . ($categoryId ?: 'Tidak ada kategori'));

    //         if ($id) {
    //             // Jika ada ID, tampilkan detail produk
    //             $product = BarangTitipan::with('fotoBarang', 'kategori')
    //                 ->where('id_barang', $id)
    //                 ->where('status_barang', 'Tersedia') // Memastikan hanya menampilkan barang dengan status "Tersedia"
    //                 ->firstOrFail();

    //             return response()->json([
    //                 'success' => true,
    //                 'data' => $product
    //             ]);
    //         } else {
    //             if (empty($search)) {
    //                 $search = 'Tidak ada nilai pencarian';  // Menetapkan nilai default jika tidak ada pencarian
    //             }

    //             $productsQuery = BarangTitipan::with('fotoBarang', 'kategori');

    //             // Jika search tidak kosong, lakukan pencarian berdasarkan nama
    //             if ($search !== 'Tidak ada nilai pencarian') {
    //                 $productsQuery->where('nama_barang', 'like', "%{$search}%");
    //             }

    //             // Jika categoryId ada dan tidak sama dengan 'Semua', filter berdasarkan kategori
    //             if ($categoryId && $categoryId != 'Semua') {
    //                 $productsQuery->where('id_kategori', $categoryId);
    //             }

    //             // Menambahkan filter untuk hanya menampilkan barang dengan status "Tersedia"
    //             $productsQuery->where('status_barang', 'Tersedia');

    //             // Paginate the results
    //             $products = $productsQuery->paginate(10);  // Limiting results to 10 per page

    //             return response()->json([
    //                 'success' => true,
    //                 'data' => $products
    //             ]);
    //         }
    //     } catch (\Exception $e) {
    //         \Log::error('Error fetching product: ' . $e->getMessage());
    //         return response()->json([
    //             'success' => false,
    //             'error' => 'Gagal mengambil produk.'
    //         ], 500);
    //     }
    // }
    
    public function searchProducts(Request $request)
    {
        try {
            $search = $request->query('search');  // Ambil query parameter 'search'
            
            // Cari produk berdasarkan nama
            $products = BarangTitipan::with('fotoBarang', 'kategori')
                ->where('nama_barang', 'like', "%{$search}%")
                ->get();

            return response()->json([
                'success' => true,
                'data' => $products
            ]);
        } catch (\Exception $e) {
            \Log::error('Error searching products: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Gagal mencari produk.'
            ], 500);
        }
    }

}
