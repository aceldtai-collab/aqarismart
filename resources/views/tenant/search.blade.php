@extends('layouts.app')

@section('title', ($tenant->name ?? config('app.name')) . ' - ' . __('Search'))

@section('content')
@php
    use App\Models\Unit;
    use Illuminate\Support\Str;

    $locale = app()->getLocale();
    $isAr = $locale === 'ar';
    $langParam = config('locales.cookie_name', 'lang');
    $urlEn = request()->fullUrlWithQuery([$langParam => 'en']);
    $urlAr = request()->fullUrlWithQuery([$langParam => 'ar']);
    $theme = is_array($tenant->settings ?? null) ? $tenant->settings : [];

    $normalizeAsset = function (?string $value): ?string {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        $value = trim($value);
        if (Str::startsWith($value, ['http://', 'https://'])) {
            return $value;
        }

        return url('/') . '/' . (Str::startsWith($value, 'storage/') ? $value : 'storage/' . ltrim($value, '/'));
    };

    $scheme = request()->getScheme() ?: 'http';
    $port = request()->getPort();
    $defaultPort = $scheme === 'https' ? 443 : 80;
    $portPart = $port && $port !== $defaultPort ? ':' . $port : '';
    $centralMarketplaceUrl = sprintf('%s://%s%s/marketplace', $scheme, config('tenancy.base_domain'), $portPart);

    $heroImage = $normalizeAsset($theme['header_bg_url'] ?? null);
    $tenantWebsite = route('tenant.home', ['tenant_slug' => $tenant->slug]);
    $searchAction = route('tenant.search', ['tenant_slug' => $tenant->slug]);
    $clearUrl = route('tenant.search', ['tenant_slug' => $tenant->slug]);

    $navTx = [
        'brand' => $isAr ? 'عقاري سمارت' : 'Aqari Smart',
        'home' => $isAr ? 'الرئيسية' : 'Home',
        'featured_nav' => $isAr ? 'نتائج البحث' : 'Search Results',
        'sale_nav' => $isAr ? 'الخريطة' : 'Map',
        'rent_nav' => $isAr ? 'السوق' : 'Marketplace',
        'contact_nav' => $isAr ? 'الواجهة' : 'Storefront',
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
        'guest_subtitle' => $isAr ? 'رحلة بحث كاملة داخل واجهة الوكالة ثم انتقال مباشر إلى صفحة العقار.' : 'A complete search journey inside the storefront, then direct movement into the property page.',
        'switch_language' => $isAr ? 'تغيير اللغة' : 'Switch language',
    ];

    $navLinks = [
        ['label' => $navTx['home'], 'href' => $tenantWebsite],
        ['label' => $navTx['featured_nav'], 'href' => '#search-results'],
        ['label' => $navTx['sale_nav'], 'href' => '#search-map-section'],
        ['label' => $navTx['rent_nav'], 'href' => $centralMarketplaceUrl],
        ['label' => $navTx['contact_nav'], 'href' => $tenantWebsite],
    ];

    $searchUi = [
        'hero_title' => $isAr ? 'ابحث داخل عقارات ' . $tenant->name : 'Search inside ' . $tenant->name,
        'hero_subtitle' => $isAr ? 'هذه الصفحة هي مرآة الواجهة العامة: خريطة حيّة، فلاتر دقيقة، ونتائج مصممة لبدء رحلة العميل بثقة.' : 'This page mirrors the public storefront: a live map, focused filters, and result cards designed to start the client journey with confidence.',
    ];
@endphp

    @include('search.experience', [
    'context' => 'tenant',
    'tenant' => $tenant,
    'units' => $units,
    'categories' => $categories,
    'filters' => $filters,
    'searchExperience' => $searchExperience,
    'searchAction' => $searchAction,
    'clearUrl' => $clearUrl,
    'heroImage' => $heroImage,
    'themePrimary' => $theme['primary_color'] ?? '#0f5a46',
    'themeAccent' => $theme['accent_color'] ?? '#b6842f',
    'sellWithUsUrl' => Route::has('tenant.sales-flow') ? route('tenant.sales-flow', ['tenant_slug' => $tenant->slug]) : '#',
    'navTx' => $navTx,
    'navLinks' => $navLinks,
    'navBrandHref' => $centralMarketplaceUrl,
    'navBrandLabel' => $navTx['brand'],
    'urlEn' => $urlEn,
    'urlAr' => $urlAr,
    'activeFiltersCount' => collect($filters)->reject(fn ($value, $key) => $key === 'listing_type' && ($value ?? Unit::LISTING_RENT) === Unit::LISTING_RENT)->filter(fn ($value) => filled($value) && ! in_array($value, [0, '0'], true))->count(),
    'searchUi' => $searchUi,
])
@endsection
