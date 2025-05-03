@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools"></div>
        </div>
        <div class="card-body">
            @if(is_null($supplier))
                <div class="alert alert-danger alert-dismissible">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!</h5>
                    Data yang Anda cari tidak ditemukan.
                </div>
            @else
                <table class="table table-bordered table-striped table-hover table-sm">
                    <tr>
                        <th>Kode Supplier</th>
                        <td>{{ $supplier->supplier_kode }}</td>
                    </tr>
                    <tr>
                        <th>Nama Supplier</th>
                        <td>{{ $supplier->supplier_nama }}</td>
                    </tr>
                    <tr>
                        <th>Alamat Supplier</th>
                        <td>{{ $supplier->supplier_alamat }}</td>
                    </tr>

                </table>
            @endif
            <a href="{{ url('supplier') }}" class="btn btn-sm btn-default mt-2">Kembali</a>
        </div>
    </div>
@endsection

@push('css')
@endpush

@push('js')
@endpush
