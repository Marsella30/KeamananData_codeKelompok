@extends('pegawai_gudang.dashboard')
@section('isi')

@php
    $pegawai = auth()->guard('pegawai')->user();
@endphp

<div class="d-flex justify-content-center align-items-center" style="padding-top: 40px; padding-bottom: 40px;">
    <div class="card shadow" style="width: 1000px;">
        <div class="card-body">
            <h2 class="mb-2 text-center"><strong>Edit Barang: {{ $barang->nama_barang }}</strong></h2>
            <p class="text-center">
                <strong>Oleh:</strong> P{{ $pegawai->id_pegawai }} - {{ $pegawai->nama_pegawai }}
            </p>

            <!-- {{-- Tombol “Batal / Kembali” --}}
            <div class="mb-3">
                @if($context === 'create')
                {{-- Kembali ke form Tambah Barang untuk nota yang sama --}}
                <a href="{{ route('pegawai_gudang.barangTitipan.create', ['id_nota' => $idNota]) }}"
                    class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Form Tambah Barang
                </a>
                @else
                {{-- context=="detail": Kembali ke Detail Barang --}}
                <a href="{{ route('pegawai_gudang.barangTitipan.showDetail', $barang->id_barang) }}"
                    class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Detail Barang
                </a>
                @endif
            </div> -->

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Oops!</strong> Ada kesalahan pada input:<br>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('pegawai_gudang.barangTitipan.update', $barang->id_barang) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <input type="hidden" name="context" value="{{ $context }}">
                @if($context === 'create')
                    <input type="hidden" name="id_nota" value="{{ $idNota }}">
                @endif
                <input type="hidden" name="id_penitip" value="{{ $barang->id_penitip }}">
                <input type="hidden" name="id_pegawai" value="{{ $barang->id_pegawai }}">

                <div class="mb-3">
                    <label class="form-label">Penitip</label>
                    <input type="text" class="form-control"
                        value="T{{ $barang->penitip->id_penitip }} - {{ $barang->penitip->nama_penitip }}" readonly>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Pegawai QC</label>
                        <select name="id_qc_pegawai" class="form-select" required>
                            @foreach ($pegawaiQc as $qc)
                                <option value="{{ $qc->id_pegawai }}" {{ $barang->id_qc_pegawai == $qc->id_pegawai ? 'selected' : '' }}>
                                    P{{ $qc->id_pegawai }} - {{ $qc->nama_pegawai }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Hunter</label>
                        <select name="id_hunter" class="form-select">
                            <option value="">-- Tidak Ada --</option>
                            @foreach ($pegawaiHunter as $hunter)
                                <option value="{{ $hunter->id_pegawai }}" {{ $barang->id_hunter == $hunter->id_pegawai ? 'selected' : '' }}>
                                    P{{ $hunter->id_pegawai }} - {{ $hunter->nama_pegawai }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control" value="{{ $barang->nama_barang }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kategori</label>
                        <select name="id_kategori" class="form-select" required>
                            @foreach ($kategori as $k)
                                <option value="{{ $k->id_kategori }}" {{ $barang->id_kategori == $k->id_kategori ? 'selected' : '' }}>
                                    {{ $k->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Berat (kg)</label>
                        <input type="number" step="0.01" name="berat" class="form-control" value="{{ $barang->berat }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Harga Jual</label>
                        <input type="number" name="harga_jual" class="form-control" value="{{ $barang->harga_jual }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" class="form-control">{{ $barang->deskripsi }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Garansi</label>
                        <select name="garansi" class="form-select">
                            <option value="1" {{ $barang->garansi ? 'selected' : '' }}>Tersedia</option>
                            <option value="0" {{ !$barang->garansi ? 'selected' : '' }}>Tidak Tersedia</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Garansi</label>
                        <input type="date" name="tanggal_garansi" class="form-control"
                            value="{{ $barang->tanggal_garansi ? $barang->tanggal_garansi->format('Y-m-d') : '' }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status Barang</label>
                        <input type="text" name="status_barang" class="form-control" value="{{ $barang->status_barang }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status Perpanjangan</label>
                        <select name="status_perpanjangan" class="form-select">
                            <option value="1" {{ $barang->status_perpanjangan ? 'selected' : '' }}>Ya</option>
                            <option value="0" {{ !$barang->status_perpanjangan ? 'selected' : '' }}>Tidak</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Masuk</label>
                        <input type="datetime-local" name="tanggal_masuk" class="form-control"
                            value="{{ \Carbon\Carbon::parse($barang->tanggal_masuk)->format('Y-m-d\TH:i') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Akhir</label>
                        <input type="datetime-local" name="tanggal_akhir" class="form-control"
                            value="{{ \Carbon\Carbon::parse($barang->tanggal_akhir)->format('Y-m-d\TH:i') }}" readonly>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Upload Foto Baru</label>
                    <input type="file" name="foto_barang[]" class="form-control" id="inputFotoBaru" multiple>
                    <div id="previewFotoBaru" class="d-flex flex-wrap gap-3 mt-2"></div>

                    <small class="text-muted">Kosongkan jika tidak ingin menambah foto</small>
                </div>

                @if ($barang->fotoBarang->count())
                    <div class="mb-3">
                        <label class="form-label">Foto</label>
                        <small class="text-muted d-block mt-1">Minimal 2 foto wajib dimiliki. Jika ingin hapus semua, pastikan upload pengganti.</small>
                        <div class="d-flex flex-wrap gap-3">
                            @foreach ($barang->fotoBarang as $foto)
                                <div style="position: relative; width: 150px; height: 150px;">
                                    <img src="{{ asset('images/barang/' . $foto->nama_file) }}"
                                        style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px; border: 1px solid #ccc;">
                                    
                                    <div style="position: absolute; top: 6px; right: 6px;">
                                        <input type="checkbox" name="hapus_foto[]" value="{{ $foto->id_foto }}">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <small class="text-muted d-block">Centang untuk menghapus</small>
                    </div>
                @endif

                <div class="text-end">
                    <!-- <a href="{{ route('pegawai_gudang.barangTitipan.showDetail', $barang->id_barang) }}" class="btn btn-secondary">Batal</a> -->
                    <!-- @if ($errors->has('foto_barang'))
                        <div class="alert alert-danger mt-2">
                            {{ $errors->first('foto_barang') }}
                        </div>
                    @endif -->
                    {{-- Tombol Simpan --}}
                    <div class="mb-3 row">
                        <div class="col-sm-12">
                            <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                            @if($context === 'create')
                                <a href="{{ route('pegawai_gudang.barangTitipan.create', ['id_nota' => $idNota]) }}"
                                class="btn btn-secondary ms-2">
                                Batal
                                </a>
                            @else
                                <a href="{{ route('pegawai_gudang.barangTitipan.showDetail', $barang->id_barang) }}"
                                class="btn btn-secondary ms-2">
                                Batal
                                </a>
                            @endif
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tglMasuk = document.querySelector('input[name="tanggal_masuk"]');
        const tglAkhir = document.querySelector('input[name="tanggal_akhir"]');

        function updateTanggalAkhir() {
            if (!tglMasuk || !tglAkhir) return;

            const masuk = new Date(tglMasuk.value);

            if (!isNaN(masuk.getTime())) {
                const akhir = new Date(masuk);
                akhir.setDate(akhir.getDate() + 30);
                akhir.setHours(masuk.getHours(), masuk.getMinutes(), 0, 0);

                const pad = n => n.toString().padStart(2, '0');
                const formatted = `${akhir.getFullYear()}-${pad(akhir.getMonth() + 1)}-${pad(akhir.getDate())}T${pad(akhir.getHours())}:${pad(akhir.getMinutes())}`;
                tglAkhir.value = formatted;
            }
        }

        // Update saat load dan saat berubah
        updateTanggalAkhir();
        tglMasuk.addEventListener('input', updateTanggalAkhir);
        tglMasuk.addEventListener('change', updateTanggalAkhir);
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    const inputFoto = document.querySelector('input[name="foto_barang[]"]');
    const totalFotoLama = {{ $barang->fotoBarang->count() }};

    form.addEventListener('submit', function (e) {
        console.log("Form SUBMIT jalan");

        const totalFotoBaru = inputFoto?.files?.length || 0;
        const totalSetelahSubmit = totalFotoLama + totalFotoBaru;

        if (totalFotoSetelahSubmit < 2) {
            e.preventDefault();
            alert("Total foto setelah update minimal 2.");
        }
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputFoto = document.getElementById('inputFotoBaru');
    const previewDiv = document.getElementById('previewFotoBaru');

    inputFoto.addEventListener('change', function () {
        previewDiv.innerHTML = ''; // kosongkan dulu
        Array.from(this.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = e => {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style = "width: 100px; height: 100px; object-fit: cover; border: 1px solid #ccc; margin-right: 10px;";
                previewDiv.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    });
});
</script>

@endpush