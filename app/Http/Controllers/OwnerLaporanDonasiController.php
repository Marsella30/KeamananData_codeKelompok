<?php

namespace App\Http\Controllers;

use App\Models\Donasi;
use Carbon\Carbon;
use PDF;
use Illuminate\Http\Request;

class OwnerLaporanDonasiController extends Controller
{
    /**
     * Tampilkan halaman laporan donasi barang.
     */
    public function index(Request $request)
    {
        // Filter tahun (default tahun sekarang)
        $year = $request->query('year', Carbon::now()->year);

        // Ambil data donasi berdasarkan tahun
        $donations = Donasi::with(['barang_titipan', 'barang_titipan.penitip', 'barang_titipan.hunter', 'request_donasi.organisasi'])
            ->whereYear('tanggal_donasi', $year)
            ->orderBy('tanggal_donasi', 'desc')
            ->paginate(10);

            // dd($donations->toArray());

        return view('owner.laporan.donasiIndex', [
            'donations'    => $donations,
            'year'         => $year,
            'tanggalCetak' => Carbon::today()->locale('id')->isoFormat('DD MMMM YYYY'),
        ]);
    }

    /**
     * Download PDF laporan donasi barang.
     */
    public function downloadPDF(Request $request)
    {
        $year = $request->query('year', Carbon::now()->year);

        $donations = Donasi::with(['barang_titipan', 'barang_titipan.penitip', 'barang_titipan.hunter', 'request_donasi.organisasi'])
            ->whereYear('tanggal_donasi', $year)
            ->orderBy('tanggal_donasi', 'desc')
            ->get();

        $pdf = PDF::loadView('owner.laporan.donasiPdf', [
            'donations'    => $donations,
            'year'         => $year,
            'tanggalCetak' => Carbon::today()->locale('id')->isoFormat('DD MMMM YYYY'),
        ])->setPaper('a4', 'portrait')
          ->setOptions(['enable-local-file-access' => true]);

        return $pdf->download("Laporan_Donasi_Barang_{$year}.pdf");
    }
}
