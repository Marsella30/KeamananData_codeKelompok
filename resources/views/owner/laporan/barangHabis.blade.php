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
        text-align: center;
    }
    th {
        background-color: #f0f0f0;
    }
    .container-fluid {
        max-width: 1065px;
        margin: auto;
    }
</style>

<div class="container-fluid mt-2">
    <h3 class="mb-4 text-center"><strong>Laporan Barang yang Masa Penitipannya Sudah Habis</strong></h3>

    <form method="GET" action="{{ route('owner.laporan.barangHabis') }}" class="form-inline mb-4">
        <a href="{{ route('owner.laporan.barangHabisPdf', ['tahun' => $tahun]) }}"
           class="btn btn-sm btn-danger">
            <i class="bi bi-file-earmark-pdf"></i> Unduh PDF
        </a>
    </form>

    <table>
        <thead>
            <tr>
                <th>Kode Produk</th>
                <th>Nama Produk</th>
                <th>Id Penitip</th>
                <th>Nama Penitip</th>
                <th>Tanggal Masuk</th>
                <th>Tanggal Akhir</th>
                <th>Batas Ambil</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporan as $item)
                <tr>
                    <td class="text-center">{{ strtoupper(substr($item->nama_barang, 0, 1)) . $item->id_barang }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ 'T' . $item->id_penitip }}</td>
                    <td>{{ optional($item->penitip)->nama_penitip ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_masuk)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_akhir)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_akhir)->addDays(7)->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Tidak ada data barang yang masa penitipannya sudah habis.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $laporan->links() }}
    </div>
</div>

@endsection
