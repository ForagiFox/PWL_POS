<?php

namespace App\Http\Controllers;

use App\Models\SupplierModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class SupplierController extends Controller
{
    public function index(){
        $breadcrumb = (object) [
            'title' => 'Daftar Supplier',
            'list' => ['Home','Supplier']
        ];

        $page = (object)[
            'title' => 'Daftar supplier yang terdaftar dalam sistem'
        ];

        $activeMenu = 'supplier';

        return view('supplier.index',['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu]);
    }

    public function list(Request $request)
    {
       $supplier = SupplierModel::select('supplier_id','supplier_kode','supplier_nama','supplier_alamat');

        return DataTables::of($supplier)
            ->addIndexColumn() // Menambahkan kolom index / nomor urut
            ->addColumn("aksi", function ($user) {
                $btn =
                    '<button onclick="modalAction(\'' .
                    url("/supplier/" . $user->supplier_id . "/show_ajax") .
                    '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .=
                    '<button onclick="modalAction(\'' .
                    url("/supplier/" . $user->supplier_id . "/edit_ajax") .
                    '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .=
                    '<button onclick="modalAction(\'' .
                    url("/supplier/" . $user->supplier_id . "/delete_ajax") .
                    '\')" class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(["aksi"]) // Memberitahu bahwa kolom aksi mengandung HTML
            ->make(true);
    }

    public function create(){
        $breadcrumb = (object) [
            'title' => 'Tambah Supplier',
            'list' => ['Home','Supplier','Tambah']
        ];

        $page = (object)[
            'title' => 'Tambah supplier baru'
        ];

        $activeMenu = 'supplier';

        return view('supplier.create',['breadcrumb'=>$breadcrumb,'page'=>$page,'activeMenu'=>$activeMenu]);
    }

    public function store(Request $request){
        $request->validate([
            'kode' => 'required|string|max:6|unique:m_supplier,supplier_kode',
            'nama' => 'required|string|max:100',
            'alamat' => 'required|string|max:200',
        ]);

        SupplierModel::create([
            'supplier_kode' => $request->kode,
            'supplier_nama' => $request->nama,
            'supplier_alamat' => $request->alamat,
        ]);

        return redirect('/supplier')->with('success','Data supplier berhasil disimpan');
    }

    public function show(string $id){

        $supplier = SupplierModel::find($id);

        $breadcrumb = (object) [
            'title' => 'Daftar Supplier',
            'list' => ['Home','Supplier']
        ];

        $page = (object)[
            'title' => 'Daftar supplier yang terdaftar dalam sistem'
        ];

        $activeMenu = 'supplier';

        return view('supplier.show',['breadcrumb' => $breadcrumb, 'page' => $page,'supplier' => $supplier, 'activeMenu' => $activeMenu]);
    }

    public function edit(string $id){
        $breadcrumb = (object) [
            'title' => 'Edit Supplier',
            'list' => ['Home','Supplier','Edit']
        ];

        $page = (object)[
            'title' => 'Edit Supplier'
        ];
        $supplier = SupplierModel::find($id);
        $activeMenu = 'supplier';

        return view('supplier.edit',['breadcrumb'=>$breadcrumb,'page'=>$page,'supplier'=>$supplier,'activeMenu'=>$activeMenu]);
    }

    public function update(Request $request,string $id){
        $request->validate([
            'kode' => 'required|string|max:6',
            'nama' => 'required|string|max:100',
            'alamat' => 'required|string|max:300',
        ]);

        SupplierModel::find($id)->update([
            'supplier_kode' => $request->kode,
            'supplier_nama' => $request->nama,
        ]);

        return redirect('/supplier')->with('success','Data supplier berhasil disimpan');
    }
    public function destroy(string $id){
        $check = SupplierModel::find($id);

        if (!$check) {
            return redirect('/supplier')->with('error','Data supplier tidak ditemukan');
        }

        try{
            SupplierModel::destroy($id);
            return redirect('/supplier')->with('success','Data supplier berhasil dihapus');
        }catch (\Illuminate\Database\QueryException $e){
            return redirect('/supplier')->with('error','Data user gagal dihapus karena masih terdapat tabel lain terkait dengan data ini');
        }
    }
         public function store_ajax(Request $reqeust)
    {
        if ($reqeust->ajax() || $reqeust->wantsJson()) {
            $rules = [
                "supplier_kode" => "required|string|max:3|unique:m_supplier,supplier_kode",
                "supplier_nama" => "required|string|max:100",
                "supplier_alamat" => "required|string|max:200",
            ];

            $validator = Validator::make($reqeust->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    "status" => false,
                    "message" => "Validasi Gagal",
                    "msgField" => $validator->errors(),
                ]);
            }

            SupplierModel::create($reqeust->all());
            return response()->json([
                "status" => true,
                "message" => "Data user berhasil disimpan",
            ]);
        }
    }

    public function create_ajax()
    {

        return view("supplier.create_ajax");
    }
   public function edit_ajax(string $id)
    {
        $supplier = SupplierModel::find($id);

        return view("supplier.edit_ajax", ["supplier" => $supplier]);
    }

    public function update_ajax(Request $request, $id)
    {
        // cek apakah request dari ajax
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                "supplier_kode" =>
                    "required|max:6|unique:m_supplier,supplier_kode," .
                    $id .
                    ",supplier_id",
                "supplier_nama" => "required|max:100",
                "supplier_alamat" => "required|max:300",
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
            $check = SupplierModel::find($id);
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
        $supplier = SupplierModel::find($id);

        return view('supplier.confirm_ajax', ['supplier'=>$supplier]);

    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {

            $user = SupplierModel::find($id);
            if ($user) {
                $user->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Data Berhasil dihapus'
                ]);
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
