<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model {
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'starts_at',
        'finishes_at',
        'has_sessions_type',
        'has_sessions_id',
    ];
    
    protected $hidden = [
        'pivot',
    ];

    public function has_sessions() {
        return $this->morphTo();
    }

    public function users() {
        return $this->belongsToMany(User::class);
    }
}
