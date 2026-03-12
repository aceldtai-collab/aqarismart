<?php

return [
    // Site-wide public (base domain) theme settings controlled by Super Admin
    'name' => env('PUBLIC_SITE_NAME', config('app.name', 'RentoJO')),
    'logo_url' => env('PUBLIC_SITE_LOGO', ''),
    'favicon_url' => env('PUBLIC_SITE_FAVICON', ''),
    'primary_color' => env('PUBLIC_SITE_PRIMARY', '#ff2929ff'),
    'accent_color' => env('PUBLIC_SITE_ACCENT', '#3c9c3fff'),
    'font_color' => env('PUBLIC_SITE_FONT', '#074860ff'),
    'typography' => env('PUBLIC_SITE_TYPO', 'system'), // system|serif|mono
    'max_width' => env('PUBLIC_SITE_MAX_W', 'max-w-6xl'),
];

