<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminUserSeeder extends Seeder {
    
    /**
     * assign values for admin
     *
     * @return User 
     */


    public function run() : User {
        $admin = User::create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => bcrypt('123456'),
            'national_id' => '12345677654321',
            'avatar_url' => 'public/default_avatar.png',
            'date_of_birth' => '1999-9-13 00:00:0000'
        ]);

        $admin->assignRole('admin');
        return $admin ;
    }
}
