<?php

namespace App\Http\Controllers;

use App\Models\LevelModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Illuminate\Support\Facades\Hash;
// use function Laravel\Prompts\select;
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
    {    // Jika bukan admin, hanya boleh mengedit dirinya sendiri
    $user = auth()->user();

    // Jika bukan ADM/MNG dan bukan diri sendiri
    if (!in_array($user->level->level_kode, ['ADM', 'MNG']) && $user->user_id != $id) {
        abort(403); // Forbidden
    }else{
        $user = UserModel::find($id);
        $level = LevelModel::select("level_id", "level_nama")->get();

            return view("user.edit_ajax", ["user" => $user, "level" => $level]);
    }
    }

public function update_ajax(Request $request, $id)
{
    if ($request->ajax() || $request->wantsJson()) {

        $rules = [
            "level_id" => "nullable|integer",
            "username" => "required|max:20|unique:m_user,username," . $id . ",user_id",
            "nama" => "required|max:100",
            "password" => "nullable|min:6|max:20",
            "photo"    => "nullable|mimes:jpg,jpeg,png|max:2048"
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                "status"   => false,
                "message"  => "Validasi gagal.",
                "msgField" => $validator->errors(),
            ]);
        }

        $check = UserModel::find($id);
        if ($check) {
            // Ambil data kecuali field photo (karena akan kita proses terpisah)
            $data = $request->except("photo");

            // Proses upload file foto jika ada
            if ($request->hasFile("photo")) {
                $foto    = $request->file("photo");
                $namaFoto = time() . '_' . uniqid() . '.' . $foto->getClientOriginalExtension();
                $foto->move(public_path('uploads/foto_user'), $namaFoto);

                // Hapus foto lama jika ada
                if ($check->photo && file_exists(public_path('uploads/foto_user/' . $check->photo))) {
                    unlink(public_path('uploads/foto_user/' . $check->photo));
                }

                $data["photo"] = $namaFoto;
            }

            // Jika password tidak diisi maka hapus dari data update agar tidak diupdate sebagai string kosong
            if (!$request->filled("password")) {
                unset($data["password"]);
            }

            $check->update($data);
            return response()->json([
                "status"  => true,
                "message" => "Data berhasil diupdate",
            ]);
        } else {
            return response()->json([
                "status"  => false,
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
    public function import()
    {
        return view('user.import');
    }

    /**
     * Memproses file import user via AJAX.
     */
    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {

            // Validasi file: harus .xlsx dengan ukuran maksimal 2MB
            $rules = [
                'file_user' => ['required', 'mimes:xlsx', 'max:2048'],
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status'   => false,
                    'message'  => 'Validasi Gagal.' . "\n" . 'Mohon ikuti instruksi di template.',
                    'msgField' => $validator->errors(),
                ]);
            }

            try {
                $file = $request->file('file_user');
                $reader = IOFactory::createReader('Xlsx');
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();
                $data = $sheet->toArray(null, true, true, true);

                // Pastikan ada data minimal (header + 1 baris data)
                if (count($data) <= 1) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Tidak ada data yang diimport.' . "\n" . 'Mohon ikuti instruksi di template.',
                    ]);
                }

                // Validasi header file
                $headerA = strtolower(str_replace(' ', '_', trim($data[1]['A'] ?? '')));
                $headerB = strtolower(str_replace(' ', '_', trim($data[1]['B'] ?? '')));
                $headerC = strtolower(str_replace(' ', '_', trim($data[1]['C'] ?? '')));
                $headerD = strtolower(str_replace(' ', '_', trim($data[1]['D'] ?? '')));
                $expectedHeader = ['level_id', 'username', 'nama', 'password'];
                if (!($headerA === $expectedHeader[0] &&
                    $headerB === $expectedHeader[1] &&
                    $headerC === $expectedHeader[2] &&
                    $headerD === $expectedHeader[3])) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Header file Excel tidak sesuai. Pastikan kolom A sampai D berturut-turut: ' .
                            implode(', ', $expectedHeader) . '.' . "\n" . 'Mohon ikuti instruksi di template.',
                    ]);
                }

                $insert = [];
                foreach ($data as $rowIndex => $rowValue) {
                    if ($rowIndex == 1) {
                        continue; // Lewati header
                    }

                    $levelId  = trim($rowValue['A'] ?? '');
                    $username = trim($rowValue['B'] ?? '');
                    $nama     = trim($rowValue['C'] ?? '');
                    $password = trim($rowValue['D'] ?? '');

                    // Jika seluruh kolom kosong, lewati baris tersebut (misalnya, baris kosong di akhir file)
                    if ($levelId === '' && $username === '' && $nama === '' && $password === '') {
                        continue;
                    }

                    // Jika hanya sebagian kolom kosong, return error
                    if ($levelId === '' || $username === '' || $nama === '' || $password === '') {
                        return response()->json([
                            'status'  => false,
                            'message' => "Data pada baris {$rowIndex} tidak lengkap. Semua kolom wajib diisi." . "\n" . 'Mohon ikuti instruksi di template.',
                        ]);
                    }

                    // Validasi: level_id harus ada di tabel level
                    if (!LevelModel::where('level_id', $levelId)->exists()) {
                        return response()->json([
                            'status'  => false,
                            'message' => "Data pada baris {$rowIndex}: Level dengan ID '{$levelId}' tidak ditemukan." . "\n" . 'Mohon ikuti instruksi di template.',
                        ]);
                    }

                    // Cek duplikat berdasarkan username
                    $existing = UserModel::where('username', $username)->first();
                    if ($existing) {
                        return response()->json([
                            'status'  => false,
                            'message' => "Data pada baris {$rowIndex}: User dengan username '{$username}' sudah ada." . "\n" . 'Mohon ikuti instruksi di template.',
                        ]);
                    }

                    $insert[] = [
                        'level_id'   => $levelId,
                        'username'   => $username,
                        'nama'       => $nama,
                        'password'   => Hash::make($password),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if (count($insert) > 0) {
                    UserModel::insert($insert);
                    return response()->json([
                        'status'  => true,
                        'message' => 'Data berhasil diimport',
                    ]);
                } else {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Tidak ada data valid yang diimport.' . "\n" . 'Mohon ikuti instruksi di template.',
                    ]);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Terjadi kesalahan saat memproses file: ' . $e->getMessage() .
                        "\n" . 'Mohon ikuti instruksi di template.',
                ]);
            }
        }

        return redirect('/');
    }

    public function export_excel()
    {
        // Ambil data user dengan relasi level
        $users = UserModel::with('level')->orderBy('username', 'asc')->get();

        // Buat objek Spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header kolom
        $sheet->setCellValue('A1', 'level_id');
        $sheet->setCellValue('B1', 'username');
        $sheet->setCellValue('C1', 'nama');
        $sheet->setCellValue('D1', 'password');

        // Buat header bold
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);

        // Isi data user
        $no = 1;
        $row = 2;
        foreach ($users as $user) {
            $sheet->setCellValue('A' . $row, $user->level->level_id);
            // Jika relasi level tidak ada, tampilkan kosong
            $sheet->setCellValue('B' . $row, $user->username);
            $sheet->setCellValue('C' . $row, $user->nama);
            $sheet->setCellValue('D' . $row, $user->password);
            $row++;
            $no++;
        }

        // Set auto-size untuk kolom A sampai D
        foreach (range('A', 'D') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Set judul sheet
        $sheet->setTitle('Data User');

        // Buat writer untuk file Excel
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data User ' . date('Y-m-d H:i:s') . '.xlsx';

        // Set header HTTP untuk file download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        // Output file Excel ke browser
        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        // Ambil data user beserta relasi level
        $users = UserModel::with('level')->orderBy('username', 'asc')->get();

        // Muat view export PDF dengan data user
        $pdf = Pdf::loadView('user.export_pdf', ['users' => $users]);

        // Atur ukuran kertas dan orientasi
        $pdf->setPaper('a4', 'portrait');
        // Aktifkan opsi remote jika ada gambar dari URL
        $pdf->setOption("isRemoteEnabled", true);

        return $pdf->stream('Data User ' . date('Y-m-d H:i:s') . '.pdf');
    }
}

