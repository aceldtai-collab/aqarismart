<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Aqari Smart' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>window.__AQARI_API_BASE = @json(config('nativephp.remote_api_url') ?: 'https://aqarismart.com');</script>
    <script>
        // Override fetch to include cookies for credentialed requests to the API
        // when the API base is remote and no Authorization header is present.
        (function() {
            const _fetch = window.fetch && window.fetch.bind(window);
            if (! _fetch) return;
            window.fetch = function(input, init = {}) {
                try {
                    const apiBase = window.__AQARI_API_BASE || '';
                    let url = (typeof input === 'string') ? input : (input && input.url) || '';
                    if (apiBase && typeof url === 'string' && url.indexOf(apiBase) === 0) {
                        init = Object.assign({}, init || {});
                        const headers = (init.headers && typeof init.headers === 'object') ? init.headers : {};
                        const hasAuth = headers.Authorization || headers.authorization || headers['Authorization'] || headers['authorization'];
                        if (! hasAuth && !('credentials' in init)) {
                            init.credentials = 'include';
                        }
                    }
                } catch (e) {
                    // ignore errors and fall back to native fetch
                }
                return _fetch(input, init);
            };
        })();
    </script>
    @stack('head')
    <style>
        [x-cloak]{display:none!important}
        .aqr-register-overlay{position:fixed;inset:0;z-index:9999;display:flex;align-items:flex-end;justify-content:center}
        @media(min-width:640px){.aqr-register-overlay{align-items:center;padding:1rem}}
    </style>
</head>
@php
    $currentLocale = in_array(app()->getLocale(), ['en', 'ar']) ? app()->getLocale() : 'en';
    $langToggleEn = url(request()->path()) . '?lang=en';
    $langToggleAr = url(request()->path()) . '?lang=ar';
    $countryCodes = config('phone.codes', []);
    $defaultCountry = config('phone.default', '+962');
    if (empty($countryCodes)) {
        $countryCodes = [$defaultCountry => $defaultCountry];
    }
