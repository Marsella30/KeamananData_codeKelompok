@extends('pegawai_gudang.dashboard')

@section('isi')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<style>
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
        white-space: nowrap;
    }
    th {
        background-color: #f8f9fa;
    }
    .container-fluid {
        max-width: 1065px;
        margin: auto;
    }
    .btn-action {
        font-size: 14px;
        padding: 6px 12px;
    }
    .table-sm th,
    .table-sm td {
        padding: 0.45rem 0.5rem;
        font-size: 0.875rem;
        vertical-align: middle;
        white-space: nowrap;
    }
</style>

<div class="container-fluid mt-2">
    <h3 class="mb-4 text-center"><strong>Daftar Nota Transaksi</strong></h3>
    <div class="container d-flex justify-content-between align-items-center">
        <form class="d-flex mb-3" action="{{ route('pegawai_gudang.cetakNotaIndex') }}" method="GET">
            <input class="form-control me-2" 
                type="search" 
                name="search" 
                placeholder="Cari transaksi..." 
                value="{{ request('search') }}"
                aria-label="Search" 
                style="width: 250px;">

            <input class="form-control me-2" type="date" name="date" value="{{ request('date') }}">

            <button class="btn btn-outline-dark" type="submit">
                <i class="bi bi-search"></i>
            </button>
        </form>
    </div>

    <table class="table table-bordered table-striped table-sm align-middle">
        <thead class="table-dark text-center">
            <tr>
                <th>Nomor Transaksi</th>
                <th>Nama Pembeli</th>
                <th>Tanggal Transaksi</th>
                <th>Total Pembayaran</th>
                <th>Jenis Pengiriman</th>
                <th>Status Transaksi</th>
                <th>Kurir</th>
                <th>Nota</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaksi as $item)
                @php
                    $jadwalPengiriman = $item->penjadwalan->firstWhere('jenis_jadwal', 'Pengiriman') 
                                        ?? $item->penjadwalan->firstWhere('jenis_jadwal', 'Diambil');
                    $statusJadwal = $jadwalPengiriman && $jadwalPengiriman->status_jadwal ? strtolower($jadwalPengiriman->status_jadwal) : '';
                @endphp
                @if($statusJadwal !== 'diproses')
                    <tr>
                        <!-- @php
                            $tanggalTransaksi = \Carbon\Carbon::parse($item->tanggal_transaksi);
                            $tahun = $tanggalTransaksi->format('y');
                            $bulan = $tanggalTransaksi->format('m');
                            $nomorUrut = $item->id_transaksi;
                            $noNota = "{$tahun}.{$bulan}.{$nomorUrut}";
                        @endphp
                        <td class="text-center">{{ $noNota }}</td> -->
                        <td class="text-center">{{$item->nomor_transaksi }}</td>
                        <td>{{ $item->pembeli->nama_pembeli ?? '-' }}</td>
                        <td>
                            @if ($item->tanggal_transaksi)
                                {{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('d/m/Y H:i') }}
                            @else
                                <em style="color: #888;">-</em>
                            @endif
                        </td>
                        <td>Rp{{ number_format($item->total_pembayaran, 0, ',', '.') }}</td>
                        @php
                            $jadwalPengiriman = $item->penjadwalan->firstWhere('jenis_jadwal', 'Pengiriman') 
                                                ?? $item->penjadwalan->firstWhere('jenis_jadwal', 'Diambil');
                            $jenisJadwal = $jadwalPengiriman ? $jadwalPengiriman->jenis_jadwal : '-';
                        @endphp
                        <td>{{ $jenisJadwal }}</td>
                        <td class="text-center">
                            <span class="badge bg-info text-dark">{{ $item->status_transaksi }}</span>
                        </td>
                        <td>
                            @php
                                // Cari jadwal dengan jenis_jadwal = 'Pengiriman'
                                $jadwalPengiriman = $item->penjadwalan->firstWhere('jenis_jadwal', 'Pengiriman');
                                $namaKurir = $jadwalPengiriman && $jadwalPengiriman->pengiriman && $jadwalPengiriman->pengiriman->id_pegawai
                                    ? optional($jadwalPengiriman->pengiriman->pegawai)->nama_pegawai
                                    : '-';
                            @endphp
                            {{ $namaKurir }}
                        </td>
                        <td>
                            @if(strtolower($item->status_transaksi) !== 'menunggu pembayaran' && strtolower($item->status_transaksi) !== 'batal')
                                <a href="{{ route('pegawai_gudang.cetakNotaPdf', ['id' => $item->id_transaksi]) }}"
                                target="_blank"
                                class="btn btn-sm btn-dark">
                                    <i class="bi bi-printer"></i> Cetak PDF
                                </a>
                            @else
                                <em class="text-muted">Belum tersedia</em>
                            @endif
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $transaksi->links() }}
    </div>
</div>

@endsection
