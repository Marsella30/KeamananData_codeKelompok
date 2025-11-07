@extends('pegawai_gudang.dashboard')
@section('isi')
@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="container mt-5">
    <div class="card shadow rounded-3">
        <div class="card-body">
            <!-- <h4 class="mb-4"><strong>Buat Nota Penitipan Baru</strong></h4> -->

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('pegawai_gudang.notaPenitipan.store') }}" method="POST">
                @csrf

                {{-- Penitip --}}
                <div class="mb-3">
                    <label for="penitip" class="form-label">Penitip</label>
                    <input type="hidden" name="id_penitip" value="{{ $terpilih->id_penitip }}">
                    <input type="text" class="form-control" value="T{{ $terpilih->id_penitip }} - {{ $terpilih->nama_penitip }}" readonly>
                </div>

                {{-- Pegawai QC --}}
                <div class="mb-3">
                    <label for="id_qc_pegawai" class="form-label">Pegawai QC</label>
                    <select name="id_qc_pegawai" id="id_qc_pegawai" class="form-select" required>
                        <option value="">-- Pilih Pegawai QC --</option>
                        @foreach($pegawaiQc as $peg)
                            <option value="{{ $peg->id_pegawai }}" {{ old('id_qc_pegawai') == $peg->id_pegawai ? 'selected' : '' }}>
                                P{{ $peg->id_pegawai }} - {{ $peg->nama_pegawai }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_qc_pegawai')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    {{-- Tanggal Penitipan --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Penitipan</label>
                        <input type="datetime-local" id="tanggal_penitipan" name="tanggal_penitipan" class="form-control"
                            value="{{ old('tanggal_penitipan', now()->format('Y-m-d\TH:i')) }}">
                    </div>

                    {{-- Masa Berakhir --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Masa Berakhir</label>
                        <input type="datetime-local" id="masa_berakhir" name="masa_berakhir" class="form-control"
                            value="{{ old('masa_berakhir', now()->addDays(30)->format('Y-m-d\TH:i')) }}" readonly>
                    </div>
                </div>

                <button type="submit" class="btn btn-success mt-2">Next</button>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tglMasuk = document.getElementById('tanggal_penitipan');
        const tglAkhir = document.getElementById('masa_berakhir');

        function updateTanggalAkhir() {
            if (!tglMasuk || !tglAkhir) return;

            const masuk = new Date(tglMasuk.value);

            console.log("Tanggal penitipan:", tglMasuk.value);

            if (!isNaN(masuk.getTime())) {
                const akhir = new Date(masuk);
                akhir.setDate(masuk.getDate() + 30);
                akhir.setHours(masuk.getHours(), masuk.getMinutes(), 0, 0);

                const pad = n => n.toString().padStart(2, '0');
                const formatted = `${akhir.getFullYear()}-${pad(akhir.getMonth() + 1)}-${pad(akhir.getDate())}T${pad(akhir.getHours())}:${pad(akhir.getMinutes())}`;
                tglAkhir.value = formatted;
            }
        }

        // Jalankan sekali saat halaman load
        updateTanggalAkhir();

        // Jalankan saat user ubah input
        tglMasuk.addEventListener('input', updateTanggalAkhir);
        tglMasuk.addEventListener('change', updateTanggalAkhir);
    });
</script>
@endpush
