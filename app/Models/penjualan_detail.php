<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class penjualan_detail extends Model
{
    use HasFactory;
    protected $table = 't_penjualan_detail';
    protected $primaryKey = 'detail_id';
    protected $fillable = [
        'pejualan_id',
        'barang_id',
        'harga',
        'jumlah',
    ];
}
