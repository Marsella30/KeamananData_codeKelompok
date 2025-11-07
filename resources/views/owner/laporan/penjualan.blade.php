@extends('owner.dashboard')
@section('isi')
<div class="container py-4">
    <h3 class="mb-4 text-center"><strong>Laporan Penjualan Bulanan {{ $year }}</strong></h3>

    {{-- Pilih Tahun dan Tombol Unduh --}}
    <form method="GET" action="{{ route('owner.laporan.penjualan') }}" class="form-inline mb-4">
        <label for="year-select" class="mr-2">Pilih Tahun:</label>
        <select id="year-select" name="year" class="form-control form-control-sm mr-2"
                onchange="this.form.submit()">
            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                    {{ $y }}
                </option>
            @endfor
        </select>
        <a href="{{ route('owner.laporan.penjualan.download', ['year' => $year]) }}"
           class="btn btn-sm btn-danger">
            <i class="bi bi-file-earmark-pdf"></i> Unduh PDF
        </a>
    </form>

    {{-- Tabel Data --}}
    <div class="table-responsive">
        <table class="table table-bordered text-center">
            <thead class="thead-dark">
                <tr>
                    <th>Bulan</th>
                    <th>Jumlah Barang Terjual</th>
                    <th>Penjualan Penjualan Kotor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dataByMonth as $monthData)
                    <tr>
                        <td>{{ $monthData['month'] }}</td>
                        <td>{{ $monthData['count'] }}</td>
                        <td>{{ number_format($monthData['gross'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Grafik --}}
    <div class="mt-5">
        <canvas id="salesChart" width="100%" height="40"></canvas>
    </div>
</div>

{{-- Sertakan Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('salesChart').getContext('2d');

        const labels = {!! json_encode(array_column($dataByMonth, 'month')) !!};
        const dataCount = {!! json_encode(array_column($dataByMonth, 'count')) !!};
        const dataGross = {!! json_encode(array_column($dataByMonth, 'gross')) !!};

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Jumlah Terjual',
                        data: dataCount,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Penjualan Kotor (Rp)',
                        data: dataGross,
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    });
</script>
@endsection
