<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\NotaPenitipan;
use App\Models\Penitip;
use App\Models\Pegawai;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class NotaPenitipanController extends Controller
{
    // Tampilkan form tambah nota
    public function create(Request $request)
    {
        $penitips = Penitip::all();
        $terpilih = null;

        if ($request->filled('id_penitip')) {
            $terpilih = Penitip::find($request->id_penitip);
        }

        $pegawaiLogin = auth()->guard('pegawai')->user();
        $pegawaiQc = Pegawai::whereHas('jabatan', function ($q) {
            $q->where('nama_jabatan', 'Pegawai Gudang');
        })
        ->where('id_pegawai', '!=', $pegawaiLogin->id_pegawai)
        ->get();

        return view('pegawai_gudang.notaPenitipan.create', compact('penitips', 'terpilih', 'pegawaiQc'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_penitip' => 'required|exists:penitip,id_penitip',
            'tanggal_penitipan' => 'required|date_format:Y-m-d\TH:i',
            'masa_berakhir' => 'required|date_format:Y-m-d\TH:i|after_or_equal:tanggal_penitipan',
            'id_qc_pegawai' => 'required|exists:pegawai,id_pegawai',
        ]);

        DB::beginTransaction();

        try {
            // Ambil nomor urut terakhir dari semua nota
            $lastNota = DB::table('nota_penitipan')
                ->select(DB::raw("SUBSTRING_INDEX(no_nota, '.', -1) + 0 as urutan"))
                ->orderByDesc(DB::raw("urutan"))
                ->lockForUpdate() // Kunci baris terakhir saat transaksi
                ->first();

            $lastUrutan = $lastNota ? intval($lastNota->urutan) : 0;
            $urutanBaru = $lastUrutan + 1;

            $noNota = now()->format('y.m.') . str_pad($urutanBaru, 3, '0', STR_PAD_LEFT);

            $nota = NotaPenitipan::create([
                'no_nota' => $noNota,
                'tanggal_penitipan' => date('Y-m-d H:i:s', strtotime($request->tanggal_penitipan)),
                'masa_berakhir' => date('Y-m-d', strtotime($request->masa_berakhir)),
                'id_penitip' => $request->id_penitip,
                'id_qc_pegawai' => $request->id_qc_pegawai,
            ]);

            session(['id_nota' => $nota->id_nota]);

            DB::commit();

            return redirect()
                ->route('pegawai_gudang.barangTitipan.create', ['id_nota' => $nota->id_nota])
                ->with('success', 'Nomor Transaksi berhasil dibuat. Silakan tambahkan barang.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan nota: ' . $e->getMessage()]);
        }
    }

    public function printNotaPDF($id_nota)
    {
        // Ambil nota dan relasinya
        $nota      = NotaPenitipan::with('penitip', 'pegawaiQc', 'barangTitipan')->findOrFail($id_nota);
        $penitip   = $nota->penitip;
        $barangNota = $nota->barangTitipan;

        // Render view ke PDF
        $pdf = Pdf::loadView('pegawai_gudang.notaPenitipan.printNota', compact('nota','penitip','barangNota'));

        // Download sebagai file PDF
        return $pdf->download("nota-{$nota->no_nota}.pdf");
    }

    public function indexNota(Request $request)
    {
        $search = $request->input('search');
        $date   = $request->input('date');

        $query = NotaPenitipan::with('penitip')
            ->withCount('barangTitipan')
            ->orderByDesc('tanggal_penitipan');

        if ($search) {
            $query->where('no_nota', 'like', "%{$search}%")
                ->orWhereHas('penitip', function($q) use ($search) {
                    $q->where('nama_penitip', 'like', "%{$search}%")
                      ->orWhereRaw("CONCAT('T', id_penitip) LIKE ?", ["%{$search}%"]);
            });
        }

        if ($date) {
            $query->whereDate('tanggal_penitipan', $date);
        }

        $notas = $query->paginate(10)->appends($request->only(['search','date']));

        return view('pegawai_gudang.notaPenitipan.index', compact('notas'));
    }

    public function show($id_nota)
    {
        // Ambil nota beserta barang‐barangnya
        $nota = NotaPenitipan::with([
            'penitip',        // relasi ke si pemilik nota
            'pegawaiQc',      // relasi ke pegawai QC
            'barangTitipan'   // relasi ke daftar barang
        ])->findOrFail($id_nota);

        return view('pegawai_gudang.notaPenitipan.show', compact('nota'));
    }

    public function editHeader($id_nota)
    {
        // 1. Ambil data header nota yang akan diedit, beserta relasi penitip & pegawaiQC
        $nota = NotaPenitipan::with(['penitip', 'pegawaiQc'])
                    ->findOrFail($id_nota);

        // 2. Ambil list penitip jika Anda mengizinkan mengganti penitip
        //    (jika tidak perlu, Anda bisa skip langkah ini dan di‐view jadikan readonly)
        $penitips = Penitip::orderBy('nama_penitip')->get();

        // 3. Ambil daftar Pegawai QC (role “Pegawai Gudang”) untuk dropdown, kecuali pegawai login
        $pegawaiLogin = auth()->guard('pegawai')->user();
        $pegawaiQcList = Pegawai::whereHas('jabatan', function($q) {
                                $q->where('nama_jabatan', 'Pegawai Gudang');
                            })
                            ->where('id_pegawai', '!=', $pegawaiLogin->id_pegawai)
                            ->orderBy('nama_pegawai')
                            ->get();

        // 4. Kirim variabel ke view edit‐header
        return view(
            'pegawai_gudang.notaPenitipan.edit-header',
            compact('nota', 'penitips', 'pegawaiQcList')
        );
    }

    public function updateHeader(Request $request, $id_nota)
    {
        // 1. Validasi input sesuai kebutuhan
        $request->validate([
            'no_nota'           => 'required|string|max:20',
            'id_penitip'        => 'required|exists:penitip,id_penitip',
            'id_qc_pegawai'     => 'required|exists:pegawai,id_pegawai',
            'tanggal_penitipan' => 'required|date_format:Y-m-d\TH:i',
            'masa_berakhir'     => 'required|date_format:Y-m-d\TH:i|after_or_equal:tanggal_penitipan',
            // Jika Anda ingin memperbolehkan mengganti status/mencatat catatan, tambahkan validasi di sini
        ]);

        // 2. Ambil record lama
        $nota = NotaPenitipan::findOrFail($id_nota);

        // 3. Update field header
        $nota->no_nota           = $request->input('no_nota');
        $nota->id_penitip        = $request->input('id_penitip');
        $nota->id_qc_pegawai     = $request->input('id_qc_pegawai');
        // Ubah format datetime‐local (Y-m-d\TH:i) ke DB timestamp
        $nota->tanggal_penitipan = date('Y-m-d H:i:s', strtotime($request->input('tanggal_penitipan')));
        // Untuk masa_berakhir: jika di DB hanya butuh date saja, gunakan date('Y-m-d', …)
        $nota->masa_berakhir     = date('Y-m-d', strtotime($request->input('masa_berakhir')));

        $nota->save();

        // 4. Redirect kembali ke halaman detail, dengan pesan sukses
        return redirect()
            ->route('pegawai_gudang.notaPenitipan.show', $nota->id_nota)
            ->with('success', 'Header Nota Penitipan berhasil diperbarui.');
    }
}
