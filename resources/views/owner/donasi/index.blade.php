@extends('owner.dashboard')
@section('isi')
@if (session('success'))
    <div class="alert alert-success mt-3 mx-3">
        {{ session('success') }}
    </div>
@endif
@if ($errors->has('error'))
    <div class="alert alert-danger mt-3 mx-3">
        {{ $errors->first('error') }}
    </div>
@endif
<div class="container py-4">
    <h4><strong>Request Donasi</strong></h4>
    <table class="table table-bordered text-center">
        <thead class="table-dark"><tr><th>ID</th><th>Organisasi</th><th>Alamat</th><th>Request</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        @foreach($requests as $r)
            <tr>
                <td>ORG{{ $r->id_request }}</td>
                <td>{{ $r->organisasi->nama_organisasi }}</td>
                <td>{{ $r->organisasi->alamat }}</td>
                <td>{{ $r->barang_dibutuhkan }}</td>
                <td>{{ $r->status_request }}</td>
                <td>
                    <form method="POST" action="{{ route('owner.donasi.reject') }}" onsubmit="return confirm('Yakin tolak request ini?')">
                        @csrf
                        <input type="hidden" name="id_request" value="{{ $r->id_request }}">
                        <button class="btn btn-sm btn-danger">Tolak</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <h4 class="mt-5"><strong>Alokasikan Barang ke Permintaan Donasi</strong></h4>
    <form method="POST" action="{{ route('owner.donasi.allocate') }}" style="max-width: 500px;">
        @csrf
        <select name="id_barang" class="form-control mb-2 w-100" required>
            <option value="">Pilih Barang</option>
            @foreach($barangSiapDonasi as $b)
                <option value="{{ $b->id_barang }}">{{ $b->nama_barang }}</option>
            @endforeach
        </select>

        <select name="id_request" class="form-control mb-2 w-100" required>
            <option value="">Pilih Request Donasi</option>
            @foreach($requests as $r)
                <option value="{{ $r->id_request }}">{{ $r->organisasi->nama_organisasi }} - {{ $r->barang_dibutuhkan }}</option>
            @endforeach
        </select>

        <input type="text" name="penerima" class="form-control mb-2 w-100" placeholder="Nama Penerima" required>
        <input type="datetime-local" name="tanggal_donasi" class="form-control mb-2 w-100" value="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}" required>
        <button class="btn btn-success w-45">Donasikan</button>
    </form>

    <h4 class="mt-5"><strong>Histori Donasi</strong></h4>
    <table class="table table-striped text-center">
        <thead class="table-dark"><tr><th>Kode Barang</th><th>Barang</th><th>Organisasi</th><th>Penerima</th><th>Tanggal</th></tr></thead>
        <tbody>
        @foreach($donasiHistori as $d)
            <tr>
                <td>{{ strtoupper(substr($d->barang_titipan->nama_barang ?? '-', 0, 1)) }}{{ $d->barang_titipan->id_barang ?? '-' }}</td>
                <td>{{ $d->barang_titipan->nama_barang ?? '-' }}</td>
                <td>{{ $d->request_donasi->organisasi->nama_organisasi ?? '-' }}</td>
                <td>{{ $d->penerima }}</td>
                <td>{{ $d->tanggal_donasi->format('d M Y') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
