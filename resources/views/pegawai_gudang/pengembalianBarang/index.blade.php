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

    .table-sm {
        table-layout: auto;
        width: 100%;
    }
</style>

<div class="container-fluid mt-2">
    <h3 class="mb-4 text-center"><strong>Daftar Barang Pengembalian</strong></h3>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="container d-flex justify-content-between align-items-center">
        <form class="d-flex mb-3" action="{{ route('pegawai_gudang.barang.pengembalian') }}" method="GET">
            <input class="form-control me-2" 
                type="search" 
                name="search" 
                placeholder="Cari pengembalian..." 
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
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Penitip</th>
                <th>Tanggal Masuk</th>
                <th>Tanggal Akhir</th>
                <th>Harga Jual</th>
                <th>Status Barang</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($barang as $item)
                <tr>
                    <td class="text-center">{{ strtoupper(substr($item->nama_barang, 0, 1)) . $item->id_barang }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->penitip->nama_penitip ?? '-' }}</td>
                    <td>
                        {{ \Carbon\Carbon::parse($item->tanggal_masuk)->format('d/m/Y') }}
                    </td>
                    <td>
                        {{ $item->tanggal_akhir ? \Carbon\Carbon::parse($item->tanggal_akhir)->format('d/m/Y') : '-' }}
                    </td>
                    <td>Rp{{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                    <td class="text-center">
                        <span class="badge bg-warning text-dark">{{ $item->status_barang }}</span>
                    </td>
                    <td>
                        <form action="{{ route('pegawai_gudang.barang.konfirmasiPengembalian', ['id_barang' => $item->id_barang]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin mengkonfirmasi pengembalian barang ini?');">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-sm btn-dark">
                                Konfirmasi Pengembalian
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center; color: #888;">Tidak ada barang dalam proses pengembalian saat ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $barang->links() }}
    </div>
</div>

@endsection
