<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // 🔥 KHÔNG DÙNG '*'
    'allowed_origins' => [
        'http://localhost:5173',
    ],

    'allowed_headers' => ['*'],

    // 🔥 BẮT BUỘC KHI DÙNG COOKIE / SESSION
    'supports_credentials' => true,
];
