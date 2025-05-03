@extends('layouts.template')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools">
                <button onclick="modalAction('{{ url('stok/create') }}')" class="btn btn-sm btn-success mt-1">Tambah</button>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <table class="table table-bordered table-striped table-hover table-sm" id="table_penjualan">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Barang</th>
                        <th>Nama Supplier</th>
                        <th>Nama User</th>
                        <th>Tanggal Stok</th>
                        <th>Jumlah Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" data-width="75%" aria-hidden="true"></div>
@endsection

@push('css')
@endpush

@push('js')
    <script>
        function modalAction(url = '') {
            $('#myModal').load(url, function() {
                $('#myModal').modal('show');
            });
        }

        var data;


        $(document).ready(function() {
            data = $('#table_penjualan').DataTable({
                serverSide: true,
                ajax: {
                    url: "{{ url('stok/list') }}",
                    dataType: "json",
                    type: "POST",

                },
                columns: [{
                        data: "DT_RowIndex",
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "barang.barang_nama",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "supplier.supplier_nama",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "user.nama",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "stok_tanggal",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "stok_jumlah",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "aksi",
                        className: "",
                        orderable: false,
                        searchable: false
                    }
                ]
            });

        });
    </script>
@endpush
