<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class session_user extends Model {
    use HasFactory;

    protected $table = 'session_user';

    protected $fillable = [
        'attendance_date',
        'attendance_time',
        'session_id',
        'user_id',
    ];
}
