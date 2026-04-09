@php
    // NativePHP: unit not in local SQLite — render JS-loading shell
    if (!$unit) {
        $apiBase = config('nativephp.remote_api_url', '');
        $code = $unitCode ?? '';
        echo view('mobile.units.show-shell', ['unitCode' => $code, 'apiBase' => $apiBase])->render();
        exit;
    }
@endphp
@extends('mobile.layouts.app', [
    'title' => $unit->translated_title ?: $unit->code,
    'show_back_button' => false,
    'body_class' => 'mobile-unit-shell',
])

@section('full_width', true)

@php
    use App\Models\Unit as UnitModel;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $isAr = app()->getLocale() === 'ar';
    $tenantSettings = is_array($unit->tenant?->settings ?? null) ? $unit->tenant->settings : [];

    $publicUrl = function (?string $path) {
        if (! is_string($path) || trim($path) === '') {
            return null;
        }

        $path = trim($path);

        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        if (Str::startsWith($path, ['/storage/', 'storage/'])) {
            return url('/' . ltrim($path, '/'));
        }

        return url(Storage::disk('public')->url($path));
    };

    $gallery = collect(is_array($unit->photos) ? $unit->photos : [])
        ->map(fn ($image) => is_string($image) && $image !== '' ? $publicUrl($image) : null)
        ->filter()
        ->values()
        ->all();

    $displayTitle = $unit->translated_title ?: ($unit->property?->name ?? $unit->code);
    $displayDescription = trim((string) ($unit->translated_description ?: ''));
    $propertyName = $unit->property?->name ?? ($unit->translated_title ?: __('Standalone listing'));
    $tenantName = $unit->tenant?->name ?? __('Agency');
    $tenantLogo = $publicUrl($tenantSettings['logo_url'] ?? null);
    $tenantInitials = Str::of($tenantName)->squish()->explode(' ')->filter()->take(2)->map(fn ($part) => Str::upper(Str::substr($part, 0, 1)))->implode('');

    $isSale = ($unit->listing_type ?? UnitModel::LISTING_RENT) === UnitModel::LISTING_SALE;
    $statusLabel = UnitModel::statusLabels()[$unit->status] ?? Str::headline((string) $unit->status);
    $displayPrice = $isSale
        ? ($unit->price ?? 0)
        : ($unit->market_rent && $unit->market_rent > 0 ? $unit->market_rent : ($unit->price ?? 0));
    $currency = $unit->currency ?? 'JOD';

    $categoryName = $unit->subcategory?->category?->name;
    $subcategoryName = $unit->subcategory?->name ?? __('Property');
    $cityName = $isAr ? ($unit->city?->name_ar ?? $unit->city?->name_en) : ($unit->city?->name_en ?? $unit->city?->name_ar);
    $areaName = $isAr ? ($unit->area?->name_ar ?? $unit->area?->name_en) : ($unit->area?->name_en ?? $unit->area?->name_ar);

    $locationBits = array_values(array_filter([$unit->location, $areaName, $cityName]));
    if ($locationBits === []) {
        $locationBits = array_values(array_filter([
            data_get($unit->officialInfo, 'village'),
            data_get($unit->officialInfo, 'directorate'),
        ]));
    }
    $locationLabel = implode(' / ', $locationBits);
    $displayLocation = $locationLabel ?: __('Location details coming soon');

    $bedValue = $unit->beds ?: $unit->bedrooms;
    $bathValue = $unit->baths ?: $unit->bathrooms;
    $totalSqm = data_get($unit->officialInfo, 'areas.total_sqm');
    $builtSqm = data_get($unit->officialInfo, 'areas.built_sqm');
    $landSqm = data_get($unit->officialInfo, 'areas.land_sqm');
    $sizeValue = $unit->sqft ?: $totalSqm ?: $builtSqm ?: $landSqm ?: $unit->area_m2;
    $sizeLabel = $unit->sqft ? __('Sq Ft') : __('Sq M');

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

    $featureFacts = $unit->unitAttributes
        ->filter(fn ($attribute) => $attribute->attributeField && filled($attribute->formatted_value))
        ->values();

    $summaryNotes = array_values(array_filter([
        $unit->property
            ? ($isAr ? "جزء من مجموعة {$propertyName}." : "Part of the {$propertyName} collection.")
            : ($isAr ? 'يُعرض كإعلان مستقل.' : 'Presented as an independent listing.'),
        $locationLabel
            ? ($isAr ? "يقع في {$locationLabel}." : "Located in {$locationLabel}.")
            : ($isAr ? 'سيتم تحديث تفاصيل الموقع قريباً.' : 'Location details will be updated soon.'),
        count($gallery)
            ? ($isAr ? 'يتضمن ' . count($gallery) . ' صوراً للعقار.' : 'Includes ' . count($gallery) . ' listing photos.')
            : ($isAr ? 'ستضاف صور العقار قريباً.' : 'Listing visuals will be added soon.'),
    ]));

    if ($displayDescription === '') {
        $displayDescription = $isSale
            ? __('A sales listing prepared to help buyers understand the space, the setting, and the official details before they take the next step.')
            : __('A rental listing prepared to help tenants read the space, location, and practical details before they continue.');
    }

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

        $locationNeedle = strtolower((string) ($displayLocation . ' ' . ($mapHref ?? '')));
        foreach ($approximateCityPins as $needle => $pin) {
            if (str_contains($locationNeedle, $needle)) {
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
        'exact' => $isAr ? 'نقطة دقيقة من الإحداثيات المحفوظة.' : 'Exact pin from saved coordinates.',
        'url' => $isAr ? 'تم تحديد النقطة من رابط الموقع المحفوظ.' : 'Pin resolved from the saved map link.',
        'approximate' => $isAr ? 'نقطة تقريبية مبنية على الموقع المحفوظ للعقار.' : 'Approximate area pin based on the saved listing location.',
        default => $isAr ? 'لا توجد نقطة خريطة حالياً.' : 'Map pin not available yet.',
    };

    $mapTooltipMeta = implode(' · ', array_values(array_filter([
        $isSale ? __('For Sale') : __('For Rent'),
        $currency . ' ' . number_format((float) $displayPrice, 0),
        $displayLocation,
        $mapMode === 'approximate'
            ? ($isAr ? 'منطقة تقريبية' : 'Approximate area')
            : ($isAr ? 'موقع مثبت' : 'Pinned location'),
    ])));

    $tenantUrl = $unit->tenant ? route('mobile.tenants.show', $unit->tenant) : route('mobile.marketplace');

    $tx = [
        'propertyStory' => $isAr ? 'حكاية العقار' : 'Property Story',
        'agencyProfile' => $isAr ? 'واجهة الوكالة' : 'Agency Profile',
        'backMarketplace' => $isAr ? 'العودة إلى السوق' : 'Back to Marketplace',
        'liveMap' => $isAr ? 'الخريطة الحية' : 'Live Map',
        'overview' => $isAr ? 'نظرة سريعة' : 'Overview',
        'whyStandout' => $isAr ? 'لماذا يستحق هذا العقار الانتباه' : 'Why this property stands out',
        'details' => $isAr ? 'تفاصيل العقار' : 'Property Details',
        'detailsSubtitle' => $isAr ? 'الأساسيات التي يبحث عنها الزائر أولاً' : 'The essentials visitors look for first',
        'features' => $isAr ? 'الميزات' : 'Features',
        'featuresSubtitle' => $isAr ? 'المساحة والتجهيزات والحقائق المهمة' : 'Space, finishes, and useful facts',
        'recordTitle' => $isAr ? 'البيانات الرسمية' : 'Official Record',
        'recordSubtitle' => $isAr ? 'حقائق السجل العقاري' : 'Property record facts',
        'locationTitle' => $isAr ? 'الموقع' : 'Location',
        'locationSubtitle' => $isAr ? 'اقرأ المكان قبل الخطوة التالية' : 'Read the setting before the next step',
        'propertyType' => $isAr ? 'فئة العقار' : 'Property Type',
        'listingStatus' => $isAr ? 'حالة الإعلان' : 'Listing Status',
        'gallery' => $isAr ? 'المعرض' : 'Gallery',
        'listingLocation' => $isAr ? 'موقع الإعلان' : 'Listing Location',
        'openLocation' => $isAr ? 'فتح الموقع' : 'Open Location',
        'viewProperty' => $isAr ? 'عرض العقار' : 'View Property',
        'mapMissing' => $isAr ? 'لا توجد نقطة خريطة حالياً.' : 'Map pin not available yet.',
        'mapMissingHint' => $isAr ? 'أضف إحداثيات أو رابط موقع للوحدة كي تظهر هنا خريطة تفاعلية.' : 'Save coordinates or a map link on the unit to render an interactive pin here.',
        'featuresEmpty' => $isAr ? 'ستظهر الميزات التفصيلية هنا عندما تضاف إلى العقار.' : 'Detailed features will appear here once they are added to the listing.',
        'salePriceLead' => $isAr ? 'سعر البيع ظاهر بوضوح من البداية.' : 'Truthful sale pricing shown up front.',
        'rentPriceLead' => $isAr ? 'قيمة الإيجار السنوية ظاهرة بوضوح من البداية.' : 'Yearly rental figure shown up front.',
        'notSpecified' => $isAr ? 'غير محدد' : 'Not specified',
        'photosCount' => $isAr ? ':count صور' : ':count photos',
        'noPhotos' => $isAr ? 'لا توجد صور مرفوعة بعد.' : 'No photos uploaded yet.',
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
        --mus-ink:#1f2a24;
        --mus-palm:#0f5a46;
        --mus-river:#2f7a72;
        --mus-brass:#b6842f;
        --mus-clay:#9d5a3b;
        --mus-line:rgba(130,94,38,.16);
    }
    body.mobile-unit-shell{
        background:
            radial-gradient(circle at top left, rgba(182,132,47,.14), transparent 22%),
            radial-gradient(circle at top right, rgba(15,90,70,.12), transparent 24%),
            linear-gradient(180deg, #eee2cc 0, #f6efdf 300px, #fbf7ef 100%);
        color:var(--mus-ink);
        font-family:'Manrope',system-ui,sans-serif;
    }
    html[dir="rtl"] body.mobile-unit-shell{
        font-family:'Cairo','Manrope',system-ui,sans-serif;
    }
    body.mobile-unit-shell header.sticky{
        background:linear-gradient(145deg, rgba(15,32,26,.96), rgba(15,90,70,.9) 56%, rgba(48,33,15,.84));
        box-shadow:0 16px 36px -24px rgba(28,22,10,.55);
    }
    body.mobile-unit-shell aside{
        background:rgba(252,248,241,.98);
        color:var(--mus-ink);
    }
    body.mobile-unit-shell aside .bg-gradient-to-br.from-emerald-600.to-emerald-700{
        background:linear-gradient(145deg, rgba(15,32,26,.96), rgba(15,90,70,.9) 56%, rgba(48,33,15,.86)) !important;
    }
    .mus-page{min-height:100vh;padding-bottom:2.5rem}
    .mus-shell{padding-inline:1rem}
    .mus-hero{
        position:relative;
        overflow:hidden;
        border-radius:2rem;
        background:
            radial-gradient(circle at top left, rgba(255,255,255,.16), transparent 28%),
            linear-gradient(145deg, rgba(15,32,26,.96), rgba(15,90,70,.9) 54%, rgba(48,33,15,.84));
        color:#fff8ea;
        box-shadow:0 30px 64px -34px rgba(28,22,10,.58);
    }
    .mus-hero::after{
        content:"";
        position:absolute;
        inset:0;
        background:
            linear-gradient(180deg, rgba(10,16,13,.04), rgba(10,16,13,.22)),
            radial-gradient(circle at 85% 14%, rgba(255,255,255,.08), transparent 24%);
        pointer-events:none;
    }
    .mus-hero-copy{position:relative;z-index:1}
    .mus-ornament{
        height:10px;
        width:110px;
        border-radius:999px;
        background:
            linear-gradient(90deg, rgba(15,90,70,.16), rgba(182,132,47,.34), rgba(15,90,70,.16)),
            repeating-linear-gradient(90deg, transparent 0 10px, rgba(182,132,47,.58) 10px 14px, transparent 14px 24px);
    }
    .mus-kicker{
        font-size:.72rem;
        font-weight:800;
        letter-spacing:.22em;
        text-transform:uppercase;
        color:rgba(255,241,212,.74);
    }
    .mus-badge{
        display:inline-flex;
        align-items:center;
        gap:.5rem;
        border-radius:999px;
        padding:.46rem .8rem;
        font-size:.65rem;
        font-weight:800;
        letter-spacing:.14em;
        text-transform:uppercase;
        backdrop-filter:blur(10px);
    }
    .mus-badge::before{
        content:"";
        width:.45rem;
        height:.45rem;
        border-radius:999px;
        background:currentColor;
        opacity:.86;
    }
    .mus-badge-sale{background:rgba(182,132,47,.16);border:1px solid rgba(255,235,201,.18);color:#ffe1aa}
    .mus-badge-rent{background:rgba(15,90,70,.16);border:1px solid rgba(220,252,244,.15);color:#dbf8ef}
    .mus-badge-soft{background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.12);color:rgba(255,248,235,.9)}
    .mus-chip{
        display:inline-flex;
        align-items:center;
        gap:.5rem;
        border-radius:999px;
        border:1px solid rgba(255,255,255,.14);
        background:rgba(255,255,255,.08);
        padding:.7rem .95rem;
        font-size:.68rem;
        font-weight:800;
        letter-spacing:.14em;
        text-transform:uppercase;
        color:rgba(255,248,236,.88);
        backdrop-filter:blur(12px);
    }
    .mus-chip::before{
        content:"";
        width:.45rem;
        height:.45rem;
        border-radius:999px;
        background:var(--mus-brass);
        box-shadow:0 0 0 4px rgba(182,132,47,.16);
    }
    .mus-gallery{
        position:relative;
        overflow:hidden;
        border-radius:1.7rem;
        border:1px solid rgba(255,255,255,.12);
        background:linear-gradient(145deg, rgba(248,240,221,.24), rgba(255,255,255,.08));
        box-shadow:0 24px 48px -32px rgba(0,0,0,.5);
    }
    .mus-gallery-stage{
        position:relative;
        aspect-ratio: 16 / 12;
        overflow:hidden;
        background:#e6dcc9;
    }
    .mus-gallery-stage img{
        width:100%;
        height:100%;
        object-fit:cover;
        transition:transform .45s ease;
    }
    .mus-gallery:hover .mus-gallery-stage img{transform:scale(1.03)}
    .mus-gallery-control{
        position:absolute;
        top:50%;
        z-index:4;
        display:flex;
        height:2.4rem;
        width:2.4rem;
        align-items:center;
        justify-content:center;
        border-radius:999px;
        background:rgba(14,19,17,.48);
        color:#fff;
        transform:translateY(-50%);
        backdrop-filter:blur(10px);
    }
    .mus-gallery-control.prev{left:.75rem}
    .mus-gallery-control.next{right:.75rem}
    .mus-gallery-count{
        position:absolute;
        right:.85rem;
        bottom:.85rem;
        z-index:4;
        border-radius:999px;
        background:rgba(0,0,0,.42);
        padding:.45rem .75rem;
        font-size:.72rem;
        font-weight:800;
        color:#fff8ea;
        backdrop-filter:blur(10px);
    }
    .mus-thumbs{
        display:flex;
        gap:.55rem;
        overflow-x:auto;
        padding:.85rem;
        -ms-overflow-style:none;
        scrollbar-width:none;
    }
    .mus-thumbs::-webkit-scrollbar{display:none}
    .mus-thumb{
        flex:0 0 auto;
        width:4rem;
        height:4rem;
        overflow:hidden;
        border-radius:1rem;
        border:1px solid rgba(255,255,255,.14);
        background:rgba(255,255,255,.1);
        opacity:.7;
        transition:opacity .18s ease, transform .18s ease, border-color .18s ease;
    }
    .mus-thumb.is-active{
        opacity:1;
        transform:translateY(-1px);
        border-color:rgba(255,248,235,.56);
        box-shadow:0 12px 24px -18px rgba(0,0,0,.4);
    }
    .mus-thumb img{width:100%;height:100%;object-fit:cover}
    .mus-fact-grid{
        display:grid;
        gap:.7rem;
        grid-template-columns:repeat(3, minmax(0, 1fr));
    }
    .mus-fact{
        border:1px solid rgba(255,255,255,.12);
        background:rgba(255,255,255,.08);
        border-radius:1.2rem;
        padding:.9rem .85rem;
        backdrop-filter:blur(14px);
    }
    .mus-fact-label{
        font-size:.62rem;
        letter-spacing:.18em;
        text-transform:uppercase;
        color:rgba(255,244,221,.62);
        font-weight:700;
    }
    .mus-fact-value{
        margin-top:.35rem;
        font-size:.95rem;
        line-height:1.25;
        font-weight:800;
        color:#fff8ea;
    }
    .mus-price-card{
        border:1px solid rgba(255,255,255,.12);
        background:rgba(255,255,255,.1);
        box-shadow:0 26px 56px -34px rgba(0,0,0,.48);
        backdrop-filter:blur(18px);
    }
    .mus-surface{
        border:1px solid var(--mus-line);
        background:linear-gradient(180deg, rgba(255,249,239,.98), rgba(247,237,214,.92));
        box-shadow:0 24px 54px -36px rgba(57,42,16,.3);
    }
    .mus-card{
        border-radius:1.7rem;
        padding:1.2rem;
    }
    .mus-section-kicker{
        font-size:.68rem;
        font-weight:800;
        letter-spacing:.2em;
        text-transform:uppercase;
        color:var(--mus-brass);
    }
    .mus-section-title{
        margin-top:.4rem;
        font-size:1.65rem;
        line-height:1.06;
        font-weight:900;
        letter-spacing:-.04em;
        color:var(--mus-ink);
    }
    .mus-soft-note{
        border-radius:1.2rem;
        border:1px solid rgba(130,94,38,.12);
        background:rgba(255,250,242,.84);
        padding:1rem;
    }
    .mus-grid{
        display:grid;
        gap:.75rem;
    }
    .mus-detail{
        border-radius:1.2rem;
        border:1px solid rgba(130,94,38,.14);
        background:rgba(255,251,244,.88);
        padding:1rem;
    }
    .mus-detail-label{
        font-size:.65rem;
        font-weight:800;
        letter-spacing:.16em;
        text-transform:uppercase;
        color:#747770;
    }
    .mus-detail-value{
        margin-top:.55rem;
        font-size:1rem;
        line-height:1.45;
        font-weight:800;
        color:var(--mus-ink);
    }
    .mus-map-wrap{
        overflow:hidden;
        border-radius:1.45rem;
        border:1px solid rgba(130,94,38,.14);
        background:linear-gradient(145deg, rgba(248,240,221,.95), rgba(244,229,199,.92));
        box-shadow:0 24px 54px -38px rgba(57,42,16,.3);
    }
    .mus-map{
        height:260px;
        width:100%;
    }
    .mus-map-popup{
        min-width:180px;
        font-family:inherit;
    }
    .mus-map-popup-title{
        font-size:.9rem;
        line-height:1.3;
        font-weight:800;
        color:var(--mus-ink);
    }
    .mus-map-popup-meta{
        margin-top:.3rem;
        font-size:.75rem;
        line-height:1.5;
        color:#63675f;
    }
    .mus-map-popup-link{
        margin-top:.55rem;
        display:inline-flex;
        align-items:center;
        gap:.35rem;
        font-size:.72rem;
        font-weight:800;
        letter-spacing:.12em;
        text-transform:uppercase;
        color:var(--mus-palm);
        text-decoration:none;
    }
    .mus-link{
        color:var(--mus-palm);
        font-weight:800;
    }
    .mus-link:hover{color:#0a4838}
    .mobile-unit-shell .leaflet-container{
        background:#e7ddca;
        font:inherit;
    }
    .mus-modal{
        position:fixed;
        inset:0;
        z-index:60;
        display:none;
        background:rgba(7,10,9,.92);
        backdrop-filter:blur(10px);
    }
    .mus-modal.is-open{display:flex}
    .mus-modal img{max-width:100%;max-height:100%;object-fit:contain}
</style>
@endpush

@section('content')
<div class="mus-page">
    <section class="mus-shell pt-4">
        <div class="mus-hero">
            <div class="mus-hero-copy px-4 pb-5 pt-5">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="mus-kicker">{{ $tx['propertyStory'] }}</p>
                        <div class="mt-3 mus-ornament"></div>
                    </div>
                    <span class="mus-chip">{{ $tenantName }}</span>
                </div>

                <div class="mt-5 mus-gallery" x-data="mobileUnitGallery(@js($gallery))">
                    <div class="mus-gallery-stage">
                        <template x-if="hasImages">
                            <img :src="images[index]" alt="{{ $displayTitle }}" @click="open()" loading="eager">
                        </template>
                        <template x-if="!hasImages">
                            <div class="flex h-full items-center justify-center bg-gradient-to-br from-[#163129] via-[#0f5a46] to-[#3a2410]">
                                <svg class="h-16 w-16 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 22V12h6v10"/>
                                </svg>
                            </div>
                        </template>
                        <template x-if="images.length > 1">
                            <button type="button" class="mus-gallery-control prev" @click.stop="previous()">
                                <svg class="h-4 w-4 {{ $isAr ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                        </template>
                        <template x-if="images.length > 1">
                            <button type="button" class="mus-gallery-control next" @click.stop="next()">
                                <svg class="h-4 w-4 {{ $isAr ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </template>
                        <template x-if="hasImages">
                            <div class="mus-gallery-count" x-text="`${index + 1}/${images.length}`"></div>
                        </template>
                    </div>

                    <template x-if="images.length > 1">
                        <div class="mus-thumbs">
                            <template x-for="(image, thumbIndex) in images" :key="`${image}-${thumbIndex}`">
                                <button type="button" class="mus-thumb" :class="{ 'is-active': index === thumbIndex }" @click="go(thumbIndex)">
                                    <img :src="image" alt="" loading="lazy">
                                </button>
                            </template>
                        </div>
                    </template>

                    <div class="mus-modal" :class="{ 'is-open': fullscreen }" @click.self="close()">
                        <button type="button" class="absolute {{ $isAr ? 'left-4' : 'right-4' }} top-4 z-10 rounded-full bg-white/10 p-3 text-white backdrop-blur-sm transition hover:bg-white/20" @click="close()">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                        <button type="button" class="mus-gallery-control prev {{ $isAr ? 'right-4 left-auto' : '' }}" @click.stop="previous()" x-show="images.length > 1">
                            <svg class="h-4 w-4 {{ $isAr ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button type="button" class="mus-gallery-control next {{ $isAr ? 'left-4 right-auto' : '' }}" @click.stop="next()" x-show="images.length > 1">
                            <svg class="h-4 w-4 {{ $isAr ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                        <div class="flex h-full w-full items-center justify-center p-5">
                            <img :src="images[index]" alt="{{ $displayTitle }}">
                        </div>
                    </div>
                </div>

                <div class="mt-5 flex flex-wrap gap-2">
                    <span class="mus-badge {{ $isSale ? 'mus-badge-sale' : 'mus-badge-rent' }}">{{ $isSale ? __('For Sale') : __('For Rent') }}</span>
                    <span class="mus-badge mus-badge-soft">{{ $statusLabel }}</span>
                    <span class="mus-badge mus-badge-soft">{{ $subcategoryName }}</span>
                </div>

                <div class="mt-4">
                    @if ($categoryName)
                        <p class="text-[10px] font-black uppercase tracking-[0.22em] text-white/58">{{ $categoryName }}</p>
                    @endif
                    <h1 class="mt-2 text-[2.3rem] font-black leading-none tracking-[-0.045em] text-white">{{ $displayTitle }}</h1>
                    <p class="mt-3 text-[13px] leading-6 text-white/78">{{ $displayLocation }}</p>
                </div>

                <div class="mus-price-card mt-4 rounded-[1.6rem] p-4">
                    <div class="text-[10px] font-black uppercase tracking-[0.22em] text-white/58">
                        {{ $isSale ? __('Asking Price') : __('Annual Rent') }}
                    </div>
                    <div class="mt-3 text-[2.2rem] font-black leading-none tracking-[-0.04em] text-[#fff7ea]">
                        {{ $currency }} {{ number_format((float) $displayPrice, 0) }}
                    </div>
                    <p class="mt-2 text-sm text-white/68">
                        {{ $isSale ? $tx['salePriceLead'] : $tx['rentPriceLead'] }}
                    </p>
                </div>

                <div class="mus-fact-grid mt-4">
                    <div class="mus-fact">
                        <div class="mus-fact-label">{{ __('Area') }}</div>
                        <div class="mus-fact-value">
                            {{ $sizeValue ? number_format((float) $sizeValue, 0) . ' ' . $sizeLabel : $tx['notSpecified'] }}
                        </div>
                    </div>
                    <div class="mus-fact">
                        <div class="mus-fact-label">{{ __('Bathrooms') }}</div>
                        <div class="mus-fact-value">{{ $bathValue ?: $tx['notSpecified'] }}</div>
                    </div>
                    <div class="mus-fact">
                        <div class="mus-fact-label">{{ __('Bedrooms') }}</div>
                        <div class="mus-fact-value">{{ $bedValue ?: $tx['notSpecified'] }}</div>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    <a href="{{ $tenantUrl }}" class="inline-flex items-center justify-center rounded-[1rem] bg-white px-4 py-3 text-sm font-bold text-slate-900 shadow-lg shadow-black/10 transition duration-200 hover:-translate-y-0.5">
                        {{ $tx['agencyProfile'] }}
                    </a>
                    <a href="#mus-location" class="inline-flex items-center justify-center rounded-[1rem] border border-white/16 bg-white/10 px-4 py-3 text-sm font-bold text-white transition duration-200 hover:-translate-y-0.5">
                        {{ $tx['liveMap'] }}
                    </a>
                    <a href="{{ route('mobile.marketplace') }}" class="inline-flex items-center justify-center rounded-[1rem] border border-white/16 bg-white/10 px-4 py-3 text-sm font-bold text-white transition duration-200 hover:-translate-y-0.5">
                        {{ $tx['backMarketplace'] }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="mus-shell mt-5">
        <div class="mus-surface mus-card">
            <p class="mus-section-kicker">{{ $tx['overview'] }}</p>
            <h2 class="mus-section-title">{{ $tx['whyStandout'] }}</h2>

            <div class="mt-5 space-y-3 text-sm leading-7 text-slate-700">
                <p>{{ $displayDescription }}</p>
                @foreach ($summaryNotes as $note)
                    <div class="mus-soft-note">{{ $note }}</div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="mus-location" class="mus-shell mt-5">
        <div class="mus-surface mus-card">
            <p class="mus-section-kicker">{{ $tx['locationTitle'] }}</p>
            <h2 class="mus-section-title">{{ $tx['locationSubtitle'] }}</h2>

            <div class="mus-grid mt-5">
                <div class="mus-detail">
                    <div class="mus-detail-label">{{ $tx['listingLocation'] }}</div>
                    <div class="mus-detail-value">{{ $displayLocation }}</div>
                    <p class="mt-3 text-sm leading-7 text-slate-600">{{ $mapPrecisionLabel }}</p>
                    @if ($mapHref)
                        <a href="{{ $mapHref }}" target="_blank" rel="noopener noreferrer" class="mus-link mt-4 inline-flex items-center gap-2 text-sm">
                            <span>{{ $tx['openLocation'] }}</span>
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7m0 0v7m0-7L10 14"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5v14h14"/></svg>
                        </a>
                    @endif
                </div>

                <div class="mus-map-wrap">
                    <div id="mus-map" class="mus-map"></div>
                    @unless ($hasInteractiveMap)
                        <div class="flex h-full items-center justify-center px-6 text-center">
                            <div>
                                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-[rgba(15,90,70,.08)] text-[color:var(--mus-palm)]">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                                </div>
                                <p class="text-sm font-semibold text-slate-600">{{ $tx['mapMissing'] }}</p>
                                <p class="mt-1 text-xs leading-6 text-slate-400">{{ $tx['mapMissingHint'] }}</p>
                            </div>
                        </div>
                    @endunless
                </div>
            </div>
        </div>
    </section>

    <section class="mus-shell mt-5">
        <div class="mus-surface mus-card">
            <p class="mus-section-kicker">{{ $tx['details'] }}</p>
            <h2 class="mus-section-title">{{ $tx['detailsSubtitle'] }}</h2>

            <div class="mus-grid mt-5">
                <div class="mus-detail">
                    <div class="mus-detail-label">{{ __('Unit Code') }}</div>
                    <div class="mus-detail-value">{{ $unit->code }}</div>
                </div>
                <div class="mus-detail">
                    <div class="mus-detail-label">{{ __('Property') }}</div>
                    <div class="mus-detail-value">{{ $propertyName }}</div>
                </div>
                <div class="mus-detail">
                    <div class="mus-detail-label">{{ $tx['propertyType'] }}</div>
                    <div class="mus-detail-value">{{ $categoryName ?: __('Standalone listing') }}</div>
                </div>
                <div class="mus-detail">
                    <div class="mus-detail-label">{{ __('Type') }}</div>
                    <div class="mus-detail-value">{{ $subcategoryName }}</div>
                </div>
                <div class="mus-detail">
                    <div class="mus-detail-label">{{ $tx['listingStatus'] }}</div>
                    <div class="mus-detail-value">{{ $statusLabel }}</div>
                </div>
                <div class="mus-detail">
                    <div class="mus-detail-label">{{ $tx['gallery'] }}</div>
                    <div class="mus-detail-value">
                        {{ count($gallery) ? str_replace(':count', count($gallery), $tx['photosCount']) : $tx['noPhotos'] }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mus-shell mt-5">
        <div class="mus-surface mus-card">
            <p class="mus-section-kicker">{{ $tx['features'] }}</p>
            <h2 class="mus-section-title">{{ $tx['featuresSubtitle'] }}</h2>

            @if ($featureFacts->count() > 0)
                <div class="mus-grid mt-5">
                    @foreach ($featureFacts as $attribute)
                        <div class="mus-detail">
                            <div class="mus-detail-label">{{ $attribute->attributeField->translated_label }}</div>
                            <div class="mus-detail-value">{{ $attribute->formatted_value }}</div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="mus-soft-note mt-5 text-sm leading-7 text-slate-700">
                    {{ $tx['featuresEmpty'] }}
                </div>
            @endif

            @if ($recordFacts !== [])
                <div class="mt-6 border-t border-[rgba(130,94,38,.14)] pt-6">
                    <p class="mus-section-kicker">{{ $tx['recordTitle'] }}</p>
                    <h3 class="mt-2 text-[1.45rem] font-black leading-tight tracking-[-0.04em] text-[color:var(--mus-ink)]">
                        {{ $tx['recordSubtitle'] }}
                    </h3>

                    <div class="mus-grid mt-5">
                        @foreach ($recordFacts as $label => $value)
                            <div class="mus-detail">
                                <div class="mus-detail-label">{{ $label }}</div>
                                <div class="mus-detail-value">{{ $value }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
window.mobileUnitGallery = function(images) {
    return {
        images: Array.isArray(images) ? images : [],
        index: 0,
        fullscreen: false,
        get hasImages() {
            return this.images.length > 0;
        },
        go(nextIndex) {
            if (!this.images.length) return;
            this.index = nextIndex;
        },
        next() {
            if (!this.images.length) return;
            this.index = (this.index + 1) % this.images.length;
        },
        previous() {
            if (!this.images.length) return;
            this.index = (this.index - 1 + this.images.length) % this.images.length;
        },
        open() {
            if (!this.images.length) return;
            this.fullscreen = true;
            document.body.style.overflow = 'hidden';
        },
        close() {
            this.fullscreen = false;
            document.body.style.overflow = '';
        },
    };
};

document.addEventListener('DOMContentLoaded', () => {
    const mapEl = document.getElementById('mus-map');
    const lat = @json($mapLat);
    const lng = @json($mapLng);
    const zoom = @json($mapZoom);
    const hasInteractiveMap = @json($hasInteractiveMap);
    const isApproximate = @json($mapMode === 'approximate');
    const tooltipTitle = @json($displayTitle);
    const tooltipMeta = @json($mapTooltipMeta);
    const linkHref = @json(route('mobile.units.show', $unit));

    document.addEventListener('keydown', (event) => {
        const modal = document.querySelector('.mus-modal.is-open');
        if (event.key === 'Escape' && modal) {
            modal.dispatchEvent(new Event('close'));
        }
    });

    if (!mapEl || !hasInteractiveMap || typeof window.L === 'undefined') {
        return;
    }

    const map = L.map(mapEl, {
        scrollWheelZoom: false,
        dragging: true,
        tap: false,
        zoomControl: false,
        attributionControl: false,
    }).setView([Number(lat), Number(lng)], Number(zoom || 15));

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
    }).addTo(map);

    L.control.zoom({ position: 'topright' }).addTo(map);

    L.circle([Number(lat), Number(lng)], {
        radius: isApproximate ? 900 : 320,
        color: '#b6842f',
        weight: 1,
        fillColor: '#b6842f',
        fillOpacity: 0.12,
    }).addTo(map);

    const marker = L.circleMarker([Number(lat), Number(lng)], {
        radius: 9,
        color: '#fff4dc',
        weight: 3,
        fillColor: isApproximate ? '#b6842f' : '#2f7a72',
        fillOpacity: 1,
    }).addTo(map);

    const popupHtml = `
        <div class="mus-map-popup" dir="${document.documentElement.dir === 'rtl' ? 'rtl' : 'ltr'}">
            <div class="mus-map-popup-title">${tooltipTitle}</div>
            <div class="mus-map-popup-meta">${tooltipMeta}</div>
            <a class="mus-map-popup-link" href="${linkHref}">{{ $tx['viewProperty'] }}</a>
        </div>
    `;

    marker.bindPopup(popupHtml, {
        closeButton: false,
        offset: [0, -8],
    });

    marker.openPopup();
    setTimeout(() => map.invalidateSize(), 180);
});
</script>
@endpush
