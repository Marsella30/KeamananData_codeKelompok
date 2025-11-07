{{-- resources/views/owner/laporan/stok-pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Stok Gudang {{ $tanggalCetak }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        .header h2 { margin: 0; font-size: 16px;}
        .header p { margin: 0; font-size: 14px; }
        h3 {
            text-align: left;
            margin-bottom: 5px;
            text-decoration: underline;
        }
        .details-box {
            padding: 0 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            font-size: 14px;
        }
        table, th, td { border: 1px solid #333; }
        th, td { padding: 6px; text-align: center; }
        .chart-container { text-align: center; margin-bottom: 30px; }
        .info p {
            margin: 2px 0;
            font-size: 14px;
        }
        /* Garis tebal di antara isi tabel dan baris total */
        tfoot tr td {
            border-top: 2px solid #333;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="details-box">
        <div class="header">
            <h2><strong>ReUse Mart</strong></h2>
            <p>Jl. Green Eco Park No. 456 Yogyakarta</p>
        </div>

        <h3><strong>LAPORAN Stok Gudang</strong></h3>

        <div class="info">
            <p>Tanggal Cetak: {{ ucfirst(now()->locale('id')->isoFormat('DD MMMM YYYY')) }}</p>
        </div>

        {{-- Tabel Stok --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>Kode Produk</th>
                        <th>Nama Produk</th>
                        <th>Id Penitip</th>
                        <th>Nama Penitip</th>
                        <th>Tanggal Masuk</th>
                        <th>Perpanjangan</th>
                        <th>Id Hunter</th>
                        <th>Nama Hunter</th>
                        <th>Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stokItems as $item)
                        <tr>
                            <td class="text-center">{{ strtoupper(substr($item->nama_barang, 0, 1)) . $item->id_barang }}</td>
                            <td class="text-start">{{ $item->nama_barang }}</td>
                            <td style="text-align: center;">T{{ $item->id_penitip }}</td>
                            <td class="text-start">{{ $item->penitip->nama_penitip ?? '-' }}</td>
                            <td style="text-align: center;">{{ \Carbon\Carbon::parse($item->tanggal_masuk)->format('d/m/Y') }}</td>
                            <td style="text-align: center;">{{ $item->status_perpanjangan ? 'Ya' : 'Tidak' }}</td>
                            <td style="text-align: center;">{{ $item->id_hunter ? 'P' . $item->id_hunter : '-' }}</td>
                            <td class="text-start">{{ $item->hunter->nama_pegawai ?? '-' }}</td>
                            <td style="text-align: center;">{{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">Belum ada stok barang untuk hari ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
