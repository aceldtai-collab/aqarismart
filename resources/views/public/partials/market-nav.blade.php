@php
    $isAr = $isAr ?? app()->getLocale() === 'ar';
    $navTx = array_merge([
        'brand' => $isAr ? 'عقاري سمارت' : 'Aqari Smart',
        'login_cta' => $isAr ? 'تسجيل الدخول' : 'Sign in',
        'register_cta' => $isAr ? 'إنشاء حساب' : 'Create account',
        'sell_cta' => $isAr ? 'بيع معنا' : 'Sell with us',
        'profile_cta' => $isAr ? 'الملف الشخصي' : 'Profile',
        'menu_cta' => $isAr ? 'القائمة' : 'Menu',
        'close_cta' => $isAr ? 'إغلاق' : 'Close',
        'account_title' => $isAr ? 'حسابك' : 'Your Account',
        'browse_title' => $isAr ? 'تصفح السوق' : 'Browse Marketplace',
        'dashboard_cta' => $isAr ? 'لوحة التحكم' : 'Dashboard',
        'logout_cta' => $isAr ? 'تسجيل الخروج' : 'Log Out',
        'welcome_cta' => $isAr ? 'أهلاً' : 'Welcome',
        'guest_subtitle' => $isAr
            ? 'اكتشف العقارات وتابع رحلتك بسهولة من أي صفحة.'
            : 'Discover listings and continue your journey from any page.',
        'switch_language' => $isAr ? 'تغيير اللغة' : 'Switch language',
    ], $navTx ?? []);

    $langParam = config('locales.cookie_name', 'lang');
    $urlEn = $urlEn ?? request()->fullUrlWithQuery([$langParam => 'en']);
    $urlAr = $urlAr ?? request()->fullUrlWithQuery([$langParam => 'ar']);
    $navLinks = array_values(array_filter($navLinks ?? [], fn ($link) => filled($link['href'] ?? null) && filled($link['label'] ?? null)));
    $navBrandHref = $navBrandHref ?? ($navLinks[0]['href'] ?? url('/'));
    $navBrandLabel = $navBrandLabel ?? ($navTx['brand'] ?? config('app.name'));
    $tenantCtx = $tenantCtx ?? app(\App\Services\Tenancy\TenantManager::class)->tenant();

    $authUser = Auth::user();
    $superAdminEmails = collect(config('auth.super_admin_emails', []))
        ->map(fn ($email) => strtolower((string) $email))
        ->filter();
    $isSuperAdminUser = $authUser
        ? $superAdminEmails->contains(strtolower((string) $authUser->email))
        : false;
    $isResidentUser = $authUser
        ? ((method_exists($authUser, 'hasRole') && $authUser->hasRole('resident')))
        : false;
    $isAdminUser = $authUser
        ? ((method_exists($authUser, 'hasRole') && $authUser->hasRole('admin*')) || $isSuperAdminUser)
        : false;
    $profileUrl = $authUser
        ? (($isResidentUser && $tenantCtx && Route::has('resident.profile'))
            ? route('resident.profile')
            : (Route::has('profile.edit') ? route('profile.edit') : '#'))
        : null;
    $dashboardUrl = null;
    if ($authUser && $tenantCtx && ! $isResidentUser && Route::has('dashboard')) {
        $dashboardUrl = route('dashboard');
    } elseif ($isAdminUser && Route::has('admin.index')) {
        $dashboardUrl = route('admin.index');
    }

    $sellWithUsUrl = $sellWithUsUrl ?? (
        $tenantCtx && Route::has('tenant.sales-flow')
            ? route('tenant.sales-flow')
            : (Route::has('sales-flow')
                ? route('sales-flow')
                : (Route::has('book-call') ? route('book-call') : '#'))
    );
    $scheme = request()->getScheme() ?: 'http';
    $port = request()->getPort();
    $defaultPort = $scheme === 'https' ? 443 : 80;
    $portPart = $port && $port !== $defaultPort ? ':' . $port : '';
    $centralMarketplaceUrl = Route::has('public.marketplace')
        ? route('public.marketplace')
        : sprintf('%s://%s%s/marketplace', $scheme, config('tenancy.base_domain'), $portPart);
    $loginUrl = $centralMarketplaceUrl . '?auth=login';
    $registerUrl = $centralMarketplaceUrl . '?auth=register';
    $loginOpensModal = ! $tenantCtx && request()->routeIs('public.marketplace');
    $registerOpensModal = ! $tenantCtx && request()->routeIs('public.marketplace');
    $userFirstName = $authUser ? (explode(' ', trim((string) $authUser->name))[0] ?: $authUser->name) : null;
    $userAvatar = $authUser
        ? 'https://ui-avatars.com/api/?name=' . urlencode((string) $authUser->name) . '&background=b6842f&color=fff'
        : null;
@endphp
<script>
    document.addEventListener('alpine:init', () => {
        if (typeof Alpine !== 'undefined' && Alpine.store('auth') === undefined) {
            Alpine.store('auth', { login: false, register: false });
        }
    });
</script>

<div x-data="{ mobileNavOpen: false }">
    <header class="absolute inset-x-0 top-0 z-40">
        <div class="market-shell mx-auto px-4 pt-4 text-white sm:px-6 lg:px-8">
            <div class="market-nav-shell flex items-center justify-between rounded-[28px] px-4 py-3 sm:px-5 lg:px-6">
                <div class="flex items-center gap-3">
                    <button type="button" @click="mobileNavOpen = true" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/14 bg-white/8 text-white md:hidden">
                        <span class="sr-only">{{ $navTx['menu_cta'] }}</span>
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16"/></svg>
                    </button>
                    <a href="{{ $navBrandHref }}" class="flex items-center gap-3 font-semibold tracking-wide">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-white/14 text-sm font-bold text-white shadow-lg shadow-black/15 backdrop-blur">AS</span>
                        <span>{{ $navBrandLabel }}</span>
                    </a>
                </div>
                <nav class="hidden items-center gap-1 text-sm text-white/85 md:flex">
                    @foreach ($navLinks as $link)
                        <a href="{{ $link['href'] }}" class="market-nav-link">{{ $link['label'] }}</a>
                    @endforeach
                </nav>
                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="hidden items-center rounded-full border border-white/20 bg-white/10 p-1 text-xs font-medium backdrop-blur sm:flex">
                        <a href="{{ $urlEn }}" class="rounded-full px-3 py-1 {{ ! $isAr ? 'bg-white text-slate-900' : 'text-white/80' }}">EN</a>
                        <a href="{{ $urlAr }}" class="rounded-full px-3 py-1 {{ $isAr ? 'bg-white text-slate-900' : 'text-white/80' }}">ع</a>
                    </div>
                    @auth
                        <div x-data="{ open: false }" @click.outside="open = false" class="relative hidden md:block">
                            <button type="button" @click="open = !open" class="inline-flex items-center gap-3 rounded-full border border-white/14 bg-white/8 py-1.5 pl-1.5 pr-3 {{ $isAr ? 'text-right' : 'text-left' }} text-white backdrop-blur">
                                <img src="{{ $userAvatar }}" alt="{{ $authUser->name }}" class="market-nav-avatar h-10 w-10 rounded-full ring-2 ring-white/20">
                                <div class="hidden min-w-0 lg:block">
                                    <div class="truncate text-sm font-bold">{{ $userFirstName }}</div>
                                    <div class="truncate text-xs text-white/62">{{ $navTx['account_title'] }}</div>
                                </div>
                            </button>
                            <div x-show="open" x-cloak x-transition class="market-user-menu absolute {{ $isAr ? 'left-0' : 'right-0' }} mt-3 w-60 rounded-[24px] p-3 text-slate-900">
                                <div class="rounded-[18px] bg-[rgba(250,244,229,.9)] px-4 py-3">
                                    <div class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">{{ $navTx['welcome_cta'] }}</div>
                                    <div class="mt-1 truncate text-base font-extrabold">{{ $authUser->name }}</div>
                                    <div class="truncate text-sm text-slate-500">{{ $authUser->email }}</div>
                                </div>
                                <div class="mt-3 space-y-1">
                                    @if($dashboardUrl)
                                        <a href="{{ $dashboardUrl }}" class="block rounded-2xl px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-[rgba(15,90,70,.06)]">{{ $navTx['dashboard_cta'] }}</a>
                                    @endif
                                    @if($profileUrl)
                                        <a href="{{ $profileUrl }}" class="block rounded-2xl px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-[rgba(15,90,70,.06)]">{{ $navTx['profile_cta'] }}</a>
                                    @endif
                                    @if(Route::has('my-listings.index'))
                                        <a href="{{ route('my-listings.index') }}" class="block rounded-2xl px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-[rgba(15,90,70,.06)]">{{ $isAr ? 'إعلاناتي' : 'My Listings' }}</a>
                                    @endif
                                    <a href="{{ $sellWithUsUrl }}" class="block rounded-2xl px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-[rgba(15,90,70,.06)]">{{ $navTx['sell_cta'] }}</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full rounded-2xl px-4 py-3 {{ $isAr ? 'text-right' : 'text-left' }} text-sm font-semibold text-[color:var(--market-clay,#9d5a3b)] transition hover:bg-[rgba(157,90,59,.08)]">{{ $navTx['logout_cta'] }}</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        @if($loginUrl)
                            <a href="{{ $loginUrl }}" @if($loginOpensModal) @click.prevent="$store.auth.login = true" @endif class="market-nav-action market-nav-ghost hidden md:inline-flex">{{ $navTx['login_cta'] }}</a>
                        @endif
                        @if($registerUrl)
                            <a href="{{ $registerUrl }}" @if($registerOpensModal) @click.prevent="$store.auth.register = true" @endif class="market-nav-action market-nav-solid hidden md:inline-flex">{{ $navTx['register_cta'] }}</a>
                        @endif
                        <a href="{{ $sellWithUsUrl }}" class="market-nav-action market-nav-ghost hidden lg:inline-flex">{{ $navTx['sell_cta'] }}</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <div x-show="mobileNavOpen" x-cloak @click="mobileNavOpen = false" class="fixed inset-0 z-40 bg-black/55 backdrop-blur-sm md:hidden"></div>
    <aside x-show="mobileNavOpen" x-cloak class="market-mobile-sheet fixed top-0 {{ $isAr ? 'right-0' : 'left-0' }} z-50 h-full w-[88vw] max-w-sm overflow-y-auto md:hidden" x-transition:enter="transition ease-out duration-250" x-transition:enter-start="{{ $isAr ? 'translate-x-full' : '-translate-x-full' }}" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-180" x-transition:leave-start="translate-x-0" x-transition:leave-end="{{ $isAr ? 'translate-x-full' : '-translate-x-full' }}">
        <div class="px-5 pb-6 pt-5">
            <div class="flex items-center justify-between text-white">
                <div class="flex items-center gap-3">
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/12 bg-white/12 text-sm font-bold">AS</span>
                    <div>
                        <div class="text-sm font-bold uppercase tracking-[0.18em] text-white/60">{{ $navTx['menu_cta'] }}</div>
                        <div class="text-base font-extrabold">{{ $navBrandLabel }}</div>
                    </div>
                </div>
                <button type="button" @click="mobileNavOpen = false" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/12 bg-white/10 text-white">
                    <span class="sr-only">{{ $navTx['close_cta'] }}</span>
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="mt-6 rounded-[24px] bg-white/10 p-4 text-white backdrop-blur">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <div class="text-xs font-bold uppercase tracking-[0.18em] text-white/60">{{ $navTx['account_title'] }}</div>
                        @auth
                            <div class="mt-1 text-lg font-extrabold">{{ $authUser->name }}</div>
                            <div class="text-sm text-white/70">{{ $authUser->email }}</div>
                        @else
                            <div class="mt-1 text-lg font-extrabold">{{ $navTx['welcome_cta'] }}</div>
                            <div class="text-sm text-white/70">{{ $navTx['guest_subtitle'] }}</div>
                        @endauth
                    </div>
                    @auth
                        <img src="{{ $userAvatar }}" alt="{{ $authUser->name }}" class="h-14 w-14 rounded-full ring-2 ring-white/16">
                    @endauth
                </div>
            </div>

            <div class="mt-5 grid gap-3">
                @auth
                    @if($dashboardUrl)
                        <a href="{{ $dashboardUrl }}" @click="mobileNavOpen = false" class="market-mobile-link market-mobile-link-dark">{{ $navTx['dashboard_cta'] }}</a>
                    @endif
                    @if($profileUrl)
                        <a href="{{ $profileUrl }}" @click="mobileNavOpen = false" class="market-mobile-link market-mobile-link-dark">{{ $navTx['profile_cta'] }}</a>
                    @endif
                    @if(Route::has('my-listings.index'))
                        <a href="{{ route('my-listings.index') }}" @click="mobileNavOpen = false" class="market-mobile-link market-mobile-link-dark">{{ $isAr ? 'إعلاناتي' : 'My Listings' }}</a>
                    @endif
                    <a href="{{ $sellWithUsUrl }}" @click="mobileNavOpen = false" class="market-mobile-link market-mobile-link-dark">{{ $navTx['sell_cta'] }}</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="market-mobile-link market-mobile-link-dark w-full {{ $isAr ? 'text-right' : 'text-left' }}">{{ $navTx['logout_cta'] }}</button>
                    </form>
                @else
                    @if($loginUrl)
                        <a href="{{ $loginUrl }}" @if($loginOpensModal) @click.prevent="mobileNavOpen = false; $store.auth.login = true" @else @click="mobileNavOpen = false" @endif class="market-mobile-link market-mobile-link-dark">{{ $navTx['login_cta'] }}</a>
                    @endif
                    @if($registerUrl)
                        <a href="{{ $registerUrl }}" @if($registerOpensModal) @click.prevent="mobileNavOpen = false; $store.auth.register = true" @else @click="mobileNavOpen = false" @endif class="market-mobile-link">{{ $navTx['register_cta'] }}</a>
                    @endif
                    <a href="{{ $sellWithUsUrl }}" @click="mobileNavOpen = false" class="market-mobile-link">{{ $navTx['sell_cta'] }}</a>
                @endauth
            </div>

            @if ($navLinks !== [])
                <div class="mt-6 rounded-[26px] bg-white p-5">
                    <div class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">{{ $navTx['browse_title'] }}</div>
                    <div class="mt-4 grid gap-3">
                        @foreach ($navLinks as $link)
                            <a href="{{ $link['href'] }}" @click="mobileNavOpen = false" class="market-mobile-link">{{ $link['label'] }}</a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mt-6 rounded-[26px] bg-white p-5">
                <div class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">{{ $navTx['switch_language'] }}</div>
                <div class="mt-4 grid grid-cols-2 gap-3">
                    <a href="{{ $urlEn }}" class="market-mobile-link text-center {{ ! $isAr ? 'ring-2 ring-[color:var(--market-brass,#b6842f)]' : '' }}">EN</a>
                    <a href="{{ $urlAr }}" class="market-mobile-link text-center {{ $isAr ? 'ring-2 ring-[color:var(--market-brass,#b6842f)]' : '' }}">{{ $isAr ? 'العربية' : 'Arabic' }}</a>
                </div>
            </div>
        </div>
    </aside>
</div>
