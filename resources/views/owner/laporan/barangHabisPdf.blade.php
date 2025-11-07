<!DOCTYPE html>
<html>
<head>
    <title>Laporan Barang yang Masa Penitipannya Sudah Habis</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: left; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: center; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <div class="header">
        <p><strong>ReUse Mart</strong></p>
        <p>Jl. Green Eco Park No. 456 Yogyakarta</p>
        <h3>LAPORAN Barang yang Masa Penitipannya Sudah Habis</h3>
        <p>Tanggal cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>Kode Produk</th>
                <th>Nama Produk</th>
                <th>Id Penitip</th>
                <th>Nama Penitip</th>
                <th>Tanggal Masuk</th>
                <th>Tanggal Akhir</th>
                <th>Batas Ambil</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporan as $item)
                <tr>
                    <td class="text-center">{{ strtoupper(substr($item->nama_barang, 0, 1)) . $item->id_barang }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ 'T' . $item->id_penitip }}</td>
                    <td>{{ optional($item->penitip)->nama_penitip ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_masuk)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_akhir)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_akhir)->addDays(7)->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Tidak ada data barang yang masa penitipannya sudah habis.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
