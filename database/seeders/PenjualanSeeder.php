<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenjualanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [];

        for ($i = 11; $i <= 22; $i++) {
            $data[] = [
                'penjualan_id' => $i,
                'penjualan_kode' => 'P' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'user_id' => 3,
                'pembeli' => 'Customer ' . $i,
                'penjualan_tanggal' => Carbon::create(now()->year, $i, rand(1, 28)), // Tanggal acak per bulan
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('t_penjualan')->insert($data);
    }
}
