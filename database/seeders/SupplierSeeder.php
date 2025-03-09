<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['supplier_id' => 1, 'supplier_kode' => 'SUP001', 'supplier_nama' => 'PT Supplier Sejahtera', 'supplier_alamat' => 'Jl. Merdeka No. 123'],
            ['supplier_id' => 2, 'supplier_kode' => 'SUP002', 'supplier_nama' => 'CV Makmur Jaya', 'supplier_alamat' => 'Jl. Raya Darmo No. 45'],
            ['supplier_id' => 3, 'supplier_kode' => 'SUP003', 'supplier_nama' => 'UD Berkah Abadi', 'supplier_alamat' => 'Jl. Pahlawan No. 67'],
        ];
        DB::table('m_supplier')->insert($data);
    }
}
