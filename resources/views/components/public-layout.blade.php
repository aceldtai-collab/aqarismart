@props(['title' => null])

@php
    $tenant = app(\App\Services\Tenancy\TenantManager::class)->tenant();
    $tenantName = $tenant?->name ?? config('app.name', 'Laravel');
    $pageTitle = $title ? (string) $title : $tenantName;
    $settings = is_array($tenant?->settings ?? null) ? $tenant->settings : [];
    $primaryColor = $settings['primary_color'] ?? '#4f46e5';
    $accentColor = $settings['accent_color'] ?? '#0ea5e9';
    $fontColor = $settings['font_color'] ?? '#0b3849';
    $typo = $settings['typography'] ?? 'system';
    $fontClass = $typo === 'serif' ? 'font-serif' : ($typo === 'mono' ? 'font-mono' : 'font-sans');
    $faviconUrl = $settings['favicon_url'] ?? '';
    if (!empty($faviconUrl) && !\Illuminate\Support\Str::startsWith($faviconUrl, ['http://', 'https://'])) {
        $faviconUrl = asset($faviconUrl);
    }
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale()==='ar' ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $pageTitle }}</title>

        <x-vite-assets />
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
        @if(!empty($faviconUrl))
            <link rel="icon" href="{{ $faviconUrl }}" type="image/x-icon">
        @endif
        <style>
            :root{
                --brand: {{ $primaryColor }};
                --accent: {{ $accentColor }};
                --font-color: {{ $fontColor }};
            }
            .brand-bg{ background-color: var(--brand) !important; }
            .brand-text{ color: var(--brand) !important; }
            .accent-bg{ background-color: var(--accent) !important; }
            .accent-text{ color: var(--accent) !important; }
            .btn-brand{ background-color: var(--brand); color: #fff; }
            .btn-brand:hover{ filter: brightness(0.95); }
            .btn-accent{ background-color: var(--accent); color: #fff; }
            .btn-accent:hover{ filter: brightness(0.95); }
        </style>
    </head>
    <body class="min-h-screen bg-gray-50 {{ $fontClass }} text-gray-900" style="color: var(--font-color)">
        {{ $slot }}
    </body>
</html>