@endphp
<body class="bg-gray-50 min-h-screen text-slate-800 {{ $body_class ?? '' }}">
    <div
        x-data="mobileShell({
            defaultCountry: @js($defaultCountry),
            marketplaceUrl: @js(route('mobile.marketplace')),
            profileUrl: @js(route('mobile.profile')),
            dashboardUrl: @js(route('mobile.dashboard')),
            residentRegisterPath: @js(route('api.mobile.auth.register-resident', [], false)),
            shouldAutoOpenRegister: @js((request()->routeIs('mobile.marketplace') || request()->routeIs('mobile.search')) && request()->query('auth') === 'register'),
        })"
        x-init="if (shouldAutoOpenRegister) openResidentRegister()"
        class="min-h-screen"
    >
        <aside class="overflow-auto fixed inset-y-0 left-0 rtl:left-auto rtl:right-0 z-40 w-[300px] transform bg-white shadow-2xl transition-all duration-300" :class="open ? 'translate-x-0 rtl:-translate-x-0' : '-translate-x-full rtl:translate-x-full'">
            <div class="relative bg-gradient-to-br from-emerald-600 to-emerald-700 px-6 pb-6 pt-[max(1.5rem,env(safe-area-inset-top,1.5rem))]">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm">
                            <img src="{{ asset('images/logotest.png') }}" alt="Aqari Smart" class="h-7 w-7 object-contain">
                        </div>
                        <div>
                            <div class="text-lg font-bold text-white tracking-tight">Aqari Smart</div>
                            <div class="text-xs font-medium text-emerald-100/80" x-text="authed ? userName : '{{ app()->getLocale() === 'ar' ? 'مرحباً' : 'Welcome' }}'"></div>
                        </div>
                    </div>
                    <button type="button" class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/15 backdrop-blur-sm transition-colors hover:bg-white/25" @click="open = false">
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <nav class="flex flex-col gap-1 px-4 py-5">
                <a href="{{ route('mobile.marketplace') }}" class="group flex items-center gap-3.5 rounded-xl px-4 py-3 transition-all duration-150 {{ request()->routeIs('mobile.marketplace') || request()->routeIs('mobile.search') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-700 hover:bg-slate-50' }}">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ request()->routeIs('mobile.marketplace') || request()->routeIs('mobile.search') ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-emerald-50 group-hover:text-emerald-600' }} transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <div class="text-sm font-semibold">{{ app()->getLocale() === 'ar' ? 'السوق' : 'Marketplace' }}</div>
                        <div class="text-[11px] font-medium {{ request()->routeIs('mobile.marketplace') || request()->routeIs('mobile.search') ? 'text-emerald-500' : 'text-slate-400' }}">{{ app()->getLocale() === 'ar' ? 'تصفح العقارات' : 'Browse properties' }}</div>
                    </div>
                </a>
                <a href="{{ route('mobile.tenants.index') }}" class="group flex items-center gap-3.5 rounded-xl px-4 py-3 transition-all duration-150 {{ request()->routeIs('mobile.tenants.*') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-700 hover:bg-slate-50' }}">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ request()->routeIs('mobile.tenants.*') ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-emerald-50 group-hover:text-emerald-600' }} transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <div class="text-sm font-semibold">{{ app()->getLocale() === 'ar' ? 'الوكالات' : 'Agencies' }}</div>
                        <div class="text-[11px] font-medium {{ request()->routeIs('mobile.tenants.*') ? 'text-emerald-500' : 'text-slate-400' }}">{{ app()->getLocale() === 'ar' ? 'تصفح الوكالات' : 'Browse agencies' }}</div>
                    </div>
                </a>
                <a x-show="authed && hasTenantAccess" x-cloak href="{{ route('mobile.dashboard') }}" class="group flex items-center gap-3.5 rounded-xl px-4 py-3 transition-all duration-150 {{ request()->routeIs('mobile.dashboard') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-700 hover:bg-slate-50' }}">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ request()->routeIs('mobile.dashboard') ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-emerald-50 group-hover:text-emerald-600' }} transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <div>
                        <div class="text-sm font-semibold">{{ app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Dashboard' }}</div>
                        <div class="text-[11px] font-medium {{ request()->routeIs('mobile.dashboard') ? 'text-emerald-500' : 'text-slate-400' }}">{{ app()->getLocale() === 'ar' ? 'نظرة عامة' : 'Overview' }}</div>
                    </div>
                </a>
                <a x-show="authed && isStaff" x-cloak href="{{ route('mobile.units.index') }}" class="group flex items-center gap-3.5 rounded-xl px-4 py-3 transition-all duration-150 {{ request()->routeIs('mobile.units.*') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-700 hover:bg-slate-50' }}">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ request()->routeIs('mobile.units.*') ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-emerald-50 group-hover:text-emerald-600' }} transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                    </div>
                    <div>
                        <div class="text-sm font-semibold">{{ app()->getLocale() === 'ar' ? 'الوحدات' : 'My Units' }}</div>
                        <div class="text-[11px] font-medium {{ request()->routeIs('mobile.units.*') ? 'text-emerald-500' : 'text-slate-400' }}">{{ app()->getLocale() === 'ar' ? 'إدارة العقارات' : 'Manage listings' }}</div>
                    </div>
                </a>

                <div class="my-2 border-t border-slate-100"></div>

                <div x-show="!authed" class="flex flex-col gap-1">
                    <a href="{{ route('mobile.login') }}" class="group flex items-center gap-3.5 rounded-xl px-4 py-3 text-slate-700 transition-all duration-150 hover:bg-slate-50">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition-colors group-hover:bg-emerald-50 group-hover:text-emerald-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold">{{ app()->getLocale() === 'ar' ? 'تسجيل الدخول' : 'Sign in' }}</div>
                            <div class="text-[11px] font-medium text-slate-400">{{ app()->getLocale() === 'ar' ? 'دخول إلى حسابك' : 'Access your account' }}</div>
                        </div>
                    </a>
                    <button type="button" @click="openResidentRegister()" class="group flex w-full items-center gap-3.5 rounded-xl px-4 py-3 text-left text-slate-700 transition-all duration-150 hover:bg-slate-50">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700 transition-colors group-hover:bg-amber-100">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-amber-800">{{ app()->getLocale() === 'ar' ? 'إنشاء حساب' : 'Register' }}</div>
                            <div class="text-[11px] font-medium text-slate-400">{{ app()->getLocale() === 'ar' ? 'للباحثين عن العقارات' : 'For buyers & renters' }}</div>
                        </div>
                    </button>
                    <a href="{{ route('mobile.register') }}" class="group flex items-center gap-3.5 rounded-xl px-4 py-3 text-slate-700 transition-all duration-150 hover:bg-slate-50">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 transition-colors group-hover:bg-emerald-100">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-emerald-700">{{ app()->getLocale() === 'ar' ? 'بيع معنا' : 'Sell with us' }}</div>
                            <div class="text-[11px] font-medium text-slate-400">{{ app()->getLocale() === 'ar' ? 'سجّل عقارك الآن' : 'List your property' }}</div>
                        </div>
                    </a>
                </div>

                <a x-show="authed" x-cloak href="{{ route('mobile.profile') }}" class="group flex items-center gap-3.5 rounded-xl px-4 py-3 text-slate-700 transition-all duration-150 {{ request()->routeIs('mobile.profile') ? 'bg-emerald-50 text-emerald-700' : 'hover:bg-slate-50' }}">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ request()->routeIs('mobile.profile') ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-emerald-50 group-hover:text-emerald-600' }} transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <div class="text-sm font-semibold">{{ app()->getLocale() === 'ar' ? 'الملف الشخصي' : 'My Profile' }}</div>
                        <div class="text-[11px] font-medium {{ request()->routeIs('mobile.profile') ? 'text-emerald-500' : 'text-slate-400' }}">{{ app()->getLocale() === 'ar' ? 'معلوماتك ونشاطك' : 'Your info & activity' }}</div>
                    </div>
                </a>

                <a x-show="authed" x-cloak href="{{ route('mobile.my-listings.index') }}" class="group flex items-center gap-3.5 rounded-xl px-4 py-3 text-slate-700 transition-all duration-150 {{ request()->routeIs('mobile.my-listings.*') ? 'bg-green-50 text-green-700' : 'hover:bg-slate-50' }}">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ request()->routeIs('mobile.my-listings.*') ? 'bg-green-600 text-white' : 'bg-green-50 text-green-600 group-hover:bg-green-100' }} transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <div>
                        <div class="text-sm font-semibold {{ request()->routeIs('mobile.my-listings.*') ? 'text-green-700' : '' }}">{{ app()->getLocale() === 'ar' ? 'إعلاناتي' : 'My Listings' }}</div>
                        <div class="text-[11px] font-medium text-slate-400">{{ app()->getLocale() === 'ar' ? 'إدارة عقاراتك المنشورة' : 'Manage your posted properties' }}</div>
                    </div>
                </a>

                <button x-show="authed" x-cloak type="button" class="group flex w-full items-center gap-3.5 rounded-xl px-4 py-3 text-left text-slate-700 transition-all duration-150 hover:bg-red-50" @click="clearAuth()">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-500 transition-colors group-hover:bg-red-100">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-red-600">{{ app()->getLocale() === 'ar' ? 'تسجيل الخروج' : 'Sign out' }}</div>
                        <div class="text-[11px] font-medium text-slate-400">{{ app()->getLocale() === 'ar' ? 'خروج من حسابك' : 'Log out of your account' }}</div>
                    </div>
                </button>
            </nav>

            <div class="mt-auto border-t border-slate-100 px-6 py-4">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-medium uppercase tracking-widest text-slate-300">{{ app()->getLocale() === 'ar' ? 'اللغة' : 'Language' }}</span>
                    <div class="flex items-center gap-0.5 rounded-full bg-slate-100 p-0.5 text-[11px] font-semibold">
                        <a href="{{ $langToggleEn }}" class="rounded-full px-3 py-1 transition {{ $currentLocale === 'en' ? 'bg-white text-emerald-700 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">EN</a>
                        <a href="{{ $langToggleAr }}" class="rounded-full px-3 py-1 transition {{ $currentLocale === 'ar' ? 'bg-white text-emerald-700 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">ع</a>
                    </div>
                </div>
            </div>
        </aside>
        <div class="fixed inset-0 z-30 bg-slate-950/40 backdrop-blur-sm" x-show="open" x-transition.opacity @click="open = false"></div>

        <div class="relative min-h-screen">
            <div class="bg-emerald-700" style="padding-top: env(safe-area-inset-top, 0px)"></div>

            <header class="sticky top-0 z-20 bg-emerald-700 shadow-sm">
                <div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-3 sm:px-6">
                    <div class="flex items-center gap-2.5">
                        @if(!isset($show_back_button) || $show_back_button !== false)
                        @if(request()->header('Referer') || url()->previous() !== url()->current())
                        <button type="button" onclick="history.back()" class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/10 transition-colors hover:bg-white/20">
                            <svg class="h-5 w-5 text-white rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                        </button>
                        @endif
                        @endif
                        <button type="button" class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/10 transition-colors hover:bg-white/20" @click="open = true">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <div class="flex items-center gap-2">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/15">
                                <img src="{{ asset('images/logotest.png') }}" alt="Aqari Smart" class="h-5 w-5 object-contain">
                            </div>
                            <span class="text-[15px] font-bold text-white tracking-tight">{{ $title ?? 'Aqari Smart' }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-0.5 rounded-full bg-white/10 p-0.5 text-[11px] font-semibold">
                        <a href="{{ $langToggleEn }}" class="rounded-full px-2.5 py-1 transition {{ $currentLocale === 'en' ? 'bg-white text-emerald-700 shadow-sm' : 'text-white/80 hover:bg-white/15' }}">EN</a>
                        <a href="{{ $langToggleAr }}" class="rounded-full px-2.5 py-1 transition {{ $currentLocale === 'ar' ? 'bg-white text-emerald-700 shadow-sm' : 'text-white/80 hover:bg-white/15' }}">ع</a>
                    </div>
                </div>
            </header>
            <main class="mx-auto max-w-5xl @hasSection('full_width') @else px-4 py-6 sm:px-6 @endif">
                @yield('content')
            </main>
        </div>

        <template x-teleport="body">
            <div x-show="residentRegisterOpen" x-cloak class="aqr-register-overlay"
                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                <div style="position:absolute;inset:0;background:rgba(0,0,0,.75);backdrop-filter:blur(4px)" @click="closeResidentRegister()"></div>
                <div style="position:relative;z-index:1;width:100%;max-width:28rem;max-height:90vh;display:flex;flex-direction:column;overflow:hidden;border-radius:1rem 1rem 0 0;box-shadow:0 -10px 40px rgba(0,0,0,.3)"
                     class="sm:!rounded-2xl sm:!mb-0">
                    {{-- Header --}}
                    <div style="flex-shrink:0;padding:1.25rem 1.5rem 1rem;background:linear-gradient(135deg,#065f46,#047857,#d97706);color:#fff;border-radius:1rem 1rem 0 0" class="sm:!rounded-t-2xl">
                        <div style="display:flex;align-items:start;justify-content:space-between">
                            <div>
                                <h2 style="font-size:1.25rem;font-weight:700;margin:0">{{ app()->getLocale() === 'ar' ? 'إنشاء حساب مستخدم' : 'Create your account' }}</h2>
                                <p style="font-size:.8rem;margin-top:.35rem;color:rgba(255,255,255,.8)">{{ app()->getLocale() === 'ar' ? 'حساب واحد للبحث والشراء أو الإيجار' : 'One account for browsing, buying, or renting' }}</p>
                            </div>
                            <button type="button" @click="closeResidentRegister()" style="color:rgba(255,255,255,.7);cursor:pointer;background:none;border:none;padding:0.25rem">
                                <svg style="width:1.25rem;height:1.25rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>
                    {{-- Form --}}
                    <form style="flex:1;overflow-y:auto;overscroll-behavior:contain;padding:1rem 1.25rem;background:#fff;display:flex;flex-direction:column;gap:.85rem" @submit.prevent="submitResidentRegister">
                        <div x-show="residentRegisterError" x-cloak style="padding:.65rem 1rem;border-radius:.5rem;background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;font-size:.85rem" x-html="residentRegisterError"></div>

                        <div>
                            <label style="display:block;font-size:.75rem;font-weight:700;color:#1e293b;margin-bottom:.35rem">{{ app()->getLocale() === 'ar' ? 'الاسم الكامل' : 'Full name' }}</label>
                            <input x-model="residentForm.name" type="text" placeholder="{{ app()->getLocale() === 'ar' ? 'أدخل اسمك' : 'Enter your name' }}" required
                                   style="width:100%;padding:.6rem .85rem;border:1px solid #d1d5db;border-radius:.75rem;font-size:.875rem;outline:none;background:#fff">
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 2fr;gap:.6rem">
                            <div>
                                <label style="display:block;font-size:.75rem;font-weight:700;color:#1e293b;margin-bottom:.35rem">{{ app()->getLocale() === 'ar' ? 'الرمز' : 'Code' }}</label>
                                <select x-model="residentForm.country_code" required
                                        style="width:100%;padding:.6rem .4rem;border:1px solid #d1d5db;border-radius:.75rem;font-size:.875rem;outline:none;background:#fff">
                                    @foreach($countryCodes as $code => $label)
                                        <option value="{{ $code }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label style="display:block;font-size:.75rem;font-weight:700;color:#1e293b;margin-bottom:.35rem">{{ app()->getLocale() === 'ar' ? 'رقم الهاتف' : 'Phone' }}</label>
                                <input x-model="residentForm.phone" type="tel" placeholder="7XX XXX XXXX" required
                                       style="width:100%;padding:.6rem .85rem;border:1px solid #d1d5db;border-radius:.75rem;font-size:.875rem;outline:none;background:#fff">
                            </div>
                        </div>

                        <div>
                            <label style="display:block;font-size:.75rem;font-weight:700;color:#1e293b;margin-bottom:.35rem">{{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email' }} <span style="font-weight:400;color:#94a3b8">({{ app()->getLocale() === 'ar' ? 'اختياري' : 'optional' }})</span></label>
                            <input x-model="residentForm.email" type="email" placeholder="email@example.com"
                                   style="width:100%;padding:.6rem .85rem;border:1px solid #d1d5db;border-radius:.75rem;font-size:.875rem;outline:none;background:#fff">
                        </div>

                        <div>
                            <label style="display:block;font-size:.75rem;font-weight:700;color:#1e293b;margin-bottom:.35rem">{{ app()->getLocale() === 'ar' ? 'كلمة المرور' : 'Password' }}</label>
                            <input x-model="residentForm.password" type="password" required
                                   style="width:100%;padding:.6rem .85rem;border:1px solid #d1d5db;border-radius:.75rem;font-size:.875rem;outline:none;background:#fff">
                        </div>

                        <div>
                            <label style="display:block;font-size:.75rem;font-weight:700;color:#1e293b;margin-bottom:.35rem">{{ app()->getLocale() === 'ar' ? 'تأكيد كلمة المرور' : 'Confirm' }}</label>
                            <input x-model="residentForm.password_confirmation" type="password" required
                                   style="width:100%;padding:.6rem .85rem;border:1px solid #d1d5db;border-radius:.75rem;font-size:.875rem;outline:none;background:#fff">
                        </div>

                        <div style="display:flex;gap:.75rem;padding-top:.25rem">
                            <button type="button" @click="closeResidentRegister()"
                                    style="flex:1;padding:.6rem 1rem;border:1px solid #d1d5db;border-radius:.75rem;background:#fff;font-size:.875rem;font-weight:600;color:#475569;cursor:pointer">
                                {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                            </button>
                            <button type="submit" :disabled="residentRegisterLoading"
                                    style="flex:1;padding:.6rem 1rem;border:none;border-radius:.75rem;background:linear-gradient(90deg,#065f46,#d97706);font-size:.875rem;font-weight:600;color:#fff;cursor:pointer;box-shadow:0 4px 12px rgba(6,95,70,.4)">
                                <span x-show="!residentRegisterLoading">{{ app()->getLocale() === 'ar' ? 'إنشاء الحساب' : 'Create account' }}</span>
                                <span x-show="residentRegisterLoading" x-cloak>{{ app()->getLocale() === 'ar' ? 'جاري...' : 'Creating...' }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>
    <script>
    function mobileShell(config) {
        return {
            open: false,
            authed: !!localStorage.getItem('aqari_mobile_token'),
            userName: localStorage.getItem('aqari_mobile_user_name') || '',
            tenantSlug: localStorage.getItem('aqari_mobile_tenant_slug') || '',
            userRole: localStorage.getItem('aqari_mobile_user_role') || '',
            shouldAutoOpenRegister: !!config.shouldAutoOpenRegister,
            residentRegisterOpen: false,
            residentRegisterLoading: false,
            residentRegisterError: '',
            residentForm: {
                name: '',
                country_code: config.defaultCountry,
                phone: '',
                email: '',
                password: '',
                password_confirmation: '',
            },
            get hasTenantAccess() {
                return this.authed && this.tenantSlug !== '' && this.userRole !== '';
            },
            get isStaff() {
                return this.hasTenantAccess && this.userRole !== 'resident';
            },
            openResidentRegister() {
                this.open = false;
                this.residentRegisterError = '';
                this.residentRegisterOpen = true;
            },
            closeResidentRegister() {
                this.residentRegisterOpen = false;
                this.residentRegisterLoading = false;
                this.residentRegisterError = '';
            },
            persistAuth(json) {
                const token = json.token || '';
                const tenantSlug = json.current_tenant?.slug || '';
                const userName = json.user?.name || '';
                const userRole = json.tenant_role || json.user?.tenant_role || '';

                localStorage.setItem('aqari_mobile_token', token);

                if (tenantSlug) {
                    localStorage.setItem('aqari_mobile_tenant_slug', tenantSlug);
                } else {
                    localStorage.removeItem('aqari_mobile_tenant_slug');
                }

                if (userName) {
                    localStorage.setItem('aqari_mobile_user_name', userName);
                } else {
                    localStorage.removeItem('aqari_mobile_user_name');
                }

                if (userRole) {
                    localStorage.setItem('aqari_mobile_user_role', userRole);
                } else {
                    localStorage.removeItem('aqari_mobile_user_role');
                }

                this.authed = !!token;
                this.userName = userName;
                this.tenantSlug = tenantSlug;
                this.userRole = userRole;
            },
            clearAuth() {
                localStorage.removeItem('aqari_mobile_token');
                localStorage.removeItem('aqari_mobile_tenant_slug');
                localStorage.removeItem('aqari_mobile_user_name');
                localStorage.removeItem('aqari_mobile_user_role');
                this.authed = false;
                this.userName = '';
                this.tenantSlug = '';
                this.userRole = '';
                this.open = false;
                this.residentRegisterOpen = false;
                window.location.href = config.marketplaceUrl;
            },
            flattenErrors(json) {
                if (json?.errors) {
                    return Object.values(json.errors).flat().join('<br>');
                }

                return json?.message || 'Registration failed';
            },
            async submitResidentRegister() {
                if (this.residentRegisterLoading) {
                    return;
                }

                this.residentRegisterError = '';
                this.residentRegisterLoading = true;

                try {
                    const res = await fetch((window.__AQARI_API_BASE || '') + config.residentRegisterPath, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            Accept: 'application/json',
                        },
                        body: JSON.stringify(this.residentForm),
                    });
                    const json = await res.json();

                    if (!res.ok) {
                        this.residentRegisterError = this.flattenErrors(json);
                        return;
                    }

                    this.persistAuth(json);
                    this.closeResidentRegister();
                    window.location.href = config.profileUrl;
                } catch (error) {
                    this.residentRegisterError = error.message || 'Connection error';
                } finally {
                    this.residentRegisterLoading = false;
                }
            },
        };
    }
    </script>
    @stack('scripts')
</body>
</html>
