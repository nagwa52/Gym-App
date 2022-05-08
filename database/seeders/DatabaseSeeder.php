<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Collection;
use App\Models\City;
use App\Models\Gym;
use App\Models\Package;
use App\Models\Purchase;
use App\Models\Session;
use App\Models\session_user;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory;
use Faker\Generator;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder {

    private Generator $faker;    //define property named faker

    /**
     * Seed the application's database.
     *
     * @return void
     */

    public function run() {
        $this->faker = Factory::create();   // use the factory to create a Faker\Generator instance 

        $this->addPermissionsToRoles($roles = $this->seedRoles(), $this->seedPermissions()); //seed Permission for seeded roles  
        $this->call(AdminUserSeeder::class);  //seed admin 
        $numberOfUsersPerRole = config('seeding.parameters.numberOfCities') * config('seeding.parameters.numberOfGymsPerCity');
        $users = $this->seedUsers($roles, $numberOfUsersPerRole);     //seed users

        /** @var User $cityManager */
        $cityManager = $users->first(fn (User $user) => $user->getRoleNames()->contains('city_manager')); //find first city manager from seeded users
        /** @var User $gymManager */
        $gymManager = $users->first(fn (User $user) => $user->getRoleNames()->contains('gym_manager'));  //find gym manager from seeded users
        /** @var User $member */
        $member = $users->first(fn (User $user) => $user->getRoleNames()->contains('member'));  //find member from seeded users


        /** @var User[] $cityManagers */
        $cityManagers = $users->where(fn (User $user) => $user->getRoleNames()->contains('city_manager'))->values(); //find all city managers from seeded users
        /** @var City[] $cities */
        $cities = collect();
        /** @var Gym[] $gyms */
        $gyms = collect();
        /** @var User[] $gymManagers */
        $gymManagers = $users->where(fn (User $user) => $user->getRoleNames()->contains('gym_manager'))->values(); //find all gym managers from seeded users

        $numberOfCities = config('seeding.parameters.numberOfCities');
        for ($i = 0; $i < $numberOfCities; $i++) {
            $cities->add($this->seedCity($this->faker->city)); //seed city with actual city name 
            $this->setManager($cityManagers[$i], $cities[0]);  // assign city manager a city 
            for ($j = 0; $j < config('seeding.parameters.numberOfGymsPerCity'); $j++) {
                $index = $j + $i * $numberOfCities;
                $gyms->add($this->seedGym($this->faker->name, config('seeding.data.gym.coverImageUrl'), $cities[$i], $gymManagers[$index]));
                $this->setManager($gymManagers[$index], $gyms[$index]);
            }
        }

        $gymSessions = $this->seedGymSessions($gyms[0]);
        $gymSessions->map(
            fn (Session $session) => $this->seedSessionUser($member, $session, $gyms[0]),
        );

        $this->seedPurchase(
            $this->faker->word,
            $this->faker->randomFloat(1, 0, 1000),
            $this->faker->randomNumber(2),
            $member,
            $cityManagers[0],
            $gyms[0]
        );

        $this->seedPackage(
            $this->faker->word,
            $this->faker->randomElement([1000, 5000]),
            $this->faker->randomNumber(2),
            $this->seedPackage(
                $this->faker->word,
                $this->faker->randomElement([1000, 5000]),
                $this->faker->randomNumber(2)
            )
        );
    }

    private function setManager(User $manager, City|Gym $manageable): void {
        $manager->manageable_id = $manageable->id;
        $manager->manageable_type = get_class($manageable);
        $manager->save();
    }

    private function seedCity(string $name): City {
        $this->call(CitySeeder::class, parameters: compact('name'));

        return City::latest('id')->first();
    }

    private function seedUser(string $name, string $email, string $password, int $nationalId, string $avatarUrl): User {
        $gender = $this->faker->randomElement(['male', 'female']);
        $dateOfBirth = $this->faker->date('Y-m-d');
        $this->call(UserSeeder::class, parameters: compact('name', 'email', 'password', 'nationalId', 'avatarUrl', 'dateOfBirth', 'gender'));

        return User::latest('id')->first();
    }

    private function seedGym(string $name, string $coverUrl, ?City $city, ?User $creator): Gym {
        $this->call(GymSeeder::class, parameters: [
            'name' => $name,
            'coverUrl' => $coverUrl,
            'city' => $city,
            'creator' => $creator
        ]);

        return Gym::latest('id')->first();
    }

    private function seedGymSessions(?Gym $gym): Collection {
        foreach (config('seeding.data.gymSessions') as $gymSession => $duration) {
            $startDate = Carbon::parse($this->faker->date('Y-m-d H:i:s'));

            $this->call(SessionSeeder::class, parameters: [
                'name' => $gymSession,
                'startsAt' => $startDate,
                'endsAt' => $startDate->addDays($duration),
                'gym' => $gym
            ]);
        }

        return Session::orderBy('id', 'desc')->limit(count(config('seeding.data.gymSessions')))->get();
    }

    private function seedSessionUser(User $user, Session $session, Gym $gym): session_user {
        $attendanceDate = $attendanceTime = $this->faker->dateTimeBetween($session->starts_at, $session->finishes_at)->format('Y-m-d H:i:s');
        $this->call(UserSessionSeeder::class, parameters: compact('user', 'session', 'gym', 'attendanceDate', 'attendanceTime'));

        return session_user::latest('id')->first();
    }

    private function seedPurchase(string $packageName, float $price, int $sessionsAmount, User $seller, User $buyer, Gym $gym): Purchase {
        $this->call(PurchaseSeeder::class, parameters: compact('packageName', 'price', 'sessionsAmount', 'seller', 'buyer', 'gym'));

        return Purchase::latest('id')->first();
    }

    private function seedPackage(string $name, int $price, int $sessions, ?Package $package = null): Package {
        $this->call(PackageSeeder::class, parameters: compact('name', 'price', 'sessions', 'package'));

        return Package::latest('id')->first();
    }


    /** @var string[] $rolesNames */

    private function seedRoles(): Collection {
        $rolesNames = config('seeding.data.roles');
        foreach ($rolesNames as $name) {
            $this->call(RoleSeeder::class, parameters: compact('name'));
        }

        return Role::whereIn('name', $rolesNames)->get();
    }

    private function seedPermissions(): Collection {
        $permissionsNames = config('seeding.data.permissions');
        foreach ($permissionsNames as $name) {
            $this->call(PermissionSeeder::class, parameters: compact('name'));
        }

        return Permission::whereIn('name', $permissionsNames)->get();
    }

    private function addPermissionsToRoles(Collection $roles, Collection $permissions): void {
        foreach (config('seeding.data.rolesPermissions') as $roleName => $permissionsNames) {
            $roles->first(fn (Role $role) => $roleName == $role->name)->givePermissionTo(
                $permissions->whereIn('name', $permissionsNames)->all()
            );
        }
    }

    private function seedUsers(Collection $roles, int $count): Collection {
        $users = collect();

        for ($i = 0; $i < $count; $i++) {
            $adminRole = $roles->where('name', 'admin')->first();
            $cityManagerRole = $roles->where('name', 'city_manager')->first();
            $gymManagerRole = $roles->where('name', 'gym_manager')->first();
            $coachRole = $roles->where('name', 'coach')->first();
            $memberRole = $roles->where('name', 'member')->first();

            $userPassword = config('seeding.data.user.password');
            $userAvatarUrl = config('seeding.data.user.avatarImageUrl');


            $cityManager = $this->seedUser(
                $this->faker->name,
                $this->faker->email,
                $userPassword,
                rand(10000000000000, 99999999999999),
                $userAvatarUrl
            );
            $cityManager->roles()->attach($cityManagerRole);
            $users->add($cityManager);


            $gymManager = $this->seedUser(
                $this->faker->name,
                $this->faker->email,
                $userPassword,
                rand(10000000000000, 99999999999999),
                $userAvatarUrl
            );
            $gymManager->roles()->attach($gymManagerRole);
            $users->add($gymManager);


            $coach = $this->seedUser(
                $this->faker->name,
                $this->faker->email,
                $userPassword,
                rand(10000000000000, 99999999999999),
                $userAvatarUrl
            );
            $coach->roles()->attach($coachRole);
            $users->add($coach);


            $member = $this->seedUser(
                $this->faker->name,
                $this->faker->email,
                $userPassword,
                rand(10000000000000, 99999999999999),
                $userAvatarUrl
            );
            $member->roles()->attach($memberRole);
            $users->add($member);
        }

        return $users;
    }
}
