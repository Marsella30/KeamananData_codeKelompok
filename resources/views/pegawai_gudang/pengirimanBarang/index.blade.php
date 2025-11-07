@extends('pegawai_gudang.dashboard')

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
        white-space: nowrap;
    }
    th {
        background-color: #f8f9fa;
    }
    .container-fluid {
        max-width: 1065px;
        margin: auto;
    }
    .btn-action {
        font-size: 14px;
        padding: 6px 12px;
    }
    .table-sm th,
    .table-sm td {
        padding: 0.45rem 0.5rem;
        font-size: 0.875rem;
        vertical-align: middle;
        white-space: nowrap;
    }

    .table-sm {
        table-layout: auto;
        width: 100%;
    }
</style>

<div class="container-fluid mt-2">
    <h3 class="mb-4 text-center"><strong>Daftar Transaksi Pengiriman / Pengambilan</strong></h3>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="container d-flex justify-content-between align-items-center">
        <form class="d-flex mb-3" action="{{ route('pegawai_gudang.pengiriman.index') }}" method="GET">
            <input class="form-control me-2" 
                type="search" 
                name="search" 
                placeholder="Cari transaksi barang..." 
                value="{{ request('search') }}"
                aria-label="Search" 
                style="width: 250px;">

            <input class="form-control me-2" type="date" name="date" value="{{ request('date') }}">

            <select name="status_pengiriman" class="form-select me-2" style="width: 200px;">
                <option value="" disabled selected>Status</option>
                <option value="Diterima">Diterima</option>
                <option value="Dibatalkan">Dibatalkan</option>
                <option value="Sampai">Sampai</option>
                <option value="Disiapkan">Disiapkan</option>
                <option value="Diantar">Diantar</option>
                <option value="Belum Disiapkan">Belum Disiapkan</option>
            </select>

            <button class="btn btn-outline-dark" type="submit">
                <i class="bi bi-search"></i>
            </button>
        </form>
    </div>
    
    <table class="table table-bordered table-striped table-sm align-middle">
        <thead class="table-dark text-center">
            <tr>
                <th class="text-center">Nomor Transaksi</th>
                <th class="text-center">Nama Pembeli</th>
                <th class="text-center">Tanggal Transaksi</th>
                <th class="text-center">Tanggal Jadwal</th>
                <th class="text-center">Jenis Pengiriman</th>
                <!-- <th>Status Jadwal</th> -->
                <th class="text-center">Status Pengiriman</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transaksi as $item)
                <tr>
                    <td class="text-center">{{ $item->nomor_transaksi }}</td>
                    <td>{{ $item->pembeli->nama_pembeli ?? '-' }}</td>
                    <td>
                        @if ($item->tanggal_transaksi)
                            {{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('d/m/Y H:i') }}
                        @else
                            <em style="color: #888;">-</em>
                        @endif
                    </td>
                    @php
                        $jadwal = $item->penjadwalan->firstWhere('jenis_jadwal', 'Pengiriman') 
                            ?? $item->penjadwalan->firstWhere('jenis_jadwal', 'Diambil');
                    @endphp
                    <td>
                        @if ($jadwal && $jadwal->tanggal_jadwal)
                            {{ \Carbon\Carbon::parse($jadwal->tanggal_jadwal)->format('d/m/Y H:i') }}
                        @else
                            <em style="color: #888;">Belum ditentukan</em>
                        @endif
                    </td>
                    <td>{{ $jadwal->jenis_jadwal ?? '-' }}</td>
                    <!-- <td>{{ $jadwal->status_jadwal ?? '-' }}</td> -->
                    <td class="text-center">
                        @if ($jadwal && $jadwal->pengiriman && $jadwal->pengiriman->status_pengiriman)
                            @php
                                $status = $jadwal->pengiriman->status_pengiriman;
                                $badgeClass = match($status) {
                                    'Diterima' => 'badge bg-success',
                                    'Dibatalkan' => 'badge bg-danger',
                                    'Sampai' => 'badge bg-primary',
                                    'Disiapkan' => 'badge bg-warning text-dark',
                                    'Diantar' => 'badge bg-info text-dark',
                                    default => 'badge bg-secondary',
                                };
                            @endphp
                            <span class="{{ $badgeClass }}">{{ $status }}</span>
                        @else
                            <em style="color: #888;">Belum Disiapkan</em>
                        @endif
                    </td>
                    <td class="text-center">
                        @if (!$jadwal || !$jadwal->tanggal_jadwal)
                            @if ($jadwal && $jadwal->jenis_jadwal === 'Pengiriman')
                                <!-- Pengiriman -->
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#jadwalModal{{$item->id_transaksi}}">
                                    Pilih Jadwal
                                </button>

                                <!-- Modal -->
                                <div class="modal fade" id="jadwalModal{{$item->id_transaksi}}" tabindex="-1" aria-labelledby="jadwalModalLabel{{$item->id_transaksi}}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('pegawai_gudang.pengiriman.tambahJadwal') }}" method="POST" onsubmit="return confirm('Yakin ingin menyimpan jadwal ini?');">
                                                @csrf
                                                <input type="hidden" name="id_transaksi" value="{{ $item->id_transaksi }}">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Pilih Jadwal & Kurir</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="tanggal_jadwal" class="form-label">Tanggal Jadwal</label>
                                                        <input type="datetime-local" name="tanggal_jadwal" class="form-control" required min="{{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('Y-m-d') }}T{{ (\Carbon\Carbon::parse($item->tanggal_transaksi)->hour >= 16 ? '00:00' : '00:00') }}">
                                                        <small class="text-muted">Jika transaksi setelah jam 16:00, jadwal tidak boleh di hari yang sama.</small>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="id_kurir" class="form-label">Pilih Kurir</label>
                                                        <select name="id_kurir" class="form-select" required>
                                                            <option value="">-- Pilih Kurir --</option>
                                                            @foreach ($kurir as $k)
                                                                <option value="{{ $k->id_pegawai }}">{{ $k->nama_pegawai }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @elseif ($jadwal && $jadwal->jenis_jadwal === 'Diambil')
                                <!-- Diambil -->
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#jadwalModal{{$item->id_transaksi}}">
                                    Pilih Jadwal
                                </button>

                                <!-- Modal -->
                                <div class="modal fade" id="jadwalModal{{$item->id_transaksi}}" tabindex="-1" aria-labelledby="jadwalModalLabel{{$item->id_transaksi}}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('pegawai_gudang.pengiriman.tambahJadwal') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="id_transaksi" value="{{ $item->id_transaksi }}">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Pilih Jadwal</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="tanggal_jadwal" class="form-label">Tanggal Jadwal</label>
                                                        <input type="datetime-local" name="tanggal_jadwal" class="form-control" required min="{{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('Y-m-d') }}T{{ (\Carbon\Carbon::parse($item->tanggal_transaksi)->hour >= 16 ? '00:00' : '00:00') }}">
                                                        <small class="text-muted">Jika transaksi setelah jam 16:00, jadwal tidak boleh di hari yang sama.</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @else
                            @if ($jadwal && $jadwal->pengiriman && $jadwal->pengiriman->status_pengiriman)
                                @php
                                    $status = $jadwal->pengiriman->status_pengiriman;
                                    $showConfirmButton = 
                                        ($jadwal->jenis_jadwal === 'Pengiriman' && $status === 'Diantar') ||
                                        ($jadwal->jenis_jadwal === 'Diambil' && $status === 'Disiapkan');
                                @endphp

                                <div class="modal fade" id="modalKonfirmasi{{ $jadwal->id_jadwal }}" tabindex="-1" aria-labelledby="modalLabel{{ $jadwal->id_jadwal }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                    <form action="{{ route('pegawai_gudang.pengiriman.konfirmasi', ['id_jadwal' => $jadwal->id_jadwal]) }}" method="POST" onsubmit="return confirm('Yakin ingin melakukan konfirmasi?');">
                                        @csrf
                                        @method('PUT')

                                        <div class="modal-header">
                                        <h5 class="modal-title" id="modalLabel{{ $jadwal->id_jadwal }}">Rincian Komisi Transaksi #{{ $jadwal->transaksi->nomor_transaksi }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body">
                                        <table class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <th>Nama Barang</th>
                                                <th>Penitip</th>
                                                <th>Harga Jual</th>
                                                <th>Komisi Reusemart</th>
                                                <th>Komisi Hunter</th>
                                                <th>Komisi Penitip</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($jadwal->transaksi->detailTransaksi as $detail)
                                                @php
                                                $barang = $detail->barang;
                                                $hargaBarang = $barang->harga_jual;
                                                $barangHunter = $barang->barang_hunter;
                                                $statusPerpanjangan = $barang->status_perpanjangan;

                                                $periodeTopSeller = date('Y-m-01', strtotime($jadwal->tanggal_jadwal));
                                                $isTopSeller = \DB::table('badge')
                                                    ->where('id_penitip', $barang->id_penitip)
                                                    ->where('periode_pemberian', $periodeTopSeller)
                                                    ->exists();

                                                $komisiPersen = $statusPerpanjangan ? 0.30 : 0.20;
                                                if ($isTopSeller) {
                                                    $komisiPersen -= 0.01;
                                                }

                                                $komisiHunterPersen = 0;
                                                $komisiHunterRp = 0;
                                                if ($barangHunter) {
                                                    $komisiHunterPersen = 0.05;
                                                    $komisiPersen -= $komisiHunterPersen;
                                                    $komisiHunterRp = 0.05 * $hargaBarang;
                                                }

                                                $komisiReusemart = $komisiPersen * $hargaBarang;

                                                $tanggalMasuk = \Carbon\Carbon::parse($barang->tanggal_masuk);
                                                $tanggalTransaksi = \Carbon\Carbon::parse($jadwal->transaksi->tanggal_transaksi);
                                                $selisihHari = $tanggalMasuk->diffInDays($tanggalTransaksi);

                                                $bonusDiskonPenitip = 0;
                                                if ($selisihHari < 7) {
                                                    $bonusDiskonPenitip = 0.10 * $komisiReusemart;
                                                    $komisiReusemart -= $bonusDiskonPenitip;
                                                }

                                                $komisiPenitip = $hargaBarang - ($komisiReusemart + $komisiHunterRp);
                                                @endphp
                                                <tr>
                                                <td>{{ $barang->nama_barang }}</td>
                                                <td>{{ $barang->penitip->nama_penitip }}</td>
                                                <td>Rp{{ number_format($hargaBarang, 0, ',', '.') }}</td>
                                                <td>Rp{{ number_format($komisiReusemart, 0, ',', '.') }}</td>
                                                <td>
                                                    @if($barangHunter)
                                                    Rp{{ number_format($komisiHunterRp, 0, ',', '.') }}
                                                    @else
                                                    -
                                                    @endif
                                                </td>
                                                <td>
                                                    Rp{{ number_format($komisiPenitip, 0, ',', '.') }}
                                                    @if($bonusDiskonPenitip > 0)
                                                    <br><small>(Bonus: 10%)</small>
                                                    @endif
                                                </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                        </div>

                                        <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Konfirmasi</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        </div>

                                    </form>
                                    </div>
                                </div>
                                </div>

                                @if ($showConfirmButton)
                                    @if ($showConfirmButton && $jadwal->jenis_jadwal === 'Diambil')
                                        <a href="{{ route('pegawai_gudang.cetakNotaPdf', ['id' => $item->id_transaksi]) }}"
                                            target="_blank"
                                            class="btn btn-sm btn-dark">
                                                <i class="bi bi-printer"></i>  Cetak PDF
                                        </a>
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalKonfirmasi{{ $jadwal->id_jadwal }}">
                                        <i class="bi bi-check-circle"></i> Konfirmasi
                                        </button>
                                    @else
                                        <a href="{{ route('pegawai_gudang.cetakNotaPdf', ['id' => $item->id_transaksi]) }}"
                                        target="_blank"
                                        class="btn btn-sm btn-dark">
                                            <i class="bi bi-printer"></i> Cetak PDF
                                        </a>
                                    @endif
                                @else
                                    @if(strtolower($item->status_transaksi) !== 'menunggu pembayaran' && strtolower($jadwal->pengiriman->status_pengiriman) !== 'dibatalkan')
                                        <a href="{{ route('pegawai_gudang.cetakNotaPdf', ['id' => $item->id_transaksi]) }}"
                                        target="_blank"
                                        class="btn btn-sm btn-dark">
                                            <i class="bi bi-printer"></i> Cetak PDF
                                        </a>
                                    @else
                                        <span>-</span>
                                    @endif
                                @endif
                            @else
                                <em style="color: #888;">Belum Disiapkan</em>
                            @endif
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center; color: #888;">Tidak ada transaksi pengiriman atau pengambilan saat ini.</td>
                </tr>
                
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $transaksi->links() }}
    </div>
</div>

@endsection
