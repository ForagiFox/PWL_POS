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
                <div class="row g-4 mb-5">
                    <div class="col-12 col-md-4">
                        <x-count title="Total Transaksi" :value="$penjualan" />
                    </div>
                    <div class="col-12 col-md-4">
                        <x-count title="Total Stok Ready" :value="$stok . ' / ' . $barang . ' Barang'" bg="bg-success" />
                    </div>
                    <div class="col-12 col-md-4">
                        <x-count title="Total Stok Terjual" :value="$terjual" bg="bg-info" />
                    </div>
                </div>

                <div class="h-25 mt-3">
                    <h3>Total Penjualan per Bulan</h3>
                    <div id='chart'>

                    </div>
                </div>
                <h3>Table Total Stok Ready</h3>
                <table class="table table-bordered table-striped table-hover table-sm" id="table_penjualan">
                    <thead>
                        <tr>
                            <th>Nama Barang</th>
                            <th>Jumlah Stok</th>
                        </tr>
                    </thead>
                </table>



            </div>
        </div>
    @endauth
    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static"
        data-keyboard="false" data-width="75%" aria-hidden="true"></div>
@endsection
@push('css')
@endpush
@php
    $labels = [];
    $values = [];

    foreach ($penjualanBulanan as $item) {
        $labels[] = \Carbon\Carbon::create()->month($item->bulan)->format('F'); // nama bulan
        $values[] = $item->total;
    }
@endphp
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
                    url: "{{ url('/list') }}",
                    dataType: "json",
                    type: "POST",

                },
                pageLength: 5,
                columns: [{
                        data: "barang_nama",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "total_stok",
                        className: "",
                        orderable: true,
                        searchable: true
                    },

                ]
            });

        });
    </script>

    <script>
        var options = {
            chart: {
                type: 'bar',
                height: 300
            },
            series: [{
                name: 'Penjualan',
                data: @json($values)
            }],
            xaxis: {
                categories: @json($labels)
            }
        }

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();

        function modalAction(url = '') {
            $('#myModal').load(url, function() {
                $('#myModal').modal('show');
            });
        }
    </script>
@endpush
