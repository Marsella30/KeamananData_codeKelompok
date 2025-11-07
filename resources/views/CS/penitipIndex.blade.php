@extends('CS.dashboard')

@section('isi')
<div class="container py-5">
    <h2 class="mb-4">Dashboard Customer Service</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <form action="{{ route('cs.penitip.index') }}" method="GET" class="d-flex" style="gap: 10px;">
            <input type="text" name="q" class="form-control" placeholder="Cari Data Penitip" value="{{ $search }}">
            <button class="btn btn-outline-primary" type="submit">Cari</button>
        </form>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal">+ Tambah Penitip</button>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>No KTP</th>
                    <th>Email</th>
                    <th>Username</th>
                    <th>Status</th>
                    <th>Foto KTP</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($penitips as $index => $penitip)
                    <tr>
                        <td>{{ $penitips->firstItem() + $index }}</td>
                        <td>{{ $penitip->nama_penitip }}</td>
                        <td>{{ $penitip->no_ktp }}</td>
                        <td>{{ $penitip->email }}</td>
                        <td>{{ $penitip->username }}</td>
                        <td>
                            <span class="badge {{ $penitip->status_aktif ? 'bg-success' : 'bg-secondary' }}">
                                {{ $penitip->status_aktif ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </td>
                        <td>
                            @if($penitip->foto_ktp)
                                <a href="{{ asset('storage/' . $penitip->foto_ktp) }}" target="_blank">Lihat</a>
                            @else
                                <span class="text-muted">Tidak ada</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $penitip->id_penitip }}">Edit</button>

                            <div class="modal fade" id="editModal{{ $penitip->id_penitip }}" tabindex="-1" aria-labelledby="editModalLabel{{ $penitip->id_penitip }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('cs.penitip.update', $penitip->id_penitip) }}" method="POST" enctype="multipart/form-data" onsubmit="return validateFormUpdate();">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editModalLabel{{ $penitip->id_penitip }}">Edit Penitip</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Penitip</label>
                                                    <input type="text" name="nama_penitip" class="form-control" value="{{ $penitip->nama_penitip }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">No KTP</label>
                                                    <input type="text" name="no_ktp" class="form-control" value="{{ $penitip->no_ktp }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Email</label>
                                                    <input type="email" name="email" class="form-control" value="{{ $penitip->email }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Username</label>
                                                    <input type="text" name="username" class="form-control" value="{{ $penitip->username }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Foto KTP</label>
                                                    <input type="file" name="foto_ktp" class="form-control">
                                                    @if($penitip->foto_ktp)
                                                        <small>File lama: <a href="{{ asset('storage/' . $penitip->foto_ktp) }}" target="_blank">Lihat</a></small>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <form action="{{ route('cs.penitip.destroy', $penitip->id_penitip) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Data penitip tidak ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {!! $penitips->withQueryString()->links('pagination::bootstrap-5') !!}
    </div>

    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('cs.penitip.store') }}" method="POST" enctype="multipart/form-data" class="modal-content" onsubmit="return validateFormAdd();">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">Tambah Penitip</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Penitip</label>
                        <input type="text" name="nama_penitip" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No KTP</label>
                        <input type="text" name="no_ktp" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Alamat</label>
                        <textarea name="alamat" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto KTP</label>
                        <input type="file" name="foto_ktp" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function validateFormUpdate() {
    return confirm('Yakin ingin mengubah data ini?');
}
function validateFormAdd() {
    return confirm('Yakin ingin menambah data ini?');
}
</script>
@endsection
