@extends('mobile.layouts.app', [
    'title' => app()->getLocale() === 'ar' ? 'عقاري سمارت' : 'Aqari Smart',
    'subtitle' => '',
    'show_back_button' => false,
    'body_class' => 'mobile-marketplace-shell',
])

@section('full_width', true)

@push('head')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root{
        --market-ink:#1f2a24;
        --market-palm:#0f5a46;
        --market-river:#2f7a72;
        --market-brass:#b6842f;
        --market-clay:#9d5a3b;
        --market-sand:#efe4cf;
        --market-cream:#fbf7ef;
        --market-line:rgba(130,94,38,.16);
        --market-soft:rgba(255,252,246,.94);
        --market-soft-strong:rgba(255,248,235,.98);
    }
    body.mobile-marketplace-shell{
        background:
            radial-gradient(circle at top left, rgba(182,132,47,.14), transparent 22%),
            radial-gradient(circle at top right, rgba(15,90,70,.1), transparent 24%),
            linear-gradient(180deg, #eee2cc 0, #f7efdf 300px, #fbf7ef 100%);
        color:var(--market-ink);
        font-family:'Manrope',system-ui,sans-serif;
    }
    html[dir="rtl"] body.mobile-marketplace-shell{
        font-family:'Cairo','Manrope',system-ui,sans-serif;
    }
    body.mobile-marketplace-shell aside{
        background:rgba(252,248,241,.98);
        color:var(--market-ink);
    }
    body.mobile-marketplace-shell aside .bg-gradient-to-br.from-emerald-600.to-emerald-700{
        background:linear-gradient(145deg, rgba(15,32,26,.96), rgba(15,90,70,.9) 56%, rgba(48,33,15,.86)) !important;
    }
    body.mobile-marketplace-shell header.sticky{
        background:linear-gradient(145deg, rgba(15,32,26,.96), rgba(15,90,70,.9) 56%, rgba(48,33,15,.86));
        box-shadow:0 16px 36px -24px rgba(28,22,10,.55);
    }
    body.mobile-marketplace-shell .mobile-market-page{
        min-height:100vh;
    }
    .mm-shell{padding-inline:1rem}
    .mm-card{
        border:1px solid var(--market-line);
        background:var(--market-soft);
        box-shadow:0 24px 54px -36px rgba(57,42,16,.34);
    }
    .mm-surface{
        background:linear-gradient(180deg, rgba(255,249,239,.98), rgba(247,237,214,.9));
        border:1px solid rgba(182,132,47,.22);
        box-shadow:0 24px 54px -36px rgba(57,42,16,.28);
    }
    .mm-ornament{
        height:10px;
        width:104px;
        border-radius:999px;
        background:
            linear-gradient(90deg, rgba(15,90,70,.16), rgba(182,132,47,.32), rgba(15,90,70,.16)),
            repeating-linear-gradient(90deg, transparent 0 10px, rgba(182,132,47,.56) 10px 14px, transparent 14px 24px);
    }
    .mm-hero{
        position:relative;
        overflow:hidden;
        border-radius:2rem;
        background:
            radial-gradient(circle at top left, rgba(255,255,255,.14), transparent 28%),
            linear-gradient(145deg, rgba(15,32,26,.96), rgba(15,90,70,.88) 54%, rgba(48,33,15,.82));
        color:#fff8ea;
        box-shadow:0 30px 64px -34px rgba(28,22,10,.58);
    }
    .mm-hero::after{
        content:"";
        position:absolute;
        inset:0;
        background:
            linear-gradient(180deg, rgba(10,16,13,.04), rgba(10,16,13,.22)),
            radial-gradient(circle at 85% 14%, rgba(255,255,255,.08), transparent 24%);
        pointer-events:none;
    }
    .mm-hero-copy,.mm-section-copy{
        position:relative;
        z-index:1;
    }
    .mm-kicker{
        font-size:.7rem;
        font-weight:800;
        letter-spacing:.22em;
        text-transform:uppercase;
        color:rgba(255,241,212,.74);
    }
    .mm-chip{
        display:inline-flex;
        align-items:center;
        gap:.55rem;
        border-radius:999px;
        border:1px solid rgba(255,255,255,.14);
        background:rgba(255,255,255,.08);
        padding:.68rem .95rem;
        font-size:.68rem;
        font-weight:800;
        letter-spacing:.14em;
        text-transform:uppercase;
        color:rgba(255,248,236,.88);
        backdrop-filter:blur(12px);
    }
    .mm-chip::before{
        content:"";
        width:.45rem;
        height:.45rem;
        border-radius:999px;
        background:var(--market-brass);
        box-shadow:0 0 0 4px rgba(182,132,47,.16);
    }
    .mm-stat{
        border:1px solid rgba(255,255,255,.12);
        background:rgba(255,255,255,.08);
        border-radius:1.35rem;
        padding:.9rem .95rem;
        backdrop-filter:blur(14px);
    }
    .mm-stat-label{
        font-size:.65rem;
        letter-spacing:.18em;
        text-transform:uppercase;
        color:rgba(255,244,221,.62);
        font-weight:700;
    }
    .mm-stat-value{
        margin-top:.35rem;
        font-size:1rem;
        line-height:1.2;
        font-weight:800;
        color:#fff8ea;
    }
    .mm-filter-shell{
        border:1px solid rgba(182,132,47,.22);
        background:rgba(255,248,235,.97);
        box-shadow:0 26px 54px -34px rgba(19,24,20,.44);
    }
    .mm-toggle{
        display:flex;
        gap:.35rem;
        border-radius:1.2rem;
        background:rgba(15,90,70,.08);
        padding:.3rem;
    }
    .mm-toggle button{
        flex:1;
        border-radius:1rem;
        padding:.8rem .75rem;
        font-size:.82rem;
        font-weight:800;
        color:#6d7069;
        transition:all .18s ease;
    }
    .mm-toggle button.is-active{
        background:linear-gradient(135deg, var(--market-palm), var(--market-brass));
        color:#fff;
        box-shadow:0 16px 26px -18px rgba(15,90,70,.8);
    }
    .mm-input{
        border:1px solid rgba(130,94,38,.16);
        background:rgba(255,255,255,.82);
        color:var(--market-ink);
        transition:border-color .2s ease, box-shadow .2s ease;
    }
    .mm-input:focus{
        outline:none;
        border-color:rgba(182,132,47,.72);
        box-shadow:0 0 0 4px rgba(182,132,47,.12);
    }
    .mm-search-submit{
        background:linear-gradient(135deg, var(--market-palm), var(--market-brass));
        color:#fff;
        box-shadow:0 18px 34px -18px rgba(15,90,70,.8);
    }
    .mm-anchor{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        border-radius:999px;
        border:1px solid rgba(255,255,255,.14);
        background:rgba(255,255,255,.08);
        padding:.7rem .9rem;
        font-size:.72rem;
        font-weight:800;
        letter-spacing:.12em;
        text-transform:uppercase;
        color:#fff8ea;
        text-decoration:none;
        backdrop-filter:blur(10px);
    }
    .mm-section-head{
        display:flex;
        align-items:end;
        justify-content:space-between;
        gap:1rem;
        margin-bottom:1rem;
    }
    .mm-section-kicker{
        font-size:.68rem;
        font-weight:800;
        letter-spacing:.2em;
        text-transform:uppercase;
        color:var(--market-brass);
    }
    .mm-section-title{
        margin-top:.35rem;
        font-size:1.5rem;
        line-height:1.1;
        font-weight:900;
        letter-spacing:-.04em;
        color:var(--market-ink);
    }
    .mm-section-link{
        font-size:.74rem;
        font-weight:800;
        letter-spacing:.14em;
        text-transform:uppercase;
        color:var(--market-palm);
        text-decoration:none;
    }
    .mm-scroll{
        display:flex;
        gap:.85rem;
        overflow-x:auto;
        padding-bottom:.25rem;
        scroll-snap-type:x mandatory;
        -ms-overflow-style:none;
        scrollbar-width:none;
    }
    .mm-scroll::-webkit-scrollbar{display:none}
    .mm-category-card,.mm-agency-card,.mm-city-card,.mm-unit-card{
        border:1px solid var(--market-line);
        background:rgba(255,252,246,.96);
        box-shadow:0 22px 46px -34px rgba(55,38,12,.36);
    }
    .mm-category-card{
        min-width:112px;
        border-radius:1.4rem;
        padding:1rem .8rem;
        text-align:center;
    }
    .mm-category-card.is-active{
        background:linear-gradient(145deg, rgba(15,90,70,.92), rgba(48,122,114,.84));
        color:#fff;
        border-color:transparent;
    }
    .mm-category-icon{
        display:flex;
        height:3.35rem;
        width:3.35rem;
        align-items:center;
        justify-content:center;
        margin:0 auto .7rem;
        border-radius:999px;
        background:rgba(15,90,70,.08);
        color:var(--market-palm);
    }
    .mm-category-card.is-active .mm-category-icon{
        background:rgba(255,255,255,.14);
        color:#fff;
    }
    .mm-agency-card{
        min-width:258px;
        border-radius:1.6rem;
        padding:1rem;
        text-decoration:none;
        color:inherit;
        scroll-snap-align:start;
    }
    .mm-agency-logo{
        height:3.1rem;
        width:3.1rem;
        overflow:hidden;
        border-radius:1rem;
        border:1px solid rgba(130,94,38,.14);
        background:rgba(255,255,255,.8);
    }
    .mm-agency-stat{
        border-top:1px solid rgba(130,94,38,.12);
        margin-top:.85rem;
        padding-top:.85rem;
        display:flex;
        gap:1rem;
        align-items:center;
    }
    .mm-agency-stat strong{
        display:block;
        font-size:1rem;
        font-weight:900;
        color:var(--market-ink);
    }
    .mm-agency-stat span{
        font-size:.68rem;
        font-weight:800;
        letter-spacing:.12em;
        text-transform:uppercase;
        color:#81857d;
    }
    .mm-city-grid{
        display:grid;
        grid-template-columns:repeat(2, minmax(0, 1fr));
        gap:.8rem;
    }
    .mm-city-card{
        position:relative;
        overflow:hidden;
        border-radius:1.45rem;
        text-decoration:none;
        color:#fff;
        min-height:148px;
    }
    .mm-city-card img{
        position:absolute;
        inset:0;
        width:100%;
        height:100%;
        object-fit:cover;
    }
    .mm-city-card::after{
        content:"";
        position:absolute;
        inset:0;
        background:linear-gradient(180deg, rgba(10,14,12,.04), rgba(10,14,12,.72));
    }
    .mm-city-copy{
        position:relative;
        z-index:1;
        display:flex;
        height:100%;
        flex-direction:column;
        justify-content:flex-end;
        padding:1rem;
    }
    .mm-slider{
        position:relative;
        overflow:hidden;
        border-radius:1.35rem 1.35rem 0 0;
        background:#ece4d5;
    }
    .mm-slider-track{
        display:flex;
        overflow-x:auto;
        scroll-snap-type:x mandatory;
        -ms-overflow-style:none;
        scrollbar-width:none;
    }
    .mm-slider-track::-webkit-scrollbar{display:none}
    .mm-slide{
        width:100%;
        flex:0 0 100%;
        scroll-snap-align:start;
    }
    .mm-slider-btn{
        position:absolute;
        top:50%;
        z-index:6;
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
    .mm-slider-btn.prev{left:.65rem}
    .mm-slider-btn.next{right:.65rem}
    .mm-slider-dots{
        position:absolute;
        left:50%;
        bottom:.7rem;
        z-index:6;
        display:flex;
        gap:.3rem;
        transform:translateX(-50%);
        border-radius:999px;
        background:rgba(0,0,0,.35);
        padding:.35rem .5rem;
        backdrop-filter:blur(8px);
    }
    .mm-slider-dot{
        height:.35rem;
        width:.35rem;
        border-radius:999px;
        background:rgba(255,255,255,.42);
    }
    .mm-slider-dot.is-active{background:#fff}
    .mm-unit-card{
        overflow:hidden;
        border-radius:1.6rem;
        text-decoration:none;
        color:inherit;
    }
    .mm-unit-card.compact{
        min-width:280px;
        scroll-snap-align:start;
    }
    .mm-unit-card-body{padding:1rem}
    .mm-unit-badge{
        display:inline-flex;
        align-items:center;
        border-radius:999px;
        padding:.45rem .7rem;
        font-size:.62rem;
        font-weight:800;
        letter-spacing:.14em;
        text-transform:uppercase;
        color:#fff;
        backdrop-filter:blur(10px);
    }
    .mm-unit-badge.sale{background:rgba(182,132,47,.88)}
    .mm-unit-badge.rent{background:rgba(15,90,70,.88)}
    .mm-price-pill{
        display:inline-flex;
        align-items:center;
        border-radius:999px;
        background:rgba(255,249,239,.94);
        padding:.5rem .78rem;
        font-size:.82rem;
        font-weight:900;
        color:var(--market-ink);
        box-shadow:0 12px 24px -20px rgba(0,0,0,.42);
    }
    .mm-unit-meta{
        display:flex;
        flex-wrap:wrap;
        gap:.45rem;
        margin-top:.75rem;
    }
    .mm-pill{
        border-radius:999px;
        padding:.38rem .7rem;
        font-size:.62rem;
        font-weight:800;
        letter-spacing:.12em;
        text-transform:uppercase;
    }
    .mm-pill.palm{background:rgba(15,90,70,.08); color:var(--market-palm)}
    .mm-pill.brass{background:rgba(182,132,47,.12); color:var(--market-brass)}
    .mm-pill.clay{background:rgba(157,90,59,.08); color:var(--market-clay)}
    .mm-map-card{
        overflow:hidden;
        border-radius:1.6rem;
        border:1px solid rgba(130,94,38,.14);
        background:linear-gradient(145deg, rgba(248,240,221,.96), rgba(244,229,199,.92));
        box-shadow:0 24px 54px -38px rgba(57,42,16,.3);
    }
    .mm-map-note{
        border:1px solid rgba(130,94,38,.12);
        background:rgba(255,252,246,.9);
        border-radius:1.35rem;
        padding:.95rem 1rem;
    }
    .mm-feed{
        display:grid;
        gap:1rem;
    }
    .mm-empty{
        border:1px dashed rgba(130,94,38,.22);
        background:rgba(255,252,246,.82);
        border-radius:1.5rem;
        padding:2rem 1.2rem;
        text-align:center;
    }
    .mm-loading{
        display:flex;
        align-items:center;
        justify-content:center;
        gap:.65rem;
        border-radius:1.5rem;
        background:rgba(255,252,246,.86);
        padding:1.4rem 1rem;
        border:1px solid rgba(130,94,38,.12);
    }
    .mm-spinner{
        height:1.1rem;
        width:1.1rem;
        border-radius:999px;
        border:2px solid rgba(15,90,70,.2);
        border-top-color:var(--market-palm);
        animation:mm-spin .9s linear infinite;
    }
    .mm-map-popup{
        min-width:180px;
        font-family:inherit;
    }
    .mm-map-popup-title{
        font-size:.9rem;
        line-height:1.3;
        font-weight:800;
        color:var(--market-ink);
    }
    .mm-map-popup-meta{
        margin-top:.3rem;
        font-size:.75rem;
        line-height:1.5;
        color:#63675f;
    }
    .mm-map-popup-link{
        margin-top:.55rem;
        display:inline-flex;
        align-items:center;
        gap:.35rem;
        font-size:.72rem;
        font-weight:800;
        letter-spacing:.12em;
        text-transform:uppercase;
        color:var(--market-palm);
        text-decoration:none;
    }
    .mobile-marketplace-shell .leaflet-container{
        background:#e7ddca;
        font:inherit;
    }
    @keyframes mm-spin{to{transform:rotate(360deg)}}
</style>
@endpush

@section('content')
    @php
        $locale = app()->getLocale() === 'ar' ? 'ar' : 'en';
        $isAr = $locale === 'ar';
        $mobileTestData = config('mobiletestdata');
        $hero = $mobileTestData['hero'] ?? [];

        $t = function ($value, $fallback = null) use ($locale) {
            if (is_array($value)) {
                return $value[$locale] ?? $value['en'] ?? $fallback;
            }

            return $value ?? $fallback;
        };

        $tx = [
            'heroEyebrow' => $isAr ? 'سوق عقاري معاصر' : 'A Modern Property Market',
            'heroTitle' => $t($hero['title'] ?? null, $isAr ? 'من السوق إلى بيتك القادم' : 'From the marketplace to your next place'),
            'heroSubtitle' => $t($hero['subtitle'] ?? null, $isAr ? 'واجهة دافئة تعرض العقارات والوكالات والمواقع بطريقة أوضح وأقرب لرحلة المستخدم على الموبايل.' : 'A warmer mobile storefront for listings, agencies, and locations with a clearer, more guided property journey.'),
            'heroSearch' => $t($hero['search_placeholder'] ?? null, $isAr ? 'ابحث باسم العقار أو المدينة أو الوكالة' : 'Search property, city, or agency'),
            'heroHint' => $isAr ? 'ابدأ بالبحث، ثم تحرك بين الوكالات والعقارات والخريطة من نفس الصفحة.' : 'Start with a search, then move across agencies, listings, and the map from one consistent page.',
            'all' => $isAr ? 'الكل' : 'All',
            'buy' => $isAr ? 'شراء' : 'Buy',
            'rent' => $isAr ? 'إيجار' : 'Rent',
            'resultsStat' => $isAr ? 'العروض' : 'Listings',
            'agenciesStat' => $isAr ? 'الوكالات' : 'Agencies',
            'citiesStat' => $isAr ? 'المدن' : 'Cities',
            'recommendedKicker' => $isAr ? 'مختاراتنا' : 'Curated Picks',
            'recommendedTitle' => $isAr ? 'موصى به لك' : 'Recommended for you',
            'categoriesKicker' => $isAr ? 'الأنواع' : 'Property Types',
            'categoriesTitle' => $isAr ? 'تصفح حسب الفئة' : 'Browse by category',
            'agenciesKicker' => $isAr ? 'الوكالات' : 'Agencies',
            'agenciesTitle' => $isAr ? 'أبرز الوكالات' : 'Top agencies',
            'citiesKicker' => $isAr ? 'المناطق' : 'Popular Areas',
            'citiesTitle' => $isAr ? 'استكشف حسب المدينة' : 'Explore by city',
            'mapKicker' => $isAr ? 'الخريطة' : 'Map',
            'mapTitle' => $isAr ? 'اقرأ السوق على الخريطة' : 'Read the market on a map',
            'mapText' => $isAr ? 'العقارات التي تملك إحداثيات تظهر هنا لقراءة أسرع للمواقع النشطة.' : 'Listings with saved coordinates appear here so visitors can read active areas faster.',
            'mapHint' => $isAr ? 'اضغط على العلامة للانتقال إلى صفحة العقار.' : 'Tap a marker to jump into the property page.',
            'feedKicker' => $isAr ? 'النتائج' : 'Results',
            'feedTitle' => $isAr ? 'نتائج البحث' : 'Search results',
            'viewAll' => $isAr ? 'عرض الكل' : 'View all',
            'jumpResults' => $isAr ? 'النتائج' : 'Results',
            'jumpAgencies' => $isAr ? 'الوكالات' : 'Agencies',
            'jumpMap' => $isAr ? 'الخريطة' : 'Map',
            'searchAction' => $isAr ? 'استكشف' : 'Explore',
        ];
    @endphp

    <div class="mobile-market-page pb-12">
        <section class="mm-shell px-4 pb-2 pt-4">
            <div class="mm-hero px-5 py-6">
                <div class="mm-hero-copy">
                    <div class="mm-ornament"></div>
                    <p class="mm-kicker mt-5">{{ $tx['heroEyebrow'] }}</p>
                    <h1 class="mt-3 text-[2rem] font-black leading-[1.02] tracking-[-0.05em] text-[#fff8ea]">{{ $tx['heroTitle'] }}</h1>
                    <p class="mt-3 max-w-xl text-sm leading-7 text-white/78">{{ $tx['heroSubtitle'] }}</p>

                    <div class="mt-5 flex flex-wrap gap-2 {{ $isAr ? 'justify-end' : '' }}">
                        <a href="#feed-section" class="mm-anchor">{{ $tx['jumpResults'] }}</a>
                        <a href="#agencies-section" class="mm-anchor">{{ $tx['jumpAgencies'] }}</a>
                        <a href="#map-section" class="mm-anchor">{{ $tx['jumpMap'] }}</a>
                    </div>

                    <div class="mt-5 grid grid-cols-3 gap-2.5">
                        <div class="mm-stat">
                            <div class="mm-stat-label">{{ $tx['resultsStat'] }}</div>
                            <div class="mm-stat-value" id="mm-stat-results">--</div>
                        </div>
                        <div class="mm-stat">
                            <div class="mm-stat-label">{{ $tx['agenciesStat'] }}</div>
                            <div class="mm-stat-value" id="mm-stat-agencies">--</div>
                        </div>
                        <div class="mm-stat">
                            <div class="mm-stat-label">{{ $tx['citiesStat'] }}</div>
                            <div class="mm-stat-value" id="mm-stat-cities">--</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mm-surface mm-filter-shell -mt-8 rounded-[1.8rem] p-4">
                <form id="mobile-marketplace-filter" class="space-y-3" action="{{ route('mobile.search') }}" method="GET">
                    <div id="listing-type-toggle" class="mm-toggle">
                        <button type="button" class="listing-toggle is-active" data-value="">{{ $tx['all'] }}</button>
                        <button type="button" class="listing-toggle" data-value="sale">{{ $tx['buy'] }}</button>
                        <button type="button" class="listing-toggle" data-value="rent">{{ $tx['rent'] }}</button>
                    </div>
                    <input type="hidden" name="listing_type" id="listing_type_input" value="">
                    <input type="hidden" name="category_id" id="filter_category_id" value="">
                    <input type="hidden" name="city_id" id="filter_city_id" value="">

                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 rtl:left-auto rtl:right-0 rtl:pr-3.5">
                                <svg class="h-4.5 w-4.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input name="q" placeholder="{{ $tx['heroSearch'] }}" class="mm-input h-12 w-full rounded-2xl py-3 pl-10 pr-4 rtl:pl-4 rtl:pr-10 text-sm font-medium placeholder:text-slate-400">
                        </div>
                        <button type="submit" class="mm-search-submit inline-flex h-12 items-center justify-center rounded-2xl px-4 text-sm font-extrabold tracking-[0.14em] uppercase">
                            {{ $tx['searchAction'] }}
                        </button>
                    </div>
                </form>
                <p class="mt-3 text-xs leading-6 text-slate-500">{{ $tx['heroHint'] }}</p>
            </div>
        </section>

        <section class="mm-shell mt-7 px-4">
            <div class="mm-section-head">
                <div class="mm-section-copy">
                    <div class="mm-section-kicker">{{ $tx['recommendedKicker'] }}</div>
                    <h2 class="mm-section-title">{{ $tx['recommendedTitle'] }}</h2>
                </div>
                <a href="{{ route('mobile.search') }}" class="mm-section-link" id="mm-full-search-link">{{ $tx['viewAll'] }}</a>
            </div>
            <div id="mp-recommended" class="mm-scroll">
                <div class="mm-loading w-full">
                    <span class="mm-spinner"></span>
                    <span class="text-sm font-semibold text-slate-500">{{ $isAr ? 'جاري التحميل...' : 'Loading...' }}</span>
                </div>
            </div>
        </section>

        <!-- Direct from Owner Premium Section -->
        <section class="mt-7 px-4 mm-shell" id="direct-owner-section">
            <!-- Section header with gradient badge -->
            <div class="mm-dfo-header rounded-2xl overflow-hidden mb-4" style="background:linear-gradient(135deg,#065f46 0%,#0f5a46 40%,#1d4ed8 100%);padding:1.25rem 1.25rem 1rem;">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="inline-flex items-center gap-1.5 bg-white/20 backdrop-blur text-white text-xs font-bold px-3 py-1 rounded-full">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg>
                                {{ $isAr ? 'مالك مباشر' : 'Direct Owner' }}
                            </span>
                        </div>
                        <h2 class="text-[1.5rem] font-black leading-tight text-white mb-1">{{ $isAr ? 'من المالك مباشرةً' : 'Straight from the Owner' }}</h2>
                        <p class="text-sm text-white/80 leading-relaxed">{{ $isAr ? 'تواصل مباشرة مع المالك — بلا وسيط، بلا عمولة' : 'Talk directly to the owner — no middleman, no fees' }}</p>
                    </div>
                    <div class="ml-3 flex-shrink-0">
                        <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resident Listings Scroll -->
            <div id="mp-resident-listings" class="mm-scroll">
                <div class="mm-loading w-full">
                    <span class="mm-spinner"></span>
                    <span class="text-sm font-semibold text-slate-500">{{ $isAr ? 'جاري التحميل...' : 'Loading...' }}</span>
                </div>
            </div>
            
            <!-- Empty State -->
            <div id="mp-resident-listings-empty" class="hidden text-center py-8">
                <p class="text-sm text-slate-500">{{ $isAr ? 'لا توجد إعلانات مباشرة حاليًا' : 'No direct owner listings yet' }}</p>
                <a href="{{ route('mobile.my-listings.create') }}" class="inline-block mt-3 text-sm font-semibold text-[color:var(--market-palm)]">{{ $isAr ? 'كن الأول — انشر الآن' : 'Be the first — Post yours now' }}</a>
            </div>
        </section>

        <section class="mm-shell mt-7 px-4">
            <div class="mm-section-head">
                <div class="mm-section-copy">
                    <div class="mm-section-kicker">{{ $tx['categoriesKicker'] }}</div>
                    <h2 class="mm-section-title">{{ $tx['categoriesTitle'] }}</h2>
                </div>
            </div>
            <div id="mp-categories" class="mm-scroll"></div>
        </section>

        <section class="mm-shell mt-7 px-4" id="agencies-section">
            <div class="mm-section-head">
                <div class="mm-section-copy">
                    <div class="mm-section-kicker">{{ $tx['agenciesKicker'] }}</div>
                    <h2 class="mm-section-title">{{ $tx['agenciesTitle'] }}</h2>
                </div>
            </div>
            <div id="mp-tenants" class="mm-scroll">
                <div class="mm-loading w-full">
                    <span class="mm-spinner"></span>
                    <span class="text-sm font-semibold text-slate-500">{{ $isAr ? 'جاري التحميل...' : 'Loading...' }}</span>
                </div>
            </div>
        </section>

        <section class="mm-shell mt-7 px-4">
            <div class="mm-section-head">
                <div class="mm-section-copy">
                    <div class="mm-section-kicker">{{ $tx['citiesKicker'] }}</div>
                    <h2 class="mm-section-title">{{ $tx['citiesTitle'] }}</h2>
                </div>
            </div>
            <div id="mp-cities" class="mm-city-grid"></div>
        </section>

        <section class="mm-shell mt-7 px-4" id="map-section">
            <div class="mm-section-head">
                <div class="mm-section-copy">
                    <div class="mm-section-kicker">{{ $tx['mapKicker'] }}</div>
                    <h2 class="mm-section-title">{{ $tx['mapTitle'] }}</h2>
                </div>
            </div>
            <div class="mm-map-note mb-3 text-sm leading-7 text-slate-600">
                <strong class="block text-sm font-extrabold text-[color:var(--market-ink)]">{{ $tx['mapText'] }}</strong>
                <span class="mt-1 block text-xs text-slate-500">{{ $tx['mapHint'] }}</span>
            </div>
            <div class="mm-map-card">
                <div id="mp-map" class="relative h-[290px] w-full">
                    <div id="mp-map-placeholder" class="flex h-full flex-col items-center justify-center px-6 text-center">
                        <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-[rgba(15,90,70,.08)] text-[color:var(--market-palm)]">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <p class="text-sm font-semibold text-slate-600">{{ $isAr ? 'جاري التحميل...' : 'Loading...' }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="mm-shell mt-7 px-4" id="feed-section">
            <div class="mm-section-head">
                <div class="mm-section-copy">
                    <div class="mm-section-kicker">{{ $tx['feedKicker'] }}</div>
                    <h2 class="mm-section-title">{{ $tx['feedTitle'] }}</h2>
                </div>
            </div>
            <div id="mobile-marketplace-results" class="mm-feed">
                <div class="mm-loading">
                    <span class="mm-spinner"></span>
                    <span class="text-sm font-semibold text-slate-500">{{ $isAr ? 'جاري التحميل...' : 'Loading...' }}</span>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
const lang = document.documentElement.lang === 'ar' ? 'ar' : 'en';
const marketplaceForm = document.getElementById('mobile-marketplace-filter');
const marketplaceResults = document.getElementById('mobile-marketplace-results');
const fullSearchLink = document.getElementById('mm-full-search-link');
const fullSearchBaseUrl = @json(route('mobile.search'));
const statResults = document.getElementById('mm-stat-results');
const statAgencies = document.getElementById('mm-stat-agencies');
const statCities = document.getElementById('mm-stat-cities');
const tx = {
    noResults: lang === 'ar' ? 'لا توجد نتائج حالياً' : 'No results right now',
    noResultsHint: lang === 'ar' ? 'جرّب تغيير البحث أو الفلاتر.' : 'Try adjusting the search or filters.',
    noLocations: lang === 'ar' ? 'لا توجد مواقع متاحة بعد' : 'No map locations yet',
    noLocationsHint: lang === 'ar' ? 'ستظهر العقارات هنا عند توفر الإحداثيات.' : 'Listings will appear here once coordinates are available.',
    loading: lang === 'ar' ? 'جاري التحميل...' : 'Loading...',
    sale: lang === 'ar' ? 'للبيع' : 'For Sale',
    rent: lang === 'ar' ? 'للإيجار' : 'For Rent',
    beds: lang === 'ar' ? 'غرف' : 'beds',
    baths: lang === 'ar' ? 'حمامات' : 'baths',
    sqft: lang === 'ar' ? 'قدم²' : 'sq ft',
    listings: lang === 'ar' ? 'إعلانات' : 'Listings',
    active: lang === 'ar' ? 'نشط' : 'Active',
    viewProperty: lang === 'ar' ? 'عرض العقار' : 'View property',
    perYear: lang === 'ar' ? 'سنوياً' : 'per year',
    allCategories: lang === 'ar' ? 'الكل' : 'All',
    allCategoriesHint: lang === 'ar' ? 'كل الفئات' : 'All categories',
    viewAll: lang === 'ar' ? 'عرض الكل' : 'View all',
};

let sectionsLoaded = false;
let mapInstance = null;
let mapMarkers = [];
let debounceTimer = null;

function formatNumber(value) {
    return new Intl.NumberFormat().format(Number(value || 0));
}

function money(value, currency = 'JOD') {
    return `${currency} ${formatNumber(value)}`;
}

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, (char) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;',
    })[char]);
}

