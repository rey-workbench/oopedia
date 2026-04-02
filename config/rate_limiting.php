<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API Rate Limits
    |--------------------------------------------------------------------------
    |
    | Default rate limits for API endpoints.
    |
    */

    'api' => [
        'limit'  => 60,
        'period' => 1, // per minute
    ],

    /*
    |--------------------------------------------------------------------------
    | Guest Rate Limits
    |--------------------------------------------------------------------------
    |
    | Rate limits for unauthenticated users.
    |
    */

    'guest' => [
        'limit'  => 30,
        'period' => 1, // per minute
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Rate Limits
    |--------------------------------------------------------------------------
    |
    | Rate limits for login, registration, and password reset.
    |
    */

    'auth' => [
        'login' => [
            'limit'  => 5,
            'period' => 1, // per minute
        ],
        'register' => [
            'limit'  => 3,
            'period' => 1, // per minute
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Quiz Rate Limits
    |--------------------------------------------------------------------------
    |
    | Rate limits for quiz-related operations.
    |
    */

    'quiz' => [
        'submit' => [
            'limit'  => 30,
            'period' => 1, // per minute
        ],
        'review' => [
            'limit'  => 60,
            'period' => 1, // per minute
        ],
    ],

];
