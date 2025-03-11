<?php

namespace App\Http\Controllers;

use App\Models\SupplierModel;
use Illuminate\Http\Request;
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
            ->addIndexColumn()
            ->addColumn('aksi', function ($supplier) {
                $btn = '<a href="' . url('/supplier/' . $supplier->supplier_id) . '" class="btn btn-info btn-sm">Detail</a> ';
                $btn .= '<a href="' . url('/supplier/' . $supplier->supplier_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                $btn .= '<form class="d-inline-block" method="POST" action="' . url('/supplier/' . $supplier->supplier_id) . '" style="display:inline;">'
                    . csrf_field() . method_field('DELETE') .
                    '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button>
                </form>';
                return $btn;
            })
            ->rawColumns(['aksi'])
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
            'alamat' => 'required|string|max:200',
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

}
