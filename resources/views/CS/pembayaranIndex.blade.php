@extends('CS.dashboard')

@section('isi')
<div class="container py-5">
    <h2 class="mb-4">Daftar Pembayaran</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Pembayaran</th>
                    <th>ID Pembeli</th>
                    <th>ID Pegawai</th>
                    <th>ID Transaksi</th>
                    <th>Status Verifikasi</th>
                    <th>Bukti Transfer</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pembayarans as $index => $pembayaran)
                    <tr>
                        <td>{{ $pembayarans->firstItem() + $index }}</td>
                        <td>{{ $pembayaran->id_pembayaran }}</td>
                        <td>{{ $pembayaran->id_pembeli }}</td>
                        <td>{{ $pembayaran->id_pegawai ?? '-' }}</td>
                        <td>{{ $pembayaran->id_transaksi }}</td>
                        <td>
                            @php
                                $status = (int) $pembayaran->status_verifikasi;  // cast ke int biar pasti
                                $idPegawai = $pembayaran->id_pegawai;

                                $badgeClass = 'bg-secondary';
                                $statusText = 'Belum Diverifikasi';

                                if ($status === 0 && (is_null($idPegawai) || $idPegawai == '')) {
                                    $badgeClass = 'bg-secondary';
                                    $statusText = 'Belum Diverifikasi';
                                } elseif ($status === 0 && !is_null($idPegawai) && $idPegawai != '') {
                                    $badgeClass = 'bg-danger';
                                    $statusText = 'Ditolak';
                                } elseif ($status === 1) {
                                    $badgeClass = 'bg-success';
                                    $statusText = 'Diterima';
                                }
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                        </td>
                        <td>
                            @if ($pembayaran->bukti_transfer)
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#verifikasiModal{{ $pembayaran->id_pembayaran }}">
                                    Verifikasi
                                </button>
                            @else
                                <span class="text-muted">Belum ada</span>
                            @endif
                        </td>
                    </tr>

                    <!-- Modal untuk Bukti Transfer dan Verifikasi -->
                    <div class="modal fade" id="verifikasiModal{{ $pembayaran->id_pembayaran }}" tabindex="-1" aria-labelledby="verifikasiModalLabel{{ $pembayaran->id_transaksi }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="verifikasiModalLabel{{ $pembayaran->id_transaksi }}">Verifikasi Bukti Pembayaran</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                </div>
                                <div class="modal-body text-center">
                                    @if ($pembayaran->bukti_transfer)
                                        <img src="{{ asset('images/bukti_pembayaran/' . $pembayaran->bukti_transfer) }}" alt="Bukti Pembayaran" class="img-fluid mb-3" style="max-height: 400px;">
                                    @else
                                        <p class="text-muted">Belum ada bukti pembayaran.</p>
                                    @endif
                                </div>
                                <div class="modal-footer justify-content-between">
                                    <div class="d-flex">
                                        <form action="{{ route('cs.pembayaran.tolak', $pembayaran->id_transaksi) }}" method="POST" onsubmit="return confirm('Tolak bukti pembayaran?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" 
                                                @if($pembayaran->status_verifikasi == 1) disabled title="Sudah diverifikasi" @endif>
                                                Tolak
                                            </button>
                                        </form>
                                    </div>
                                    
                                    <div class="d-flex">
                                        <form action="{{ route('cs.pembayaran.verifikasi', $pembayaran->id_transaksi) }}" method="POST" onsubmit="return confirm('Terima bukti pembayaran?')">
                                            @csrf
                                            <button type="submit" class="btn btn-success">
                                                Terima
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                @empty
                    <tr>
                        <td colspan="8" class="text-center">Data pembayaran tidak ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {!! $pembayarans->withQueryString()->links('pagination::bootstrap-5') !!}
    </div>
</div>
@endsection
