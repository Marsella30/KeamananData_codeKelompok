<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Penarikan Saldo</title>

  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

  <style>
    body {
      background-color: #f5f8f2; /* Hijau very light background */
      color: #4a6f28; /* Hijau tua untuk teks */
    }

    .card {
      background-color: #e7f0da; /* Hijau muda lembut */
      border: 1px solid #6f8e3a; /* Hijau sedang */
      box-shadow: 0 4px 6px rgba(111, 143, 70, 0.3);
    }

    h3 {
      color: #6f8e3a;
      font-weight: 700;
    }

    .detail-barang {
      background-color: #ffffff;
      border: 1px solid #a8c165;
      border-radius: 8px;
      padding: 1rem;
      margin-bottom: 1.5rem;
      color: #3d5229;
    }

    .detail-barang h5 {
      color: #4a6f28;
      margin-bottom: 1rem;
      font-weight: 700;
    }

    .detail-barang p{
        margin-bottom: 0.5rem; /* jarak antar baris sekitar 8px */
    }

    /* Styling bintang rating hijau */
    .rating {
      direction: rtl;
      unicode-bidi: bidi-override;
      font-size: 2.2rem;
      display: inline-flex;
    }
    .rating input[type="radio"] {
      display: none;
    }
    .rating label {
        color: transparent; /* atau #eee; */
        cursor: pointer;
        padding: 0 6px;
        transition: color 0.3s ease;
        font-family: Arial, sans-serif;
        text-shadow: 0 0 0 #ccc; /* agar bentuk bintang tetap terlihat samar */
        }
    .rating label:hover,
    .rating label:hover ~ label,
    .rating input[type="radio"]:checked ~ label {
      color: #ffc107; /* kuning cerah */
        text-shadow: 0 0 5px #ffde59;
    }

    /* Tombol */
    button.btn-primary {
      background-color: #6f8e3a;
      border-color: #6f8e3a;
      font-weight: 600;
    }
    button.btn-primary:hover {
      background-color: #56742a;
      border-color: #56742a;
    }

    a.btn-secondary {
      background-color: #a8c165;
      border-color: #a8c165;
      color: #2d3b12;
      font-weight: 600;
    }
    a.btn-secondary:hover {
      background-color: #8baa3a;
      border-color: #8baa3a;
      color: white;
      text-decoration: none;
    }

    /* Foto barang */
    .foto-barang img {
      max-width: 120px; 
      max-height: 120px; 
      object-fit: cover; 
      border-radius: 6px; 
      border: 1px solid #a8c165;
      box-shadow: 0 0 8px rgba(111, 143, 70, 0.4);
    }

  </style>
</head>
<body>
  <div class="container py-5">
    <div class="card shadow p-4 mx-auto" style="max-width: 600px;">
      <h3 class="mb-4 text-center">Penarikan Saldo</h3>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form id="tarikForm" action="{{ route('penitip.prosesTarikSaldo') }}" method="POST">
            @csrf
            <div class="row g-3">
            <div class="col-md-6">
                <label>ID</label>
                <input type="text" class="form-control" value="T{{ $penitip->id_penitip }}" disabled>
            </div>
            <div class="col-md-6">
                <label>Nama</label>
                <input type="text" class="form-control" value="{{ $penitip->nama_penitip }}" disabled>
            </div>
            <div class="col-md-6">
                <label>Saldo Penitip</label>
                <input type="text" class="form-control" value="{{ $penitip->saldo_penitip }}" disabled>
            </div>
            <div class="col-md-6">
                <label>Nominal Penarikan Saldo</label>
                <input type="number" name="nominal_tarik" id="nominal_tarik" placeholder="Masukkan nominal tarik" required>
            </div>
        </div>

        <div class="d-flex justify-content-center gap-3 mt-5">
            <a href="{{ route('penitip.profil') }}" class="btn btn-secondary px-4">Batal</a>
            <button type="button" class="btn btn-primary" onclick="konfirmasiPenarikan()">Tarik Saldo</button>
        </div>
    </form>
    

  <!-- Bootstrap JS CDN -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
        function konfirmasiPenarikan() {
            const nominal = parseFloat(document.getElementById('nominal_tarik').value);
            if (isNaN(nominal) || nominal <= 0) {
                alert("Masukkan nominal yang valid.");
                return;
            }

            const biaya = nominal * 0.05;
            const total = nominal + biaya;
            const saldo = {{ $penitip->saldo_penitip }};

            if (total > saldo) {
                alert("Saldo Anda tidak mencukupi.");
                return;
            }

            const sisa = saldo - total;

            if (confirm(
                `Yakin ingin menarik Rp${nominal.toLocaleString()}?\n` +
                `Biaya penarikan (5%): Rp${biaya.toLocaleString()}\n` +
                `Sisa saldo setelah penarikan: Rp${sisa.toLocaleString()}`
            )) {
                document.getElementById('tarikForm').submit();
            }
        }
    </script>
</body>
</html>

<!-- @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<form id="tarikForm" method="POST" action="{{ route('penitip.tarikSaldo') }}">
    @csrf
    <label>Saldo Anda: Rp{{ number_format($penitip->saldo_penitip, 0, ',', '.') }}</label><br>
    <input type="number" name="nominal_tarik" id="nominal_tarik" placeholder="Masukkan nominal tarik" required>
    <button type="button" onclick="konfirmasiPenarikan()">Tarik</button>
</form> -->



