@extends('layouts.template')

@section('content')
    @auth
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Halo {{ auth()->user()->nama }}, apakabar?!!</h3>
                <div class="card-tools">
                    <a href="{{ url('logout') }}" class="btn btn-sm btn-danger mt-1">Logout</a>
                </div>
            </div>
            <div class="card-body">
<div class="row g-4">
    <div class="col-12 col-md-6">
        <x-count
            title="Total Penjualan"
            :value="$penjualan"
        />
    </div>
    <div class="col-12 col-md-6">
        <x-count
            title="Total Stok"
            :value="$stok"
            bg="bg-success"
        />
    </div>
</div>
            </div>
        </div>
    @endauth
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
</script>
@endpush
