<?php

namespace Database\Seeders;

use App\Models\Gym;
use App\Models\Session;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SessionSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(string $name, string $startsAt, string $endsAt, ?Gym $gym): Session {
        
        return Session::create([
            'name'  => $name,
            'starts_at' => $startsAt,
            'finishes_at'   => $endsAt,
            'has_sessions_type' => Gym::class,
            'has_sessions_id' => is_null($gym) ? null : $gym->id
        ]);
    }
}
