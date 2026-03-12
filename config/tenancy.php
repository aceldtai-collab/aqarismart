<?php

return [
    // Base domain used to resolve subdomain tenants (e.g., app.localtest.me)
    'base_domain' => env('TENANCY_BASE_DOMAIN', parse_url(env('APP_URL', ''), PHP_URL_HOST) ?: 'localhost'),
];

