@extends('owner.dashboard')

@section('isi')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<style>
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
    }
    th {
        background-color: #f8f9fa;
    }
    .container-fluid {
        max-width: 1065px;
        margin: auto;
    }
</style>

<div class="container-fluid mt-2">
    <h3 class="mb-4 text-center"><strong>Laporan Penjualan Per Kategori Tahun {{ $tahun }}</strong></h3>

    <form method="GET" action="{{ route('owner.laporan.penjualanPerKategori') }}" class="form-inline mb-4">
        <label for="year-select" class="mr-2">Pilih Tahun:</label>
        <select id="year-select" name="tahun" class="form-control form-control-sm mr-2"
                onchange="this.form.submit()">
            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>
                    {{ $y }}
                </option>
            @endfor
        </select>
        <a href="{{ route('owner.laporan.penjualanPerKategori-pdf', ['tahun' => $tahun]) }}" class="btn btn-sm btn-danger">
            <i class="bi bi-file-earmark-pdf"></i> Unduh PDF
        </a>
    </form>

    <table class="table table-bordered text-center">
        <thead class="table-dark text-center">
            <tr>
                <th>Kategori</th>
                <th>Jumlah Item Terjual</th>
                <th>Jumlah Item Gagal Terjual</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalTerjual = 0;
                $totalGagal = 0;
            @endphp
            @forelse($laporan as $item)
                <tr>
                    <td>{{ $item->nama_kategori }}</td>
                    <td class="text-center">{{ $item->jumlah_terjual }}</td>
                    <td class="text-center">{{ $item->jumlah_gagal +  $item->jumlah_donasi}}</td>
                </tr>
                @php
                    $totalTerjual += $item->jumlah_terjual;
                    $totalGagal += ($item->jumlah_gagal + $item->jumlah_donasi);
                @endphp
            @empty
                <tr>
                    <td colspan="3" class="text-center">Tidak ada data penjualan untuk tahun ini.</td>
                </tr>
            @endforelse
            <tr class="fw-bold">
                <td>Total</td>
                <td class="text-center">{{ $totalTerjual }}</td>
                <td class="text-center">{{ $totalGagal }}</td>
            </tr>
        </tbody>
    </table>
</div>

@endsection
