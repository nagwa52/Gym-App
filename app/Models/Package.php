<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model {
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'sessions_amount',
        'has_packages_type',
        'has_packages_id',
    ];

    public function has_packages() {
        return $this->morphTo();
    }
}
