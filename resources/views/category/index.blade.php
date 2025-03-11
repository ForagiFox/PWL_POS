@extends('layouts.template')

@section('content')

<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{$page->title}}</h3>
        <div class="card-tools">
            <a href="{{url('kategori/create')}}" class="btn btn-sm btn-primary mt-1">Tambah</a>
        </div>
    </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <table class="table table-bordered table-striped table-hover table-sm" id="table_user">
            <thead>
            <tr>
            <th>ID</th>
            <th>Kode Kategori</th>
            <th>Nama Kategori</th>
            <th>aksi</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

@endsection

@push('css')
@endpush

@push('js')
    <script>
        $(document).ready(function () {
            var dataUser = $('#table_user').DataTable({
                serverSide: true,
                ajax: {
                    url: "{{ url('kategori/list') }}",
                    dataType: "json",
                    type: "POST",

                },
                columns: [
                    {data: "DT_RowIndex", className: "text-center", orderable: false, searchable: false},
                    {data: "kategori_kode", className: "", orderable: true, searchable: true},
                    {data: "kategori_nama", className: "", orderable: true, searchable: true},
                    {data: "aksi", className: "", orderable: false, searchable: false}
                ]
            });

        });
    </script>
@endpush
