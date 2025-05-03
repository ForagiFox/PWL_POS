<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // Elektronik dan ATK
            ['barang_id' => 1, 'kategori_id' => 1, 'barang_kode' => 'BRG001', 'barang_nama' => 'Laptop Acer', 'harga_beli' => 8000000, 'harga_jual' => 9000000],
            ['barang_id' => 2, 'kategori_id' => 1, 'barang_kode' => 'BRG002', 'barang_nama' => 'Printer Epson', 'harga_beli' => 2000000, 'harga_jual' => 2500000],
            ['barang_id' => 3, 'kategori_id' => 5, 'barang_kode' => 'BRG003', 'barang_nama' => 'Pensil 2B', 'harga_beli' => 3000, 'harga_jual' => 5000],
            ['barang_id' => 4, 'kategori_id' => 5, 'barang_kode' => 'BRG004', 'barang_nama' => 'Buku Tulis', 'harga_beli' => 20000, 'harga_jual' => 25000],
            ['barang_id' => 5, 'kategori_id' => 5, 'barang_kode' => 'BRG005', 'barang_nama' => 'Spidol', 'harga_beli' => 8000, 'harga_jual' => 12000],
            
            // Pakaian
            ['barang_id' => 6, 'kategori_id' => 2, 'barang_kode' => 'BRG006', 'barang_nama' => 'Kemeja Putih', 'harga_beli' => 150000, 'harga_jual' => 200000],
            ['barang_id' => 7, 'kategori_id' => 2, 'barang_kode' => 'BRG007', 'barang_nama' => 'Celana Jeans', 'harga_beli' => 200000, 'harga_jual' => 250000],
            ['barang_id' => 8, 'kategori_id' => 2, 'barang_kode' => 'BRG008', 'barang_nama' => 'Kaos Polos', 'harga_beli' => 50000, 'harga_jual' => 75000],
            ['barang_id' => 9, 'kategori_id' => 2, 'barang_kode' => 'BRG009', 'barang_nama' => 'Jaket Hoodie', 'harga_beli' => 150000, 'harga_jual' => 200000],
            ['barang_id' => 10, 'kategori_id' => 2, 'barang_kode' => 'BRG010', 'barang_nama' => 'Topi Baseball', 'harga_beli' => 40000, 'harga_jual' => 60000],
            
            // Makanan dan Minuman
            ['barang_id' => 11, 'kategori_id' => 3, 'barang_kode' => 'BRG011', 'barang_nama' => 'Snack Pack', 'harga_beli' => 15000, 'harga_jual' => 20000],
            ['barang_id' => 12, 'kategori_id' => 3, 'barang_kode' => 'BRG012', 'barang_nama' => 'Biskuit', 'harga_beli' => 8000, 'harga_jual' => 10000],
            ['barang_id' => 13, 'kategori_id' => 4, 'barang_kode' => 'BRG013', 'barang_nama' => 'Air Mineral', 'harga_beli' => 3000, 'harga_jual' => 5000],
            ['barang_id' => 14, 'kategori_id' => 4, 'barang_kode' => 'BRG014', 'barang_nama' => 'Soda Kaleng', 'harga_beli' => 5000, 'harga_jual' => 8000],
            ['barang_id' => 15, 'kategori_id' => 4, 'barang_kode' => 'BRG015', 'barang_nama' => 'Jus Buah', 'harga_beli' => 6000, 'harga_jual' => 10000],
        ];
        DB::table('m_barang')->insert($data);
    }
}
