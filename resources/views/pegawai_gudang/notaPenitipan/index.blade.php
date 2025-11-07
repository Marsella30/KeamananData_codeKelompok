@extends('pegawai_gudang.dashboard')

@section('isi')
<div class="container mt-2">
    <h2 class="mb-4 text-center"><strong>Daftar Penitipan</strong></h2>

    {{-- Flash message --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="container d-flex justify-content-between align-items-center">
        <form class="d-flex mb-3" action="{{ route('pegawai_gudang.notaPenitipan.index') }}" method="GET">
            <input class="form-control me-2" 
                type="search" 
                name="search" 
                placeholder="Cari..." 
                value="{{ request('search') }}"
                aria-label="Search" 
                style="width: 250px;">
            <input class="form-control me-2" type="date" name="date" value="{{ request('date') }}">
            <button class="btn btn-outline-dark" type="submit">
                <i class="bi bi-search"></i>
            </button>
        </form>

        <a href="{{ route('pegawai_gudang.barangTitipan.createBlank') }}" class="btn btn-success mb-3">+ Tambah Penitipan</a>
    </div>

  <table class="table table-bordered table-striped">
    <thead class="table-dark text-center">
      <tr>
        <th>No Nota</th>
        <th>Penitip</th>
        <th>Tanggal Penitipan</th>
        <th>Jumlah Barang</th>
        <th class="text-center">Aksi</th>
      </tr>
    </thead>
    <tbody>
      @forelse($notas as $nota)
      <tr>
        <td class="text-center">{{ $nota->no_nota }}</td>
        <td>T{{ $nota->penitip->id_penitip }} – {{ $nota->penitip->nama_penitip }}</td>
        <td>{{ \Carbon\Carbon::parse($nota->tanggal_penitipan)->format('d/m/Y H:i') }}</td>
        <td class="text-center">{{ $nota->barang_titipan_count }}</td>
        <td class="text-center">
          <!-- Lihat Detail (daftar barang) -->
          <a href="{{ route('pegawai_gudang.notaPenitipan.show', $nota->id_nota) }}"
            class="btn btn-info btn-sm">
            <i class="bi bi-eye"></i>
          </a>
          <!-- Cetak PDF -->
          <a href="{{ route('pegawai_gudang.notaPenitipan.print', $nota->id_nota) }}"
             class="btn btn-danger btn-sm"><i class="fas fa-print"></i></a>

          <!-- Tambah Barang -->
          <!-- <a href="{{ route('pegawai_gudang.barangTitipan.create', $nota->id_nota) }}"
             class="btn btn-success btn-sm"><i class="fas fa-plus"></i></a> -->
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="5" class="text-center">Transaksi Tidak Ditemukan.</td>
      </tr>
      @endforelse
    </tbody>
  </table>

  <div class="d-flex justify-content-center">
    {{ $notas->appends(['search'=>request('search')])->links() }}
  </div>
</div>
@endsection