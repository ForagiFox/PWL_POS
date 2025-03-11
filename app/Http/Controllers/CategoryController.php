<?php

namespace App\Http\Controllers;

use App\Models\CategoryModel;
use Illuminate\Http\Request;
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
            ->addIndexColumn()
            ->addColumn('aksi', function ($kategori) {
                $btn = '<a href="' . url('/kategori/' . $kategori->kategori_id) . '" class="btn btn-info btn-sm">Detail</a> ';
                $btn .= '<a href="' . url('/kategori/' . $kategori->kategori_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                $btn .= '<form class="d-inline-block" method="POST" action="' . url('/kategori/' . $kategori->kategori_id) . '" style="display:inline;">'
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
            return redirect('/kategori')->with('success','Data level berhasil dihapus');
        }catch (\Illuminate\Database\QueryException $e){
            return redirect('/kategori')->with('error','Data user gagal dihapus karena masih terdapat tabel lain terkait dengan data ini');
        }
    }
}
