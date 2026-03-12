<?php

namespace App\Helpers;

class PropertyThemeHelper
{
    /**
     * Get property management theme configuration
     */
    public static function config(string $key = null, $default = null)
    {
        $config = config('property_theme');
        
        if ($key === null) {
            return $config;
        }
        
        return data_get($config, $key, $default);
    }
    
    /**
     * Get property status badge classes
     */
    public static function getStatusBadge(string $status): array
    {
        $badges = self::config('components.badges', []);
        
        return $badges[$status] ?? [
            'bg' => 'gray-100',
            'text' => 'gray-800',
            'icon' => 'question-mark-circle',
        ];
    }
    
    /**
     * Get property card classes
     */
    public static function getPropertyCardClasses(): string
    {
        return 'property-card';
    }
    
    /**
     * Get button classes by type
     */
    public static function getButtonClasses(string $type = 'primary'): string
    {
        $buttonConfig = self::config("components.buttons.{$type}");
        
        if (!$buttonConfig) {
            return 'btn-property-primary';
        }
        
        return "btn-property-{$type}";
    }
    
    /**
     * Get property info grid classes
     */
    public static function getPropertyInfoClasses(): string
    {
        return 'property-info-grid';
    }
    
    /**
     * Get property price display classes
     */
    public static function getPriceClasses(): string
    {
        $priceConfig = self::config('property_specific.price_display');
        
        return implode(' ', [
            $priceConfig['font_size'] ?? 'text-3xl',
            $priceConfig['font_weight'] ?? 'font-extrabold',
            'text-property-gradient'
        ]);
    }
    
    /**
     * Get responsive grid classes for property cards
     */
    public static function getResponsiveGridClasses(): string
    {
        $responsive = self::config('property_specific.property_cards_per_row');
        
        $classes = ['grid'];
        
        foreach ($responsive as $breakpoint => $cols) {
            if ($breakpoint === 'sm') {
                $classes[] = "grid-cols-{$cols}";
            } else {
                $classes[] = "{$breakpoint}:grid-cols-{$cols}";
            }
        }
        
        $gaps = self::config('layout.grid_gaps');
        $classes[] = $gaps['md'] ?? 'gap-6';
        
        return implode(' ', $classes);
    }
    
    /**
     * Get property feature icon classes
     */
    public static function getFeatureIconClasses(): string
    {
        $features = self::config('property_specific.property_features');
        
        return implode(' ', [
            $features['icon_size'] ?? 'w-4 h-4',
            'text-property-500'
        ]);
    }
    
    /**
     * Get property feature text classes
     */
    public static function getFeatureTextClasses(): string
    {
        $features = self::config('property_specific.property_features');
        
        return implode(' ', [
            $features['text_size'] ?? 'text-sm',
            'text-gray-600',
            'font-medium'
        ]);
    }
    
    /**
     * Get status indicator configuration
     */
    public static function getStatusIndicator(string $status): array
    {
        $indicators = self::config('property_specific.status_indicators');
        
        return $indicators[$status] ?? [
            'color' => 'gray-500',
            'text' => ucfirst($status),
            'icon' => 'question-mark-circle',
        ];
    }
    
    /**
     * Generate CSS custom properties for dynamic theming
     */
    public static function generateCssProperties(array $theme = []): string
    {
        $colors = self::config('colors');
        $properties = [];
        
        // Override with tenant-specific theme
        if (!empty($theme['primary_color'])) {
            $colors['primary'] = $theme['primary_color'];
        }
        if (!empty($theme['secondary_color'])) {
            $colors['secondary'] = $theme['secondary_color'];
        }
        if (!empty($theme['accent_color'])) {
            $colors['accent'] = $theme['accent_color'];
        }
        
        foreach ($colors as $name => $value) {
            $properties[] = "--property-{$name}: {$value}";
        }
        
        return implode('; ', $properties);
    }
    
    /**
     * Get form classes for property management
     */
    public static function getFormClasses(): array
    {
        return [
            'form' => 'form-property',
            'group' => 'form-group-property',
            'label' => 'form-label-property',
            'input' => 'form-input-property',
            'select' => 'form-select-property',
        ];
    }
    
    /**
     * Get navigation classes for property management
     */
    public static function getNavClasses(): array
    {
        return [
            'nav' => 'nav-property',
            'item' => 'nav-property-item',
            'active' => 'nav-property-item active',
        ];
    }
    
    /**
     * Get table classes for property management
     */
    public static function getTableClasses(): array
    {
        return [
            'table' => 'table-property',
            'header' => 'bg-gray-50',
            'row' => 'hover:bg-gray-50',
        ];
    }
    
    /**
     * Get dashboard card classes
     */
    public static function getDashboardClasses(): array
    {
        return [
            'card' => 'dashboard-card-property',
            'stat' => 'dashboard-stat-property',
            'label' => 'dashboard-stat-label-property',
        ];
    }
    
    /**
     * Get alert classes by type
     */
    public static function getAlertClasses(string $type = 'info'): string
    {
        return "alert-property-{$type}";
    }
}