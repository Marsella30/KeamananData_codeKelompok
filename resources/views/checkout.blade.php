<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReUseMart - Checkout</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            color: #333;
            background-color: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: saturate(150%) blur(30px);
            z-index: 3;
        }

        .container {
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: nowrap; /* agar tidak melipat */
        }

        .logo {
            margin-left: -40px;
            background-color: rgba(111, 143, 70, 1); /* semi-transparan */
            padding: 8px 12px;
            border-radius: 50%;
        }

        header {
            background-color: rgba(111, 143, 70, 1);
            padding: 10px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            height: 80px; /* atur tinggi navbar */
            box-shadow: 0 4px 6px -2px rgba(0, 0, 0, 0.1);
        }

        header .logo img {
            height: 60px;
            filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.2)); /* efek bayangan */
            transition: transform 0.3s ease;
            border-radius: 50%;
        }

        header .logo img:hover {
            transform: scale(1.1); 
            filter: drop-shadow(6px 6px 12px rgba(0, 0, 0, 0.3));
            cursor: pointer;
        }

        nav ul {
            display: flex;
            list-style-type: none;
            margin: 0;
            padding: 0;
        }

        nav ul li {
            margin: 0 20px;
        }

        nav ul li a {
            text-decoration: none;
            color: white
            font-size: 15px;
            font-weight: 600;
        }

        .cart-search {
            display: flex;
            align-items: center;
        }

        .cart-search select,
        .cart-search input[type="search"] {
            width: 200px;
            padding: 8px;
            border-radius: 20px;
            border: 1px solid #ccc;
            margin-right: 10px;
            outline: none;
        }

        .cart-search input[type="search"] {
            width: 200px;
        }

        .cart-search a img {
            width: 24px;
            height: 24px;
            margin-right: 10px;
        }

        .cart-search .icons {
            display: flex;
            align-items: center;
        }

        .cart-search .icons a {
            margin-left: 15px;
        }

        .navbar-shadow-separator {
            height: 1px;
            background-color: #ccc;
            box-shadow: 0 4px 6px -2px rgba(0, 0, 0, 0.15);
            margin-bottom: 5px;
        }

        #carouselExampleCaptions {
            max-height: 500px;
            overflow: hidden;
        }

        .carousel-item {
            height: 400px;
            position: relative;
        }

        .carousel-item img {
            height: 100%;
            width: 100%;
            object-fit: cover;
        }

        .carousel-item a:hover img {
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.2);
            cursor: pointer; /* Menambahkan pointer saat hover */
        }

        .carousel-caption {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 15px 30px;
            background: rgba(0, 0, 0, 0.5); /* latar belakang transparan gelap */
            color: white;
            text-align: left;
        }

        .carousel-divider {
            width: 80%;
            height: 1px; /* tebal garis */
            background-color: rgba(111, 143, 70, 1);; /* warna garis */
            margin: 20px auto; /* jarak dari carousel */
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1); /* bayangan untuk garis */
        }

        /* Judul Kategori */
        .category-title {
            text-align: center;
            margin-top: 20px;
        }

        .category-title h2 {
            font-size: 28px;
            font-weight: 600;
            color: #333;
        }

        /* Container untuk Kategori */
        .category-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(85px, max-content));
            gap: 10px;
            justify-items: center;
            margin-top: 20px;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }

        /* Setiap Kartu Kategori */
        .category-card {
            background-color: #f9f9f9;
            padding: 8px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            height: 100px;
            margin: 0;
            text-decoration: none;
        }

        .category-card img {
            width: 75%;
            height: 65px;
            object-fit: cover;
            border-radius: 10px;
        }

        .category-card h3 {
            margin-top: 5px;
            margin: 2px 0 0 0;
            font-size: 8px;
            color: #333;
            text-decoration: none;
        }

        .populer-produk{
            text-align: left;
            margin-top: 30px;
            margin-left: 125px;
        }

        .populer-produk h2 {
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }

        .product-container {
            display: flex;  /* Menggunakan Flexbox */
            justify-content: space-between;  /* Membuat produk bersebelahan dengan jarak yang sama */
            gap: 10px;  /* Memberikan jarak antar produk */
            margin-top: 5px;
            flex-wrap: wrap;  /* Memastikan produk akan membungkus ke baris berikutnya jika ruang tidak cukup */
            margin-left: 125px;
            margin-right: 125px;
        }

        /* Kartu Produk */
        .product-card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            width: 200px;
            text-align: center;
            margin: 10px auto;
            padding: 10px;
            display: block;
            text-decoration: none;
            color: inherit;
            border: 2px solid #f1f1f1;
            width: calc(20% - 10px);
            margin-bottom: 10px; 
            height: 400px;
        }

        .product-card:hover {
            transform: scale(1.05); /* Memperbesar produk sedikit saat hover */
        }

        /* Gambar Produk */
        .product-image {
            width: 100%;
            height: 190px;
            border-radius: 10px;
            margin-top: 10px;
            object-fit: cover;
        }

        .product-card img {
            height: 170px;
            width: 100%;
            object-fit: contain;
            margin-bottom: 10px;
        }

        /* Informasi Produk */
        .product-info {
            margin-top: 15px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        /* Nama Kategori Produk */
        .product-category {
            font-size: 10px;
            color: #777;
            text-align: left;
        }

        /* Nama Produk */
        .product-name {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin: 10px 0;
        }

        /* Rating Produk */
        .product-rating {
            font-size: 10px;
            color: #ff5a5f;
        }

        /* Nama Merek Produk */
        .product-status {
            font-size: 11px;
            color: #777;
            text-align: left;
        }

        .product-brand {
            font-size: 11px;
            color: #777;
        }

        /* Harga Produk */
        .product-price {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
            width: 100%;
        }

        /* Harga Produk (Di kiri) */
        .price-container {
            flex: 1;
            text-align: left;
        }

        .current-price {
            font-size: 13px;
            font-weight: bold;
            color: #333;
            flex-direction: column;
        }

        .add-to-cart-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Tombol Add to Cart */
        .add-to-cart {
            background-color: #F0FFF0;
            color: #28a745;
            padding: 5px 10px;
            border: 2px solid #F0FFF0;;
            border-radius: 6px;
            display: flex;
            align-items: center;
            font-size: 11px;
            cursor: pointer;
        }

        .add-to-cart img {
            margin-right: 5px; /* Jarak antara ikon cart dan teks Add */
            height: 18px;
        }

        .add-to-cart:hover {
            background-color: #ACE1AF;
        }

        footer {
            background-color: #f4f4f4;
            padding: 10px 50px;  
            border-top: 1px solid rgba(111, 143, 70, 1);;
            font-size: 14px;
        }

        .footer-container {
            display: flex;
            justify-content: space-between;  /* Menempatkan elemen di kiri dan kanan */
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

        /* Footer Middle - Social Icons */
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

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                flex-direction: row;
                justify-content: space-between;
            }

            nav ul {
                margin-top: 15px;
                flex-direction: row;
                align-items: center;
                gap: 15px;
            }

            nav ul li {
                margin: 5px 0;
            }

            .cart-search input[type="search"] {
                width: 150px;
                display: none;
            }

            .category-container {
                grid-template-columns: repeat(6, 1fr);
            }

            .category-card img {
                width: 65%;
                height: 45px;
            }

            .category-card {
                height: 90px;
            }

            .populer-produk{
                text-align: left;
                margin-top: 20px;
                margin-left: 65px;
            }

            .product-container {
                margin-left: 60px;
                margin-right: 60px;
                gap: 1px;
            }

            .product-card {
                width: calc(20% - 10px); /* Produk akan lebih besar pada layar kecil */
                height: 400px;
                margin-bottom: 10px; /* Menambahkan jarak antara produk */
                height: 280px;
                flex-direction: column;
            }

            /* Gambar Produk */
            .product-image {
                width: 100%;
                height: 80px;
                object-fit: cover;
            }

            /* Nama Kategori Produk */
            .product-category {
                font-size: 6px;
            }

            /* Nama Produk */
            .product-name {
                font-size: 9px;
            }

            /* Rating Produk */
            .product-rating {
                font-size: 9px;
            }

            /* Nama Merek Produk */
            .product-brand {
                font-size: 8px;
            }

            .current-price {
                font-size: 6px;
                flex-direction: column;
            }

            /* Tombol Add to Cart */
            .add-to-cart {
                padding: 2px 4px;
                font-size: 6px;
            }

            .add-to-cart img {
                margin-right: 5px; 
                width: 8px;
                height: 8px;
            }

            .add-to-cart:hover {
                background-color: #ACE1AF;
            }

            footer {
                padding: 5px 40px;  
            }

            .footer-left p {
                font-size: 10px;
            }

            .social-icon img {
                width: 14px;
                height: 14px;
            }
        }
    </style>

