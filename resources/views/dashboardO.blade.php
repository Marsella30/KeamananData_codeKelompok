<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Organisasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: rgba(111, 143, 70, 1);
        }

        body {
            background-color: #f5f8f2;
            font-family: 'Segoe UI', sans-serif;
        }

        h2 {
            color: var(--primary-color);
            font-weight: 700;
        }

        .btn-success {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-success:hover {
            background-color: #5c7e3e;
            border-color: #5c7e3e;
        }

        .table thead {
            background-color: var(--primary-color);
            color: white;
        }

        .badge.bg-success {
            background-color: var(--primary-color) !important;
        }

        .modal-header {
            background-color: #e7f0da;
        }

        .modal-title {
            color: var(--primary-color);
            font-weight: 600;
        }

        .btn-outline-primary {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .user-info {
            font-size: 14px;
            color: var(--primary-color);
            font-weight: 600;
        }

    </style>
</head>
<body>

<div class="container py-5">
    <h2 class="mb-4">Dashboard Organisasi</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="user-info">
        <strong>Masuk sebagai:</strong> {{ Auth::user()->nama_organisasi}}
    </div>
    <div class="d-flex justify-content-end mb-3">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="btn btn-outline-danger" type="submit">Logout</button>
        </form>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <form action="{{ route('organisasi.request.index') }}" method="GET" class="d-flex" style="gap: 10px;">
            <input type="text" name="q" class="form-control" placeholder="Cari Barang" value="{{ $search ?? '' }}">
            <button class="btn btn-outline-primary" type="submit">Cari</button>
        </form>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal">+ Tambah Request Donasi</button>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Barang Dibutuhkan</th>
                    <th>Status Request</th>
                    <th>Organisasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requestDonasis as $index => $requestDonasi)
                    <tr>
                        <td>{{ $requestDonasis->firstItem() + $index }}</td>
                        <td>{{ $requestDonasi->barang_dibutuhkan }}</td>
                        <td>
                            <span class="badge 
                                {{ 
                                    $requestDonasi->status_request == 'Diterima' ? 'bg-success' : 
                                    ($requestDonasi->status_request == 'Menunggu' ? 'bg-warning' : 
                                    'bg-danger') 
                                }}">
                                {{ $requestDonasi->status_request }}
                            </span>

                        </td>
                        <td>{{ $requestDonasi->organisasi->nama_organisasi }}</td>
                        <td>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $requestDonasi->id_request }}">Edit</button>

                            <!-- Modal Edit -->
                            <div class="modal fade" id="editModal{{ $requestDonasi->id_request }}" tabindex="-1" aria-labelledby="editModalLabel{{ $requestDonasi->id_request }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('organisasi.request.update', $requestDonasi->id_request) }}" method="POST" enctype="multipart/form-data" onsubmit="return confirm('Yakin ingin mengubah request?')">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editModalLabel{{ $requestDonasi->id_request }}">Edit Request Donasi</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Barang Dibutuhkan</label>
                                                    <input type="text" name="barang_dibutuhkan" class="form-control" value="{{ $requestDonasi->barang_dibutuhkan }}" required>
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

                            <form action="{{ route('organisasi.request.destroy', $requestDonasi->id_request) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menghapus request?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $requestDonasis->withQueryString()->links() }}
    </div>

    <!-- Modal Tambah Request Donasi -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('organisasi.request.store') }}" method="POST" enctype="multipart/form-data" class="modal-content" onsubmit="return confirm('Yakin ingin menambah request?')">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">Tambah Request Donasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Barang Dibutuhkan</label>
                        <input type="text" name="barang_dibutuhkan" class="form-control" required>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
