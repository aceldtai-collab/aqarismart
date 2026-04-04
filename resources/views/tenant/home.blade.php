@extends('layouts.app')

@section('title', $tenant->name . ' - ' . __('Home'))

@section('content')
@php
    $theme = is_array($tenant->settings ?? null) ? $tenant->settings : [];
    $locale = app()->getLocale();
    $isAr = $locale === 'ar';
    $langParam = config('locales.cookie_name', 'lang');
    $urlEn = request()->fullUrlWithQuery([$langParam => 'en']);
    $urlAr = request()->fullUrlWithQuery([$langParam => 'ar']);

    $scheme = request()->getScheme() ?: 'http';
    $port = request()->getPort();
    $defaultPort = $scheme === 'https' ? 443 : 80;
    $portPart = $port && $port !== $defaultPort ? ':' . $port : '';
    $centralMarketplaceUrl = sprintf('%s://%s%s/marketplace', $scheme, config('tenancy.base_domain'), $portPart);

    $normalizeAsset = function (?string $value): ?string {
        if (! is_string($value) || trim($value) === '') return null;
        $value = trim($value);
        if (\Illuminate\Support\Str::startsWith($value, ['http://', 'https://'])) return $value;
        return url('/') . '/' . (\Illuminate\Support\Str::startsWith($value, 'storage/') ? $value : 'storage/' . ltrim($value, '/'));
    };

    $logoUrl = $normalizeAsset($theme['logo_url'] ?? null);
    $heroImage = $normalizeAsset($theme['header_bg_url'] ?? null);
    $tenantPrimary = $theme['primary_color'] ?? '#0f5a46';
    $tenantAccent = $theme['accent_color'] ?? '#b6842f';
    $tenantTagline = trim((string) data_get($theme, 'tagline', ''));
    $tenantTagline = $tenantTagline !== '' ? $tenantTagline : ($isAr ? 'واجهة عامة تعرض عقارات الوكالة بأسلوب أوضح وأدفأ.' : 'A warmer public storefront for the tenant inventory.');
    $tenantStory = trim((string) data_get($theme, 'about', ''));
    $tenantStory = $tenantStory !== '' ? $tenantStory : ($isAr ? 'اكتشف أحدث العقارات، اقرأ تفاصيلها، وانتقل بين المواقع من واجهة واحدة متماسكة.' : 'Explore the latest listings, read their details, and move through locations from one cohesive public experience.');

    $tenantWebsite = route('tenant.home', ['tenant_slug' => $tenant->slug]);
    $tenantSearchUrl = route('tenant.search', ['tenant_slug' => $tenant->slug]);
    $sellWithUsUrl = Route::has('tenant.sales-flow') ? route('tenant.sales-flow', ['tenant_slug' => $tenant->slug]) : '#';

    $navTx = [
        'brand' => $isAr ? 'عقاري سمارت' : 'Aqari Smart',
        'home' => $isAr ? 'الرئيسية' : 'Home',
        'featured_nav' => $isAr ? 'العقارات' : 'Listings',
        'sale_nav' => $isAr ? 'الأنواع' : 'Types',
        'rent_nav' => $isAr ? 'الخريطة' : 'Map',
        'contact_nav' => $isAr ? 'السوق' : 'Marketplace',
        'login_cta' => $isAr ? 'تسجيل الدخول' : 'Sign in',
        'register_cta' => $isAr ? 'إنشاء حساب' : 'Create account',
        'sell_cta' => $isAr ? 'بيع معنا' : 'Sell with us',
        'profile_cta' => $isAr ? 'الملف الشخصي' : 'Profile',
        'menu_cta' => $isAr ? 'القائمة' : 'Menu',
        'close_cta' => $isAr ? 'إغلاق' : 'Close',
        'account_title' => $isAr ? 'حسابك' : 'Your Account',
        'browse_title' => $isAr ? 'تنقل داخل الواجهة' : 'Browse Storefront',
        'dashboard_cta' => $isAr ? 'لوحة التحكم' : 'Dashboard',
        'logout_cta' => $isAr ? 'تسجيل الخروج' : 'Log Out',
        'welcome_cta' => $isAr ? 'أهلاً' : 'Welcome',
        'guest_subtitle' => $isAr ? 'استكشف عقارات الوكالة أو ارجع إلى السوق المركزي.' : 'Explore this tenant inventory or head back to the central marketplace.',
        'switch_language' => $isAr ? 'تغيير اللغة' : 'Switch language',
    ];
    $navLinks = [
        ['label' => $navTx['home'], 'href' => $tenantWebsite],
        ['label' => $navTx['featured_nav'], 'href' => '#latest-listings'],
        ['label' => $navTx['sale_nav'], 'href' => '#home-types'],
        ['label' => $navTx['rent_nav'], 'href' => '#home-map'],
        ['label' => $navTx['contact_nav'], 'href' => $centralMarketplaceUrl],
    ];

    $cardUnits = collect($spotlightUnits ?? []);
    if ($cardUnits->isEmpty()) $cardUnits = collect($units->items());
    $stats = $stats ?? ['total' => $units->total(), 'sale' => 0, 'rent' => 0, 'types' => 0];
    $formatMoney = fn ($amount, $currency = 'JOD') => $currency . ' ' . number_format((float) $amount, 0);
    $displayPrice = fn ($unit) => (($unit->listing_type ?? \App\Models\Unit::LISTING_RENT) === \App\Models\Unit::LISTING_SALE)
        ? ($unit->price ?? 0)
        : (($unit->market_rent && $unit->market_rent > 0) ? $unit->market_rent : ($unit->price ?? 0));
    $tenantContactEmail = trim((string) data_get($theme, 'contact_email', ''));
    $tenantContactPhone = trim((string) data_get($theme, 'contact_phone', ''));
    $tenantAddress = trim((string) data_get($theme, 'address', ''));
    $tenantWebsiteLabel = trim((string) data_get($theme, 'website', ''));
    $footerLinks = collect(data_get($theme, 'footer.links', []))->filter(fn ($link) => filled(data_get($link, 'label')) && filled(data_get($link, 'href')))->take(4);
    $socialLinks = collect(data_get($theme, 'footer.social', []))->filter(fn ($entry) => filled(data_get($entry, 'url')))->take(4);

    $cityPins = [
        'amman' => ['lat' => 31.9539494, 'lng' => 35.9106350],
        'zarqa' => ['lat' => 32.0727530, 'lng' => 36.0889350],
        'irbid' => ['lat' => 32.5569636, 'lng' => 35.8478960],
        'madaba' => ['lat' => 31.7165941, 'lng' => 35.7943856],
        'aqaba' => ['lat' => 29.5266730, 'lng' => 35.0077800],
        'salt' => ['lat' => 32.0391666, 'lng' => 35.7272222],
        'ajloun' => ['lat' => 32.3332576, 'lng' => 35.7518020],
        'mafraq' => ['lat' => 32.3416924, 'lng' => 36.2029971],
    ];
    $mapPins = collect($mapUnits ?? [])->map(function ($unit) use ($cityPins, $displayPrice, $formatMoney, $tenant) {
        $lat = $unit->lat !== null ? (float) $unit->lat : null;
        $lng = $unit->lng !== null ? (float) $unit->lng : null;
        $mode = $lat !== null && $lng !== null ? 'exact' : null;
        if (($lat === null || $lng === null) && filled($unit->location)) {
            $haystack = strtolower((string) $unit->location);
            foreach ($cityPins as $needle => $pin) {
                if (str_contains($haystack, $needle)) {
                    $lat = $pin['lat'];
                    $lng = $pin['lng'];
                    $mode = 'approximate';
                    break;
                }
            }
        }
        if ($lat === null || $lng === null) return null;
        return [
            'title' => $unit->translated_title ?: ($unit->property?->name ?? $unit->code),
            'lat' => $lat,
            'lng' => $lng,
            'mode' => $mode,
            'location' => $unit->location ?: __('Location details coming soon'),
            'price' => $formatMoney($displayPrice($unit), $unit->currency ?? 'JOD'),
            'href' => route('tenant.unit', ['tenant_slug' => $tenant->slug, 'unit' => $unit]),
        ];
    })->filter()->values();
    $tenantLat = $tenant->latitude !== null ? (float) $tenant->latitude : null;
    $tenantLng = $tenant->longitude !== null ? (float) $tenant->longitude : null;
    $mapCenter = [
        'lat' => $tenantLat ?? ($mapPins->first()['lat'] ?? 31.9539494),
        'lng' => $tenantLng ?? ($mapPins->first()['lng'] ?? 35.9106350),
        'zoom' => ($tenantLat !== null && $tenantLng !== null) ? 12 : (($mapPins->first()['mode'] ?? null) === 'approximate' ? 11 : 12),
    ];
