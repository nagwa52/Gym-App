<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model {
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function users() {
        return $this->morphMany(User::class, 'manageable');
    }

    public function gyms(){
        return $this->morphMany(Gym::class, 'has_gyms');
    }
}
