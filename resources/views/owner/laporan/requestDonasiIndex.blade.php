@extends('owner.dashboard')
@section('isi')
<div class="container py-4">
    <h3 class="text-center"><strong>Laporan Request Donasi (Menunggu)</strong></h3>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <a href="{{ route('owner.laporan.requestdonasi.download', ['year' => request('year')]) }}"
               class="btn btn-sm btn-danger">
                <i class="bi bi-file-earmark-pdf"></i> Unduh PDF
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-sm text-center">
            <thead class="table-dark">
                <tr>
                    <th>ID Request</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>Request</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $row)
                    <tr>
                        <td>{{ 'ORG' . $row->id_request }}</td>
                        <td>{{ optional($row->organisasi)->nama_organisasi ?? '-' }}</td>
                        <td>{{ optional($row->organisasi)->alamat ?? '-' }}</td>
                        <td class="text-start">{{ $row->barang_dibutuhkan }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">Tidak ada data request donasi yang menunggu di tahun ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $requests->appends(request()->query())->links() }}
    </div>
</div>
@endsection