@endphp

<style>
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Manrope:wght@400;500;600;700;800&display=swap');
    @import url('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
    @include('public.partials.market-nav-styles')
    .tenant-home-page{--market-ink:#1f2a24;--market-palm:#0f5a46;--market-brass:#b6842f;--market-clay:#9d5a3b;--market-line:rgba(130,94,38,.16);--tenant-primary:{{ $tenantPrimary }};--tenant-accent:{{ $tenantAccent }};background:radial-gradient(circle at top left,rgba(182,132,47,.16),transparent 24%),radial-gradient(circle at top right,rgba(15,90,70,.14),transparent 26%),linear-gradient(180deg,#ece2cf 0,#f7f0e4 320px,#fbf7ef 100%);color:var(--market-ink);font-family:'Manrope',system-ui,sans-serif}
    .tenant-home-page[dir="rtl"]{font-family:'Cairo','Noto Sans Arabic',sans-serif}
    .tenant-shell{max-width:1320px;margin:0 auto;padding-inline:1rem}
    .tenant-surface{background:rgba(255,252,246,.94);border:1px solid var(--market-line);box-shadow:0 24px 54px -36px rgba(57,42,16,.36)}
    .tenant-ornament{height:10px;width:112px;border-radius:999px;background:linear-gradient(90deg,rgba(15,90,70,.16),rgba(182,132,47,.32),rgba(15,90,70,.16)),repeating-linear-gradient(90deg,transparent 0 10px,rgba(182,132,47,.58) 10px 14px,transparent 14px 24px)}
    .tenant-note{border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.08);border-radius:1.45rem;padding:1rem;backdrop-filter:blur(14px);box-shadow:0 18px 40px -28px rgba(0,0,0,.35)}
    .tenant-note-label{font-size:.68rem;letter-spacing:.18em;text-transform:uppercase;color:rgba(255,255,255,.62);font-weight:700}
    .tenant-note-value{margin-top:.45rem;font-size:1rem;line-height:1.25;font-weight:800;color:#fff8ec}
    .tenant-home-page .leaflet-container{font:inherit;background:#e7dfcf}
    .tenant-map-tooltip{background:transparent;border:0;box-shadow:none}.tenant-map-tooltip .leaflet-tooltip-content{margin:0}
    .tenant-map-bubble{min-width:220px;border-radius:1rem;border:1px solid rgba(182,132,47,.22);background:rgba(20,31,26,.94);padding:.85rem .95rem;color:#fff8ea;box-shadow:0 18px 40px -20px rgba(0,0,0,.4)}
    .tenant-map-bubble-title{font-size:.95rem;font-weight:800;line-height:1.3}.tenant-map-bubble-meta{margin-top:.35rem;font-size:.78rem;line-height:1.5;color:rgba(255,244,221,.78)}
</style>

<div class="tenant-home-page min-h-screen" dir="{{ $isAr ? 'rtl' : 'ltr' }}">
    @include('public.partials.market-nav', ['isAr' => $isAr, 'tenantCtx' => $tenant, 'navTx' => $navTx, 'navLinks' => $navLinks, 'navBrandHref' => $centralMarketplaceUrl, 'navBrandLabel' => $navTx['brand'], 'urlEn' => $urlEn, 'urlAr' => $urlAr, 'sellWithUsUrl' => $sellWithUsUrl])

    <section class="tenant-shell px-4 pb-8 pt-28 sm:px-6 sm:pt-32 lg:px-8 lg:pt-36">
        <div class="relative isolate overflow-hidden rounded-[2.25rem] px-6 py-8 text-[#fff7ea] sm:px-8 sm:py-10 lg:px-10 lg:py-12" style="background:radial-gradient(circle at top left, rgba(255,255,255,.14), transparent 28%), linear-gradient(140deg, rgba(15,32,26,.96), rgba(15,90,70,.88) 52%, rgba(48,33,15,.84));">
            <div class="absolute inset-0 opacity-80" style="@if($heroImage)background:linear-gradient(120deg, rgba(8,14,11,.3), rgba(8,14,11,.55)), url('{{ $heroImage }}') center/cover no-repeat;@endif"></div>
            <div class="relative z-10 grid gap-8 lg:grid-cols-[1.08fr_.92fr] lg:items-end">
                <div class="{{ $isAr ? 'text-right' : 'text-left' }}">
                    <div class="tenant-ornament"></div>
                    <p class="mt-6 text-xs font-bold uppercase tracking-[0.2em] text-[rgba(255,244,221,.72)]">{{ $isAr ? 'واجهة عامة' : 'Public Storefront' }}</p>
                    <div class="mt-5 flex flex-wrap gap-3 {{ $isAr ? 'justify-end' : '' }}">
                        <span class="inline-flex items-center rounded-full border border-white/12 bg-white/10 px-4 py-2 text-[11px] font-extrabold uppercase tracking-[0.16em] text-white/90">{{ $tenant->name }}</span>
                        <span class="inline-flex items-center rounded-full bg-[rgba(182,132,47,.14)] px-4 py-2 text-[11px] font-extrabold uppercase tracking-[0.16em] text-[#ffe6af]">{{ number_format((int) $stats['total']) }} {{ $isAr ? 'عقار' : 'listings' }}</span>
                    </div>
                    <div class="mt-6 flex flex-wrap items-center gap-4 {{ $isAr ? 'justify-end' : '' }}">
                        @if ($logoUrl)<div class="h-20 w-20 overflow-hidden rounded-[1.65rem] border border-white/12 bg-white/10 shadow-[0_18px_36px_-24px_rgba(0,0,0,.38)]"><img src="{{ $logoUrl }}" alt="{{ $tenant->name }}" class="h-full w-full object-cover"></div>@endif
                        <div>
                            <h1 class="text-4xl font-extrabold leading-[1.02] tracking-[-0.04em] text-[#fff7ea] sm:text-5xl lg:text-[4rem]">{{ $tenant->name }}</h1>
                            <p class="mt-2 max-w-2xl text-base leading-8 text-white/78">{{ $tenantTagline }}</p>
                        </div>
                    </div>
                    <p class="mt-6 max-w-3xl text-base leading-8 text-white/78">{{ $tenantStory }}</p>
                    <div class="mt-8 grid gap-3 sm:grid-cols-3">
                        <div class="tenant-note"><div class="tenant-note-label">{{ $isAr ? 'الإجمالي' : 'Inventory' }}</div><div class="tenant-note-value">{{ number_format((int) $stats['total']) }} {{ $isAr ? 'عقار' : 'units' }}</div></div>
                        <div class="tenant-note"><div class="tenant-note-label">{{ $isAr ? 'للبيع' : 'For Sale' }}</div><div class="tenant-note-value">{{ number_format((int) $stats['sale']) }} {{ $isAr ? 'عروض' : 'listings' }}</div></div>
                        <div class="tenant-note"><div class="tenant-note-label">{{ $isAr ? 'للإيجار' : 'For Rent' }}</div><div class="tenant-note-value">{{ number_format((int) $stats['rent']) }} {{ $isAr ? 'عروض' : 'listings' }}</div></div>
                    </div>
                </div>

                <div class="tenant-surface rounded-[2rem] p-5 text-slate-900 sm:p-6 lg:p-7" style="background:linear-gradient(180deg,rgba(255,249,239,.98),rgba(247,237,214,.9));">
                    <div class="tenant-ornament"></div>
                    <p class="mt-6 text-xs font-bold uppercase tracking-[0.18em] text-[color:var(--market-brass)]">{{ $isAr ? 'ابدأ الرحلة' : 'Start the journey' }}</p>
                    <h2 class="mt-3 text-3xl font-extrabold leading-tight tracking-[-0.03em] text-slate-900">{{ $isAr ? 'ابحث داخل واجهة الوكالة' : 'Search this storefront' }}</h2>
                    <p class="mt-3 text-sm leading-7 text-slate-600">{{ $isAr ? 'انتقل مباشرة إلى نتائج البحث أو ارجع إلى السوق المركزي لمقارنة وكالات أخرى.' : 'Jump straight into filtered results or return to the central marketplace to compare more agencies.' }}</p>
                    <form method="get" action="{{ $tenantSearchUrl }}" class="mt-6 rounded-[1.7rem] border border-[rgba(182,132,47,.24)] bg-[rgba(255,248,235,.97)] p-4 shadow-[0_30px_60px_-34px_rgba(19,24,20,.5)]">
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="sm:col-span-2"><input type="search" name="q" placeholder="{{ $isAr ? 'ابحث باسم العقار أو الموقع' : 'Search by property name or location' }}" class="h-14 w-full rounded-2xl border border-[rgba(130,94,38,.16)] bg-[rgba(255,255,255,.78)] px-4 text-sm font-medium text-slate-900 outline-none transition focus:border-[rgba(182,132,47,.68)]"></div>
                            <div><select name="listing_type" class="h-14 w-full rounded-2xl border border-[rgba(130,94,38,.16)] bg-[rgba(255,255,255,.78)] px-4 text-sm font-medium text-slate-900 outline-none transition focus:border-[rgba(182,132,47,.68)]"><option value="rent">{{ $isAr ? 'للإيجار' : 'For Rent' }}</option><option value="sale">{{ $isAr ? 'للبيع' : 'For Sale' }}</option></select></div>
                            <button type="submit" class="inline-flex h-14 items-center justify-center rounded-2xl px-6 text-sm font-semibold text-white transition hover:opacity-95" style="background:linear-gradient(135deg,var(--tenant-primary),var(--tenant-accent));box-shadow:0 18px 34px -18px rgba(15,90,70,.8)">{{ $isAr ? 'استكشف النتائج' : 'Explore listings' }}</button>
                        </div>
                    </form>
                    <div class="mt-6 flex flex-wrap gap-3 {{ $isAr ? 'justify-end' : '' }}">
                        <a href="{{ $tenantSearchUrl }}" class="inline-flex items-center justify-center rounded-2xl bg-[rgba(15,90,70,.08)] px-4 py-3 text-sm font-bold text-[color:var(--market-palm)] transition hover:-translate-y-0.5">{{ $isAr ? 'عرض كل العقارات' : 'Browse all listings' }}</a>
                        @guest
                            <button type="button" @click="$store.auth.register = true" class="inline-flex items-center justify-center rounded-2xl border border-[rgba(130,94,38,.16)] bg-white px-4 py-3 text-sm font-bold text-slate-900 transition hover:-translate-y-0.5">{{ $isAr ? 'إنشاء حساب' : 'Create account' }}</button>
                        @else
                            <a href="{{ $centralMarketplaceUrl }}" class="inline-flex items-center justify-center rounded-2xl border border-[rgba(130,94,38,.16)] bg-white px-4 py-3 text-sm font-bold text-slate-900 transition hover:-translate-y-0.5">{{ $isAr ? 'العودة إلى السوق' : 'Back to marketplace' }}</a>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </section>

    <main class="tenant-shell space-y-14 px-4 pb-20 sm:px-6 lg:px-8">
        @if ($types->isNotEmpty())
            <section id="home-types" class="tenant-surface rounded-[2rem] px-6 py-8 sm:px-8">
                <div class="tenant-ornament"></div>
                <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-[color:var(--market-brass)]">{{ $isAr ? 'أنواع العقارات' : 'Property Types' }}</p>
                        <h2 class="mt-3 text-3xl font-extrabold tracking-[-0.03em] text-slate-900">{{ $isAr ? 'استكشف حسب النوع' : 'Browse by type' }}</h2>
                    </div>
                    <div class="text-sm text-slate-500">{{ $isAr ? 'فئات حقيقية من مخزون الوكالة الحالي.' : 'Real categories from the current tenant inventory.' }}</div>
                </div>
                <div class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    @foreach ($types as $type)
                        <a href="{{ route('tenant.search', ['tenant_slug' => $tenant->slug, 'subcategory' => $type->id]) }}" class="block rounded-[1.6rem] border border-[rgba(130,94,38,.16)] bg-[linear-gradient(180deg,rgba(255,250,241,.94),rgba(248,239,219,.88))] p-5 shadow-[0_20px_42px_-34px_rgba(55,38,12,.42)]">
                            <div class="text-[11px] font-extrabold uppercase tracking-[0.16em] text-[color:var(--market-brass)]">{{ $isAr ? 'نوع نشط' : 'Active type' }}</div>
                            <h3 class="mt-3 text-2xl font-extrabold tracking-[-0.03em] text-slate-900">{{ $type->name }}</h3>
                            <p class="mt-3 text-sm leading-7 text-slate-600">{{ number_format((int) ($type->units_count ?? 0)) }} {{ $isAr ? 'عقار ضمن هذا النوع.' : 'listings currently under this type.' }}</p>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        @if (collect($popularCities)->isNotEmpty())
            <section class="tenant-surface rounded-[2rem] px-6 py-8 sm:px-8">
                <div class="tenant-ornament"></div>
                <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-[color:var(--market-brass)]">{{ $isAr ? 'مناطق مطلوبة' : 'Popular Areas' }}</p>
                        <h2 class="mt-3 text-3xl font-extrabold tracking-[-0.03em] text-slate-900">{{ $isAr ? 'استكشف المواقع الحاضرة في المخزون' : 'Explore active locations' }}</h2>
                    </div>
                    <div class="text-sm text-slate-500">{{ $isAr ? 'مستخلصة من المواقع الفعلية داخل القوائم.' : 'Pulled from real location labels inside the listings.' }}</div>
                </div>
                <div class="mt-8 grid gap-4 md:grid-cols-3">
                    @foreach ($popularCities as $city)
                        <a href="{{ $city['link'] }}" class="overflow-hidden rounded-[1.9rem] border border-[rgba(130,94,38,.16)] bg-[rgba(255,252,246,.96)] shadow-[0_22px_46px_-34px_rgba(55,38,12,.44)]">
                            <div class="relative h-52 overflow-hidden bg-[rgba(34,38,30,.08)]">
                                @if (!empty($city['image']))
                                    <img src="{{ $city['image'] }}" alt="{{ $city['name'] }}" class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full items-center justify-center bg-gradient-to-br from-[rgba(15,90,70,.16)] to-[rgba(182,132,47,.18)] text-4xl font-black text-[color:var(--market-palm)]">{{ \Illuminate\Support\Str::substr($city['name'], 0, 1) }}</div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-[rgba(15,18,14,.82)] via-transparent to-transparent"></div>
                                <div class="absolute bottom-4 left-4 right-4">
                                    <div class="text-[11px] font-extrabold uppercase tracking-[0.16em] text-[rgba(255,241,212,.86)]">{{ number_format((int) $city['count']) }} {{ $isAr ? 'عقار' : 'listings' }}</div>
                                    <h3 class="mt-2 text-2xl font-extrabold tracking-[-0.03em] text-white">{{ $city['name'] }}</h3>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        <section id="latest-listings" class="tenant-surface rounded-[2rem] px-6 py-8 sm:px-8">
            <div class="tenant-ornament"></div>
            <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-[color:var(--market-brass)]">{{ $isAr ? 'أحدث العروض' : 'Latest Collection' }}</p>
                    <h2 class="mt-3 text-3xl font-extrabold tracking-[-0.03em] text-slate-900">{{ $isAr ? 'أحدث العقارات المنشورة' : 'Latest published listings' }}</h2>
                </div>
                <a href="{{ $tenantSearchUrl }}" class="text-sm font-bold text-[color:var(--market-palm)]">{{ $isAr ? 'عرض كل العقارات' : 'View all listings' }}</a>
            </div>
            <div class="mt-8 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($cardUnits as $unit)
                    @php
                        $isSale = ($unit->listing_type ?? \App\Models\Unit::LISTING_RENT) === \App\Models\Unit::LISTING_SALE;
                        $title = $unit->translated_title ?: ($unit->property?->name ?? $unit->code);
                        $photo = null;
                        if (is_array($unit->photos) && filled($unit->photos[0] ?? null)) $photo = $normalizeAsset($unit->photos[0]);
                        $unitHref = route('tenant.unit', ['tenant_slug' => $tenant->slug, 'unit' => $unit]);
                        $beds = $unit->beds ?: $unit->bedrooms;
                        $baths = $unit->baths ?: $unit->bathrooms;
                    @endphp
                    <article class="overflow-hidden rounded-[1.9rem] border border-[rgba(130,94,38,.16)] bg-[rgba(255,252,246,.96)] shadow-[0_22px_46px_-34px_rgba(55,38,12,.44)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_30px_62px_-30px_rgba(55,38,12,.52)]">
                        <a href="{{ $unitHref }}" class="block">
                            <div class="relative h-64 overflow-hidden bg-[rgba(34,38,30,.08)]">
                                @if ($photo)
                                    <img src="{{ $photo }}" alt="{{ $title }}" class="h-full w-full object-cover transition duration-700 hover:scale-105">
                                @else
                                    <div class="flex h-full items-center justify-center bg-gradient-to-br from-[rgba(15,90,70,.16)] to-[rgba(182,132,47,.18)] text-5xl font-black text-[color:var(--market-palm)]">{{ \Illuminate\Support\Str::substr($title, 0, 1) }}</div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-[rgba(15,18,14,.86)] via-transparent to-transparent"></div>
                                <div class="absolute left-4 right-4 top-4 flex items-start justify-between gap-3 {{ $isAr ? 'flex-row-reverse' : '' }}">
                                    <span class="inline-flex items-center rounded-full px-3 py-1.5 text-[10px] font-extrabold uppercase tracking-[0.18em] {{ $isSale ? 'bg-[rgba(182,132,47,.16)] text-[#ffe6af]' : 'bg-[rgba(15,90,70,.22)] text-[#ddf7ef]' }}">{{ $isSale ? __('For Sale') : __('For Rent') }}</span>
                                    @if ($unit->subcategory?->name)<span class="rounded-full border border-white/12 bg-white/10 px-3 py-1.5 text-[10px] font-extrabold uppercase tracking-[0.16em] text-white/90">{{ $unit->subcategory->name }}</span>@endif
                                </div>
                                <div class="absolute bottom-4 left-4 right-4 {{ $isAr ? 'text-right' : 'text-left' }}">
                                    <div class="text-[11px] font-extrabold uppercase tracking-[0.18em] text-[rgba(255,241,212,.86)]">{{ $unit->location ?: __('Location details coming soon') }}</div>
                                    <div class="mt-2 text-3xl font-extrabold tracking-[-0.03em] text-white">{{ $formatMoney($displayPrice($unit), $unit->currency ?? 'JOD') }}</div>
                                </div>
                            </div>
                        </a>
                        <div class="p-6">
                            <a href="{{ $unitHref }}" class="block"><h3 class="text-2xl font-extrabold tracking-[-0.03em] text-slate-900">{{ $title }}</h3></a>
                            <div class="mt-4 flex flex-wrap gap-3 text-[11px] font-extrabold uppercase tracking-[0.14em] text-slate-600">
                                @if ($unit->sqft)<span class="rounded-full bg-[rgba(15,90,70,.08)] px-3 py-1.5 text-[color:var(--market-palm)]">{{ number_format((float) $unit->sqft, 0) }} {{ $isAr ? 'قدم²' : 'sq ft' }}</span>@endif
                                <span class="rounded-full bg-[rgba(182,132,47,.11)] px-3 py-1.5 text-[color:var(--market-brass)]">{{ $beds ?: 0 }} {{ $isAr ? 'غرف' : 'beds' }}</span>
                                <span class="rounded-full bg-[rgba(157,90,59,.08)] px-3 py-1.5 text-[color:var(--market-clay)]">{{ $baths ?: 0 }} {{ $isAr ? 'حمامات' : 'baths' }}</span>
                            </div>
                            <div class="mt-6"><a href="{{ $unitHref }}" class="inline-flex items-center gap-2 text-sm font-bold text-[color:var(--market-palm)]">{{ $isAr ? 'عرض تفاصيل العقار' : 'View property details' }}<svg class="h-4 w-4 {{ $isAr ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg></a></div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-[1.8rem] border border-dashed border-[rgba(130,94,38,.28)] bg-[rgba(255,252,246,.75)] px-6 py-14 text-center text-slate-500">
                        <div class="mb-2 text-xl font-extrabold tracking-[-0.02em] text-slate-900">{{ $isAr ? 'لا توجد عقارات منشورة حالياً' : 'No published listings right now' }}</div>
                        <p class="text-sm leading-7">{{ $isAr ? 'ستظهر العقارات هنا فور نشرها على الواجهة العامة.' : 'Listings will appear here as soon as they are published to the public storefront.' }}</p>
                    </div>
                @endforelse
            </div>
        </section>
        
        <section id="home-map" class="tenant-surface rounded-[2rem] px-6 py-8 sm:px-8">
            <div class="tenant-ornament"></div>
            <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-[color:var(--market-brass)]">{{ $isAr ? 'خريطة العقارات' : 'Property Map' }}</p>
                    <h2 class="mt-3 text-3xl font-extrabold tracking-[-0.03em] text-slate-900">{{ $isAr ? 'اقرأ المخزون على الخريطة' : 'Read the inventory on a map' }}</h2>
                </div>
                <div class="text-sm text-slate-500">{{ $isAr ? 'المواضع التقريبية تُستخدم عندما لا تكون الإحداثيات محفوظة.' : 'Approximate area markers are used when exact coordinates are not saved.' }}</div>
            </div>
            <div class="mt-8 grid gap-5 lg:grid-cols-[.82fr_1.18fr]">
                <div class="space-y-5">
                    <div class="rounded-[1.6rem] border border-[rgba(130,94,38,.16)] bg-[rgba(255,252,246,.96)] p-5 shadow-[0_20px_42px_-34px_rgba(55,38,12,.42)]">
                        <div class="text-[11px] font-extrabold uppercase tracking-[0.16em] text-[color:var(--market-brass)]">{{ $isAr ? 'نقطة الانطلاق' : 'Map center' }}</div>
                        <div class="mt-3 text-2xl font-extrabold tracking-[-0.03em] text-slate-900">{{ $tenant->name }}</div>
                        <p class="mt-3 text-sm leading-7 text-slate-600">{{ $isAr ? 'تُظهر الخريطة العقارات المنشورة وتمنح الزائر قراءة أسرع للمناطق التي تغطيها الوكالة.' : 'The map surfaces published listings and gives visitors a faster read on the areas covered by this agency.' }}</p>
                    </div>
                    <div class="rounded-[1.6rem] border border-[rgba(130,94,38,.16)] bg-[rgba(255,252,246,.96)] p-5 shadow-[0_20px_42px_-34px_rgba(55,38,12,.42)]">
                        <div class="text-[11px] font-extrabold uppercase tracking-[0.16em] text-[color:var(--market-brass)]">{{ $isAr ? 'مفتاح الخريطة' : 'Map legend' }}</div>
                        <ul class="mt-4 space-y-3 text-sm text-slate-700">
                            <li>{{ $isAr ? 'العلامة الثابتة تعرض اسم العقار والسعر والموقع.' : 'Each permanent tooltip shows listing title, price, and location.' }}</li>
                            <li>{{ $isAr ? 'الدائرة الذهبية تشير إلى موضع تقريبي مبني على اسم المنطقة.' : 'The brass radius indicates an approximate area based on the saved location label.' }}</li>
                            <li>{{ $isAr ? 'يمكن الضغط على العلامة للانتقال إلى صفحة العقار.' : 'Click any marker to jump directly into the listing page.' }}</li>
                        </ul>
                    </div>
                </div>
                <div class="relative overflow-hidden rounded-[1.9rem] border border-[rgba(130,94,38,.14)] bg-[linear-gradient(145deg,rgba(248,240,221,.95),rgba(244,229,199,.92))] shadow-[0_24px_54px_-38px_rgba(57,42,16,.3)]">
                    <div id="tenant-home-map"
                        class="h-[440px] w-full"
                        data-center-lat="{{ $mapCenter['lat'] }}"
                        data-center-lng="{{ $mapCenter['lng'] }}"
                        data-center-zoom="{{ $mapCenter['zoom'] }}"
                        data-pins='@json($mapPins)'
                        data-tenant-name="{{ $tenant->name }}"
                        data-tenant-lat="{{ $tenantLat }}"
                        data-tenant-lng="{{ $tenantLng }}"
                        data-is-rtl="{{ $isAr ? '1' : '0' }}"></div>
                    <div class="absolute top-4 {{ $isAr ? 'left-4' : 'right-4' }} z-[450] rounded-full border border-white/14 bg-[rgba(19,30,24,.72)] px-4 py-2 text-[11px] font-extrabold uppercase tracking-[0.14em] text-[#fff4dc] backdrop-blur">{{ $isAr ? 'واجهة عامة' : 'Public map' }}</div>
                </div>
            </div>
        </section>

        <section class="tenant-surface rounded-[2rem] px-6 py-8 sm:px-8" style="background:linear-gradient(180deg,rgba(255,249,239,.98),rgba(247,237,214,.9));">
            <div class="grid gap-6 lg:grid-cols-[1.1fr_.9fr] lg:items-end">
                <div>
                    <div class="tenant-ornament"></div>
                    <p class="mt-6 text-xs font-bold uppercase tracking-[0.18em] text-[color:var(--market-brass)]">{{ $isAr ? 'الخطوة التالية' : 'Next Step' }}</p>
                    <h2 class="mt-3 text-3xl font-extrabold tracking-[-0.03em] text-slate-900">{{ $isAr ? 'تابع التصفح داخل الوكالة أو ارجع إلى السوق المركزي' : 'Keep browsing here or head back to the central marketplace' }}</h2>
                    <p class="mt-3 text-sm leading-7 text-slate-600">{{ $isAr ? 'بعد الانطباع الأول من الواجهة، يمكن للزائر الانتقال إلى صفحة العقار المناسبة أو مقارنة وكالات أخرى من السوق العام.' : 'Once the visitor has a first impression from this storefront, they can dive into a property page or compare more agencies from the public marketplace.' }}</p>
                </div>
                <div class="flex flex-wrap gap-3 {{ $isAr ? 'lg:justify-end' : '' }}">
                    <a href="{{ $tenantSearchUrl }}" class="inline-flex items-center justify-center rounded-2xl px-5 py-3 text-sm font-bold text-white shadow-[0_18px_34px_-18px_rgba(15,90,70,.8)] transition hover:-translate-y-0.5" style="background:linear-gradient(135deg,var(--tenant-primary),var(--tenant-accent))">{{ $isAr ? 'استكشف كل العقارات' : 'Explore all listings' }}</a>
                    <a href="{{ $centralMarketplaceUrl }}" class="inline-flex items-center justify-center rounded-2xl border border-[rgba(130,94,38,.16)] bg-white px-5 py-3 text-sm font-bold text-slate-900 transition hover:-translate-y-0.5">{{ $isAr ? 'العودة إلى السوق' : 'Back to marketplace' }}</a>
                </div>
            </div>
        </section>
    </main>

    <footer class="mt-4 bg-[linear-gradient(140deg,rgba(15,32,26,.98),rgba(15,90,70,.92)_48%,rgba(48,33,15,.92))] text-[#fff8ea]">
        <div class="tenant-shell grid gap-10 px-4 py-12 sm:px-6 lg:grid-cols-[1.15fr_.85fr_.85fr] lg:px-8">
            <div class="space-y-4">
                <div class="text-xs font-bold uppercase tracking-[0.18em] text-white/55">{{ $isAr ? 'واجهة عامة' : 'Public Storefront' }}</div>
                <h3 class="text-3xl font-extrabold tracking-[-0.03em] text-white">{{ $tenant->name }}</h3>
                <p class="max-w-xl text-sm leading-7 text-white/74">{{ $tenantTagline }}</p>
                @if ($tenantWebsiteLabel)<a href="{{ $tenantWebsiteLabel }}" target="_blank" rel="noopener noreferrer" class="inline-flex text-sm font-bold text-[rgba(255,231,176,.96)]">{{ $tenantWebsiteLabel }}</a>@endif
            </div>
            <div class="space-y-4">
                <div class="text-xs font-bold uppercase tracking-[0.18em] text-white/55">{{ $isAr ? 'روابط سريعة' : 'Quick Links' }}</div>
                <div class="space-y-2 text-sm text-white/74">
                    <a href="{{ $tenantWebsite }}" class="block">{{ $isAr ? 'الرئيسية' : 'Home' }}</a>
                    <a href="{{ $tenantSearchUrl }}" class="block">{{ $isAr ? 'كل العقارات' : 'All listings' }}</a>
                    <a href="{{ $centralMarketplaceUrl }}" class="block">{{ $isAr ? 'السوق المركزي' : 'Central marketplace' }}</a>
                    @foreach ($footerLinks as $link)<a href="{{ $link['href'] }}" class="block">{{ $link['label'] }}</a>@endforeach
                </div>
            </div>
            <div class="space-y-4">
                <div class="text-xs font-bold uppercase tracking-[0.18em] text-white/55">{{ $isAr ? 'تواصل' : 'Contact' }}</div>
                <div class="space-y-2 text-sm text-white/74">
                    @if ($tenantContactEmail)<div>{{ $tenantContactEmail }}</div>@endif
                    @if ($tenantContactPhone)<div>{{ $tenantContactPhone }}</div>@endif
                    @if ($tenantAddress)<div>{{ $tenantAddress }}</div>@endif
                </div>
                @if ($socialLinks->isNotEmpty())
                    <div class="flex flex-wrap gap-3 pt-2">
                        @foreach ($socialLinks as $social)
                            <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex rounded-full border border-white/12 bg-white/8 px-4 py-2 text-xs font-bold uppercase tracking-[0.14em] text-white/82">{{ $social['label'] ?? __('Link') }}</a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </footer>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const mapEl = document.getElementById('tenant-home-map');
    if (!mapEl || typeof window.L === 'undefined') return;
    const centerLat = Number(mapEl.dataset.centerLat);
    const centerLng = Number(mapEl.dataset.centerLng);
    const centerZoom = Number(mapEl.dataset.centerZoom || 12);
    const tenantLat = Number(mapEl.dataset.tenantLat);
    const tenantLng = Number(mapEl.dataset.tenantLng);
    const tenantName = mapEl.dataset.tenantName || '';
    const isRtl = mapEl.dataset.isRtl === '1';
    const pins = JSON.parse(mapEl.dataset.pins || '[]');
    const map = L.map(mapEl, { scrollWheelZoom: false, dragging: true, tap: false }).setView([centerLat, centerLng], centerZoom);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap contributors', maxZoom: 19 }).addTo(map);
    if (Number.isFinite(tenantLat) && Number.isFinite(tenantLng)) {
        L.circleMarker([tenantLat, tenantLng], { radius: 8, color: '#fff4dc', weight: 3, fillColor: '#0f5a46', fillOpacity: 1 }).addTo(map).bindTooltip(`<div class="tenant-map-bubble" dir="${isRtl ? 'rtl' : 'ltr'}"><div class="tenant-map-bubble-title">${tenantName}</div><div class="tenant-map-bubble-meta">${isRtl ? 'موقع الوكالة' : 'Agency anchor'}</div></div>`, { permanent: true, direction: 'top', offset: [0, -18], className: 'tenant-map-tooltip', opacity: 1 }).openTooltip();
    }
    pins.forEach((pin) => {
        if (!Number.isFinite(Number(pin.lat)) || !Number.isFinite(Number(pin.lng))) return;
        const approximate = pin.mode === 'approximate';
        if (approximate) L.circle([pin.lat, pin.lng], { radius: 900, color: '#b6842f', weight: 1, fillColor: '#b6842f', fillOpacity: 0.12 }).addTo(map);
        const marker = L.circleMarker([pin.lat, pin.lng], { radius: 9, color: '#fff4dc', weight: 3, fillColor: approximate ? '#b6842f' : '#2f7a72', fillOpacity: 1 }).addTo(map);
        marker.bindTooltip(`<div class="tenant-map-bubble" dir="${isRtl ? 'rtl' : 'ltr'}"><div class="tenant-map-bubble-title">${pin.title}</div><div class="tenant-map-bubble-meta">${pin.price} • ${pin.location}${approximate ? ` • ${isRtl ? 'موضع تقريبي' : 'Approximate area'}` : ''}</div></div>`, { permanent: true, direction: 'top', offset: [0, -18], className: 'tenant-map-tooltip', opacity: 1 }).openTooltip();
        marker.on('click', () => { if (pin.href) window.location.href = pin.href; });
    });
    window.setTimeout(() => map.invalidateSize(), 180);
});
</script>
@endsection
