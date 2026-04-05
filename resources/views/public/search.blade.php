@php
    $loc = app()->getLocale();
    $isAr = $loc === 'ar';
    $assets = $landing['assets'] ?? [];
    $langParam = config('locales.cookie_name', 'lang');
    $urlEn = request()->fullUrlWithQuery([$langParam => 'en']);
    $urlAr = request()->fullUrlWithQuery([$langParam => 'ar']);
    $heroImage = $assets['hero_image'] ?? null;
    $sellWithUsUrl = Route::has('sales-flow')
        ? route('sales-flow')
        : (Route::has('book-call') ? route('book-call') : '#');
    $marketplaceUrl = route('public.marketplace');
    $clearParams = [];
    if (filled($filters['listing_type'] ?? null)) {
        $clearParams['listing_type'] = $filters['listing_type'];
    }
    $clearUrl = route('public.search', $clearParams);

    $navTx = [
        'brand' => $isAr ? 'عقاري سمارت' : 'Aqari Smart',
        'home' => $isAr ? 'الرئيسية' : 'Home',
        'featured_nav' => $isAr ? 'نتائج البحث' : 'Search Results',
        'sale_nav' => $isAr ? 'الخريطة' : 'Map',
        'rent_nav' => $isAr ? 'السوق' : 'Marketplace',
        'contact_nav' => $isAr ? 'احجز عرضاً' : 'Book Demo',
        'login_cta' => $isAr ? 'تسجيل الدخول' : 'Sign in',
        'register_cta' => $isAr ? 'إنشاء حساب' : 'Create account',
        'sell_cta' => $isAr ? 'بيع معنا' : 'Sell with us',
        'profile_cta' => $isAr ? 'الملف الشخصي' : 'Profile',
        'menu_cta' => $isAr ? 'القائمة' : 'Menu',
        'close_cta' => $isAr ? 'إغلاق' : 'Close',
        'account_title' => $isAr ? 'حسابك' : 'Your Account',
        'browse_title' => $isAr ? 'تنقل داخل السوق' : 'Browse Marketplace',
        'dashboard_cta' => $isAr ? 'لوحة التحكم' : 'Dashboard',
        'logout_cta' => $isAr ? 'تسجيل الخروج' : 'Log Out',
        'welcome_cta' => $isAr ? 'أهلاً' : 'Welcome',
        'guest_subtitle' => $isAr ? 'ابحث في السوق، ثم انتقل مباشرة إلى صفحة العقار المناسبة على موقع الوكالة.' : 'Search the market, then move directly into the right property page on the agency site.',
        'switch_language' => $isAr ? 'تغيير اللغة' : 'Switch language',
    ];

    $navLinks = [
        ['label' => $navTx['home'], 'href' => $marketplaceUrl],
        ['label' => $navTx['featured_nav'], 'href' => '#search-results'],
        ['label' => $navTx['sale_nav'], 'href' => '#search-map-section'],
        ['label' => $navTx['rent_nav'], 'href' => $marketplaceUrl],
        ['label' => $navTx['contact_nav'], 'href' => Route::has('book-call') ? route('book-call') : '#'],
    ];

    $searchUi = [
        'hero_title' => $isAr ? 'ابحث في السوق العام بالكامل' : 'Search the full public market',
        'hero_subtitle' => $isAr ? 'هذه هي صفحة المرآة الحقيقية للسوق: خريطة حيّة، توزيع جغرافي واضح، ونتائج تقود مباشرة إلى صفحة العقار على موقع الوكالة.' : 'This is the true mirror page for the market: a live map, clear geographic spread, and results that move directly into the property page on the agency site.',
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ $loc }}" dir="{{ $isAr ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $isAr ? 'بحث السوق العقاري' : 'Property Search' }} | {{ $navTx['brand'] }}</title>
    <meta name="description" content="{{ $searchUi['hero_subtitle'] }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&display=swap" rel="stylesheet" />
    @if($isAr)<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">@endif
    <x-vite-assets />
</head>
<body>
    @include('search.experience', [
        'context' => 'public',
        'units' => $units,
        'categories' => $categories,
        'cities' => $cities,
        'tenants' => $tenants,
        'filters' => $filters,
        'searchExperience' => $searchExperience,
        'searchAction' => route('public.search'),
        'clearUrl' => $clearUrl,
        'heroImage' => $heroImage,
        'themePrimary' => '#0f5a46',
        'themeAccent' => '#b6842f',
        'sellWithUsUrl' => $sellWithUsUrl,
        'navTx' => $navTx,
        'navLinks' => $navLinks,
        'navBrandHref' => $marketplaceUrl,
        'navBrandLabel' => $navTx['brand'],
        'urlEn' => $urlEn,
        'urlAr' => $urlAr,
        'activeFiltersCount' => collect($filters)
            ->reject(fn ($value, $key) => $key === 'sort' && ($value ?? 'latest') === 'latest')
            ->filter(fn ($value) => filled($value) && ! in_array($value, [0, '0'], true))
            ->count(),
        'searchUi' => $searchUi,
    ])
</body>
</html>
