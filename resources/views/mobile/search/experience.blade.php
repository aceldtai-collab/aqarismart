@extends('mobile.layouts.app', [
    'title' => ($context ?? 'public') === 'tenant' && isset($tenant) ? $tenant->name : (app()->getLocale() === 'ar' ? 'بحث العقارات' : 'Property search'),
    'show_back_button' => false,
    'body_class' => 'mobile-search-shell',
])

@section('full_width', true)

@php
    use App\Models\Unit;
    use Illuminate\Support\Str;

    $context = $context ?? 'public';
    $tenant = $tenant ?? null;
    $units = $units ?? collect();
    $categories = $categories ?? collect();
    $cities = $cities ?? collect();
    $tenants = $tenants ?? collect();
    $filters = $filters ?? [];
    $searchExperience = $searchExperience ?? [];
    $isAr = app()->getLocale() === 'ar';
    $summary = $searchExperience['summary'] ?? [];
    $mapData = $searchExperience['map'] ?? ['center' => ['lat' => 31.9539494, 'lng' => 35.9106350, 'zoom' => 7], 'markers' => []];
    $locationClusters = collect($searchExperience['locations'] ?? []);
    $typeClusters = collect($searchExperience['types'] ?? []);

    $settings = $context === 'tenant' && $tenant ? (is_array($tenant->settings ?? null) ? $tenant->settings : []) : [];
    $themePrimary = is_string($settings['primary_color'] ?? null) && trim($settings['primary_color']) !== '' ? trim($settings['primary_color']) : '#0f5a46';
    $themeAccent = is_string($settings['accent_color'] ?? null) && trim($settings['accent_color']) !== '' ? trim($settings['accent_color']) : '#b6842f';
    $themeSurface = $context === 'tenant' ? 'rgba(255,255,255,.10)' : 'rgba(255,255,255,.08)';

    $subcategories = $categories->flatMap(function ($category) {
        return $category->subcategories ?? collect();
    })->sortBy('name')->values();

    $priceFormatter = static function ($amount, string $currency = 'JOD'): string {
        if ($amount === null || $amount === '') {
            return __('Price on request');
        }

        return strtoupper($currency) . ' ' . number_format((float) $amount, 0);
    };

    $locationLabel = static function (Unit $unit) use ($isAr): string {
        return collect([
            $unit->location,
            $unit->city?->{$isAr ? 'name_ar' : 'name_en'},
            $unit->city?->name_en,
            $unit->area?->{$isAr ? 'name_ar' : 'name_en'},
            $unit->area?->name_en,
            $unit->property?->city,
            $unit->property?->address,
        ])->map(fn ($value) => trim((string) $value))
            ->first(fn ($value) => $value !== '') ?: ($isAr ? 'تفاصيل الموقع قريباً' : 'Location details coming soon');
    };

    $titleLabel = static function (Unit $unit): string {
        return $unit->translated_title ?: ($unit->property?->name ?: $unit->code);
    };

    $ui = [
        'eyebrow' => $context === 'tenant'
            ? ($isAr ? 'بحث حي داخل الوكالة' : 'Live search inside the agency')
            : ($isAr ? 'مرآة السوق العامة' : 'Mirror of the marketplace'),
        'title' => $context === 'tenant'
            ? ($isAr ? 'ابحث في مخزون الوكالة بخريطة ونتائج حيّة' : 'Search the agency inventory with live map and results')
            : ($isAr ? 'ابحث في السوق على خريطة واحدة' : 'Search the market on one map'),
        'subtitle' => $context === 'tenant'
            ? ($isAr ? 'رحلة بحث احترافية تجمع الفلاتر والخريطة والنتائج في صفحة واحدة واضحة على الجوال.' : 'A flagship mobile search that brings filters, map, and results into one clear journey.')
            : ($isAr ? 'رحلة بحث عامة تجمع العقارات النشطة من الوكالات المختلفة في صفحة موبايل احترافية.' : 'A public mobile search that pulls active agency inventory into one professional page.'),
        'quickTitle' => $isAr ? 'حرك البحث بسرعة' : 'Move through search faster',
        'quickText' => $isAr ? 'ابدأ بالكلمة المفتاحية، ثم اضبط النوع والمدينة والسعر، وبعدها اقرأ النتائج على الخريطة والبطاقات.' : 'Start with a keyword, tune listing type, city, and price, then read the results on the map and cards.',
        'search' => $isAr ? 'استكشف' : 'Explore',
        'clear' => $isAr ? 'مسح الفلاتر' : 'Clear filters',
        'back' => $isAr ? 'العودة' : 'Back',
        'rent' => $isAr ? 'إيجار' : 'Rent',
        'sale' => $isAr ? 'بيع' : 'Sale',
        'all' => $isAr ? 'الكل' : 'All',
        'keyword' => $isAr ? 'ابحث بالاسم أو الرمز أو الموقع' : 'Search by title, code, or location',
        'subcategory' => $isAr ? 'نوع العقار' : 'Property type',
        'city' => $isAr ? 'المدينة' : 'City',
        'agency' => $isAr ? 'الوكالة' : 'Agency',
        'bedrooms' => $isAr ? 'الغرف' : 'Bedrooms',
        'priceMin' => $isAr ? 'من سعر' : 'Min price',
        'priceMax' => $isAr ? 'إلى سعر' : 'Max price',
        'sort' => $isAr ? 'الترتيب' : 'Sort',
        'latest' => $isAr ? 'الأحدث' : 'Latest',
        'oldest' => $isAr ? 'الأقدم' : 'Oldest',
        'priceLow' => $isAr ? 'السعر: الأقل أولاً' : 'Price: low to high',
        'priceHigh' => $isAr ? 'السعر: الأعلى أولاً' : 'Price: high to low',
        'mapTitle' => $isAr ? 'الخريطة الحية' : 'Live map',
        'mapText' => $isAr ? 'كل نتيجة موثقة بإشارة على الخريطة مع بطاقة سريعة ثم انتقال مباشر إلى العقار.' : 'Every result is reflected on the map with a quick card and direct entry into the property page.',
        'resultsTitle' => $isAr ? 'نتائج البحث' : 'Search results',
        'resultsText' => $isAr ? 'بطاقات مصممة للقراءة السريعة على الجوال قبل فتح صفحة العقار.' : 'Cards designed for fast mobile reading before opening the property page.',
        'locationsTitle' => $isAr ? 'إيقاع المناطق' : 'Area rhythm',
        'typesTitle' => $isAr ? 'مزيج العقارات' : 'Property mix',
        'priceTitle' => $isAr ? 'مدى الأسعار' : 'Price span',
        'viewProperty' => $isAr ? 'عرض العقار' : 'View property',
        'photos' => $isAr ? 'صور' : 'photos',
        'beds' => $isAr ? 'غرف' : 'beds',
        'baths' => $isAr ? 'حمامات' : 'baths',
        'sqft' => $isAr ? 'قدم²' : 'sqft',
        'perYear' => $isAr ? 'سنوياً' : 'per year',
        'noMap' => $isAr ? 'لا توجد مواقع مهيأة للخريطة حالياً' : 'No mapped results yet',
        'noMapText' => $isAr ? 'ستظهر العلامات هنا عندما تتوفر إحداثيات أو مواقع قابلة للتقريب.' : 'Pins appear here once results have coordinates or a recognizable location.',
        'noResults' => $isAr ? 'لا توجد نتائج مطابقة الآن' : 'No matching results right now',
        'noResultsText' => $isAr ? 'جرّب تغيير البحث أو الفلاتر أو التبديل بين البيع والإيجار.' : 'Try another query, adjust filters, or switch between rent and sale.',
        'priceOnRequest' => $isAr ? 'السعر عند الطلب' : 'Price on request',
        'allTypes' => $isAr ? 'كل الأنواع' : 'All types',
        'allCities' => $isAr ? 'كل المدن' : 'All cities',
        'allAgencies' => $isAr ? 'كل الوكالات' : 'All agencies',
        'anyBedrooms' => $isAr ? 'أي عدد غرف' : 'Any bedrooms',
        'openOnMap' => $isAr ? 'الخريطة' : 'Map',
    ];
