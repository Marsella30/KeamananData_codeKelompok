<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan Per Kategori Barang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h3 {
            margin: 0;
            font-size: 16px;
        }
        .header p {
            margin: 2px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .left {
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="header" style="text-align: left;">
        <p><strong>ReUse Mart</strong></p>
        <p>Jl. Green Eco Park No. 456 Yogyakarta</p>
        <h3>LAPORAN PENJUALAN PER KATEGORI BARANG</h3>
        <p>Tahun: {{ $tahun }}</p>
        <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="left">Kategori</th>
                <th>Jumlah Item Terjual</th>
                <th>Jumlah Item Gagal Terjual</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalTerjual = 0;
                $totalGagal = 0;
            @endphp
            @foreach ($laporan as $item)
                <tr>
                    <td class="left">{{ $item->nama_kategori }}</td>
                    <td>{{ $item->jumlah_terjual ?? '....' }}</td>
                    <td>{{ ($item->jumlah_gagal + $item->jumlah_donasi) ?? '....' }}</td>
                </tr>
                @php
                    $totalTerjual += $item->jumlah_terjual ?? 0;
                    $totalGagal += ($item->jumlah_gagal + $item->jumlah_donasi) ?? 0;
                @endphp
            @endforeach
            <tr>
                <th class="left">Total</th>
                <th>{{ $totalTerjual }}</th>
                <th>{{ $totalGagal }}</th>
            </tr>
        </tbody>
    </table>
</body>
</html>
