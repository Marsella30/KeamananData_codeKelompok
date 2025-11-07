@extends('owner.dashboard')
@section('isi')
<div class="container py-4">
    <h3 class="text-center mb-5"><strong>Laporan Komisi Bulanan per Produk</strong></h3>

    <div class="d-flex justify-content-between align-items-center mb-3">
    {{-- Filter bulan & tahun --}}
    <form class="row g-2 mb-3" method="GET" action="{{ route('owner.laporan.komisi') }}">
      <div class="col-auto">
        <select name="month" class="form-select form-select-sm">
            @foreach(range(1,12) as $m)
                @php
                    // gunakan createFromDate(YYYY, MM, 1)
                    $name = \Carbon\Carbon::createFromDate($year, $m, 1)
                            ->locale('id')
                            ->isoFormat('MMMM');
                @endphp
                <option value="{{ $m }}" {{ request('month', date('n')) == $m ? 'selected' : '' }}>
                    {{ ucfirst($name) }}
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

    
        <!-- <div>
            <p class="mb-0">
                <strong>Bulan:</strong> {{ $monthName }} | <strong>Tahun:</strong> {{ $year }}
                <strong>Tanggal Cetak:</strong> {{ $tanggalCetak }}</p>
            </p>
        </div> -->
        <div>
            <a href="{{ route('owner.laporan.komisi.download', ['month' => request('month'), 'year' => request('year')]) }}"
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
            <th>Harga Jual</th>
            <th>Tanggal Masuk</th>
            <th>Tanggal Laku</th>
            <!-- <th>Komisi Kotor<br>(20% / 30%)</th> -->
            <th>Komisi Hunter</th>
            <th>Komisi ReUseMart</th>
            <th>Komisi Penitip</th>
            <th>Bonus Penitip</th>
          </tr>
        </thead>
        <tbody>
          @forelse($data as $row)
            <tr>
              <td class="text-center">{{ strtoupper(substr($row['nama'], 0, 1)) . $row['kode'] }}</td>
              <td class="text-start">{{ $row['nama'] }}</td>
              <td>{{ number_format($row['harga'],0,',','.') }}</td>
              <td>{{ $row['tanggal_masuk'] }}</td>
              <td>{{ $row['tanggal_laku'] }}</td>
              <td>{{ number_format($row['komisi_hunter'],0,',','.') }}</td>
              <td>{{ number_format($row['komisi_reuse'],0,',','.') }}</td>
              <td>{{ number_format($row['komisi_penitip'],0,',','.') }}</td>
              <td>{{ number_format($row['bonus_penitip'],0,',','.') }}</td>
            </tr>
          @empty
            <tr><td colspan="8">Tidak ada penjualan di bulan ini.</td></tr>
          @endforelse
        </tbody>
            <tfoot>
                <tr class="fw-bold">
                    {{-- Dua kolom pertama jadi label “Total” --}}
                    <td colspan="2">Total</td>

                    {{-- Total Harga Jual --}}
                    <td>{{ number_format(collect($data)->sum('harga'), 0, ',', '.') }}</td>

                    <td colspan="2" style="border-top: 1px solid #000; border-left: none; border-right: none;"></td>

                    {{-- Total Komisi Hunter --}}
                    <td>{{ number_format(collect($data)->sum('komisi_hunter'), 0, ',', '.') }}</td>

                    {{-- Total Komisi ReUseMart --}}
                    <td>{{ number_format(collect($data)->sum('komisi_reuse'), 0, ',', '.') }}</td>
                    
                    {{-- Total Komisi Penitip --}}
                    <td>{{ number_format(collect($data)->sum('komisi_penitip'), 0, ',', '.') }}</td>

                    {{-- Total Bonus Penitip --}}
                    <td>{{ number_format(collect($data)->sum('bonus_penitip'), 0, ',', '.') }}</td>
                </tr>
            </tfoot>
      </table>
    </div>
</div>
@endsection