function categoryIcon() {
    return `<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2.5a2 2 0 012 2v2.5a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm9.5 0a2 2 0 012-2H18a2 2 0 012 2v2.5a2 2 0 01-2 2h-2.5a2 2 0 01-2-2V6zM4 15.5a2 2 0 012-2h2.5a2 2 0 012 2V18a2 2 0 01-2 2H6a2 2 0 01-2-2v-2.5zm9.5 0a2 2 0 012-2H18a2 2 0 012 2V18a2 2 0 01-2 2h-2.5a2 2 0 01-2-2v-2.5z"/></svg>`;
}

function unitTitle(unit) {
    return unit.translated_title ?? unit.title ?? unit.code;
}

function resolvedUnitLocation(unit) {
    if (unit.location_label) return unit.location_label;
    return unitLocation(unit);
}

function priceValue(unit) {
    return unit.display_price ?? unit.price ?? 0;
}

function attributeHighlightsHtml(unit) {
    const attributes = Array.isArray(unit.attribute_highlights) ? unit.attribute_highlights.slice(0, 3) : [];
    return attributes.map((attribute) => {
        const tone = attribute.featured ? 'palm' : 'clay';
        return `<span class="mm-pill ${tone}">${escapeHtml(attribute.label)}: ${escapeHtml(attribute.value)}</span>`;
    }).join('');
}

