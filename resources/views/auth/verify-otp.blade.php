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

        .otp-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1.5px solid rgba(111, 143, 70, 0.25);
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
            animation: fadeIn 0.4s ease;
        }

        /* OTP inputs */
        .otp-input {
            width: 52px;
            height: 58px;
            font-size: 24px;
            font-weight: 600;
            text-align: center;
            border-radius: 12px;
            border: 1.5px solid rgba(111,143,70,0.4);
            background: #fff;
            transition: 0.2s;
        }

        .otp-input:focus {
            border-color: rgba(111,143,70,1);
            box-shadow: 0 0 8px rgba(111,143,70,0.4);
            outline: none;
        }

        /* Button verify */
        .btn-verify {
            background: rgb(111,143,70);
            color: #fff;
            border-radius: 10px;
            font-weight: 600;
            border: none;
            transition: .25s;
        }

        .btn-verify:hover {
            background: rgb(95,121,58);
            transform: scale(1.02);
        }

        /* Smooth appear */
        @keyframes fadeIn {
            from {opacity:0; transform: translateY(12px);}
            to {opacity:1; transform: translateY(0);}
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
                        <div class="otp-card p-4">
                            <h4 class="text-center mb-2 fw-bold" style="color:#4e5f39;">
                                Verifikasi OTP
                            </h4>

                            <p class="text-center text-muted mb-3" style="font-size: 14px;">
                                Masukkan 6 digit kode yang dikirim ke email Anda.
                            </p>

                            @if(session('info'))
                                <div class="alert alert-info py-2">{{ session('info') }}</div>
                            @endif
                            @if($errors->any())
                                <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
                            @endif

                            <form method="POST" action="{{ route('otp.verify') }}">
                                @csrf

                                <div class="d-flex justify-content-center gap-2 mb-4">
                                    <input type="text" maxlength="1" class="otp-input" name="otp[]" required>
                                    <input type="text" maxlength="1" class="otp-input" name="otp[]" required>
                                    <input type="text" maxlength="1" class="otp-input" name="otp[]" required>
                                    <input type="text" maxlength="1" class="otp-input" name="otp[]" required>
                                    <input type="text" maxlength="1" class="otp-input" name="otp[]" required>
                                    <input type="text" maxlength="1" class="otp-input" name="otp[]" required>
                                </div>

                                <button type="submit" class="btn btn-verify w-100 py-2">
                                    Verifikasi
                                </button>

                                <a href="{{ route('login') }}" class="d-block text-center mt-3" style="color:#6f8f46;">
                                    ← Kembali ke Login
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

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
    const otpInputs = document.querySelectorAll(".otp-input");

    otpInputs.forEach((input, index) => {
        input.addEventListener("input", () => {
            if (input.value.length === 1 && index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
            }
        });

        input.addEventListener("keydown", (e) => {
            if (e.key === "Backspace" && index > 0 && !input.value) {
                otpInputs[index - 1].focus();
            }
        });
    });
</script>

</body>
</html>