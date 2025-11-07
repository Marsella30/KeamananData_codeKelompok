<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi Penitip - {{ $penitip->nama_penitip }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        .header h2 { margin: 0; font-size: 16px; }
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
        .info p {
            margin: 2px 0;
            font-size: 14px;
        }
        tfoot tr td {
            font-weight: bold;
            background-color: #f2f2f2;
        }
        .text-start {
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="details-box">
        <div class="header">
            <h2><strong>ReUse Mart</strong></h2>
            <p>Jl. Green Eco Park No. 456 Yogyakarta</p>
        </div>

        <h3><strong>LAPORAN TRANSAKSI PENITIP</strong></h3>

        <div class="info">
            <p>ID Penitip: {{ $penitip->id_penitip }}</p>
            <p>Nama Penitip: {{ $penitip->nama_penitip }}</p>
            <p>Bulan: {{ $bulan }}</p>
            <p>Tahun: {{ $year }}</p>
            <p>Tanggal Cetak: {{ ucfirst($tanggalCetak) }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Kode Produk</th>
                    <th>Nama Produk</th>
                    <th>Tanggal Masuk</th>
                    <th>Tanggal Laku</th>
                    <th>Harga Jual Bersih</th>
                    <th>Bonus Terjual Cepat</th>
                    <th>Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($laporan as $item)
                    <tr>
                        <td>{{ $item['kode'] }}</td>
                        <td class="text-start">{{ $item['nama'] }}</td>
                        <td>{{ $item['tanggal_masuk'] }}</td>
                        <td>{{ $item['tanggal_laku'] }}</td>
                        <td>{{ number_format($item['harga_jual_bersih'], 0, ',', '.') }}</td>
                        <td>{{ number_format($item['bonus_terjual_cepat'], 0, ',', '.') }}</td>
                        <td>{{ number_format($item['pendapatan'], 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">Tidak ada transaksi di bulan ini.</td>
                    </tr>
                @endforelse
            </tbody>
            @if(count($laporan) > 0)
            <tfoot>
                <tr>
                    <td colspan="4">TOTAL</td>
                    <td>{{ number_format(collect($laporan)->sum('harga_jual_bersih'), 0, ',', '.') }}</td>
                    <td>{{ number_format(collect($laporan)->sum('bonus_terjual_cepat'), 0, ',', '.') }}</td>
                    <td>{{ number_format(collect($laporan)->sum('pendapatan'), 0, ',', '.') }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</body>
</html>
