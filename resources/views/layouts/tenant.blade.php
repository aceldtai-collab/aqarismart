<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale()==='ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $tenantCtx = $tenantCtx ?? app(\App\Services\Tenancy\TenantManager::class)->tenant();
        $theme = $tenantCtx?->theme ?? [];
    @endphp
    <title>@yield('title', ($tenantCtx?->name ?? config('app.name')) . ' — ' . __('Dashboard'))</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @if(app()->getLocale() === 'ar')
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    @endif

    <x-vite-assets />
    @if(class_exists('\Livewire\Livewire'))
        @livewireStyles
    @endif

    <style>
        [x-cloak]{display:none!important}
        body{font-family:'Inter',sans-serif;margin:0;color:#0f172a}
        @if(app()->getLocale()==='ar') body{font-family:'Noto Sans Arabic','Inter',sans-serif} @endif

        .tz-sidebar{background:#0f172a;width:250px;height:100vh;transition:transform .25s ease;flex-shrink:0;overflow-y:auto}
        .tz-sidebar-link{display:flex;align-items:center;gap:10px;padding:8px 16px;margin:1px 10px;color:rgba(255,255,255,.45);font-size:.8125rem;font-weight:500;border-radius:6px;transition:all .15s;white-space:nowrap;text-decoration:none}
        .tz-sidebar-link:hover{color:rgba(255,255,255,.85);background:rgba(255,255,255,.06)}
        .tz-sidebar-link.active{color:#fff;background:rgba(255,255,255,.1)}
        .tz-sidebar-link svg{width:18px;height:18px;flex-shrink:0;opacity:.7}
        .tz-sidebar-link.active svg{opacity:1}
        .tz-sidebar-section{padding:20px 16px 6px;font-size:.65rem;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:rgba(255,255,255,.2);margin:0 10px}

        .tz-topbar{background:#fff;border-bottom:1px solid #e2e8f0}
        .tz-back{display:inline-flex;align-items:center;gap:5px;font-size:.8125rem;font-weight:500;color:#64748b;transition:color .15s;text-decoration:none}
        .tz-back:hover{color:#0f172a}
        .tz-back svg{width:15px;height:15px}

        .tz-table-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch}

        @media(max-width:1023px){
            .tz-sidebar{position:fixed;z-index:50;transform:translateX(-100%)}
            [dir="rtl"] .tz-sidebar{transform:translateX(100%)}
            .tz-sidebar.open{transform:translateX(0)}
        }
    </style>
</head>
<body x-data="{ sidebarOpen: false }" class="antialiased bg-[#f1f5f9]">
    <div class="flex min-h-screen">
        {{-- Sidebar Overlay (mobile) --}}
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
             class="fixed inset-0 bg-black/40 z-40 lg:hidden" x-transition.opacity></div>

        {{-- Sidebar --}}
        <aside class="tz-sidebar fixed lg:sticky top-0 z-50 flex flex-col"
               :class="sidebarOpen ? 'open' : ''">
            {{-- Logo --}}
            <div class="flex items-center gap-3 px-5 h-14 border-b border-white/[.06] flex-shrink-0">
                @if(!empty($theme['logo_url']))
                    <img src="{{ \Illuminate\Support\Str::startsWith($theme['logo_url'], ['http://','https://']) ? $theme['logo_url'] : asset($theme['logo_url']) }}" alt="logo" class="h-8 w-auto rounded flex-shrink-0" />
                @else
                    <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/></svg>
                    </div>
                @endif
                <span class="text-white font-semibold text-sm tracking-tight truncate">{{ $tenantCtx?->name ?? config('app.name') }}</span>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 py-4 overflow-y-auto" @click.self="if(window.innerWidth < 1024) sidebarOpen = false">
                <div class="tz-sidebar-section mt-1">{{ __('Main') }}</div>

                @can('view-dashboard')
                <a href="{{ route('dashboard') }}" @click="if(window.innerWidth < 1024) sidebarOpen = false"
                   class="tz-sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                    {{ __('Dashboard') }}
                </a>
                @endcan

                @if(($tenantCtx?->canUse('properties') && Auth::user()?->can('viewAny', App\Models\Property::class)) || ($tenantCtx?->canUse('units') && Auth::user()?->can('viewAny', App\Models\Unit::class)))
                <div class="tz-sidebar-section mt-4">{{ __('Properties') }}</div>
                @endif

                @can('viewAny', App\Models\Property::class)
                    @if($tenantCtx?->canUse('properties'))
                    <a href="{{ route('properties.index') }}" @click="if(window.innerWidth < 1024) sidebarOpen = false"
                       class="tz-sidebar-link {{ request()->routeIs('properties.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205l3 1m1.5.5l-1.5-.5M6.75 7.364V3h-3v18m3-13.636l10.5-3.819"/></svg>
                        {{ __('Properties') }}
                    </a>
                    @endif
                @endcan

                @can('viewAny', App\Models\Unit::class)
                    @if($tenantCtx?->canUse('units'))
                    <a href="{{ route('units.index') }}" @click="if(window.innerWidth < 1024) sidebarOpen = false"
                       class="tz-sidebar-link {{ request()->routeIs('units.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                        {{ __('Units') }}
                    </a>
                    @endif
                @endcan

                @can('manage-attributes')
                    @if($tenantCtx?->canUse('custom_attributes'))
                    <a href="{{ route('custom-attributes.index') }}" @click="if(window.innerWidth < 1024) sidebarOpen = false"
                       class="tz-sidebar-link {{ request()->routeIs('custom-attributes.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"/></svg>
                        {{ __('Custom Attributes') }}
                    </a>
                    @endif
                @endcan

                @can('viewAny', App\Models\Resident::class)
                <div class="tz-sidebar-section mt-4">{{ __('Residents') }}</div>

                <a href="{{ route('residents.index') }}" @click="if(window.innerWidth < 1024) sidebarOpen = false"
                   class="tz-sidebar-link {{ request()->routeIs('residents.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                    {{ __('Residents') }}
                </a>

                @can('viewAny', App\Models\Lease::class)
                <a href="{{ route('leases.index') }}" @click="if(window.innerWidth < 1024) sidebarOpen = false"
                   class="tz-sidebar-link {{ request()->routeIs('leases.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    {{ __('Leases') }}
                </a>
                @endcan
                @endcan

                @if($tenantCtx?->canUse('agents'))
                <div class="tz-sidebar-section mt-4">{{ __('Sales') }}</div>

                @can('viewAny', App\Models\Agent::class)
                <a href="{{ route('agents.index') }}" @click="if(window.innerWidth < 1024) sidebarOpen = false"
                   class="tz-sidebar-link {{ request()->routeIs('agents.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                    {{ __('Agents') }}
                </a>
                @endcan

                @can('viewAny', App\Models\Contact::class)
                <a href="{{ route('contacts.index') }}" @click="if(window.innerWidth < 1024) sidebarOpen = false"
                   class="tz-sidebar-link {{ request()->routeIs('contacts.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155"/></svg>
                    {{ __('Contacts') }}
                </a>
                @endcan

                <a href="{{ route('agent-leads.index') }}" @click="if(window.innerWidth < 1024) sidebarOpen = false"
                   class="tz-sidebar-link {{ request()->routeIs('agent-leads.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                    {{ __('Leads') }}
                </a>

                <a href="{{ route('property-viewings.index') }}" @click="if(window.innerWidth < 1024) sidebarOpen = false"
                   class="tz-sidebar-link {{ request()->routeIs('property-viewings.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ __('Viewings') }}
                </a>

                <a href="{{ route('agent-commissions.index') }}" @click="if(window.innerWidth < 1024) sidebarOpen = false"
                   class="tz-sidebar-link {{ request()->routeIs('agent-commissions.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ __('Commissions') }}
                </a>
                @endif

                <div class="tz-sidebar-section mt-4">{{ __('Operations') }}</div>

                @if($tenantCtx?->canUse('maintenance'))
                <a href="{{ route('maintenance.index') }}" @click="if(window.innerWidth < 1024) sidebarOpen = false"
                   class="tz-sidebar-link {{ request()->routeIs('maintenance.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17l-5.384 3.18.64-5.99L2.294 8.004l6.005-.873L11.42 2l2.693 5.131 6.005.873-4.382 4.356.64 5.99z"/></svg>
                    {{ __('Maintenance') }}
                </a>
                @endif

                @can('view-reports')
                <a href="{{ route('reports.index') }}" @click="if(window.innerWidth < 1024) sidebarOpen = false"
                   class="tz-sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                    {{ __('Reports') }}
                </a>
                @endcan

                <div class="tz-sidebar-section mt-4">{{ __('System') }}</div>

                @can('view-members')
                <a href="{{ route('members.index') }}" @click="if(window.innerWidth < 1024) sidebarOpen = false"
                   class="tz-sidebar-link {{ request()->routeIs('members.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/></svg>
                    {{ __('Members') }}
                </a>
                @endcan

                <a href="{{ route('billing.index') }}" @click="if(window.innerWidth < 1024) sidebarOpen = false"
                   class="tz-sidebar-link {{ request()->routeIs('billing.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
                    {{ __('Billing') }}
                </a>

                @can('manage-permissions', $tenantCtx)
                <a href="{{ route('permissions.index') }}" @click="if(window.innerWidth < 1024) sidebarOpen = false"
                   class="tz-sidebar-link {{ request()->routeIs('permissions.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                    {{ __('Permissions') }}
                </a>
                @endcan

                @can('view-settings')
                <a href="{{ route('settings.edit') }}" @click="if(window.innerWidth < 1024) sidebarOpen = false"
                   class="tz-sidebar-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ __('Settings') }}
                </a>
                @endcan
            </nav>

            {{-- Sidebar Footer --}}
            <div class="p-3 border-t border-white/[.06] flex-shrink-0">
                <div class="flex items-center gap-2.5 px-2">
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0 text-xs font-semibold text-white">{{ substr(Auth::user()->name ?? 'U', 0, 1) }}</div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-white/80 truncate">{{ Auth::user()->name ?? 'User' }}</div>
                        <div class="text-[11px] text-white/30 truncate">{{ Auth::user()->email ?? '' }}</div>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Main Area --}}
        <div class="flex-1 flex flex-col min-w-0">
            {{-- Topbar --}}
            <header class="tz-topbar sticky top-0 z-30 px-4 sm:px-6">
                <div class="flex items-center justify-between h-14">
                    <div class="flex items-center gap-3 min-w-0">
                        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-1.5 -ml-1 rounded-md hover:bg-slate-100 transition flex-shrink-0">
                            <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                        </button>
                        @if(!request()->routeIs('dashboard'))
                            <a href="javascript:history.back()" class="tz-back">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                                <span class="hidden sm:inline">{{ __('Back') }}</span>
                            </a>
                        @endif
                        @if(isset($header))
                            <span class="text-sm font-semibold text-slate-900 truncate">{{ $header }}</span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">

                    @php
                        $currentLang = app()->getLocale()==='ar' ? 'ar' : 'en';
                        $langParam = config('locales.cookie_name', 'lang');
                        $urlEn = request()->fullUrlWithQuery([$langParam => 'en']);
                        $urlAr = request()->fullUrlWithQuery([$langParam => 'ar']);
                    @endphp
                    <div class="flex items-center gap-1 rounded-md border border-slate-200 p-0.5 text-xs font-medium">
                        <a href="{{ $urlEn }}" class="px-2 py-0.5 rounded transition {{ $currentLang==='en' ? 'bg-gray-50 ' : 'text-slate-500 hover:bg-slate-50' }}">EN</a>
                        <a href="{{ $urlAr }}" class="px-2 py-0.5 rounded transition {{ $currentLang==='ar' ? 'bg-gray-50 ' : 'text-slate-500 hover:bg-slate-50' }}">ع</a>
                    </div>

                        <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                            <button @click="open = !open" class="flex items-center gap-2 p-1 rounded-md hover:bg-slate-50 transition">
                                <div class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center text-xs font-semibold text-slate-600 flex-shrink-0">{{ substr(Auth::user()->name ?? 'U', 0, 1) }}</div>
                                <svg class="w-3.5 h-3.5 text-slate-400 hidden sm:block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                            </button>
                            <div x-show="open" x-cloak x-transition
                                 class="absolute ltr:right-0 rtl:left-0 mt-1 w-44 bg-white border border-slate-200 rounded-lg shadow-lg py-1 z-50">
                                <div class="px-3 py-2 border-b border-slate-100">
                                    <div class="text-sm font-medium text-slate-900 truncate">{{ Auth::user()->name ?? 'User' }}</div>
                                    <div class="text-xs text-slate-400 truncate">{{ Auth::user()->email ?? '' }}</div>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-sm text-slate-600 hover:bg-slate-50">{{ __('Profile') }}</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full ltr:text-left rtl:text-right px-3 py-2 text-sm text-slate-600 hover:bg-slate-50">{{ __('Log Out') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @if(isset($headerActions))
                    <div class="flex flex-wrap items-center gap-2 px-0 pb-3 -mt-1 text-xs">
                        {{ $headerActions }}
                    </div>
                @endif
            </header>

            {{-- Content --}}
            <main class="flex-1 p-4 sm:p-6">
                @if(session('status'))
                    <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-emerald-700 text-sm">{{ session('status') }}</div>
                @endif
                @if(session('error'))
                    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-red-700 text-sm">{{ session('error') }}</div>
                @endif

                @if (View::hasSection('content'))
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif
            </main>
        </div>
    </div>

    @if(class_exists('\Livewire\Livewire'))
        @livewireScripts
    @endif
</body>
</html>
