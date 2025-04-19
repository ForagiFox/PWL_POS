<?php

namespace App\Http\Controllers;

use App\Models\PenjualanDetailModel;
use App\Models\PenjualanModel;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PenjualanController extends Controller
{

    public function index()
    {
        $breadcrumb = (object) [
            "title" => "Daftar Penjualan",
            "list" => ["Home", "penjualan"],
        ];

        $page = (object) [
            "title" => "Daftar penjualan yang terdaftar dalam sistem",
        ];

        $activeMenu = "penjualan";

        return view("penjualan.index", [
            "breadcrumb" => $breadcrumb,
            "page" => $page,
            "activeMenu" => $activeMenu,
        ]);
    }


    public function list(Request $request)
    {
        $datas = PenjualanModel::select(
            "penjualan_id",
            "user_id",
            "penjualan_kode",
            "pembeli",
            "penjualan_tanggal",
        )->with("user");


        return DataTables::of($datas)
            ->addIndexColumn()
            ->addColumn("aksi", function ($data) {
                // $btn = '<a href="' . url('/barang/' . $barang->barang_id) . '" class="btn btn-info btn-sm">Detail</a> ';
                // $btn .= '<a href="' . url('/barang/' . $barang->barang_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                // $btn .= '<form class="d-inline-block" method="POST" action="' . url('/barang/' . $barang->barang_id) . '" style="display:inline;">'
                //     . csrf_field() . method_field('DELETE') .
                //     '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button>
                // </form>';

                $btn =
                    '<button onclick="modalAction(\'' .
                    url("/penjualan/" . $data->penjualan_id . "/detail") .
                    '\')" class="btn btn-info btn-sm">Detail</button> ';
                return $btn;
            })
            ->rawColumns(["aksi"])
            ->make(true);
    }

    public function detail(string $id) {
        $datas = PenjualanDetailModel::where(
            'penjualan_id', $id
        )->with(['barang','penjualan'])->get();
        return view('penjualan.detail', [
            "data" => $datas
        ]);
    }

}
