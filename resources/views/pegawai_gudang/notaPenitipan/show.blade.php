@extends('pegawai_gudang.dashboard')

@section('isi')
<div class="container mt-4 mb-3">
  <div class="card shadow">
    <div class="card-body">
      <!-- <h4 class="card-title"><strong>Detail Nota: {{ $nota->no_nota }}</strong></h4> -->
      <hr>
      <div class="mb-3">
        <strong>Penitip:</strong> T{{ $nota->penitip->id_penitip }} – {{ $nota->penitip->nama_penitip }}<br>
        <strong>Tanggal Penitipan:</strong> 
          {{ \Carbon\Carbon::parse($nota->tanggal_penitipan)->format('d/m/Y H:i') }}<br>
        <strong>Masa Berakhir:</strong> 
          {{ \Carbon\Carbon::parse($nota->masa_berakhir)->format('d/m/Y') }}<br>
        <strong>QC Oleh:</strong> 
          P{{ $nota->pegawaiQc->id_pegawai }} – {{ $nota->pegawaiQc->nama_pegawai }}
      </div>

      <hr>

      <h5 class="mt-3">Daftar Barang di Nota {{ $nota->no_nota }}:</h5>
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead class="table-light">
            <tr>
              <th class="text-center">Kode Barang</th>
              <th class="text-center">Nama Barang</th>
              <th class="text-center">Harga Jual</th>
              <th class="text-center">Kategori</th>
              <th class="text-center">Garansi</th>
              <th class="text-center">Berat</th>
              <th class="text-center">Status</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($nota->barangTitipan as $barang)
              <tr>
                <td class="text-center">
                  {{ strtoupper(substr($barang->nama_barang, 0, 1)) . $barang->id_barang }}
                </td>
                <td>{{ $barang->nama_barang }}</td>
                <td class="text-center">
                  Rp{{ number_format($barang->harga_jual, 0, ',', '.') }}
                </td>
                <td class="text-center">
                  {{ $barang->kategori->nama_kategori }}
                </td>
                <td class="item-extra">
                  @if($barang->garansi && $barang->tanggal_garansi)
                    Garansi ON {{ \Carbon\Carbon::parse($barang->tanggal_garansi)->format('M Y') }}
                  @endif
                </td>
                <td class="text-center">
                  {{ $barang->berat }}
                </td>
                <td class="text-center">{{ $barang->status_barang }}</td>
                <td class="text-center">
                  <a href="{{ route('pegawai_gudang.barangTitipan.showDetail', $barang->id_barang) }}" class="btn btn-sm btn-info">
                      <i class="bi bi-eye"></i>
                  </a>
                </td>
                <!-- <td class="text-center">
                        <a href="{{ route('pegawai_gudang.barangTitipan.edit', $barang->id_barang) }}" class="btn bi-pencil-square btn-warning" style="font-size: 16px; "></a>
                </td> -->
              </tr>
            @endforeach

            @if($nota->barangTitipan->isEmpty())
              <tr>
                <td colspan="5" class="text-center">Belum ada barang di nota ini.</td>
              </tr>
            @endif
          </tbody>
        </table>
      </div>

      <div class="mt-4 d-flex justify-content-between mt-4">
        <a href="{{ route('pegawai_gudang.notaPenitipan.index') }}" class="btn btn-secondary me-2">
          ← Kembali
        </a>
        <a href="{{ route('pegawai_gudang.notaPenitipan.print', $nota->id_nota) }}" 
           class="btn btn-outline-danger btn-sm"><i class="bi bi-download me-2"></i>PDF
        </a>
      </div>
    </div>
  </div>
</div>
@endsection
