<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale()==='ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('Admin') . ' — ' . config('app.name', 'Laravel'))</title>

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

        .gz-sidebar{background:#0f172a;width:250px;height:100vh;transition:transform .25s ease;flex-shrink:0;overflow-y:auto}
        .gz-sidebar-link{display:flex;align-items:center;gap:10px;padding:8px 16px;margin:1px 10px;color:rgba(255,255,255,.45);font-size:.8125rem;font-weight:500;border-radius:6px;transition:all .15s;white-space:nowrap;text-decoration:none}
        .gz-sidebar-link:hover{color:rgba(255,255,255,.85);background:rgba(255,255,255,.06)}
        .gz-sidebar-link.active{color:#fff;background:rgba(255,255,255,.1)}
        .gz-sidebar-link svg{width:18px;height:18px;flex-shrink:0;opacity:.7}
        .gz-sidebar-link.active svg{opacity:1}
        .gz-sidebar-section{padding:20px 16px 6px;font-size:.65rem;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:rgba(255,255,255,.2);margin:0 10px}

        .gz-topbar{background:#fff;border-bottom:1px solid #e2e8f0;height:56px}

        .gz-stat-card{background:#fff;border-radius:10px;padding:20px;border:1px solid #e2e8f0}
        .gz-icon-box{width:40px;height:40px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
        .gz-search{display:block;width:100%;padding:9px 14px;border:1px solid #e2e8f0;border-radius:8px;font-size:.875rem;background:#f8fafc;transition:border-color .15s,box-shadow .15s}
        .gz-search:focus{outline:none;border-color:#0f172a;box-shadow:0 0 0 3px rgba(15,23,42,.08)}
        .gz-badge{display:inline-flex;align-items:center;padding:3px 8px;border-radius:4px;font-size:.75rem;font-weight:600}
        .gz-widget{background:#fff;border-radius:10px;border:1px solid #e2e8f0;overflow:hidden}
        .gz-widget-header{padding:16px 20px 0;display:flex;align-items:center;justify-content:space-between}
        .gz-widget-body{padding:16px 20px}
        .gz-table{border-collapse:collapse}
        .gz-table th{font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:#64748b;padding:10px 14px;border-bottom:1px solid #e2e8f0;white-space:nowrap}
        .gz-table td{padding:12px 14px;font-size:.8125rem;border-bottom:1px solid #f1f5f9}
        .gz-table tr:last-child td{border-bottom:none}
        .gz-table tr:hover td{background:#f8fafc}
        .gz-table-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch}
        .gz-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-size:.8125rem;font-weight:600;transition:all .15s;cursor:pointer;white-space:nowrap;border:none}
        .gz-btn-primary{background:#0f172a;color:#fff}
        .gz-btn-primary:hover{background:#1e293b}
        .gz-btn-outline{background:transparent;color:#334155;border:1px solid #e2e8f0}
        .gz-btn-outline:hover{border-color:#94a3b8;background:#f8fafc}

        @media(max-width:1023px){
            .gz-sidebar{position:fixed;z-index:50;transform:translateX(-100%)}
            [dir="rtl"] .gz-sidebar{transform:translateX(100%)}
            .gz-sidebar.open{transform:translateX(0)}
        }
        @media(max-width:639px){
            .gz-stat-card{padding:16px}
            .gz-icon-box{width:36px;height:36px}
            .gz-widget-header{padding:12px 14px 0}
            .gz-widget-body{padding:12px 14px}
            .gz-table th{padding:8px 10px}
            .gz-table td{padding:8px 10px;font-size:.8rem}
        }
    </style>
</head>
<body x-data="{ sidebarOpen: false }" class="antialiased bg-[#f1f5f9]">
    <div class="flex min-h-screen">
        {{-- Sidebar Overlay (mobile) --}}
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
             class="fixed inset-0 bg-black/40 z-40 lg:hidden" x-transition.opacity></div>

        {{-- Sidebar --}}
        <aside class="gz-sidebar fixed lg:sticky top-0 z-50 flex flex-col"
               :class="sidebarOpen ? 'open' : ''">
            {{-- Logo --}}
            <div class="flex items-center gap-3 px-5 h-14 border-b border-white/[.06] flex-shrink-0">
                <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                </div>
                <div class="min-w-0">
                    <span class="text-white font-semibold text-sm tracking-tight block truncate">{{ config('app.name', 'Aqari Smart') }}</span>
                    <span class="block text-[10px] text-white/25 font-medium uppercase tracking-wider">{{ __('Admin') }}</span>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 py-4 overflow-y-auto">
                <div class="gz-sidebar-section mt-1">{{ __('Main') }}</div>

                <a href="{{ route('admin.index') }}" @click="if(window.innerWidth < 1024) sidebarOpen = false"
                   class="gz-sidebar-link {{ request()->routeIs('admin.index') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                    {{ __('Dashboard') }}
                </a>

                <div class="gz-sidebar-section mt-4">{{ __('Management') }}</div>

                <a href="{{ route('admin.tenants.index') }}" @click="if(window.innerWidth < 1024) sidebarOpen = false"
                   class="gz-sidebar-link {{ request()->routeIs('admin.tenants.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/></svg>
                    {{ __('Tenants') }}
                </a>

                <a href="{{ route('admin.users.index') }}" @click="if(window.innerWidth < 1024) sidebarOpen = false"
                   class="gz-sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                    {{ __('Users') }}
                </a>

                <a href="{{ route('admin.properties.index') }}" @click="if(window.innerWidth < 1024) sidebarOpen = false"
                   class="gz-sidebar-link {{ request()->routeIs('admin.properties.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205l3 1m1.5.5l-1.5-.5M6.75 7.364V3h-3v18m3-13.636l10.5-3.819"/></svg>
                    {{ __('Properties') }}
                </a>

                <a href="{{ route('admin.units.index') }}" @click="if(window.innerWidth < 1024) sidebarOpen = false"
                   class="gz-sidebar-link {{ request()->routeIs('admin.units.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                    {{ __('Units') }}
                </a>

                <a href="{{ route('admin.agents.index') }}" @click="if(window.innerWidth < 1024) sidebarOpen = false"
                   class="gz-sidebar-link {{ request()->routeIs('admin.agents.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                    {{ __('Agents') }}
                </a>

                <a href="{{ route('admin.contacts.index') }}" @click="if(window.innerWidth < 1024) sidebarOpen = false"
                   class="gz-sidebar-link {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155"/></svg>
                    {{ __('Contacts') }}
                </a>

                <a href="{{ route('admin.resident-listings.index') }}" @click="if(window.innerWidth < 1024) sidebarOpen = false"
                   class="gz-sidebar-link {{ request()->routeIs('admin.resident-listings.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
                    {{ app()->getLocale() === 'ar' ? 'إعلانات المالكين' : 'Resident Listings' }}
                </a>

                <div class="gz-sidebar-section mt-4">{{ __('Catalog') }}</div>

                <a href="{{ route('admin.categories.index') }}" @click="if(window.innerWidth < 1024) sidebarOpen = false"
                   class="gz-sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/></svg>
                    {{ __('Categories') }}
                </a>

                <a href="{{ route('admin.subcategories.index') }}" @click="if(window.innerWidth < 1024) sidebarOpen = false"
                   class="gz-sidebar-link {{ request()->routeIs('admin.subcategories.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z"/></svg>
                    {{ __('Subcategories') }}
                </a>

                <a href="{{ route('admin.attribute-fields.index') }}" @click="if(window.innerWidth < 1024) sidebarOpen = false"
                   class="gz-sidebar-link {{ request()->routeIs('admin.attribute-fields.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"/></svg>
                    {{ __('Attribute Fields') }}
                </a>

                <div class="gz-sidebar-section mt-4">{{ __('Billing') }}</div>

                <a href="{{ route('admin.packages.index') }}" @click="if(window.innerWidth < 1024) sidebarOpen = false"
                   class="gz-sidebar-link {{ request()->routeIs('admin.packages.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/></svg>
                    {{ __('Packages') }}
                </a>

                <a href="{{ route('admin.ad-durations.index') }}" @click="if(window.innerWidth < 1024) sidebarOpen = false"
                   class="gz-sidebar-link {{ request()->routeIs('admin.ad-durations.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ app()->getLocale() === 'ar' ? 'مدد الإعلان' : 'Ad Durations' }}
                </a>

                <a href="{{ route('admin.addons.index') }}" @click="if(window.innerWidth < 1024) sidebarOpen = false"
                   class="gz-sidebar-link {{ request()->routeIs('admin.addons.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 16.875h3.375m0 0h3.375m-3.375 0V13.5m0 3.375v3.375M6 10.5h2.25a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H6A2.25 2.25 0 003.75 6v2.25A2.25 2.25 0 006 10.5zm0 9.75h2.25A2.25 2.25 0 0010.5 18v-2.25a2.25 2.25 0 00-2.25-2.25H6a2.25 2.25 0 00-2.25 2.25V18A2.25 2.25 0 006 20.25zm9.75-9.75H18a2.25 2.25 0 002.25-2.25V6A2.25 2.25 0 0018 3.75h-2.25A2.25 2.25 0 0013.5 6v2.25a2.25 2.25 0 002.25 2.25z"/></svg>
                    {{ __('Add-ons') }}
                </a>

                <div class="gz-sidebar-section mt-4">{{ __('Analytics') }}</div>

                <a href="{{ route('admin.reports.index') }}" @click="if(window.innerWidth < 1024) sidebarOpen = false"
                   class="gz-sidebar-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                    {{ __('Reports') }}
                </a>

                <div class="gz-sidebar-section mt-4">{{ __('System') }}</div>

                <a href="{{ route('admin.settings.landing.edit') }}" @click="if(window.innerWidth < 1024) sidebarOpen = false"
                   class="gz-sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ __('Settings') }}
                </a>
            </nav>

            {{-- Sidebar Footer --}}
            <div class="p-3 border-t border-white/[.06] flex-shrink-0">
                <div class="flex items-center gap-2.5 px-2">
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0 text-xs font-semibold text-white">{{ substr(Auth::user()->name ?? 'A', 0, 1) }}</div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-white/80 truncate">{{ Auth::user()->name ?? 'Admin' }}</div>
                        <div class="text-[11px] text-white/30 truncate">{{ Auth::user()->email ?? '' }}</div>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Main Area --}}
        <div class="flex-1 flex flex-col min-w-0">
            {{-- Topbar --}}
            <header class="gz-topbar sticky top-0 z-30 flex items-center justify-between px-4 sm:px-6">
                <div class="flex items-center gap-3 min-w-0">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-1.5 -ml-1 rounded-md hover:bg-slate-100 transition flex-shrink-0">
                        <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                    </button>
                    @if(isset($header))
                        <span class="text-sm font-semibold text-slate-900 truncate">{{ $header }}</span>
                    @endif
                </div>

                <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
                    @if(isset($headerActions))
                        <div class="flex items-center gap-2">{{ $headerActions }}</div>
                    @endif

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
                            <div class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center text-xs font-semibold text-slate-600 flex-shrink-0">{{ substr(Auth::user()->name ?? 'A', 0, 1) }}</div>
                            <svg class="w-3.5 h-3.5 text-slate-400 hidden sm:block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                        </button>
                        <div x-show="open" x-cloak x-transition
                             class="absolute ltr:right-0 rtl:left-0 mt-1 w-44 bg-white border border-slate-200 rounded-lg shadow-lg py-1 z-50">
                            <div class="px-3 py-2 border-b border-slate-100">
                                <div class="text-sm font-medium text-slate-900 truncate">{{ Auth::user()->name ?? 'Admin' }}</div>
                                <div class="text-xs text-slate-400">{{ __('Super Admin') }}</div>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-sm text-slate-600 hover:bg-slate-50">{{ __('Profile') }}</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full ltr:text-left rtl:text-right px-3 py-2 text-sm text-slate-600 hover:bg-slate-50">{{ __('Log Out') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
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
