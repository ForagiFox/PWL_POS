<?php

namespace App\Http\Controllers;

use App\Models\BarangModel;
use App\Models\PenjualanModel;
use App\Models\StokModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WelcomeController extends Controller
{
    public function index()
    {
        $penjualan = PenjualanModel::count();
        $stok = StokModel::sum('stok_jumlah');
        $breadcrumb = (object)[
            'title' => 'Selamat Datang',
            'list' => ['Home', 'Dashboard']
        ];

$stokData = StokModel::select(
        DB::raw('DATE(stok_tanggal) as tanggal'),
        DB::raw('SUM(stok_jumlah) as total_stok')
    )
    ->groupBy(DB::raw('DATE(stok_tanggal)'))
    ->orderBy('tanggal')
    ->get();

        $labels = $stokData->pluck('tanggal');
        $values = $stokData->pluck('total_stok');

        $barang = BarangModel::count();
        $activeMenu = 'dashboard';
        return view('welcome',['breadcrumb' => $breadcrumb,'barang' => $barang, 'activeMenu'=> $activeMenu,'labels' => $labels,'values' => $values, 'penjualan' => $penjualan, 'stok' => $stok]);
    }
}
