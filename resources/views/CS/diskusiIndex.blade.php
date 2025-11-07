@extends('cs.dashboard')

@section('isi')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container">
    <h2>Diskusi Produk Belum Dijawab</h2>

    {{-- Menampilkan pesan sukses jika ada --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Menampilkan daftar diskusi --}}
    <div class="discussion-container">
        @forelse ($diskusi as $d)
            <div class="card mb-3" id="diskusi_{{ $d->id_diskusi }}">
                <div class="card-header">
                    <strong>Produk:</strong> 
                    {{ $d->barang_titipan ? $d->barang_titipan->nama_barang : 'Produk tidak ditemukan' }}
                </div>
                <div class="card-body">
                    <h5 class="card-title"><strong>Pembeli:</strong> {{ $d->pembeli->nama_pembeli }}</h5>
                    <p class="card-text"><strong>Pertanyaan:</strong> {{ $d->pertanyaan }}</p>

                    {{-- Tombol untuk membuka modal jawaban --}}
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#jawabModal" data-id="{{ $d->id_diskusi }}" data-pertanyaan="{{ $d->pertanyaan }}">Tambah Jawaban</button>
                </div>
            </div>
            @empty
                <p class="text-gray-500">Diskusi sudah dijawab semua.</p>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $diskusi->links() }}
    </div>
</div>

<!-- Modal Form Jawaban -->
<div class="modal fade" id="jawabModal" tabindex="-1" aria-labelledby="jawabModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="jawabModalLabel">Jawab Pertanyaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formJawaban" method="POST">
                    @csrf
                    <input type="hidden" id="diskusi_id" name="diskusi_id">
                    <div class="mb-3">
                        <label for="jawaban" class="form-label">Jawaban</label>
                        <textarea id="jawaban" name="jawaban" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Kirim Jawaban</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
   // Mengambil CSRF token dari meta tag
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Mengisi modal dengan data diskusi yang dipilih
var jawabModal = document.getElementById('jawabModal');
jawabModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget; // Tombol yang diklik
    var diskusiId = button.getAttribute('data-id'); // Ambil ID diskusi
    var pertanyaan = button.getAttribute('data-pertanyaan'); // Ambil pertanyaan

    // Set nilai ke form dalam modal
    document.getElementById('diskusi_id').value = diskusiId;

    // Mengatur action URL pada form modal
    var form = document.getElementById('formJawaban');
    form.action = "/cs/diskusi/" + diskusiId + "/jawab"; // Pastikan URL action mengandung diskusi ID
});


document.getElementById('formJawaban').addEventListener('submit', function (e) {
    e.preventDefault(); // Mencegah pengalihan halaman

    const id = $('#diskusi_id').val(); // Ambil ID diskusi
    const data = {
        jawaban: $('#jawaban').val().trim() // Ambil jawaban dari textarea
    };

    fetch('/cs/diskusi/' + id + '/jawab', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json', // Pastikan JSON
            'X-CSRF-TOKEN': csrfToken,  // Menambahkan CSRF token di header
        },
        body: JSON.stringify(data)  // Mengirim data dalam format JSON
    })
    .then(response => response.json())
    .then(res => {
        // Menutup modal setelah berhasil
        $('#jawabModal').modal('hide');

        // Menampilkan jawaban langsung pada halaman tanpa reload
        var diskusiCard = $('#diskusi_' + res.diskusi_id);
        var jawabanElement = $('<p></p>');
        jawabanElement.html('<strong>Jawaban:</strong> ' + res.jawaban);
        diskusiCard.find('.card-body').append(jawabanElement);

        alert(res.message); // Pesan sukses

        // Redirect ke halaman /cs/diskusi/belum-dijawab setelah submit
        window.location.replace("/cs/diskusi/belum-dijawab");
    })
    .catch(error => console.error('Error:', error));
});

</script>

@endsection
