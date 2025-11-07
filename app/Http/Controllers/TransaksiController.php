<?php

namespace App\Http\Controllers;
use App\Models\Transaksi;
use App\Models\Pembeli;
use App\Models\Pembayaran;
use App\Models\BarangTitipan;
use App\Models\Penitip;
use App\Models\Pegawai;
use App\Models\Penjadwalan;
use App\Models\DetailTransaksi;

use App\Notifications\transaksiDisiapkan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\FirebaseService;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class TransaksiController extends Controller
{
    public function index()
    {
        // Mengambil semua data pembayaran dengan pagination 10 per halaman
        $pembayarans = Pembayaran::paginate(10);

        // Kirim data pembayaran ke view dengan variabel 'pembayarans'
        return view('CS.pembayaranIndex', compact('pembayarans'));
    }

    public function showPembayaran(Request $request)
    {
        $idTransaksi = $request->query('id_transaksi');
        if (!$idTransaksi) {
            abort(404, 'ID transaksi tidak ditemukan.');
        }

        $transaksi = Transaksi::with('detailTransaksi')->find($idTransaksi);
        if (!$transaksi) {
            abort(404, 'Transaksi tidak ditemukan.');
        }

        // Hitung total harga dari detail transaksi
        $totalHarga = $transaksi->detailTransaksi->sum('sub_total');

        $tanggalTransaksiPlus1Menit = \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->copy()->addMinute();

        return view('pembayaran', [
            'transaksi' => $transaksi,
            'totalHarga' => $totalHarga,
            'tanggalTransaksiPlus1Menit' => $tanggalTransaksiPlus1Menit,
        ]);
    }

    public function uploadBukti(Request $request)
    {
        // Validasi input
        $request->validate([
            'id_transaksi' => 'required|exists:transaksi,id_transaksi',
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        DB::beginTransaction();

        try {
            // Ambil data pembayaran berdasar id_transaksi
            $pembayaran = Pembayaran::where('id_transaksi', $request->id_transaksi)->first();

            if (!$pembayaran) {
                return back()->with('error', 'Data pembayaran tidak ditemukan untuk transaksi ini.');
            }

            // Simpan file ke folder public/images/bukti_pembayaran
            $file = $request->file('bukti_pembayaran');
            $extension = $file->getClientOriginalExtension();
            $namaFile = uniqid() . '.' . $extension;
            $file->move(public_path('images/bukti_pembayaran'), $namaFile);

            // Update field bukti_transfer dan status_verifikasi di tabel pembayaran
            $pembayaran->bukti_transfer = $namaFile;
            $pembayaran->status_verifikasi = 0;  // status belum diverifikasi, bisa diubah oleh admin nanti
            $pembayaran->save();

            DB::commit();
            return redirect()->route('home')->with('success', 'Bukti pembayaran berhasil diunggah dan menunggu verifikasi.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengunggah bukti pembayaran: ' . $e->getMessage());
        }
    }

    public function verifikasiPembayaran($id_transaksi)
    {
        $pembayaran = Pembayaran::where('id_transaksi', $id_transaksi)->first();

        if (!$pembayaran) {
            return redirect()->back()->with('error', 'Data pembayaran tidak ditemukan.');
        }

        $pembayaran->status_verifikasi = 1;
        $pembayaran->id_pegawai = auth()->guard('pegawai')->id(); 
        $pembayaran->save();

        $transaksi = Transaksi::with('detailTransaksi.barang.penitip')->find($id_transaksi);
        if ($transaksi) {
            $transaksi->status_transaksi = 'Disiapkan';
            $transaksi->save();
            // Kumpulkan nama barang per penitip dalam transaksi ini
            $penitipBarangMap = [];
            // Buat data baru di tabel penjadwalan
            $jenis_jadwal = null;
            if ($transaksi->jenis_pengiriman == 'Kurir') {
                $jenis_jadwal = 'Pengiriman';
            } elseif ($transaksi->jenis_pengiriman == 'Ambil Sendiri') {
                $jenis_jadwal = 'Diambil';
            }

            if ($jenis_jadwal) {
                Penjadwalan::create([
                    'id_transaksi' => $id_transaksi,
                    'jenis_jadwal' => $jenis_jadwal,
                    'tanggal_jadwal' => null,
                    'status_jadwal' => 'Diproses',
                ]);
            }

            foreach ($transaksi->detailTransaksi as $detail) {
                $penitip = $detail->barang->penitip ?? null;
                if ($penitip) {
                    $idPenitip = $penitip->id_penitip;
                    $namaBarang = $detail->barang->nama_barang ?? 'Barang';

                    $penitipBarangMap[$idPenitip]['penitip'] = $penitip;
                    $penitipBarangMap[$idPenitip]['barang'][] = $namaBarang;
                }
            }

            // Kirim notifikasi ke masing-masing penitip dengan daftar nama barangnya
            foreach ($penitipBarangMap as $data) {
                $penitip = $data['penitip'];
                $barangList = collect($data['barang']);

                if ($penitip->email) {
                    $penitip->notify(new transaksiDisiapkan($transaksi, $penitip, $barangList));
                }
            }

            // Tambah notifikasi khusus mobile
            // $firebase = new FirebaseService();
            
            // foreach ($penitipBarangMap as $data) {
            //     $penitip = $data['penitip'];
            //     $barangList = collect($data['barang']);

            //     // Kirim notifikasi push via Firebase Cloud Messaging (FCM)
            //     $penitipFcmToken = $penitip->fcm_token ?? null;

            //     // Buat judul dan isi notifikasi, bisa sesuaikan sesuai kebutuhan
            //     $title = "Transaksi Disiapkan";
            //     $body = "Barang: " . $barangList->implode(', ') . " sudah disiapkan untuk transaksi ID #" . $transaksi->id_transaksi;

            //     if ($penitipFcmToken) {
            //         $firebase->sendMessage($penitipFcmToken, $title, $body);
            //     }
            // }
        }

        return redirect()->back()->with('success', 'Pembayaran diverifikasi, status transaksi diubah, dan email notifikasi dikirim ke semua penitip terkait.');
    }
  
  public function indexNota(Request $request)
    {
        $search = $request->input('search');
        $date = $request->input('date');

        $query = Transaksi::with('pembeli')
            ->orderBy('tanggal_transaksi', 'desc');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('id_transaksi', 'like', "%{$search}%")
                ->orWhere('nomor_transaksi', 'like', "%{$search}%")  // tambahan ini
                ->orWhereHas('pembeli', function($q2) use ($search) {
                    $q2->where('nama_pembeli', 'like', "%{$search}%");
                });
            });
        }

        if ($date) {
            $query->whereDate('tanggal_transaksi', $date);
        }

        $transaksi = $query->paginate(10)->appends($request->only('search', 'date'));

        return view('pegawai_gudang.pengirimanBarang.cetakNota', compact('transaksi'));
    }

    public function cetakNota($id)
    {
        $transaksi = Transaksi::with([
            'pembeli',
            'alamat',
            'penjadwalan.pengiriman.pegawai',
            // 'qc',
            'detailTransaksi.barang'
        ])->findOrFail($id);

        return view('pegawai_gudang.pengirimanBarang.viewNota', compact('transaksi'));
    }

    public function cetakNotaPdf($id)
    {
        $transaksi = Transaksi::with([
            'pembeli',
            'alamat',
            'penjadwalan.pengiriman.pegawai',
            // 'qc',
            'detailTransaksi.barang'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('pegawai_gudang.pengirimanBarang.viewNota', compact('transaksi'));

        return $pdf->stream('Nota_Penjualan_'.$transaksi->no_nota.'.pdf');
    }

    public function batalTransaksi(Request $request)
    {
        try {
            $id = $request->input('id_transaksi');
            \Log::info('batalTransaksi dipanggil', ['id_transaksi' => $id]);

            $transaksi = Transaksi::with('detailTransaksi.barang')->find($id);
            if (!$transaksi) {
                return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan']);
            }

            // Log nilai status transaksi dan bukti pembayaran
            \Log::info('Detail transaksi', [
                'status_transaksi' => $transaksi->status_transaksi,
                'bukti_pembayaran' => $transaksi->bukti_pembayaran,
            ]);

            // Cek kondisi pembatalan
            if ($transaksi->status_transaksi === 'Menunggu Pembayaran' && !$transaksi->bukti_pembayaran) {
                // Update status transaksi jadi Batal
                $transaksi->status_transaksi = 'Batal';
                $transaksi->save();

                // Kembalikan status barang menjadi 'Tersedia'
                foreach ($transaksi->detailTransaksi as $detail) {
                    if ($detail->barang) {
                        $detail->barang->status_barang = 'Tersedia';
                        $detail->barang->save();
                    }
                }

                // Kembalikan poin yang digunakan ke pembeli
                $pembeli = $transaksi->pembeli; // Pastikan relasi 'pembeli' ada di model Transaksi
                if ($pembeli && $transaksi->poin_digunakan > 0) {
                    $pembeli->poin += $transaksi->poin_digunakan;
                    $pembeli->save();
                }

                return response()->json(['success' => true, 'message' => 'Transaksi dibatalkan, status barang dikembalikan tersedia, dan poin dikembalikan.']);
            } else {
                return response()->json(['success' => false, 'message' => 'Transaksi tidak valid atau sudah dibayar.']);
            }
        } catch (\Exception $e) {
            \Log::error('Error batalTransaksi: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan server.', 'error' => $e->getMessage()], 500);
        }
    }

    public function tolakPembayaran($id_transaksi)
    {
        // Cari pembayaran berdasarkan id_transaksi
        $pembayaran = Pembayaran::where('id_transaksi', $id_transaksi)->first();

        if (!$pembayaran) {
            return redirect()->back()->with('error', 'Data pembayaran tidak ditemukan.');
        }

        // Update status_verifikasi jadi 2 (ditolak)
        $pembayaran->status_verifikasi = 0;

        // Isi id_pegawai dengan id pegawai yang sedang login
        $pembayaran->id_pegawai = auth()->guard('pegawai')->id(); 
        // sesuaikan guard jika perlu

        $pembayaran->save();

        // Update status transaksi jadi 'Batal'
        $transaksi = Transaksi::find($id_transaksi);
        if ($transaksi) {
            $transaksi->status_transaksi = 'Batal';
            $transaksi->save();

            // Kembalikan status barang jadi 'Tersedia'
            $detailBarang = DetailTransaksi::where('id_transaksi', $id_transaksi)->get();

            foreach ($detailBarang as $detail) {
                BarangTitipan::where('id_barang', $detail->id_barang)
                    ->update(['status_barang' => 'Tersedia']);
            }

            // Kembalikan poin yang digunakan ke pembeli
            $pembeli = Pembeli::find($transaksi->id_pembeli);
            if ($pembeli) {
                $poinDigunakan = $transaksi->poin_digunakan ?? 0;
                $pembeli->poin += $poinDigunakan;
                $pembeli->save();
            }
        }

        return redirect()->back()->with('success', 'Bukti pembayaran ditolak, status transaksi diubah menjadi batal, status barang dikembalikan tersedia, dan poin dikembalikan.');
    }
}
