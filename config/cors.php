<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    // Only the public read methods — write actions go through Sanctum bearer auth
    // and don't need to be CORS-permitted across origins.
    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    // Lock down to known frontends. Update with your production domains.
    // Local dev: 127.0.0.1:3000 and localhost:3000.
    'allowed_origins' => array_values(array_filter([
        env('FRONTEND_URL'),
        'http://localhost:3000',
        'http://127.0.0.1:3000',
        'https://yakspiritadventures.com',
        'https://www.yakspiritadventures.com',
    ])),

    'allowed_origins_patterns' => [],

    // Bearer auth + JSON content type + x-request-id + X-CSRF-Token (added by the frontend
    // axios interceptor for request tracing and CSRF protection).
    'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With', 'Accept', 'x-request-id', 'X-Request-ID', 'X-CSRF-Token'],

    'exposed_headers' => [],

    'max_age' => 86400,

    // Tokens are sent via Authorization header, not cookies, so no credentials needed.
    'supports_credentials' => false,

];
