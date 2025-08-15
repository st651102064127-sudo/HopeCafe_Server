<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

'allowed_origins' => [
    'http://localhost:5174',
    'https://hope-cafe-front-end.vercel.app/',
    'https://hopecafe-server.onrender.com/',
],
    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true, // ถ้าใช้ cookie/session
];
