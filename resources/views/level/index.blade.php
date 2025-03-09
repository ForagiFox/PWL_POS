@extends('layouts.template')

@section('content')

<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{$page->title}}</h3>
        <div class="card-tools">
            <a href="{{url('level/create')}}" class="btn btn-sm btm-primary mt-1">Tambah</a>
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
            <th>Username</th>
            <th>Nama</th>
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
                    url: "{{ url('level/list') }}",
                    dataType: "json",
                    type: "POST",

                },
                columns: [
                    {data: "DT_RowIndex", className: "text-center", orderable: false, searchable: false},
                    {data: "level_kode", className: "", orderable: true, searchable: true},
                    {data: "level_nama", className: "", orderable: true, searchable: true},
                    {data: "aksi", className: "", orderable: false, searchable: false}
                ]
            });

        });
    </script>
@endpush
