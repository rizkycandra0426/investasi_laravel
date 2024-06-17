<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortofolioBeli extends Model
{
    protected $table = "portofolio_belis";
    protected $fillable = [
        'id_saham',
        'user_id',
        'volume_beli',
        'tanggal_beli',
        'harga_beli',
        'harga_total',
        'pembelian',
        'id_sekuritas',
    ];

    public $timestamps = false;
    protected $primaryKey = 'id_portofolio_beli';
    public function emiten()
    {
        return $this->hasMany('Saham');
    }
    public function sekuritas()
    {
        return $this->hasMany('Sekuritas');
    }

}
