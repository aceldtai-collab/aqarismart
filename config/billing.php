<?php

return [
    'prices' => [
        'starter' => env('STRIPE_PRICE_STARTER'),
        'pro' => env('STRIPE_PRICE_PRO'),
        'enterprise' => env('STRIPE_PRICE_ENTERPRISE'),
    ],
];

