    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Transaksi Penjualan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-penjualan">
                    @csrf
                    <div class="form-group">
                        <label for="pembeli">Pembeli</label>
                        <input type="text" class="form-control" id="pembeli" name="pembeli"
                            placeholder="Masukkan nama pembeli">
                        <small class="text-danger" id="error-pembeli"></small>
                    </div>
                    <div class="form-group">
                        <label for="penjualan_tanggal">Tanggal Penjualan</label>
                        <input type="date" class="form-control" id="penjualan_tanggal" value={{ now() }}
                            name="penjualan_tanggal">
                        <small class="text-danger" id="error-penjualan_tanggal"></small>
                    </div>
                    <div class="form-group">
                        <label>Barang</label>
                        <table class="table table-bordered" id="items-table">
                            <thead>
                                <tr>
                                    <th>Barang</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <button type="button" class="btn btn-sm btn-success" id="add-item">Tambah Barang</button>
                        <small class="text-danger" id="error-barang_id"></small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(function() {
            var barangOptions = '';
            @foreach ($barang as $b)
                barangOptions +=
                    '<option value="{{ $b->barang_id }}" data-harga="{{ $b->harga_jual }}">{{ $b->barang_nama }}</option>';
            @endforeach
            function addRow() {
                var row = '<tr>' +
                    '<td><select name="barang_id[]" class="form-control barang-select"><option value="">Pilih</option>' +
                    barangOptions + '</select></td>' +
                    '<td><input type="text" name="harga_jual[]" class="form-control harga-input" readonly></td>' +
                    '<td><input type="number" name="jumlah[]" class="form-control jumlah-input" min="1"></td>' +
                    '<td><button type="button" class="btn btn-danger btn-sm remove-item">X</button></td>' +
                    '</tr>';
                $('#items-table tbody').append(row);
            }
            $('#add-item').click(addRow);
            $(document).on('change', '.barang-select', function() {
                var harga = $(this).find('option:selected').data('harga');
                $(this).closest('tr').find('.harga-input').val(harga);
            });
            $(document).on('click', '.remove-item', function() {
                $(this).closest('tr').remove();
            });

            addRow();
            $('#form-penjualan').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ url('penjualan/store') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(res) {
                        if (res.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: res.message
                            }).then(() => {
                                $('#myModal').modal('hide');
                                data.ajax.reload();
                            });
                        } else {
                            $('.text-danger').text('');
                            if (res.msgField) {
                                $.each(res.msgField, function(k, v) {
                                    $('#error-' + k).text(v[0]);
                                });
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: res.message
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan'
                        });
                    }
                });
            });
        });
    </script>
