<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
class PenjualanModel extends Model
{
    use HasFactory;
    protected $table = 't_penjualan';
    protected $primaryKey = 'penjualan_id';
    protected $fillable = [
        'user_id',
        'pembeli',
        'penjualan_kode',
        'penjualan_tanggal',
        'images',
    ];

    public function user(): BelongsTo{
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }

    public function detail(){
        return $this->hasMany(PenjualanDetailModel::class, 'penjualan_id', 'penjualan_id');
    }
    protected  function images(): Attribute
    {
        return Attribute::make(
            get: fn ($images) => url('storage/posts/' . $images),
        );
    }
}
