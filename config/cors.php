<?php
return [
    'paths' => ['api/*'],
    'allowed_methods' => ['/*'],
    'allowed_origins' => ['http://localhost:4000'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['Content-Type', 'Accept', 'Authorization'], // Include relevant headers
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true, // Required for withCredentials
];