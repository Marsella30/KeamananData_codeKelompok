<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
        crossorigin="anonymous">
    <style>
        body {
            font-family: 'Figtree', Arial, Helvetica, sans-serif;
            margin: 0;
            overflow: hidden;
        }

        .background-animation {
            position: relative;
            width: 100%;
            min-height: 100vh;
            overflow: hidden;
        }

        .video-background {
            position: absolute;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            z-index: 1;
            transform: translate(-50%, -50%);
            object-fit: cover;
        }

        .bg-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(111, 143, 70, 0.5);
            z-index: 2;
        }

        .bg-glass {
            background-color: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: saturate(150%) blur(30px);
            z-index: 3;
        }

        .content {
            position: relative;
            z-index: 4;
            color: white;
            text-align: center;
        }

        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

    </style>
</head>
<body class="antialiased">

<section class="background-animation">
    <video class="video-background" autoplay loop muted>
        <source src="{{ asset('images/video.mp4') }}" type="video/mp4">
        <!-- <source src="{{asset("images/test.mp4")}}" type="video/mp4"> -->
        Your browser does not support the video tag.
    </video>

    <div class="bg-overlay"></div>
    <div class="container px-4 py-5 px-md-5 text-center text-lg-start my-5 content">
        <div class="row gx-lg-5 align-items-center mb-5">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <h1 class="my-5 display-5 fw-bold">
                    Gunakan Kembali, Kurangi Sampah, Berbagi Lebih Banyak
                </h1>
            </div>
            <div class="col-lg-5 mb-5 mb-lg-0 position-relative ms-auto">
                <div class="card bg-glass">
                    <div class="card-body px-4 py-5 px-md-5">
                    <form class="form" action="{{ url('login') }}" method="POST" id="loginForm">
                        @csrf
                        <div>
                            <h4 class="mb-3 fw-bold text-start">Selamat Datang</h4>
                        </div>

                        <div class="form-floating mb-4">
                            <select name="tipe_user" class="form-select" id="floatingTipeUser" required>
                                <option value="" selected disabled>Pilih Tipe Pengguna</option>
                                <option value="pembeli">Pembeli</option>
                                <option value="penitip">Penitip</option>
                                <option value="organisasi">Organisasi</option>
                                <option value="pegawai">Pegawai</option>
                            </select>
                            <label for="floatingTipeUser">Tipe Pengguna</label>
                        </div>

                        <div class="form-floating mb-4" id="emailField">
                            <input type="email" name="email" class="form-control" id="floatingInput" placeholder="Email" />
                            <label for="floatingInput">Email</label>
                        </div>

                        <div class="form-floating mb-4 d-none" id="organisasiSelectWrapper">
                            <select name="id_organisasi" class="form-select" id="organisasiSelect">
                                <option value="" disabled selected>Pilih Organisasi</option>
                                @foreach($organisasiList as $org)
                                    <option value="{{ $org->id_organisasi }}">{{ $org->nama_organisasi }}</option>
                                @endforeach
                            </select>
                            <label for="organisasiSelect">Nama Organisasi</label>
                        </div>

                        <div class="form-floating">
                            <input type="password" name="password" class="form-control mb-4" id="floatingPassword" placeholder="Kata Sandi" required />
                            <label for="floatingPassword">Kata Sandi</label>
                        </div>

                        <!-- @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif -->

                        <!-- Tempat untuk error message dari server -->
                        <div id="errorMessages" class="alert alert-danger d-none mb-2"></div>
                        <!-- Countdown rate limit -->
                        <div id="countdownLabel" class="text-danger mb-2"></div>

                        <button type="button" id="loginButton" style="width:100%;" class="btn btn-dark btn-block mb-2 mt-3">Login</button>

                        <!-- <button type="submit" style="width:100%;" class="btn btn-dark btn-block mb-2 mt-3">Login</button> -->

                        <div class="d-flex justify-content-center mt-2">
                            <a href="{{ url('linkForm') }}" class="link-dark" style="font-size: 17px;">Lupa Password?</a>
                        </div>
                        <div class="d-flex justify-content-center mt-2">
                            <a href="{{ url('register') }}" class="link-dark" style="font-size: 22px;">Buat Akun ReUseMart</a>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-C6RzsynM9kwDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous">
</script>
<script>
    const tipeUserSelect = document.getElementById('floatingTipeUser');
    const emailField = document.getElementById('emailField');
    const orgWrapper = document.getElementById('organisasiSelectWrapper');

    tipeUserSelect.addEventListener('change', function () {
        if (this.value === 'organisasi') {
            emailField.classList.add('d-none');
            orgWrapper.classList.remove('d-none');
        } else {
            emailField.classList.remove('d-none');
            orgWrapper.classList.add('d-none');
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    const form = document.getElementById('loginForm');
const loginButton = document.getElementById('loginButton');
const countdownLabel = document.getElementById('countdownLabel');
const errorDiv = document.getElementById('errorMessages');

// CSRF header untuk axios
axios.defaults.headers.common['X-CSRF-TOKEN'] = form.querySelector('input[name="_token"]').value;

loginButton.addEventListener('click', async function() {
    errorDiv.classList.add('d-none'); // sembunyikan error sebelumnya
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    try {
        const response = await axios.post(form.action, data);
        console.log('Login sukses', response.data);

        if(response.data.redirect_page) {
            window.location.href = response.data.redirect_page;
        }
    } catch (err) {
        if(err.response && err.response.status === 429) {
            const seconds = err.response.headers['retry-after'] || 60;
            loginButton.disabled = true;
            let s = seconds;
            countdownLabel.innerText = `Coba lagi dalam ${s} detik`;
            const interval = setInterval(() => {
                s--;
                countdownLabel.innerText = `Coba lagi dalam ${s} detik`;
                if(s <= 0) {
                    clearInterval(interval);
                    loginButton.disabled = false;
                    countdownLabel.innerText = '';
                }
            }, 1000);
        } else if(err.response && err.response.data) {
            errorDiv.innerText = err.response.data.error || 'Login gagal';
            errorDiv.classList.remove('d-none');
        } else {
            errorDiv.innerText = 'Terjadi kesalahan server';
            errorDiv.classList.remove('d-none');
        }
    }
});
</script>

</body>
</html>