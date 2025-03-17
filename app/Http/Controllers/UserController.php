<?php

namespace App\Http\Controllers;

use App\Models\LevelModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use function Laravel\Prompts\select;
class UserController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            "title" => "Daftar User",
            "list" => ["Home", "User"],
        ];

        $page = (object) [
            "title" => "Daftar user yang terdaftar dalam sistem",
        ];

        $activeMenu = "user";
        $level = LevelModel::all();
        return view("user.index", [
            "breadcrumb" => $breadcrumb,
            "level" => $level,
            "page" => $page,
            "activeMenu" => $activeMenu,
        ]);
    }

    public function list(Request $request)
    {
        $users = UserModel::select(
            "user_id",
            "username",
            "nama",
            "level_id"
        )->with("level");

        // Filter data user berdasarkan level_id
        if ($request->level_id) {
            $users->where("level_id", $request->level_id);
        }

        return DataTables::of($users)
            ->addIndexColumn() // Menambahkan kolom index / nomor urut
            ->addColumn("aksi", function ($user) {
                $btn =
                    '<button onclick="modalAction(\'' .
                    url("/user/" . $user->user_id . "/show_ajax") .
                    '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .=
                    '<button onclick="modalAction(\'' .
                    url("/user/" . $user->user_id . "/edit_ajax") .
                    '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .=
                    '<button onclick="modalAction(\'' .
                    url("/user/" . $user->user_id . "/delete_ajax") .
                    '\')" class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(["aksi"]) // Memberitahu bahwa kolom aksi mengandung HTML
            ->make(true);
    }

     public function store_ajax(Request $reqeust)
    {
        if ($reqeust->ajax() || $reqeust->wantsJson()) {
            $rules = [
                "username" => "required|string|min:3|unique:m_user,username",
                "nama" => "required|string|max:100",
                "password" => "required|min:6",
                "level_id" => "required|integer",
            ];

            $validator = Validator::make($reqeust->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    "status" => false,
                    "message" => "Validasi Gagal",
                    "msgField" => $validator->errors(),
                ]);
            }

            UserModel::create($reqeust->all());
            return response()->json([
                "status" => true,
                "message" => "Data user berhasil disimpan",
            ]);
        }
    }

    public function create_ajax()
    {
        $level = LevelModel::select("level_id", "level_nama")->get();

        return view("user.create_ajax")->with("level", $level);
    }
   public function edit_ajax(string $id)
    {
        $user = UserModel::find($id);
        $level = LevelModel::select("level_id", "level_nama")->get();

        return view("user.edit_ajax", ["user" => $user, "level" => $level]);
    }

    public function update_ajax(Request $request, $id)
    {
        // cek apakah request dari ajax
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                "level_id" => "required|integer",
                "username" =>
                    "required|max:20|unique:m_user,username," .
                    $id .
                    ",user_id",
                "nama" => "required|max:100",
                "password" => "nullable|min:6|max:20",
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
            $check = UserModel::find($id);
            if ($check) {
                if (!$request->filled("password")) {
                    // jika password tidak diisi, maka hapus dari
                    $request->request->remove("password");
                }
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
        $user = UserModel::find($id);

        return view('user.confirm_ajax', ['user'=>$user]);

    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {

            $user = UserModel::find($id);
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
                    'message' => 'Terjadi Kesalahan'.$e
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

    public function create()
    {
        $breadcrumb = (object) [
            "title" => "Tambah User",
            "list" => ["Home", "User", "Tambah"],
        ];

        $page = (object) [
            "title" => "Tambah user baru",
        ];

        $level = LevelModel::all();
        $activeMenu = "user";

        return view("user.create", [
            "breadcrumb" => $breadcrumb,
            "page" => $page,
            "level" => $level,
            "activeMenu" => $activeMenu,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            "username" => "required|string|min:3|unique:m_user,username",
            "nama" => "required|string|max:100",
            "password" => "required|min:5",
            "level_id" => "required|integer",
        ]);

        UserModel::create([
            "username" => $request->username,
            "nama" => $request->nama,
            "password" => $request->password,
            "level_id" => $request->level_id,
        ]);

        return redirect("/user")->with(
            "success",
            "Data user berhasil disimpan"
        );
    }

    public function show(string $id)
    {
        $user = UserModel::with("level")->find($id);

        $breadcrumb = (object) [
            "title" => "Daftar User",
            "list" => ["Home", "User"],
        ];

        $page = (object) [
            "title" => "Daftar user yang terdaftar dalam sistem",
        ];

        $activeMenu = "user";

        return view("user.show", [
            "breadcrumb" => $breadcrumb,
            "page" => $page,
            "user" => $user,
            "activeMenu" => $activeMenu,
        ]);
    }

    public function edit(string $id)
    {
        $breadcrumb = (object) [
            "title" => "Tambah User",
            "list" => ["Home", "User", "Edit"],
        ];

        $page = (object) [
            "title" => "Edit user",
        ];
        $user = UserModel::find($id);
        $level = LevelModel::all();
        $activeMenu = "user";

        return view("user.edit", [
            "breadcrumb" => $breadcrumb,
            "user" => $user,
            "page" => $page,
            "level" => $level,
            "activeMenu" => $activeMenu,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            "username" =>
                "required|string|min:3|unique:m_user,username," .
                $id .
                ",user_id",
            "nama" => "required|string|max:100",
            "password" => "nullable|min:5",
            "level_id" => "required|integer",
        ]);

        UserModel::find($id)->create([
            "username" => $request->username,
            "nama" => $request->nama,
            "password" => $request->password
                ? bcrypt($request->password)
                : UserModel::find($id)->password,
            "level_id" => $request->level_id,
        ]);

        return redirect("/user")->with(
            "success",
            "Data user berhasil disimpan"
        );
    }
    public function destroy(string $id)
    {
        $check = UserModel::find($id);

        if (!$check) {
            return redirect("/user")->with(
                "error",
                "Data user tidak ditemukan"
            );
        }

        try {
            UserModel::destroy($id);
            return redirect("/user")->with(
                "success",
                "Data user berhasil dihapus"
            );
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect("/user")->with(
                "error",
                "Data user gagal dihapus karena masih terdapat tabel lain terkait dengan data ini"
            );
        }
    }


}

