@extends('owner.dashboard')
@section('isi')
<div class="container py-4">
    <h3 class="text-center"><strong>Laporan Donasi Barang</strong></h3>

    {{-- Filter tahun --}}
    <form class="row g-2 mb-3" method="GET" action="{{ route('owner.laporan.donasi') }}">
        <div class="col-auto">
            <input type="number" name="year" class="form-control form-control-sm" 
                   value="{{ request('year', date('Y')) }}" min="2000" max="2099">
        </div>
        <div class="col-auto">
            <button class="btn btn-sm btn-success">Tampilkan</button>
        </div>
    </form>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <p class="mb-0">
                <strong>Tahun:</strong> {{ $year }}
            </p>
        </div>
        <div>
            <a href="{{ route('owner.laporan.donasi.download', ['year' => request('year')]) }}"
            class="btn btn-sm btn-danger">
                <i class="bi bi-file-earmark-pdf"></i> Unduh PDF
            </a>
        </div>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered table-sm text-center" style="table-layout: fixed;">
        <thead class="table-dark">
          <tr>
            <th>Kode Produk</th>
            <th>Nama Produk</th>
            <th>Nama Penitip</th>
            <th>Tanggal Donasi</th>
            <th>Organisasi</th>
            <th>Penerima</th>
          </tr>
        </thead>
        <tbody>
          @forelse($donations as $row)
            <tr>
                <td>{{ $row->barang_titipan ? strtoupper(substr($row->barang_titipan->nama_barang, 0, 1)) . $row->barang_titipan->id_barang : '-' }}</td>
                <td class="text-start">{{ $row->barang_titipan->nama_barang ?? '-' }}</td>
                <td>{{ $row->barang_titipan->penitip->nama_penitip ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($row->tanggal_donasi)->format('d/m/Y') }}</td>
                <td>{{ $row->request_donasi->organisasi->nama_organisasi ?? '-' }}</td>
                <td>{{ $row->penerima }}</td>
            </tr>
          @empty
            <tr><td colspan="6">Tidak ada data donasi di tahun ini.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    
    <div class="d-flex justify-content-center mt-4">
        {{ $donations->appends(request()->query())->links() }}
    </div>
</div>
@endsection
