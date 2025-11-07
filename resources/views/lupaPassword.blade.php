<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reset Password</title>

  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
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
      top: 0; left: 0;
      width: 100%; height: 100%;
      background-color: rgba(111, 143, 70, 0.8);
      z-index: 2;
    }
    .bg-glass {
      background-color: rgba(255, 255, 255, 0.85) !important;
      backdrop-filter: saturate(150%) blur(30px);
      z-index: 3;
    }
    .content {
      position: relative;
      z-index: 4;
      color: #333;
      text-align: center;
    }
    .form-control, .form-label {
      color: #333;
    }
  </style>
</head>
<body>

<section class="background-animation">
  <video class="video-background" autoplay loop muted>
    <source src="{{ asset('images/test.mp4') }}" type="video/mp4">
    Your browser does not support the video tag.
  </video>
  <div class="bg-overlay"></div>

  <div class="container px-4 py-5 text-center content">
    <div class="row justify-content-center">
      <div class="col-lg-6">
        <h1 class="my-4 display-5 fw-bold text-white">Reset Password</h1>
        <div class="card bg-glass">
          <div class="card-body p-4">
            <form action="{{ route('password.ubah') }}" method="POST">
              @csrf
                <div class="form-floating mb-4">
                    <select name="tipe_user" class="form-select" id="floatingTipeUser" required>
                        <option value="" selected disabled>Pilih Tipe Pengguna</option>
                        <option value="pembeli">Pembeli</option>
                        <option value="penitip">Penitip</option>
                        <option value="organisasi">Organisasi</option>
                    </select>
                    <label for="floatingTipeUser">Tipe Pengguna</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="email"
                          name="email"
                          class="form-control"
                          id="floatingEmail"
                          placeholder="name@example.com"
                          required>
                    <label for="floatingEmail">Alamat Email</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="password"
                          name="password"
                          class="form-control"
                          id="floatingPassword"
                          placeholder="Password Baru"
                          required>
                    <label for="floatingPassword">Password Baru</label>
                </div>

                @if ($errors->any())
                <div class="alert alert-danger">
                  <ul>
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                  </ul>
                </div>
                @endif

                <button type="submit"
                        class="btn btn-dark w-100 mb-2">
                    Ganti Password
                </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kwDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
</body>
</html>
