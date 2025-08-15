<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

'allowed_origins' => [
    // เพิ่ม URL ของ Vercel เข้าไปในนี้
    'https://hope-cafe-front-end.vercel.app',
],
    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true, // ถ้าใช้ cookie/session
];
