<?php

namespace App\Http\Controllers;

use App\Models\PenjualanDetailModel;
use App\Models\PenjualanModel;
use Illuminate\Http\Request;
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


    public function list(Request $request)
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


}
