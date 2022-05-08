<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\User;

class UserSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(string $name, string $email, string $password, string $dateOfBirth, string $gender, int $nationalId, string $avatarUrl): User {
        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
            'national_id' => $nationalId,
            'avatar_url' => $avatarUrl,
            'date_of_birth' => $dateOfBirth,
            'gender' => $gender
        ]);
    }
}
