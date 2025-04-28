<?php

namespace App\Http\Controllers;

use App\Models\BarangModel;
use App\Models\PenjualanDetailModel;
use App\Models\PenjualanModel;
use App\Models\StokModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class WelcomeController extends Controller
{
    public function index()
    {
        $subStok = DB::table('t_stok')
    ->select('barang_id', DB::raw('SUM(stok_jumlah) as total_stok'))
    ->groupBy('barang_id');

$subPenjualan = DB::table('t_penjualan_detail')
    ->select('barang_id', DB::raw('SUM(jumlah) as total_terjual'))
    ->groupBy('barang_id');

$stok = DB::table('m_barang')
    ->leftJoinSub($subStok, 's', 'm_barang.barang_id', '=', 's.barang_id')
    ->leftJoinSub($subPenjualan, 'p', 'm_barang.barang_id', '=', 'p.barang_id')
    ->select(
        'm_barang.barang_id',
        'm_barang.barang_nama',
        DB::raw('COALESCE(s.total_stok, 0) - COALESCE(p.total_terjual, 0) as total_stok')
            )
            ->get()
            ->sum('total_stok');

        $penjualan = PenjualanModel::count();
        // $stok = StokModel::sum('stok_jumlah');
        $terjual = PenjualanDetailModel::sum('jumlah');
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

        $barang = BarangModel::count('barang_id');
        $penjualan = PenjualanDetailModel::count('barang_id');
        $activeMenu = 'dashboard';
        return view('welcome',['breadcrumb' => $breadcrumb,'terjual' => $terjual,'penjualan' => $penjualan, 'activeMenu'=> $activeMenu,'penjualanBulanan' => $penjualanBulanan, 'penjualan' => $penjualan, 'stok' => $stok, 'barang' => $barang]);
    }
    public function list(Request $request)
    {
        // $stokTerbaru = StokModel::with(['barang', 'supplier'])
        //     ->selectRaw('barang_id, supplier_id, SUM(stok_jumlah) as total_stok')
        //     ->groupBy('barang_id')
        //     ->get();
        //

$subStok = DB::table('t_stok')
    ->select('barang_id', DB::raw('SUM(stok_jumlah) as total_stok'))
    ->groupBy('barang_id');

$subPenjualan = DB::table('t_penjualan_detail')
    ->select('barang_id', DB::raw('SUM(jumlah) as total_terjual'))
    ->groupBy('barang_id');

$stokTerbaru = DB::table('m_barang')
    ->leftJoinSub($subStok, 's', 'm_barang.barang_id', '=', 's.barang_id')
    ->leftJoinSub($subPenjualan, 'p', 'm_barang.barang_id', '=', 'p.barang_id')
    ->select(
        'm_barang.barang_id',
        'm_barang.barang_nama',
        DB::raw('COALESCE(s.total_stok, 0) - COALESCE(p.total_terjual, 0) as total_stok')
    )
    ->get();



        return DataTables::of($stokTerbaru)
            ->addIndexColumn()
            ->rawColumns(['aksi'])
            ->make(true);
    }
}


