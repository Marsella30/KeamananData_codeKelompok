<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Request Donasi Menunggu {{ $year }}</title>
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
    </style>
</head>
<body>
    <div class="details-box">
        <div class="header">
            <h2><strong>ReUse Mart</strong></h2>
            <p>Jl. Green Eco Park No. 456 Yogyakarta</p>
        </div>

        <h3><strong>LAPORAN REQUEST DONASI (MENUNGGU)</strong></h3>

        <div class="info">
            <p>Tahun: {{ $year }}</p>
            <p>Tanggal Cetak: {{ ucfirst(now()->locale('id')->isoFormat('DD MMMM YYYY')) }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID Request</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>Request</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $row)
                    <tr>
                        <td>{{ 'ORG' . $row->id_request }}</td>
                        <td>{{ optional($row->organisasi)->nama_organisasi ?? '-' }}</td>
                        <td>{{ optional($row->organisasi)->alamat ?? '-' }}</td>
                        <td class="text-start">{{ $row->barang_dibutuhkan }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Tidak ada data request donasi yang menunggu di tahun ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