function marketplaceSearchUrl() {
    const params = new URLSearchParams();
    for (const [key, value] of new FormData(marketplaceForm).entries()) {
        if (String(value ?? '').trim() !== '') {
            params.set(key, value);
        }
    }
    const query = params.toString();
    return query ? `${fullSearchBaseUrl}?${query}` : fullSearchBaseUrl;
}

function syncMarketplaceSearchHref() {
    if (fullSearchLink) {
        fullSearchLink.href = marketplaceSearchUrl();
    }
}

function unitLocation(unit) {
    const parts = [];
    if (unit.property?.name) parts.push(unit.property.name);
    if (unit.city?.name_en || unit.city?.name_ar) parts.push(lang === 'ar' ? (unit.city?.name_ar ?? unit.city?.name_en) : (unit.city?.name_en ?? unit.city?.name_ar));
    if (parts.length) return parts.join(' · ');
    if (unit.location) return unit.location;
    return lang === 'ar' ? 'تفاصيل الموقع قريباً' : 'Location details coming soon';
}

function typeLabel(listingType) {
    return listingType === 'sale' ? tx.sale : tx.rent;
}

function typeClass(listingType) {
    return listingType === 'sale' ? 'sale' : 'rent';
}

function unitGalleryHtml(unit, options = {}) {
    const photos = Array.isArray(unit.photos) && unit.photos.length
        ? unit.photos
        : ['https://picsum.photos/seed/aqarismart-fallback/960/680'];
    const sliderId = `slider-${unit.code}-${options.compact ? 'compact' : 'feed'}`;
    const heightClass = options.compact ? 'h-44' : 'h-56';
    const title = escapeHtml(unitTitle(unit));

    return `
        <div class="mm-slider ${heightClass}" data-slider="${sliderId}">
            <div class="mm-slider-track h-full" data-slider-track>
                ${photos.map((photo, index) => `
                    <div class="mm-slide h-full">
                        <img src="${photo}" alt="${title}" class="h-full w-full object-cover" loading="lazy" data-slide-index="${index}">
                    </div>
                `).join('')}
            </div>
            ${photos.length > 1 ? `
                <button type="button" class="mm-slider-btn prev" data-slider-prev aria-label="${lang === 'ar' ? 'الصورة السابقة' : 'Previous image'}">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${lang === 'ar' ? 'M9 5l7 7-7 7' : 'M15 19l-7-7 7-7'}"/></svg>
                </button>
                <button type="button" class="mm-slider-btn next" data-slider-next aria-label="${lang === 'ar' ? 'الصورة التالية' : 'Next image'}">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${lang === 'ar' ? 'M15 19l-7-7 7-7' : 'M9 5l7 7-7 7'}"/></svg>
                </button>
                <div class="mm-slider-dots">
                    ${photos.map((_, index) => `<span class="mm-slider-dot ${index === 0 ? 'is-active' : ''}" data-slider-dot data-slide="${index}"></span>`).join('')}
                </div>
            ` : ''}
        </div>
    `;
}

