<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationScheduler extends Model
{
    protected $table = "notification_scheduler";
    protected $primaryKey = 'id';
    protected $guarded = [];
}