</head>
<body>
    <!-- Header Section -->
    <header>
        <div class="container">
            <div class="logo">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('images/logo2.png') }}" alt="Brand Logo">
                </a>
            </div>
            <nav>
                <ul>
                    <li><a href="{{ url('/kategori') }}" style="color: white;">Collection</a></li>
                    <li><a href="/about" style="color: white;">About Us</a></li>
                </ul>
            </nav>
            <!-- Cart, Search, and Location -->
            <div class="cart-search">
                <!-- Icons -->
                <div class="icons">
                    <a href="{{ route('keranjang') }}"><img src="https://img.icons8.com/material/24/ffffff/shopping-cart.png" alt="Cart"></a>
                    <a href="{{ route('diskusi.index') }}"><img src="https://img.icons8.com/?size=100&id=123773&format=png&color=ffffff" alt="Diskusi"></a>
                    <a href="{{ route(Auth::guard('penitip')->check() ? 'penitip.profil' : 'pembeli.profil') }}">
                        <img src="https://img.icons8.com/material/24/ffffff/user.png" alt="Account">
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- <div class="navbar-shadow-separator"></div> -->
    
    <!-- Main Section -->
    <main>
        @if (session('error'))
            <div class="alert alert-danger mt-2">
                {{ session('error') }}
            </div>
        @endif
        @php
            $alamatPertama = $alamatList->first();
        @endphp

        <div class="container d-flex justify-content-center my-4">
            <div class="row">
                <h1>Checkout</h1>
                {{-- KIRI: Detail Checkout --}}
                <div class="col-md-8">
                    {{-- Alamat Pengiriman --}}
                    <div class="card mb-3">
                        <div class="card-body">
                            <h4 class="fw-bold">ALAMAT PENGIRIMAN</h4>
                            <div id="alamatUtama">
                                <p>
                                    {{ $alamatPertama->jalan }}, {{ $alamatPertama->kelurahan }}, {{ $alamatPertama->kecamatan }},
                                    {{ $alamatPertama->kota }}, {{ $alamatPertama->provinsi }}<br>
                                    {{ $alamatPertama->kode_pos }}<br>
                                    {{ $alamatPertama->detail }}
                                </p>
                            </div>
                            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#gantiAlamatModal">
                                Ganti
                            </button>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-body">
                            <h4 class="fw-bold">JENIS PENGIRIMAN</h4>
                            <div id="jenisPengirimanTerpilih" class="mb-3">
                                <p>Kurir</p>
                            </div>
                            <input type="hidden" id="inputJenisPengiriman" value="Kurir">
                            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#gantiPengirimanModal">
                                Ganti
                            </button>
                        </div>
                    </div>

                    {{-- Produk --}}
                    <div class="card mb-3">
                        @foreach ($items as $item)
                        <div class="card-body">
                            <h5 class="fw-bold mb-3">{{ $item->nama_barang }}</h5>
                            <div class="d-flex">
                                <img src="{{ asset('images/barang/' . ($item->fotoBarang->first()->nama_file ?? 'default.jpg')) }}" width="100" alt="Foto Barang" class="img-fluid">
                                <div class="ms-5 mt-3">
                                    <p class="mb-3">{{ $item->deskripsi }}</p>
                                    <p class="fw-bold">Rp{{ number_format($item->harga_jual, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <hr>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- KANAN: Informasi Poin dan Pembayaran --}}
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="fw-bold">Poin Pembeli</h5>
                            <p>Total Poin: <strong id="totalPoin">{{$poin}}</strong></p>

                            <label for="poinInput" class="form-label">Masukkan Poin yang Ingin Ditukar</label>
                            <input type="number" class="form-control" id="poinInput" name="poin_tukar" placeholder="Contoh: 100" min="0" max="300">
                            
                            <div class="form-text mb-2">100 poin = Rp10.000 potongan</div>
                            <div id="poinError" class="text-danger mt-1" style="visibility: hidden; height: 18px;">
                                Jumlah poin melebihi total poin.
                            </div>
                        </div>
                    </div>

                    {{-- Total Pembayaran --}}
                    <div class="card">
                        <div class="card-body">
                            <p class="d-flex justify-content-between">
                                <span>Subtotal</span>
                                <span id="subtotalDisplay">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                                <input type="hidden" id="subtotalHidden" value="{{ $subtotal }}">
                            </p>
                            <p class="d-flex justify-content-between">
                                <span>Potongan Poin</span>
                                <span class="text-danger" id="potonganPoin">-Rp0</span>
                            </p>
                            <p class="d-flex justify-content-between">
                                <span>Ongkir</span>
                                <span id="ongkirDisplay">Rp0</span>
                            </p>
                            <hr>
                            <h5 class="d-flex justify-content-between">
                                <span>Total Tagihan</span>
                                <span class="text-success fw-bold" id="totalBayar">Rp248.734</span>
                            </h5>
                            <form method="POST" action="{{ route('checkout.submit') }}">
                                @csrf
                                <input type="hidden" name="total_pembayaran" id="totalPembayaranInput" value="0">
                                <input type="hidden" name="jenis_pengiriman" id="inputJenisPengiriman" value="Kurir">
                                <input type="hidden" name="poin_tukar" id="inputPoinTukar" value="0">
                                <input type="hidden" name="id_alamat" id="inputIdAlamat" value="{{ $alamatPertama->id_alamat_pembeli }}">
                                <button type="submit" class="btn btn-success w-100 mt-3">Bayar Sekarang</button>
                            </form>
                            <small class="d-block text-center text-muted mt-2">Dengan melanjutkan, anda menyetujui S&K</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer Section -->
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

    <!-- Modal -->
    <div class="modal fade" id="gantiAlamatModal" tabindex="-1" aria-labelledby="gantiAlamatModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pilih Alamat Pengiriman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        @foreach ($alamatList as $alamat)
                            <div class="col-12 mb-3">
                                <div class="card border-start border-4 border-success shadow-sm">
                                    <div class="card-body d-flex justify-content-between align-items-start">
                                        <div>
                                            <p class="mb-3 pe-4">
                                                {{ $alamat->jalan }},
                                                {{ $alamat->kelurahan }},
                                                {{ $alamat->kecamatan }},
                                                {{ $alamat->kota }},
                                                {{ $alamat->provinsi }} {{ $alamat->kode_pos }}
                                            </p>
                                            <p class="mb-1 fw-bold">Detail:</p>
                                            <p class="mb-0">
                                                {{ $alamat->detail }}
                                            </p>
                                        </div>

                                        {{-- Tombol Pilih --}}
                                        <button type="button" class="btn btn-success"
                                            onclick="gunakanAlamatDariCard(this)"
                                            data-id="{{ $alamat->id_alamat }}"
                                            data-jalan="{{ $alamat->jalan }}"
                                            data-kelurahan="{{ $alamat->kelurahan }}"
                                            data-kecamatan="{{ $alamat->kecamatan }}"
                                            data-kota="{{ $alamat->kota }}"
                                            data-provinsi="{{ $alamat->provinsi }}"
                                            data-kodepos="{{ $alamat->kode_pos }}"
                                            data-detail="{{ $alamat->detail }}">
                                            Pilih
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="gantiPengirimanModal" tabindex="-1" aria-labelledby="gantiPengirimanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pilih Jenis Pengiriman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        {{-- Opsi 1: Kurir --}}
                        <div class="col-12 mb-3">
                            <div class="card border-start border-4 border-primary shadow-sm">
                                <div class="card-body d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="fw-bold">Kurir</h6>
                                        <p class="mb-2">Pengiriman hanya untuk wilayah Yogyakarta dan sekitarnya. Biaya ongkir berlaku sesuai ketentuan.</p>
                                    </div>
                                    <button type="button" class="btn btn-primary"
                                            onclick="pilihPengiriman('kurir')">
                                        Pilih
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Opsi 2: Ambil Sendiri --}}
                        <div class="col-12">
                            <div class="card border-start border-4 border-success shadow-sm">
                                <div class="card-body d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="fw-bold">Ambil Sendiri</h6>
                                        <p class="mb-2">Silakan ambil barang langsung di gudang ReUseMart dalam waktu 3 hari setelah transaksi.</p>
                                    </div>
                                    <button type="button" class="btn btn-success"
                                            onclick="pilihPengiriman('ambil_sendiri')">
                                        Pilih
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies (Popper.js and Bootstrap JS) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const poinInput = document.getElementById('poinInput');
            const totalPoin = parseInt(document.getElementById('totalPoin').textContent);
            const subtotal = parseInt(document.getElementById('subtotalHidden').value);
            const errorEl = document.getElementById('poinError');
            const ongkirDisplay = document.getElementById('ongkirDisplay');

            window.hitungTotal = function () {
                let poin = parseInt(poinInput.value) || 0;

                if (poin > totalPoin) {
                    errorEl.style.visibility = 'visible';
                    return;
                } else {
                    errorEl.style.visibility = 'hidden';
                }

                const potongan = Math.floor(poin / 100) * 10000;

                // AMBIL jenis_pengiriman dari input[name]
                const jenis = document.querySelector('input[name="jenis_pengiriman"]').value;
                let ongkir = 0;

                if (jenis === 'Kurir') {
                    ongkir = subtotal < 1500000 ? 100000 : 0;
                    ongkirDisplay.textContent = `Rp${ongkir.toLocaleString('id-ID')}`;
                } else {
                    ongkirDisplay.textContent = 'Rp0';
                }

                const total = subtotal - potongan + ongkir;

                document.getElementById('potonganPoin').textContent = `-Rp${potongan.toLocaleString('id-ID')}`;
                document.getElementById('totalBayar').textContent = `Rp${total.toLocaleString('id-ID')}`;
                document.getElementById('totalPembayaranInput').value = total;
            }

            hitungTotal();

            poinInput.addEventListener('input', function () {
                document.getElementById('inputPoinTukar').value = poinInput.value;
                hitungTotal();
            });
        });
    </script>

    <script>
        function gunakanAlamatDariCard(button) {
            const data = button.dataset;

            const html = `
                <p>
                    ${data.jalan}, ${data.kelurahan}, ${data.kecamatan}, 
                    ${data.kota}, ${data.provinsi}<br>
                    ${data.kodepos}<br>
                    ${data.detail}
                </p>
            `;

            document.getElementById('alamatUtama').innerHTML = html;
            document.getElementById('inputIdAlamat').value = data.id;

            // Tutup modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('gantiAlamatModal'));
            modal.hide();
        }
    </script>

    <script>
        function pilihPengiriman(jenis) {
            // Tampilkan label yang dipilih
            document.getElementById('jenisPengirimanTerpilih').innerHTML = `<p>${jenis === 'kurir' ? 'Kurir' : 'Ambil Sendiri'}</p>`;

            // Ubah hidden input
            const input = document.querySelector('input[name="jenis_pengiriman"]');
            if (input) {
                input.value = jenis === 'kurir' ? 'Kurir' : 'Ambil Sendiri';
            }

            // Re-hit total
            if (typeof hitungTotal === 'function') {
                hitungTotal();
            }

            // Tutup modal
            const modalEl = document.getElementById('gantiPengirimanModal');
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.hide();
        }
    </script>
</body>
</html>
