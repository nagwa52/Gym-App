<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(string $name, int $price, int $sessions, ?Package $package): Package {
        return Package::create([
            'name' => $name,
            'price' => $price,
            'sessions_amount' => $sessions,
            'has_packages_type' => is_null($package) ? null : get_class($package),
            'has_packages_id' => is_null($package) ? null : $package->id
        ]);
    }
}
