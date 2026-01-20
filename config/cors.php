<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'auth/google', 'auth/google/callback'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:5173',
        'http://127.0.0.1:5173',
        'https://yourfrontend.com', 
        'http://localhost:8080',      // WAJIB: Port Docker Frontend kamu
        'http://127.0.0.1:8080',      // Tambahkan ini untuk jaga-jaga 
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => ['Authorization'],

    'max_age' => 0,

    'supports_credentials' => true,

];
