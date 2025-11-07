@extends('Admin.dashboard')
@section('isi')

<!-- CSRF token untuk AJAX -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<form class="d-flex mt-3 ms-3 ps-0" method="GET" action="{{ route('organisasi.show') }}">
    <input
      class="form-control me-2"
      type="search"
      name="q"
      placeholder="Cari organisasi..."
      value="{{ old('q', $q) }}"
      style="width: 250px;"
    >
    <button class="btn btn-outline-dark" type="submit">
      <i class="bi bi-search"></i> Cari
    </button>
  </form>

<table class="table table-striped border-dark text-center mt-3" id="organisasi-table">
  <thead class="table-dark">
    <tr>
      <th>ID</th>
      <th>Nama</th>
      <th>Alamat</th>
      <th>Status</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
    @foreach($organisasi as $org)
      <tr data-id="{{ $org->id_organisasi }}">
        <td>ORG{{ $org->id_organisasi }}</td>
        <td class="col-nama">{{ $org->nama_organisasi }}</td>
        <td class="col-alamat">{{ $org->alamat }}</td>
        <td>{{ $org->status_aktif ? 'Aktif' : 'Nonaktif' }}</td>
        <td>
          <button type="button"
                  class="btn btn-sm btn-warning btn-edit"
                  data-bs-toggle="modal"
                  data-bs-target="#editModal"
                  data-id="{{ $org->id_organisasi }}"
                  data-nama="{{ $org->nama_organisasi }}"
                  data-alamat="{{ $org->alamat }}">
            Perbarui
          </button>
          <button type="button"
                  class="btn btn-sm btn-danger btn-nonaktif"
                  data-bs-toggle="modal"
                  data-bs-target="#nonaktifModal"
                  data-id="{{ $org->id_organisasi }}">
            Nonaktif
          </button>
          <button type="button"
                  class="btn btn-sm btn-dark btn-hapus"
                  data-bs-toggle="modal"
                  data-bs-target="#hapusModal"
                  data-id="{{ $org->id_organisasi }}">
            Hapus
          </button>
        </td>
      </tr>
    @endforeach
  </tbody>
</table>

{{-- Modal Edit --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="editForm">
        @csrf

        <div class="modal-header">
          <h5 class="modal-title">Perbarui Data Organisasi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" id="editId">

          <div class="mb-3">
            <label class="form-label">Nama Organisasi Baru</label>
            <input type="text" id="editNama" name="nama_organisasi" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Alamat Baru</label>
            <input type="text" id="editAlamat" name="alamat" class="form-control" required>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal Nonaktif --}}
<div class="modal fade" id="nonaktifModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="nonaktifForm">
        @csrf

        <div class="modal-header bg-danger">
          <h5 class="modal-title">Konfirmasi Nonaktif</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <input type="hidden" id="nonaktifId">

        <div class="modal-body">
          <h3>Ingin menonaktifkan organisasi ini?</h3>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Nonaktif</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal Hapus --}}
<div class="modal fade" id="hapusModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="hapusForm">
        @csrf

        <div class="modal-header bg-dark">
          <h5 class="modal-title">Konfirmasi Hapus</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <input type="hidden" id="hapusId">

        <div class="modal-body">
          <h3>Ingin menghapus organisasi ini?</h3>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Hapus</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
$(function(){
  // Pasang header CSRF untuk semua AJAX
  $.ajaxSetup({
    headers:{ 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
  });

  // Saat modal Edit dibuka, isi field
  $('#editModal').on('show.bs.modal', function(e){
    const btn  = $(e.relatedTarget);
    const id   = btn.data('id');
    const nama = btn.data('nama');
    const alamat = btn.data('alamat');

    $('#editId').val(id);
    $('#editNama').val(nama);
    $('#editAlamat').val(alamat);
  });

  // Submit Edit via AJAX
  $('#editForm').on('submit', function(e){
    e.preventDefault();
    const id = $('#editId').val();
    const data = {
      nama_organisasi: $('#editNama').val().trim(),
      alamat:         $('#editAlamat').val().trim()
    };

    $.post(`/organisasi/${id}`, data)
      .done(res=>{
        $('#editModal').modal('hide');
        alert('Data berhasil diperbarui');
        location.reload();
      })
      .fail(xhr=>{
        alert('Gagal update: '+xhr.responseJSON?.message||xhr.statusText);
      });
  });

  $('#nonaktifModal').on('show.bs.modal', function(e){
    const btn = $(e.relatedTarget);
    $('#nonaktifId').val(btn.data('id'));
  });

  // Submit Nonaktif via AJAX
  $('#nonaktifForm').on('submit', function(e){
    e.preventDefault();
    const id = $('#nonaktifId').val();

    $.post(`/organisasi/${id}/nonaktif`, {})
      .done(res=>{
        $('#nonaktifModal').modal('hide');
        alert('Organisasi berhasil dinonaktifkan');
        location.reload();
      })
      .fail(xhr=>{
        alert('Gagal nonaktif: '+xhr.responseJSON?.message||xhr.statusText);
      });
  });

  // Saat modal hapus dibuka, simpan ID
  $('#hapusModal').on('show.bs.modal', function(e){
    const btn = $(e.relatedTarget);
    $('#hapusId').val(btn.data('id'));
  });

  // Submit hapus via AJAX
  $('#hapusForm').on('submit', function(e){
    e.preventDefault();
    const id = $('#hapusId').val();

    $.post(`/organisasi/${id}/hapus`, {})
      .done(res=>{
        $('#hapusModal').modal('hide');
        alert('Organisasi berhasil dihapus');
        location.reload();
      })
      .fail(xhr=>{
        alert('Gagal hapus: '+xhr.responseJSON?.message||xhr.statusText);
      });
  });
});
</script>

@endsection