function unitCardHtml(unit, options = {}) {
    const title = escapeHtml(unitTitle(unit));
    const location = escapeHtml(resolvedUnitLocation(unit));
    const beds = unit.bedrooms ?? unit.beds ?? 0;
    const baths = unit.bathrooms ?? unit.baths ?? 0;
    const area = unit.sqft ?? unit.area_m2 ?? 0;
    const compact = !!options.compact;
    return `
        <article class="mm-unit-card ${compact ? 'compact' : ''}" data-unit-card>
            <div class="relative">
                ${unitGalleryHtml(unit, { compact })}
                <a href="/mobile/units/${unit.code}" class="absolute inset-0 z-[4]" aria-label="${title}"></a>
                <div class="pointer-events-none absolute left-3 top-3 z-[5]">
                    <span class="mm-unit-badge ${typeClass(unit.listing_type)}">${typeLabel(unit.listing_type)}</span>
                </div>
                <div class="pointer-events-none absolute bottom-3 left-3 z-[5]">
                    <span class="mm-price-pill">${money(priceValue(unit), unit.currency ?? 'JOD')}${unit.listing_type === 'rent' && priceValue(unit) ? ` <span class="text-[10px] font-extrabold uppercase tracking-[0.12em] text-slate-500">${tx.perYear}</span>` : ''}</span>
                </div>
            </div>
            <div class="mm-unit-card-body">
                <div class="text-[11px] font-extrabold uppercase tracking-[0.15em] text-[color:var(--market-brass)]">${escapeHtml(unit.subcategory?.name ?? typeLabel(unit.listing_type))}</div>
                <h3 class="mt-2 text-lg font-black leading-tight tracking-[-0.03em] text-[color:var(--market-ink)]">${title}</h3>
                <p class="mt-2 text-sm leading-6 text-slate-500">${location}</p>
                <div class="mm-unit-meta">
                    <span class="mm-pill palm">${formatNumber(beds)} ${tx.beds}</span>
                    <span class="mm-pill brass">${formatNumber(baths)} ${tx.baths}</span>
                    ${area ? `<span class="mm-pill clay">${formatNumber(area)} ${tx.sqft}</span>` : ''}
                    ${attributeHighlightsHtml(unit)}
                </div>
                <div class="mt-5 inline-flex items-center gap-2 text-[11px] font-extrabold uppercase tracking-[0.16em] text-[color:var(--market-palm)]">
                    <span>${tx.viewProperty}</span>
                    <svg class="h-3.5 w-3.5 ${lang === 'ar' ? 'rotate-180' : ''}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </div>
            </div>
        </article>
    `;
}

