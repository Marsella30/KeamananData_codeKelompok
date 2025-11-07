<!-- ReUseMart - Dashboard Penitip -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Penitip</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: rgba(111, 143, 70, 1);
        }
        body {
            background-color: #f5f8f2;
            font-family: 'Segoe UI', sans-serif;
        }
        header {
            background-color: var(--primary-color);
            padding: 15px 20px;
            color: white;
        }
        .logo img {
            height: 50px;
            border-radius: 50%;
        }
        .title {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .table thead {
            background-color: var(--primary-color);
            color: white;
        }
        .btn-outline-primary {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
        }
        .status-btn {
            border: none;
            border-radius: 20px;
            padding: 5px 12px;
            font-size: 0.875rem;
        }
        .status-tersedia {
            background-color: #d4edda;
            color: #155724;
        }
        .status-terjual {
            background-color: #f8d7da;
            color: #721c24;
        }
        .status-didonasikan {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-donasi {
            background-color: #dee2e6;
            color: #495057;
        }
        .status-diambil {
            background-color: #dee2e6;
            color:rgb(14, 180, 209);
        }
        footer {
            background-color: #f4f4f4;
            padding: 10px 50px;  
            border-top: 1px solid rgba(111, 143, 70, 1);
            font-size: 14px;
            margin-top: 40px;
        }
        .footer-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;  
        }
        .footer-left p {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
            color: #333;
        }
        .footer-middle {
            display: flex;
            justify-content: right;
            flex: 1;
        }
        .social-icons {
            display: flex;
            gap: 15px;
            margin-top: 10px;
        }
        .social-icon img {
            width: 21px;
            height: 21px;
            transition: transform 0.3s;
        }
        .social-icon:hover img {
            transform: scale(1.2);
        }     
    </style>
</head>
<body>

<header class="p-3 mb-3 border-bottom">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-between">
            <div class="logo">
                <a href="#"><img src="{{ asset('images/logo2.png') }}" alt="Logo"></a>
            </div>

            <div class="mx-auto">
                <h5 class="mb-0">Dashboard Penitip</h5>
            </div>

            <div class="dropdown text-end">
                <a href="{{ route(Auth::guard('penitip')->check() ? 'penitip.profil' : 'pembeli.profil') }}" class="d-block link-light text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://img.icons8.com/material/24/ffffff/user.png" alt="Account" width="32" height="32" class="rounded-circle">
                </a>
                <ul class="dropdown-menu text-small" aria-labelledby="dropdownUser1">
                    <li>
                        <a class="dropdown-item" href="{{ route(Auth::guard('penitip')->check() ? 'penitip.profil' : 'pembeli.profil') }}">
                            Profile
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="{{ route('penitip.tarikSaldo') }}">
                            Pengajuan Penarikan Saldo
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <a href="#" class="dropdown-item" onclick="
                            if(confirm('Yakin ingin logout?')) {
                                event.preventDefault();
                                document.getElementById('logout-form').submit();
                            } else {
                                event.preventDefault();
                            }
                        ">
                            Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>

<!-- <header class="d-flex justify-content-between align-items-center">
    <div class="logo">
        <a href="#"><img src="{{ asset('images/logo2.png') }}" alt="Logo"></a>
    </div>
    <div class="title">Dashboard Penitip</div>
    <div class="dropdown text-end">
        <a href="{{ route(Auth::guard('penitip')->check() ? 'penitip.profil' : 'pembeli.profil') }}" class="d-block link-dark text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="https://img.icons8.com/material/24/ffffff/user.png" alt="Account" width="32" height="32" class="rounded-circle">
        </a>
        <ul class="dropdown-menu text-small" aria-labelledby="dropdownUser1">
            <li>
                <a class="dropdown-item" href="{{ route(Auth::guard('penitip')->check() ? 'penitip.profil' : 'pembeli.profil') }}">
                    Profile
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
                <a href="#" class="dropdown-item" onclick="
                    if(confirm('Yakin ingin logout?')) {
                        event.preventDefault();
                        document.getElementById('logout-form').submit();
                    } else {
                        event.preventDefault();
                    }
                ">
                    Logout
                </a>
            </li>
        </ul>
    </div>
</header> -->

<div class="container mt-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <form action="{{ route('penitip.barang.index') }}" method="GET" class="d-flex" style="gap: 10px;">
            <input type="text" name="q" class="form-control" placeholder="Cari nama barang..." value="{{ request('q') }}">
            <button class="btn btn-outline-primary" type="submit">Cari</button>
        </form>
    </div>

    <h4 class="mb-3">Daftar Barang Titipan Anda</h4>
    <h6 class="mb-3">Jumlah total barang yang Anda titip: <span class="status-btn status-tersedia">{{ $totalBarang }}</span></h6>
    <h6 class="mb-3">Yang belum laku: <t><span class="status-btn status-terjual">{{ $totalBarangBelumLaku }}</span></h6>
    
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Foto</th>
                    <th>Nama Barang</th>
                    <th>Status</th>
                    <th>Tanggal Masuk</th>
                    <th>Tanggal Keluar</th>
                    <th>Sisa Waktu</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($barangs as $index => $barang)
                    <tr>
                        <td>{{ $barangs->firstItem() + $index }}</td>
                        <td>
                            <img src="{{ asset('images/barang/' . ($barang->fotoBarang->first()->nama_file ?? 'default.jpg')) }}" alt="Foto" class="img-thumbnail" style="width: 80px; cursor:pointer" data-bs-toggle="modal" data-bs-target="#modalDeskripsi{{ $barang->id_barang }}">
                        </td>
                        <td>{{ $barang->nama_barang }}</td>
                        <td>
                            @php
                                $status = strtolower($barang->status_barang);
                            @endphp
                            <span class="status-btn
                                {{ $status === 'tersedia' ? 'status-tersedia' : '' }}
                                {{ $status === 'terjual' ? 'status-terjual' : '' }}
                                {{ $status === 'didonasikan' ? 'status-didonasikan' : '' }}
                                {{ $status === 'barang untuk donasi' ? 'status-donasi' : '' }}
                                {{ $status === 'diambil kembali' ? 'status-terjual' : '' }}
                                {{ $status === 'pengambilan diproses' ? 'status-terjual' : '' }}">
                                {{ $status === 'barang untuk donasi' ? 'barang untuk donasi' : $barang->status_barang }}
                            </span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($barang->tanggal_masuk)->locale('id')->isoFormat('D MMMM YYYY') }}</td>
                        <td>
                        {{ $barang->tanggal_keluar 
                            ? \Carbon\Carbon::parse($barang->tanggal_keluar)->locale('id')->isoFormat('D MMMM YYYY') 
                            : '-' 
                        }}
                        </td>
                        <td>
                            @if($barang->status_barang === 'Tersedia')
                                @php
                                    $tanggalAkhir = \Carbon\Carbon::parse($barang->tanggal_akhir);
                                    $hariSekarang = now();

                                    $sisaHari = round($hariSekarang->diffInDays($tanggalAkhir, false));
                                    $hariLewat = round($hariSekarang->diffInDays($tanggalAkhir, false) * -1);
                                    $batasAmbil = 7; 
                                @endphp

                                @if($sisaHari > 0)
                                    <span class="badge bg-success">{{ $sisaHari }} hari tersisa</span>
                                @elseif($sisaHari === 0)
                                    <span class="badge bg-warning text-dark">Hari terakhir!</span>
                                @elseif($hariLewat <= $batasAmbil)
                                    <span class="badge bg-danger">Sisa {{ $batasAmbil - $hariLewat }} hari untuk Pengambilan</span>
                                @else
                                    <span class="badge bg-danger">Terlambat {{ $hariLewat }} hari</span>
                                @endif
                            @else
                                -
                            @endif
                        </td>

                        <td>
                            @if($barang->status_barang === 'Tersedia')
                                {{-- Tombol Perpanjang --}}
                                @if(!$barang->status_perpanjangan && $hariSekarang->isAfter($tanggalAkhir) || ($batasAmbil - $hariLewat == 7))
                                    <form action="{{ route('penitip.perpanjang', $barang->id_barang) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-primary">Perpanjang</button>
                                    </form>
                                @endif

                                {{-- Tombol Ambil Barang saat window 7 hari --}}
                                @if($hariLewat >= 0 && $hariLewat <= $batasAmbil)
                                    <form action="{{ route('penitip.ambil', $barang->id_barang) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Ambil barang ini sekarang?')">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-success">Ambil Barang</button>
                                    </form>
                                @endif
                            @elseif($barang->status_barang === 'Terjual')
                                <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $barang->id_barang }}">Detail</button>
                            @endif
                        </td>                               
                    </tr>

                    <!-- Modal Deskripsi -->
                    <div class="modal fade" id="modalDeskripsi{{ $barang->id_barang }}" tabindex="-1" aria-labelledby="deskripsiLabel{{ $barang->id_barang }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-light">
                                    <h5 class="modal-title" id="deskripsiLabel{{ $barang->id_barang }}">Deskripsi Barang</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Nama:</strong> {{ $barang->nama_barang }}</p>
                                    <p><strong>Harga Jual:</strong> Rp{{ number_format($barang->harga_jual, 0, ',', '.') }}</p>
                                    <p><strong>Deskripsi:</strong><br>{{ $barang->deskripsi }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Detail -->
                    <div class="modal fade" id="modalDetail{{ $barang->id_barang }}" tabindex="-1" aria-labelledby="modalLabel{{ $barang->id_barang }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-light">
                                    <h5 class="modal-title" id="modalLabel{{ $barang->id_barang }}">Detail Penjualan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Nama:</strong> {{ $barang->nama_barang }}</p>
                                    <p><strong>Harga Jual:</strong> Rp{{ number_format($barang->harga_jual, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada barang titipan ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {!! $barangs->withQueryString()->links('pagination::bootstrap-5') !!}
    </div>
</div>

<footer>
    <div class="footer-container">
        <div class="footer-left">
            <p>© 2025 ReUseMart. All rights reserved.</p>
        </div>
        <div class="footer-middle">
            <div class="social-icons">
                <a href="#" class="social-icon"><img src="https://img.icons8.com/material/24/000000/facebook.png" alt="Facebook"></a>
                <a href="#" class="social-icon"><img src="https://img.icons8.com/material/24/000000/twitter.png" alt="Twitter"></a>
                <a href="#" class="social-icon"><img src="https://img.icons8.com/material/24/000000/instagram.png" alt="Instagram"></a>
                <a href="#" class="social-icon"><img src="https://img.icons8.com/material/24/000000/pinterest.png" alt="Pinterest"></a>
                <a href="#" class="social-icon"><img src="https://img.icons8.com/material/24/000000/youtube.png" alt="YouTube"></a>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
