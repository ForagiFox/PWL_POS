<?php

namespace App\Http\Controllers;

use App\Models\LevelModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
class UserController extends Controller
{

    public function index(){
        $breadcrumb = (object) [
            'title' => 'Daftar User',
            'list' => ['Home','User']
        ];

        $page = (object)[
            'title' => 'Daftar user yang terdaftar dalam sistem'
        ];

        $activeMenu = 'user';
        $level = LevelModel::all();
        return view('user.index',['breadcrumb' => $breadcrumb,'level' => $level, 'page' => $page, 'activeMenu' => $activeMenu]);
    }

    public function store_ajax(Request $reqeust){
        if($reqeust->ajax() || $reqeust->wantsJson()){
            $rules=[
            'username' => 'required|string|min:3|unique:m_user,username',
            'nama' => 'required|string|max:100',
            'password' => 'required|min:6',
            'level_id' => 'required|integer',

            ];

            $validator = Validator::make($reqeust->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }


            UserModel::create($reqeust->all());
            return response()->json([
                'status' => true,
                'message' => 'Data user berhasil disimpan'
            ]);
        }


    }

    public function create_ajax()
    {
        $level = LevelModel::select('level_id','level_nama')->get();

        return view('user.create_ajax')->with('level',$level);
    }
    public function list(Request $request)
    {
        $users = UserModel::select('user_id','username','nama','level_id')
            ->with('level');

        if ($request->level_id) {
            $users->where('level_id',$request->level_id);
        }

       return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('aksi', function ($user) {
                $btn = '<a href="' . url('/user/' . $user->user_id) . '" class="btn btn-info btn-sm">Detail</a> ';
                $btn .= '<a href="' . url('/user/' . $user->user_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
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
            'list' => ['Home','User','Tambah']
        ];

        $page = (object)[
            'title' => 'Tambah user baru'
        ];

        $level = LevelModel::all();
        $activeMenu = 'user';

        return view('user.create',['breadcrumb'=>$breadcrumb,'page'=>$page,'level'=>$level,'activeMenu'=>$activeMenu]);
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
            'title' => 'Daftar User',
            'list' => ['Home','User']
        ];

        $page = (object)[
            'title' => 'Daftar user yang terdaftar dalam sistem'
        ];

        $activeMenu = 'user';

        return view('user.show',['breadcrumb' => $breadcrumb, 'page' => $page,'user' => $user, 'activeMenu' => $activeMenu]);
    }

    public function edit(string $id){
        $breadcrumb = (object) [
            'title' => 'Tambah User',
            'list' => ['Home','User','Edit']
        ];

        $page = (object)[
            'title' => 'Edit user'
        ];
        $user= UserModel::find($id);
        $level = LevelModel::all();
        $activeMenu = 'user';

        return view('user.edit',['breadcrumb'=>$breadcrumb,'user'=>$user,'page'=>$page,'level'=>$level,'activeMenu'=>$activeMenu]);
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
