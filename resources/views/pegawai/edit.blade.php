@extends('Admin.dashboard')
@section('isi')
<div class="d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card shadow" style="width: 800px; height: 750px; margin-top: 20px; margin-bottom: 20px;">
        <div class="card-body">
            <h3 class="mb-4 text-center"><strong>Edit Pegawai</strong></h3>
            <form action="{{ route('pegawai.update', $pegawai->id_pegawai) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label>Nama</label>
                    <input type="text" name="nama_pegawai" class="form-control" value="{{ $pegawai->nama_pegawai }}" required>
                </div>
                <div class="mb-3">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" value="{{ $pegawai->username }}" required>
                </div>
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $pegawai->email }}" required>
                </div>
                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" value="{{ $pegawai->password }}" required>
                </div>
                <div class="mb-3">
                    <label>No Telp</label>
                    <input type="text" name="notelp" class="form-control" value="{{ $pegawai->notelp }}" required>
                </div>
                <div class="mb-3">
                    <label>Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control" value="{{ $pegawai->tanggal_lahir }}" required>
                </div>
                <div class="mb-3">
                    <label>Jabatan</label>
                    <select name="id_jabatan" class="form-control" required>
                        @foreach($jabatan as $j)
                            <option value="{{ $j->id_jabatan }}" {{ $pegawai->id_jabatan == $j->id_jabatan ? 'selected' : '' }}>{{ $j->nama_jabatan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('pegawai.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-success">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection