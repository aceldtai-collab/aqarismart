<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Aqari Smart' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
@php
    $currentLocale = in_array(app()->getLocale(), ['en', 'ar']) ? app()->getLocale() : 'en';
    $langToggleEn = url(request()->path()) . '?lang=en';
    $langToggleAr = url(request()->path()) . '?lang=ar';
@endphp
<body class="bg-gray-50 min-h-screen text-emerald-300">
    <div x-data="{ open: false, authed: !!localStorage.getItem('aqari_mobile_token'), userName: localStorage.getItem('aqari_mobile_user_name') || '' }" class="min-h-screen">
        <aside class="overflow-auto fixed inset-y-0 left-0 z-40 w-80 transform bg-gradient-to-br from-emerald-600 via-emerald-700 to-emerald-800 text-white transition-all duration-300 shadow-2xl" :class="open ? 'translate-x-0' : '-translate-x-full'">
            <div class="flex items-center justify-between border-b border-emerald-300/20 px-6 py-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500 flex items-center justify-center">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <div>
                        <div class="text-xl font-bold text-white">Aqari Smart</div>
                        <div class="text-sm text-emerald-200 font-medium" x-text="authed ? userName : '{{ app()->getLocale() === 'ar' ? 'مرحباً' : 'Welcome' }}'"></div>
                    </div>
                </div>
                <button type="button" class="rounded-xl p-2.5 hover:bg-emerald-500 transition-colors" @click="open = false">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <nav class="px-6 py-4 space-y-2 bg-gray-50">
                <div class="space-y-1">
                    <a href="{{ route('mobile.marketplace') }}" class="group flex items-center gap-4 rounded-2xl px-5 py-4 text-emerald-300 hover:bg-emerald-500 transition-all duration-200">
                        <div class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center group-hover:bg-emerald-700 transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div>
                            <div class="font-semibold">{{ app()->getLocale() === 'ar' ? 'السوق' : 'Marketplace' }}</div>
                            <div class="text-xs text-emerald-300/70">{{ app()->getLocale() === 'ar' ? 'العقارات' : 'Properties' }}</div>
                        </div>
                    </a>
                    <a href="{{ route('mobile.tenants.index') }}" class="group flex items-center gap-4 rounded-2xl px-5 py-4 text-emerald-300 hover:bg-emerald-500 transition-all duration-200">
                        <div class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center group-hover:bg-emerald-700 transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-3M7 20v-2a3 3 0 00-5.356-3M7 10a2 2 0 012 2m2-3a2 2 0 012 2m2-3a2 2 0 012 2m2-3a2 2 0 012 2"/></svg>
                        </div>
                        <div>
                            <div class="font-semibold">{{ app()->getLocale() === 'ar' ? 'المنشآت' : 'Tenants' }}</div>
                            <div class="text-xs text-emerald-300/70">{{ app()->getLocale() === 'ar' ? 'البحث عن المنشآت' : 'Search tenants' }}</div>
                        </div>
                    </a>
                    <a x-show="authed" x-cloak href="{{ route('mobile.dashboard') }}" class="group flex items-center gap-4 rounded-2xl px-5 py-4 text-emerald-300 hover:bg-emerald-500 transition-all duration-200">
                        <div class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center group-hover:bg-emerald-700 transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <div>
                            <div class="font-semibold">{{ app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Dashboard' }}</div>
                            <div class="text-xs text-emerald-300/70">{{ app()->getLocale() === 'ar' ? 'نظرة عامة' : 'Overview' }}</div>
                        </div>
                    </a>
                    <a x-show="authed" x-cloak href="{{ route('mobile.units.index') }}" class="group flex items-center gap-4 rounded-2xl px-5 py-4 text-emerald-300 hover:bg-emerald-500 transition-all duration-200">
                        <div class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center group-hover:bg-emerald-700 transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div>
                            <div class="font-semibold">{{ app()->getLocale() === 'ar' ? 'الوحدات' : 'Units' }}</div>
                            <div class="text-xs text-emerald-300/70">{{ app()->getLocale() === 'ar' ? 'إدارة العقارات' : 'Manage properties' }}</div>
                        </div>
                    </a>
                </div>
                
                <div class="border-t border-emerald-300/20 pt-4 mt-4">
                    <div x-show="!authed" class="space-y-1">
                        <a href="{{ route('mobile.login') }}" class="group flex items-center gap-4 rounded-2xl px-5 py-3 text-emerald-300 hover:bg-emerald-500 transition-all duration-200">
                            <div class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center group-hover:bg-emerald-700 transition-colors">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                            </div>
                            <div>
                                <div class="font-semibold">{{ app()->getLocale() === 'ar' ? 'تسجيل الدخول' : 'Login' }}</div>
                                <div class="text-xs text-emerald-300/70">{{ app()->getLocale() === 'ar' ? 'دخول إلى الحساب' : 'Sign in' }}</div>
                            </div>
                        </a>
                        <a href="{{ route('mobile.register') }}" class="group flex items-center gap-4 rounded-2xl px-5 py-3 text-emerald-300 hover:bg-emerald-500 transition-all duration-200">
                            <div class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center group-hover:bg-emerald-700 transition-colors">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                            </div>
                            <div>
                                <div class="font-semibold">{{ app()->getLocale() === 'ar' ? 'إنشاء حساب' : 'Register' }}</div>
                                <div class="text-xs text-emerald-300/70">{{ app()->getLocale() === 'ar' ? 'حساب جديد' : 'New account' }}</div>
                            </div>
                        </a>
                    </div>
                    
                    <button x-show="authed" x-cloak type="button" class="w-full flex items-center gap-4 rounded-2xl px-5 py-3 text-left text-emerald-300 hover:bg-red-600 transition-all duration-200" @click="localStorage.removeItem('aqari_mobile_token'); localStorage.removeItem('aqari_mobile_tenant_slug'); localStorage.removeItem('aqari_mobile_user_name'); authed = false; window.location.href='{{ route('mobile.marketplace') }}';">
                        <div class="w-10 h-10 rounded-xl bg-red-600 flex items-center justify-center">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </div>
                        <div>
                            <div class="font-semibold">{{ app()->getLocale() === 'ar' ? 'تسجيل الخروج' : 'Logout' }}</div>
                            <div class="text-xs text-emerald-300/70">{{ app()->getLocale() === 'ar' ? 'خروج من النظام' : 'Sign out' }}</div>
                        </div>
                    </button>
                </div>
            </nav>
        </aside>
        <div class="fixed inset-0 z-30 bg-slate-950/40" x-show="open" x-transition.opacity @click="open = false"></div>
        <div class="relative min-h-screen">
            <header class="sticky top-0 z-20 border-b border-emerald-300/20 bg-emerald-600/90 backdrop-blur-md">
                <div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-4 sm:px-6">
                    <div class="flex items-center gap-3">
                        @if(!isset($show_back_button) || $show_back_button !== false)
                        @if(request()->header('Referer') || url()->previous() !== url()->current())
                        <button type="button" onclick="history.back()" class="rounded-xl border border-emerald-300/20 bg-white/10 backdrop-blur-sm p-2 shadow-lg hover:bg-white/20">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                        </button>
                        @endif
                        @endif
                        <button type="button" class="rounded-xl border border-emerald-300/20 bg-white/10 backdrop-blur-sm p-2 shadow-lg hover:bg-white/20" @click="open = true">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <div class="flex items-center gap-2">
                            <img src="{{ asset('images/logotest.png') }}" alt="Aqari Smart" class="h-5 w-5 object-contain filter brightness-0 invert">
                            <div>
                                <div class="text-base font-semibold text-white">{{ $title ?? 'Aqari Smart' }}</div>
                                @isset($subtitle)
                                @if(!empty($subtitle))
                                    <div class="text-xs text-white/70">{{ $subtitle }}</div>
                                @endif
                                @endisset
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="flex items-center gap-0.5 rounded-full border border-emerald-300/20 bg-white/10 backdrop-blur-sm p-1 text-[11px] font-semibold text-emerald-300 shadow-lg">
                            <a href="{{ $langToggleEn }}" class="rounded-full px-2.5 py-1 transition {{ $currentLocale === 'en' ? 'bg-white text-emerald-700' : 'hover:bg-white/20' }}">EN</a>
                            <a href="{{ $langToggleAr }}" class="rounded-full px-2.5 py-1 transition {{ $currentLocale === 'ar' ? 'bg-white text-emerald-700' : 'hover:bg-white/20' }}">ع</a>
                        </div>
                    </div>
                </div>
            </header>
            <main class="mx-auto max-w-5xl px-4 py-6 sm:px-6">
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
