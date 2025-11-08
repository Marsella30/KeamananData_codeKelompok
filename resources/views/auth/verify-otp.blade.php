<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: rgba(111, 143, 70, 0.15); /* hijau lembut untuk background */
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        .otp-card {
            background-color: #ffffff;
            border: 1.5px solid rgba(111, 143, 70, 0.3); /* hijau tipis */
            border-radius: 18px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
            width: 400px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .otp-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(111, 143, 70, 0.1);
        }

        .otp-title {
            font-weight: 700;
            color: rgba(111, 143, 70, 1);
            margin-bottom: 0.5rem;
        }

        .otp-subtext {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 500;
            color: #4a5d3e;
            text-align: left;
            display: block;
        }

        .form-control {
            border-radius: 12px;
            border: 1px solid #cbd5bd;
            padding: 0.6rem 0.75rem;
            transition: 0.3s;
        }

        .form-control:focus {
            border-color: rgba(111, 143, 70, 0.8);
            box-shadow: 0 0 5px rgba(111, 143, 70, 0.2);
        }

        .btn-verify {
            background-color: rgba(111, 143, 70, 1);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            padding: 0.7rem;
            width: 100%;
            transition: 0.3s;
        }

        .btn-verify:hover {
            background-color: rgba(101, 130, 65, 1);
            transform: scale(1.02);
        }

        .alert {
            border-radius: 10px;
            font-size: 0.9rem;
        }

        .resend-link {
            margin-top: 1rem;
            display: block;
            font-size: 0.9rem;
            color: rgba(111, 143, 70, 1);
            text-decoration: none;
        }

        .resend-link:hover {
            text-decoration: underline;
        }

        /* Animasi muncul lembut */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .otp-card {
            animation: fadeUp 0.6s ease-out;
        }
    </style>
</head>
<body>
    <div class="otp-card">
        <h4 class="otp-title">Verifikasi OTP</h4>
        <p class="otp-subtext">Kami telah mengirimkan kode OTP ke email Anda. Silakan masukkan kode tersebut untuk melanjutkan.</p>

        @if(session('info'))
            <div class="alert alert-info">{{ session('info') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('otp.verify') }}">
            @csrf
            <div class="mb-3 text-start">
                <label for="otp" class="form-label">Kode OTP</label>
                <input type="text" id="otp" name="otp" class="form-control" placeholder="Masukkan kode 6 digit" required>
            </div>
            <button type="submit" class="btn-verify">Verifikasi</button>
        </form>

        <a href="{{ route('login') }}" class="resend-link">← Kembali ke halaman login</a>
    </div>
</body>
</html>
