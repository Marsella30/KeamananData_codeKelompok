<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Badge;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BadgeController extends Controller
{
    /**
     * Ambil semua badge milik penitip yang sedang login
     */
    public function getTopSeller()
    {
        $badge = Badge::with('penitip')
            ->where('nama_badge', 'Top Seller')
            ->orderByDesc('periode_pemberian')
            ->first();

        if (!$badge) {
            return response()->json(['message' => 'Belum ada Top Seller.'], 404);
        }

        return response()->json([
            'message' => 'Top Seller bulan ini',
            'data' => [
                'id_badge' => $badge->id_badge,
                'id_penitip' => $badge->id_penitip,
                'nama_badge' => $badge->nama_badge,
                'bonus' => $badge->bonus,
                'total_penjualan' => $badge->total_penjualan,
                'periode_pemberian' => $badge->periode_pemberian,
                'penitip' => [
                    'nama_penitip' => $badge->penitip->nama_penitip,
                ]
            ],
        ]);
    }

    public function getTopSellerCurrentMonthFull()
    {
        $targetDate = now()->subMonth(); // Ambil bulan sebelumnya

        // Ambil ranking penitip
        $ranking = Transaksi::select(
                'penitip.id_penitip',
                'penitip.nama_penitip',
                DB::raw('SUM(detail_transaksi.sub_total) as total_penjualan')
            )
            ->join('detail_transaksi', 'transaksi.id_transaksi', '=', 'detail_transaksi.id_transaksi')
            ->join('barang_titipan', 'barang_titipan.id_barang', '=', 'detail_transaksi.id_barang')
            ->join('penitip', 'penitip.id_penitip', '=', 'barang_titipan.id_penitip')
            ->whereYear('transaksi.tanggal_transaksi', $targetDate->year)
            ->whereMonth('transaksi.tanggal_transaksi', $targetDate->month)
            ->whereRaw('LOWER(transaksi.status_transaksi) = ?', ['transaksi selesai'])
            ->groupBy('penitip.id_penitip', 'penitip.nama_penitip')
            ->orderByDesc('total_penjualan')
            ->take(10) // Ambil hanya 10 teratas
            ->get();

        $topSeller = $ranking->first();                   // Posisi #1
        $otherSellers = $ranking->slice(1)->values();     // Posisi #2–10

        return response()->json([
            'top_seller' => $topSeller,
            'other_sellers' => $otherSellers,
        ]);
    }

}