function setupUnitSliders(scope = document) {
    scope.querySelectorAll('[data-slider]').forEach((slider) => {
        if (slider.dataset.bound === 'true') return;
        slider.dataset.bound = 'true';

        const track = slider.querySelector('[data-slider-track]');
        const dots = Array.from(slider.querySelectorAll('[data-slider-dot]'));
        const prev = slider.querySelector('[data-slider-prev]');
        const next = slider.querySelector('[data-slider-next]');
        if (!track) return;

        const updateDots = () => {
            if (!dots.length) return;
            const width = track.clientWidth || 1;
            const currentIndex = Math.max(0, Math.min(dots.length - 1, Math.round(track.scrollLeft / width)));
            dots.forEach((dot, index) => dot.classList.toggle('is-active', index === currentIndex));
        };

        const scrollToIndex = (index) => {
            const width = track.clientWidth || 1;
            track.scrollTo({ left: width * index, behavior: 'smooth' });
        };

        prev?.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            const width = track.clientWidth || 1;
            const currentIndex = Math.round(track.scrollLeft / width);
            scrollToIndex(currentIndex <= 0 ? dots.length - 1 : currentIndex - 1);
        });

        next?.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            const width = track.clientWidth || 1;
            const currentIndex = Math.round(track.scrollLeft / width);
            scrollToIndex(currentIndex >= dots.length - 1 ? 0 : currentIndex + 1);
        });

        dots.forEach((dot, index) => {
            dot.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                scrollToIndex(index);
            });
        });

        track.addEventListener('scroll', updateDots, { passive: true });
        window.addEventListener('resize', updateDots, { passive: true });
        updateDots();
    });
}

