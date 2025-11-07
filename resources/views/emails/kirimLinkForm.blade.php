<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6fc;
            margin: 0;
            height: 100vh;
        }

        .background-animation {
            position: relative;
            width: 100%;
            height: 100%; /* Cover full height */
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center; /* This centers the card vertically and horizontally */
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
            background-color: rgba(111, 143, 70, 1);
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
            width: 100%; /* Ensure full width */
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card-container {
            width: 500px; /* Set width to your desired size */
            margin: auto;
        }

        .btn-custom {
            background-color: rgba(111, 143, 70, 1);
            color: white;
            border: none;
        }

        .btn-custom:hover {
            background-color: rgba(101, 130, 65, 1);
        }

    </style>
</head>
<body class="antialiased">

<section class="background-animation">
    <video class="video-background" autoplay loop muted>
        <source src="{{ asset('images/test.mp4') }}" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    <div class="bg-overlay"></div>

    <div class="container px-4 py-5 px-md-5 text-center text-lg-start my-5 content">
        <div class="card bg-glass card-container">
            <div class="card-body px-4 py-5 px-md-5">
                <h4 class="mb-3 fw-bold">Ubah Password Anda</h4>

                <form action="{{ route('kirim.link') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label>Email Anda</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Kirim Link</button>
                </form>
            </div>
        </div>
    </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
