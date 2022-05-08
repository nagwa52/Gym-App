<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Gym;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GymSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(string $name, string $coverUrl, ?City $city, ?User $creator): Gym {
        return Gym::create([
            'name' => $name,
            'cover_url' => $coverUrl,
            'has_gyms_type'   => City::class,
            'has_gyms_id'     => is_null($city) ? null : $city->id,
            'creatable_type' => User::class,
            'creatable_id'     => is_null($creator) ? null : $creator->id,
        ]);
    }
}
