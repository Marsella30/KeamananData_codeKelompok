<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Nota Penjualan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
        }
        .no-border td {
            border: none;
        }
    </style>
</head>
<body>
    <div class="header">
        <h3>Nota Penjualan</h3>
        <p><strong>ReUse Mart</strong><br>
        Jl. Green Eco Park No. 456 Yogyakarta</p>
    </div>

    <table class="no-border">
        <tr>
            <td><strong>No Nota</strong></td>
            <td>: {{ $transaksi->nomor_transaksi }}</td>
        </tr>
        <tr>
            <td><strong>Tanggal Pesan</strong></td>
            <td>: {{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td><strong>Lunas Pada</strong></td>
            <td>: {{ \Carbon\Carbon::parse($transaksi->tanggal_lunas)->format('d/m/Y H:i') ?? '-' }}</td>
        </tr>
        @php
            $jadwalPengiriman = $transaksi->penjadwalan->firstWhere('jenis_jadwal', 'Pengiriman') 
                            ?? $transaksi->penjadwalan->firstWhere('jenis_jadwal', 'Diambil');
            $labelTanggalKirim = ($jadwalPengiriman && $jadwalPengiriman->jenis_jadwal === 'Diambil')
                ? 'Tanggal Diambil'
                : 'Tanggal Dikirim';
        @endphp
        <tr>
            <td><strong>{{ $labelTanggalKirim }}</strong></td>
            <td>: {{ \Carbon\Carbon::parse($transaksi->tanggal_kirim)->format('d/m/Y') ?? '-' }}</td>
        </tr>
    </table>

    <p><strong>Pembeli:</strong> {{ $transaksi->pembeli->email ?? '-' }} / {{ $transaksi->pembeli->nama_pembeli ?? '-' }}</p>
    @if ($transaksi->alamat)
        <p>{{ $transaksi->alamat->jalan }},
        {{ $transaksi->alamat->kelurahan }},
        {{ $transaksi->alamat->kecamatan }},
        {{ $transaksi->alamat->kota }},
        {{ $transaksi->alamat->provinsi }},
        {{ $transaksi->alamat->kode_pos }}</p>
    @else
        <p><em>Alamat tidak tersedia</em></p>
    @endif
    @php
        $jadwalPengiriman = $transaksi->penjadwalan->firstWhere('jenis_jadwal', 'Pengiriman') 
                            ?? $transaksi->penjadwalan->firstWhere('jenis_jadwal', 'Diambil');

        if ($jadwalPengiriman && $jadwalPengiriman->jenis_jadwal === 'Pengiriman') {
            $kurir = $jadwalPengiriman->pengiriman && $jadwalPengiriman->pengiriman->pegawai
                ? $jadwalPengiriman->pengiriman->pegawai->nama_pegawai
                : '-';
            $delivery = "Kurir ReUseMart ({$kurir})";
            $diterimaLabel = "Diterima oleh";
        } elseif ($jadwalPengiriman && $jadwalPengiriman->jenis_jadwal === 'Diambil') {
            $delivery = "(Diambil Sendiri)";
            $diterimaLabel = "Diambil Oleh";
        } else {
            $delivery = '-';
            $diterimaLabel = "Diterima oleh";
        }
    @endphp

    <p>Delivery: {{ $delivery }}</p>
    <table>
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Harga (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaksi->detailTransaksi as $detail)
                <tr>
                    <td>{{ $detail->barang->nama_barang }}</td>
                    <td>{{ number_format($detail->barang->harga_jual, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    @php
        $poinDidapat = floor($transaksi->total_pembayaran / 10000);

        // Ongkir default
        $ongkosKirim = 100000;

        // Cek apakah total_pembayaran lebih dari 1.500.000 atau jenis_jadwal "Diambil"
        if ($transaksi->total_pembayaran > 1500000 || ($jadwalPengiriman && $jadwalPengiriman->jenis_jadwal === 'Diambil')) {
            $ongkosKirim = 0;
        }

        $poinDigunakan = $transaksi->poin_digunakan ?? 0;

        // Hitung potongan poin dalam rupiah
        // misal poin 100 → 10.000 rupiah
        $potonganPoinRp = floor($poinDigunakan / 100) * 10000;

        // Ongkir default
        $ongkosKirim = 100000;

        // Cek apakah total_pembayaran lebih dari 1.500.000 atau jenis_jadwal "Diambil"
        if ($transaksi->total_pembayaran > 1500000 || ($jadwalPengiriman && $jadwalPengiriman->jenis_jadwal === 'Diambil')) {
            $ongkosKirim = 0;
        }

        // Total (harga asli + potongan poin)
        $total = $transaksi->total_pembayaran + $potonganPoinRp;

        // Total bayar (total + ongkir - potongan poin)
        $totalBayar = $total + $ongkosKirim - $potonganPoinRp;
    @endphp

    <table class="no-border">
        <tr>
            <td><strong>Ongkos Kirim</strong></td>
            <td>: Rp{{ number_format($ongkosKirim, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td><strong>Potongan Poin</strong></td>
            <td>: -Rp{{ number_format($potonganPoinRp, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td><strong>Total Bayar</strong></td>
            <td>: Rp{{ number_format($transaksi->total_pembayaran, 0, ',', '.') }}</td>
        </tr>
    </table>

    <!-- @php
        $poinDidapat = floor($transaksi->total_pembayaran / 10000);
    @endphp -->
    <p><strong>Poin dari Pesanan:</strong> {{ $transaksi->poin_didapat }}</p>
    @php
        $totalPoinCustomer = ($transaksi->pembeli->poin ?? 0) + $transaksi->poin_didapat;
    @endphp
    <p><strong>Total Poin Customer:</strong> {{ $totalPoinCustomer }}</p>

    <!-- <p>QC oleh: {{ $transaksi->qc->nama_pegawai ?? '-' }} ({{ $transaksi->qc->id_pegawai ?? '-' }})</p> -->

    <br><br>
    <p>{{ $diterimaLabel }}: ________________________</p>
    <p>Tanggal: ________________________</p>
</body>
</html>
