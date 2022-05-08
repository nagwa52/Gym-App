<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(string $name): Permission {
        return Permission::create(compact('name'));
    }
}
