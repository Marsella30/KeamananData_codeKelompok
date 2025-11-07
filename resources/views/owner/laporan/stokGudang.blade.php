@extends('owner.dashboard')
@section('isi')
<div class="container py-4">
    <h3 class="text-center mb-5"><strong>Laporan Stok Gudang</strong></h3>

    <div class="d-flex justify-content-between align-items-center mb-2">
        <!-- <p>
            <strong>Tanggal Cetak:</strong>
            {{ \Carbon\Carbon::parse($tanggalCetak)->locale('id')->isoFormat('DD MMMM YYYY') }}
        </p> -->

        <form method="GET" class="mb-2">
            <div class="row g-4">
                <div class="col-md-10">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari...">
                </div>
                <!-- <div class="col-md-4">
                    <select name="sort" class="form-select">
                        <option value="">Urutkan berdasarkan</option>
                        <option value="id_barang" {{ request('sort') == 'id_barang' ? 'selected' : '' }}>Kode Produk</option>
                        <option value="nama_barang" {{ request('sort') == 'nama_barang' ? 'selected' : '' }}>Nama Produk</option>
                        <option value="id_penitip" {{ request('sort') == 'id_penitip' ? 'selected' : '' }}>Id Penitip</option>
                        <option value="tanggal_masuk" {{ request('sort') == 'tanggal_masuk' ? 'selected' : '' }}>Tanggal Masuk</option>
                        <option value="harga_jual" {{ request('sort') == 'harga_jual' ? 'selected' : '' }}>Harga</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="direction" class="form-select">
                        <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Naik</option>
                        <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Turun</option>
                    </select>
                </div> -->
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-info">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </form>

        @if(!$isPdf)
            <a href="{{ route('owner.laporan.stok.download') }}" class="btn btn-danger">
                <i class="bi bi-file-earmark-pdf"></i> Unduh PDF
            </a>
        @endif
    </div>

    @if($isPdf)
        {{-- Jika rendering PDF, jangan muat CSS/JS eksternal --}}
        @push('head')
            {{-- kosongkan atau hanya CSS inline minimal --}}
        @endpush
    @else
        {{-- Jika tampilan web, muat CSS/JS layout seperti biasa --}}
    @endif

    {{-- Tabel Stok --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-sm align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th>Kode Produk</th>
                    <th>Nama Produk</th>
                    <th>Id Penitip</th>
                    <th>Nama Penitip</th>
                    <th>Tanggal Masuk</th>
                    <th>Perpanjangan</th>
                    <th>Id Hunter</th>
                    <th>Nama Hunter</th>
                    <th>Harga</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stokItems as $item)
                    <tr>
                        <td class="text-center">{{ strtoupper(substr($item->nama_barang, 0, 1)) . $item->id_barang }}</td>
                        <td class="text-start">{{ $item->nama_barang }}</td>
                        <td style="text-align: center;">T{{ $item->id_penitip }}</td>
                        <td class="text-start">{{ $item->penitip->nama_penitip ?? '-' }}</td>
                        <td style="text-align: center;">{{ \Carbon\Carbon::parse($item->tanggal_masuk)->format('d/m/Y') }}</td>
                        <td style="text-align: center;">{{ $item->status_perpanjangan ? 'Ya' : 'Tidak' }}</td>
                        <td style="text-align: center;">{{ $item->id_hunter ? 'P' . $item->id_hunter : '-' }}</td>
                        <td class="text-start">{{ $item->hunter->nama_pegawai ?? '-' }}</td>
                        <td style="text-align: center;">{{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9">Belum ada stok barang untuk hari ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Hanya tampilkan pagination bila bukan PDF --}}
    @if(!$isPdf)
        <div class="d-flex justify-content-center">
            {{ $stokItems->links() }}
        </div>
    @endif
</div>
@endsection
