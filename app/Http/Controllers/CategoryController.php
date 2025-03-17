<?php

namespace App\Http\Controllers;

use App\Models\CategoryModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class CategoryController extends Controller
{
    public function index(){
        $breadcrumb = (object) [
            'title' => 'Daftar Kategori',
            'list' => ['Home','Category']
        ];

        $page = (object)[
            'title' => 'Daftar kategori yang terdaftar dalam sistem'
        ];

        $activeMenu = 'kategori';

        return view('category.index',['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu]);
    }

    public function list(Request $request)
    {
       $kategori = CategoryModel::select('kategori_id','kategori_kode','kategori_nama');

        return DataTables::of($kategori)
            ->addIndexColumn() // Menambahkan kolom index / nomor urut
            ->addColumn("aksi", function ($user) {
                $btn =
                    '<button onclick="modalAction(\'' .
                    url("/kategori/" . $user->kategori_id . "/show_ajax") .
                    '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .=
                    '<button onclick="modalAction(\'' .
                    url("/kategori/" . $user->kategori_id . "/edit_ajax") .
                    '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .=
                    '<button onclick="modalAction(\'' .
                    url("/kategori/" . $user->kategori_id . "/delete_ajax") .
                    '\')" class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(["aksi"]) // Memberitahu bahwa kolom aksi mengandung HTML
            ->make(true);
    }

    public function create(){
        $breadcrumb = (object) [
            'title' => 'Tambah Kategori',
            'list' => ['Home','Category','Tambah']
        ];

        $page = (object)[
            'title' => 'Tambah kategori baru'
        ];

        $activeMenu = 'kategori';

        return view('category.create',['breadcrumb'=>$breadcrumb,'page'=>$page,'activeMenu'=>$activeMenu]);
    }

    public function store(Request $request){
        $request->validate([
            'kode' => 'required|string|max:3|unique:m_kategori,kategori_kode',
            'nama' => 'required|string|max:100',
        ]);

        CategoryModel::create([
            'kategori_kode' => $request->kode,
            'kategori_nama' => $request->nama,
        ]);

        return redirect('/kategori')->with('success','Data kategori berhasil disimpan');
    }

    public function show(string $id){

        $kategori = CategoryModel::find($id);

        $breadcrumb = (object) [
            'title' => 'Daftar Kategori',
            'list' => ['Home','Category']
        ];

        $page = (object)[
            'title' => 'Daftar kategori yang terdaftar dalam sistem'
        ];

        $activeMenu = 'kategori';

        return view('category.show',['breadcrumb' => $breadcrumb, 'page' => $page,'kategori' => $kategori, 'activeMenu' => $activeMenu]);
    }

    public function edit(string $id){
        $breadcrumb = (object) [
            'title' => 'Edit Kategori',
            'list' => ['Home','Category','Edit']
        ];

        $page = (object)[
            'title' => 'Edit kategori'
        ];
        $kategori = CategoryModel::find($id);
        $activeMenu = 'kategori';

        return view('category.edit',['breadcrumb'=>$breadcrumb,'page'=>$page,'kategori'=>$kategori,'activeMenu'=>$activeMenu]);
    }

    public function update(Request $request,string $id){
        $request->validate([
            'kode' => 'required|string|max:3',
            'nama' => 'required|string|max:100',
        ]);

        CategoryModel::find($id)->update([
            'kategori_kode' => $request->kode,
            'kategori_nama' => $request->nama,
        ]);

        return redirect('/kategori')->with('success','Data kategori berhasil disimpan');
    }
    public function destroy(string $id){
        $check = CategoryModel::find($id);

        if (!$check) {
            return redirect('/kategori')->with('error','Data kategori tidak ditemukan');
        }

        try{
            CategoryModel::destroy($id);
            return redirect('/kategori')->with('success','Data kategori berhasil dihapus');
        }catch (\Illuminate\Database\QueryException $e){
            return redirect('/kategori')->with('error','Data user gagal dihapus karena masih terdapat tabel lain terkait dengan data ini');
        }
    }

             public function store_ajax(Request $reqeust)
    {
        if ($reqeust->ajax() || $reqeust->wantsJson()) {
            $rules = [
                "kategori_kode" => "required|string|max:3|unique:m_kategori,kategori_kode",
                "kategori_nama" => "required|string|max:100",
            ];

            $validator = Validator::make($reqeust->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    "status" => false,
                    "message" => "Validasi Gagal",
                    "msgField" => $validator->errors(),
                ]);
            }

            CategoryModel::create($reqeust->all());
            return response()->json([
                "status" => true,
                "message" => "Data user berhasil disimpan",
            ]);
        }
    }

    public function create_ajax()
    {

        return view("category.create_ajax");
    }
   public function edit_ajax(string $id)
    {
        $kategori = CategoryModel::find($id);

        return view("category.edit_ajax", ["kategori" => $kategori]);
    }

    public function update_ajax(Request $request, $id)
    {
        // cek apakah request dari ajax
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                "kategori_kode" =>
                    "required|max:3|unique:m_kategori,kategori_kode," .
                    $id .
                    ",kategori_id",
                "kategori_nama" => "required|max:100",
            ];
            // use Illuminate\Support\Facades\Validator;
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    "status" => false, // respon json, true: berhasil, false: gagal
                    "message" => "Validasi gagal.",
                    "msgField" => $validator->errors(), // menunjukkan field mana yang error
                ]);
            }
            $check = CategoryModel::find($id);
            if ($check) {

                $check->update($request->all());
                return response()->json([
                    "status" => true,
                    "message" => "Data berhasil diupdate",
                ]);
            } else {
                return response()->json([
                    "status" => false,
                    "message" => "Data tidak ditemukan",
                ]);
            }
        }
        return redirect("/");
    }

    public function confirm_ajax(string $id) {
        $kategori = CategoryModel::find($id);

        return view('category.confirm_ajax', ['kategori'=>$kategori]);

    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {

            $user = CategoryModel::find($id);
            if ($user) {
                 try{
                $user->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Data Berhasil dihapus'
                    ]);

                }catch(\Illuminate\Database\QueryException $e){
                    return response()->json([
                    'status' => false,
                    'message' => 'Terjadi Kesalahan'
                    ]);
                }
            }else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        }
        return redirect('/');

    }


}
