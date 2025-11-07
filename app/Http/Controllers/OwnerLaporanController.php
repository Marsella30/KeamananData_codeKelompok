<?php

namespace App\Http\Controllers;

// use App\Models\DetailTransaksi;
// use App\Models\Transaksi;
use App\Models\BarangTitipan;
use App\Models\Komisi;
use App\Models\Kategori;
use App\Models\Transaksi;
use App\Models\Badge;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PDF;
use Illuminate\Support\Facades\Http; 

class OwnerLaporanController extends Controller
{

    public function index(Request $request)
    {
        $year = $request->query('year', Carbon::now()->year);

        // Inisialisasi array per bulan
        $dataByMonth = [];
        for ($m = 1; $m <= 12; $m++) {
            $dataByMonth[$m] = [
                'month' => Carbon::createFromDate($year, $m, 1)->format('M'),
                'count' => 0,
                'gross' => 0,
            ];
        }

        // Ambil semua transaksi yang selesai di tahun itu, beserta detail_transaksi
        $transactions = Transaksi::with('detailTransaksi')
            ->where('status_transaksi', 'Transaksi Selesai')
            ->whereYear('tanggal_transaksi', $year)
            ->get(['id_transaksi', 'tanggal_transaksi']);

        // Hitung per bulan
        foreach ($transactions as $tx) {
            $monthIndex = Carbon::parse($tx->tanggal_transaksi)->month;

            // setiap detail adalah satu barang terjual
            $itemCount = $tx->detailTransaksi->count();
            $itemGross = $tx->detailTransaksi->sum('sub_total');

            $dataByMonth[$monthIndex]['count'] += $itemCount;
            $dataByMonth[$monthIndex]['gross'] += $itemGross;
        }

        return view('owner.laporan.penjualan', [
            'dataByMonth' => $dataByMonth,
            'year'        => $year,
        ]);
    }

    public function downloadPDF(Request $request)
    {
        $year = $request->query('year', Carbon::now()->year);

        // 1. Kumpulkan data penjualan per bulan
        $dataByMonth = [];
        for ($m = 1; $m <= 12; $m++) {
            $dataByMonth[$m] = [
                'month' => Carbon::createFromDate($year, $m, 1)
                                ->locale('id')
                                ->isoFormat('MMMM'),
                'count' => 0,
                'gross' => 0,
            ];
        }

        $transactions = Transaksi::with('detailTransaksi')
            ->where('status_transaksi', 'Transaksi Selesai')
            ->whereYear('tanggal_transaksi', $year)
            ->get(['id_transaksi', 'tanggal_transaksi']);

        foreach ($transactions as $tx) {
            $mIdx = Carbon::parse($tx->tanggal_transaksi)->month;
            $dataByMonth[$mIdx]['count'] += $tx->detailTransaksi->count();
            $dataByMonth[$mIdx]['gross'] += $tx->detailTransaksi->sum('sub_total');
        }

        // 2. Siapkan ChartConfig
        $labels    = array_column($dataByMonth, 'month');
        $dataCount = array_column($dataByMonth, 'count');
        $dataGross = array_column($dataByMonth, 'gross');

        $chartConfig = [
            'type' => 'bar',
            'data' => [
                'labels'   => $labels,
                'datasets' => [
                    [
                        'label'           => 'Jumlah Terjual',
                        'data'            => $dataCount,
                        'backgroundColor' => 'rgba(54, 162, 235, 0.6)',
                        'borderColor'     => 'rgba(54, 162, 235, 1)',
                        'borderWidth'     => 1,
                    ],
                    [
                        'label'           => 'Penjualan Kotor (Rp)',
                        'data'            => $dataGross,
                        'backgroundColor' => 'rgba(255, 99, 132, 0.6)',
                        'borderColor'     => 'rgba(255, 99, 132, 1)',
                        'borderWidth'     => 1,
                    ],
                ],
            ],
            'options' => [
                'responsive' => false,
                'scales'     => [
                    'y' => ['beginAtZero' => true],
                ],
            ],
        ];

        $query    = http_build_query(['c'=>json_encode($chartConfig),'w'=>800,'h'=>400,'format'=>'png']);
        $pngData  = @file_get_contents("https://quickchart.io/chart?{$query}");
        $chartBase64 = $pngData ? base64_encode($pngData) : null;

        // 3. Render PDF
        $pdfView = view('owner.laporan.penjualan-pdf', [
            'dataByMonth'=> $dataByMonth,
            'year'       => $year,
            'chartBase64'=> $chartBase64,
        ])->render();

        $pdf = PDF::loadHTML($pdfView)
            ->setOptions(['no-outline'=>true,'enable-local-file-access'=>true])
            ->setPaper('a4','portrait');

        return $pdf->download("Laporan_Penjualan_{$year}.pdf");
    }

