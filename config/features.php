<?php

return [
    'plans' => [
        'starter' => [
            'contacts' => true,
            'agents' => true,
            'files' => false,
            'properties' => false,
            'units' => false,
            'leases' => false,
            'maintenance' => false,
            'custom_attributes' => false,
            'users_limit' => 3,
        ],
        'pro' => [
            'contacts' => true,
            'agents' => true,
            'files' => true,
            'properties' => true,
            'units' => true,
            'leases' => true,
            'maintenance' => true,
            'custom_attributes' => true,
            'users_limit' => 10,
        ],
        'enterprise' => [
            'contacts' => true,
            'agents' => true,
            'files' => true,
            'properties' => true,
            'units' => true,
            'leases' => true,
            'maintenance' => true,
            'custom_attributes' => true,
            'users_limit' => 100,
        ],
    ],
];
