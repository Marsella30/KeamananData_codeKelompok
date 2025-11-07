<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pegawai;
use \App\Models\Penitip;
use App\Models\Pengiriman;
use Illuminate\Support\Facades\DB;
use App\Services\FirebaseService;
use Carbon\Carbon;

class KurirController extends Controller
{
    public function index()
    {
        // Pastikan sudah login dengan guard 'pegawai'
        $pegawai = Auth::guard('sanctum')->user();

        // Tambahkan validasi untuk role 'kurir'
        if ($pegawai && $pegawai->jabatan && strtolower($pegawai->jabatan->nama_jabatan) === 'kurir') {
            return response()->json([
                'status' => true,
                'message' => 'Data kurir berhasil diambil.',
                'data' => [
                    'id_pegawai' => $pegawai->id_pegawai,
                    'nama_pegawai' => $pegawai->nama_pegawai,
                    'username' =>$pegawai->username,
                    'email' => $pegawai->email,
                    'notelp' => $pegawai->notelp,
                    'tanggal_lahir' => $pegawai->tanggal_lahir,
                    'jabatan' => $pegawai->jabatan->nama_jabatan,
                    'status_aktif' => $pegawai->status_aktif,
                ]
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Pegawai bukan kurir atau belum login.'
            ], 403);
        }
    }

