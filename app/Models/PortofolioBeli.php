<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortofolioBeli extends Model
{
    protected $table = "portofolio_belis";
    protected $fillable = [
        'id_portofolio_beli',
        'id_saham',
        'user_id',
        'volume',
        'tanggal_beli',
        'harga_beli',
        'id_sekuritas',
    ];

    public $timestamps = false;
    protected $primaryKey = 'id_portofolio_beli';
    public function emiten()
    {
        return $this->hasMany('Saham');
    }

}
