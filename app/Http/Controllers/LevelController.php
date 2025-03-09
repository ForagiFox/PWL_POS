<?php

namespace App\Http\Controllers;

use App\Models\LevelModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class LevelController extends Controller
{
    public function index(){
        $breadcrumb = (object) [
            'title' => 'Daftar User',
            'list' => ['Home','Level']
        ];

        $page = (object)[
            'title' => 'Daftar user yang terdaftar dalam sistem'
        ];

        $activeMenu = 'level';

        return view('level.index',['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu]);
    }

    public function list(Request $request)
    {
        $levels = LevelModel::select('level_id','level_kode','level_nama');

       return DataTables::of($levels)
            ->addIndexColumn()
            ->addColumn('aksi', function ($user) {
                $btn = '<a href="' . url('/level/' . $user->level_id) . '" class="btn btn-info btn-sm">Detail</a> ';
                $btn .= '<a href="' . url('/level/' . $user->level_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                $btn .= '<form class="d-inline-block" method="POST" action="' . url('/level/' . $user->level_id) . '" style="display:inline;">'
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
            'title' => 'Tambah User',
            'list' => ['Home','Level','Tambah']
        ];

        $page = (object)[
            'title' => 'Tambah user baru'
        ];

        $activeMenu = 'user';

        return view('level.create',['breadcrumb'=>$breadcrumb,'page'=>$page,'activeMenu'=>$activeMenu]);
    }

    public function store(Request $request){
        $request->validate([
            'kode' => 'required|string|max:3|unique:m_level,level_kode',
            'nama' => 'required|string|max:100',
        ]);

        LevelModel::create([
            'level_kode' => $request->kode,
            'level_nama' => $request->nama,
        ]);

        return redirect('/level')->with('success','Data level berhasil disimpan');
    }

    public function show(string $id){

        $level = LevelModel::find($id);

        $breadcrumb = (object) [
            'title' => 'Daftar Level',
            'list' => ['Home','Level']
        ];

        $page = (object)[
            'title' => 'Daftar user yang terdaftar dalam sistem'
        ];

        $activeMenu = 'level';

        return view('level.show',['breadcrumb' => $breadcrumb, 'page' => $page,'level' => $level, 'activeMenu' => $activeMenu]);
    }

    public function edit(string $id){
        $breadcrumb = (object) [
            'title' => 'Tambah Level',
            'list' => ['Home','Level','Edit']
        ];

        $page = (object)[
            'title' => 'Edit user'
        ];
        $level = LevelModel::find($id);
        $activeMenu = 'user';

        return view('level.edit',['breadcrumb'=>$breadcrumb,'page'=>$page,'level'=>$level,'activeMenu'=>$activeMenu]);
    }

    public function update(Request $request,string $id){
        $request->validate([
            'kode' => 'required|string|max:3|unique:m_level,level_kode',
            'nama' => 'required|string|max:100',
        ]);

        LevelModel::find($id)->update([
            'level_kode' => $request->kode,
            'level_nama' => $request->nama,
        ]);

        return redirect('/level')->with('success','Data level berhasil disimpan');
    }
    public function destroy(string $id){
        $check = LevelModel::find($id);

        if (!$check) {
            return redirect('/level')->with('error','Data level tidak ditemukan');
        }

        try{
            LevelModel::destroy($id);
            return redirect('/level')->with('success','Data level berhasil dihapus');
        }catch (\Illuminate\Database\QueryException $e){
            return redirect('/level')->with('error','Data user gagal dihapus karena masih terdapat tabel lain terkait dengan data ini');
        }
    }

}