function updateStats(json) {
    if (statResults) statResults.textContent = formatNumber(json?.data?.length ?? 0);
    if (statAgencies) statAgencies.textContent = formatNumber(json?.tenants?.length ?? 0);
    if (statCities) statCities.textContent = formatNumber(json?.cities?.length ?? 0);
}

function renderCategories(categories) {
    const container = document.getElementById('mp-categories');
    if (!container) return;

    const items = [{ id: '', name: tx.allCategories, count: 0, isAll: true }, ...(categories ?? [])];
    container.innerHTML = items.map((cat, index) => {
        const name = typeof cat.name === 'object' ? (cat.name[lang] ?? cat.name.en ?? tx.allCategories) : (cat.name ?? tx.allCategories);
        const count = cat.count ?? cat.units_count ?? 0;
        return `
            <button type="button" class="category-filter mm-category-card ${index === 0 ? 'is-active' : ''}" data-id="${cat.id ?? ''}">
                <div class="mm-category-icon">${categoryIcon()}</div>
                <div class="text-xs font-black leading-tight">${escapeHtml(name)}</div>
                <div class="mt-2 text-[10px] font-extrabold uppercase tracking-[0.14em] ${index === 0 ? 'text-white/76' : 'text-slate-400'}">${formatNumber(count)} ${cat.isAll ? tx.allCategoriesHint : tx.listings}</div>
            </button>
        `;
    }).join('');

    bindCategoryFilters();
}

