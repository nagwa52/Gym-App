<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Creating Roles
        $admin = Role::create(['name' => 'admin']);
        $city_manager = Role::create(['name' => 'city_manager']);
        $gym_manager = Role::create(['name' => 'gym_manager']);
        $coach = Role::create(['name' => 'coach']);
        $member = Role::create(['name' => 'member']);

        // Creating permissions.
        $CRUD_city_managers = Permission::create(['name' => 'CRUD_city_managers']);
        $CRUD_cities = Permission::create(['name' => 'CRUD_cities']);
        $CRUD_gym_managers = Permission::create(['name' => 'CRUD_gym_managers']);
        $CRUD_coaches = Permission::create(['name' => 'CRUD_coaches']);
        $CRUD_members = Permission::create(['name' => 'CRUD_members']);
        $CRUD_gyms = Permission::create(['name' => 'CRUD_gyms']);
        $CRUD_sessions = Permission::create(['name' => 'CRUD_sessions']);

        // Assigning permissions to roles.
        $admin->givePermissionTo([$CRUD_city_managers, $CRUD_gym_managers, $CRUD_coaches, $CRUD_members, $CRUD_gyms, $CRUD_sessions]);
        $city_manager->givePermissionTo($CRUD_gym_managers);  
        $gym_manager->givePermissionTo($CRUD_gym_managers);  
    }
}
