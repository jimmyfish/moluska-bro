<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'url' => env('APP_URL'),
    'name'              => env('APP_NAME', 'Laravel'),
    'env'               => env('APP_ENV', 'production'),
    'debug'             => (bool)env('APP_DEBUG', false),
    'locale'            => 'en',
    'fallback_locale'   => 'en',
    'key'               => env('APP_KEY'),
    'cipher'            => 'AES-256-CBC',
    'aliases'           => [
        'App' => Illuminate\Support\Facades\App::class,
    ],
    'timezone'          => env('APP_TIMEZONE', 'Asia/Jakarta'),
    'cdnUrl'            => env('APP_CDN'),
    'sepatUrl'          => env('APP_SEPAT_CDN', 'https://sepat.roomme.id'),
    'webUrl'            => env('WEB_URL'),
    'sendEmail'         => env('SEND_EMAIL', true),
    'bypassOtp'         => env('EMPLOYEE_BYPASS_OTP', 'IChallengeYouToGuessMyPassword'),
    'tmpDir'            => "/tmp/bro",
    'profilePictureDir' => env("LOCAL_S3", storage_path('app/profile')),
    'fcmToken' => env('FCM_TOKEN'),
    'imageURI' => env('IMAGE_URL'),
];
