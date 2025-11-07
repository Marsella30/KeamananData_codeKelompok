<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Penitip</title>
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
                    <h4 class="mb-0">{{ $penitip->nama_penitip }}</h4>
                    <small>{{ $penitip->email }}</small>
                </div>
            </div>
            <a href="{{ route('dashboard.penitip') }}" class="back-btn">← Kembali ke Beranda</a>
        </div>

        <div class="profile-info">
            <h5 class="mb-4">Informasi Akun</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label>Nama</label>
                    <input type="text" class="form-control" value="{{ $penitip->nama_penitip }}" disabled>
                </div>
                <div class="col-md-6">
                    <label>Username</label>
                    <input type="text" class="form-control" value="{{ $penitip->username }}" disabled>
                </div>
                <div class="col-md-6">
                    <label>Alamat</label>
                    <input type="text" class="form-control" value="{{ $penitip->alamat }}" disabled>
                </div>
                <div class="col-md-6">
                    <label>Poin</label>
                    <input type="text" class="form-control" value="{{ $penitip->poin }}" disabled>
                </div>
                <div class="col-md-6">
                    <label>Status Akun</label>
                    <input type="text" class="form-control" value="{{ $penitip->status_aktif ? 'Aktif' : 'Tidak Aktif' }}" disabled>
                </div>
                <div class="col-md-6">
                    <label>Email Aktif</label>
                    <input type="text" class="form-control" value="{{ $penitip->email }}" disabled>
                </div>
                <div class="col-md-6">
                    <label>Saldo Akun</label>
                    <input type="text" class="form-control" value="{{ $penitip->saldo_penitip }}" disabled>
                </div>
                <div class="col-md-6">
                    <label>Rata-rata Rating</label>
                    <input type="text" class="form-control" value="{{ number_format($avgRating, 2) }}" disabled>
                </div>
            </div>
        </div>

        <!-- Modal Riwayat Pembelian -->
        <div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title title" id="historyModalLabel">Riwayat Penjualan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body" style="background-color: #f5f8f2;">
                @forelse ($transaksiList as $transaksi)
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header" style="background-color: #e7f0da; color: rgba(111, 143, 70, 1);">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>Tanggal Keluar:</strong> {{ $transaksi->tanggal_keluar }}<br>
                                    <strong>Status:</strong> {{ $transaksi->status_barang }}
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>{{ $transaksi->nama_barang }}</strong>
                                        </div>
                                        <div class="fw-semibold text-success">
                                            Harga Jual: Rp{{ number_format($transaksi->harga_jual, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-warning">
                        Belum ada transaksi yang tercatat.
                    </div>
                @endforelse
            </div>
            </div>
        </div>
        </div>

        <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('penitip.update', $penitip->id_penitip) }}" method="POST" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="editProfileModalLabel">Edit Profil Pembeli</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                <div class="col-md-6">
                    <label>Nama</label>
                    <input type="text" name="nama_penitip" class="form-control" value="{{ $penitip->nama_penitip }}" required>
                </div>
                <div class="col-md-6">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" value="{{ $penitip->username }}" required>
                </div>
                <div class="col-md-6">
                    <label>Alamat</label>
                    <input type="text" name="alamat" class="form-control" value="{{ $penitip->alamat }}" required>
                </div>
                <div class="col-md-6">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $penitip->email }}" required>
                </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-custom">Simpan Perubahan</button>
            </div>
            </form>
        </div>
        </div>

        <div class="profile-actions d-flex justify-content-between align-items-center mt-4">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#historyModal">
                    Lihat Riwayat Penjualan
                </button>
                <button type="button" class="btn btn-custom" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                    Edit Profil
                </button>
            </div>
            <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Yakin ingin logout?')">
                @csrf
                <button type="submit" class="btn btn-logout">Logout</button>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>