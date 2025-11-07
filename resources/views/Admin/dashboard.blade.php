<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport"content="width=device-width, initial-scale=1.0">
        <title>Dashboard</title>
        
        <style>
            .main-sidebar {
                height: 100vh; /* Menyesuaikan tinggi dengan layar */
                overflow-y: auto; /* Agar sidebar bisa di-scroll jika terlalu panjang */
                background: linear-gradient(135deg, rgba(0, 128, 0, 0.7) 0%, rgba(108, 241, 108, 0.7) 100%);
            }

            .main-header{
                background-color: #ffffff;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            }

            .nav-sidebar .nav-item .nav-link{
                border-radius: 10px;
                transition: background-color 0.3 ease;
            }

            .nav-sidebar .nav-item .nav-link:hover{
                background-color: rgba(255, 255, 255, 0.1);
            }

            .card{
                border-radius: 10px;
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                transition: transform 0.3s ease-in-out;
            }

            .card:hover{
                transform: scale(1.02);
            }

            .modal-content{
                border-radius: 10px;
            }

            .content-wrapper {
                padding-top: 20px; /* Memberikan padding agar tidak tabrakan dengan header */
                min-height: calc(100vh - 50px); /* Agar konten tidak tumpang tindih dengan footer */
            }

            .modal-header{
                background-color: #3498db;
                color: white;
            }

            .btn-close{
                background-color: white;
            }

            .main-footer{
                background-color: #f8f9fa;
                border-top: 1px solid #dee2e6;
                padding: 10px;
                text-align: center;
            }
        </style>

        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <!-- Link untuk CSS Bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
    </head>
    <body class="hold-transition sidebar-mini">
        <div class="wrapper">
            <nav class="main-header navbar navbar-expand navbar-white navbar-light">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                            <i class="fas fa-bars"></i>
                        </a>
                    </li>
                </ul>

                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </li>
                </ul>

                <ul class="navbar-nav ml-left">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                            <i class="fas fa-expand-arrows-alt"></i>
                        </a>
                    </li>
                </ul>
            </nav>

            <aside class="main-sidebar sidebar-dark-primary elevation-4">
                <a href="{{ url('dashboard.admin') }}" class="brand-link">

                    <span class="brand-text font-weight-light" style="padding-left:5vw;">Reusemart</span>
                </a>

                <div class="sidebar">
                    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                        <div class="image">
                            <a href="{{ route('dashboard.admin') }}">
                                <img src="{{ asset('images/logo2.png') }}" alt="Logo Reusmart"
                                    class="brand-image img-circle elevation-3"
                                    style="opacity: .8; width: 200px; height: 80px;">
                            </a>
                        </div>
                    </div>

                    <nav class="mt-2">
                        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-acccordion="false">
                            <li class="nav-item">
                                <a href="{{url('organisasi')}}" class="nav-link">
                                    <i class="nav-icon bi bi-buildings-fill" style="padding-left: -10px;"></i>
                                    <p>Organisasi</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('pegawai') }}" class="nav-link">
                                    <i class="bi bi-person-badge-fill"></i>
                                    <p>Pegawai</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.topSeller') }}" class="nav-link">
                                    <i class="nav-icon bi bi-stars"></i>
                                    <p>Top Seller</p>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </aside>

            <div class="content-wrapper">
                @yield('isi')
            </div>

            <footer class="main-footer">
                
                <strong>Kami ada untuk Anda </strong>
            </footer>
        </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1.0/dist/js/adminlte.min.js"></script>
    </body>
        
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Apakah ingin Logout</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="{{url('/')}}">
                        <button type="button" class="btn btn-danger" id="logout-btn">Logout</button>
                    </a>
                </div>
            </div>
        </div>
    </div>
</html>