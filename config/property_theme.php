<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Property Management Theme Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the theme configuration for property management
    | specific styling and branding options.
    |
    */

    'colors' => [
        'primary' => '#0ea5e9',      // Professional blue for trust
        'secondary' => '#eab308',     // Gold for premium feel
        'accent' => '#10b981',        // Green for success/available
        'neutral' => '#64748b',       // Professional gray
        'danger' => '#ef4444',        // Red for alerts/unavailable
        'warning' => '#f59e0b',       // Orange for maintenance
        'success' => '#10b981',       // Green for confirmations
        'info' => '#3b82f6',         // Blue for information
    ],

    'typography' => [
        'headings' => [
            'font_family' => 'Figtree',
            'font_weight' => '700',
            'line_height' => '1.2',
        ],
        'body' => [
            'font_family' => 'Figtree',
            'font_weight' => '400',
            'line_height' => '1.6',
        ],
        'property_titles' => [
            'font_family' => 'Figtree',
            'font_weight' => '600',
            'line_height' => '1.3',
        ],
    ],

    'components' => [
        'property_card' => [
            'border_radius' => '0.75rem',
            'shadow' => 'property',
            'hover_shadow' => 'property-hover',
            'transition' => 'all 0.3s ease',
        ],
        'buttons' => [
            'primary' => [
                'bg' => 'gradient-to-r from-property-500 to-blue-600',
                'text' => 'white',
                'padding' => 'px-6 py-3',
                'border_radius' => '0.75rem',
                'font_weight' => '600',
            ],
            'secondary' => [
                'bg' => 'white',
                'text' => 'property-700',
                'border' => '2px solid property-200',
                'padding' => 'px-6 py-3',
                'border_radius' => '0.75rem',
                'font_weight' => '600',
            ],
        ],
        'badges' => [
            'available' => [
                'bg' => 'emerald-100',
                'text' => 'emerald-800',
                'icon' => 'check-circle',
            ],
            'rented' => [
                'bg' => 'red-100',
                'text' => 'red-800',
                'icon' => 'x-circle',
            ],
            'pending' => [
                'bg' => 'yellow-100',
                'text' => 'yellow-800',
                'icon' => 'clock',
            ],
            'maintenance' => [
                'bg' => 'orange-100',
                'text' => 'orange-800',
                'icon' => 'wrench',
            ],
        ],
    ],

    'layout' => [
        'container_max_width' => '7xl',
        'section_spacing' => 'py-12 lg:py-16',
        'card_spacing' => 'p-6',
        'grid_gaps' => [
            'sm' => 'gap-4',
            'md' => 'gap-6',
            'lg' => 'gap-8',
        ],
    ],

    'property_specific' => [
        'price_display' => [
            'font_size' => 'text-3xl',
            'font_weight' => 'font-extrabold',
            'color' => 'property-gradient',
        ],
        'property_features' => [
            'icon_size' => 'w-4 h-4',
            'text_size' => 'text-sm',
            'spacing' => 'gap-3',
        ],
        'status_indicators' => [
            'available' => [
                'color' => 'emerald-500',
                'text' => 'Available',
                'icon' => 'check-circle',
            ],
            'rented' => [
                'color' => 'red-500',
                'text' => 'Rented',
                'icon' => 'x-circle',
            ],
            'pending' => [
                'color' => 'yellow-500',
                'text' => 'Pending',
                'icon' => 'clock',
            ],
            'maintenance' => [
                'color' => 'orange-500',
                'text' => 'Maintenance',
                'icon' => 'wrench-screwdriver',
            ],
        ],
    ],

    'responsive' => [
        'breakpoints' => [
            'sm' => '640px',
            'md' => '768px',
            'lg' => '1024px',
            'xl' => '1280px',
            '2xl' => '1536px',
        ],
        'property_cards_per_row' => [
            'sm' => 1,
            'md' => 2,
            'lg' => 3,
            'xl' => 4,
        ],
    ],

    'animations' => [
        'card_hover' => [
            'transform' => 'translateY(-4px)',
            'shadow' => 'property-hover',
            'duration' => '300ms',
        ],
        'button_hover' => [
            'transform' => 'scale(1.02)',
            'duration' => '200ms',
        ],
    ],

    'accessibility' => [
        'focus_ring' => 'focus:ring-2 focus:ring-property-500 focus:ring-offset-2',
        'contrast_ratios' => [
            'text_on_primary' => 'white',
            'text_on_secondary' => 'gray-900',
            'text_on_surface' => 'gray-900',
        ],
    ],
];