@endphp

@push('head')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<style>
    :root{
        --msearch-ink:#1f2a24;
        --msearch-palm:#0f5a46;
        --msearch-river:#2f7a72;
        --msearch-brass:#b6842f;
        --msearch-clay:#9d5a3b;
        --msearch-line:rgba(130,94,38,.16);
        --msearch-cream:#fbf7ef;
        --msearch-sand:#efe4cf;
    }
    body.mobile-search-shell{
        background:
            radial-gradient(circle at top left, rgba(182,132,47,.14), transparent 22%),
            radial-gradient(circle at top right, rgba(15,90,70,.12), transparent 24%),
            linear-gradient(180deg, #eee2cc 0, #f7efdf 300px, #fbf7ef 100%);
        color:var(--msearch-ink);
        font-family:'Manrope',system-ui,sans-serif;
    }
    html[dir="rtl"] body.mobile-search-shell{
        font-family:'Cairo','Manrope',system-ui,sans-serif;
    }
    body.mobile-search-shell header.sticky{
        background:linear-gradient(145deg, rgba(15,32,26,.96), {{ $themePrimary }} 54%, {{ $themeAccent }});
        box-shadow:0 16px 36px -24px rgba(28,22,10,.55);
    }
    body.mobile-search-shell aside{
        background:rgba(252,248,241,.98);
        color:var(--msearch-ink);
    }
    body.mobile-search-shell aside .bg-gradient-to-br.from-emerald-600.to-emerald-700{
        background:linear-gradient(145deg, rgba(15,32,26,.96), {{ $themePrimary }} 54%, {{ $themeAccent }}) !important;
    }
    .msearch-page{min-height:100vh;padding-bottom:2rem}
    .msearch-shell{padding-inline:1rem}
    .msearch-hero{
        position:relative;
        overflow:hidden;
        border-radius:2rem;
        background:
            radial-gradient(circle at top left, rgba(255,255,255,.16), transparent 28%),
            linear-gradient(145deg, rgba(15,32,26,.96), {{ $themePrimary }} 54%, {{ $themeAccent }});
        color:#fff8ea;
        box-shadow:0 30px 64px -34px rgba(28,22,10,.58);
    }
    .msearch-hero::after{
        content:"";
        position:absolute;
        inset:0;
        background:
            linear-gradient(180deg, rgba(10,16,13,.04), rgba(10,16,13,.22)),
            radial-gradient(circle at 85% 14%, rgba(255,255,255,.08), transparent 24%);
        pointer-events:none;
    }
    .msearch-copy{position:relative;z-index:1}
    .msearch-kicker{
        font-size:.7rem;
        font-weight:800;
        letter-spacing:.22em;
        text-transform:uppercase;
        color:rgba(255,241,212,.72);
    }
    .msearch-title{
        margin-top:.7rem;
        font-size:2.2rem;
        line-height:.98;
        font-weight:900;
        letter-spacing:-.05em;
        color:#fff8ea;
    }
    .msearch-text{
        margin-top:.8rem;
        font-size:.9rem;
        line-height:1.85;
        color:rgba(255,255,255,.84);
    }
    .msearch-stat{
        border:1px solid rgba(255,255,255,.12);
        background:{{ $themeSurface }};
        border-radius:1.35rem;
        padding:.9rem .95rem;
        backdrop-filter:blur(14px);
    }
    .msearch-stat-label{
        font-size:.65rem;
        letter-spacing:.18em;
        text-transform:uppercase;
        color:rgba(255,244,221,.62);
        font-weight:700;
    }
    .msearch-stat-value{
        margin-top:.35rem;
        font-size:1rem;
        line-height:1.2;
        font-weight:800;
        color:#fff8ea;
    }
    .msearch-link{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        border-radius:999px;
        border:1px solid rgba(255,255,255,.14);
        background:rgba(255,255,255,.1);
        padding:.72rem .95rem;
        font-size:.72rem;
        font-weight:800;
        letter-spacing:.14em;
        text-transform:uppercase;
        color:#fff8ea;
        text-decoration:none;
        backdrop-filter:blur(10px);
    }
    .msearch-card{
        border:1px solid var(--msearch-line);
        background:rgba(255,252,246,.95);
        box-shadow:0 24px 54px -36px rgba(57,42,16,.34);
    }
    .msearch-filter-card{
        border-radius:1.75rem;
        padding:1rem;
    }
    .msearch-filter-grid{
        display:grid;
        gap:.75rem;
    }
    .msearch-label{
        display:block;
        margin-bottom:.35rem;
        font-size:.68rem;
        font-weight:800;
        letter-spacing:.16em;
        text-transform:uppercase;
        color:#74808b;
    }
    .msearch-input,.msearch-select{
        display:block;
        width:100%;
        min-height:3.2rem;
        border:1px solid rgba(130,94,38,.18);
        background:rgba(255,255,255,.88);
        color:var(--msearch-ink);
        border-radius:1rem;
        padding:.82rem 1rem;
        font-size:.92rem;
        font-weight:600;
    }
    .msearch-input:focus,.msearch-select:focus{
        outline:none;
        border-color:rgba(182,132,47,.72);
        box-shadow:0 0 0 4px rgba(182,132,47,.12);
        background:#fff;
    }
    .msearch-select{
        appearance:none;
        background-image:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2378786b' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.6' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position:right 1rem center;
        background-repeat:no-repeat;
        background-size:1rem 1rem;
        padding-right:2.5rem;
    }
    html[dir="rtl"] .msearch-select{
        background-position:left 1rem center;
        padding-right:1rem;
        padding-left:2.5rem;
    }
    .msearch-toggle{
        display:flex;
        gap:.35rem;
        border-radius:1.1rem;
        background:rgba(15,90,70,.08);
        padding:.3rem;
    }
    .msearch-toggle button{
        flex:1;
        border-radius:.9rem;
        padding:.8rem .65rem;
        font-size:.78rem;
        font-weight:800;
        letter-spacing:.12em;
        text-transform:uppercase;
        color:#6d7069;
        transition:all .18s ease;
    }
    .msearch-toggle button.is-active{
        background:linear-gradient(135deg, {{ $themePrimary }}, {{ $themeAccent }});
        color:#fff;
        box-shadow:0 16px 26px -18px rgba(15,90,70,.8);
    }
    .msearch-submit{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        min-height:3.2rem;
        border-radius:1rem;
        padding:.9rem 1rem;
        font-size:.82rem;
        font-weight:800;
        letter-spacing:.12em;
        text-transform:uppercase;
        color:#fff8ea;
        background:linear-gradient(135deg, {{ $themePrimary }}, {{ $themeAccent }});
        box-shadow:0 18px 34px -18px rgba(15,90,70,.8);
    }
    .msearch-secondary{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        min-height:3.2rem;
        border-radius:1rem;
        padding:.9rem 1rem;
        font-size:.78rem;
        font-weight:800;
        letter-spacing:.12em;
        text-transform:uppercase;
        color:var(--msearch-ink);
        border:1px solid rgba(130,94,38,.16);
        background:rgba(255,255,255,.72);
    }
    .msearch-strip{
        display:flex;
        gap:.75rem;
        overflow-x:auto;
        padding-bottom:.15rem;
        -ms-overflow-style:none;
        scrollbar-width:none;
    }
    .msearch-strip::-webkit-scrollbar{display:none}
    .msearch-chip-card{
        min-width:176px;
        border-radius:1.35rem;
        padding:1rem;
    }
    .msearch-chip-label{
        font-size:.62rem;
        font-weight:800;
        letter-spacing:.18em;
        text-transform:uppercase;
        color:var(--msearch-brass);
    }
    .msearch-chip-value{
        margin-top:.55rem;
        font-size:1rem;
        font-weight:900;
        color:var(--msearch-ink);
    }
    .msearch-map-card{
        overflow:hidden;
        border-radius:1.75rem;
    }
    .msearch-map-frame{
        position:relative;
        height:21rem;
        background:#e7ddca;
    }
    .msearch-map-empty{
        position:absolute;
        inset:0;
        display:flex;
        flex-direction:column;
        align-items:center;
        justify-content:center;
        padding:1.5rem;
        text-align:center;
        color:#63706b;
    }
    .msearch-results{
        display:grid;
        gap:1rem;
    }
    .msearch-result-card{
        overflow:hidden;
        border-radius:1.7rem;
    }
    .msearch-gallery{
        position:relative;
        overflow:hidden;
        background:#ede3d4;
    }
    .msearch-gallery-track{
        display:flex;
        transition:transform .28s ease;
    }
    .msearch-gallery-slide{
        width:100%;
        flex:0 0 100%;
        aspect-ratio:16/11;
    }
    .msearch-gallery-slide img{
        height:100%;
        width:100%;
        object-fit:cover;
    }
    .msearch-gallery-btn{
        position:absolute;
        top:50%;
        z-index:5;
        display:flex;
        height:2rem;
        width:2rem;
        align-items:center;
        justify-content:center;
        border-radius:999px;
        background:rgba(14,19,17,.46);
        color:#fff;
        transform:translateY(-50%);
        backdrop-filter:blur(10px);
    }
    .msearch-gallery-btn.prev{left:.65rem}
    .msearch-gallery-btn.next{right:.65rem}
    .msearch-gallery-dots{
        position:absolute;
        left:50%;
        bottom:.7rem;
        z-index:5;
        display:flex;
        gap:.3rem;
        transform:translateX(-50%);
        border-radius:999px;
        background:rgba(0,0,0,.35);
        padding:.35rem .5rem;
        backdrop-filter:blur(8px);
    }
    .msearch-gallery-dot{
        height:.35rem;
        width:.35rem;
        border-radius:999px;
        background:rgba(255,255,255,.42);
    }
    .msearch-gallery-dot.is-active{background:#fff}
    .msearch-result-topbar,
    .msearch-result-bottombar{
        position:absolute;
        inset-inline:0;
        z-index:4;
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:.55rem;
        padding:1rem;
        pointer-events:none;
    }
    .msearch-result-topbar{top:0}
    .msearch-result-bottombar{bottom:0}
    .msearch-badge{
        display:inline-flex;
        align-items:center;
        border-radius:999px;
        padding:.45rem .72rem;
        font-size:.62rem;
        font-weight:800;
        letter-spacing:.14em;
        text-transform:uppercase;
        color:#fff;
        backdrop-filter:blur(10px);
    }
    .msearch-badge.sale{background:rgba(182,132,47,.9)}
    .msearch-badge.rent{background:rgba(15,90,70,.9)}
    .msearch-badge.soft{background:rgba(255,255,255,.18); color:#fff8ea}
    .msearch-price{
        display:inline-flex;
        align-items:center;
        border-radius:999px;
        background:rgba(255,249,239,.94);
        padding:.5rem .78rem;
        font-size:.82rem;
        font-weight:900;
        color:var(--msearch-ink);
        box-shadow:0 12px 24px -20px rgba(0,0,0,.42);
    }
    .msearch-code{
        display:inline-flex;
        align-items:center;
        border-radius:999px;
        border:1px solid rgba(255,255,255,.24);
        background:rgba(14,19,17,.28);
        padding:.48rem .72rem;
        font-size:.66rem;
        font-weight:800;
        letter-spacing:.12em;
        text-transform:uppercase;
        color:#fff8ea;
        backdrop-filter:blur(8px);
    }
    .msearch-body{padding:1rem}
    .msearch-card-title{
        margin-top:.55rem;
        font-size:1.08rem;
        line-height:1.15;
        font-weight:900;
        letter-spacing:-.03em;
        color:var(--msearch-ink);
    }
    .msearch-card-subtitle{
        margin-top:.5rem;
        font-size:.86rem;
        line-height:1.8;
        color:#64716a;
    }
    .msearch-facts{
        display:flex;
        flex-wrap:wrap;
        gap:.45rem;
        margin-top:.8rem;
    }
    .msearch-pill{
        border-radius:999px;
        padding:.38rem .68rem;
        font-size:.62rem;
        font-weight:800;
        letter-spacing:.12em;
        text-transform:uppercase;
    }
    .msearch-pill.palm{background:rgba(15,90,70,.08); color:var(--msearch-palm)}
    .msearch-pill.brass{background:rgba(182,132,47,.12); color:var(--msearch-brass)}
    .msearch-pill.clay{background:rgba(157,90,59,.08); color:var(--msearch-clay)}
    .msearch-pill.soft{background:rgba(100,113,106,.08); color:#64716a}
    .msearch-card-footer{
        margin-top:1rem;
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:.75rem;
    }
    .msearch-link-inline{
        display:inline-flex;
        align-items:center;
        gap:.35rem;
        font-size:.72rem;
        font-weight:800;
        letter-spacing:.12em;
        text-transform:uppercase;
        color:var(--msearch-palm);
        text-decoration:none;
    }
    .msearch-empty{
        border:1px dashed rgba(130,94,38,.22);
        background:rgba(255,252,246,.82);
        border-radius:1.5rem;
        padding:2rem 1.2rem;
        text-align:center;
    }
    .msearch-result-card.is-active{
        box-shadow:0 28px 54px -34px rgba(15,90,70,.42);
        border-color:rgba(15,90,70,.28);
    }
    .mobile-search-shell .leaflet-container{
        background:#e7ddca;
        font:inherit;
    }
</style>
@endpush

@section('content')
<div class="msearch-page">
    <section class="msearch-shell pt-4">
        <div class="msearch-hero px-5 py-6">
            <div class="msearch-copy">
                <div class="flex items-center justify-between gap-3">
                    <div class="msearch-kicker">{{ $ui['eyebrow'] }}</div>
                    <a href="{{ $backUrl }}" class="msearch-link">
                        <span>{{ $ui['back'] }}</span>
                    </a>
                </div>

                <h1 class="msearch-title">{{ $ui['title'] }}</h1>
                <p class="msearch-text">{{ $ui['subtitle'] }}</p>

                <div class="mt-5 grid grid-cols-3 gap-3">
                    <div class="msearch-stat">
                        <div class="msearch-stat-label">{{ $isAr ? 'النتائج' : 'Results' }}</div>
                        <div class="msearch-stat-value">{{ number_format((int) ($summary['total_results'] ?? $units->total() ?? 0)) }}</div>
                    </div>
                    <div class="msearch-stat">
                        <div class="msearch-stat-label">{{ $ui['rent'] }}</div>
                        <div class="msearch-stat-value">{{ number_format((int) ($summary['rent_count'] ?? 0)) }}</div>
                    </div>
                    <div class="msearch-stat">
                        <div class="msearch-stat-label">{{ $ui['sale'] }}</div>
                        <div class="msearch-stat-value">{{ number_format((int) ($summary['sale_count'] ?? 0)) }}</div>
                    </div>
                </div>

                <div class="mt-5 rounded-[1.6rem] border border-white/12 bg-white/10 p-4 backdrop-blur-xl">
                    <div class="text-[11px] font-extrabold uppercase tracking-[0.18em] text-white/70">{{ $ui['quickTitle'] }}</div>
                    <p class="mt-2 text-sm leading-7 text-white/80">{{ $ui['quickText'] }}</p>

                    <form action="{{ $searchAction }}" method="GET" class="mt-4 space-y-3" id="mobile-search-form">
                        <input type="hidden" name="listing_type" id="mobile-search-listing-type" value="{{ $filters['listing_type'] ?? '' }}">
                        @if (filled($filters['category_id'] ?? null))
                            <input type="hidden" name="category_id" value="{{ $filters['category_id'] }}">
                        @endif
                        <div class="msearch-toggle">
                            <button type="button" class="{{ ($filters['listing_type'] ?? '') === '' ? 'is-active' : '' }}" data-listing-toggle="">{{ $ui['all'] }}</button>
                            <button type="button" class="{{ ($filters['listing_type'] ?? '') === Unit::LISTING_SALE ? 'is-active' : '' }}" data-listing-toggle="{{ Unit::LISTING_SALE }}">{{ $ui['sale'] }}</button>
                            <button type="button" class="{{ ($filters['listing_type'] ?? '') === Unit::LISTING_RENT ? 'is-active' : '' }}" data-listing-toggle="{{ Unit::LISTING_RENT }}">{{ $ui['rent'] }}</button>
                        </div>

                        <div>
                            <label class="msearch-label" for="mobile-search-q">{{ $ui['keyword'] }}</label>
                            <input id="mobile-search-q" type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="{{ $ui['keyword'] }}" class="msearch-input">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="msearch-label" for="mobile-search-subcategory">{{ $ui['subcategory'] }}</label>
                                <select id="mobile-search-subcategory" name="subcategory_id" class="msearch-select">
                                    <option value="">{{ $ui['allTypes'] }}</option>
                                    @foreach ($subcategories as $subcategory)
                                        <option value="{{ $subcategory->id }}" @selected((string) ($filters['subcategory_id'] ?? '') === (string) $subcategory->id)>{{ $subcategory->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="msearch-label" for="mobile-search-city">{{ $ui['city'] }}</label>
                                <select id="mobile-search-city" name="city_id" class="msearch-select">
                                    <option value="">{{ $ui['allCities'] }}</option>
                                    @foreach ($cities as $city)
                                        @php $cityLabel = $isAr ? ($city->name_ar ?? $city->name_en) : ($city->name_en ?? $city->name_ar); @endphp
                                        <option value="{{ $city->id }}" @selected((string) ($filters['city_id'] ?? '') === (string) $city->id)>{{ $cityLabel }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @if ($context === 'public')
                            <div>
                                <label class="msearch-label" for="mobile-search-tenant">{{ $ui['agency'] }}</label>
                                <select id="mobile-search-tenant" name="tenant_id" class="msearch-select">
                                    <option value="">{{ $ui['allAgencies'] }}</option>
                                    @foreach ($tenants as $tenantOption)
                                        <option value="{{ $tenantOption->id }}" @selected((string) ($filters['tenant_id'] ?? '') === (string) $tenantOption->id)>{{ $tenantOption->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="msearch-label" for="mobile-search-bedrooms">{{ $ui['bedrooms'] }}</label>
                                <select id="mobile-search-bedrooms" name="bedrooms" class="msearch-select">
                                    <option value="">{{ $ui['anyBedrooms'] }}</option>
                                    @foreach ([1, 2, 3, 4, 5] as $bedroom)
                                        <option value="{{ $bedroom }}" @selected((string) ($filters['bedrooms'] ?? '') === (string) $bedroom)>{{ $bedroom }}+</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="msearch-label" for="mobile-search-price-min">{{ $ui['priceMin'] }}</label>
                                <input id="mobile-search-price-min" type="number" name="price_min" value="{{ $filters['price_min'] ?? '' }}" class="msearch-input" min="0" step="1000">
                            </div>
                            <div>
                                <label class="msearch-label" for="mobile-search-price-max">{{ $ui['priceMax'] }}</label>
                                <input id="mobile-search-price-max" type="number" name="price_max" value="{{ $filters['price_max'] ?? '' }}" class="msearch-input" min="0" step="1000">
                            </div>
                        </div>

                        <div>
                            <label class="msearch-label" for="mobile-search-sort">{{ $ui['sort'] }}</label>
                            <select id="mobile-search-sort" name="sort" class="msearch-select">
                                <option value="latest" @selected(($filters['sort'] ?? 'latest') === 'latest')>{{ $ui['latest'] }}</option>
                                <option value="oldest" @selected(($filters['sort'] ?? '') === 'oldest')>{{ $ui['oldest'] }}</option>
                                <option value="price_asc" @selected(($filters['sort'] ?? '') === 'price_asc')>{{ $ui['priceLow'] }}</option>
                                <option value="price_desc" @selected(($filters['sort'] ?? '') === 'price_desc')>{{ $ui['priceHigh'] }}</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <button type="submit" class="msearch-submit">{{ $ui['search'] }}</button>
                            <a href="{{ $clearUrl }}" class="msearch-secondary">{{ $ui['clear'] }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="msearch-shell mt-6">
        <div class="msearch-strip">
            <div class="msearch-chip-card msearch-card">
                <div class="msearch-chip-label">{{ $ui['locationsTitle'] }}</div>
                <div class="msearch-chip-value">{{ $locationClusters->first()['name'] ?? ($isAr ? 'قريباً' : 'Soon') }}</div>
                <div class="mt-2 text-xs leading-6 text-slate-500">{{ ($locationClusters->first()['count'] ?? 0) . ' ' . ($isAr ? 'نتائج' : 'results') }}</div>
            </div>
            <div class="msearch-chip-card msearch-card">
                <div class="msearch-chip-label">{{ $ui['typesTitle'] }}</div>
                <div class="msearch-chip-value">{{ $typeClusters->first()['name'] ?? ($isAr ? 'متنوع' : 'Mixed') }}</div>
                <div class="mt-2 text-xs leading-6 text-slate-500">{{ ($typeClusters->first()['count'] ?? 0) . ' ' . ($isAr ? 'عقارات' : 'listings') }}</div>
            </div>
            <div class="msearch-chip-card msearch-card">
                <div class="msearch-chip-label">{{ $ui['priceTitle'] }}</div>
                <div class="msearch-chip-value">
                    @if (($summary['price_min'] ?? null) !== null || ($summary['price_max'] ?? null) !== null)
                        {{ $priceFormatter($summary['price_min'] ?? 0, $summary['currency'] ?? 'JOD') }}
                    @else
                        {{ $ui['priceOnRequest'] }}
                    @endif
                </div>
                <div class="mt-2 text-xs leading-6 text-slate-500">
                    @if (($summary['price_min'] ?? null) !== null && ($summary['price_max'] ?? null) !== null)
                        {{ $priceFormatter($summary['price_max'], $summary['currency'] ?? 'JOD') }}
                    @else
                        {{ $isAr ? 'يتحدث مع الفلاتر' : 'Updates with filters' }}
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="msearch-shell mt-6" id="mobile-search-map-section">
        <div class="mb-3 flex items-end justify-between gap-3">
            <div>
                <div class="text-[11px] font-extrabold uppercase tracking-[0.18em] text-[color:var(--msearch-brass)]">{{ $ui['openOnMap'] }}</div>
                <h2 class="mt-2 text-2xl font-black tracking-[-0.04em] text-[color:var(--msearch-ink)]">{{ $ui['mapTitle'] }}</h2>
                <p class="mt-2 text-sm leading-7 text-slate-500">{{ $ui['mapText'] }}</p>
            </div>
        </div>
        <div class="msearch-map-card msearch-card">
            <div class="msearch-map-frame">
                <div id="mobile-search-map" class="h-full w-full"></div>
                <div class="msearch-map-empty" id="mobile-search-map-empty" @if(!empty($mapData['markers'])) style="display:none" @endif>
                    <div class="text-base font-black tracking-[-0.03em] text-[color:var(--msearch-ink)]">{{ $ui['noMap'] }}</div>
                    <p class="mt-2 text-sm leading-7 text-slate-500">{{ $ui['noMapText'] }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="msearch-shell mt-6">
        <div class="mb-3">
            <div class="text-[11px] font-extrabold uppercase tracking-[0.18em] text-[color:var(--msearch-brass)]">{{ number_format((int) ($summary['visible_results'] ?? $units->count() ?? 0)) }}</div>
            <h2 class="mt-2 text-2xl font-black tracking-[-0.04em] text-[color:var(--msearch-ink)]">{{ $ui['resultsTitle'] }}</h2>
            <p class="mt-2 text-sm leading-7 text-slate-500">{{ $ui['resultsText'] }}</p>
        </div>

        @if ($units->count())
            <div class="msearch-results" id="mobile-search-results">
                @foreach ($units as $unit)
                    @php
                        $unitTitle = $titleLabel($unit);
                        $unitLocation = $locationLabel($unit);
                        $unitPrice = ($unit->listing_type ?? Unit::LISTING_RENT) === Unit::LISTING_SALE
                            ? $unit->price
                            : (($unit->market_rent && $unit->market_rent > 0) ? $unit->market_rent : $unit->price);
                        $unitHref = route('mobile.units.show', ['unit' => $unit]);
                        $photos = collect($unit->photos ?? [])->filter()->values();
                        $bedrooms = $unit->bedrooms ?? $unit->beds ?? null;
                        $bathrooms = $unit->bathrooms ?? $unit->baths ?? null;
                        $areaValue = $unit->sqft ?? $unit->area_m2 ?? null;
                        $attributeFacts = $unit->relationLoaded('unitAttributes')
                            ? $unit->unitAttributes
                                ->map(function ($attribute) {
                                    $field = $attribute->attributeField;
                                    $formatted = $attribute->formatted_value;

                                    if (! $field || $formatted === null || trim((string) $formatted) === '') {
                                        return null;
                                    }

                                    $group = strtolower((string) $field->group);
                                    $featured = (bool) $field->promoted
                                        || (bool) $field->searchable
                                        || Str::contains($group, ['life', 'amen', 'view', 'finish', 'feature', 'community']);

                                    return [
                                        'label' => $field->translated_label,
                                        'value' => $formatted,
                                        'featured' => $featured,
                                    ];
                                })
                                ->filter()
                                ->sortByDesc(fn ($item) => $item['featured'])
                                ->take(4)
                                ->values()
                            : collect();
                    @endphp
                    <article class="msearch-result-card msearch-card {{ $isAr ? 'text-right' : 'text-left' }}" data-search-card data-unit-code="{{ $unit->code }}">
                        <div class="msearch-gallery">
                            <a href="{{ $unitHref }}" class="absolute inset-0 z-[3]" aria-label="{{ $unitTitle }}"></a>
                            <div class="msearch-gallery-track" data-gallery-track>
                                @forelse ($photos as $photoIndex => $photo)
                                    <div class="msearch-gallery-slide" data-gallery-slide="{{ $photoIndex }}">
                                        <img src="{{ $photo }}" alt="{{ $unitTitle }}" loading="lazy">
                                    </div>
                                @empty
                                    <div class="msearch-gallery-slide">
                                        <div class="flex h-full w-full items-center justify-center bg-[linear-gradient(135deg,rgba(255,249,239,.96),rgba(245,234,206,.88))]">
                                            <div class="rounded-full bg-[rgba(15,90,70,.08)] px-4 py-3 text-sm font-extrabold text-[color:var(--msearch-palm)]">{{ $unit->subcategory?->name ?? __('Property') }}</div>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                            @if ($photos->count() > 1)
                                <button type="button" class="msearch-gallery-btn prev" data-gallery-prev aria-label="{{ $isAr ? 'الصورة السابقة' : 'Previous photo' }}">
                                    <svg class="h-4 w-4 {{ $isAr ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                </button>
                                <button type="button" class="msearch-gallery-btn next" data-gallery-next aria-label="{{ $isAr ? 'الصورة التالية' : 'Next photo' }}">
                                    <svg class="h-4 w-4 {{ $isAr ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                                <div class="msearch-gallery-dots">
                                    @foreach ($photos as $photoIndex => $photo)
                                        <button type="button" class="msearch-gallery-dot{{ $photoIndex === 0 ? ' is-active' : '' }}" data-gallery-dot="{{ $photoIndex }}" aria-label="{{ ($isAr ? 'الصورة' : 'Photo') . ' ' . ($photoIndex + 1) }}"></button>
                                    @endforeach
                                </div>
                            @endif

                            <div class="msearch-result-topbar {{ $isAr ? 'flex-row-reverse' : '' }}">
                                <span class="msearch-badge {{ ($unit->listing_type ?? Unit::LISTING_RENT) === Unit::LISTING_SALE ? 'sale' : 'rent' }}">
                                    {{ ($unit->listing_type ?? Unit::LISTING_RENT) === Unit::LISTING_SALE ? $ui['sale'] : $ui['rent'] }}
                                </span>
                                @if ($photos->count() > 1)
                                    <span class="msearch-badge soft">{{ $photos->count() }} {{ $ui['photos'] }}</span>
                                @endif
                            </div>

                            <div class="msearch-result-bottombar {{ $isAr ? 'flex-row-reverse' : '' }}">
                                <span class="msearch-price">
                                    {{ $unitPrice ? $priceFormatter($unitPrice, $unit->currency ?? 'JOD') : $ui['priceOnRequest'] }}
                                    @if (($unit->listing_type ?? Unit::LISTING_RENT) === Unit::LISTING_RENT && $unitPrice)
                                        <span class="ml-1 text-[10px] font-extrabold uppercase tracking-[0.12em] text-slate-500">{{ $ui['perYear'] }}</span>
                                    @endif
                                </span>
                                <span class="msearch-code">{{ $unit->code }}</span>
                            </div>
                        </div>

                        <div class="msearch-body">
                            <div class="text-[11px] font-extrabold uppercase tracking-[0.16em] text-[color:var(--msearch-brass)]">{{ $unit->subcategory?->name ?? __('Property') }}</div>
                            <h3 class="msearch-card-title">{{ $unitTitle }}</h3>
                            <p class="msearch-card-subtitle">{{ $unitLocation }}</p>
                            <p class="msearch-card-subtitle">{{ Str::limit(strip_tags((string) ($unit->translated_description ?: $unit->description)), 120) }}</p>

                            <div class="msearch-facts">
                                @if ($bedrooms)
                                    <span class="msearch-pill palm">{{ number_format((float) $bedrooms) }} {{ $ui['beds'] }}</span>
                                @endif
                                @if ($bathrooms)
                                    <span class="msearch-pill brass">{{ number_format((float) $bathrooms) }} {{ $ui['baths'] }}</span>
                                @endif
                                @if ($areaValue)
                                    <span class="msearch-pill clay">{{ number_format((float) $areaValue) }} {{ $ui['sqft'] }}</span>
                                @endif
                                @foreach ($attributeFacts as $attributeFact)
                                    <span class="msearch-pill {{ $attributeFact['featured'] ? 'palm' : 'soft' }}">{{ $attributeFact['label'] }}: {{ $attributeFact['value'] }}</span>
                                @endforeach
                            </div>

                            <div class="msearch-card-footer {{ $isAr ? 'flex-row-reverse' : '' }}">
                                <div class="text-xs font-bold text-slate-500">
                                    @if ($context === 'public')
                                        {{ $unit->tenant?->name ?? config('app.name') }}
                                    @else
                                        {{ $tenant?->name ?? config('app.name') }}
                                    @endif
                                </div>
                                <a href="{{ $unitHref }}" class="msearch-link-inline">
                                    <span>{{ $ui['viewProperty'] }}</span>
                                    <svg class="h-3.5 w-3.5 {{ $isAr ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $units->links() }}
            </div>
        @else
            <div class="msearch-empty">
                <div class="text-lg font-black tracking-[-0.03em] text-[color:var(--msearch-ink)]">{{ $ui['noResults'] }}</div>
                <p class="mt-2 text-sm leading-7 text-slate-500">{{ $ui['noResultsText'] }}</p>
            </div>
        @endif
    </section>
</div>
@endsection

@push('scripts')
<script>
    window.addEventListener('load', () => {
        const isRtl = @json($isAr);
        const markers = @json($mapData['markers'] ?? []);
        const center = @json($mapData['center'] ?? ['lat' => 31.9539494, 'lng' => 35.9106350, 'zoom' => 7]);
        const priceOnRequestLabel = @json($ui['priceOnRequest']);
        const propertyPageLabel = @json($ui['viewProperty']);
        const approximateNoteLabel = @json($isAr ? 'الموقع تقريبي بحسب المنطقة المتاحة.' : 'Pin is approximate based on the available area.');
        const toggleInput = document.getElementById('mobile-search-listing-type');
        const toggleButtons = Array.from(document.querySelectorAll('[data-listing-toggle]'));
        const resultCards = Array.from(document.querySelectorAll('[data-search-card]'));
        const cardsByCode = new Map();

        toggleButtons.forEach((button) => {
            button.addEventListener('click', () => {
                toggleButtons.forEach((item) => item.classList.remove('is-active'));
                button.classList.add('is-active');
                if (toggleInput) {
                    toggleInput.value = button.dataset.listingToggle ?? '';
                }
            });
        });

        const initCardGallery = (card) => {
            const track = card.querySelector('[data-gallery-track]');
            if (!track) {
                return;
            }

            const slides = Array.from(track.querySelectorAll('[data-gallery-slide]'));
            const prevButton = card.querySelector('[data-gallery-prev]');
            const nextButton = card.querySelector('[data-gallery-next]');
            const dots = Array.from(card.querySelectorAll('[data-gallery-dot]'));
            let currentIndex = 0;

            const renderGallery = () => {
                track.style.transform = `translate3d(${currentIndex * -100}%, 0, 0)`;
                dots.forEach((dot, dotIndex) => dot.classList.toggle('is-active', dotIndex === currentIndex));
            };

            const moveGallery = (direction) => {
                currentIndex = (currentIndex + direction + slides.length) % slides.length;
                renderGallery();
            };

            [prevButton, nextButton, ...dots].forEach((control) => {
                control?.addEventListener('click', (event) => {
                    event.preventDefault();
                    event.stopPropagation();
                });
            });

            prevButton?.addEventListener('click', () => moveGallery(-1));
            nextButton?.addEventListener('click', () => moveGallery(1));
            dots.forEach((dot, dotIndex) => {
                dot.addEventListener('click', () => {
                    currentIndex = dotIndex;
                    renderGallery();
                });
            });

            renderGallery();
        };

        resultCards.forEach((card) => {
            const code = card.dataset.unitCode;
            if (code) {
                cardsByCode.set(code, card);
            }

            initCardGallery(card);
        });

        if (!window.L) {
            return;
        }

        const mapElement = document.getElementById('mobile-search-map');
        const mapEmpty = document.getElementById('mobile-search-map-empty');
        if (!mapElement || !markers.length) {
            return;
        }

        mapEmpty?.style.setProperty('display', 'none');

        const map = L.map(mapElement, {
            zoomControl: false,
            scrollWheelZoom: true,
        }).setView([center.lat, center.lng], center.zoom || 7);

        L.control.zoom({ position: isRtl ? 'topleft' : 'topright' }).addTo(map);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors',
        }).addTo(map);

        const bounds = [];
        const markersByCode = new Map();
        let lockedCode = null;

        const markerStyles = (marker, active = false) => {
            const sale = marker.listing_type === 'sale';

            if (active) {
                return {
                    radius: marker.approximate ? 12 : 11,
                    weight: 4,
                    color: '#fff7ea',
                    fillColor: sale ? '#d09c39' : '#12745a',
                    fillOpacity: 1,
                    dashArray: null,
                };
            }

            return {
                radius: marker.approximate ? 9 : 8,
                weight: marker.approximate ? 2 : 3,
                color: '#fff7ea',
                fillColor: sale ? '#b6842f' : '#0f5a46',
                fillOpacity: marker.approximate ? 0.76 : 0.92,
                dashArray: marker.approximate ? '6 4' : null,
            };
        };

        const syncActiveState = (activeCode = null) => {
            cardsByCode.forEach((card, code) => {
                card.classList.toggle('is-active', code === activeCode);
            });

            markersByCode.forEach(({ markerLayer, markerData }, code) => {
                markerLayer.setStyle(markerStyles(markerData, code === activeCode));
                if (code === activeCode) {
                    markerLayer.bringToFront();
                }
            });
        };

        const focusResult = (code, options = {}) => {
            const { pan = false, lock = false, scroll = false } = options;
            if (!code) {
                syncActiveState(lockedCode);
                return;
            }

            if (lock) {
                lockedCode = code;
            }

            syncActiveState(code);
            const entry = markersByCode.get(code);
            if (!entry) {
                return;
            }

            if (pan) {
                map.panTo(entry.markerLayer.getLatLng(), { animate: true, duration: 0.45 });
            }

            if (scroll) {
                cardsByCode.get(code)?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        };

        const releaseHoverState = () => {
            if (lockedCode) {
                syncActiveState(lockedCode);
                return;
            }

            syncActiveState(null);
        };

        markers.forEach((marker) => {
            const mapMarker = L.circleMarker([marker.lat, marker.lng], markerStyles(marker)).addTo(map);
            const price = marker.price
                ? `${marker.currency} ${new Intl.NumberFormat().format(marker.price)}`
                : priceOnRequestLabel;
            const modeNote = marker.approximate ? `<div style="margin-top:.45rem;font-size:.75rem;line-height:1.6;color:#64716a">${approximateNoteLabel}</div>` : '';
            const photo = marker.photo ? `<div style="overflow:hidden;border-radius:1rem;height:7rem;background:#ede3d4"><img src="${marker.photo}" alt="${marker.title}" style="height:100%;width:100%;object-fit:cover"></div>` : '';

            mapMarker.bindPopup(`
                <div dir="${isRtl ? 'rtl' : 'ltr'}" style="min-width:190px;font-family:inherit">
                    ${photo}
                    <div style="padding:${photo ? '.8rem .1rem 0' : '.1rem'}">
                        <div style="font-size:.95rem;line-height:1.35;font-weight:800;color:#1f2a24">${marker.title}</div>
                        <div style="margin-top:.35rem;font-size:.78rem;line-height:1.6;color:#64716a">${marker.type} · ${marker.location}</div>
                        <div style="margin-top:.35rem;font-size:.78rem;line-height:1.6;color:#64716a">${price}</div>
                        ${modeNote}
                        <a href="${marker.href}" style="margin-top:.7rem;display:inline-flex;align-items:center;gap:.35rem;font-size:.72rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:#0f5a46;text-decoration:none">${propertyPageLabel}</a>
                    </div>
                </div>
            `);

            markersByCode.set(marker.code, {
                markerLayer: mapMarker,
                markerData: marker,
            });

            mapMarker.on('mouseover', () => {
                if (lockedCode === marker.code) {
                    return;
                }

                focusResult(marker.code);
            });

            mapMarker.on('mouseout', () => {
                if (lockedCode === marker.code) {
                    return;
                }

                releaseHoverState();
            });

            mapMarker.on('click', () => {
                focusResult(marker.code, { pan: true, lock: true, scroll: true });
            });

            mapMarker.on('popupopen', () => {
                lockedCode = marker.code;
                syncActiveState(marker.code);
            });

            mapMarker.on('popupclose', () => {
                lockedCode = null;
                releaseHoverState();
            });

            bounds.push([marker.lat, marker.lng]);
        });

        resultCards.forEach((card) => {
            const code = card.dataset.unitCode;
            if (!code || !markersByCode.has(code)) {
                return;
            }

            card.addEventListener('mouseenter', () => {
                if (lockedCode === code) {
                    return;
                }

                focusResult(code, { pan: true });
            });

            card.addEventListener('mouseleave', () => {
                if (lockedCode === code) {
                    return;
                }

                releaseHoverState();
            });

            card.addEventListener('focusin', () => focusResult(code));
            card.addEventListener('focusout', (event) => {
                if (card.contains(event.relatedTarget) || lockedCode === code) {
                    return;
                }

                releaseHoverState();
            });
        });

        if (bounds.length > 1) {
            map.fitBounds(bounds, { padding: [28, 28] });
        } else if (bounds.length === 1) {
            map.setView(bounds[0], center.zoom || 12);
        }
    });
</script>
@endpush
