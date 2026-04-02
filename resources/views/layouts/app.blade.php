<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale()==='ar' ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'Laravel'))</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @if(app()->getLocale() === 'ar')
            <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
        @endif

        <!-- Gradient CSS -->
        <link href="{{ asset('css/gradient-admin.css') }}" rel="stylesheet">

        <x-vite-assets />
        @if(class_exists('\Livewire\Livewire'))
            @livewireStyles
        @endif
        @php
            $authParam = strtolower((string) request('auth', ''));
            $registerModalShouldOpen = old('register_intent') === 'resident'
                || $errors->hasAny(['name','country_code','phone','email','password','password_confirmation'])
                || $authParam === 'register';
            $loginModalShouldOpen = old('login_intent') === 'login'
                || (!$registerModalShouldOpen && $errors->hasAny(['login','password']))
                || $authParam === 'login';
        @endphp
        <script>
            document.addEventListener('alpine:init', () => {
                if (!window.Alpine) return;
                Alpine.store('auth', { login: {{ $loginModalShouldOpen ? 'true' : 'false' }}, register: {{ $registerModalShouldOpen ? 'true' : 'false' }} });
                Alpine.store('mobilemenu', { open: false });
            });
        </script>
        @php
            $tenantThemeTenant = app(\App\Services\Tenancy\TenantManager::class)->tenant();
            $theme = is_array($tenantThemeTenant?->settings ?? null) ? $tenantThemeTenant->settings : [];
        @endphp
        @if(!empty($theme['favicon_url']))
            <link rel="icon" href="{{ \Illuminate\Support\Str::startsWith($theme['favicon_url'], ['http://','https://']) ? $theme['favicon_url'] : asset($theme['favicon_url']) }}" />
        @endif
        <style>
            [x-cloak]{ display:none !important; }
            :root{ 
                --brand: {{ $theme['primary_color'] ?? '#3b82f6' }}; 
                --accent: {{ $theme['accent_color'] ?? '#06b6d4' }}; 
                --surface: #ffffff;
                --surface-hover: #f8fafc;
                --border: #e2e8f0;
                --text-primary: #0f172a;
                --text-secondary: #64748b;
                --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
                --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
                --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
                --radius: 0.75rem;
                --radius-sm: 0.5rem;
            }
            
            .card { 
                background: var(--surface); 
                border: 1px solid var(--border); 
                border-radius: var(--radius); 
                box-shadow: var(--shadow-sm);
                transition: all 0.2s ease;
            }
            .card:hover { 
                box-shadow: var(--shadow-md); 
                transform: translateY(-1px);
            }
            
            .btn-primary {
                background: linear-gradient(135deg, var(--brand), color-mix(in srgb, var(--brand) 80%, #000));
                color: white;
                border: none;
                border-radius: var(--radius-sm);
                padding: 0.75rem 1.5rem;
                font-weight: 600;
                transition: all 0.2s ease;
                box-shadow: var(--shadow-sm);
            }
            .btn-primary:hover {
                transform: translateY(-1px);
                box-shadow: var(--shadow-md);
            }
            
            .btn-secondary {
                background: var(--surface);
                color: var(--text-primary);
                border: 1px solid var(--border);
                border-radius: var(--radius-sm);
                padding: 0.75rem 1.5rem;
                font-weight: 500;
                transition: all 0.2s ease;
            }
            .btn-secondary:hover {
                background: var(--surface-hover);
                border-color: var(--brand);
            }
            
            .brand-bg{ background-color: var(--brand) !important; }
            .brand-text{ color: var(--brand) !important; }
            .brand-border{ border-color: var(--brand) !important; }
            .accent-bg{ background-color: var(--accent) !important; }
            .accent-text{ color: var(--accent) !important; }
            .accent-border{ border-color: var(--accent) !important; }
        </style>
    </head>
    @php
        $typo = $theme['typography'] ?? 'system';
        $fontClass = $typo === 'serif' ? 'font-serif' : ($typo === 'mono' ? 'font-mono' : 'font-sans');
    @endphp
    <body x-data class="antialiased {{ $fontClass }}">
        @php
            $tenantCtx = app(\App\Services\Tenancy\TenantManager::class)->tenant();
            $authUser = Auth::user();
            $pivotRole = ($tenantCtx && $authUser)
                ? optional($authUser->tenants()->whereKey($tenantCtx->getKey())->first())->pivot->role ?? null
                : null;
            $isResident = $authUser
                ? ((strtolower((string) $pivotRole) === 'resident') || (method_exists($authUser, 'hasRole') && $authUser->hasRole('resident')))
                : false;
            $navSuppressed = request()->routeIs([
                'tenant.sales-flow',
                'tenant.sales-flow.print',
                'tenant.sales-story',
                'tenant.home',
                'tenant.search',
                'tenant.unit',
                'sales-flow',
                'sales-flow.print',
                'sales-story',
                'sales-story.short',
            ]);
            $showBack = ! request()->routeIs(['tenant.home', 'home']);
            $showNav = $authUser && (! $tenantCtx || ! $isResident) && ! $navSuppressed;
            $rtl = app()->getLocale()==='ar';
            $useBrandHeader = (! $showNav) && request()->routeIs('settings.*');
        @endphp
        <div class="min-h-screen {{ $showNav ? 'bg-gradient-to-br from-slate-50 to-blue-50/30' : 'bg-gray-100' }}">
            @if($showNav)
                <!-- Top Navbar -->
                <nav class="bg-white border-b border-slate-200 sticky top-0 z-50">
                    <div class="max-w-7xl mx-auto px-4 lg:px-6">
                        <div class="flex items-center justify-between h-16">
                            <!-- Mobile Menu Button (Left in LTR, Right in RTL) -->
                            <button @click="$store.mobilemenu.open = true" class="lg:hidden p-2 rounded-lg hover:bg-slate-100 ltr:order-1 rtl:order-3">
                                <svg class="w-6 h-6 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                            </button>
                            
                            <!-- Logo (Center on mobile, Left/Right on desktop) -->
                            <div class="flex items-center gap-3 ltr:order-2 rtl:order-2">
                                @if(!empty($theme['logo_url']))
                                    <img src="{{ \Illuminate\Support\Str::startsWith($theme['logo_url'], ['http://','https://']) ? $theme['logo_url'] : asset($theme['logo_url']) }}" alt="logo" class="h-8 w-auto" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';" />
                                    <div class="h-8 w-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center" style="display: none;">
                                        <span class="text-white font-bold text-sm">{{ substr($tenantCtx?->name ?? config('app.name'), 0, 1) }}</span>
                                    </div>
                                @else
                                    <div class="h-8 w-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">{{ substr($tenantCtx?->name ?? config('app.name'), 0, 1) }}</span>
                                    </div>
                                @endif
                                <span class="text-lg font-semibold text-slate-900 hidden sm:inline">{{ $tenantCtx?->name ?? config('app.name') }}</span>
                            </div>
                            
                            <!-- Desktop Only: Language & Profile (Right in LTR, Left in RTL) -->
                            <div class="hidden lg:flex items-center gap-3 ltr:order-3 rtl:order-1">
                                @php
                                    $currentLang = app()->getLocale()==='ar' ? 'ar' : 'en';
                                    $langParam = config('locales.cookie_name', 'lang');
                                    $urlEn = request()->fullUrlWithQuery([$langParam => 'en']);
                                    $urlAr = request()->fullUrlWithQuery([$langParam => 'ar']);
                                    $settingsRoute = $tenantCtx
                                        ? (Route::has('settings.edit') ? route('settings.edit') : null)
                                        : (Route::has('admin.settings.landing.edit') ? route('admin.settings.landing.edit') : null);
                                @endphp
                                <div class="flex items-center rounded-full border border-slate-200 bg-white p-0.5 text-xs font-semibold text-slate-500">
                                    <a href="{{ $urlEn }}" class="px-2 py-1 rounded-full transition {{ $currentLang==='en' ? 'bg-gray-50 text-white' : 'hover:bg-slate-100' }}">EN</a>
                                    <a href="{{ $urlAr }}" class="px-2 py-1 rounded-full transition {{ $currentLang==='ar' ? 'bg-gray-50 text-white' : 'hover:bg-slate-100' }}">ع</a>
                                </div>
                                @auth
                                    <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                                        <button @click="open = !open" class="focus:outline-none inline-flex items-center">
                                            <img alt="avatar" class="h-8 w-8 rounded-full ring-2 ring-slate-200" src="{{ 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=0f172a&color=fff' }}">
                                        </button>
                                        <div x-show="open" x-cloak x-transition class="absolute ltr:left-0 rtl:right-0 mt-2 bg-white border border-slate-200 py-1 rounded-md shadow-lg z-50 w-48">
                                            @if($tenantCtx)
                                                <a href="{{ route('dashboard') }}" class="block px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">{{ __('Dashboard') }}</a>
                                            @elseif(Auth::user()?->hasRole('admin*'))
                                                        $isAdminContext = !isset($tenantCtx) || $tenantCtx === null || request()->is('admin*');

                                                <a href="{{ route('admin.index') }}" class="block px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">{{ __('Dashboard') }}</a>
                                            @endif
                                            <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">{{ __('Profile') }}</a>
                                            @if($settingsRoute)
                                                <a href="{{ $settingsRoute }}" class="block px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">{{ __('Settings') }}</a>
                                            @endif
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit" class="block w-full px-3 py-2 text-left text-sm text-slate-700 hover:bg-slate-50">{{ __('Log Out') }}</button>
                                            </form>
                                        </div>
                                    </div>
                                @endauth
                            </div>
                            
                            <!-- Mobile Spacer -->
                            <div class="lg:hidden w-10 ltr:order-3 rtl:order-1"></div>
                        </div>
                    </div>
                </nav>
                
                <!-- Mobile Sidebar Menu -->
                @include('layouts.navigation-mobile')
                
                <!-- Sub Navigation -->
                <div class="bg-white border-b border-slate-200 hidden lg:block">
                    <div class="max-w-7xl mx-auto px-4 lg:px-6">
                        <div class="flex items-center gap-1 py-2  scrollbar-hide">
                            @include('layouts.navigation')
                        </div>
                    </div>
                </div>
                
                <!-- Main Content -->
                <main class="max-w-7xl mx-auto px-4 lg:px-6 py-6">
                    @if (View::hasSection('content'))
                        @yield('content')
                    @else
                        {{ $slot ?? '' }}
                    @endif
                </main>
            @else
                <header class="{{ ($useBrandHeader ?? false) ? 'brand-bg text-white' : 'bg-white' }} flex items-center justify-between shadow px-4 h-[72px]">
                    <div class="flex items-center gap-3 ltr:order-1 rtl:order-2">
                        <a href="{{ app(\App\Services\Tenancy\TenantManager::class)->tenant() ? route('tenant.home') : route('home') }}" class="inline-flex items-center">
                            @if(!empty($theme['logo_url']))
                                <img src="{{ \Illuminate\Support\Str::startsWith($theme['logo_url'], ['http://','https://']) ? $theme['logo_url'] : asset($theme['logo_url']) }}" alt="logo" class="h-8 w-auto" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />
                                <div class="h-8 w-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center" style="display: none;">
                                    <span class="text-white font-bold text-sm">{{ substr(config('app.name'), 0, 1) }}</span>
                                </div>
                            @else
                                <div class="h-8 w-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                    <span class="text-white font-bold text-sm">{{ substr(config('app.name'), 0, 1) }}</span>
                                </div>
                            @endif
                        </a>
                        <div class="leading-tight">
                            <h2 class="font-semibold text-xl {{ ($useBrandHeader ?? false) ? 'text-white' : 'text-gray-800' }}">{{ config('app.name') }}</h2>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 relative ltr:order-2 rtl:order-1">
                        @php
                            $currentLang = app()->getLocale()==='ar' ? 'ar' : 'en';
                            $langParam = config('locales.cookie_name', 'lang');
                            $urlEn = request()->fullUrlWithQuery([$langParam => 'en']);
                            $urlAr = request()->fullUrlWithQuery([$langParam => 'ar']);
                        @endphp
                        <div class="flex items-center rounded-full border {{ ($useBrandHeader ?? false) ? 'border-white/30' : 'border-gray-300' }} p-0.5" title="{{ __('Switch language') }}" aria-label="{{ __('Switch language') }}">
                            <a href="{{ $urlEn }}" class="px-2 py-1 text-xs rounded-full transition {{ $currentLang==='en' ? (($useBrandHeader ?? false) ? 'bg-white/20 text-white' : 'bg-indigo-600 text-white') : (($useBrandHeader ?? false) ? 'text-white hover:bg-white/10' : 'text-gray-700 hover:bg-gray-50') }}">EN</a>
                            <a href="{{ $urlAr }}" class="px-2 py-1 text-xs rounded-full transition {{ $currentLang==='ar' ? (($useBrandHeader ?? false) ? 'bg-white/20 text-white' : 'bg-indigo-600 text-white') : (($useBrandHeader ?? false) ? 'text-white hover:bg-white/10' : 'text-gray-700 hover:bg-gray-50') }}">ع</a>
                        </div>
                        @auth
                            @php
                                $userTenants = Auth::user()->tenants()->orderBy('name')->get();
                            @endphp
                            <div class="hidden sm:block text-sm {{ ($useBrandHeader ?? false) ? 'text-white' : 'text-gray-700' }}">{{ explode(' ', Auth::user()->name)[0] ?? Auth::user()->name }}</div>
                            <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                                <button @click="open = !open" class="focus:outline-none inline-flex items-center">
                                    <img alt="avatar" class="h-8 w-8 rounded-full ring-2 ring-indigo-100" src="{{ 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=4f46e5&color=fff' }}">
                                </button>
                                <div x-show="open" x-cloak x-transition class="absolute bg-white border border-gray-200 py-1 rounded-md shadow-lg z-50 w-56 ltr:right-0 rtl:right-0 top-full mt-2">
                                @php
                                    $tenantNav = app(\App\Services\Tenancy\TenantManager::class)->tenant();
                                @endphp
                                @if($tenantNav)
                                    <a href="{{ route('dashboard') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ __('Dashboard') }}</a>
                                @elseif(Auth::user()?->hasRole('admin*'))
                                    <a href="{{ route('admin.index') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ __('Dashboard') }}</a>
                                @endif
                                @if((method_exists(Auth::user(), 'hasRole') && Auth::user()->hasRole('resident')) || ($tenantNav && (strtolower((string) (optional(Auth::user()->tenants()->whereKey($tenantNav->getKey())->first())->pivot->role ?? null)) === 'resident')))
                                    <a href="{{ route('resident.profile') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ __('Profile') }}</a>
                                @else
                                    <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ __('Profile') }}</a>
                                @endif
                                @if($userTenants->count() > 1)
                                    <div class="my-1 border-t"></div>
                                    <div class="px-3 py-2 text-xs font-semibold uppercase text-gray-400">{{ __('Switch Tenant') }}</div>
                                    <div class="max-h-56 overflow-y-auto">
                                        @foreach($userTenants as $t)
                                            <form method="post" action="{{ route('tenant.switch') }}">
                                                @csrf
                                                <input type="hidden" name="tenant_id" value="{{ $t->id }}" />
                                                <button type="submit" class="block w-full px-3 py-2 text-left text-sm hover:bg-gray-50 {{ optional(app(\App\Services\Tenancy\TenantManager::class)->tenant())->id === $t->id ? 'text-indigo-700 font-medium' : 'text-gray-700' }}">
                                                    {{ $t->name }}
                                                </button>
                                            </form>
                                        @endforeach
                                    </div>
                                @endif
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">{{ __('Log Out') }}</button>
                                </form>
                            </div>
                        @endauth
                        @guest
                            @if(app(\App\Services\Tenancy\TenantManager::class)->tenant())
                                <a href="{{ route('tenant.home') }}?auth=login" @click.prevent="$store.auth.login = true" class="hidden sm:inline-flex items-center rounded-md px-3 py-1.5 text-sm font-medium {{ ($useBrandHeader ?? false) ? 'text-white hover:bg-white/10 border border-white/30' : 'text-indigo-600 border border-indigo-200 hover:bg-indigo-50' }}">{{ __('Log in') }}</a>
                                <a href="{{ route('tenant.home') }}?auth=register" @click.prevent="$store.auth.register = true" class="hidden sm:inline-flex items-center rounded-md px-3 py-1.5 text-sm font-semibold {{ ($useBrandHeader ?? false) ? 'bg-white text-indigo-600 hover:bg-white/90' : 'bg-indigo-600 text-white hover:bg-indigo-700' }}">{{ __('Create account') }}</a>
                            @else
                                <a href="{{ route('login') }}" class="hidden sm:block text-sm {{ ($useBrandHeader ?? false) ? 'text-white' : 'text-indigo-600' }} hover:underline">{{ __('Log in') }}</a>
                                <a href="{{ route('register') }}" class="hidden sm:inline-flex ml-2 items-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-indigo-700">{{ __('Create account') }}</a>
                            @endif
                        @endguest
                    </div>
                </header>
                <div class="min-h-screen bg-gray-100">
                    <main>
                        @if (View::hasSection('content'))
                            @yield('content')
                        @else
                            {{ $slot ?? '' }}
                        @endif
                    </main>
                </div>
            @endif
        </div>
        @guest
            @include('partials.auth-modals')
        @endguest
        @if(class_exists('\Livewire\Livewire'))
            @livewireScripts
        @endif
    </body>
</html>