function renderCities(cities) {
    const container = document.getElementById('mp-cities');
    if (!container) return;

    if (!cities?.length) {
        container.innerHTML = `<div class="mm-empty col-span-full">
            <div class="text-base font-black tracking-[-0.03em] text-[color:var(--market-ink)]">${tx.noResults}</div>
            <p class="mt-2 text-sm leading-7 text-slate-500">${tx.noResultsHint}</p>
        </div>`;
        return;
    }

    container.innerHTML = cities.map((city) => {
        const name = lang === 'ar' ? (city.name_ar ?? city.name_en ?? '') : (city.name_en ?? city.name_ar ?? '');
        return `
            <button type="button" class="city-filter mm-city-card text-left" data-id="${city.id ?? ''}">
                <img src="${city.image}" alt="${escapeHtml(name)}" loading="lazy">
                <div class="mm-city-copy">
                    <div class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-[rgba(255,241,212,.86)]">${formatNumber(city.units_count ?? 0)} ${tx.listings}</div>
                    <h3 class="mt-2 text-lg font-black leading-tight tracking-[-0.03em] text-white">${escapeHtml(name)}</h3>
                </div>
            </button>
        `;
    }).join('');

    bindCityFilters();
}

function renderTenants(tenants) {
    const container = document.getElementById('mp-tenants');
    if (!container) return;

    if (!tenants?.length) {
        container.innerHTML = `<div class="mm-empty w-full">
            <div class="text-base font-black tracking-[-0.03em] text-[color:var(--market-ink)]">${tx.noResults}</div>
            <p class="mt-2 text-sm leading-7 text-slate-500">${tx.noResultsHint}</p>
        </div>`;
        return;
    }

    container.innerHTML = tenants.map((tenant) => {
        const logo = tenant.branding?.logo_url ?? `https://ui-avatars.com/api/?name=${encodeURIComponent(tenant.name)}&background=efe4cf&color=0f5a46&bold=true`;
        const desc = tenant.summary?.description ?? (lang === 'ar' ? 'واجهة عقارية موثوقة' : 'A trusted real estate storefront');
        return `
            <a href="/mobile/tenants/${tenant.slug}" class="mm-agency-card">
                <div class="flex items-center gap-3">
                    <div class="mm-agency-logo">
                        <img src="${logo}" alt="${escapeHtml(tenant.name)}" class="h-full w-full object-cover" loading="lazy">
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="text-[11px] font-extrabold uppercase tracking-[0.16em] text-[color:var(--market-brass)]">${tx.viewAll}</div>
                        <h3 class="mt-1 truncate text-lg font-black leading-tight tracking-[-0.03em] text-[color:var(--market-ink)]">${escapeHtml(tenant.name)}</h3>
                        <p class="mt-1 line-clamp-2 text-xs leading-6 text-slate-500">${escapeHtml(desc)}</p>
                    </div>
                </div>
                <div class="mm-agency-stat">
                    <div>
                        <strong>${formatNumber(tenant.stats?.units_count ?? 0)}</strong>
                        <span>${tx.listings}</span>
                    </div>
                    <div>
                        <strong>${formatNumber(tenant.stats?.active_units_count ?? 0)}</strong>
                        <span>${tx.active}</span>
                    </div>
                </div>
            </a>
        `;
    }).join('');
}

function renderUnitScroll(containerId, units) {
    const container = document.getElementById(containerId);
    if (!container) return;

    if (!units?.length) {
        container.innerHTML = `<div class="mm-empty w-full">
            <div class="text-base font-black tracking-[-0.03em] text-[color:var(--market-ink)]">${tx.noResults}</div>
            <p class="mt-2 text-sm leading-7 text-slate-500">${tx.noResultsHint}</p>
        </div>`;
        return;
    }

    container.innerHTML = units.map((unit) => unitCardHtml(unit, { compact: true })).join('');
    setupUnitSliders(container);
}

function renderFeed(units) {
    if (!units?.length) {
        marketplaceResults.innerHTML = `<div class="mm-empty">
            <div class="text-lg font-black tracking-[-0.03em] text-[color:var(--market-ink)]">${tx.noResults}</div>
            <p class="mt-2 text-sm leading-7 text-slate-500">${tx.noResultsHint}</p>
        </div>`;
        return;
    }

    marketplaceResults.innerHTML = units.map((unit) => unitCardHtml(unit)).join('');
    setupUnitSliders(marketplaceResults);
}

function renderMap(units) {
    const mapEl = document.getElementById('mp-map');
    const placeholder = document.getElementById('mp-map-placeholder');
    if (!mapEl) return;

    const geoUnits = (units || []).filter((unit) => unit.lat && unit.lng && Number(unit.lat) !== 0 && Number(unit.lng) !== 0);
    if (!geoUnits.length) {
        if (placeholder) {
            placeholder.innerHTML = `<div class="flex h-full flex-col items-center justify-center px-6 text-center">
                <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-[rgba(15,90,70,.08)] text-[color:var(--market-palm)]">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                </div>
                <p class="text-sm font-semibold text-slate-600">${tx.noLocations}</p>
                <p class="mt-1 text-xs leading-6 text-slate-400">${tx.noLocationsHint}</p>
            </div>`;
        }
        return;
    }

    if (placeholder) placeholder.style.display = 'none';

    if (!mapInstance) {
        mapInstance = L.map(mapEl, { zoomControl: false, attributionControl: false, scrollWheelZoom: false }).setView([Number(geoUnits[0].lat), Number(geoUnits[0].lng)], 10);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 18 }).addTo(mapInstance);
        L.control.zoom({ position: 'topright' }).addTo(mapInstance);
        setTimeout(() => mapInstance.invalidateSize(), 180);
    }

    mapMarkers.forEach((marker) => marker.remove());
    mapMarkers = [];

    const bounds = L.latLngBounds();
    geoUnits.forEach((unit) => {
        const lat = Number(unit.lat);
        const lng = Number(unit.lng);
        const marker = L.circleMarker([lat, lng], {
            radius: 8,
            color: '#fff4dc',
            weight: 3,
            fillColor: unit.listing_type === 'sale' ? '#b6842f' : '#2f7a72',
            fillOpacity: 1,
        }).addTo(mapInstance);

        marker.bindPopup(`
            <div class="mm-map-popup" dir="${lang === 'ar' ? 'rtl' : 'ltr'}">
                <div class="mm-map-popup-title">${escapeHtml(unitTitle(unit))}</div>
                <div class="mm-map-popup-meta">${escapeHtml(money(unit.price ?? 0, unit.currency ?? 'JOD'))} · ${escapeHtml(unitLocation(unit))}</div>
                <a class="mm-map-popup-link" href="/mobile/units/${unit.code}">${tx.viewProperty}</a>
            </div>
        `);
        marker.on('click', () => marker.openPopup());
        mapMarkers.push(marker);
        bounds.extend([lat, lng]);
    });

    if (bounds.isValid()) {
        mapInstance.fitBounds(bounds, { padding: [28, 28], maxZoom: 13 });
    }
}

