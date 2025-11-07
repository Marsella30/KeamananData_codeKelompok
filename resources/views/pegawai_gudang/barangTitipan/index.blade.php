@extends('pegawai_gudang.dashboard')

@section('isi')

<style>
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

<div class="container mt-2">
    <h2 class="mb-4 text-center"><strong>Daftar Barang Titipan</strong></h2>

    {{-- Flash message --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="container d-flex justify-content-between align-items-center">
        <form class="d-flex mb-3" action="{{ route('pegawai_gudang.barangTitipan.index') }}" method="GET">
            <input class="form-control me-2" 
                type="search" 
                name="search" 
                placeholder="Cari barang titipan..." 
                value="{{ request('search') }}"
                aria-label="Search" 
                style="width: 250px;">
            <input class="form-control me-2" type="date" name="date" value="{{ request('date') }}">
            <button class="btn btn-outline-dark" type="submit">
                <i class="bi bi-search"></i>
            </button>
        </form>

        <!-- <a href="{{ route('pegawai_gudang.barangTitipan.createBlank') }}" class="btn btn-success mb-3">Tambah Barang</a> -->

    </div>

    {{-- Tabel --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-sm align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Status</th>
                    <th>Penitip</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($barang as $item)
                <tr>
                    <td class="text-center">{{ strtoupper(substr($item->nama_barang, 0, 1)) . $item->id_barang }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                    <td>{{ $item->status_barang }}</td>
                    <td>T{{ $item->penitip->id_penitip}} - {{ $item->penitip->nama_penitip ?? '-' }}</td>
                    <td class="text-center">
                        <a href="{{ route('pegawai_gudang.barangTitipan.showDetail', $item->id_barang) }}" class="btn btn-sm btn-info">
                            <i class="bi bi-eye"></i>
                        </a>
                        <!-- <form action="{{ route('pegawai_gudang.barangTitipan.destroy', $item->id_barang) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus Barang ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Hapus</button>
                        </form> -->
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="12" class="text-center text-muted">Belum ada barang titipan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="d-flex justify-content-center mt-3">
            {{ $barang->links() }}
        </div>
    </div>
</div>
@endsection
