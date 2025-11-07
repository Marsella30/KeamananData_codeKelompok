<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\Penitip;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{

    // Menampilkan Top Seller Bulan Berjalan dan daftar ranking penitip
    public function getTopSellerCurrentMonth()
    {
        $targetDate = now()->subMonth();
        // dd($targetDate);

        $allSellers = Transaksi::select('penitip.id_penitip', 'penitip.nama_penitip', DB::raw('SUM(detail_transaksi.sub_total) as total_penjualan'))
            ->join('detail_transaksi', 'transaksi.id_transaksi', '=', 'detail_transaksi.id_transaksi')
            ->join('barang_titipan', 'barang_titipan.id_barang', '=', 'detail_transaksi.id_barang')
            ->join('penitip', 'penitip.id_penitip', '=', 'barang_titipan.id_penitip')
            // ->where(DB::raw('MONTH(transaksi.tanggal_transaksi)'), 6)
            // ->where(DB::raw('YEAR(transaksi.tanggal_transaksi)'), 2025)
            ->whereYear('transaksi.tanggal_transaksi', $targetDate->year)
            ->whereMonth('transaksi.tanggal_transaksi', $targetDate->month)
            ->whereRaw('LOWER(transaksi.status_transaksi) = ?', ['transaksi selesai'])
            ->groupBy('penitip.id_penitip', 'penitip.nama_penitip')
            ->orderByDesc('total_penjualan')
            ->get();

        $topSeller = $allSellers->first();

        return view('admin.topseller', [
            'topSeller' => $topSeller,
            'lastMonthTopSeller' => $this->getTopSellerLastMonth(),
            'rankingThisMonth' => $allSellers,
            'historyTopSellers' => $this->getAllTopSellersHistory(),
        ]);
    }

    // Menetapkan Top Seller Bulan Lalu dan memberikan Badge
    public function setTopSellerLastMonth(Request $request)
    {
        \Log::info('Menjalankan setTopSellerLastMonth()');

        if (now()->day !== 1) {
            return response()->json([
                'message' => 'Penentuan Top Seller hanya dapat dilakukan pada tanggal 1 setiap bulan.'
            ], 400);
        }

        $targetPeriod = now()->subMonth()->startOfMonth(); // 1 bulan sebelumnya
        $targetMonth = $targetPeriod->month;
        $targetYear = $targetPeriod->year;
        $periodePemberian = now()->startOfMonth(); // periode saat badge diberikan

        // Cek duplikasi
        $existing = Badge::where('nama_badge', 'Top Seller')
            ->whereDate('periode_pemberian', $periodePemberian)
            ->exists();

        if ($existing) {
            return response()->json([
                'message' => 'Top Seller sudah ditentukan untuk bulan lalu.'
            ], 400);
        }

        // Ambil top seller bulan lalu
        $topSellers = Transaksi::select('penitip.id_penitip', 'penitip.nama_penitip', DB::raw('SUM(detail_transaksi.sub_total) as total_penjualan'))
            ->join('detail_transaksi', 'transaksi.id_transaksi', '=', 'detail_transaksi.id_transaksi')
            ->join('barang_titipan', 'barang_titipan.id_barang', '=', 'detail_transaksi.id_barang')
            ->join('penitip', 'penitip.id_penitip', '=', 'barang_titipan.id_penitip')
            ->whereMonth('transaksi.tanggal_transaksi', $targetMonth)
            ->whereYear('transaksi.tanggal_transaksi', $targetYear)
            ->whereRaw('LOWER(transaksi.status_transaksi) = ?', ['transaksi selesai'])
            ->groupBy('penitip.id_penitip', 'penitip.nama_penitip')
            ->orderByDesc('total_penjualan')
            ->get();

        if ($topSellers->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada transaksi selesai pada bulan lalu.'
            ], 400);
        }

        $topSeller = $topSellers->first();

        DB::beginTransaction();
        try {
            // Simpan badge
            $bonus = round($topSeller->total_penjualan * 0.01, 2);
            dd($bonus);
            Badge::create([
                'nama_badge' => 'Top Seller',
                'id_penitip' => $topSeller->id_penitip,
                'total_penjualan' => $topSeller->total_penjualan,
                'bonus' => $bonus,
                'periode_pemberian' => $periodePemberian,
            ]);

            // Tambah saldo penitip
            Penitip::where('id_penitip', $topSeller->id_penitip)
                ->increment('saldo_penitip', $bonus);

            DB::commit();

            return response()->json([
                'message' => 'Top Seller berhasil ditentukan!',
                'top_seller' => [
                    'nama' => $topSeller->nama_penitip,
                    'total_penjualan' => $topSeller->total_penjualan,
                    'bonus' => $bonus
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal menetapkan Top Seller: ' . $e->getMessage());

            return response()->json([
                'message' => 'Gagal menetapkan Top Seller.'
            ], 500);
        }
    }

    private function getTopSellerLastMonth()
    {
        $lastMonth = now()->subMonth();

        $topSeller = Transaksi::select('penitip.id_penitip', 'penitip.nama_penitip', DB::raw('SUM(detail_transaksi.sub_total) as total_penjualan'))
            ->join('detail_transaksi', 'transaksi.id_transaksi', '=', 'detail_transaksi.id_transaksi')
            ->join('barang_titipan', 'barang_titipan.id_barang', '=', 'detail_transaksi.id_barang')
            ->join('penitip', 'penitip.id_penitip', '=', 'barang_titipan.id_penitip')
            ->whereMonth('transaksi.tanggal_transaksi', $lastMonth->month)
            ->whereYear('transaksi.tanggal_transaksi', $lastMonth->year)
            ->whereRaw('LOWER(transaksi.status_transaksi) = ?', ['transaksi selesai'])
            ->groupBy('penitip.id_penitip', 'penitip.nama_penitip')
            ->orderByDesc('total_penjualan')
            ->first();

            // dd($topSeller);
        return $topSeller; // Pastikan topSeller tidak null
    }

    private function getAllTopSellersHistory()
    {
        return Badge::where('nama_badge', 'Top Seller')
            ->with('penitip')
            ->orderByDesc('periode_pemberian')
            ->get();
    }

}
