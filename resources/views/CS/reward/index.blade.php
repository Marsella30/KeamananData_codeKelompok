@extends('CS.dashboard')

@section('isi')
<div class="container">
    <h2 class="mb-5 mt-3 text-center"><strong>Daftar Klaim Merchandise</strong></h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="container mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <form method="GET" action="{{ route('cs.reward.index') }}" class="form-inline">
                <label for="filter-select" class="mr-2">Filter:</label>
                <select 
                    id="filter-select" 
                    name="filter" 
                    class="form-control form-control-sm" 
                    onchange="this.form.submit()"
                >
                    <option value="semua" {{ $filter === 'semua' ? 'selected' : '' }}>
                        Semua
                    </option>
                    <option value="belum" {{ $filter === 'belum' ? 'selected' : '' }}>
                        Belum Diambil
                    </option>
                </select>
            </form>

            <form class="d-flex" action="{{ route('cs.reward.index') }}" method="GET">
                <input 
                    type="search" 
                    name="search" 
                    class="form-control me-2" 
                    placeholder="Cari Klaim Merch..." 
                    value="{{ request('search') }}" 
                    aria-label="Search"
                    style="width: 250px;"
                >
                <input type="date" name="tanggal_klaim" class="form-control me-2"  value="{{ request('tanggal_klaim') }}">
                <button class="btn btn-outline-dark" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </div>
    </div>

    {{-- Tabel daftar klaim --}}
    <table class="table table-bordered table-striped table-sm align-middle">
        <thead class="table-dark text-center">
            <tr>
                <th>No</th>
                <th>Pembeli</th>
                <th>Merchandise</th>
                <th>Tanggal Klaim</th>
                <th>Status</th>
                <th>Tanggal Ambil</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rewards as $reward)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $reward->pembeli->nama_pembeli }}</td>
                    <td class="text-center">{{ $reward->merchandise->nama_merchandise }}</td>
                    <td class="text-center">{{ $reward->tanggal_klaim->format('d-m-Y') }}</td>
                    <td class="text-center">
                        @if($reward->status_penukaran)
                            <span class="badge badge-success">Sudah Diambil</span>
                        @else
                            <span class="badge badge-warning">Belum diambil</span>
                        @endif
                    </td>
                    <td class="text-center">
                        {{ $reward->tanggal_ambil ? $reward->tanggal_ambil->format('d-m-Y') : '-' }}
                    </td>
                    <td class="text-center">
                        @if(!$reward->status_penukaran)
                            {{-- Form untuk menandai diambil --}}
                            <form action="{{ route('cs.reward.ambilMerch', $reward->id_reward) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary"
                                        onclick="return confirm('Yakin ingin menandai sebagai sudah diambil?')">
                                    Tandai Diambil
                                </button>
                            </form>
                        @else
                            <button class="btn btn-sm btn-secondary" disabled>—
                            </button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Klaim Merchandise Tidak Ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $rewards->appends(['search' => request('search'), 'filter' => request('filter'), 'date' => request('date')])->links() }}
  </div>
</div>
@endsection
