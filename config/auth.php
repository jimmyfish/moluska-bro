<?php

return [
    'defaults' => [
        'guard' => 'api',
        'passwords' => 'users',
    ],

    'guards' => [
        'api' => [
            'driver' => 'jwt',
            'provider' => 'users',
        ],
        'api-employee' => [
            'driver' => 'jwt',
            'provider' => 'employee'
        ]
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => \App\Models\User::class
        ],
        'employee' => [
            'driver' => 'eloquent',
            'model' => \App\Models\Employee::class
        ],
    ],
];
