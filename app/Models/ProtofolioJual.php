<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProtofolioJual extends Model
{
    protected $table = "portofolio_juals";
    protected $fillable = [
        'id_portofolio_jual',
        'id_saham',
        'user_id',
        'volume',
        'tanggal_jual',
        'harga_jual',
        'id_sekuritas',
    ];

    public $timestamps = false;
    protected $primaryKey = 'id_portofolio_jual';
    public function emiten()
    {
        return $this->hasMany('Saham');
    }
}
