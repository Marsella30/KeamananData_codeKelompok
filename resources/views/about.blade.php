<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReUseMart - About Us</title>

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

        /* Garis Pemisah di Bawah Carousel */
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
        
        section {
            padding: 20px;
            margin-bottom: 30px;
        }

        h1 {
            font-size: 2rem;
            color: #333;
            text-align: center;
        }

        h2 {
            font-size: 1.5rem;
            color: #333;
            margin-top: 20px;
        }

        p {
            font-size: 14px;
            color: #777;
            line-height: 1.6;
        }

        ul {
            list-style-type: none;
            padding-left: 0;
        }

        ul li {
            font-size: 14px;
            color: #777;
            margin: 5px 0;
        }

        .mission, .team, .values {
            margin-top: 30px;
        }

        .values ul {
            padding-left: 20px;
        }

        /* Container untuk Menampilkan 3 Nilai per Baris */
        .values-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);  /* Membuat 3 kolom */
            gap: 20px;  /* Jarak antar nilai */
            margin-top: 20px;
        }

        /* Setiap Kotak Nilai */
        .value-box {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            height: 250px;  /* Menetapkan tinggi untuk setiap kotak nilai */
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .value-box:hover {
            transform: scale(1.05);  /* Membesarkan kotak sedikit */
            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.2);  /* Menambahkan bayangan lebih kuat saat hover */
        }

        /* Judul dari Setiap Nilai */
        .value-box h3 {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        /* Garis Bawah Setiap Judul */
        .value-box hr {
            width: 60%;
            margin: 0 auto;
            border: 1px solid #28a745;  /* Garis hijau */
            margin-bottom: 10px;
        }

        /* Deskripsi dari Setiap Nilai */
        .value-box p {
            font-size: 14px;
            color: #777;
            line-height: 1.6;
            text-align: justify;
            margin-left: 10px;
            margin-right: 10px;
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

            .values-container {
                grid-template-columns: 1fr;  /* 1 kolom per baris di ponsel */
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
                    <li><a href="/kategori">Collection</a></li>
                    <li><a href="/about">About Us</a></li>
                </ul>
            </nav>
            <!-- Cart, Search, and Location -->
            <div class="cart-search">
                <!-- Search Input -->
                <!-- <input type="search" placeholder="Search for items..."> -->

                <div class="icons">
                    <a href="#"><img src="https://img.icons8.com/material/24/ffffff/shopping-cart.png" alt="Cart"></a>
                    <a href="login"><img src="https://img.icons8.com/material/24/ffffff/user.png" alt="Account"></a>
                </div>
            </div>
        </div>
    </header>

    <div class="navbar-shadow-separator"></div>
    
    <!-- Main Section -->
    <main>
        <div class="about-us" style="font-family: Arial, sans-serif; padding: 20px; max-width: 1000px; margin: 0 auto;">

            <!-- Gambar Header -->
            <div class="header-image" style="display: flex; justify-content: center; margin-bottom: 20px;">
                <img src="{{ asset('images/barang/reusemart.jpg') }}" alt="ReUseMart Banner" style="width: 80%; max-width: 1100px; height: 400px; object-fit: cover; border-radius: 10px;">
            </div>

            <!-- Kotak-kotak Penjelasan -->
            <div style="display: grid; gap: 20px;">

                <div style="background: #f9f9f9; padding: 20px; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
                    <h2><strong>Visi</strong></h2>
                    <p><strong>ReUseMart</strong> menjadi platform e-commerce terkemuka yang mendorong keberlanjutan dan pengurangan sampah dengan menyediakan tempat untuk membeli dan menjual barang bekas berkualitas tinggi.</p>
                </div>

                <div style="background: #f9f9f9; padding: 20px; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
                    <h2><strong>Misi</strong></h2>
                    <p><strong>ReUseMart</strong> hadir dengan tujuan menciptakan pasar berkelanjutan yang memudahkan orang untuk membeli dan menjual barang bekas. Kami ingin mengurangi limbah dan mendorong gaya hidup ramah lingkungan.</p>
                    <ol>
                        <li><strong>Meningkatkan Aksesibilitas untuk Barang Bekas Berkualitas:</strong>
                            <p style="margin-bottom: 0";>Memberikan pengalaman berbelanja yang mudah, nyaman, dan terpercaya bagi konsumen yang mencari barang bekas berkualitas.</p>
                        </li>
                        <li><strong>Mendorong Keberlanjutan:</strong>
                            <p style="margin-bottom: 0">Mengurangi limbah dengan memberikan barang bekas kesempatan kedua untuk dipakai oleh orang lain, sehingga mendukung prinsip ekonomi sirkular dan keberlanjutan.</p>
                        </li>
                        <li><strong>Menyediakan Pilihan yang Beragam:</strong>
                            <p style="margin-bottom: 0">Menawarkan berbagai kategori produk bekas yang terjamin kualitasnya, mulai dari elektronik, pakaian, perabotan rumah tangga, hingga barang koleksi.</p>
                        </li>
                        <li><strong>Memberdayakan Komunitas:</strong>
                            <p style="margin-bottom: 0">Membantu individu dan komunitas untuk mendapatkan akses ke barang bekas dengan harga terjangkau serta memberikan mereka kesempatan untuk menjual barang yang tidak lagi mereka butuhkan.</p>
                        </li>
                        <li><strong>Edukasi dan Kesadaran Konsumen:</strong>
                            <p style="margin-bottom: 0">Meningkatkan kesadaran masyarakat mengenai pentingnya membeli barang bekas untuk mendukung keberlanjutan dan pengurangan limbah.</p>
                        </li>
                    </ol>
                </div>

                <div style="background: #f9f9f9; padding: 20px; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
                    <h2 style="text-align: center;"><strong>Our Values</strong></h2>

                    <div class="values-container">
                        <div class="value-box">
                            <h3>Keberlanjutan</h3>
                            <hr>
                            <p>ReUseMart berkomitmen untuk mengurangi dampak lingkungan melalui perdagangan barang bekas yang masih berkualitas, mendukung pengurangan sampah dan penggunaan ulang sumber daya.</p>
                        </div>

                        <div class="value-box">
                            <h3>Kualitas</h3>
                            <hr>
                            <p>Meskipun barang yang dijual adalah barang bekas, kami memastikan bahwa setiap produk yang tersedia di platform kami telah melalui pemeriksaan kualitas yang ketat agar hanya barang berkualitas tinggi yang bisa ditemukan oleh pembeli.</p>
                        </div>

                        <div class="value-box">
                            <h3>Transparansi</h3>
                            <hr>
                            <p>Kami percaya bahwa kepercayaan adalah dasar dari hubungan jangka panjang dengan pelanggan dan penjual. Oleh karena itu, kami selalu memastikan bahwa deskripsi barang dan proses transaksi dilakukan secara terbuka dan jelas.</p>
                        </div>
                    </div>

                    <div class="values-container">
                        <div class="value-box">
                            <h3>Inovasi</h3>
                            <hr>
                            <p>Kami terus berinovasi untuk menciptakan platform yang lebih baik, mempermudah proses transaksi, serta meningkatkan pengalaman pelanggan dan penjual di ReUseMart.</p>
                        </div>

                        <div class="value-box">
                            <h3>Kehidupan yang Lebih Baik</h3>
                            <hr>
                            <p>Dengan menawarkan akses kepada barang-barang bekas berkualitas, kami membantu masyarakat mendapatkan barang yang mereka butuhkan dengan harga terjangkau sambil mendukung keberlanjutan dan kehidupan yang lebih baik.</p>
                        </div>

                        <div class="value-box">
                            <h3>Komunitas</h3>
                            <hr>
                            <p>Kami mengutamakan pemberdayaan komunitas untuk menciptakan ekosistem yang inklusif, yang dapat menghubungkan pembeli dan penjual untuk berbagi barang yang masih berguna dan mengurangi pemborosan.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="background: rgba(111, 143, 70, 1); height: 200px; padding: 40px 50px; border-radius: 0; box-shadow: 0 2px 6px rgba(0,0,0,0.2); width: 100%; margin-bottom: 0px;">
            <h2 style="color: white; margin-bottom: 10px;"><strong>
                <span style="border-bottom: 2px solid white; padding-bottom: 5px; display: inline-block; width: 150px;">Our Contact</span>
            </h2></strong>
            <p style="margin-bottom: 0px; color: white;">Jika Anda memiliki pertanyaan atau ingin tahu lebih banyak tentang ReUseMart, silakan hubungi kami:</p>
            <p style="margin-bottom: 0px; color: white;">Email:</> support@reusemart.com</p>
            <p style="margin-bottom: 0px; color: white;">Telepon:+1 (123) 456-7890</p>
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
</body>
</html>
