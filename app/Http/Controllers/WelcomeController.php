<?php

namespace App\Http\Controllers;

use App\Models\BarangModel;
use App\Models\PenjualanDetailModel;
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

$penjualanBulanan = PenjualanModel::select(
        DB::raw('YEAR(penjualan_tanggal) as tahun'),
        DB::raw('MONTH(penjualan_tanggal) as bulan'),
        DB::raw('COUNT(*) as total')
    )
    ->groupBy(DB::raw('YEAR(penjualan_tanggal)'), DB::raw('MONTH(penjualan_tanggal)'))
    ->orderBy(DB::raw('YEAR(penjualan_tanggal)'))
    ->orderBy(DB::raw('MONTH(penjualan_tanggal)'))
    ->get();


        $barang = PenjualanDetailModel::count('barang_id');
        $activeMenu = 'dashboard';
        return view('welcome',['breadcrumb' => $breadcrumb,'barang' => $barang, 'activeMenu'=> $activeMenu,'penjualanBulanan' => $penjualanBulanan, 'penjualan' => $penjualan, 'stok' => $stok]);
    }
}
