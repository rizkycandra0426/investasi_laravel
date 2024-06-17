<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortofolioJual extends Model
{
    protected $table = "portofolio_juals";
    protected $fillable = [
        'id_saham',
        'user_id',
        'volume_jual',
        'tanggal_jual',
        'harga_jual',
        'harga_total',
        'penjualan',
        'id_sekuritas',
    ];

    public $timestamps = false;
    protected $primaryKey = 'id_portofolio_jual';
    public function emiten()
    {
        return $this->hasMany('Saham');
    }
    public function sekuritas()
    {
        return $this->hasMany('Sekuritas');
    }
}
php artisan migrate:refresh --path=/database/migrations/11_2024_06_04_072742_create_portofolio_juals_table.php