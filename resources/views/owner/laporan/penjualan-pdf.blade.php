{{-- resources/views/owner/laporan-pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan Bulanan {{ $year }}</title>
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

        <h3><strong>LAPORAN PENJUALAN BULANAN</strong></h3>

        <div class="info">
            <p>Tahun: {{ $year }}</p>
            <p>Tanggal Cetak: {{ ucfirst(now()->locale('id')->isoFormat('DD MMMM YYYY')) }}</p>
        </div>

        {{-- TABEL --}}
        <table>
            <thead>
                <tr>
                    <th>Bulan</th>
                    <th>Jumlah Barang Terjual</th>
                    <th>Penjualan Penjualan Kotor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dataByMonth as $monthData)
                    <tr>
                        <td>{{ $monthData['month'] }}</td>
                        <td>{{ $monthData['count'] }}</td>
                        <td>{{ number_format($monthData['gross'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">Total</td>
                    <td>
                        {{-- Hitung total gross --}}
                        {{ number_format(collect($dataByMonth)->sum('gross'), 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>

        {{-- GRAFIK (PNG yang sudah base64‐encode) --}}
        <div class="chart-container mt-5">
            @if($chartBase64)
                <img src="data:image/png;base64,{{ $chartBase64 }}"
                     width="800" height="400"
                     alt="Grafik Penjualan">
            @else
                <p>Gagal memuat grafik.</p>
            @endif
        </div>
    </div>
</body>
</html>
