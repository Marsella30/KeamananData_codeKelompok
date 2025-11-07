<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Laporan Donasi Barang {{ $year }}</title>
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

        <h3><strong>LAPORAN Donasi Barang</strong></h3>

        <div class="info">
            <p>Tahun: {{ $year }}</p>
            <p>Tanggal Cetak: {{ ucfirst(now()->locale('id')->isoFormat('DD MMMM YYYY')) }}</p>
        </div>

        <table>
          <thead>
            <tr>
              <th>Kode Produk</th>
              <th>Nama Produk</th>
              <th>Nama Penitip</th>
              <th>Tanggal Donasi</th>
              <th>Organisasi</th>
              <th>Penerima</th>
            </tr>
          </thead>
          <tbody>
            @forelse($donations as $row)
                <tr>
                <td>{{ $row->barang_titipan ? strtoupper(substr($row->barang_titipan->nama_barang, 0, 1)) . $row->barang_titipan->id_barang : '-' }}</td>
                <td class="text-start">{{ $row->barang_titipan->nama_barang ?? '-' }}</td>
                <td>{{ $row->barang_titipan->penitip->nama_penitip ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($row->tanggal_donasi)->format('d/m/Y') }}</td>
                <td>{{ $row->request_donasi->organisasi->nama_organisasi ?? '-' }}</td>
                <td>{{ $row->penerima }}</td>
                </tr>
            @empty
                <tr><td colspan="6">Tidak ada data donasi di tahun ini.</td></tr>
            @endforelse
        </tbody>

        </table>
    </div>
</body>
</html>
