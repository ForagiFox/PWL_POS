<?php

namespace App\Http\Controllers;

use App\Models\PenjualanModel;
use App\Models\StokModel;
use Illuminate\Http\Request;

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
        $activeMenu = 'dashboard';
        return view('welcome',['breadcrumb' => $breadcrumb, 'activeMenu'=> $activeMenu, 'penjualan' => $penjualan, 'stok' => $stok]);
    }
}
