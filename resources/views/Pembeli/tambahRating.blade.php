<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Beri Rating Produk</title>

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
      <h3 class="mb-4 text-center">Beri Rating Produk</h3>

      {{-- Foto Barang --}}
      <div class="d-flex flex-wrap gap-2 mb-3 justify-content-center foto-barang">
        @foreach ($barang->fotoBarang as $foto)
            <img 
                src="{{ asset('images/barang/' . $foto->nama_file) }}" 
                alt="Foto {{ $barang->nama_barang }}" 
            >
        @endforeach
      </div>

      {{-- Detail Barang --}}
      <div class="detail-barang">
        <h5>{{ $barang->nama_barang }}</h5>
        <p><strong>Kode:</strong> B{{ $barang->id_barang }}</p>
        <p><strong>Penitip:</strong> {{ $barang->penitip->nama_penitip ?? '-' }}</p>
        <p><strong>Harga:</strong> Rp{{ number_format($barang->harga_jual, 0, ',', '.') }}</p>
        <p><strong>Deskripsi:</strong> {{ $barang->deskripsi }}</p>
      </div>

      {{-- Form Rating --}}
      <form id="ratingForm" action="{{ route('pembeli.storeRating') }}" method="POST">
        @csrf
        <input type="hidden" name="id_barang" value="{{ $barang->id_barang }}">

        <div class="mb-3 text-center">
          <label class="form-label d-block mb-2">Rating (1–5):</label>
          <div class="rating mx-auto">
            @for ($i = 5; $i >= 1; $i--)
              <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" required
                {{ (old('rating', $existing->rating ?? '') == $i) ? 'checked' : '' }} />
              <label for="star{{ $i }}" title="{{ $i }} bintang">&#9733;</label>
            @endfor
          </div>
          @error('rating')
            <div class="text-danger mt-1">{{ $message }}</div>
          @enderror
        </div>
        <div class="d-flex justify-content-center gap-3">
            <a href="{{ route('pembeli.riwayatTransaksi') }}" class="btn btn-secondary px-4">Batal</a>
            <button type="submit" class="btn btn-primary px-4">Kirim</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Bootstrap JS CDN -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('ratingForm').addEventListener('submit', function(e) {
            if(!confirm('Apakah Anda yakin ingin mengirim rating ini?')) {
            e.preventDefault(); // batalkan submit jika tidak yakin
            }
        });
    </script>
</body>
</html>
