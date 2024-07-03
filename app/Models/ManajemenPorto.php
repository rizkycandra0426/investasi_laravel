<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManajemenPorto extends Model
{
    use HasFactory;

    protected $table = 'manajemen_portos';
    protected $primaryKey = 'id_manajemen_porto';

    protected $fillable = [
        'user_id',
        'valuasi_awal',
        'harga_unit_awal',
        'jumlah_unit_awal',
        'valuasi_saat_ini',
        'jumlah_unit_penyertaan',
        'harga_unit',
        'yield',
        'ihsg_start',
        'ihsg_end',
        'yield_ihsg',
    ];
}
