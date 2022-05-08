<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model {
    use HasFactory;

    protected $table = 'purchases';

    protected $fillable = [
        'name',
        'price',
        'sessions_amount',
        'buyable_id',
        'buyable_type',
        'sellable_id',
        'sellable_type',
        'gym_id',
        'is_paid',
    ];

    public function buyable() {
        return $this->morphTo();
    }

    public function sellable() {
        return $this->morphTo();
    }

    public function has_purchases() {
        return $this->morphTo();
    }
}
