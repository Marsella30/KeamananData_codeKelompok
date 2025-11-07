<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Nota Penitipan {{ $nota->no_nota }}</title>
  <style>
    body { font-family: Arial, sans-serif; font-size: 34px; }
    .header { margin-bottom: 20px; }
    .header h2 { font-size: 36px; margin-top: 5px; margin-bottom: 5px; }
    .header p { margin: 2px 0; }
    .details, .penitip { margin-bottom: 16px; }
    .details-box {
        display: inline-block;      
        border: 1px solid #000;     
        padding: 6px 8px;           
        margin-bottom: 12px;        
    }
    .details table {
        border-collapse: collapse;
    }
    .details th,
    .details td {
        border: none;               
        font-weight: normal; 
        text-align: left; 
        margin: 0;
        padding: 0;      
    }
    .details tr {
        margin-bottom: 4px;
    }
    .items {
        margin-bottom: 0;
        padding-bottom: 0;
        margin: 0;
        padding: 0;
    }
    .item-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
    }
    .item-table td {
        margin: 0;
        padding: 0;
        vertical-align: top;
        font-size: 34px;
    }
    .item-name {
        text-align: left;
        width: 70%;
    }
    .item-price {
        text-align: right;
        width: 30%;
    }
    .item-extra {
        text-align: left;
        font-size: 34px;
        margin: 0;
        padding: 0;
    }
    .footer {
        margin-top: 10px;
        padding-top: 0;
        margin-left: 20px;
    }
    .footer p {
        margin: 0;
        padding: 0;
    }
  </style>
</head>
<body>
    <div class="details-box">
        <div class="header">
            <h2><strong>ReUse Mart</strong></h2>
            <p>Jl. Green Eco Park No. 456 Yogyakarta</p>
        </div>

    
        <table class="details">
            <tr>
                <th>No Nota</th>
                <td>: {{ $nota->no_nota }}</td>
            </tr>
            <tr>
                <th>Tanggal penitipan</th>
                <td>: {{ \Carbon\Carbon::parse($nota->tanggal_penitipan)->format('d/m/Y H:i:s') }}</td>
            </tr>
            <tr>
                <th>Masa penitipan sampai</th>
                <td>: {{ \Carbon\Carbon::parse($nota->masa_berakhir)->format('d/m/Y') }}</td>
            </tr>
        </table>
    

        <div class="penitip">
            <strong>Penitip :</strong> T{{ $penitip->id_penitip }} / {{ $penitip->nama_penitip }}<br>
            {{ $penitip->alamat }}<br>
            <!-- Delivery: Kurir ReUseMart ({{ $nota->pegawaiQc->nama_pegawai }}) -->
        </div>

        <div class="items">
            @foreach($barangNota as $barang)
                <table class="item-table">
                <tr>
                    <td class="item-name">{{ $barang->nama_barang }}</td>
                    <td class="item-price">{{ number_format($barang->harga_jual, 0, ',', '.') }}</td>
                </tr>
                @if($barang->garansi && $barang->tanggal_garansi)
                <tr>
                    <td class="item-extra" colspan="2">
                    Garansi ON {{ \Carbon\Carbon::parse($barang->tanggal_garansi)->format('M Y') }}
                    </td>
                </tr>
                @endif
                <tr>
                    <td class="item-extra" colspan="2">
                    Berat barang: {{ $barang->berat }} kg
                    </td>
                </tr>
                </table>
            @endforeach
        </div>

        <div class="footer">
            <p>Diterima dan QC oleh:</p>
            <br><br><br>
            <p>P{{ $nota->pegawaiQc->id_pegawai }} - {{ $nota->pegawaiQc->nama_pegawai }}</p>
        </div>
    </div>
</body>
</html>
