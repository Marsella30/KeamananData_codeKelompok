@extends('owner.dashboard')
@section('isi')
<div class="container py-4">
    <h4 class="mb-4"><strong>Histori Donasi - {{ $organisasi->nama_organisasi }}</strong></h4>
    <table class="table table-bordered text-center">
        <thead class="table-dark"><tr><th>Kode Barang</th><th>Barang</th><th>Tanggal Donasi</th><th>Penerima</th></tr></thead>
        <tbody>
        @forelse($donasiHistori as $d)
            <tr>
                <td>{{ strtoupper(substr($d->barang_titipan->nama_barang ?? '-', 0, 1)) }}{{ $d->barang_titipan->id_barang ?? '-' }}</td>
                <td>{{ $d->barang_titipan->nama_barang ?? '-' }}</td>
                <td>{{ $d->tanggal_donasi->format('d M Y') }}</td>
                <td>{{ $d->penerima }}</td>
            </tr>
        @empty
            <tr><td colspan="3">Belum ada donasi</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
