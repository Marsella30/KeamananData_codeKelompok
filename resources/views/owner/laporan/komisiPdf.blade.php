<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Komisi {{ $monthName }} {{ $year }}</title>
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

        <h3><strong>LAPORAN KOMISI BULANAN</strong></h3>

        <div class="info">
            <p>Bulan: {{ $monthName }}</p>
            <p>Tahun: {{ $year }}</p>
            <p>Tanggal Cetak: {{ ucfirst(now()->locale('id')->isoFormat('DD MMMM YYYY')) }}</p>
        </div>

    <table>
      <thead>
        <tr>
          <th>Kode</th>
          <th>Nama Produk</th>
          <th>Harga Jual</th>
          <th>Tanggal Masuk</th>
          <th>Tanggal Laku</th>
          <th>Komisi Hunter</th>
          <th>Komisi ReUseMart</th>
          <th>Bonus Penitip</th>
        </tr>
      </thead>
      <tbody>
        @forelse($data as $row)
          <tr>
            <td class="text-center">{{ strtoupper(substr($row['nama'], 0, 1)) . $row['kode'] }}</td>
            <td class="text-start">{{ $row['nama'] }}</td>
            <td>{{ number_format($row['harga'],0,',','.') }}</td>
            <td>{{ $row['tanggal_masuk'] }}</td>
            <td>{{ $row['tanggal_laku'] }}</td>
            <td>{{ number_format($row['komisi_hunter'],0,',','.') }}</td>
            <td>{{ number_format($row['komisi_reuse'],0,',','.') }}</td>
            <td>{{ number_format($row['komisi_penitip'],0,',','.') }}</td>
          </tr>
        @empty
          <tr><td colspan="8">Tidak ada penjualan di bulan ini.</td></tr>
        @endforelse
      </tbody>
      <tfoot>
            <tr class="fw-bold">
                {{-- Dua kolom pertama jadi label “Total” --}}
                <td colspan="2">Total</td>

                {{-- Total Harga Jual --}}
                <td>{{ number_format(collect($data)->sum('harga'), 0, ',', '.') }}</td>

                <td colspan="2" style="border-top: 1px solid #000; border-left: none; border-right: none;"></td>

                {{-- Total Komisi Hunter --}}
                <td>{{ number_format(collect($data)->sum('komisi_hunter'), 0, ',', '.') }}</td>

                {{-- Total Komisi ReUseMart --}}
                <td>{{ number_format(collect($data)->sum('komisi_reuse'), 0, ',', '.') }}</td>

                {{-- Total Bonus Penitip --}}
                <td>{{ number_format(collect($data)->sum('komisi_penitip'), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
  </div>
</body>
</html>
