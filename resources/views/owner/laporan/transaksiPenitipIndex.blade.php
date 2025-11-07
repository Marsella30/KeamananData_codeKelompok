@extends('owner.dashboard')
@section('isi')
<div class="container py-4">
    <h3 class="text-center"><strong>Laporan Transaksi Semua Penitip</strong></h3>

    <div class="mb-3">
        <form class="row g-2 mb-3" method="GET" action="{{ route('owner.laporan.transaksipenitip') }}">
            <div class="col-auto">
                <select name="month" class="form-select form-select-sm">
                    @foreach(range(1,12) as $m)
                        @php
                            $monthName = \Carbon\Carbon::create()->month($m)->locale('id')->isoFormat('MMMM');
                        @endphp
                        <option value="{{ $m }}" {{ request('month', date('n')) == $m ? 'selected' : '' }}>
                            {{ ucfirst($monthName) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <input type="number" name="year" class="form-control form-control-sm" 
                    value="{{ request('year', date('Y')) }}" min="2000" max="2099">
            </div>
            <div class="col-auto">
                <button class="btn btn-sm btn-success">Tampilkan</button>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-sm text-center">
            <thead class="table-dark">
                <tr>
                    <th>ID Penitip</th>
                    <th>Nama Penitip</th>
                    <th>Harga Jual Bersih</th>
                    <th>Bonus Terjual Cepat</th>
                    <th>Pendapatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekap as $row)
                    <tr>
                        <td>{{ $row['id_penitip'] }}</td>
                        <td>{{ $row['nama_penitip'] }}</td>
                        <td>{{ number_format($row['harga_jual_bersih'], 0, ',', '.') }}</td>
                        <td>{{ number_format($row['bonus_terjual_cepat'], 0, ',', '.') }}</td>
                        <td>{{ number_format($row['pendapatan'], 0, ',', '.') }}</td>
                        <td>
                            <a href="{{ route('owner.laporan.transaksipenitip.download', [
                                'id_penitip' => $row['id_penitip'],
                                'month' => request('month', now()->month),
                                'year' => request('year', now()->year)
                            ]) }}" class="btn btn-sm btn-danger">
                                <i class="bi bi-file-earmark-pdf"></i> Download PDF
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">Tidak ada data transaksi di bulan ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Navigasi Halaman --}}
    <div class="d-flex justify-content-center mt-4">
        {{ $penitips->appends(request()->query())->links() }}
    </div>
</div>
@endsection
