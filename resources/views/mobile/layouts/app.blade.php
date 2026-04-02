<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Aqari Smart' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>window.__AQARI_API_BASE = '{{ config("nativephp.remote_api_url", "") }}';</script>
</head>
@php
    $currentLocale = in_array(app()->getLocale(), ['en', 'ar']) ? app()->getLocale() : 'en';
    $langToggleEn = url(request()->path()) . '?lang=en';
    $langToggleAr = url(request()->path()) . '?lang=ar';
@endphp
<body class="bg-gray-50 min-h-screen text-slate-800">
    <div x-data="{ open: false, authed: !!localStorage.getItem('aqari_mobile_token'), userName: localStorage.getItem('aqari_mobile_user_name') || '' }" class="min-h-screen">
        {{-- ═══ Side Menu — Premium Light Theme ═══ --}}
        <aside class="overflow-auto fixed inset-y-0 left-0 rtl:left-auto rtl:right-0 z-40 w-[300px] transform bg-white shadow-2xl transition-all duration-300" :class="open ? 'translate-x-0 rtl:-translate-x-0' : '-translate-x-full rtl:translate-x-full'">
            {{-- Menu Header --}}
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

            {{-- Menu Navigation --}}
            <nav class="flex flex-col gap-1 px-4 py-5">
                <a href="{{ route('mobile.marketplace') }}" class="group flex items-center gap-3.5 rounded-xl px-4 py-3 transition-all duration-150 {{ request()->routeIs('mobile.marketplace') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-700 hover:bg-slate-50' }}">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ request()->routeIs('mobile.marketplace') ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-emerald-50 group-hover:text-emerald-600' }} transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <div class="text-sm font-semibold">{{ app()->getLocale() === 'ar' ? 'السوق' : 'Marketplace' }}</div>
                        <div class="text-[11px] font-medium {{ request()->routeIs('mobile.marketplace') ? 'text-emerald-500' : 'text-slate-400' }}">{{ app()->getLocale() === 'ar' ? 'تصفح العقارات' : 'Browse properties' }}</div>
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
                <a x-show="authed" x-cloak href="{{ route('mobile.dashboard') }}" class="group flex items-center gap-3.5 rounded-xl px-4 py-3 transition-all duration-150 {{ request()->routeIs('mobile.dashboard') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-700 hover:bg-slate-50' }}">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ request()->routeIs('mobile.dashboard') ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-emerald-50 group-hover:text-emerald-600' }} transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <div>
                        <div class="text-sm font-semibold">{{ app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Dashboard' }}</div>
                        <div class="text-[11px] font-medium {{ request()->routeIs('mobile.dashboard') ? 'text-emerald-500' : 'text-slate-400' }}">{{ app()->getLocale() === 'ar' ? 'نظرة عامة' : 'Overview' }}</div>
                    </div>
                </a>
                <a x-show="authed" x-cloak href="{{ route('mobile.units.index') }}" class="group flex items-center gap-3.5 rounded-xl px-4 py-3 transition-all duration-150 {{ request()->routeIs('mobile.units.*') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-700 hover:bg-slate-50' }}">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ request()->routeIs('mobile.units.*') ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-emerald-50 group-hover:text-emerald-600' }} transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                    </div>
                    <div>
                        <div class="text-sm font-semibold">{{ app()->getLocale() === 'ar' ? 'الوحدات' : 'My Units' }}</div>
                        <div class="text-[11px] font-medium {{ request()->routeIs('mobile.units.*') ? 'text-emerald-500' : 'text-slate-400' }}">{{ app()->getLocale() === 'ar' ? 'إدارة العقارات' : 'Manage listings' }}</div>
                    </div>
                </a>

                {{-- Divider --}}
                <div class="my-2 border-t border-slate-100"></div>

                {{-- Auth Section --}}
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

                <button x-show="authed" x-cloak type="button" class="group flex w-full items-center gap-3.5 rounded-xl px-4 py-3 text-left text-slate-700 transition-all duration-150 hover:bg-red-50" @click="localStorage.removeItem('aqari_mobile_token'); localStorage.removeItem('aqari_mobile_tenant_slug'); localStorage.removeItem('aqari_mobile_user_name'); authed = false; window.location.href='{{ route('mobile.marketplace') }}';">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-500 transition-colors group-hover:bg-red-100">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-red-600">{{ app()->getLocale() === 'ar' ? 'تسجيل الخروج' : 'Sign out' }}</div>
                        <div class="text-[11px] font-medium text-slate-400">{{ app()->getLocale() === 'ar' ? 'خروج من حسابك' : 'Log out of your account' }}</div>
                    </div>
                </button>
            </nav>

            {{-- Menu Footer --}}
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

        {{-- ═══ Main Content Area ═══ --}}
        <div class="relative min-h-screen">
            {{-- Safe-area spacer for mobile status bar --}}
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
    </div>
    @stack('scripts')
</body>
</html>