    public function stokIndex(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $query = BarangTitipan::with(['penitip', 'hunter'])->where('status_barang', 'Tersedia');

        // Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%$search%")
                ->orWhere('id_barang', 'like', "%$search%")
                ->orWhere('id_penitip', 'like', "%$search%")
                ->orWhereHas('penitip', fn($q2) => $q2->where('nama_penitip', 'like', "%$search%"))
                ->orWhere('id_hunter', 'like', "%$search%")
                ->orWhereHas('hunter', fn($q3) => $q3->where('nama_pegawai', 'like', "%$search%"))
                ->orWhere('harga_jual', 'like', "%$search%");
            });
        }

        // Urutan
        $sort = $request->get('sort', 'tanggal_masuk');
        $direction = $request->get('direction', 'asc');
        $allowedSorts = ['id_barang', 'nama_barang', 'id_penitip', 'tanggal_masuk', 'harga_jual'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        }

        $stokItems = $query->paginate(10)->withQueryString();

        return view('owner.laporan.stokGudang', [
            'stokItems' => $stokItems,
            'tanggalCetak' => $today,
            'isPdf' => false,
        ]);
    }

    public function stokDownload(Request $request)
    {
        $today = Carbon::today()->toDateString();

        // Ambil semua data, bukan paginate
        $stokItems = BarangTitipan::with(['penitip', 'hunter'])
            ->where('status_barang', 'Tersedia')
            ->orderBy('tanggal_masuk', 'asc')
            ->get();

        // Gunakan view khusus untuk PDF yang ringan
        $pdf = PDF::loadView('owner.laporan.stokPdf', [
            'stokItems'    => $stokItems,
            'tanggalCetak' => $today,
        ])->setPaper('a4', 'landscape')
        ->setOptions(['enable-local-file-access' => true]);

        return $pdf->download("Laporan_Stok_Gudang_{$today}.pdf");
    }

    // public function komisiIndex(Request $request)
    // {
    //     // filter bulan & tahun
    //     $month = $request->query('month', Carbon::now()->month);
    //     $year  = $request->query('year', Carbon::now()->year);

    //     // ambil barang yang sudah terjual bulan tersebut
    //     $items = BarangTitipan::with(['penitip'])
    //         ->where('status_barang', 'Terjual')
    //         ->whereYear('tanggal_keluar', $year)
    //         ->whereMonth('tanggal_keluar', $month)
    //         ->get();

    //     // hitung komisi per item
    //     $data = $items->map(function($item) {
    //         $harga = $item->harga_jual;
    //         $days  = $item->tanggal_masuk->diffInDays($item->tanggal_keluar);

    //         $baseRate   = 0.20;
    //         $hunterRate = 0.05;
    //         $bonusRate  = 0.10;

    //         if ($item->status_perpanjangan) {
    //             $baseRate   = 0.30;
    //             $hunterRate = 0.00;
    //             $bonusRate  = 0.00;
    //         }

    //         $komisiKotor = $harga * $baseRate;

    //         $komisiHunter = ($item->barang_hunter && $item->id_hunter)
    //             ? $harga * $hunterRate
    //             : 0;

    //         $komisiPenitip = (!$item->status_perpanjangan && $days < 7)
    //             ? $komisiKotor * $bonusRate
    //             : 0;

    //         $komisiReUseMart = $komisiKotor - $komisiHunter - $komisiPenitip;

    //         return [
    //             'kode'           => $item->id_barang,
    //             'nama'           => $item->nama_barang,
    //             'harga'          => $harga,
    //             'tanggal_masuk'  => $item->tanggal_masuk->format('d/m/Y'),
    //             'tanggal_laku'   => $item->tanggal_keluar->format('d/m/Y'),
    //             'komisi_kotor'   => $komisiKotor,
    //             'komisi_hunter'  => $komisiHunter,
    //             'komisi_penitip' => $komisiPenitip,
    //             'komisi_reuse'   => $komisiReUseMart,
    //         ];
    //     });

    //     $bulan = Carbon::createFromDate($year, $month, 1)->locale('id')->isoFormat('MMMM');

    //     return view('owner.laporan.komisiIndex', [
    //         'data'          => $data,
    //         'monthName'     => ucfirst($bulan),
    //         'year'          => $year,
    //         'tanggalCetak'  => Carbon::today()->locale('id')->isoFormat('DD MMMM YYYY'),
    //     ]);
    // }

    public function komisiIndex(Request $request)
    {
        // 1. Ambil filter bulan & tahun
        $month = $request->query('month', Carbon::now()->month);
        $year  = $request->query('year',  Carbon::now()->year);

        // 2. Tarik semua komisi yg tercatat untuk barang yang tanggal_keluar-nya di periode itu
        $komisis = Komisi::with(['transaksi.detailTransaksi.barang','penitip','pegawai'])
            ->whereHas('transaksi', function($q) use($month, $year) {
                $q->whereMonth('tanggal_transaksi', $month)
                ->whereYear('tanggal_transaksi',  $year);
            })
            ->get();

        // 3. Map ke array untuk view
        $data = $komisis->map(function($k) {
            $detail = $k->transaksi->detailTransaksi
                        ->firstWhere('id_barang', $k->id_barang);
            $barang = optional($detail)->barang;

            $tanggalMasuk = optional($barang)->tanggal_masuk;
            $tanggalTransaksi = optional($k->transaksi)->tanggal_transaksi;

            $komisiDasar = 0.20 * ($barang->harga_jual ?? 0); // 20% komisi dasar
            $bonus = 0;

            if ($tanggalMasuk && $tanggalTransaksi) {
                $durasi = Carbon::parse($tanggalMasuk)->diffInDays(Carbon::parse($tanggalTransaksi));

                $isCepatLaku = $durasi < 7;

                $isTopSeller = \App\Models\Badge::where('id_penitip', $k->id_penitip)
                    ->where('nama_badge', 'Top Seller')
                    ->whereMonth('periode_pemberian', Carbon::parse($tanggalTransaksi)->month)
                    ->whereYear('periode_pemberian', Carbon::parse($tanggalTransaksi)->year)
                    ->exists();

                if ($isTopSeller) {
                    $bonus += $komisiDasar * 0.01;
                }

                if ($isCepatLaku) {
                    $bonus += $komisiDasar * 0.10;
                }
            }

            return [
                'kode'           => $barang->id_barang ?? '-',
                'nama'           => $barang->nama_barang ?? '-',
                'harga'          => $barang->harga_jual ?? 0,
                'tanggal_masuk'  => $tanggalMasuk ? $tanggalMasuk->format('d/m/Y') : '-',
                'tanggal_laku'   => $tanggalTransaksi ? Carbon::parse($tanggalTransaksi)->format('d/m/Y') : '-',
                'komisi_hunter'  => $k->komisi_hunter  ?? 0,
                'komisi_penitip' => $k->komisi_penitip ?? 0,
                'komisi_reuse'   => $k->komisi        ?? 0,
                'bonus_penitip'  => (int) $bonus,
            ];
        });

        // 4. Nama bulan & tanggal cetak
        $bulan       = Carbon::createFromDate($year, $month, 1)
                               ->locale('id')->isoFormat('MMMM');
        $tanggalCetak = Carbon::today()
                               ->locale('id')->isoFormat('DD MMMM YYYY');

        // 5. Render view
        return view('owner.laporan.komisiIndex', [
            'data'         => $data,
            'monthName'    => ucfirst($bulan),
            'year'         => $year,
            'tanggalCetak' => $tanggalCetak,
        ]);
    }

    public function komisiDownload(Request $request)
    {
        // 1. Sama filter bulan & tahun
        $month = $request->query('month', Carbon::now()->month);
        $year  = $request->query('year',  Carbon::now()->year);

        // 2. Tarik data Komisi seperti di index
        $komisis = Komisi::with(['transaksi.detailTransaksi.barang','penitip','pegawai'])
            ->whereHas('transaksi', function($q) use($month, $year) {
                $q->whereMonth('tanggal_transaksi', $month)
                ->whereYear('tanggal_transaksi',  $year);
            })
            ->get();

        // 3. Map ke array untuk PDF
        $data = $komisis->map(function($k) {
            $detail = $k->transaksi->detailTransaksi
                        ->firstWhere('id_barang', $k->id_barang);
            $barang = optional($detail)->barang;

            $tanggalMasuk = optional($barang)->tanggal_masuk;
            $tanggalTransaksi = optional($k->transaksi)->tanggal_transaksi;

            // Default 0
            $bonus = 0;

            // Hitung durasi laku
            if ($tanggalMasuk && $tanggalTransaksi) {
                $durasi = Carbon::parse($tanggalMasuk)->diffInDays(Carbon::parse($tanggalTransaksi));

                // Ambil persentase bonus berdasarkan ketentuan
                $isCepatLaku = $durasi < 7;

                // Cek top seller dari tabel badge_penitip
                $isTopSeller = \App\Models\Badge::where('id_penitip', $k->id_penitip)
                    ->where('nama_badge', 'Top Seller')
                    ->whereMonth('periode_pemberian', Carbon::parse($tanggalTransaksi)->month)
                    ->whereYear('periode_pemberian', Carbon::parse($tanggalTransaksi)->year)
                    ->exists();

                $komisiReuseAsli = $k->komisi;

                if ($isTopSeller) {
                    $bonus += $komisiReuseAsli * 0.01;
                }

                if ($isCepatLaku) {
                    $bonus += $komisiReuseAsli * 0.10;
                }
            }

            return [
                'kode'           => $barang->id_barang ?? '-',
                'nama'           => $barang->nama_barang ?? '-',
                'harga'          => $barang->harga_jual ?? 0,
                'tanggal_masuk'  => $tanggalMasuk ? $tanggalMasuk->format('d/m/Y') : '-',
                'tanggal_laku'   => $tanggalTransaksi ? Carbon::parse($tanggalTransaksi)->format('d/m/Y') : '-',
                'komisi_hunter'  => $k->komisi_hunter  ?? 0,
                'komisi_penitip' => $k->komisi_penitip ?? 0,
                'komisi_reuse'   => $k->komisi        ?? 0,
                'bonus_penitip'  => (int) $bonus,
            ];
        });

        // 4. Nama bulan & tanggal cetak
        $bulan       = Carbon::createFromDate($year, $month, 1)
                               ->locale('id')->isoFormat('MMMM');
        $tanggalCetak = Carbon::today()
                               ->locale('id')->isoFormat('DD MMMM YYYY');

        // 5. Generate PDF
        $pdf = PDF::loadView('owner.laporan.komisiPdf', [
            'data'         => $data,
            'monthName'    => ucfirst($bulan),
            'year'         => $year,
            'tanggalCetak' => $tanggalCetak,
        ])->setPaper('a4', 'portrait');

        return $pdf->download("Komisi_{$bulan}_{$year}.pdf");
    }

    public function laporanPerKategori(Request $request)
    {
        // Tangkap tahun dari request jika ada, default ke tahun sekarang
        $tahun = $request->input('tahun', date('Y'));

        $laporan = Kategori::select('kategori.nama_kategori')
            ->leftJoin('barang_titipan', 'kategori.id_kategori', '=', 'barang_titipan.id_kategori')
            ->selectRaw("
                kategori.nama_kategori,
                SUM(CASE WHEN barang_titipan.status_barang = 'Terjual' AND YEAR(barang_titipan.tanggal_keluar) = {$tahun} THEN 1 ELSE 0 END) as jumlah_terjual,
                SUM(CASE WHEN barang_titipan.status_barang = 'Didonasikan' AND YEAR(barang_titipan.tanggal_keluar) = {$tahun} THEN 1 ELSE 0 END) as jumlah_donasi,
                SUM(CASE WHEN barang_titipan.status_barang = 'Diambil Kembali' AND YEAR(barang_titipan.tanggal_keluar) = {$tahun} THEN 1 ELSE 0 END) as jumlah_gagal
            ")
            ->groupBy('kategori.nama_kategori')
            ->orderBy('kategori.nama_kategori')
            ->get();

        return view('owner.laporan.penjualanPerKategori', [
            'laporan' => $laporan,
            'tahun' => $tahun
        ]);
    }

    public function downloadLaporanPerKategori(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));

        $laporan = Kategori::select('kategori.nama_kategori')
            ->leftJoin('barang_titipan', 'kategori.id_kategori', '=', 'barang_titipan.id_kategori')
            ->selectRaw("
                kategori.nama_kategori,
                SUM(CASE WHEN barang_titipan.status_barang = 'Terjual' AND YEAR(barang_titipan.tanggal_keluar) = {$tahun} THEN 1 ELSE 0 END) as jumlah_terjual,
                SUM(CASE WHEN barang_titipan.status_barang = 'Didonasikan' AND YEAR(barang_titipan.tanggal_keluar) = {$tahun} THEN 1 ELSE 0 END) as jumlah_donasi,
                SUM(CASE WHEN barang_titipan.status_barang = 'Diambil Kembali' AND YEAR(barang_titipan.tanggal_keluar) = {$tahun} THEN 1 ELSE 0 END) as jumlah_gagal
            ")
            ->groupBy('kategori.nama_kategori')
            ->orderBy('kategori.nama_kategori')
            ->get();

        $pdf = PDF::loadView('owner.laporan.penjualanPerKategori-pdf', [
            'laporan' => $laporan,
            'tahun' => $tahun
        ]);

        return $pdf->download('Laporan_Penjualan_Per_Kategori_' . $tahun . '.pdf');
    }

    public function laporanBarangHabis(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));

        // Ambil barang yang masa penitipannya habis
        $laporan = BarangTitipan::with('penitip')
            ->whereYear('tanggal_akhir', '=', $tahun)
            ->where('status_barang', 'Tersedia')
            ->whereDate('tanggal_akhir', '<', now())
            ->whereDate('tanggal_akhir', '>=', now()->subDays(7))
            ->orderBy('tanggal_akhir')
            ->paginate(10)
            ->appends(['tahun' => $tahun]);

        return view('owner.laporan.barangHabis', [
            'laporan' => $laporan,
            'tahun' => $tahun
        ]);
    }

    public function downloadLaporanBarangHabis(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));

        $laporan = BarangTitipan::with('penitip')
            ->whereYear('tanggal_akhir', '=', $tahun)
            ->where('status_barang', 'Tersedia')
            ->whereDate('tanggal_akhir', '<', now())
            ->whereDate('tanggal_akhir', '>=', now()->subDays(7))
            ->orderBy('tanggal_akhir')
            ->get();

        $pdf = PDF::loadView('owner.laporan.barangHabisPdf', [
            'laporan' => $laporan,
            'tahun' => $tahun
        ]);

        return $pdf->download('Laporan-Barang-Habis-' . $tahun . '.pdf');
    }

}