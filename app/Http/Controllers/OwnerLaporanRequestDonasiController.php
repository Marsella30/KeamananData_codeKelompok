<?php

namespace App\Http\Controllers;

use App\Models\RequestDonasi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PDF;

class OwnerLaporanRequestDonasiController extends Controller
{
    /**
     * Tampilkan halaman laporan request donasi.
     */
    public function index(Request $request)
    {
        $year = $request->query('year', Carbon::now()->year);

        $requests = RequestDonasi::with('organisasi')
            ->where('status_request', 'Menunggu')
            ->orderBy('id_request', 'desc')
            ->paginate(10);

        return view('owner.laporan.requestDonasiIndex', [
            'requests'     => $requests,
            'year'         => $year,
            'tanggalCetak' => Carbon::today()->locale('id')->isoFormat('DD MMMM YYYY'),
        ]);
    }

    /**
     * Download PDF laporan request donasi.
     */
    public function downloadPDF(Request $request)
    {
        $year = $request->query('year', Carbon::now()->year);

        $requests = RequestDonasi::with('organisasi')
            ->where('status_request', 'Menunggu')
            ->orderBy('id_request', 'desc')
            ->get();

        $pdf = PDF::loadView('owner.laporan.requestDonasiPdf', [
            'requests'     => $requests,
            'year'         => $year,
            'tanggalCetak' => Carbon::today()->locale('id')->isoFormat('DD MMMM YYYY'),
        ])->setPaper('a4', 'portrait')
          ->setOptions(['enable-local-file-access' => true]);

        return $pdf->download("Laporan_Request_Donasi.pdf");
    }
}
