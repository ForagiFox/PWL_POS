@extends('layouts.template')

@section('content')

<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{$page->title}}</h3>
        <div class="card-tools">
            <a href="{{url('user/create')}}" class="btn btn-sm btm-primary mt-1">Tambah</a>
        </div>
    </div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">
                {{session('success')}}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                {{session('error')}}
            </div>
        @endif

        <table class="table table-bordered table-striped table-hover table-sm" id="table_user">
            <thead>
            <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Nama</th>
            <th>Level Pengguna</th>
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
$(document).ready(function() {
var dataUser = $('#table_user').DataTable({
// serverSide: true, jika ingin menggunakan server side processing
serverSide: true,
ajax: {
"url": "{{ url('user/list') }}",
"dataType": "json",
"type": "POST"
},
columns: [
{
// nomor urut dari laravel datatable addIndexColumn()
data: "DT_RowIndex",
className: "text-center",
orderable: false,
searchable: false
},{
data: "username",
className: "",
// orderable: true, jika ingin kolom ini bisa diurutkan
orderable: true,
// searchable: true, jika ingin kolom ini bisa dicari
searchable: true
},{
data: "nama",
className: "",
orderable: true,
searchable: true
},{
// mengambil data level hasil dari ORM berelasi
data: "level.level_nama",
className: "",
orderable: false,
searchable: false
},{
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
