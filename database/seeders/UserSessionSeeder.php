<?php

namespace Database\Seeders;

use App\Models\Gym;
use App\Models\Session;
use App\Models\session_user;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSessionSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(User $user, Session $session, Gym $gym, string $attendanceDate, string $attendanceTime): session_user {
        return session_user::create([
            'user_id' => $user->id,
            'session_id' => $session->id,
            // 'gym_id' => $gym->id,
            'attendance_date' => $attendanceDate,
            'attendance_time' => $attendanceTime
        ]);
    }
}
