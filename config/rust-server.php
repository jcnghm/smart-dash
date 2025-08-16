<?php

return [
    'base_uri' => env('RUST_SERVER_BASE_URI', 'http://host.docker.internal:8080/'),

    'demo_admin_user' => env('RUST_SERVER_DEMO_ADMIN_USER'),

    'demo_admin_password' => env('RUST_SERVER_DEMO_ADMIN_PASSWORD')
];
