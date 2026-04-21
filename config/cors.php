<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for cross-origin requests. The NativePHP mobile app runs on a
    | local embedded server (http://127.0.0.1:PORT) and fetches data from the
    | production API (https://aqarismart.com/api/*). These headers allow the
    | WebView's cross-origin fetch calls to succeed.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // Allow origins can be configured via CORS_ALLOWED_ORIGINS (comma-separated).
    // Default to the APP_URL host to be safe in production.
    'allowed_origins' => array_filter(array_map('trim', explode(',', env('CORS_ALLOWED_ORIGINS', env('APP_URL', 'https://aqarismart.com'))))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // Enable credentials so browsers and WebViews can send cookies. When true,
    // ensure CORS_ALLOWED_ORIGINS is not '*' and lists explicit origins.
    'supports_credentials' => (bool) env('CORS_SUPPORTS_CREDENTIALS', true),

];
