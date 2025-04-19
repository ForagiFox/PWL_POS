    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail Penjualan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <table class="table table-bordered table-striped table-hover table-sm" id="tpd">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Barang</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                            @php $count = 1; @endphp
                    @foreach ($data as $datas)
                <tr>

                    <td>{{$count++}}</td>
                    <td>{{$datas->barang->barang_nama}}</td>
                    <td>{{$datas->harga}}</td>
                    <td>{{$datas->jumlah}}</td>
                </tr>
                @endforeach
            </table>

            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Tutup</button>
            </div>
        </div>
    </div>
    <!-- <script> -->
    <!--     var datas; -->
    <!---->
    <!---->
    <!--     $(document).ready(function() { -->
    <!--         datas = $('#tpd').DataTable({ -->
    <!--             serverSide: true, -->
    <!--             ajax: { -->
    <!--                 url: "{{ url('penjualan/1/detail_list') }}", -->
    <!--                 dataType: "json", -->
    <!--                 type: "POST", -->
    <!---->
    <!--             }, -->
    <!--             columns: [{ -->
    <!--                     data: "DT_RowIndex", -->
    <!--                     className: "text-center", -->
    <!--                     orderable: false, -->
    <!--                     searchable: false -->
    <!--                 }, -->
    <!--                 { -->
    <!--                     data: "barang.barang_nama", -->
    <!--                     className: "", -->
    <!--                     orderable: true, -->
    <!--                     searchable: true -->
    <!--                 }, -->
    <!--                 { -->
    <!--                     data: "harga", -->
    <!--                     className: "", -->
    <!--                     orderable: true, -->
    <!--                     searchable: true -->
    <!--                 }, -->
    <!--                 { -->
    <!--                     data: "jumlah", -->
    <!--                     className: "", -->
    <!--                     orderable: true, -->
    <!--                     searchable: true -->
    <!--                 }, -->
    <!--                 { -->
    <!--            ] -->
    <!--         }); -->
    <!---->
    <!--     }); -->
    <!-- </script> -->

