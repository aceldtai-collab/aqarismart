<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Aqari Smart' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>window.__AQARI_API_BASE = '{{ config("nativephp.remote_api_url", "") }}';</script>
    @stack('head')
    <style>[x-cloak]{display:none!important}</style>
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
    $showResidentRegister = request()->routeIs('mobile.marketplace') || request()->routeIs('mobile.search');
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
                    @if($showResidentRegister)
                        <button type="button" @click="openResidentRegister()" class="group flex w-full items-center gap-3.5 rounded-xl px-4 py-3 text-left text-slate-700 transition-all duration-150 hover:bg-slate-50">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700 transition-colors group-hover:bg-amber-100">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 4v16m8-8H4m4-6.5h8a3.5 3.5 0 013.5 3.5v6A3.5 3.5 0 0116 18.5H8A3.5 3.5 0 014.5 15v-6A3.5 3.5 0 018 5.5z"/></svg>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-amber-800">{{ app()->getLocale() === 'ar' ? 'إنشاء حساب' : 'Register' }}</div>
                                <div class="text-[11px] font-medium text-slate-400">{{ app()->getLocale() === 'ar' ? 'للباحثين عن العقارات' : 'For buyers & renters' }}</div>
                            </div>
                        </button>
                    @endif
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

        @if($showResidentRegister)
            <div x-show="residentRegisterOpen" x-cloak class="fixed inset-0 z-[60] flex items-end justify-center sm:items-center">
                <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="closeResidentRegister()"></div>
                <div
                    class="relative z-10 w-full max-w-md overflow-hidden rounded-t-[2rem] border border-[#dcccae] bg-[#fbf7ef] shadow-[0_30px_70px_-35px_rgba(36,27,10,.55)] sm:rounded-[2rem]"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-8 sm:scale-95 sm:translate-y-0"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-8 sm:scale-95 sm:translate-y-0"
                >
                    <div class="rounded-t-[2rem] bg-gradient-to-br from-[#10231b] via-[#0f5a46] to-[#b6842f] px-6 pb-5 pt-6 text-white sm:rounded-t-[2rem]">
                        <button type="button" @click="closeResidentRegister()" class="absolute top-4 {{ app()->getLocale() === 'ar' ? 'left-4' : 'right-4' }} text-white/70 transition hover:text-white">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                        <div class="inline-flex items-center rounded-full border border-white/10 bg-white/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-white/85">
                            Aqari Smart
                        </div>
                        <div class="mt-4 h-[10px] w-[104px] rounded-full bg-[linear-gradient(90deg,rgba(255,255,255,.18),rgba(255,222,164,.72),rgba(255,255,255,.18))]"></div>
                        <h2 class="mt-4 text-2xl font-bold">{{ app()->getLocale() === 'ar' ? 'إنشاء حساب مستخدم' : 'Create your account' }}</h2>
                        <p class="mt-2 text-sm leading-7 text-white/80">{{ app()->getLocale() === 'ar' ? 'أنشئ حساباً واحداً للبحث والشراء أو الإيجار عبر جميع الوكالات داخل السوق، ثم أكمل رحلتك من ملفك الشخصي على الموبايل.' : 'Create one buyer account for browsing, buying, or renting across every agency in the marketplace, then continue from your mobile profile.' }}</p>
                    </div>

                    <form class="max-h-[80vh] space-y-4 overflow-y-auto bg-[linear-gradient(180deg,rgba(255,250,242,.98),rgba(246,236,214,.88))] px-6 py-5" @submit.prevent="submitResidentRegister">
                        <div x-show="residentRegisterError" x-cloak class="rounded-[1.25rem] border border-rose-200 bg-rose-50/90 px-4 py-3 text-sm text-rose-700 ring-1 ring-rose-100" x-html="residentRegisterError"></div>

                        <div class="rounded-[1.4rem] border border-[#d8cab1] bg-white/80 p-4 shadow-[0_20px_44px_-34px_rgba(55,38,12,.34)]">
                            <div class="text-[11px] font-extrabold uppercase tracking-[0.2em] text-[#b6842f]">{{ app()->getLocale() === 'ar' ? 'حساب الباحثين' : 'Buyer profile' }}</div>
                            <p class="mt-2 text-sm leading-7 text-slate-600">{{ app()->getLocale() === 'ar' ? 'هذا الحساب مخصص للبحث وحفظ النشاط وإدارة التفضيلات، وليس لإدارة وكالة أو لوحة موظفين.' : 'This account is for browsing, saving activity, and managing personal preferences, not for agency staff or dashboard access.' }}</p>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-extrabold text-[#31423b]">{{ app()->getLocale() === 'ar' ? 'الاسم الكامل' : 'Full name' }}</label>
                            <input x-model="residentForm.name" type="text" class="block w-full rounded-[1.1rem] border border-[#d9ccb2] bg-white px-4 py-3 text-sm text-slate-900 shadow-sm transition focus:border-[#b6842f] focus:ring-[#b6842f]" required>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="mb-2 block text-sm font-extrabold text-[#31423b]">{{ app()->getLocale() === 'ar' ? 'الرمز' : 'Code' }}</label>
                                <select x-model="residentForm.country_code" class="block w-full rounded-[1.1rem] border border-[#d9ccb2] bg-white px-3 py-3 text-sm text-slate-900 shadow-sm transition focus:border-[#b6842f] focus:ring-[#b6842f]" required>
                                    @foreach($countryCodes as $code => $label)
                                        <option value="{{ $code }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="mb-2 block text-sm font-extrabold text-[#31423b]">{{ app()->getLocale() === 'ar' ? 'رقم الهاتف' : 'Phone number' }}</label>
                                <input x-model="residentForm.phone" type="tel" class="block w-full rounded-[1.1rem] border border-[#d9ccb2] bg-white px-4 py-3 text-sm text-slate-900 shadow-sm transition focus:border-[#b6842f] focus:ring-[#b6842f]" required>
                            </div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-extrabold text-[#31423b]">{{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email' }} <span class="font-medium text-slate-400">({{ app()->getLocale() === 'ar' ? 'اختياري' : 'optional' }})</span></label>
                            <input x-model="residentForm.email" type="email" class="block w-full rounded-[1.1rem] border border-[#d9ccb2] bg-white px-4 py-3 text-sm text-slate-900 shadow-sm transition focus:border-[#b6842f] focus:ring-[#b6842f]">
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-extrabold text-[#31423b]">{{ app()->getLocale() === 'ar' ? 'كلمة المرور' : 'Password' }}</label>
                            <input x-model="residentForm.password" type="password" class="block w-full rounded-[1.1rem] border border-[#d9ccb2] bg-white px-4 py-3 text-sm text-slate-900 shadow-sm transition focus:border-[#b6842f] focus:ring-[#b6842f]" required>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-extrabold text-[#31423b]">{{ app()->getLocale() === 'ar' ? 'تأكيد كلمة المرور' : 'Confirm password' }}</label>
                            <input x-model="residentForm.password_confirmation" type="password" class="block w-full rounded-[1.1rem] border border-[#d9ccb2] bg-white px-4 py-3 text-sm text-slate-900 shadow-sm transition focus:border-[#b6842f] focus:ring-[#b6842f]" required>
                        </div>

                        <div class="flex gap-3 pt-2">
                            <button type="button" class="w-full rounded-[1.1rem] border border-[#d6c7ad] bg-white/80 px-4 py-3 text-sm font-semibold uppercase tracking-[0.12em] text-slate-700 transition hover:-translate-y-0.5 hover:border-[#b6842f] hover:bg-white" @click="closeResidentRegister()">
                                {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                            </button>
                            <button type="submit" :disabled="residentRegisterLoading" class="w-full rounded-[1.1rem] bg-gradient-to-r from-[#0f5a46] to-[#b6842f] px-4 py-3 text-sm font-semibold uppercase tracking-[0.12em] text-[#fff8ea] shadow-[0_18px_34px_-18px_rgba(15,90,70,.8)] transition hover:-translate-y-0.5 hover:shadow-[0_22px_36px_-20px_rgba(15,90,70,.82)] disabled:opacity-60">
                                <span x-show="!residentRegisterLoading">{{ app()->getLocale() === 'ar' ? 'إنشاء الحساب' : 'Create account' }}</span>
                                <span x-show="residentRegisterLoading" x-cloak>{{ app()->getLocale() === 'ar' ? 'جاري الإنشاء...' : 'Creating...' }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
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
