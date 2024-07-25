<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ihsg extends Model
{
    use HasFactory;

    protected $table = 'ihsgs';
    protected $primaryKey = 'id_ihsg';

    protected $fillable = ['user_id','tanggal','ihsg_start','ihsg_end','yield_ihsg'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
