<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\Penitip;
use App\Models\Transaksi;
use App\Models\BarangTitipan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TopSellerController extends Controller
{
    // Fungsi untuk mendapatkan Top Seller berdasarkan bulan tertentu (current / last)
    private function getTopSeller($month = null, $year = null)
    {
        if (!$month) {
            $month = now()->month;  // Gunakan bulan berjalan
        }

        if (!$year) {
            $year = now()->year;  // Gunakan tahun berjalan
        }

        return Transaksi::select('penitip.id_penitip', 'penitip.nama_penitip', DB::raw('SUM(detail_transaksi.sub_total) as total_penjualan'))
            ->join('detail_transaksi', 'transaksi.id_transaksi', '=', 'detail_transaksi.id_transaksi')
            ->join('barang_titipan', 'barang_titipan.id_barang', '=', 'detail_transaksi.id_barang')
            ->join('penitip', 'penitip.id_penitip', '=', 'barang_titipan.id_penitip')
            ->whereMonth('transaksi.tanggal_transaksi', $month)
            ->whereYear('transaksi.tanggal_transaksi', $year)
            ->whereRaw('LOWER(transaksi.status_transaksi) = ?', ['transaksi selesai'])
            ->groupBy('penitip.id_penitip', 'penitip.nama_penitip')
            ->orderByDesc('total_penjualan')
            ->first();
    }

    // Menetapkan Top Seller bulan berjalan
    public function setTopSellerCurrentMonth()
    {
        $topSeller = $this->getTopSeller(now()->month, now()->year);  // Mengambil Top Seller bulan ini

        if (!$topSeller) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada transaksi selesai bulan ini.',
            ], 404);
        }

        // Cek apakah penitip sudah pernah diberikan badge "Top Seller" bulan ini
        $existing = Badge::where('id_penitip', $topSeller->id_penitip)
            ->where('nama_badge', 'Top Seller')
            ->whereMonth('periode_pemberian', now()->month)
            ->whereYear('periode_pemberian', now()->year)
            ->with('penitip')
            ->exists();

        if (!$existing) {
            // Berikan badge Top Seller jika belum ada
            Badge::create([
                'id_penitip' => $topSeller->id_penitip,
                'nama_badge' => 'Top Seller',
                'periode_pemberian' => now()->addMonth()->startOfMonth(),  // Memberikan badge pada tanggal 1 bulan berikutnya
                'total_penjualan' => $topSeller->total_penjualan,  // Menyimpan total penjualan
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Top Seller bulan ini berhasil diperbarui dan badge diberikan.',
            'data' => $topSeller,
        ]);
    }

    // Menetapkan Top Seller bulan lalu dan memberikan badge serta bonus
    public function setTopSellerLastMonth(Request $request)
    {
        $lastMonth = now()->subMonth();
        $force = request()->query('force', false);

        // Cek apakah badge sudah ada bulan lalu, jika sudah, tidak perlu diproses lagi (kecuali dipaksa)
        $existing = Badge::where('nama_badge', 'Top Seller')
            ->whereMonth('periode_pemberian', $lastMonth->month)
            ->whereYear('periode_pemberian', $lastMonth->year)
            ->with('penitip')
            ->exists();

        if ($existing && !$force) {
            $badge = Badge::where('nama_badge', 'Top Seller')
                ->whereMonth('periode_pemberian', $lastMonth->month)
                ->whereYear('periode_pemberian', $lastMonth->year)
                ->with('penitip')
                ->first();

            // Hitung ulang total penjualan bulan lalu untuk penitip ini
            $totalPenjualan = Transaksi::join('detail_transaksi', 'transaksi.id_transaksi', '=', 'detail_transaksi.id_transaksi')
                ->join('barang_titipan', 'barang_titipan.id_barang', '=', 'detail_transaksi.id_barang')
                ->where('barang_titipan.id_penitip', $badge->id_penitip)
                ->whereMonth('transaksi.tanggal_transaksi', $lastMonth->month)
                ->whereYear('transaksi.tanggal_transaksi', $lastMonth->year)
                ->whereRaw('LOWER(transaksi.status_transaksi) = ?', ['transaksi selesai'])
                ->sum('detail_transaksi.sub_total');

            return response()->json([
                'message' => 'Top Seller sudah ditentukan.',
                'badge' => [
                    'id_penitip' => $badge->id_penitip,
                    'nama' => $badge->penitip->nama_penitip ?? 'Tidak diketahui',
                    'total_penjualan' => $totalPenjualan
                ]
            ], 200);
        }

        // Jika dipaksa, hapus badge lama dan lakukan perhitungan ulang
        if ($existing && $force) {
            Badge::where('nama_badge', 'Top Seller')
                ->whereMonth('periode_pemberian', $lastMonth->month)
                ->whereYear('periode_pemberian', $lastMonth->year)
                ->with('penitip')
                ->delete();
        }

        // Mengambil Top Seller bulan lalu
        $topSeller = $this->getTopSeller($lastMonth->month, $lastMonth->year); // Mengambil Top Seller bulan lalu

        if (!$topSeller) {
            return response()->json(['message' => 'Tidak ada data transaksi bulan lalu'], 404);
        }

        // Berikan badge "Top Seller" bulan lalu
        Badge::create([
            'id_penitip' => $topSeller->id_penitip,
            'nama_badge' => 'Top Seller',
            'periode_pemberian' => now()->addMonth()->startOfMonth(),  // Memberikan badge pada tanggal 1 bulan berikutnya
            'total_penjualan' => $topSeller->total_penjualan,  // Menyimpan total penjualan
        ]);

        return response()->json([
            'message' => 'Top Seller bulan lalu ditetapkan.',
            'top_seller' => [
                'id_penitip' => $topSeller->id_penitip,
                'nama' => $topSeller->nama_penitip,
                'total_penjualan' => $topSeller->total_penjualan,
            ]
        ]);
    }

    // Memberikan bonus ke Top Seller bulan lalu
    public function giveBonusToTopSeller()
    {
        $lastMonth = now()->subMonth()->format('Y-m');

        // Mendapatkan Top Seller bulan lalu
        $topSeller = Badge::where('nama_badge', 'Top Seller')
            ->whereMonth('periode_pemberian', now()->subMonth()->month)
            ->whereYear('periode_pemberian', now()->subMonth()->year)
            ->first();

        if (!$topSeller) {
            return response()->json(['message' => 'Top Seller belum ditentukan'], 404);
        }

        // Menghitung total penjualan untuk penitip
        $totalPenjualan = Transaksi::where('id_penitip', $topSeller->id_penitip)
            ->where('status_transaksi', 'selesai')
            ->whereMonth('updated_at', now()->subMonth()->month)
            ->whereYear('updated_at', now()->subMonth()->year)
            ->sum('total_pembayaran');

        // Menghitung bonus 1% dari total penjualan
        $bonus = round($totalPenjualan * 0.01);

        // Menambahkan bonus ke poin penitip
        $penitip = Penitip::find($topSeller->id_penitip);
        $penitip->poin += $bonus;
        $penitip->save();

        return response()->json([
            'message' => 'Bonus diberikan',
            'bonus' => $bonus,
            'penitip' => $penitip
        ]);
    }

    // Mengubah status barang yang tidak diambil lebih dari 7 hari menjadi "barang untuk donasi"
    public function changeStatusBarangForDonation()
    {
        $updatedRows = BarangTitipan::where('status_barang', 'Tersedia')
            ->where('tanggal_akhir', '<', now()->subDays(7))
            ->update(['status_barang' => 'barang untuk donasi']);

        return response()->json([
            'success' => true,
            'message' => "$updatedRows barang telah diubah statusnya ke 'barang untuk donasi'.",
        ]);
    }
}
