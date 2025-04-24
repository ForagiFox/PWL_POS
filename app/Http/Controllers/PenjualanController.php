<?php

namespace App\Http\Controllers;

use App\Models\BarangModel;
use App\Models\PenjualanDetailModel;
use App\Models\PenjualanModel;
use App\Models\StokModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
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


    public function list()
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

        public function export_excel()
    {
        // Ambil data user dengan relasi level
        $users = PenjualanModel::with('user')->get();

        // Buat objek Spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header kolom
        $sheet->setCellValue('A1', 'Nama User');
        $sheet->setCellValue('B1', 'Pembeli');
        $sheet->setCellValue('C1', 'Kode Penjualan');
        $sheet->setCellValue('D1', 'Tanggal Penjualan');

        // Buat header bold
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);

        // Isi data user
        $no = 1;
        $row = 2;
        foreach ($users as $user) {
            $sheet->setCellValue('A' . $row, $user->user->nama);
            // Jika relasi level tidak ada, tampilkan kosong
            $sheet->setCellValue('B' . $row, $user->pembeli);
            $sheet->setCellValue('C' . $row, $user->penjualan_kode);
            $sheet->setCellValue('D' . $row, $user->penjualan_tanggal);
            $row++;
            $no++;
        }

        // Set auto-size untuk kolom A sampai D
        foreach (range('A', 'D') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Set judul sheet
        $sheet->setTitle('Data Penjualan');

        // Buat writer untuk file Excel
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Transaksi Penjualan ' . date('Y-m-d H:i:s') . '.xlsx';

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

    public function create()
    {
    $barang = DB::table('m_barang')
        ->join('t_stok', 'm_barang.barang_id', '=', 't_stok.barang_id')
        ->select('m_barang.barang_id', 'm_barang.barang_nama', 'm_barang.harga_jual')
        ->groupBy('m_barang.barang_id', 'm_barang.barang_nama', 'm_barang.harga_jual')
            ->get();

        return view('penjualan.create', [
            'barang' => $barang
        ]);
    }

    public function store(Request $request)
    {
        if ($request->ajax()) {
            $rules = [
                'pembeli'=>'required|string',
                'penjualan_tanggal'=>'required|date',
                'barang_id'=>'required|array|min:1',
                'barang_id.*'=>'required|exists:m_barang,barang_id',
                'jumlah'=>'required|array|min:1',
                'jumlah.*'=>'required|integer|min:1'
            ];
            $v = Validator::make($request->all(), $rules);
            if ($v->fails()) {
                return response()->json(['status'=>false,'message'=>'Validasi gagal','msgField'=>$v->errors()]);
            }

            DB::beginTransaction();

            $last = PenjualanModel::latest('penjualan_id')->first();
            $next = $last? ((int)substr($last->penjualan_kode,3)+1) : 1;
            $kode = 'P'.str_pad($next,3,'0',STR_PAD_LEFT);
            $id = DB::table('t_penjualan')->insertGetId([
                'user_id'           => auth()->id(),
                'pembeli'           => $request->pembeli,
                'penjualan_kode'    => $kode,
                'penjualan_tanggal' => $request->penjualan_tanggal,
                'created_at'        => now(),
                'updated_at'        => now()
            ]);
            $barangId = $request->input('barang_id');
            $jumlah = $request->input('jumlah');
            $hargaJual = $request->input('harga_jual');
            $detail = [];

            $subStok = DB::table('t_stok')
    ->select('barang_id', DB::raw('SUM(stok_jumlah) as total_stok'))
    ->groupBy('barang_id');

$subPenjualan = DB::table('t_penjualan_detail')
    ->select('barang_id', DB::raw('SUM(jumlah) as total_terjual'))
    ->groupBy('barang_id');
            foreach ($barangId as $i => $bId) {
                $stok = DB::table('m_barang')
    ->leftJoinSub($subStok, 's', 'm_barang.barang_id', '=', 's.barang_id')
    ->leftJoinSub($subPenjualan, 'p', 'm_barang.barang_id', '=', 'p.barang_id')
    ->select(
        'm_barang.barang_id',
        'm_barang.barang_nama',
        DB::raw('COALESCE(s.total_stok, 0) - COALESCE(p.total_terjual, 0) as total_stok')
    )
    ->where('s.barang_id',$bId)->first();
                // dd($stok);
                if($stok == null){
                    DB::rollBack();
                    return response()->json(['status'=> false,'message'=>'Stok Tidak Tersedia '.$bId]);
                }elseif($stok->total_stok <= 0){
                    DB::rollBack();
                    return response()->json(['status'=> false,'message'=>'Stok '.$stok->barang_nama.' Sedang Habis']);
                }else{
                $detail[] = [
                    'penjualan_id'=>$id,
                    'barang_id'=>$bId,
                    'harga'=>$hargaJual[$i] ?? 0,
                    'jumlah'=>$jumlah[$i],
                    'created_at'=>now(),
                    'updated_at'=>now()
                ];
                }
            }
            PenjualanDetailModel::insert($detail);
            DB::commit();
            return response()->json(['status'=>true,'message'=>'Data berhasil disimpan']);
        }
        return redirect('/');
    }

}
