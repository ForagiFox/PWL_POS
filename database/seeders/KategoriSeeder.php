<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['kategori_id' => 1, 'kategori_kode' => 'ELK', 'kategori_nama' => 'Elektronik'],
            ['kategori_id' => 2, 'kategori_kode' => 'PCK', 'kategori_nama' => 'Pakaian'],
            ['kategori_id' => 3, 'kategori_kode' => 'MKN', 'kategori_nama' => 'Makanan'],
            ['kategori_id' => 4, 'kategori_kode' => 'MNM', 'kategori_nama' => 'Minuman'],
            ['kategori_id' => 5, 'kategori_kode' => 'ATK', 'kategori_nama' => 'Alat Tulis'],
        ];
        DB::table('m_kategori')->insert($data);
    }
}
