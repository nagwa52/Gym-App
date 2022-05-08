<?php

namespace Database\Seeders;

use App\Models\Gym;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PurchaseSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(string $packageName, float $price, int $sessionsAmount, User $seller, User $buyer, Gym $gym): Purchase {
        return Purchase::create([
            'name' => $packageName,
            'price' => $price,
            'sessions_amount' => $sessionsAmount,
            'buyable_id' => $buyer->id,
            'buyable_type' => User::class,
            'sellable_id' => $seller->id,
            'sellable_type' => User::class,
            'gym_id' => $gym->id
        ]);
    }
}
