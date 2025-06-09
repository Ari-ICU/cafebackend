<?php


return [
    'driver' => env('SESSION_DRIVER', 'file'), // Use 'file' or 'redis'
    'lifetime' => 120,
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => storage_path('framework/sessions'),
    'connection' => null,
    'table' => 'sessions',
    'store' => null,
    'lottery' => [2, 100],
    'cookie' => env('SESSION_COOKIE', 'laravel_session'),
    'path' => '/',
    'domain' => null,
    'secure' => env('SESSION_SECURE_COOKIE', false), // Set to true in production
    'http_only' => true,
    'same_site' => 'lax',
];