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
                $btn = '<a href="' . url('/level/' . $user->user_id) . '" class="btn btn-info btn-sm">Detail</a> ';
                $btn .= '<a href="' . url('/level/' . $user->user_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                $btn .= '<form class="d-inline-block" method="POST" action="' . url('/user/' . $user->user_id) . '" style="display:inline;">'
                    . csrf_field() . method_field('DELETE') .
                    '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button>
                </form>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);    }

    public function create(){
        $breadcrumb = (object) [
            'title' => 'Tambah User',
            'list' => ['Home','Level','Tambah']
        ];

        $page = (object)[
            'title' => 'Tambah user baru'
        ];

        $level = LevelModel::all();
        $activeMenu = 'user';

        return view('level.create',['breadcrumb'=>$breadcrumb,'page'=>$page,'level'=>$level,'activeMenu'=>$activeMenu]);
    }

    public function store(Request $request){
        $request->validate([
            'username' => 'required|string|min:3|unique:m_user,username',
            'nama' => 'required|string|max:100',
            'password' => 'required|min:5',
            'level_id' => 'required|integer',
        ]);

        UserModel::create([
            'username' => $request->username,
            'nama' => $request->nama,
            'password' => $request->password,
            'level_id' => $request->level_id,
        ]);

        return redirect('/user')->with('success','Data user berhasil disimpan');
    }

    public function show(string $id){

        $user = UserModel::with('level')->find($id);

        $breadcrumb = (object) [
            'title' => 'Daftar Level',
            'list' => ['Home','Level']
        ];

        $page = (object)[
            'title' => 'Daftar user yang terdaftar dalam sistem'
        ];

        $activeMenu = 'user';

        return view('level.show',['breadcrumb' => $breadcrumb, 'page' => $page,'user' => $user, 'activeMenu' => $activeMenu]);
    }

    public function edit(string $id){
        $breadcrumb = (object) [
            'title' => 'Tambah Level',
            'list' => ['Home','Level','Edit']
        ];

        $page = (object)[
            'title' => 'Edit user'
        ];
        $user= UserModel::find($id);
        $level = LevelModel::all();
        $activeMenu = 'user';

        return view('level.edit',['breadcrumb'=>$breadcrumb,'user'=>$user,'page'=>$page,'level'=>$level,'activeMenu'=>$activeMenu]);
    }

    public function update(Request $request,string $id){
        $request->validate([
            'username' => 'required|string|min:3|unique:m_user,username,'.$id.',user_id',
            'nama' => 'required|string|max:100',
            'password' => 'nullable|min:5',
            'level_id' => 'required|integer',
        ]);

        UserModel::find($id)->create([
            'username' => $request->username,
            'nama' => $request->nama,
            'password' => $request->password ? bcrypt($request->password): UserModel::find($id)->password,
            'level_id' => $request->level_id,
        ]);

        return redirect('/user')->with('success','Data user berhasil disimpan');
    }
    public function destroy(string $id){
        $check = UserModel::find($id);

        if (!$check) {
            return redirect('/user')->with('error','Data user tidak ditemukan');
        }

        try{
            UserModel::destroy($id);
            return redirect('/user')->with('success','Data user berhasil dihapus');
        }catch (\Illuminate\Database\QueryException $e){
            return redirect('/user')->with('error','Data user gagal dihapus karena masih terdapat tabel lain terkait dengan data ini');
        }
    }

}
