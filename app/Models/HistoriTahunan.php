<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriTahunan extends Model
{
    use HasFactory;
    protected $table = "histori_tahunan";
    protected $primaryKey = 'id';
    protected $guarded = [];
}
