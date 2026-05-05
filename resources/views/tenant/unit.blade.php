@extends('layouts.app')

@php
    $propertyName = $unit->property?->name ?? ($unit->translated_title ?: __('Standalone listing'));
@endphp

@section('title', __('Unit :code - :property', ['code' => $unit->code, 'property' => $propertyName]))

@section('content')
    @php
        $tenantCtx = $tenantCtx ?? app(\App\Services\Tenancy\TenantManager::class)->tenant();
        $locale = app()->getLocale();
        $isRtl = $locale === 'ar';
        $scheme = request()->getScheme() ?: 'http';
        $port = request()->getPort();
        $defaultPort = $scheme === 'https' ? 443 : 80;
        $portPart = $port && $port !== $defaultPort ? ':' . $port : '';
        $centralBaseUrl = sprintf('%s://%s%s', $scheme, config('tenancy.base_domain'), $portPart);
        $centralMarketplaceUrl = rtrim($centralBaseUrl, '/') . '/marketplace';

        $gallery = [];
        if (is_array($unit->photos) && count($unit->photos)) {
            $gallery = $unit->photos;
        } elseif (is_array($unit->property?->photos) && count($unit->property?->photos)) {
            $gallery = $unit->property->photos;
        }

        $gallery = collect($gallery)
            ->map(function ($image) {
                if (! is_string($image) || $image === '') {
                    return null;
                }

                return \Illuminate\Support\Str::startsWith($image, ['http://', 'https://'])
                    ? $image
                    : url('/') . '/' . (\Illuminate\Support\Str::startsWith($image, 'storage/') ? $image : 'storage/' . $image);
            })
            ->filter()
            ->values()
            ->all();

        $isSale = ($unit->listing_type ?? \App\Models\Unit::LISTING_RENT) === \App\Models\Unit::LISTING_SALE;
        $unitCurr = $unit->currency ?? ($tenantCtx?->settings['currency'] ?? 'JOD');
        $displayPrice = $isSale
            ? ($unit->price ?? 0)
            : ($unit->market_rent && $unit->market_rent > 0 ? $unit->market_rent : ($unit->price ?? 0));

        $statusLabel = \App\Models\Unit::statusLabels()[$unit->status] ?? \Illuminate\Support\Str::headline((string) $unit->status);
        $categoryName = $unit->subcategory?->category?->name;
        $subcategoryName = $unit->subcategory?->name ?? __('Property');

        $cityName = $isRtl ? ($unit->city?->name_ar ?? $unit->city?->name_en) : ($unit->city?->name_en ?? $unit->city?->name_ar);
        $areaName = $isRtl ? ($unit->area?->name_ar ?? $unit->area?->name_en) : ($unit->area?->name_en ?? $unit->area?->name_ar);

        $locationBits = array_values(array_filter([$unit->location, $areaName, $cityName]));
        if ($locationBits === []) {
            $locationBits = array_values(array_filter([data_get($unit->officialInfo, 'village'), data_get($unit->officialInfo, 'directorate')]));
        }
        $locationLabel = implode(' / ', $locationBits);

        $bedValue = $unit->beds ?: $unit->bedrooms;
        $bathValue = $unit->baths ?: $unit->bathrooms;
        $totalSqm = data_get($unit->officialInfo, 'areas.total_sqm');
        $builtSqm = data_get($unit->officialInfo, 'areas.built_sqm');
        $landSqm = data_get($unit->officialInfo, 'areas.land_sqm');
        $sizeValue = $unit->sqft ?: $totalSqm ?: $builtSqm ?: $landSqm;
        $sizeLabel = $unit->sqft ? __('Sq Ft') : __('Sq M');

        $mapHref = $unit->location_url;
        if (! $mapHref && $unit->lat && $unit->lng) {
            $mapHref = 'https://maps.google.com/?q=' . $unit->lat . ',' . $unit->lng;
        }

        $mapLat = $unit->lat !== null ? (float) $unit->lat : null;
        $mapLng = $unit->lng !== null ? (float) $unit->lng : null;
        $mapZoom = 15;
        $mapMode = null;

        if ($mapLat !== null && $mapLng !== null) {
            $mapMode = 'exact';
        } elseif ($mapHref) {
            $matched = preg_match('/@(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)/', $mapHref, $matches)
                || preg_match('/[?&](?:q|query)=(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)/', $mapHref, $matches)
                || preg_match('/!3d(-?\d+(?:\.\d+)?)!4d(-?\d+(?:\.\d+)?)/', $mapHref, $matches);

            if ($matched) {
                $mapLat = isset($matches[1]) ? (float) $matches[1] : null;
                $mapLng = isset($matches[2]) ? (float) $matches[2] : null;
                $mapMode = ($mapLat !== null && $mapLng !== null) ? 'url' : null;
            }
        }

        $displayLocationLabel = $locationLabel ?: __('Location details coming soon');
        $areaDisplay = $sizeValue ? number_format((float) $sizeValue, 0) : __('Not specified');
        $bathDisplay = $bathValue !== null && $bathValue !== '' ? $bathValue : __('Not specified');
        $bedDisplay = $bedValue !== null && $bedValue !== '' ? $bedValue : __('Not specified');
        $photoCountLabel = count($gallery) ? count($gallery) . ' ' . __('Photos') : __('No photos uploaded yet.');

        if ($mapLat === null || $mapLng === null) {
            $approximateCityPins = [
                'amman' => ['lat' => 31.9539494, 'lng' => 35.9106350, 'zoom' => 12],
                'zarqa' => ['lat' => 32.0727530, 'lng' => 36.0889350, 'zoom' => 12],
                'irbid' => ['lat' => 32.5569636, 'lng' => 35.8478960, 'zoom' => 12],
                'madaba' => ['lat' => 31.7165941, 'lng' => 35.7943856, 'zoom' => 13],
                'aqaba' => ['lat' => 29.5266730, 'lng' => 35.0077800, 'zoom' => 12],
                'salt' => ['lat' => 32.0391666, 'lng' => 35.7272222, 'zoom' => 12],
                'mafraq' => ['lat' => 32.3416924, 'lng' => 36.2029971, 'zoom' => 12],
                'jerash' => ['lat' => 32.2747237, 'lng' => 35.8960954, 'zoom' => 12],
                'ajloun' => ['lat' => 32.3332576, 'lng' => 35.7518020, 'zoom' => 12],
                'karak' => ['lat' => 31.1853527, 'lng' => 35.7047774, 'zoom' => 12],
                'tafilah' => ['lat' => 30.8338063, 'lng' => 35.6046838, 'zoom' => 12],
                'maan' => ['lat' => 30.1926789, 'lng' => 35.7342413, 'zoom' => 12],
            ];

            $locationSearchNeedle = strtolower((string) $displayLocationLabel);
            foreach ($approximateCityPins as $needle => $pin) {
                if (str_contains($locationSearchNeedle, $needle)) {
                    $mapLat = $pin['lat'];
                    $mapLng = $pin['lng'];
                    $mapZoom = $pin['zoom'];
                    $mapMode = 'approximate';
                    break;
                }
            }
        }

        $hasInteractiveMap = $mapLat !== null && $mapLng !== null;
        $mapPrecisionLabel = match ($mapMode) {
            'exact' => __('Exact pin from saved coordinates.'),
            'url' => __('Pin resolved from the saved map link.'),
            'approximate' => __('Approximate area pin based on the saved listing location.'),
            default => __('Map pin not available yet.'),
        };

        $mapTooltipMeta = implode(' • ', array_values(array_filter([
            $isSale ? __('For Sale') : __('For Rent'),
            $unitCurr . ' ' . number_format((float) $displayPrice, 0),
            $displayLocationLabel,
            $mapMode === 'approximate' ? __('Approximate area') : __('Pinned location'),
        ])));

        $overviewFacts = array_values(array_filter([
            ['label' => __('Listing type'), 'value' => $isSale ? __('For Sale') : __('For Rent')],
            ['label' => __('Status'), 'value' => $statusLabel],
            ['label' => __('Category'), 'value' => $categoryName],
            ['label' => __('Type'), 'value' => $subcategoryName],
            ['label' => __('Unit code'), 'value' => $unit->code],
            ['label' => __('Property Gallery'), 'value' => count($gallery) ? $photoCountLabel : null],
        ], fn ($item) => filled($item['value'])));

        $recordFacts = array_filter([
            __('Directorate') => data_get($unit->officialInfo, 'directorate'),
            __('Village') => data_get($unit->officialInfo, 'village'),
            __('Basin Number') => data_get($unit->officialInfo, 'basin_number'),
            __('Basin Name') => data_get($unit->officialInfo, 'basin_name'),
            __('Plot Number') => data_get($unit->officialInfo, 'plot_number'),
            __('Apartment Number') => data_get($unit->officialInfo, 'apartment_number'),
            __('Land (sqm)') => $landSqm ? number_format((float) $landSqm, 0) : null,
            __('Built (sqm)') => $builtSqm ? number_format((float) $builtSqm, 0) : null,
            __('Total (sqm)') => $totalSqm ? number_format((float) $totalSqm, 0) : null,
        ]);

        $storyText = trim((string) ($unit->translated_description ?: ''));
        $storyText = $storyText !== ''
            ? $storyText
            : ($isSale
                ? __('A sales listing prepared to give buyers a clear first picture before they take the next step.')
                : __('A rental listing prepared to help tenants understand the space, setting, and essentials before they continue.'));

        $summaryNotes = array_values(array_filter([
            $unit->property ? __('Part of the :property collection.', ['property' => $propertyName]) : __('Presented as an independent listing.'),
            $locationLabel ? __('Located in :location.', ['location' => $locationLabel]) : __('Location details will be updated soon.'),
            count($gallery) ? __('Includes :count photos to help you read the space before a visit.', ['count' => count($gallery)]) : __('New property visuals will be added soon.'),
        ]));

        $quickSummary = array_values(array_filter([
            $isSale ? __('Sale listing') : __('Rent listing'),
            __('Area') . ': ' . ($sizeValue ? $areaDisplay . ' ' . $sizeLabel : __('Not specified')),
            $recordFacts !== [] ? __('Official property record') : null,
            $mapHref ? __('Map link available') : __('Map pin not available yet.'),
        ]));

        $listingNavTx = [
            'brand' => $isRtl ? 'عقاري سمارت' : 'Aqari Smart',
            'login_cta' => $isRtl ? 'تسجيل الدخول' : 'Sign in',
            'register_cta' => $isRtl ? 'إنشاء حساب' : 'Create account',
            'sell_cta' => $isRtl ? 'بيع معنا' : 'Sell with us',
            'profile_cta' => $isRtl ? 'الملف الشخصي' : 'Profile',
            'menu_cta' => $isRtl ? 'القائمة' : 'Menu',
            'close_cta' => $isRtl ? 'إغلاق' : 'Close',
            'account_title' => $isRtl ? 'حسابك' : 'Your Account',
            'browse_title' => $isRtl ? 'تنقل داخل العقار' : 'Navigate This Listing',
            'dashboard_cta' => $isRtl ? 'لوحة التحكم' : 'Dashboard',
            'logout_cta' => $isRtl ? 'تسجيل الخروج' : 'Log Out',
            'welcome_cta' => $isRtl ? 'أهلاً' : 'Welcome',
            'guest_subtitle' => $isRtl
                ? 'ارجع إلى السوق المركزي أو أكمل استكشاف تفاصيل هذا العقار.'
                : 'Jump back to the marketplace or keep exploring this property.',
            'switch_language' => $isRtl ? 'تغيير اللغة' : 'Switch language',
        ];

        $listingNavLinks = array_values(array_filter([
            $tenantCtx ? ['label' => $isRtl ? 'واجهة الوكالة' : 'Agency Home', 'href' => route('tenant.home')] : null,
            ['label' => $isRtl ? 'المعرض' : 'Gallery', 'href' => '#listing-gallery'],
            ['label' => $isRtl ? 'التفاصيل' : 'Details', 'href' => '#listing-details'],
            ['label' => $isRtl ? 'الموقع' : 'Location', 'href' => '#listing-location'],
        ]));
    @endphp

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Manrope:wght@400;500;600;700;800&display=swap');
        @import url('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
        @include('public.partials.market-nav-styles')
        .property-story-page{--story-ink:#1f2a24;--story-palm:#0f5a46;--story-river:#2f7a72;--story-brass:#b6842f;--story-clay:#9d5a3b;--story-line:rgba(130,94,38,.16);background:radial-gradient(circle at top left,rgba(182,132,47,.16),transparent 24%),radial-gradient(circle at top right,rgba(15,90,70,.14),transparent 26%),linear-gradient(180deg,#ece2cf 0,#f7f0e4 320px,#fbf7ef 100%);color:var(--story-ink);font-family:'Manrope',system-ui,sans-serif}
        .property-story-page[dir="rtl"]{font-family:'Cairo','Noto Sans Arabic',sans-serif}.property-shell{max-width:1340px;margin:0 auto;padding-inline:1rem}.story-surface{background:rgba(255,252,246,.94);border:1px solid var(--story-line);box-shadow:0 24px 54px -36px rgba(57,42,16,.36)}.story-panel{background:linear-gradient(180deg,rgba(255,249,239,.98),rgba(247,237,214,.92))}.story-hero-copy{position:relative;overflow:hidden;background:radial-gradient(circle at top right,rgba(182,132,47,.18),transparent 32%),linear-gradient(140deg,rgba(15,32,26,.96),rgba(15,90,70,.88) 54%,rgba(48,33,15,.84));color:#fff6e5}.story-hero-copy::before{content:"";position:absolute;inset:0;background-image:linear-gradient(90deg,rgba(255,255,255,.045) 1px,transparent 1px),linear-gradient(rgba(255,255,255,.045) 1px,transparent 1px);background-size:84px 84px;mask-image:linear-gradient(to bottom,rgba(0,0,0,.62),transparent 88%);pointer-events:none}.story-gallery-stage{position:relative;overflow:hidden;border-radius:1.8rem;border:1px solid rgba(130,94,38,.14);background:linear-gradient(145deg,rgba(248,240,221,.95),rgba(244,229,199,.92));box-shadow:0 24px 54px -38px rgba(57,42,16,.38)}.story-gallery-image{height:100%;width:100%;object-fit:cover;transition:transform .55s ease}.story-gallery-stage:hover .story-gallery-image{transform:scale(1.03)}.story-gallery-empty{background:radial-gradient(circle at top left,rgba(182,132,47,.18),transparent 24%),linear-gradient(145deg,#163129,#0f5a46 55%,#3a2410)}.story-gallery-control{height:3rem;width:3rem;border-radius:999px;background:rgba(255,250,241,.94);color:#1f2a24;box-shadow:0 18px 30px -18px rgba(0,0,0,.58);transition:transform .2s ease}.story-gallery-control:hover{transform:scale(1.05)}.story-gallery-thumb{border:1px solid rgba(130,94,38,.16);background:rgba(255,252,246,.94);box-shadow:0 18px 36px -28px rgba(55,38,12,.42);transition:transform .25s ease,box-shadow .25s ease}.story-gallery-thumb:hover{transform:translateY(-2px);box-shadow:0 24px 40px -26px rgba(55,38,12,.48)}.story-gallery-thumb.is-active{outline:2px solid var(--story-brass);outline-offset:2px}.story-ornament{height:10px;width:112px;border-radius:999px;background:linear-gradient(90deg,rgba(15,90,70,.16),rgba(182,132,47,.32),rgba(15,90,70,.16)),repeating-linear-gradient(90deg,transparent 0 10px,rgba(182,132,47,.58) 10px 14px,transparent 14px 24px)}.story-kicker{font-size:.72rem;font-weight:800;letter-spacing:.2em;text-transform:uppercase;color:rgba(255,244,221,.72)}.story-badge{display:inline-flex;align-items:center;gap:.55rem;border-radius:999px;padding:.7rem 1rem;font-size:.74rem;font-weight:800;letter-spacing:.18em;text-transform:uppercase;backdrop-filter:blur(14px)}.story-badge::before{content:"";width:.48rem;height:.48rem;border-radius:999px;background:currentColor;opacity:.88}.story-badge-sale{background:rgba(182,132,47,.14);border:1px solid rgba(255,235,201,.16);color:#ffe6af}.story-badge-rent{background:rgba(15,90,70,.16);border:1px solid rgba(220,252,244,.14);color:#ddf7ef}.story-badge-neutral{background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.12);color:rgba(255,248,235,.9)}.story-card{border-radius:1.9rem;padding:1.6rem}.story-stat,.story-fact{border:1px solid rgba(130,94,38,.14);background:rgba(255,251,244,.88)}.story-stat{border-radius:1.3rem;padding:1rem}.story-fact{border-radius:1.2rem;padding:1rem 1rem .95rem}.story-soft-note{border-radius:1.4rem;border:1px solid rgba(130,94,38,.12);background:rgba(255,250,242,.84);padding:1rem}.story-divider{border-top:1px solid rgba(130,94,38,.14)}.story-map-card{background:radial-gradient(circle at top left,rgba(182,132,47,.14),transparent 30%),linear-gradient(145deg,rgba(15,90,70,.08),rgba(255,249,239,.96))}.story-map-wrap{position:relative;overflow:hidden;border-radius:1.6rem;border:1px solid rgba(130,94,38,.14);background:linear-gradient(145deg,rgba(248,240,221,.95),rgba(244,229,199,.92));box-shadow:0 24px 54px -38px rgba(57,42,16,.3)}.story-map-canvas{height:360px;width:100%}.story-map-note{position:absolute;top:1rem;z-index:450;border-radius:999px;border:1px solid rgba(255,255,255,.14);background:rgba(19,30,24,.72);padding:.55rem .9rem;font-size:.72rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:#fff4dc;backdrop-filter:blur(10px)}.story-map-note--start{left:1rem}.story-map-note--end{right:1rem}.story-map-helper{border-radius:1.4rem;border:1px solid rgba(130,94,38,.12);background:rgba(255,250,242,.84);padding:1rem 1rem .95rem}.story-map-tooltip{background:transparent;border:0;box-shadow:none}.story-map-tooltip .leaflet-tooltip-content{margin:0}.story-map-bubble{min-width:220px;border-radius:1rem;border:1px solid rgba(182,132,47,.22);background:rgba(20,31,26,.94);padding:.85rem .95rem;color:#fff8ea;box-shadow:0 18px 40px -20px rgba(0,0,0,.4)}.story-map-bubble-title{font-size:.95rem;font-weight:800;line-height:1.3}.story-map-bubble-meta{margin-top:.35rem;font-size:.78rem;line-height:1.5;color:rgba(255,244,221,.78)}.leaflet-container{font:inherit;background:#e7dfcf}.leaflet-control-attribution{font-size:10px}.story-heading{letter-spacing:-.04em}.story-list li+li{margin-top:.75rem}.story-list li{position:relative;padding-inline-start:1.25rem}.story-list li::before{content:"";position:absolute;inset-inline-start:0;top:.55rem;width:.5rem;height:.5rem;border-radius:999px;background:var(--story-brass);box-shadow:0 0 0 4px rgba(182,132,47,.12)}.story-link{color:var(--story-palm)}.story-link:hover{color:#0a4838}.story-anchor{scroll-margin-top:7.5rem}@media (min-width:1024px){.story-card{padding:2rem}}
    </style>

    <div class="property-story-page min-h-screen" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
        @include('public.partials.market-nav', [
            'isAr' => $isRtl,
            'tenantCtx' => $tenantCtx,
            'navTx' => $listingNavTx,
            'navLinks' => $listingNavLinks,
            'navBrandHref' => $centralMarketplaceUrl,
            'navBrandLabel' => $listingNavTx['brand'],
            'sellWithUsUrl' => $tenantCtx && Route::has('tenant.sales-flow')
                ? route('tenant.sales-flow')
                : (Route::has('sales-flow') ? route('sales-flow') : '#'),
        ])

        <div x-data="{ idx: 0, imgs: @js($gallery) }" class="property-shell pb-10 pt-28 sm:pt-32 lg:pt-36">
            <section id="listing-gallery" class="story-anchor grid gap-6 lg:grid-cols-[1.02fr_.98fr]">
                <div class="story-surface story-panel story-card">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <div class="story-ornament"></div>
                            <p class="mt-6 text-xs font-bold uppercase tracking-[0.2em] text-[color:var(--story-brass)]">{{ __('Property Gallery') }}</p>
                            <h2 class="story-heading mt-3 text-3xl font-extrabold text-slate-900 sm:text-4xl">{{ __('See the property before you visit') }}</h2>
                        </div>
                        <div class="text-sm font-medium text-slate-500">{{ $photoCountLabel }}</div>
                    </div>

                    <div class="mt-6 space-y-4">
                        <div class="story-gallery-stage aspect-[4/3]">
                    <template x-if="imgs.length">
                        <img :src="imgs[idx]" alt="{{ $unit->translated_title ?: $propertyName }}" class="story-gallery-image" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 900 640%22%3E%3Crect fill=%22%23193027%22 width=%22900%22 height=%22640%22/%3E%3Ctext fill=%22%23f1e8d6%22 font-family=%22Arial%22 font-size=%2232%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22%3EListing image unavailable%3C/text%3E%3C/svg%3E'"/>
                    </template>

                    <template x-if="!imgs.length">
                        <div class="story-gallery-empty absolute inset-0 flex items-center justify-center">
                            <div class="px-8 text-center text-[rgba(255,245,223,.84)]">
                                <svg class="mx-auto mb-5 h-20 w-20 opacity-70" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                                <p class="text-xl font-semibold">{{ __('Property Photos Coming Soon') }}</p>
                            </div>
                        </div>
                    </template>

                    <template x-if="imgs.length > 1">
                        <div class="absolute inset-y-0 left-0 right-0 flex items-center justify-between px-4 sm:px-6">
                            <button type="button" @click="idx = (idx - 1 + imgs.length) % imgs.length" class="story-gallery-control"><svg class="mx-auto h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg></button>
                            <button type="button" @click="idx = (idx + 1) % imgs.length" class="story-gallery-control"><svg class="mx-auto h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg></button>
                        </div>
                    </template>

                    @if ($displayLocationLabel)
                        <div class="absolute {{ $isRtl ? 'left-4' : 'right-4' }} top-4 rounded-full border border-white/16 bg-[rgba(19,30,24,.48)] px-4 py-2 text-sm font-semibold text-white backdrop-blur">
                            {{ $displayLocationLabel }}
                        </div>
                    @endif
                </div>

                        <p class="text-sm leading-7 text-slate-600">{{ count($gallery) ? __('Browse the gallery to get a feel for the finishes, light, and layout of this property.') : __('Property visuals will appear here once media is added.') }}</p>

                        @if (count($gallery) > 1)
                            <div class="grid grid-cols-3 gap-3 sm:grid-cols-5">
                                <template x-for="(img, i) in imgs" :key="img + i">
                                    <button type="button" @click="idx = i" class="story-gallery-thumb overflow-hidden rounded-[1.35rem]" :class="idx === i ? 'is-active' : ''">
                                        <img :src="img" alt="" class="h-24 w-full object-cover sm:h-28">
                                    </button>
                                </template>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="story-surface story-hero-copy story-card">
                    <div class="relative z-10 flex h-full flex-col justify-between">
                        <div class="mb-5 flex flex-wrap gap-3 {{ $isRtl ? 'justify-end' : '' }}">
                            <span class="story-badge {{ $isSale ? 'story-badge-sale' : 'story-badge-rent' }}">{{ $isSale ? __('For Sale') : __('For Rent') }}</span>
                            <span class="story-badge story-badge-neutral">{{ $subcategoryName }}</span>
                            <span class="story-badge story-badge-neutral">{{ $statusLabel }}</span>
                        </div>
                        @if ($categoryName)
                            <p class="story-kicker">{{ $categoryName }}</p>
                        @endif
                        <h1 class="story-heading mt-4 text-4xl font-extrabold leading-[1.02] sm:text-5xl lg:text-[4rem]">{{ $unit->translated_title ?: $propertyName }}</h1>
                        <div class="mt-5 flex flex-wrap items-center gap-3 text-sm text-white/78 {{ $isRtl ? 'justify-end' : '' }}">
                            <span class="rounded-full border border-white/12 bg-white/8 px-4 py-2 font-semibold">{{ __('Unit') }} {{ $unit->code }}</span>
                            @if ($propertyName && $propertyName !== ($unit->translated_title ?: $propertyName))
                                <span class="rounded-full border border-white/12 bg-white/8 px-4 py-2 font-semibold">{{ $propertyName }}</span>
                            @elseif (! $unit->property)
                                <span class="rounded-full border border-white/12 bg-white/8 px-4 py-2 font-semibold">{{ __('Standalone listing') }}</span>
                            @endif
                            <span class="rounded-full border border-white/12 bg-white/8 px-4 py-2 font-semibold">{{ $displayLocationLabel }}</span>
                        </div>
                        <p class="mt-6 max-w-2xl text-base leading-8 text-white/78">{{ $storyText }}</p>
                    </div>

                    <div class="mt-8 space-y-5">
                        <div class="rounded-[1.8rem] border border-white/12 bg-white/10 p-5 backdrop-blur">
                            <div class="text-xs font-bold uppercase tracking-[0.22em] text-[rgba(255,244,221,.68)]">{{ $isSale ? __('Asking Price') : __('Annual Rent') }}</div>
                            <div class="mt-3 text-4xl font-extrabold tracking-[-0.04em] text-[#fff7ea] sm:text-5xl">{{ $unitCurr }} {{ number_format((float) $displayPrice, 0) }}</div>
                            <div class="mt-2 text-sm text-white/68">{{ $isSale ? __('Available for purchase.') : __('Presented as a yearly rental figure.') }}</div>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="story-stat"><div class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">{{ __('Area') }}</div><div class="mt-2 text-2xl font-extrabold text-slate-900">{{ $areaDisplay }}</div><div class="mt-1 text-sm text-slate-500">{{ $sizeValue ? $sizeLabel : __('Area not specified yet.') }}</div></div>
                            <div class="story-stat"><div class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">{{ __('Bathrooms') }}</div><div class="mt-2 text-2xl font-extrabold text-slate-900">{{ $bathDisplay }}</div><div class="mt-1 text-sm text-slate-500">{{ __('Bedrooms') }}: {{ $bedDisplay }}</div></div>
                            <div class="story-stat"><div class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">{{ __('Property Gallery') }}</div><div class="mt-2 text-2xl font-extrabold text-slate-900">{{ count($gallery) }}</div><div class="mt-1 text-sm text-slate-500">{{ __('Curated visuals for this listing.') }}</div></div>
                            <div class="story-stat"><div class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">{{ __('Location') }}</div><div class="mt-2 text-xl font-extrabold text-slate-900">{{ $displayLocationLabel }}</div><div class="mt-1 text-sm text-slate-500">{{ __('Grounded in the listing location.') }}</div></div>
                        </div>
                    </div>
                </div>
            </section>

        </div>

        <div class="property-shell pb-16">
            <div class="grid gap-8 lg:grid-cols-[1.24fr_.76fr]">
                <div class="space-y-8">
                    <section class="story-anchor story-surface story-panel story-card" id="listing-story">
                        <div class="story-ornament"></div>
                        <h2 class="story-heading mt-6 text-3xl font-extrabold text-slate-900 sm:text-4xl">{{ __('About This Property') }}</h2>
                        <div class="mt-5 space-y-5 text-base leading-8 text-slate-700">
                            <p>{{ $storyText }}</p>
                            @foreach ($summaryNotes as $note)
                                <div class="story-soft-note">{{ $note }}</div>
                            @endforeach
                        </div>
                    </section>

                    <section class="story-anchor story-surface story-card" id="listing-details">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <div class="story-ornament"></div>
                                <h2 class="story-heading mt-6 text-3xl font-extrabold text-slate-900 sm:text-4xl">{{ __('Property Details') }}</h2>
                            </div>
                            <div class="text-sm font-medium text-slate-500">{{ __('The essentials most visitors look for first.') }}</div>
                        </div>

                        <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                            @foreach ($overviewFacts as $fact)
                                <div class="story-fact">
                                    <div class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">{{ $fact['label'] }}</div>
                                    <div class="mt-3 text-lg font-extrabold leading-tight text-slate-900">{{ $fact['value'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </section>

                    <section class="story-surface story-card">
                        <div class="story-ornament"></div>
                        <h2 class="story-heading mt-6 text-3xl font-extrabold text-slate-900 sm:text-4xl">{{ __('Space and layout') }}</h2>

                        <div class="mt-8 grid gap-4 md:grid-cols-2">
                            <div class="story-fact"><div class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">{{ __('Type') }}</div><div class="mt-3 text-2xl font-extrabold text-slate-900">{{ $subcategoryName }}</div><p class="mt-2 text-sm leading-7 text-slate-600">{{ __('Presented under the :category category.', ['category' => $categoryName ?: __('core listing')]) }}</p></div>
                            <div class="story-fact"><div class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">{{ __('Area profile') }}</div><div class="mt-3 text-2xl font-extrabold text-slate-900">{{ $sizeValue ? number_format((float) $sizeValue, 0) . ' ' . $sizeLabel : __('Area not specified on this listing yet.') }}</div><p class="mt-2 text-sm leading-7 text-slate-600">{{ $sizeValue ? __('A useful first read for comparing space and fit.') : __('Area details will appear here once they are added to the listing.') }}</p></div>
                            <div class="story-fact"><div class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">{{ __('Baths / Beds') }}</div><div class="mt-3 text-2xl font-extrabold text-slate-900">{{ $bathDisplay }} / {{ $bedDisplay }}</div><p class="mt-2 text-sm leading-7 text-slate-600">{{ __('A quick read of the current room count.') }}</p></div>
                            <div class="story-fact"><div class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">{{ __('Availability') }}</div><div class="mt-3 text-2xl font-extrabold text-slate-900">{{ $statusLabel }}</div><p class="mt-2 text-sm leading-7 text-slate-600">{{ __('Current availability for this listing.') }}</p></div>
                        </div>
                    </section>

                    <section class="story-surface story-card">
                        <div class="story-ornament"></div>
                        <h2 class="story-heading mt-6 text-3xl font-extrabold text-slate-900 sm:text-4xl">{{ __('Property Features') }}</h2>

                        @if ($unit->unitAttributes->count() > 0)
                            <div class="mt-8 grid gap-4 md:grid-cols-2">
                                @foreach ($unit->unitAttributes->sortBy(fn($a) => $a->attributeField?->sort ?? 999) as $attribute)
                                    @if ($attribute->attributeField)
                                        <div class="story-fact">
                                            <div class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">{{ $attribute->attributeField->translated_label }}</div>
                                            <div class="mt-3 text-lg font-extrabold text-slate-900">{{ $attribute->formatted_value ?? __('Not specified') }}</div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <div class="mt-8 story-soft-note text-sm leading-7 text-slate-700">{{ __('More detailed features will appear here as they are added to the listing.') }}</div>
                        @endif

                        @if ($recordFacts !== [])
                            <div class="story-divider mt-8 pt-8">
                                <h3 class="text-2xl font-extrabold text-slate-900">{{ __('Official property record') }}</h3>
                                <div class="mt-5 grid gap-4 md:grid-cols-2">
                                    @foreach ($recordFacts as $label => $value)
                                        <div class="story-fact">
                                            <div class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">{{ $label }}</div>
                                            <div class="mt-3 text-lg font-extrabold text-slate-900">{{ $value }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </section>

                    <section class="story-anchor story-surface story-card story-map-card" id="listing-location">
                        <div class="story-ornament"></div>
                        <h2 class="story-heading mt-6 text-3xl font-extrabold text-slate-900 sm:text-4xl">{{ __('Location & Neighborhood') }}</h2>

                        <div class="mt-8 grid gap-5 lg:grid-cols-[.86fr_1.14fr]">
                            <div class="space-y-5">
                                <div class="story-fact">
                                    <div class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">{{ __('Listing location') }}</div>
                                    <div class="mt-3 text-2xl font-extrabold text-slate-900">{{ $displayLocationLabel }}</div>
                                    <p class="mt-3 text-sm leading-7 text-slate-600">{{ $locationLabel ? __('Location details available on this listing.') : __('More exact location details will be added soon.') }}</p>
                                </div>

                                <div class="story-fact">
                                    <div class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">{{ __('Map status') }}</div>
                                    <div class="mt-3 text-lg font-extrabold text-slate-900">{{ $mapPrecisionLabel }}</div>
                                    <p class="mt-3 text-sm leading-7 text-slate-600">
                                        @if ($mapMode === 'approximate')
                                            {{ __('The saved listing points to the area rather than a surveyed exact pin, so the marker is shown at city level for orientation.') }}
                                        @elseif ($hasInteractiveMap)
                                            {{ __('The map now gives visitors a spatial read of the listing before they decide on the next step.') }}
                                        @else
                                            {{ __('More exact location details will be added soon.') }}
                                        @endif
                                    </p>
                                    @if ($mapHref)
                                        <a href="{{ $mapHref }}" target="_blank" rel="noopener noreferrer" class="story-link mt-4 inline-flex items-center gap-2 text-sm font-bold">{{ __('Open location') }}<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7m0 0v7m0-7L10 14"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5v14h14"/></svg></a>
                                    @endif
                                </div>
                            </div>

                            <div class="story-fact">
                                @if ($hasInteractiveMap)
                                    <div class="story-map-wrap">
                                        <div id="listing-map"
                                            class="story-map-canvas"
                                            data-lat="{{ $mapLat }}"
                                            data-lng="{{ $mapLng }}"
                                            data-zoom="{{ $mapZoom }}"
                                            data-title="{{ $unit->translated_title ?: $propertyName }}"
                                            data-meta="{{ $mapTooltipMeta }}"
                                            data-approximate="{{ $mapMode === 'approximate' ? '1' : '0' }}"
                                            data-is-rtl="{{ $isRtl ? '1' : '0' }}">
                                        </div>
                                        <div class="story-map-note {{ $isRtl ? 'story-map-note--start' : 'story-map-note--end' }}">{{ $mapMode === 'approximate' ? __('Approximate area pin') : __('Listing pin') }}</div>
                                    </div>
                                @else
                                    <div class="story-map-helper">
                                        <div class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">{{ __('Map preview') }}</div>
                                        <div class="mt-3 text-lg font-extrabold text-slate-900">{{ __('Map pin not available yet.') }}</div>
                                        <p class="mt-3 text-sm leading-7 text-slate-600">{{ __('Save either coordinates or a precise map link on the unit to render an interactive property pin here.') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </section>
                </div>

                <aside class="space-y-6">
                    <section class="story-surface story-panel story-card lg:sticky lg:top-8">
                        <div class="story-ornament"></div>
                        <h2 class="story-heading mt-6 text-3xl font-extrabold text-slate-900">{{ __('Quick overview') }}</h2>
                        <p class="mt-4 text-sm leading-7 text-slate-600">{{ __('A quick summary of the facts most visitors want to confirm first.') }}</p>

                        <div class="mt-6 space-y-3">
                            <div class="story-fact"><div class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">{{ $isSale ? __('Asking Price') : __('Annual Rent') }}</div><div class="mt-3 text-2xl font-extrabold text-slate-900">{{ $unitCurr }} {{ number_format((float) $displayPrice, 0) }}</div><div class="mt-2 text-sm text-slate-500">{{ $isSale ? __('Sale listing') : __('Rent listing') }}</div></div>
                            <div class="story-fact"><div class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">{{ __('Location') }}</div><div class="mt-3 text-lg font-extrabold text-slate-900">{{ $displayLocationLabel }}</div></div>
                        </div>

                        <div class="story-divider mt-6 pt-6">
                            <div class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">{{ __('Highlights') }}</div>
                            <ul class="story-list mt-4 text-sm leading-7 text-slate-700">
                                @foreach ($quickSummary as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </section>
                </aside>
            </div>
        </div>
    </div>

    @if ($hasInteractiveMap)
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const mapEl = document.getElementById('listing-map');
                if (!mapEl || typeof window.L === 'undefined') {
                    return;
                }

                const lat = Number(mapEl.dataset.lat);
                const lng = Number(mapEl.dataset.lng);
                const zoom = Number(mapEl.dataset.zoom || 15);
                const tooltipTitle = mapEl.dataset.title || '';
                const tooltipMeta = mapEl.dataset.meta || '';
                const isApproximate = mapEl.dataset.approximate === '1';
                const isRtl = mapEl.dataset.isRtl === '1';

                if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
                    return;
                }

                const map = L.map(mapEl, {
                    scrollWheelZoom: false,
                    dragging: true,
                    tap: false,
                }).setView([lat, lng], zoom);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors',
                    maxZoom: 19,
                }).addTo(map);

                L.circle([lat, lng], {
                    radius: isApproximate ? 900 : 320,
                    color: '#b6842f',
                    weight: 1,
                    fillColor: '#b6842f',
                    fillOpacity: 0.12,
                }).addTo(map);

                const marker = L.circleMarker([lat, lng], {
                    radius: 10,
                    color: '#fff4dc',
                    weight: 3,
                    fillColor: '#b6842f',
                    fillOpacity: 1,
                }).addTo(map);

                const bubbleHtml = `
                    <div class="story-map-bubble" dir="${isRtl ? 'rtl' : 'ltr'}">
                        <div class="story-map-bubble-title">${tooltipTitle}</div>
                        <div class="story-map-bubble-meta">${tooltipMeta}</div>
                    </div>
                `;

                marker.bindTooltip(bubbleHtml, {
                    permanent: true,
                    direction: 'top',
                    offset: [0, -18],
                    className: 'story-map-tooltip',
                    opacity: 1,
                }).openTooltip();

                marker.bindPopup(bubbleHtml, {
                    closeButton: false,
                    offset: [0, -8],
                });

                map.on('click', () => marker.openPopup());
                window.setTimeout(() => map.invalidateSize(), 180);
            });
        </script>
    @endif
@endsection
