<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ruang Diskusi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .discussion-card {
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .discussion-card .jawaban {
            margin-top: 10px;
            color: green;
        }

        .discussion-card .no-jawaban {
            margin-top: 10px;
            color: gray;
            font-style: italic;
        }

        .discussion-container {
            max-height: 600px;
            overflow-y: auto;
        }
    </style>
</head>
<body>

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

<div class="container mt-5">
    <h2 class="mb-4">Diskusi Produk</h2>

    {{-- Daftar diskusi --}}
    <div class="discussion-container">
        <!-- Formulir Pertanyaan Baru -->
        <div class="mt-5">
            <h3>Tanyakan Produk</h3>
            <form action="{{ route('diskusi.tanya') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="barang_id" class="form-label">Pilih Produk</label>
                    <select id="barang_id" name="barang_id" class="form-select" required>
                        <option value="">Pilih Produk</option>
                        @foreach ($barangAvailable as $barang)
                            <option value="{{ $barang->id_barang }}">{{ $barang->nama_barang }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="pertanyaan" class="form-label">Pertanyaan</label>
                    <textarea id="pertanyaan" name="pertanyaan" class="form-control" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Kirim Pertanyaan</button>
            </form>
        </div>

        @forelse ($diskusi as $d)
            <div class="discussion-card mt-4">
                <div>
                    <strong>Produk:</strong> 
                    {{ $d->barang_titipan ? $d->barang_titipan->nama_barang : 'Produk tidak ditemukan' }}
                </div>
                <div>
                    <strong>Pembeli:</strong> {{ $d->pembeli->nama_pembeli }}
                </div>
                <strong>Pertanyaan:</strong>
                <p>{{ $d->pertanyaan }}</p>
                <div class="{{ $d->jawaban ? 'jawaban' : 'no-jawaban' }}">
                    @if ($d->jawaban)
                        <strong>Dijawab oleh:</strong> {{ $d->pegawai->nama_pegawai }}
                        <div>
                            <strong>Jawaban:</strong> 
                            <p>{{ $d->jawaban }}</p>
                        </div>
                    @else
                        <em>Belum dijawab oleh admin</em>
                    @endif
                </div>
                <div class="mt-2">
                    <strong>Dibuat pada:</strong> {{ $d->tanggal_tanya->format('d M Y H:i') }}
                    @if ($d->tanggal_jawab)
                        <br>
                        <strong>Dijawab Pada:</strong> {{ $d->tanggal_jawab->format('d M Y H:i') }}
                    @endif
                </div>
            </div>
        @empty
            <p>Belum ada diskusi untuk produk ini.</p>
        @endforelse
        <div class="d-flex justify-content-center mt-4">
            {!! $diskusi->withQueryString()->links('pagination::bootstrap-5') !!}
        </div>
    </div>
</div>

</body>
</html>
