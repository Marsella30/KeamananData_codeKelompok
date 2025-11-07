<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pembeli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6fc;
            font-family: 'Segoe UI', sans-serif;
        }

        .profile-header {
            background-color: rgba(111, 143, 70, 1);
            color: white;
            padding: 2rem;
            border-radius: 12px 12px 0 0;
        }

        .profile-header img {
            border-radius: 50%;
            width: 80px;
            height: 80px;
            object-fit: cover;
            border: 3px solid white;
        }

        .profile-card {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        .profile-info {
            padding: 2rem;
        }

        .profile-info h5 {
            color: rgba(111, 143, 70, 1);
            font-weight: 700;
        }

        .form-control[disabled] {
            background-color: #f0f3fa;
            font-weight: 500;
        }

        .profile-actions {
            padding: 0 2rem 2rem;
        }

        .btn-logout {
            background-color: #ff5a5f;
            color: white;
        }

        .btn-logout:hover {
            background-color: #e04848;
        }

        .badge-status {
            font-size: 0.85rem;
        }

        .back-btn {
            color: white;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .back-btn:hover {
            text-decoration: underline;
        }

        .btn-custom {
            background-color: rgba(111, 143, 70, 1);
            color: white;
            border: none;
        }

        .btn-custom:hover {
            background-color: rgba(101, 130, 65, 1); 
        }

    </style>
</head>
<body>

<div class="container my-5">
    <div class="profile-card">
        <div class="profile-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <img src="https://img.icons8.com/ios-glyphs/90/ffffff/user--v1.png" alt="User">
                <div>
                    <h4 class="mb-0">{{ $pembeli->nama_pembeli }}</h4>
                    <small>{{ $pembeli->email }}</small>
                </div>
            </div>
            <a href="{{ route('pembeli.profil') }}" class="back-btn">← Kembali ke Profil</a>
        </div>

        <div class="profile-info">
            <h5 class="mb-3">Alamat Pembeli</h5>
            @foreach ($alamat as $almt)
            <div class="card mx-3 my-3 shadow-sm">
                <div class="card-header" style="background-color: #e7f0da; color: rgba(111, 143, 70, 1);"></div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <div class="d-flex">
                                <!-- Left: Data Alamat -->
                                <div class="col-md-6">
                                    <div><strong>Provinsi: </strong>{{ $almt->provinsi }}</div>
                                    <div><strong>Kota: </strong>{{ $almt->kota }}</div>
                                    <div><strong>Kecamatan: </strong>{{ $almt->kecamatan }}</div>
                                    <div><strong>Kelurahan: </strong>{{ $almt->kelurahan }}</div>
                                    <div><strong>Jalan: </strong>{{ $almt->jalan }}</div>
                                    <div><strong>Kode Pos: </strong>{{ $almt->kode_pos }}</div>
                                    <div><strong>Detail: </strong>{{ $almt->detail }}</div>
                                </div>

                                <!-- Right: Form untuk Update -->
                                <div class="col-md-6">
                                    <form action="{{ route('alamat.update', $almt->id_alamat_pembeli) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="mb-3">
                                            <input type="text" name="provinsi" class="form-control" value="{{ $almt->provinsi }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <input type="text" name="kota" class="form-control" value="{{ $almt->kota }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <input type="text" name="kecamatan" class="form-control" value="{{ $almt->kecamatan }}" required>
                                        </div>
                                        <!-- Two columns for kelurahan, jalan, and kode pos -->
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <input type="text" name="kelurahan" class="form-control" value="{{ $almt->kelurahan }}" required>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <input type="text" name="jalan" class="form-control" value="{{ $almt->jalan }}" required>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <input type="text" name="kode_pos" class="form-control" value="{{ $almt->kode_pos }}" required>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <textarea name="detail" class="form-control" required>{{ $almt->detail }}</textarea>
                                        </div>
                                        <button type="submit" class="btn btn-warning btn-sm mt-2">Update</button>
                                    </form>
                                </div>
                            </div>

                            <!-- Hapus Button -->
                            <form action="{{ route('alamat.destroy', $almt->id_alamat_pembeli) }}" method="POST" class="d-inline mt-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm mt-2">Hapus</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
            @endforeach
        </div>


        <div class="modal fade" id="tambahAlamatModal" tabindex="-1" aria-labelledby="tambahAlamatModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form action="{{ route('alamat.store') }}" method="POST" class="modal-content">
                    @csrf
                    <div class="modal-header bg-light">
                        <h5 class="modal-title" id="tambahAlamatModalLabel">Tambah Alamat Pembeli</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label>Provinsi</label>
                                <input type="text" name="provinsi" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label>Kota</label>
                                <input type="text" name="kota" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label>Kecamatan</label>
                                <input type="text" name="kecamatan" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label>Kelurahan</label>
                                <input type="text" name="kelurahan" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label>Jalan</label>
                                <textarea class="form-control" name="jalan" rows="4" placeholder="Masukkan nama jalan..." required></textarea>
                            </div>
                            <div class="col-md-6">
                                <label>Kode Pos</label>
                                <input type="text" name="kode_pos" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label>Detail</label>
                                <textarea class="form-control" name="detail" rows="4" placeholder="Masukkan detail..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-custom">Simpan Alamat</button>
                    </div>
                </form>
            </div>
        </div>


        <div class="profile-actions d-flex justify-content-between align-items-center mt-4">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-custom" data-bs-toggle="modal" data-bs-target="#tambahAlamatModal">
                    Tambah Alamat
                </button>
                <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Yakin ingin logout?')">
                    @csrf
                    <button type="submit" class="btn btn-logout">Logout</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
