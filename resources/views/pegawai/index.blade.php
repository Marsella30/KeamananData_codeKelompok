@extends('Admin.dashboard')
@section('isi')
<div class="container py-4">
    <h3 class="mb-4 text-center"><strong>Daftar Pegawai</strong></h3>
    <div class="container d-flex justify-content-between align-items-center mb-3">
        {{-- Search di kiri --}}
        <form class="d-flex mb-3" action="{{ route('pegawai.search') }}" method="GET">
            <input class="form-control me-2" 
                type="search" 
                name="search" 
                placeholder="Cari pegawai..." 
                value="{{ request('search') }}"
                aria-label="Search" 
                style="width: 250px;">
            <button class="btn btn-outline-dark" type="submit">
                <i class="bi bi-search"></i>
            </button>
        </form>

        <a href="{{ route('pegawai.create') }}" class="btn btn-success">Tambah Pegawai</a>
    </div>
    @if($pegawai->isEmpty())
        <div class="mx-auto text-center mt-4" style="background-color: #f0f0f0; padding: 15px 25px; border-radius: 8px; max-width: 400px;">
            <strong>Pegawai tidak ditemukan.</strong>
        </div>
    @else
        <table class="table table-bordered table-striped text-center">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>No Telp</th>
                    <th>Jabatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pegawai as $p)
                    <tr>
                        <td>{{ $p->id_pegawai }}</td>
                        <td>{{ $p->nama_pegawai }}</td>
                        <td>{{ $p->email }}</td>
                        <td>{{ $p->notelp }}</td>
                        <td>{{ $p->jabatan->nama_jabatan ?? '-' }}</td>
                        <td>
                            <a href="{{ route('pegawai.edit', $p->id_pegawai) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('pegawai.destroy', $p->id_pegawai) }}" method="POST" style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus pegawai?')">Hapus</button>
                            </form>
                            @if($p->status_aktif == 1)
                            <form action="{{ route('pegawai.nonaktifkan', $p->id_pegawai) }}" method="POST" style="display:inline-block; margin-top:5px;">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-sm btn-secondary" onclick="return confirm('Nonaktifkan pegawai ini?')">Nonaktifkan</button>
                            </form>
                        @else
                            <form action="{{ route('pegawai.aktifkan', $p->id_pegawai) }}" method="POST" style="display:inline-block; margin-top:5px;">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-sm btn-success" onclick="return confirm('Aktifkan kembali pegawai ini?')">Aktifkan</button>
                            </form>
                        @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
