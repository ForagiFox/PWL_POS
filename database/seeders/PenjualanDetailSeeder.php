<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenjualanDetailSeeder extends Seeder
{
    public function run(): void
    {
        $data = [];
        $detail_id = 1;

        for ($i = 1; $i <= 10; $i++) {
            $used_barang = [];
            for ($j = 0; $j < 3; $j++) {
                do {
                    $barang_id = rand(1, 15);
                } while (in_array($barang_id, $used_barang));

                $used_barang[] = $barang_id;

                $barang = DB::table('m_barang')->where('barang_id', $barang_id)->first();

                $data[] = [
                    'detail_id' => $detail_id++,
                    'penjualan_id' => $i,
                    'barang_id' => $barang_id,
                    'harga' => $barang->harga_jual,
                    'jumlah' => rand(1, 5),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        DB::table('t_penjualan_detail')->insert($data);
    }
}
