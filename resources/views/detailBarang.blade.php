<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReUseMart - Collection</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

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
            transform: scale(1.1); /* sedikit membesar saat di-hover */
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
            color: white;
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

        /* Container untuk Kategori */
        .category-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(85px, max-content));
            gap: 10px;
            justify-items: center;
            margin-top: 20px;
            margin-left: auto;
            margin-right: auto;
            width: 80%;
        }

        /* Setiap Kartu Kategori */
        .category-card {
            background-color: #f9f9f9;
            padding: 8px;
            border-radius: 5px !important;
            text-align: center;
            justify-items: center;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            height: 40px !important;
            margin: 0;
            text-decoration: none;
            width: 100%;
        }

        .category-card h3 {
            margin-top: 5px;
            margin: 2px 0 0 0;
            font-size: 8px;
            color: #333;
            text-decoration: none;
        }

        .carousel-divider {
            width: 80%;
            height: 1px; /* tebal garis */
            background-color: rgba(111, 143, 70, 1);; /* warna garis */
            margin: 20px auto; /* jarak dari carousel */
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1); /* bayangan untuk garis */
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

        /* Carousel Section */
        .product-container {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 20px;
            margin: 0 auto;
            width: 80%;
            box-sizing: border-box;
        }

        /* Gambar Produk Carousel */
        .carousel-inner {
            width: 100%;
            height: 400px;  /* Menentukan tinggi carousel */
        }

        .carousel-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;  /* Agar gambar mengisi seluruh area tanpa terdistorsi */
            border-radius: 8px;
        }

        /* Informasi Produk */
        .product-info {
            width: 50%;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        /* Kontainer untuk Tombol */
        .button-container {
            display: flex;
            gap: 10px;  /* Memberikan jarak antar tombol */
            margin-top: 10px;  /* Memberikan ruang antara tombol dan elemen lainnya */
        }

        /* Tombol Add to Cart */
        .add-to-cart-btn{
            background-color: white;  /* Latar belakang putih */
            color: #28a745;  /* Teks hijau */
            padding: 10px 20px;
            border: 2px solid #28a745;  /* Border hijau */
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease, color 0.3s ease;
            width: 30%;  /* Membuat tombol lebar setengah container */
        }

        .buy-now-btn {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.2rem;
            transition: background-color 0.3s ease;
            width: 100%;  /* Membuat tombol lebar setengah container */
        }

        /* Efek Hover Tombol */
        .add-to-cart-btn:hover{
            background-color: #28a745;  /* Latar belakang hijau saat hover */
            color: white;  /* Warna teks menjadi putih saat hover */
            border: 2px solid #28a745;
        }

        .buy-now-btn:hover {
            background-color: #218838;
        }

        .related-image {
            width: 100%;
            height: 160px;
            object-fit: contain;
            border-radius: 6px;
            background-color: #f9f9f9;
        }

        .related-product-card {
            border: 1px solid #eee;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.2s;
            background-color: #fff;
            margin-bottom: 10px;
        }

        .related-product-card:hover {
            transform: scale(1.03);
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

            .product-container {
                flex-direction: column; /* Gambar dan informasi produk dalam satu kolom pada layar kecil */
            }

            .carousel-inner {
                height: 300px;  /* Menurunkan tinggi carousel pada perangkat kecil */
            }

            .add-to-cart-btn, .buy-now-btn {
                width: 100%;  /* Membuat tombol mengisi lebar penuh pada perangkat kecil */
            }
        }
    </style>
</head>
<body>

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
                    <li><a href="/kategori">Collection</a></li>
                    <li><a href="/about">About Us</a></li>
                </ul>
            </nav>
            <!-- Cart, Search, and Location -->
            <div class="cart-search">
                <!-- Search Input -->
                <!-- <input type="search" placeholder="Search for items..."> -->
                <!-- <input type="search" id="search" placeholder="Search for items..." onkeyup="searchProducts()" /> -->
                <div id="search-results"></div>
                <!-- Icons -->
                <div class="icons">
                    <a href="{{ route('keranjang') }}"><img src="https://img.icons8.com/material/24/ffffff/shopping-cart.png" alt="Cart"></a>
                    <a href="{{ route('checkout') }}"><img src="https://img.icons8.com/material/24/ffffff/user.png" alt="Account"></a>
                </div>
            </div>
        </div>
    </header>

    <div class="navbar-shadow-separator"></div>
    
    <!-- Main Section -->
    <main>
        <section class="product-detail">
            <div class="product-container">
                <!-- Gambar Produk - Carousel -->
                <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                    <!-- Carousel Indicators -->
                    <div class="carousel-indicators">
                        @foreach ($product->fotoBarang as $index => $foto)
                            <button type="button"
                                    data-bs-target="#productCarousel"
                                    data-bs-slide-to="{{ $index }}"
                                    class="{{ $index == 0 ? 'active' : '' }}"
                                    aria-current="{{ $index == 0 ? 'true' : 'false' }}"
                                    aria-label="Slide {{ $index + 1 }}"></button>
                        @endforeach
                    </div>

                    <!-- Carousel Inner -->
                    <div class="carousel-inner">
                        @foreach ($product->fotoBarang as $index => $foto)
                            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                <img src="{{ asset('images/barang/' . $foto->nama_file) }}"
                                    class="d-block w-100 img-fluid"
                                    alt="Foto {{ $index + 1 }}">
                            </div>
                        @endforeach
                    </div>

                    <!-- Carousel Controls -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>

                <!-- Informasi Produk -->
                <div class="product-info" style="line-height: 1.0;">
                    <h1 class="product-name" style="font-size: 30px; margin-bottom: 10px;"><strong>{{ $product->nama_barang }}</strong></h1>
                    <p style="font-size: 14px; color: grey; margin: 0 0 6px;">{{ $product->kategori->nama_kategori }}</p>
                    <p class="product-description" style="text-align: justify; margin: 0 0 8px;">{{ $product->deskripsi }}</p>
                    <p style="text-align: left; color: grey; margin: 0 0 8px;">Berat barang: {{ $product->berat }} kg</p>

                    <!-- Informasi Garansi Produk -->
                <div class="garansi-info" style="margin: 0 0 8px;">
                    <p style="font-size: 15px; font-weight: bold; margin-bottom: 10px; "><strong>Garansi Status:</strong></p>
                    
                    <!-- Cek jika status garansi tersedia -->
                    @if ($garansi_status !== "Garansi Tidak Tersedia")
                        <p style="font-size: 13px;">
                            {{ $garansi_status }} hingga {{ \Carbon\Carbon::parse($product->tanggal_garansi)->format('d M Y') }}
                        </p>
                    @else
                        <p style="font-size: 13px;">{{ $garansi_status }}</p>
                    @endif
                </div>

                    <div class="additional-info" style="margin: 0 0 8px;">
                        <p style="margin: 0;"><strong>Status Barang:</strong> {{ $product->status_barang }}</p>
                    </div>

                    <p class="product-price" style="font-size: 25px; margin: 4px 0 12px;">
                        <strong>Rp{{ number_format($product->harga_jual, 0, ',', '.') }}</strong>
                    </p>

                    <h5 style="font-size: 14px; color: grey; margin: 0 0 6px; display: flex; align-items: center; gap: 6px;">
                       {{ $product->penitip->nama_penitip }} -
                        @if($avgRating > 0)
                            @php
                                $rounded = round($avgRating * 2) / 2; 
                                $fullStars = floor($rounded);
                                $halfStar  = ($rounded - $fullStars) == 0.5 ? 1 : 0;
                                $emptyStars = 5 - $fullStars - $halfStar;
                            @endphp
                            {{-- Tampilkan bintang penuh --}}
                            @for($i = 0; $i < $fullStars; $i++)
                                <i class="fas fa-star text-warning"></i>
                            @endfor

                            {{-- Tampilkan setengah bintang --}}
                            @if($halfStar)
                                <i class="fas fa-star-half-alt text-warning"></i>
                            @endif

                            {{-- Tampilkan bintang kosong --}}
                            @for($i = 0; $i < $emptyStars; $i++)
                                <i class="far fa-star text-warning"></i>
                            @endfor

                            {{-- Angka rata-rata --}}
                            <span style="font-size: 13px; color: #555;">({{ number_format($avgRating, 1) }})</span>
                        
                        @else
                            {{-- Tampilkan 5 bintang kosong tanpa warna (abu-abu) --}}
                            @for($i = 0; $i < 5; $i++)
                                <span class="text-muted">&#9734;</span> {{-- ☆ --}}
                            @endfor
                            <small class="text-muted">(Belum ada rating)</small>
                        @endif
                    </h5>

                    <!-- Kontainer untuk Tombol -->
                    <div class="button-container" style="margin-top: 35px;">
                        <!-- Tombol Add to Cart -->
                        <form action="{{ route('keranjang.tambah') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id_barang" value="{{ $product->id_barang }}">
                            <button type="submit" class="add-to-cart-btn w-100">Add to Cart</button>
                        </form>
                        <!-- Tombol Beli Barang -->
                        <a href="{{ route('checkout') }}">
                            <button class="buy-now-btn">Beli Barang</button>
                        </a>
                    </div>

                </div>
            </div>

            <div class="related-products" style="margin-top: 50px; margin-left: 150px;">
                <h3 style="font-size: 24px"><Strong>Produk Serupa</Strong></h3>
                <div class="related-products-grid" style="display: flex; flex-wrap: wrap; gap: 20px;">
                    @forelse($produk_serupa as $item)
                        <a href="{{ url('product/' . $item->id_barang) }}" class="related-product-card" style="width: 200px; text-decoration: none; color: inherit;">
                           <img src="{{ asset('images/barang/' . ($item->fotoBarang->first()->nama_file ?? 'default.jpg')) }}" alt="..." class="img-fluid related-image">
                            <div style="padding: 8px;">
                                <p style="font-size: 13px; color: grey; margin: 4px 0;">{{ $item->kategori->nama_kategori ?? 'Kategori' }}</p>
                                <h4 style="font-size: 16px; margin: 0 0 4px;">{{ $item->nama_barang }}</h4>
                                <p style="font-size: 14px; font-weight: bold; margin: 0;">Rp{{ number_format($item->harga_jual, 0, ',', '.') }}</p>
                            </div>
                        </a>
                    @empty
                        <p>Tidak ada produk serupa untuk ditampilkan.</p>
                    @endforelse
                </div>
            </div>
        </section>
    </main>

    <!-- Footer Section -->
    <footer>
        <div class="footer-container">
            <div class="footer-left">
                <p>© 2024 Reusemart. All rights reserved.</p>
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
