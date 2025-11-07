{{-- resources/views/pembeli/riwayatTransaksi.blade.php --}}

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Riwayat Pembelian</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet"
  >
</head>
<body class="bg-light">
  <div class="container py-4">
    {{-- Bungkus seluruh konten dalam sebuah card --}}
    <div class="card shadow-sm">
      <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
          <h4 class="mb-0">Riwayat Pembelian</h4>
          <a href="{{ route('pembeli.profil') }}" class="btn btn-secondary btn-sm">
            ← Kembali ke Profile
          </a>
        </div>
      </div>
      <div class="card-body">
        <form method="GET" action="{{ route('pembeli.riwayatTransaksi') }}" class="mb-4">
          <div class="input-group">
            <input 
              type="text" 
              name="search" 
              class="form-control" 
              placeholder="Cari Riwayat..." 
              value="{{ request('search') }}"
            >
            <button type="submit" class="btn btn-outline-success">Cari</button>
          </div>
        </form>
        @forelse ($transaksiList as $transaksi)
            @php
                $terdapatDiterima = false;
                $statusPengirimanText = 'Belum ada pengiriman';

                if ($transaksi->penjadwalans) {
                  foreach ($transaksi->penjadwalans as $penjadwalan) {
                      if ($penjadwalan && $penjadwalan->pengiriman) {
                          $statusPengirimanText = strtolower(trim($penjadwalan->pengiriman->status_pengiriman));
                          if (in_array($statusPengirimanText, ['diterima', 'sampai'])) {
                              $terdapatDiterima = true;
                              break;
                          }
                      }
                  }
              }
            @endphp
          <div class="card mb-4">
            <div class="card-header" style="background-color: #e7f0da; color: rgba(111, 143, 70, 1);">
              <div class="d-flex justify-content-between">
                <div>
                  <strong>ID Transaksi:</strong> {{ $transaksi->id_transaksi }}<br>
                  <strong>Tanggal:</strong>
                    {{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->format('d/m/Y') }}<br>
                  <strong>Status:</strong> {{ ucfirst($transaksi->status_transaksi) }}<br>
                  <strong>Posisi Barang:</strong> {{ $statusPengirimanText }}
                </div>
                <div class="text-end fw-bold">
                  Total: Rp{{ number_format($transaksi->total_pembayaran, 0, ',', '.') }}
                </div>
              </div>
            </div>
            <div class="card-body p-0">
              <ul class="list-group list-group-flush">
                @foreach ($transaksi->detailTransaksi as $detail)
                  <li class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                      <div>
                        <strong>
                          {{ strtoupper(substr($detail->barang->nama_barang ?? '-', 0, 1)) . ($detail->barang->id_barang ?? '') }}
                        </strong>
                        {{ $detail->barang->nama_barang ?? 'Barang sudah dihapus' }}
                      </div>
                      <div class="d-flex align-items-center">
                        <span class="fw-semibold text-success me-3">
                          Rp{{ number_format(
                            ($detail->barang->harga_jual ?? $detail->sub_total), 0, ',', '.') }}
                        </span>

                        @if($terdapatDiterima)
                          @php
                            $existingRating = $detail->barang->ratingDetail ?? null;
                          @endphp

                          @if(!$existingRating)
                            <a href="{{ route('pembeli.tambahRating', ['id_barang' => $detail->barang->id_barang]) }}" class="btn btn-sm btn-warning">
                                Beri Rating
                            </a>
                          @else
                            <span class="text-success fw-semibold">
                                Rating: {{ number_format($existingRating->rating, 1) }} ★
                            </span>
                          @endif
                        @endif
                      </div>
                    </div>
                  </li>
                @endforeach
              </ul>
            </div>
          </div>
        @empty
          <div class="alert alert-warning">
            Belum ada transaksi yang tercatat.
          </div>
        @endforelse

        <div class="d-flex justify-content-center mt-4">
          {{ $transaksiList->appends(['search' => request('search')])->links() }}
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
  </script>
</body>
</html>
