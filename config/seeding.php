<?php

return [

    'parameters' => [

        'numberOfCities' => 2,
        'numberOfGyms' => 3,
        'numberOfGymsPerCity' => 2

    ],

    'data' => [

        'roles' => [
            'admin',
            'city_manager',
            'gym_manager',
            'coach',
            'member'
        ],

        'permissions' => [
            'CRUD_city_managers',
            'CRUD_cities',
            'CRUD_gym_managers',
            'CRUD_coaches',
            'CRUD_members',
            'CRUD_gyms',
            'CRUD_sessions'
        ],

        'rolesPermissions' => [
            'admin' => [ 'CRUD_city_managers', 'CRUD_gym_managers', 'CRUD_coaches', 'CRUD_members', 'CRUD_gyms', 'CRUD_sessions'],
            'city_manager' => [ 'CRUD_gym_managers' ],
            'gym_manager' => [ 'CRUD_gym_managers' ]
        ],

        'gym' => [

            'coverImageUrl' => 'public/gym_cover1.png'

        ],

        'gymSessions' => [
            // name => duration-in-days
            'Cardio' => 10,
            'MMA' => 15
        ],

        'user' => [
            
            'password' => '12345678',

            'avatarImageUrl' => 'public/default_avatar.png'

        ]


    ]

];
