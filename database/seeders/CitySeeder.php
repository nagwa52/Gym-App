<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\City;

class CitySeeder extends Seeder {

    /**
     * assign values for Cities table
     * @param string $name City name to be created 
     * @return City created
     */

    public function run(string $name): City {
        return City::create([
            'name' => $name,
        ]);
    }
}
