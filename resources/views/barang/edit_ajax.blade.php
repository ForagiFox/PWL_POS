@empty($barang)
<div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Kesalahan</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="alert alert-danger">
                <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                Data yang anda cari tidak ditemukan.
            </div>
            <a href="{{ url('/barang') }}" class="btn btn-warning">Kembali</a>
        </div>
    </div>
</div>
@else
<form action="{{ url('/barang/' . $barang->barang_id . '/update_ajax') }}" method="POST" id="formedit">
    @csrf
    @method('PUT')
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Data barang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>kategori barang</label>
                    <select name="kategori_id" id="kategori_id" class="form-control" required>
                        <option value="">- Pilih kategori -</option>
                        @foreach($kategori as $l)
                            <option {{ ($l->kategori_id == $barang->kategori_id) ? 'selected' : '' }} 
                                value="{{ $l->kategori_id }}">{{ $l->kategori_nama }}</option>
                        @endforeach
                    </select>
                    <small id="error-kategori_id" class="error-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>kode barang</label>
                    <input value="{{ $barang->barang_kode }}" type="text" name="barang_kode" id="barang_kode" class="form-control" required>
                    <small id="error-barang_kode" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>nama barag</label>
                    <input value="{{ $barang->barang_nama }}" type="text" name="barang_nama" id="barang_nama" class="form-control" required>
                    <small id="error-barang_nama" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>harga beli</label>
                    <input value="{{ $barang->harga_beli }}" type="text" name="harga_beli" id="harga_beli" class="form-control" required>
                    <small id="error-harga_beli" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>harga jual</label>
                    <input value="{{ $barang->harga_jual }}" type="text" name="harga_jual" id="harga_jual" class="form-control" required>
                    <small id="error-harga_jual" class="error-text form-text text-danger"></small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
        $("#formedit").validate({
            rules: {
                kategori_id: {required: true, number: true},
                barang_kode: {required: true, minlength: 3, maxlength: 20},
                barang_nama: {required: true, minlength: 3, maxlength: 100},
                harga_beli: {required: true, minlength: 3, maxlength: 100},
                harga_jual: {required: true, minlength: 3, maxlength: 100},
            },
            messages: {
                kategori_id: { required: "kategori pengguna wajib dipilih." },
                barang_kode: { required: "kode barang wajib diisi.", minlength: "Minimal 3 karakter.", maxlength: "Maksimal 20 karakter." },
                barang_nama: { required: "Nama barang wajib diisi.", minlength: "Minimal 3 karakter.", maxlength: "Maksimal 100 karakter." },
                harga_beli: { required: "harga beli barang wajib diisi.", minlength: "Minimal 3 karakter.", maxlength: "Maksimal 100 karakter." },
                harga_jual: { required: "harga jual barang wajib diisi.", minlength: "Minimal 3 karakter.", maxlength: "Maksimal 100 karakter." }
            },
            submitHandler: function(form) {
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: $(form).serialize(),
                    success: function(response) {
                        if (response.status) {
                            $('#modal-master').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    dataBarang.ajax.reload();
                                }
                            });
                        } else {
                            $('.error-text').text('');
                            $.each(response.msgField, function(prefix, val) {
                                $('#error-' + prefix).text(val[0]);
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan dalam mengupdate data. Silakan coba lagi.'
                        });
                    }
                });
                return false;
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function(element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid');
            }
        });
    });
</script>
@endempty
