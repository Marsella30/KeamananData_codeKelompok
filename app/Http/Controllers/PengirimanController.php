<?php
namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Penjadwalan;
use App\Models\Pengiriman;
use App\Models\Pegawai;
use App\Mail\JadwalDikirim;
use App\Mail\KonfirmasiPengiriman;
use App\Models\BarangTitipan;
use App\Models\Penitip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Notification;

class PengirimanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $date = $request->query('date');
        $statusPengiriman = $request->input('status_pengiriman');
        $query = Transaksi::with(['pembeli', 'penjadwalan']);

        $transaksi = Transaksi::with(['pembeli', 'penjadwalan'])
            ->whereHas('penjadwalan', function ($query) {
                $query->whereIn('jenis_jadwal', ['Pengiriman', 'Diambil']);
            });

        // Filter berdasarkan kata kunci pada nomor transaksi, nama pembeli, atau nama barang
        if ($search) {
            $transaksi = $transaksi->where(function($q) use ($search) {
                $q->where('id_transaksi', 'like', "%{$search}%")
                ->orWhere('nomor_transaksi', 'like', "%{$search}%")
                ->orWhereHas('pembeli', function($q2) use ($search) {
                    $q2->where('nama_pembeli', 'like', "%{$search}%");
                })
                ->orWhereHas('detailTransaksi.barang', function($q3) use ($search) {
                    $q3->where('nama_barang', 'like', "%{$search}%");
                });
            });
        }

        // Filter berdasarkan tanggal transaksi jika ada
        if ($date) {
            $transaksi = $transaksi->whereDate('tanggal_transaksi', $date);
        }

         // Filter berdasarkan status pengiriman
        if (!empty($statusPengiriman)) {
        if ($statusPengiriman === 'Belum Disiapkan') {
            // Filter khusus untuk "Belum Disiapkan" -> tanggal_jadwal masih null
            $query->whereHas('penjadwalan', function ($q) {
                $q->whereNull('tanggal_jadwal');
            });
        } else {
            // Filter status pengiriman biasa seperti sebelumnya
            $query->whereHas('penjadwalan', function ($q) use ($statusPengiriman) {
                $q->whereHas('pengiriman', function ($q2) use ($statusPengiriman) {
                    $q2->where('status_pengiriman', $statusPengiriman);
                });
            });
        }
        $transaksi = $query->paginate(10)->appends($request->only('search', 'date', 'status_pengiriman'));
    } else {
        $transaksi = $transaksi->paginate(10)->withQueryString();
    }

        $kurir = Pegawai::where('id_jabatan', 5)->get();

        return view('pegawai_gudang.pengirimanBarang.index', compact('transaksi', 'kurir'));
    }

    public function tambahJadwal(Request $request)
    {
        $validated = $request->validate([
            'id_transaksi' => 'required|exists:transaksi,id_transaksi',
            'tanggal_jadwal' => 'required|date',
            'id_kurir' => 'nullable|exists:pegawai,id_pegawai',
        ]);

        $transaksi = Transaksi::with(['pembeli'])->findOrFail($validated['id_transaksi']);
        $jam_transaksi = \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->hour;
        $tanggal_transaksi = \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->toDateString();
        $tanggal_jadwal = \Carbon\Carbon::parse($validated['tanggal_jadwal'])->toDateString();

        if ($jam_transaksi >= 16 && $tanggal_transaksi === $tanggal_jadwal) {
            return back()->withInput()
                ->with('error', 'Transaksi setelah jam 16:00 tidak bisa dijadwalkan di hari yang sama.')
                ->with('error_modal', $request->id_transaksi);
        }

        $jadwal = Penjadwalan::where('id_transaksi', $validated['id_transaksi'])
            ->whereIn('jenis_jadwal', ['Pengiriman', 'Diambil'])
            ->first();

        if (!$jadwal) {
            return back()->with('error', 'Data jadwal tidak ditemukan.')
                ->with('error_modal', $request->id_transaksi);
        }

        $jadwal->update([
            'tanggal_jadwal' => $validated['tanggal_jadwal'],
            'status_jadwal' => 'Dijadwalkan',
        ]);

        $statusPengiriman = 'Disiapkan';
        if ($jadwal->jenis_jadwal === 'Pengiriman') {
            $statusPengiriman = 'Diantar';
        }

        Pengiriman::create([
            'id_pegawai' => $request->input('id_kurir'), // bisa NULL jika Diambil
            'id_jadwal' => $jadwal->id_jadwal,
            'status_pengiriman' => $statusPengiriman,
        ]);

        if ($statusPengiriman === 'Diantar') {
            $kurir = null;
            if ($jadwal->jenis_jadwal === 'Pengiriman' && $request->filled('id_kurir')) {
                $kurir = \App\Models\Pegawai::find($validated['id_kurir']);
            }

            // Notifikasi ke Pembeli
            // if ($transaksi->pembeli) {
            //     $transaksi->pembeli->notify(new \App\Notifications\DikirimKurir($jadwal, $transaksi, $kurir));
            // }

            // Notifikasi ke Penitip
            $penitips = collect();
            foreach ($transaksi->detailTransaksi as $detail) {
                $barang = \App\Models\BarangTitipan::find($detail->id_barang);
                if ($barang) {
                    $penitip = \App\Models\Penitip::find($barang->id_penitip);
                    if ($penitip) {
                        $penitips->push($penitip);
                    }
                }
            }
            $penitips = $penitips->unique('id_penitip');
            foreach ($penitips as $penitip) {
                $penitip->notify(new \App\Notifications\DikirimKurir($jadwal, $transaksi, $kurir));
            }
        }

        // Siapkan daftar penerima email
        $recipients = [];

        // Email Pembeli
        if ($transaksi->pembeli && $transaksi->pembeli->email) {
            $recipients[] = $transaksi->pembeli->email;
        }

        // Email Penitip (loop semua penitip yang punya barang di detail_transaksi)
        $detailTransaksis = $transaksi->detailTransaksi;
        $penitipEmails = [];

        foreach ($detailTransaksis as $detail) {
            $barang = \App\Models\BarangTitipan::find($detail->id_barang);
            if ($barang) {
                $penitip = \App\Models\Penitip::find($barang->id_penitip);
                if ($penitip && $penitip->email && !in_array($penitip->email, $penitipEmails)) {
                    $penitipEmails[] = $penitip->email;
                }
            }
        }

        $recipients = array_merge($recipients, $penitipEmails);

        // Email Kurir (hanya untuk Pengiriman)
        if ($jadwal->jenis_jadwal === 'Pengiriman' && $request->filled('id_kurir')) {
            $kurir = Pegawai::find($validated['id_kurir']);
            if ($kurir && $kurir->email) {
                $recipients[] = $kurir->email;
            }
        }

        // Notifikasi ke Penitip (unique collection)
        $penitips = collect();
        foreach ($transaksi->detailTransaksi as $detail) {
            $barang = \App\Models\BarangTitipan::find($detail->id_barang);
            if ($barang) {
                $penitip = \App\Models\Penitip::find($barang->id_penitip);
                if ($penitip) {
                    $penitips->push($penitip);
                }
            }
        }

        // Kirim Email
        foreach ($recipients as $email) {
            Mail::to($email)->send(new JadwalDikirim($jadwal, $transaksi));
        }

        return back()->with('success', 'Jadwal berhasil diperbarui, pengiriman disiapkan, dan email notifikasi dikirim!');
    }

   public function konfirmasi($id_jadwal)
    {
        $jadwal = Penjadwalan::with(['pengiriman', 'transaksi.pembeli', 'transaksi.detailTransaksi'])
                    ->findOrFail($id_jadwal);

        if (!$jadwal->pengiriman) {
            return back()->with('error', 'Pengiriman tidak ditemukan.');
        }

        // Update status_pengiriman
        $statusBaru = $jadwal->jenis_jadwal === 'Diambil' ? 'Diterima' : 'Sampai';
        $jadwal->pengiriman->update(['status_pengiriman' => $statusBaru]);

        if ($jadwal->jenis_jadwal === 'Diambil') {
            $this->konfirmasiPengambilan($jadwal->id_jadwal);
        }

        $transaksi = $jadwal->transaksi;
        if ($transaksi && $transaksi->status_transaksi === 'Disiapkan' && $jadwal->jenis_jadwal === 'Diambil') {
            $transaksi->status_transaksi = 'Transaksi Selesai';
            $transaksi->save();
        }
        
        // Email notifikasi ke Pembeli
        $recipients = [];
        if ($jadwal->transaksi->pembeli && $jadwal->transaksi->pembeli->email) {
            $recipients[] = $jadwal->transaksi->pembeli->email;
        }

        // Email notifikasi ke Penitip (loop semua penitip di detail_transaksi)
        $penitipEmails = [];
        foreach ($jadwal->transaksi->detailTransaksi as $detail) {
            $barang = BarangTitipan::find($detail->id_barang);
            if ($barang) {
                $penitip = Penitip::find($barang->id_penitip);
                if ($penitip && $penitip->email && !in_array($penitip->email, $penitipEmails)) {
                    $penitipEmails[] = $penitip->email;
                }
            }
        }

        $recipients = array_merge($recipients, $penitipEmails);

        // Kirim Email
        foreach ($recipients as $email) {
            Mail::to($email)->send(new KonfirmasiPengiriman($jadwal, $statusBaru));
        }

        return back()->with('success', 'Status pengiriman berhasil diperbarui dan notifikasi email dikirim!');
    }

    public function konfirmasiPengambilan($id_jadwal)
    {
        DB::beginTransaction();
        try {
            $jadwal = Penjadwalan::with([
                'pengiriman',
                'transaksi.detailTransaksi.barang',
                'transaksi.pembeli'
            ])->findOrFail($id_jadwal);

            // Update status pengiriman
            $pengiriman = $jadwal->pengiriman;
            if ($pengiriman) {
                $pengiriman->status_pengiriman = 'Diterima';
                $pengiriman->save();
            }

            $totalPoinPembeli = 0;

            foreach ($jadwal->transaksi->detailTransaksi as $detail) {
                $barang = $detail->barang;
                $barang->tanggal_keluar = now();
                $barang->save();

                // Hitung Komisi
                $hargaBarang = $barang->harga_jual;
                $barangHunter = $barang->barang_hunter;
                $statusPerpanjangan = $barang->status_perpanjangan;

                $periodeTopSeller = date('Y-m-01', strtotime($jadwal->tanggal_jadwal));
                $isTopSeller = DB::table('badge')
                    ->where('id_penitip', $barang->id_penitip)
                    ->where('periode_pemberian', $periodeTopSeller)
                    ->exists();

                $komisiPersen = $statusPerpanjangan ? 0.30 : 0.20;
                if ($isTopSeller) {
                    $komisiPersen -= 0.01;
                }

                $komisiHunter = 0;
                if ($barangHunter) {
                    $komisiHunter = 0.05 * $hargaBarang;
                    $komisiPersen -= 0.05;
                }

                $komisiReusemart = $komisiPersen * $hargaBarang;

                $tanggalMasuk = \Carbon\Carbon::parse($barang->tanggal_masuk);
                $tanggalTransaksi = \Carbon\Carbon::parse($jadwal->transaksi->tanggal_transaksi);
                $selisihHari = $tanggalMasuk->diffInDays($tanggalTransaksi);

                $bonusDiskonPenitip = 0;
                if ($selisihHari < 7) {
                    $bonusDiskonPenitip = 0.10 * $komisiReusemart;
                    $komisiReusemart -= $bonusDiskonPenitip;
                }

                $komisiPenitip = $hargaBarang - ($komisiReusemart + $komisiHunter);

                // Buat record komisi
                \App\Models\Komisi::create([
                    'id_pegawai' => $barangHunter ? $barang->id_hunter : null,
                    'id_transaksi' => $jadwal->transaksi->id_transaksi,
                    'id_penitip' => $barang->id_penitip,
                    'id_barang' => $barang->id_barang,
                    'komisi' => $komisiReusemart,
                    'komisi_hunter' => $barangHunter ? $komisiHunter : null,
                    'komisi_penitip' => $komisiPenitip,
                ]);

                // Tambahkan saldo penitip
                $penitip = \App\Models\Penitip::find($barang->id_penitip);
                if ($penitip) {
                    $penitip->saldo_penitip += $komisiPenitip;
                    $penitip->save();
                }
            }

            // Ambil poin_didapat langsung dari transaksi
            $poinDidapat = $jadwal->transaksi->poin_didapat ?? 0;

            // Update poin pembeli dengan menambahkan poin_didapat
            $pembeli = $jadwal->transaksi->pembeli;
            if ($pembeli && $poinDidapat > 0) {
                $pembeli->poin += $poinDidapat;
                $pembeli->save();
            }

            DB::commit();

            $firebase = new FirebaseService();

            $pembeliFcmToken = $jadwal->transaksi->pembeli->fcm_token;
            $penitipFcmTokens = [];
            foreach ($jadwal->transaksi->detailTransaksi as $detail) {
                $barang = $detail->barang;
                $penitip = $barang->penitip;
                if ($penitip && $penitip->fcm_token) {
                    $penitipFcmTokens[] = $penitip->fcm_token;
                }
            }

            $titlePembeli = "Konfirmasi Pengambilan Transaksi #{$jadwal->transaksi->nomor_transaksi}";
            $titlePenitip = "Konfirmasi Pengambilan Barang (disini ID barang)";
            $titlePembeli = "Konfirmasi Pengambilan Transaksi #{$jadwal->transaksi->nomor_transaksi}";
            $bodyPembeli = "Barang telah berhasil diambil.";

            if ($pembeliFcmToken) {
                $firebase->sendMessage($pembeliFcmToken, $titlePembeli, $bodyPembeli);
            }

            foreach ($jadwal->transaksi->detailTransaksi as $detail) {
                $barang = $detail->barang;
                $penitip = $barang->penitip;
                if ($penitip && $penitip->fcm_token) {
                    $titlePenitip = "Konfirmasi Pengambilan Barang ID #{$barang->id_barang}";
                    $bodyPenitip = "Barang dengan nama {$barang->nama_barang} telah berhasil diambil oleh pembeli.";
                    $firebase->sendMessage($penitip->fcm_token, $titlePenitip, $bodyPenitip);
                }
            }
            return back()->with('success', 'Pengambilan berhasil dikonfirmasi dan komisi serta poin telah dihitung.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal menghitung komisi: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function autoPembatalanTransaksi()
    {
        DB::beginTransaction();
        try {
            $expiredJadwal = Penjadwalan::with([
                'pengiriman',
                'transaksi.detailTransaksi.barang',
                'transaksi.pembeli'
            ])
            ->where('jenis_jadwal', 'Diambil')
            ->where('status_jadwal', 'Dijadwalkan')
            ->whereDate('tanggal_jadwal', '<=', now()->subDays(2))
            ->whereHas('pengiriman', function ($query) {
                $query->whereNotIn('status_pengiriman', ['Diterima', 'Dibatalkan']);
            })
            ->get();

            foreach ($expiredJadwal as $jadwal) {
                $transaksi = $jadwal->transaksi;

                // Ubah status transaksi dan barang
                $transaksi->status_transaksi = 'Hangus';
                $transaksi->save();

                foreach ($transaksi->detailTransaksi as $detail) {
                    $barang = $detail->barang;
                    $barang->status_barang = 'Barang untuk donasi';
                    $barang->tanggal_keluar = now();
                    $barang->save();

                    // Hitung Komisi sama seperti Konfirmasi Pengambilan
                    $hargaBarang = $barang->harga_jual;
                    $barangHunter = $barang->barang_hunter;
                    $statusPerpanjangan = $barang->status_perpanjangan;

                    $periodeTopSeller = date('Y-m-01', strtotime($jadwal->tanggal_jadwal));
                    $isTopSeller = DB::table('badge')
                        ->where('id_penitip', $barang->id_penitip)
                        ->where('periode_pemberian', $periodeTopSeller)
                        ->exists();

                    $komisiPersen = $statusPerpanjangan ? 0.30 : 0.20;
                    if ($isTopSeller) {
                        $komisiPersen -= 0.01;
                    }

                    $komisiHunter = 0;
                    if ($barangHunter) {
                        $komisiHunter = 0.05 * $hargaBarang;
                        $komisiPersen -= 0.05;
                    }

                    $komisiReusemart = $komisiPersen * $hargaBarang;

                    $tanggalMasuk = \Carbon\Carbon::parse($barang->tanggal_masuk);
                    $tanggalTransaksi = \Carbon\Carbon::parse($transaksi->tanggal_transaksi);
                    $selisihHari = $tanggalMasuk->diffInDays($tanggalTransaksi);

                    $bonusDiskonPenitip = 0;
                    if ($selisihHari < 7) {
                        $bonusDiskonPenitip = 0.10 * $komisiReusemart;
                        $komisiReusemart -= $bonusDiskonPenitip;
                    }

                    $komisiPenitip = $hargaBarang - ($komisiReusemart + $komisiHunter);

                    \App\Models\Komisi::create([
                        'id_pegawai' => $barangHunter ? $barang->id_hunter : null,
                        'id_transaksi' => $transaksi->id_transaksi,
                        'id_penitip' => $barang->id_penitip,
                        'id_barang' => $barang->id_barang,
                        'komisi' => $komisiReusemart,
                        'komisi_hunter' => $barangHunter ? $komisiHunter : null,
                        'komisi_penitip' => $komisiPenitip,
                    ]);

                    // Tambah saldo penitip
                    $penitip = \App\Models\Penitip::find($barang->id_penitip);
                    if ($penitip) {
                        $penitip->saldo_penitip += $komisiPenitip;
                        $penitip->save();
                    }
                }

                // Update status pengiriman
                $pengiriman = $jadwal->pengiriman;
                if ($pengiriman) {
                    $pengiriman->status_pengiriman = 'Dibatalkan';
                    $pengiriman->save();
                }

                // Tambah poin pembeli
                $totalPoinPembeli = 0;
                foreach ($transaksi->detailTransaksi as $detail) {
                    $totalPoinPembeli += floor($detail->barang->harga_jual / 10000);
                }

                $pembeli = $transaksi->pembeli;
                if ($pembeli) {
                    $pembeli->poin += $totalPoinPembeli;
                    $pembeli->save();
                }
            }

            DB::commit();
            return back()->with('success', 'Proses auto pembatalan berhasil dijalankan.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal auto pembatalan transaksi: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

}
