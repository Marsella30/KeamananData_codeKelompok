@extends('pegawai_gudang.dashboard')

@section('isi')

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<style>
    th {
        width: 200px;
        white-space: nowrap;
        padding: 10px;
        background-color: #f8f9fa;
    }

    td {
        padding: 10px;
        vertical-align: middle;
    }

    table {
        margin-bottom: 30px;
    }

    .container-fluid {
        max-width: 1065px;
        margin: auto;
    }

    .img-thumbnail {
        border: 1px solid #ddd;
        padding: 4px;
        background-color: #fff;
    }

    h5 {
        margin-top: 20px;
    }

    .status-btn {
        border: none;
        border-radius: 20px;
        padding: 5px 12px;
        font-size: 0.875rem;
    }
    .status-tersedia {
        background-color: #d4edda;
        color: #155724;
    }
    .status-terjual {
        background-color: #f8d7da;
        color: #721c24;
    }
    .status-didonasikan {
        background-color: #fff3cd;
        color: #856404;
    }
    .status-donasi {
        background-color: #dee2e6;
        color: #495057;
    }
    .status-diambil {
        background-color: #dee2e6;
        color:rgb(14, 180, 209);
    }
</style>

<div class="container-fluid mt-2">
    <h3 class="mb-5 text-center"><strong>Detail Barang Titipan</strong></h3>

    <div class="row">
        <div class="col-12">

        <!-- <input type="hidden" name="id_nota" value="{{ request('id_nota') }}"> -->
        <div class="row">
            <div class="col-md-9">
                <table class="table table-bordered table-striped table-hover">
                    <tr>
                        <th>No Nota</th>
                        <td>{{ $nota->no_nota }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-md-9">
                <table class="table table-bordered table-striped table-hover">
                    <tr>
                        <th>Kode Barang</th>
                        <td>{{ strtoupper(substr($barang->nama_barang, 0, 1)) . $barang->id_barang }}</td>
                    </tr>
                    <tr>
                        <th>Nama Barang</th>
                        <td>{{ $barang->nama_barang }}</td>
                    </tr>
                    <tr>
                        <th>Kategori</th>
                        <td colspan="3">{{ $barang->kategori->nama_kategori ?? '-' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-3">
               <table class="table table-bordered table-striped table-hover">
                    <tr>
                        <th class="text-center">Status Barang</th>
                    </tr>
                    <tr>
                        @php
                            $status = strtolower($barang->status_barang);
                            $classStatus = '';
                            if ($status === 'tersedia') {
                                $classStatus = 'status-tersedia';
                            } elseif (in_array($status, ['terjual', 'diambil kembali', 'pengambilan diproses'])) {
                                $classStatus = 'status-terjual';
                            } elseif ($status === 'didonasikan') {
                                $classStatus = 'status-didonasikan';
                            } elseif ($status === 'barang untuk donasi') {
                                $classStatus = 'status-donasi';
                            } elseif ($status === 'diambil kembali') {
                                $classStatus = 'status-diambil';
                            }
                        @endphp
                        <td class="text-center">
                            <span class="status-btn {{ $classStatus }}">
                                {{ $status === 'barang untuk donasi' ? 'barang untuk donasi' : $barang->status_barang }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-md-9">
                <table class="table table-bordered table-striped table-hover">
                    <tr>
                        <th>Berat</th>
                        <td>{{ $barang->berat }} kg</td>
                    </tr>
                    <tr>
                        <th>Harga Jual</th>
                        <td colspan="3">Rp{{ number_format($barang->harga_jual, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Deskripsi</th>
                        <td style="text-align: justify;">{{ $barang->deskripsi }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-md-9">
                <table class="table table-bordered table-striped table-hover">
                    <tr>
                        <th>Penitip</th>
                        <td>T{{ $barang->penitip->id_penitip }} - {{ $barang->penitip->nama_penitip ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Pegawai QC</th>
                        <td>P{{ $barang->pegawaiQc->id_pegawai }} - {{ $barang->pegawaiQc->nama_pegawai ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Hunter</th>
                        <td colspan="3">
                            @if ($barang->hunter)
                                P{{ $barang->hunter->id_pegawai }} - {{ $barang->hunter->nama_pegawai }}
                                <span class="badge bg-success ms-2">Barang Hunter</span>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-md-9">
                <table class="table table-bordered table-striped table-hover">
                    <tr>
                        <th>Perpanjangan</th>
                        <td>{{ $barang->status_perpanjangan ? 'Ya' : 'Tidak' }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Masuk</th>
                        <td>{{ \Carbon\Carbon::parse($barang->tanggal_masuk)->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Akhir</th>
                        <td>{{ \Carbon\Carbon::parse($barang->tanggal_akhir)->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Keluar</th>
                        <td>{{ $barang->tanggal_keluar ? \Carbon\Carbon::parse($barang->tanggal_keluar)->format('d/m/Y H:i') : '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-md-9">
                <table class="table table-bordered table-striped table-hover">
                    <tr>
                        <th>Garansi</th>
                        <td>{{ $barang->garansi ? 'Tersedia' : 'Tidak Tersedia' }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Garansi</th>
                        <td>{{ $barang->tanggal_garansi ? \Carbon\Carbon::parse($barang->tanggal_garansi)->format('d/m/Y') : '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-md-5">
                <h5 style="font-size: 17px;"><strong>Foto Barang</strong></h5>
                <div class="d-flex flex-row flex-nowrap overflow-auto gap-3">
                    @foreach ($barang->fotoBarang as $index => $foto)
                        <div class="text-center">
                            <img src="{{ asset('images/barang/' . $foto->nama_file) }}"
                                class="img-thumbnail"
                                style="width: 200px; height: 200px; object-fit: contain; border: 1px solid #ccc;"
                                alt="Foto {{ $index + 1 }}">
                            <small class="text-muted d-block">Foto {{ $index + 1 }}</small>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between mt-4 mb-4">
        {{-- Tombol Kembali di paling kiri --}}
        <a href="{{ route('pegawai_gudang.barangTitipan.index') }}"
            class="btn btn-secondary px-4 py-2">
            ← Kembali
        </a>

        {{-- Kelompokkan Detail Nota & Edit agar berdempetan di kanan --}}
        <div class="d-flex">
            {{-- Tombol Detail Nota --}}
            <a href="{{ route('pegawai_gudang.notaPenitipan.show', $barang->nota->id_nota) }}" class="btn btn-danger px-4 py-2 me-2">
                <i class="bi bi-clipboard"></i> Detail Nota
            </a>
            {{-- Tombol Edit --}}
            @php
                $isAvailable = strtolower($barang->status_barang) === 'tersedia';
            @endphp

            <a href="{{ $isAvailable ? route('pegawai_gudang.barangTitipan.edit', ['id' => $barang->id_barang, 'context' => 'detail']) : '#' }}"
            class="btn btn-warning {{ $isAvailable ? '' : 'disabled' }}"
            {{ $isAvailable ? '' : 'aria-disabled=true tabindex=-1' }}>
            <i class="fas fa-edit"></i> Edit Barang
            </a>
            <!-- <a href="{{ route('pegawai_gudang.barangTitipan.edit', [
                    'id'        => $barang->id_barang,
                    'context'   => 'detail'
                ]) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Edit Barang
            </a> -->
        </div>
    </div>
</div>
@endsection