    public function historyPengiriman()
    {
        $kurir = Auth::guard('sanctum')->user();

        if (!$kurir) {
            return response()->json([
                'success' => false,
                'message' => 'Kurir tidak terautentikasi.'
            ], 401);
        }

        $history = Pengiriman::join('penjadwalan', 'pengiriman.id_jadwal', '=', 'penjadwalan.id_jadwal')
        ->with([
            'penjadwalan.transaksi.detailTransaksi.barang_titipan',
        ])
        ->where('pengiriman.id_pegawai', $kurir->id_pegawai)
        ->where('pengiriman.status_pengiriman', 'Sampai')
        ->orderBy('penjadwalan.tanggal_jadwal', 'desc')
        ->get()
        ->map(function ($item) {
            $detail = optional($item->penjadwalan->transaksi->detailTransaksi->first());
            $namaBarang = optional($detail->barang_titipan)->nama_barang ?? '-';

            return [
                'id_pengiriman' => $item->id_pengiriman,
                'nama_barang' => $namaBarang,
                'tanggal_jadwal' => optional($item->penjadwalan)->tanggal_jadwal
                    ? $item->penjadwalan->tanggal_jadwal->format('d/m/Y')
                    : '-',
                'status_pengiriman' => $item->status_pengiriman ?? '-',
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }

    public function showPengiriman()
    {
        $kurir = Auth::guard('sanctum')->user();

        if (!$kurir) {
            return response()->json([
                'success' => false,
                'message' => 'Kurir tidak terautentikasi.'
            ], 401);
        }

        $history = Pengiriman::with([
            'penjadwalan.transaksi.detailTransaksi.barang_titipan'
        ])
        ->where('id_pegawai', $kurir->id_pegawai)
        ->where('status_pengiriman', 'Diantar')
        ->orderBy('id_pengiriman', 'desc')
        ->get()
        ->map(function ($item) {
            // Ambil nama_barang dari detailTransaksi pertama (jika ada)
            $detail = optional($item->penjadwalan->transaksi->detailTransaksi->first());
            $namaBarang = optional($detail->barang_titipan)->nama_barang ?? '-';

            return [
                'id_pengiriman' => $item->id_pengiriman,
                'nama_barang' => $namaBarang,
                'tanggal_jadwal' => optional($item->penjadwalan)->tanggal_jadwal
                    ? $item->penjadwalan->tanggal_jadwal->format('d/m/Y H:i')
                    : '-',
                'status_pengiriman' => $item->status_pengiriman ?? '-',
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }

    public function detailPengiriman($id)
    {
        $kurir = Auth::guard('sanctum')->user();

        if (!$kurir) {
            return response()->json([
                'success' => false,
                'message' => 'Kurir tidak terautentikasi.'
            ], 401);
        }

        $pengiriman = Pengiriman::with([
            'penjadwalan.transaksi.pembeli',
            'penjadwalan.transaksi.alamat',
            'penjadwalan.transaksi.detailTransaksi.barang_titipan'
        ])
        ->where('id_pengiriman', $id)
        ->where('id_pegawai', $kurir->id_pegawai)
        ->first();

        if (!$pengiriman) {
            return response()->json([
                'success' => false,
                'message' => 'Pengiriman tidak ditemukan.'
            ], 404);
        }

        $detail = optional($pengiriman->penjadwalan->transaksi->detailTransaksi->first());
        $namaBarang = optional($detail->barang_titipan)->nama_barang ?? '-';
        $namaPembeli = optional($pengiriman->penjadwalan->transaksi->pembeli)->nama_pembeli ?? '-';
        $alamatPembeli = optional($pengiriman->penjadwalan->transaksi->alamat);
        $alamatLengkap = $alamatPembeli 
        ? "{$alamatPembeli->jalan}, {$alamatPembeli->kelurahan}, {$alamatPembeli->kecamatan}, {$alamatPembeli->kota}, {$alamatPembeli->provinsi}, {$alamatPembeli->kode_pos}" 
        : '-';

        return response()->json([
            'success' => true,
            'data' => [
                'id_pengiriman' => $pengiriman->id_pengiriman,
                'nama_barang' => $namaBarang,
                'tanggal_jadwal' => optional($pengiriman->penjadwalan)->tanggal_jadwal
                    ? $pengiriman->penjadwalan->tanggal_jadwal->format('d/m/Y H:i')
                    : '-',
                'status_pengiriman' => $pengiriman->status_pengiriman ?? '-',
                'nama_pembeli' => $namaPembeli,
                'alamat_pembeli' => $alamatLengkap,
            ]
        ]);
    }

    public function konfirmasiPengiriman($id)
    {
        DB::beginTransaction();
        try {
            $kurir = Auth::guard('sanctum')->user();

            if (!$kurir) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kurir tidak terautentikasi.'
                ], 401);
            }

            $pengiriman = Pengiriman::with([
                'penjadwalan.transaksi.detailTransaksi.barang.penitip',
                'penjadwalan.transaksi.pembeli'
            ])
            ->where('id_pengiriman', $id)
            ->where('id_pegawai', $kurir->id_pegawai)
            ->first();

            if (!$pengiriman) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengiriman tidak ditemukan.'
                ], 404);
            }

            if ($pengiriman->status_pengiriman !== 'Diantar') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya pengiriman dengan status Diantar yang dapat dikonfirmasi.'
                ], 400);
            }

            // Update status pengiriman
            $pengiriman->status_pengiriman = 'Sampai';
            $pengiriman->save();

            // Hitung Komisi
            $jadwal = $pengiriman->penjadwalan;

            foreach ($jadwal->transaksi->detailTransaksi as $detail) {
                $barang = $detail->barang;
                $barang->tanggal_keluar = now();
                $barang->save();

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

                $tanggalMasuk = Carbon::parse($barang->tanggal_masuk);
                $tanggalTransaksi = Carbon::parse($jadwal->transaksi->tanggal_transaksi);
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
                $penitip = Penitip::find($barang->id_penitip);
                if ($penitip) {
                    $penitip->saldo_penitip += $komisiPenitip;
                    $penitip->save();
                }
            }

            // Tambahkan poin pembeli
            $poinDidapat = $jadwal->transaksi->poin_didapat ?? 0;
            $pembeli = $jadwal->transaksi->pembeli;
            if ($pembeli && $poinDidapat > 0) {
                $pembeli->poin += $poinDidapat;
                $pembeli->save();
            }

            DB::commit();

            // KIRIM PUSH NOTIFICATION
            $firebase = new FirebaseService();

            // Gabungkan nama barang jadi satu string
            $namaBarangList = $jadwal->transaksi->detailTransaksi
                ->map(fn($d) => optional($d->barang)->nama_barang)
                ->filter() // Hilangkan null
                ->unique()
                ->implode(', ');

            $title = "Barang Sampai: $namaBarangList";
            $body = "Barang telah diterima oleh pembeli.";

            // Notifikasi ke Pembeli
            if ($pembeli && $pembeli->fcm_token) {
                $firebase->sendMessage($pembeli->fcm_token, $title, $body);
            }

            // Notifikasi ke Penitip
            $penitipTokens = [];
            foreach ($jadwal->transaksi->detailTransaksi as $detail) {
                $penitip = $detail->barang->penitip;
                if ($penitip && $penitip->fcm_token) {
                    $penitipTokens[] = $penitip->fcm_token;
                }
            }

            foreach (array_unique($penitipTokens) as $token) {
                $firebase->sendMessage($token, $title, $body);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pengiriman berhasil dikonfirmasi menjadi Sampai. Komisi, poin, dan notifikasi telah dikirim.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal konfirmasi pengiriman: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
