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

        .category-title h2 {
            font-size: 28px;
            font-weight: 600;
            color: #333;
        }

        .add-to-cart-container {
            display: flex;
            justify-content: flex-end;
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
            margin-right: 10px; /* Jarak antara ikon cart dan teks Add */
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

        .container-main{
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-start;
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

        .cartList{
            padding: 30px 150px;
        }

        .product-title:hover {
            color:rgb(104, 242, 136) !important;
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
                    <a href="{{ route('pembeli.profil') }}"><img src="https://img.icons8.com/material/24/ffffff/user.png" alt="Account"></a>
                </div>
            </div>
        </div>
    </header>

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

        @if (session('error'))
            <div id="liveToast" class="toast fade" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header bg-danger text-white">
                    <strong class="me-auto">Peringatan</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    {{ session('error') }}
                </div>
            </div>
        @endif
    </div>

    <!-- <div class="navbar-shadow-separator"></div> -->
    
    <!-- Main Section -->
    <main>
        <div class="cartList mx-auto mt-8">
                <div class="border rounded-lg p-4 mt-4 mb-4 flex flex-col space-y-2">
                    <div class="d-flex align-items-center mb-3 justify-content-between w-100">
                        <!-- kiri: icon + teks -->
                        <div class="d-flex align-items-center">
                                <span class="badge bg-warning text-dark me-2" style="font-size:1.5rem">
                                <i class="bi bi-clock-history"></i>
                            </span>
                            <div>
                                <h4 class="mb-0 fw-bold">Bayar sebelum</h4>
                                <small class="text-muted">{{ $tanggalTransaksiPlus1Menit->format('d M Y, H:i') }}</small>
                            </div>
                        </div>

                        <!-- kanan: countdown menempel di ujung kanan -->
                        <div class="d-flex gap-2">
                            <div id="countdown-minutes" class="bg-danger text-white px-3 py-1 rounded text-center" style="min-width:70px">1m</div>
                            <div id="countdown-seconds" class="bg-danger text-white px-3 py-1 rounded text-center" style="min-width:70px">00s</div>
                        </div>
                    </div>

                    <!-- Pesan Peringatan -->
                    <div class="alert alert-warning py-2" role="alert" style="font-size: 0.9rem">
                        <strong>Tinggal selangkah lagi untuk menyelesaikan pesananmu</strong><br />
                        Segera bayar biar nggak keduluan dengan pembeli lainnya!
                    </div>

                    <!-- Nomor Transaksi -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nomor Virtual Account</label>
                        <div class="d-flex align-items-center gap-3">
                            <h5 class="mb-0 text-dark">075048615248</h5>
                        </div>
                    </div>

                    <!-- Total Tagihan -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Total Tagihan</label>
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="text-success mb-0">Rp{{ number_format($totalHarga, 0, ',', '.') }}</h4>
                        </div>
                    </div>

                    <!-- Upload Bukti Pembayaran -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Masukkan Bukti Pembayaran</label>
                        <form action="{{ route('upload.bukti') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id_transaksi" value="{{ request('id_transaksi') }}">
                                <input type="file" id="buktiPembayaran" name="bukti_pembayaran" accept="image/png, image/jpeg, image/jpg" class="form-control"/>
                            <div id="fileError" class="text-danger mt-1" style="display:none; font-size:0.875rem;">
                                Format file tidak valid! Harap pilih file gambar (jpg, jpeg, png).
                            </div>
                            <div class="mt-3 mb-3">
                                <img id="previewImage" src="" alt="Preview Gambar" style="max-width: 80%; max-height: 300px; display: none; border-radius: 8px; border: 1px solid #ddd;" />
                            </div>

                            <div class="d-flex gap-3 mb-4">
                                <button type="submit" class="btn btn-success flex-fill">Upload Bukti Pembayaran</button>
                            </div>
                        </form>
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

    <!-- Bootstrap JS and dependencies (Popper.js and Bootstrap JS) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>

    <script>
        let countDownDate = new Date().getTime() + 60 * 1000;
        const idTransaksi = "{{ request('id_transaksi') }}"; // Ambil id_transaksi dari URL

        function updateCountdown() {
            let now = new Date().getTime();
            let distance = countDownDate - now;

            if (distance < 0) {
                document.getElementById("countdown-minutes").textContent = "0m";
                document.getElementById("countdown-seconds").textContent = "00s";
                clearInterval(interval);

                // Jalankan fungsi batal transaksi
                fetch("{{ route('batal.transaksi') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        id_transaksi: idTransaksi
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        window.location.href = "{{ route('home') }}"; // Redirect ke home setelah pembatalan
                    } else {
                        console.log(data.message);
                    }
                })
                .catch(error => {
                    console.error("Terjadi kesalahan:", error);
                });

                return;
            }

            let minutes = Math.floor(distance / (1000 * 60));
            let seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById("countdown-minutes").textContent = minutes + "m";
            document.getElementById("countdown-seconds").textContent =
                (seconds < 10 ? "0" : "") + seconds + "s";
        }

        let interval = setInterval(updateCountdown, 1000);
        updateCountdown();
    </script>

    <script>
        const inputFile = document.getElementById('buktiPembayaran');
        const fileError = document.getElementById('fileError');
        const previewImage = document.getElementById('previewImage');

        inputFile.addEventListener('change', () => {
            const file = inputFile.files[0];
            if (!file) {
                fileError.style.display = 'none';
                previewImage.style.display = 'none';
                previewImage.src = '';
                return;
            }

            const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            if (!validTypes.includes(file.type)) {
                fileError.style.display = 'block';
                previewImage.style.display = 'none';
                previewImage.src = '';
                inputFile.value = ''; // reset input file agar user pilih ulang
            } else {
                fileError.style.display = 'none';
                
                // Tampilkan preview gambar
                const reader = new FileReader();
                reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewImage.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });
    </script>

    <script>
        const toastLive = document.getElementById('liveToast');
            if (toastLive) {
                const toast = new bootstrap.Toast(toastLive);
                toast.show();
            }
    </script>
</body>
</html>
