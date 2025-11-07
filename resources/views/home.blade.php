<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReUseMart - Home</title>

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

    {{-- Toast tetap menempel di layar --}}
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
        @if (session('success'))
            <div id="liveToast" class="toast fade" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header bg-success text-white">
                    <strong class="me-auto">Sukses</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if (session('warning'))
            <div id="liveToast" class="toast fade" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header bg-danger text-white">
                    <strong class="me-auto">Peringatan</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    {{ session('warning') }}
                </div>
            </div>
        @endif
    </div>

    <!-- <div class="navbar-shadow-separator"></div> -->
    
    <!-- Main Section -->
    <main>
        <div id="carouselExampleCaptions" class="carousel slide">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
            </div>
            <div class="carousel-inner">
                @foreach($barangs->take(3) as $index => $barang)
                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                    <a href="{{ url('product/' . $barang->id_barang) }}">
                        <img src="{{ asset('images/barang/' . ($barang->fotoBarang->first()->nama_file ?? 'default.jpg')) }}" alt="Foto Barang" class="img-fluid">
                    </a>
                    <div class="carousel-caption d-none d-md-block">
                        <h5>{{ $barang->nama_barang }}</h5>
                        <p>{{ Str::limit($barang->deskripsi, 100) }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>

        <div class="carousel-divider"></div>

        <div class="category-title">
            <h2>Kategori</h2>
        </div>

        <!-- Featured Categories -->
        <div class="category-container">
            <!-- Category 1 -->
            <a href="{{ url('kategori/1') }}" class="category-card">
                <img src="{{ asset('images/kategori/laptop.png') }}" alt="Elektronik & Gadget">
                <h3>Elektronik & Gadget</h3>
            </a>
            <!-- Category 2 -->
            <a href="{{ url('kategori/2') }}" class="category-card">
                <img src="{{ asset('images/kategori/baju.png') }}" alt="Pakaian & Aksesori">
                <h3>Pakaian & Aksesori</h3>
            </a>
            <!-- Category 3 -->
            <a href="{{ url('kategori/3') }}" class="category-card">
                <img src="{{ asset('images/kategori/sofa.png') }}" alt="Perabotan Rumah Tangga">
                <h3>Perabotan Rumah Tangga</h3>
            </a>
            <!-- Category 4 -->
            <a href="{{ url('kategori/4') }}" class="category-card">
                <img src="{{ asset('images/kategori/tas.png') }}" alt="Buku & Peralatan Sekolah">
                <h3>Buku, Alat Tulis, Peralatan Sekolah</h3>
            </a>
            <!-- Category 5 -->
            <a href="{{ url('kategori/5') }}" class="category-card">
                <img src="{{ asset('images/kategori/mainan.png') }}" alt="Hobi & Mainan">
                <h3>Hobi, Mainan, Koleksi</h3>
            </a>
            <!-- Category 6 -->
            <a href="{{ url('kategori/6') }}" class="category-card">
                <img src="{{ asset('images/kategori/bayi.png') }}" alt="Perlengkapan Bayi & Anak">
                <h3>Perlengkapan Bayi & Anak</h3>
            </a>
            <!-- Category 7 -->
            <a href="{{ url('kategori/7') }}" class="category-card">
                <img src="{{ asset('images/kategori/roda.png') }}" alt="Otomotif & Aksesori">
                <h3>Otomotif & Aksesori</h3>
            </a>
            <!-- Category 8 -->
            <a href="{{ url('kategori/8') }}" class="category-card">
                <img src="{{ asset('images/kategori/tenda.png') }}" alt="Perlengkapan Taman & Outdoor">
                <h3>Perlengkapan Taman & Outdoor</h3>
            </a>
            <!-- Category 9 -->
            <a href="{{ url('kategori/9') }}" class="category-card">
                <img src="{{ asset('images/kategori/kantor.png') }}" alt="Peralatan Kantor & Industri">
                <h3>Peralatan Kantor & Industri</h3>
            </a>
            <!-- Category 10 -->
            <a href="{{ url('kategori/10') }}" class="category-card">
                <img src="{{ asset('images/kategori/cermin.png') }}" alt="Kosmetik & Perawatan Diri">
                <h3>Kosmetik & Perawatan Diri</h3>
            </a>
        </div>

        <div class="product-container">
            @foreach($barangs->take(10) as $barang)
            <a href="{{ url('product/' . $barang->id_barang) }}" class="product-card">
                <img src="{{ asset('images/barang/' . ($barang->fotoBarang->first()->nama_file ?? 'default.jpg')) }}" alt="Foto Barang" class="img-fluid">
                <div class="product-info">
                    <p class="product-category">{{ $barang->kategori->nama_kategori ?? 'Kategori Tidak Ada' }}</p>
                    <h3 class="product-name">{{ $barang->nama_barang }}</h3>
                    <!-- <div class="product-rating">
                        <span>★ ({{ rand(4,5) }}.{{ rand(0,9) }})</span>
                    </div> -->
                    <p class="product-status">{{ $barang->status_barang }}</p>
                </div>
                <div class="product-price">
                    <div class="price-container">
                        <span class="current-price">Rp{{ number_format($barang->harga_jual, 0, ',', '.') }}</span>
                    </div>
                    <div class="add-to-cart-container">
                        <form action="{{ route('keranjang.tambah') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id_barang" value="{{ $barang->id_barang }}">
                            <button type="submit" class="add-to-cart">
                                <img src="https://img.icons8.com/material/24/007848/shopping-cart.png" alt="Cart">
                                    Add
                            </button>
                        </form>
                    </div>
                </div>
            </a>
            @endforeach
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

    <!-- Bootstrap JS and dependencies (Popper.js and Bootstrap JS) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>

    <script>
        const toastLive = document.getElementById('liveToast');
            if (toastLive) {
                const toast = new bootstrap.Toast(toastLive);
                toast.show();
            }
    </script>
</body>
</html>