function bindCategoryFilters() {
    document.querySelectorAll('.category-filter').forEach((button) => {
        button.addEventListener('click', async () => {
            document.querySelectorAll('.category-filter').forEach((card) => card.classList.remove('is-active'));
            button.classList.add('is-active');
            const categoryField = document.getElementById('filter_category_id');
            if (categoryField) categoryField.value = button.dataset.id ?? '';
            syncMarketplaceSearchHref();
            await loadMarketplace();
        });
    });
}

function bindCityFilters() {
    document.querySelectorAll('.city-filter').forEach((button) => {
        button.addEventListener('click', async () => {
            const cityField = document.getElementById('filter_city_id');
            if (cityField) cityField.value = button.dataset.id ?? '';
            syncMarketplaceSearchHref();
            await loadMarketplace();
            document.getElementById('feed-section')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
}

async function loadMarketplace() {
    const params = new URLSearchParams(new FormData(marketplaceForm));
    const apiBase = window.__AQARI_API_BASE || '';
    const response = await fetch(`${apiBase}/api/mobile/marketplace?${params.toString()}`, { headers: { Accept: 'application/json' } });
    const json = await response.json();

    if (!sectionsLoaded) {
        renderCategories(json.categories ?? []);
        renderCities(json.cities ?? []);
        renderTenants(json.tenants ?? []);
        renderUnitScroll('mp-recommended', json.recommended_units ?? []);
        loadResidentListings();
        sectionsLoaded = true;
    }

    updateStats(json);
    renderFeed(json.data ?? []);
    renderMap(json.data ?? []);
}

async function loadResidentListings() {
    const apiBase = window.__AQARI_API_BASE || '';
    const container = document.getElementById('mp-resident-listings');
    const emptyState = document.getElementById('mp-resident-listings-empty');
    const section = document.getElementById('direct-owner-section');

    try {
        const response = await fetch(`${apiBase}/api/mobile/resident-listings?per_page=8`, { headers: { Accept: 'application/json' } });
        if (!response.ok) { section?.classList.add('hidden'); return; }

        const json = await response.json();
        const listings = json.data ?? [];

        if (!container) return;
        container.innerHTML = '';

        if (listings.length === 0) {
            container.classList.add('hidden');
            emptyState?.classList.remove('hidden');
            return;
        }

        listings.forEach((listing) => {
            const photo = listing.first_photo || '';
            const title = listing.title?.[lang] || listing.title?.en || listing.code;
            const price = new Intl.NumberFormat().format(Number(listing.price || 0));
            const currency = listing.currency || 'IQD';
            const listingTypeLabel = listing.listing_type === 'sale'
                ? (lang === 'ar' ? 'للبيع' : 'For Sale')
                : (lang === 'ar' ? 'للإيجار' : 'For Rent');
            const cityName = listing.city ? (lang === 'ar' ? listing.city.name_ar : listing.city.name_en) : '';
            const bedrooms = listing.bedrooms > 0 ? `${listing.bedrooms} ${lang === 'ar' ? 'غرف' : 'beds'}` : '';
            const bathrooms = listing.bathrooms > 0 ? `${listing.bathrooms} ${lang === 'ar' ? 'حمامات' : 'baths'}` : '';

            const card = document.createElement('a');
            card.href = `/mobile/resident-listings/${listing.code}`;
            card.className = 'mm-card flex-shrink-0 w-[220px] rounded-2xl overflow-hidden block';
            card.innerHTML = `
                <div class="relative w-full h-[140px] bg-slate-200 flex items-center justify-center overflow-hidden">
                    ${photo
                        ? `<img src="${escapeHtml(photo)}" alt="${escapeHtml(title)}" class="w-full h-full object-cover">`
                        : `<svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>`}
                    <span class="absolute top-2 left-2 bg-green-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">${lang === 'ar' ? 'مالك مباشر' : 'Direct Owner'}</span>
                    <span class="absolute bottom-2 right-2 bg-black/50 text-white text-[10px] font-bold px-2 py-0.5 rounded">${escapeHtml(listingTypeLabel)}</span>
                </div>
                <div class="p-3">
                    <p class="text-sm font-bold leading-tight text-[color:var(--market-ink)] line-clamp-2 mb-1">${escapeHtml(title)}</p>
                    ${cityName ? `<p class="text-xs text-slate-500 mb-2">${escapeHtml(cityName)}</p>` : ''}
                    <p class="text-base font-black text-[color:var(--market-palm)]">${price} <span class="text-xs font-semibold text-slate-500">${escapeHtml(currency)}</span></p>
                    ${(bedrooms || bathrooms) ? `<p class="text-xs text-slate-500 mt-1">${[bedrooms, bathrooms].filter(Boolean).join(' · ')}</p>` : ''}
                </div>
            `;
            container.appendChild(card);
        });

    } catch (error) {
        console.error('Failed to load resident listings:', error);
        section?.classList.add('hidden');
    }
}

document.querySelectorAll('.listing-toggle').forEach((button) => {
    button.addEventListener('click', () => {
        document.getElementById('listing_type_input').value = button.dataset.value;
        document.querySelectorAll('.listing-toggle').forEach((toggle) => toggle.classList.remove('is-active'));
        button.classList.add('is-active');
        syncMarketplaceSearchHref();
        loadMarketplace();
    });
});

marketplaceForm?.addEventListener('submit', async (event) => {
    event.preventDefault();
    window.location.href = marketplaceSearchUrl();
});

marketplaceForm?.querySelector('input[name="q"]')?.addEventListener('input', () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        syncMarketplaceSearchHref();
        loadMarketplace();
    }, 450);
});

syncMarketplaceSearchHref();
loadMarketplace();
</script>
@endpush
