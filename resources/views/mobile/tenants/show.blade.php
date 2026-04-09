@extends('mobile.layouts.app', [
    'title' => $tenant->name,
    'show_back_button' => false,
    'body_class' => 'mobile-tenant-shell',
])

@section('full_width', true)

@php
    use App\Services\Tenancy\TenantManager;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $settings = is_array($tenant->settings ?? null) ? $tenant->settings : [];
    $isAr = app()->getLocale() === 'ar';

    $pick = function ($value, $fallback = null) use ($isAr) {
        if (is_array($value)) {
            return $value[$isAr ? 'ar' : 'en'] ?? $value['en'] ?? $value['ar'] ?? $fallback;
        }

        if (is_string($value) && trim($value) !== '') {
            return trim($value);
        }

        return $fallback;
    };

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

    $toRgb = function (?string $color, string $fallback): string {
        $value = is_string($color) && preg_match('/^#?[0-9a-fA-F]{6}$/', trim($color))
            ? ltrim(trim($color), '#')
            : ltrim($fallback, '#');

        return implode(' ', [
            hexdec(substr($value, 0, 2)),
            hexdec(substr($value, 2, 2)),
            hexdec(substr($value, 4, 2)),
        ]);
    };

    $logo = $publicUrl($settings['logo_url'] ?? null);
    $headerBg = $publicUrl($settings['header_bg_url'] ?? null);
    $description = $pick($settings['about']['description'] ?? $settings['description'] ?? $settings['public_description'] ?? null);
    $coverage = $pick($settings['coverage'] ?? null);
    $phone = $pick($settings['phone'] ?? null);
    $email = $pick($settings['email'] ?? null);
    $address = $pick($settings['address'] ?? null);
    $website = $pick($settings['website'] ?? null);
    $primary = is_string($settings['primary_color'] ?? null) && trim($settings['primary_color']) !== '' ? trim($settings['primary_color']) : '#0f5a46';
    $accent = is_string($settings['accent_color'] ?? null) && trim($settings['accent_color']) !== '' ? trim($settings['accent_color']) : '#2f7a72';
    $primaryRgb = $toRgb($primary, '#0f5a46');
    $accentRgb = $toRgb($accent, '#2f7a72');
    $tenantWebsite = app(TenantManager::class)->tenantUrl($tenant, '/');
    $initials = Str::of($tenant->name)->squish()->explode(' ')->filter()->take(2)->map(fn ($part) => Str::upper(Str::substr($part, 0, 1)))->implode('');
    $fallbackDescription = $isAr
        ? "استكشف عقارات {$tenant->name} المعروضة للبيع والإيجار مع صور حقيقية وتجربة تصفح دافئة وواضحة على الجوال."
        : "Explore {$tenant->name}'s sale and rent listings with real photos and a warmer, clearer mobile browsing experience.";

    $ui = [
        'profile' => $isAr ? 'واجهة وكالة' : 'Agency profile',
        'publicHome' => $isAr ? 'موقع عام' : 'Public storefront',
        'inventorySearch' => $isAr ? 'ابحث داخل عقارات الوكالة' : 'Search this agency inventory',
        'heroHint' => $isAr ? 'ابدأ بالبحث ثم تنقّل بين المختارات والخريطة وكل العقارات من نفس الصفحة.' : 'Start with search, then move through featured listings, the map, and the full inventory from one place.',
        'visitWebsite' => $isAr ? 'زيارة موقع الوكالة' : 'Visit tenant website',
        'backMarketplace' => $isAr ? 'العودة إلى السوق' : 'Back to marketplace',
        'fullSearch' => $isAr ? 'بحث متقدم' : 'Full search',
        'staffLogin' => $isAr ? 'دخول الموظفين' : 'Staff Login',
        'listings' => $isAr ? 'العقارات' : 'Listings',
        'rent' => $isAr ? 'إيجار' : 'Rent',
        'sale' => $isAr ? 'بيع' : 'Sale',
        'featuredKicker' => $isAr ? 'مختارات الوكالة' : 'Agency highlights',
        'featuredTitle' => $isAr ? 'واجهة عقارية مميزة' : 'Featured collection',
        'featuredText' => $isAr ? 'وحدات مختارة تعطي فكرة سريعة عن شخصية الوكالة.' : 'A quick selection that sets the tone for this agency.',
        'mapKicker' => $isAr ? 'الخريطة' : 'Map',
        'mapTitle' => $isAr ? 'اقرأ حضور الوكالة على الخريطة' : 'Read the agency footprint on a map',
        'mapText' => $isAr ? 'العقارات التي تحتوي على إحداثيات تظهر هنا لتوضيح النشاط النشط حول المدينة.' : 'Listings with saved coordinates appear here to show where this agency is active.',
        'mapNoLocations' => $isAr ? 'لا توجد عقارات بإحداثيات حالياً' : 'No mapped listings yet',
        'mapNoLocationsHint' => $isAr ? 'ستظهر العلامات هنا عندما تتوفر إحداثيات دقيقة للعقارات.' : 'Pins will appear here once listings include coordinates.',
        'allKicker' => $isAr ? 'المخزون الكامل' : 'Full inventory',
        'allTitle' => $isAr ? 'كل العقارات' : 'All properties',
        'loadMore' => $isAr ? 'تحميل المزيد' : 'Load more',
        'emptyTitle' => $isAr ? 'لا توجد عقارات مطابقة الآن' : 'No matching properties right now',
        'emptyText' => $isAr ? 'جرّب تغيير البحث أو التبديل بين البيع والإيجار.' : 'Try another search or switch between rent and sale.',
        'searchPlaceholder' => $isAr ? 'ابحث بالاسم أو الرمز أو الموقع...' : 'Search by title, code, or location...',
        'liveInventory' => $isAr ? 'مخزون مباشر' : 'Live inventory',
        'viewProperty' => $isAr ? 'عرض العقار' : 'View property',
        'locationPending' => $isAr ? 'تفاصيل الموقع قريباً' : 'Location details coming soon',
        'beds' => $isAr ? 'غرف' : 'beds',
        'baths' => $isAr ? 'حمامات' : 'baths',
        'sqft' => $isAr ? 'قدم²' : 'sqft',
        'resultsLine' => $isAr ? 'عرض :count نتيجة ضمن :mode' : 'Showing :count results in :mode',
        'rentMode' => $isAr ? 'الإيجار' : 'rent',
        'saleMode' => $isAr ? 'البيع' : 'sale',
        'staffTitle' => $isAr ? 'دخول الموظفين' : 'Staff Login',
        'staffSubtitle' => $isAr ? 'خاص بطاقم الوكالة' : 'For agency staff only',
        'email' => $isAr ? 'البريد الإلكتروني' : 'Email',
        'password' => $isAr ? 'كلمة المرور' : 'Password',
        'signIn' => $isAr ? 'تسجيل الدخول' : 'Sign In',
        'signingIn' => $isAr ? 'جارٍ تسجيل الدخول...' : 'Signing in...',
        'invalidCredentials' => $isAr ? 'بيانات الدخول غير صحيحة' : 'Invalid credentials',
        'somethingWrong' => $isAr ? 'حدث خطأ، حاول مجدداً' : 'Something went wrong, try again',
        'footerTitle' => $isAr ? 'تابع الرحلة داخل موقع الوكالة الكامل' : 'Continue into the full tenant website',
        'footerText' => $isAr ? 'بعد هذه النظرة السريعة يمكنك الانتقال إلى موقع الوكالة الكامل أو العودة إلى السوق المركزي لمقارنة وكالات أخرى.' : 'After this quick storefront, continue into the full tenant website or return to the central marketplace to compare more agencies.',
        'exploreSection' => $isAr ? 'استكشف' : 'Explore',
        'tenantCoverage' => $coverage ?: ($isAr ? 'تغطية عقارية نشطة' : 'Active local coverage'),
        'phone' => $isAr ? 'الهاتف' : 'Phone',
        'address' => $isAr ? 'العنوان' : 'Address',
        'website' => $isAr ? 'الموقع' : 'Website',
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
        --tenant-ink:#1f2a24;
        --tenant-brass:#b6842f;
        --tenant-clay:#9d5a3b;
        --tenant-line:rgba(130,94,38,.16);
    }
    body.mobile-tenant-shell{
        --tenant-primary: {{ $primary }};
        --tenant-accent: {{ $accent }};
        --tenant-primary-rgb: {{ $primaryRgb }};
        --tenant-accent-rgb: {{ $accentRgb }};
        background:
            radial-gradient(circle at top left, rgb(var(--tenant-primary-rgb) / .14), transparent 22%),
            radial-gradient(circle at top right, rgb(var(--tenant-accent-rgb) / .12), transparent 24%),
            linear-gradient(180deg, #eee2cc 0, #f6efdf 300px, #fbf7ef 100%);
        color:var(--tenant-ink);
        font-family:'Manrope',system-ui,sans-serif;
    }
    html[dir="rtl"] body.mobile-tenant-shell{
        font-family:'Cairo','Manrope',system-ui,sans-serif;
    }
    body.mobile-tenant-shell header.sticky{
        background:linear-gradient(145deg, rgba(15,32,26,.96), rgb(var(--tenant-primary-rgb) / .92) 54%, rgba(48,33,15,.84));
        box-shadow:0 16px 36px -24px rgba(28,22,10,.55);
    }
    body.mobile-tenant-shell aside{
        background:rgba(252,248,241,.98);
        color:var(--tenant-ink);
    }
    body.mobile-tenant-shell aside .bg-gradient-to-br.from-emerald-600.to-emerald-700{
        background:linear-gradient(145deg, rgba(15,32,26,.96), rgb(var(--tenant-primary-rgb) / .92) 54%, rgba(48,33,15,.84)) !important;
    }
    .mts-page{min-height:100vh}
    .mts-shell{padding-inline:1rem}
    .mts-ornament{
        height:10px;
        width:110px;
        border-radius:999px;
        background:
            linear-gradient(90deg, rgb(var(--tenant-primary-rgb) / .16), rgba(182,132,47,.34), rgb(var(--tenant-accent-rgb) / .16)),
            repeating-linear-gradient(90deg, transparent 0 10px, rgba(182,132,47,.58) 10px 14px, transparent 14px 24px);
    }
    .mts-hero{
        position:relative;
        overflow:hidden;
        border-radius:2rem;
        background:
            radial-gradient(circle at top left, rgba(255,255,255,.16), transparent 28%),
            linear-gradient(145deg, rgba(15,32,26,.96), rgb(var(--tenant-primary-rgb) / .92) 52%, rgb(var(--tenant-accent-rgb) / .82));
        color:#fff8ea;
        box-shadow:0 30px 64px -34px rgba(28,22,10,.58);
    }
    .mts-hero::after{
        content:"";
        position:absolute;
        inset:0;
        background:
            linear-gradient(180deg, rgba(10,16,13,.04), rgba(10,16,13,.22)),
            radial-gradient(circle at 85% 14%, rgba(255,255,255,.08), transparent 24%);
        pointer-events:none;
    }
    .mts-hero-copy{position:relative;z-index:1}
    .mts-brand-wrap{
        margin-top:1.25rem;
        display:grid;
        gap:1rem;
    }
    .mts-brand-media{
        position:relative;
        overflow:hidden;
        border-radius:1.55rem;
        border:1px solid rgba(255,255,255,.18);
        background:rgba(255,255,255,.12);
        box-shadow:0 24px 38px -28px rgba(0,0,0,.44);
        min-height:12rem;
    }
    .mts-brand-media::after{
        content:"";
        position:absolute;
        inset:0;
        background:linear-gradient(180deg, rgba(12,18,15,.04), rgba(12,18,15,.16));
        pointer-events:none;
    }
    .mts-brand-media img{
        display:block;
        width:100%;
        height:100%;
        object-fit:cover;
    }
    .mts-brand-fallback{
        display:flex;
        height:6.15rem;
        width:6.15rem;
        align-items:center;
        justify-content:center;
        overflow:hidden;
        border-radius:1.55rem;
        border:1px solid rgba(255,255,255,.2);
        background:rgba(255,255,255,.12);
        box-shadow:0 18px 32px -24px rgba(0,0,0,.42);
        font-size:1.95rem;
        font-weight:900;
        letter-spacing:.08em;
        color:#fff;
    }
    .mts-brand-label{
        font-size:.7rem;
        font-weight:900;
        letter-spacing:.22em;
        text-transform:uppercase;
        color:rgba(255,255,255,.58);
    }
    .mts-brand-title{
        margin-top:.55rem;
        font-size:2.3rem;
        line-height:.98;
        font-weight:900;
        letter-spacing:-.05em;
        color:#fff;
    }
    .mts-brand-description{
        margin-top:.8rem;
        max-width:28rem;
        font-size:.88rem;
        font-weight:500;
        line-height:1.8;
        color:rgba(255,255,255,.84);
    }
    .mts-kicker{
        font-size:.72rem;
        font-weight:800;
        letter-spacing:.22em;
        text-transform:uppercase;
        color:rgba(255,241,212,.74);
    }
    .mts-chip{
        display:inline-flex;
        align-items:center;
        gap:.55rem;
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
    .mts-chip::before{
        content:"";
        width:.45rem;
        height:.45rem;
        border-radius:999px;
        background:var(--tenant-brass);
        box-shadow:0 0 0 4px rgba(182,132,47,.16);
    }
    .mts-stat{
        border:1px solid rgba(255,255,255,.12);
        background:rgba(255,255,255,.08);
        border-radius:1.35rem;
        padding:.9rem .95rem;
        backdrop-filter:blur(14px);
    }
    .mts-stat-label{
        font-size:.65rem;
        letter-spacing:.18em;
        text-transform:uppercase;
        color:rgba(255,244,221,.62);
        font-weight:700;
    }
    .mts-stat-value{
        margin-top:.35rem;
        font-size:1rem;
        line-height:1.2;
        font-weight:800;
        color:#fff8ea;
    }
    .mts-browse-card{
        border:1px solid rgba(255,255,255,.14);
        background:rgba(255,255,255,.12);
        box-shadow:0 28px 54px -36px rgba(0,0,0,.48);
        backdrop-filter:blur(18px);
    }
    .mts-search{
        border:1px solid rgba(130,94,38,.16);
        background:rgba(255,255,255,.92);
        color:var(--tenant-ink);
        transition:border-color .2s ease, box-shadow .2s ease;
    }
    .mts-search:focus{
        outline:none;
        border-color:rgba(182,132,47,.72);
        box-shadow:0 0 0 4px rgba(182,132,47,.12);
    }
    .mts-toggle{
        display:flex;
        gap:.35rem;
        border-radius:1.2rem;
        background:rgba(255,255,255,.12);
        padding:.32rem;
    }
    .mts-toggle button{
        flex:1;
        border-radius:1rem;
        padding:.8rem .7rem;
        font-size:.76rem;
        font-weight:800;
        letter-spacing:.14em;
        text-transform:uppercase;
        color:rgba(255,248,236,.72);
        transition:all .18s ease;
    }
    .mts-toggle button.is-active{
        background:rgba(255,255,255,.94);
        color:rgb(var(--tenant-primary-rgb));
        box-shadow:0 16px 24px -18px rgba(15,23,42,.48);
    }
    .mts-cta{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        border-radius:1.1rem;
        padding:.9rem 1rem;
        font-size:.82rem;
        font-weight:800;
        transition:transform .2s ease, box-shadow .2s ease, background-color .2s ease;
    }
    .mts-cta:hover{transform:translateY(-1px)}
    .mts-cta-primary{
        background:#fff8ea;
        color:var(--tenant-ink);
        box-shadow:0 18px 30px -18px rgba(0,0,0,.42);
    }
    .mts-cta-secondary{
        border:1px solid rgba(255,255,255,.18);
        background:rgba(255,255,255,.1);
        color:#fff8ea;
    }
    .mts-section-head{
        display:flex;
        align-items:end;
        justify-content:space-between;
        gap:1rem;
        margin-bottom:1rem;
    }
    .mts-section-kicker{
        font-size:.68rem;
        font-weight:800;
        letter-spacing:.2em;
        text-transform:uppercase;
        color:var(--tenant-brass);
    }
    .mts-section-title{
        margin-top:.35rem;
        font-size:1.5rem;
        line-height:1.1;
        font-weight:900;
        letter-spacing:-.04em;
        color:var(--tenant-ink);
    }
    .mts-section-note{
        font-size:.76rem;
        line-height:1.7;
        color:#6f726b;
    }
    .mts-scroll{
        display:flex;
        gap:.9rem;
        overflow-x:auto;
        padding-bottom:.2rem;
        scroll-snap-type:x mandatory;
        -ms-overflow-style:none;
        scrollbar-width:none;
    }
    .mts-scroll::-webkit-scrollbar{display:none}
    .mts-surface{
        border:1px solid var(--tenant-line);
        background:linear-gradient(180deg, rgba(255,249,239,.98), rgba(247,237,214,.92));
        box-shadow:0 24px 54px -36px rgba(57,42,16,.3);
    }
    .mts-unit-card{
        overflow:hidden;
        border-radius:1.6rem;
        border:1px solid var(--tenant-line);
        background:rgba(255,252,246,.96);
        box-shadow:0 22px 46px -34px rgba(55,38,12,.36);
        text-decoration:none;
        color:inherit;
        transition:transform .22s ease, box-shadow .22s ease, border-color .22s ease;
    }
    .mts-unit-card:hover{
        transform:translateY(-3px);
        border-color:rgba(182,132,47,.24);
        box-shadow:0 28px 52px -30px rgba(55,38,12,.44);
    }
    .mts-unit-card.compact{
        min-width:306px;
        scroll-snap-align:start;
    }
    .mts-slider{
        position:relative;
        overflow:hidden;
        background:#ece4d5;
    }
    .mts-slider.rounded-top{border-radius:1.45rem 1.45rem 0 0}
    .mts-slider-track{
        display:flex;
        overflow-x:auto;
        scroll-snap-type:x mandatory;
        -ms-overflow-style:none;
        scrollbar-width:none;
    }
    .mts-slider-track::-webkit-scrollbar{display:none}
    .mts-slide{
        width:100%;
        flex:0 0 100%;
        scroll-snap-align:start;
    }
    .mts-slide img{
        width:100%;
        height:100%;
        object-fit:cover;
        transition:transform .55s ease;
    }
    .mts-unit-card:hover .mts-slide img{transform:scale(1.05)}
    .mts-slider-btn{
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
    .mts-slider-btn.prev{left:.65rem}
    .mts-slider-btn.next{right:.65rem}
    .mts-slider-dots{
        position:absolute;
        left:50%;
        bottom:.75rem;
        z-index:6;
        display:flex;
        gap:.32rem;
        transform:translateX(-50%);
        border-radius:999px;
        background:rgba(0,0,0,.35);
        padding:.35rem .5rem;
        backdrop-filter:blur(8px);
    }
    .mts-slider-dot{
        height:.36rem;
        width:.36rem;
        border-radius:999px;
        background:rgba(255,255,255,.42);
    }
    .mts-slider-dot.is-active{background:#fff}
    .mts-unit-overlay{
        position:absolute;
        inset-inline-start:.85rem;
        inset-block-start:.85rem;
        z-index:5;
        display:flex;
        gap:.45rem;
        flex-wrap:wrap;
    }
    .mts-badge{
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
    .mts-badge.rent{background:rgba(15,90,70,.88)}
    .mts-badge.sale{background:rgba(182,132,47,.88)}
    .mts-badge.soft{
        background:rgba(255,249,239,.92);
        color:var(--tenant-ink);
        box-shadow:0 12px 24px -20px rgba(0,0,0,.4);
    }
    .mts-price{
        position:absolute;
        inset-inline-start:.85rem;
        inset-block-end:.85rem;
        z-index:5;
        display:inline-flex;
        align-items:center;
        border-radius:999px;
        background:rgba(255,249,239,.94);
        padding:.52rem .8rem;
        font-size:.82rem;
        font-weight:900;
        color:var(--tenant-ink);
        box-shadow:0 12px 24px -20px rgba(0,0,0,.42);
    }
    .mts-unit-body{padding:1rem 1rem 1.05rem}
    .mts-unit-body h3{
        margin-top:.55rem;
        font-size:1.06rem;
        line-height:1.28;
        font-weight:900;
        letter-spacing:-.035em;
        color:var(--tenant-ink);
    }
    .mts-unit-meta{
        margin-top:.8rem;
        display:flex;
        flex-wrap:wrap;
        gap:.42rem;
    }
    .mts-pill{
        display:inline-flex;
        align-items:center;
        border-radius:999px;
        padding:.42rem .7rem;
        font-size:.62rem;
        font-weight:800;
        letter-spacing:.12em;
        text-transform:uppercase;
    }
    .mts-pill.palm{background:rgb(var(--tenant-primary-rgb) / .08);color:rgb(var(--tenant-primary-rgb))}
    .mts-pill.brass{background:rgba(182,132,47,.12);color:var(--tenant-brass)}
    .mts-pill.clay{background:rgba(157,90,59,.08);color:var(--tenant-clay)}
    .mts-feed{display:grid;gap:1rem}
    .mts-empty{
        border:1px dashed rgba(130,94,38,.22);
        background:rgba(255,252,246,.82);
        border-radius:1.5rem;
        padding:2rem 1.2rem;
        text-align:center;
    }
    .mts-map-card{
        overflow:hidden;
        border-radius:1.8rem;
        border:1px solid rgba(130,94,38,.14);
        background:linear-gradient(145deg, rgba(248,240,221,.96), rgba(244,229,199,.92));
        box-shadow:0 24px 54px -38px rgba(57,42,16,.3);
    }
    .mts-map{
        height:270px;
        width:100%;
    }
    .mts-map-note{
        border:1px solid rgba(130,94,38,.12);
        background:rgba(255,252,246,.9);
        border-radius:1.3rem;
        padding:1rem;
    }
    .mts-map-popup{
        min-width:180px;
        font-family:inherit;
    }
    .mts-map-popup-title{
        font-size:.9rem;
        line-height:1.3;
        font-weight:800;
        color:var(--tenant-ink);
    }
    .mts-map-popup-meta{
        margin-top:.3rem;
        font-size:.75rem;
        line-height:1.5;
        color:#63675f;
    }
    .mts-map-popup-link{
        margin-top:.55rem;
        display:inline-flex;
        align-items:center;
        gap:.35rem;
        font-size:.72rem;
        font-weight:800;
        letter-spacing:.12em;
        text-transform:uppercase;
        color:rgb(var(--tenant-primary-rgb));
        text-decoration:none;
    }
    .mobile-tenant-shell .leaflet-container{
        background:#e7ddca;
        font:inherit;
    }
</style>
@endpush

@section('content')
<div
    class="mts-page pb-10"
    style="--tenant-primary: {{ $primary }}; --tenant-accent: {{ $accent }}; --tenant-primary-rgb: {{ $primaryRgb }}; --tenant-accent-rgb: {{ $accentRgb }};"
>
    <section class="mts-shell pt-4">
        <div class="mts-hero">
            @if($headerBg)
                <img src="{{ $headerBg }}" alt="{{ $tenant->name }}" class="absolute inset-0 h-full w-full object-cover">
                <div class="absolute inset-0 bg-slate-950/38"></div>
            @endif
            <div class="absolute inset-0" style="background: radial-gradient(circle at top left, rgba(255,255,255,.18), transparent 30%), linear-gradient(145deg, rgba(15,32,26,.96), rgb(var(--tenant-primary-rgb) / .92) 52%, rgb(var(--tenant-accent-rgb) / .82));"></div>
            <div class="mts-hero-copy px-4 pb-5 pt-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="mts-kicker">{{ $ui['profile'] }}</p>
                        <div class="mt-3 mts-ornament"></div>
                    </div>
                    <span class="mts-chip">{{ $coverage ?: $ui['tenantCoverage'] }}</span>
                </div>

                <div class="mts-brand-wrap">
                    @if($logo)
                        <div class="mts-brand-media">
                            <img src="{{ $logo }}" alt="{{ $tenant->name }}">
                        </div>
                    @else
                        <div class="mts-brand-fallback">{{ $initials }}</div>
                    @endif

                    <div class="min-w-0">
                        <p class="mts-brand-label">{{ $ui['publicHome'] }}</p>
                        <h1 class="mts-brand-title">{{ $tenant->name }}</h1>
                        <p id="mts-hero-description" data-has-custom-description="{{ $description ? 'true' : 'false' }}" class="mts-brand-description">
                            {{ $description ?: $fallbackDescription }}
                        </p>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-3 gap-2">
                    <div class="mts-stat">
                        <div class="mts-stat-label">{{ $ui['listings'] }}</div>
                        <div id="mts-total-stat" class="mts-stat-value">—</div>
                    </div>
                    <div class="mts-stat">
                        <div class="mts-stat-label">{{ $ui['rent'] }}</div>
                        <div id="mts-rent-stat" class="mts-stat-value">—</div>
                    </div>
                    <div class="mts-stat">
                        <div class="mts-stat-label">{{ $ui['sale'] }}</div>
                        <div id="mts-sale-stat" class="mts-stat-value">—</div>
                    </div>
                </div>

                @if($phone || $email || $address)
                    <div class="mt-3 flex flex-wrap gap-2 text-[11px] font-semibold text-white/84">
                        @if($phone)
                            <span class="rounded-full border border-white/18 bg-white/12 px-3 py-1.5 backdrop-blur-sm">{{ $phone }}</span>
                        @endif
                        @if($email)
                            <span class="rounded-full border border-white/18 bg-white/12 px-3 py-1.5 backdrop-blur-sm">{{ $email }}</span>
                        @endif
                        @if($address)
                            <span class="rounded-full border border-white/18 bg-white/12 px-3 py-1.5 backdrop-blur-sm">{{ $address }}</span>
                        @endif
                    </div>
                @endif

                <div class="mts-browse-card mt-4 rounded-[1.7rem] p-3.5">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-white/58">{{ $ui['exploreSection'] }}</p>
                            <h2 class="mt-1 text-[1.05rem] font-black leading-tight text-white">{{ $ui['inventorySearch'] }}</h2>
                        </div>
                        <span id="mts-current-mode" class="rounded-full border border-white/16 bg-white/10 px-3 py-1 text-[10px] font-black uppercase tracking-[0.18em] text-white/76">{{ $ui['rent'] }}</span>
                    </div>

                    <form id="mts-search-form" class="mt-3.5" action="{{ route('mobile.tenants.search', $tenant) }}" method="GET">
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 {{ $isAr ? 'right-3.5' : 'left-3.5' }} flex items-center text-slate-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input
                                type="search"
                                id="mts-search-q"
                                name="q"
                                class="mts-search block w-full rounded-[1.25rem] py-3 {{ $isAr ? 'pr-10 pl-4' : 'pl-10 pr-4' }} text-sm font-semibold placeholder:text-slate-400"
                                placeholder="{{ $ui['searchPlaceholder'] }}"
                            >
                        </div>
                    </form>

                    <div class="mt-3.5 flex flex-wrap gap-2">
                        <div class="mts-toggle flex-1 min-w-[12rem]">
                            <button type="button" class="mts-listing-toggle is-active" data-value="rent">{{ $ui['rent'] }}</button>
                            <button type="button" class="mts-listing-toggle" data-value="sale">{{ $ui['sale'] }}</button>
                        </div>
                        <div class="flex w-full flex-wrap gap-2">
                            <a href="{{ route('mobile.tenants.search', $tenant) }}" id="mts-full-search-link" class="mts-cta mts-cta-primary flex-1">{{ $ui['fullSearch'] }}</a>
                            <a href="{{ $tenantWebsite }}" target="_blank" rel="noopener noreferrer" class="mts-cta mts-cta-primary flex-1">{{ $ui['visitWebsite'] }}</a>
                            <a href="{{ route('mobile.marketplace') }}" class="mts-cta mts-cta-secondary flex-1">{{ $ui['backMarketplace'] }}</a>
                            <button type="button" id="mts-login-btn" class="mts-cta mts-cta-secondary hidden w-full">{{ $ui['staffLogin'] }}</button>
                        </div>
                    </div>

                    <p class="mt-3 text-[12px] leading-6 text-white/72">{{ $ui['heroHint'] }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="mts-shell mt-5">
        <div class="mts-section-head">
            <div>
                <p class="mts-section-kicker">{{ $ui['featuredKicker'] }}</p>
                <h2 class="mts-section-title">{{ $ui['featuredTitle'] }}</h2>
                <p class="mts-section-note mt-2">{{ $ui['featuredText'] }}</p>
            </div>
            <span class="rounded-full bg-emerald-50 px-3 py-1 text-[10px] font-black uppercase tracking-[0.18em] text-emerald-700 ring-1 ring-emerald-200">{{ $ui['liveInventory'] }}</span>
        </div>
        <div id="mts-featured" class="mts-scroll">
            <div class="w-[306px] shrink-0 overflow-hidden rounded-[1.6rem] bg-white shadow-sm ring-1 ring-slate-200">
                <div class="aspect-[16/10] animate-pulse bg-slate-200"></div>
                <div class="space-y-2 p-4">
                    <div class="h-3 w-24 rounded bg-slate-200"></div>
                    <div class="h-2 w-36 rounded bg-slate-100"></div>
                    <div class="h-2 w-20 rounded bg-slate-100"></div>
                </div>
            </div>
        </div>
    </section>

    <section class="mts-shell mt-5">
        <div class="mts-section-head">
            <div>
                <p class="mts-section-kicker">{{ $ui['mapKicker'] }}</p>
                <h2 class="mts-section-title">{{ $ui['mapTitle'] }}</h2>
                <p class="mts-section-note mt-2">{{ $ui['mapText'] }}</p>
            </div>
        </div>

        <div class="mts-map-card p-3">
            <div class="relative overflow-hidden rounded-[1.45rem] border border-[rgba(130,94,38,.12)] bg-[rgba(255,252,246,.86)]">
                <div id="mts-map" class="mts-map"></div>
                <div id="mts-map-placeholder" class="absolute inset-0 flex items-center justify-center bg-[rgba(255,252,246,.94)] px-6 text-center">
                    <div>
                        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-[rgba(15,90,70,.08)] text-[rgb(var(--tenant-primary-rgb))]">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                        </div>
                        <p class="text-sm font-semibold text-slate-600">{{ $ui['mapNoLocations'] }}</p>
                        <p class="mt-1 text-xs leading-6 text-slate-400">{{ $ui['mapNoLocationsHint'] }}</p>
                    </div>
                </div>
            </div>
            <div class="mts-map-note mt-3 text-sm leading-7 text-slate-600">
                {{ $ui['footerText'] }}
            </div>
        </div>
    </section>

    <section class="mts-shell mt-5">
        <div class="mts-section-head">
            <div>
                <p class="mts-section-kicker">{{ $ui['allKicker'] }}</p>
                <h2 class="mts-section-title">{{ $ui['allTitle'] }}</h2>
                <p id="mts-feed-caption" class="mts-section-note mt-2">{{ $ui['featuredText'] }}</p>
            </div>
            <div id="mts-load-more" class="hidden">
                <button type="button" id="mts-load-more-btn" class="rounded-[1rem] bg-emerald-50 px-4 py-2.5 text-sm font-bold text-emerald-700 ring-1 ring-emerald-200 transition duration-200 hover:-translate-y-0.5 hover:bg-emerald-100">
                    {{ $ui['loadMore'] }}
                </button>
            </div>
        </div>
        <div id="mts-feed" class="mts-feed"></div>
        <div id="mts-empty" class="mts-empty hidden">
            <p class="text-base font-black tracking-[-0.03em] text-[color:var(--tenant-ink)]">{{ $ui['emptyTitle'] }}</p>
            <p class="mt-2 text-sm leading-7 text-slate-500">{{ $ui['emptyText'] }}</p>
        </div>
    </section>

    <section class="mts-shell mt-5">
        <div class="mts-surface overflow-hidden rounded-[1.8rem] px-5 py-5">
            <p class="mts-section-kicker">{{ $ui['publicHome'] }}</p>
            <h2 class="mt-2 text-[1.75rem] font-black leading-none tracking-[-0.04em] text-[color:var(--tenant-ink)]">{{ $ui['footerTitle'] }}</h2>
            <p class="mt-3 text-sm leading-7 text-slate-600">{{ $ui['footerText'] }}</p>

            <div class="mt-4 flex flex-wrap gap-3">
                <a href="{{ $tenantWebsite }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center rounded-[1rem] bg-[rgb(var(--tenant-primary-rgb))] px-4 py-3 text-sm font-bold text-white shadow-lg shadow-emerald-900/10 transition duration-200 hover:-translate-y-0.5">
                    {{ $ui['visitWebsite'] }}
                </a>
                <a href="{{ route('mobile.marketplace') }}" class="inline-flex items-center justify-center rounded-[1rem] border border-[rgba(130,94,38,.14)] bg-white px-4 py-3 text-sm font-bold text-slate-800 transition duration-200 hover:-translate-y-0.5">
                    {{ $ui['backMarketplace'] }}
                </a>
            </div>

            @if($phone || $email || $address || $website)
                <div class="mt-4 rounded-[1.3rem] border border-[rgba(130,94,38,.12)] bg-white/80 px-4 py-3 text-sm text-slate-600">
                    <div class="space-y-1">
                        @if($phone)
                            <div>{{ $ui['phone'] }}: {{ $phone }}</div>
                        @endif
                        @if($email)
                            <div>{{ $ui['email'] }}: {{ $email }}</div>
                        @endif
                        @if($address)
                            <div>{{ $ui['address'] }}: {{ $address }}</div>
                        @endif
                        @if($website)
                            <div>{{ $ui['website'] }}: {{ $website }}</div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>

<div id="mts-login-modal" class="fixed inset-0 z-[60] flex items-end justify-center sm:items-center" style="display:none;">
    <div id="mts-login-backdrop" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div class="relative z-10 w-full max-w-md rounded-t-3xl bg-white p-6 shadow-2xl sm:rounded-3xl">
        <button type="button" id="mts-login-close" class="absolute top-4 {{ $isAr ? 'left-4' : 'right-4' }} text-slate-400 transition hover:text-slate-600">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <div class="flex items-center gap-3">
            <div class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-2xl bg-slate-100">
                @if($logo)
                    <img src="{{ $logo }}" alt="{{ $tenant->name }}" class="h-full w-full object-cover">
                @else
                    <span class="text-sm font-black tracking-wide text-slate-700">{{ $initials }}</span>
                @endif
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-slate-400">{{ $ui['staffSubtitle'] }}</p>
                <h2 class="mt-1 text-lg font-black text-slate-900">{{ $ui['staffTitle'] }}</h2>
                <p class="text-xs font-medium text-slate-500">{{ $tenant->name }}</p>
            </div>
        </div>

        <div id="mts-login-error" class="mt-5 hidden rounded-2xl bg-red-50 p-3 text-sm text-red-700 ring-1 ring-red-200"></div>

        <form id="mts-login-form" class="mt-5 space-y-4">
            <div>
                <label class="mb-1 block text-sm font-semibold text-slate-700">{{ $ui['email'] }}</label>
                <input type="email" name="email" required class="block w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm font-medium text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold text-slate-700">{{ $ui['password'] }}</label>
                <input type="password" name="password" required class="block w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm font-medium text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
            </div>
            <button type="submit" id="mts-login-submit" class="w-full rounded-2xl px-4 py-3 text-sm font-bold text-white shadow-lg transition duration-200 active:scale-[0.98] disabled:opacity-50" style="background-image:linear-gradient(135deg, rgb(var(--tenant-primary-rgb)) 0%, rgb(var(--tenant-accent-rgb)) 100%);">
                {{ $ui['signIn'] }}
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const lang = document.documentElement.lang === 'ar' ? 'ar' : 'en';
const locale = lang === 'ar' ? 'ar-JO' : 'en-US';
const strings = @json($ui);
const tenantSlug = @json($tenant->slug);
const tenantName = @json($tenant->name);
const apiBase = window.__AQARI_API_BASE || '';
const heroDescriptionEl = document.getElementById('mts-hero-description');
const heroHasCustomDescription = heroDescriptionEl?.dataset.hasCustomDescription === 'true';
const searchField = document.getElementById('mts-search-q');
const fullSearchLink = document.getElementById('mts-full-search-link');
const fullSearchBaseUrl = @json(route('mobile.tenants.search', $tenant));
const loadMoreWrap = document.getElementById('mts-load-more');
const loadMoreButton = document.getElementById('mts-load-more-btn');
const numberFormatter = new Intl.NumberFormat(locale);

let currentPage = 1;
let lastPage = 1;
let currentListingType = 'rent';
let searchQuery = '';
let feedUnits = [];
let featuredUnits = [];
let mapInstance = null;
let mapMarkers = [];

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, (char) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;',
    })[char]);
}

function formatNumber(value) {
    return numberFormatter.format(Number(value || 0));
}

function money(value, currency) {
    return `${escapeHtml(currency || 'JOD')} ${formatNumber(value)}`;
}

function priceValue(unit) {
    return unit.display_price ?? unit.price ?? 0;
}

function locationText(unit) {
    if (unit.location_label) {
        return unit.location_label;
    }

    const city = lang === 'ar'
        ? (unit.city?.name_ar || unit.city?.name_en || '')
        : (unit.city?.name_en || unit.city?.name_ar || '');
    const parts = [unit.location || '', city].filter(Boolean);

    if (parts.length) {
        return parts.join(' · ');
    }

    if (unit.property?.name) {
        return unit.property.name;
    }

    return strings.locationPending;
}

function categoryText(unit) {
    return unit.subcategory?.name || unit.property?.property_type || unit.property?.type || (lang === 'ar' ? 'عقار' : 'Property');
}

function attributeHighlightsHtml(unit) {
    const attributes = Array.isArray(unit.attribute_highlights) ? unit.attribute_highlights.slice(0, 3) : [];
    return attributes.map((attribute) => {
        const tone = attribute.featured ? 'palm' : 'clay';
        return `<span class="mts-pill ${tone}">${escapeHtml(attribute.label)}: ${escapeHtml(attribute.value)}</span>`;
    }).join('');
}

function tenantSearchUrl() {
    const params = new URLSearchParams();
    if (currentListingType) {
        params.set('listing_type', currentListingType);
    }
    if (searchField?.value.trim()) {
        params.set('q', searchField.value.trim());
    }
    return `${fullSearchBaseUrl}?${params.toString()}`;
}

function syncTenantSearchHref() {
    if (fullSearchLink) {
        fullSearchLink.href = tenantSearchUrl();
    }
}

function typeLabel(type) {
    return type === 'sale' ? strings.sale : strings.rent;
}

function galleryHtml(unit, options = {}) {
    const photos = Array.isArray(unit.photos) && unit.photos.length
        ? unit.photos
        : ['https://picsum.photos/seed/aqarismart-fallback/960/680'];
    const sliderId = `tenant-slider-${unit.code}-${options.compact ? 'compact' : 'wide'}`;
    const title = escapeHtml(unit.translated_title || unit.title || unit.code);
    const aspectClass = options.compact ? 'aspect-[16/11]' : 'aspect-[16/10]';

    return `
        <div class="mts-slider rounded-top ${aspectClass}" data-slider="${sliderId}">
            <div class="mts-slider-track h-full" data-slider-track>
                ${photos.map((photo, index) => `
                    <div class="mts-slide h-full">
                        <img src="${escapeHtml(photo)}" alt="${title}" loading="lazy" data-slide-index="${index}">
                    </div>
                `).join('')}
            </div>
            ${photos.length > 1 ? `
                <button type="button" class="mts-slider-btn prev" data-slider-prev aria-label="${lang === 'ar' ? 'الصورة السابقة' : 'Previous image'}">
                    <svg class="h-4 w-4 ${lang === 'ar' ? 'rotate-180' : ''}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button type="button" class="mts-slider-btn next" data-slider-next aria-label="${lang === 'ar' ? 'الصورة التالية' : 'Next image'}">
                    <svg class="h-4 w-4 ${lang === 'ar' ? 'rotate-180' : ''}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
                <div class="mts-slider-dots">
                    ${photos.map((_, index) => `<span class="mts-slider-dot ${index === 0 ? 'is-active' : ''}" data-slider-dot data-slide="${index}"></span>`).join('')}
                </div>
            ` : ''}
        </div>
    `;
}

function featuredCardHtml(unit) {
    const title = escapeHtml(unit.translated_title || unit.title || unit.code);
    const location = escapeHtml(locationText(unit));
    const beds = unit.bedrooms ?? unit.beds ?? 0;
    const baths = unit.bathrooms ?? unit.baths ?? 0;
    const area = unit.sqft ?? unit.area_m2 ?? 0;

    return `
        <article class="mts-unit-card compact">
            <div class="relative">
                ${galleryHtml(unit, { compact: false })}
                <a href="/mobile/units/${escapeHtml(unit.code)}" class="absolute inset-0 z-[4]" aria-label="${title}"></a>
                <div class="mts-unit-overlay pointer-events-none">
                    <span class="mts-badge ${unit.listing_type === 'sale' ? 'sale' : 'rent'}">${typeLabel(unit.listing_type)}</span>
                    <span class="mts-badge soft">${escapeHtml(categoryText(unit))}</span>
                </div>
                <span class="mts-price pointer-events-none">${money(priceValue(unit), unit.currency ?? 'JOD')}</span>
            </div>
            <div class="mts-unit-body">
                <div class="text-[11px] font-extrabold uppercase tracking-[0.16em] text-[color:var(--tenant-brass)]">${escapeHtml(categoryText(unit))}</div>
                <h3>${title}</h3>
                <p class="mt-2 text-sm leading-6 text-slate-500">${location}</p>
                <div class="mts-unit-meta">
                    <span class="mts-pill palm">${formatNumber(beds)} ${strings.beds}</span>
                    <span class="mts-pill brass">${formatNumber(baths)} ${strings.baths}</span>
                    ${area ? `<span class="mts-pill clay">${formatNumber(area)} ${strings.sqft}</span>` : ''}
                    ${attributeHighlightsHtml(unit)}
                </div>
                <div class="mt-4 inline-flex items-center gap-2 text-[11px] font-extrabold uppercase tracking-[0.16em] text-[rgb(var(--tenant-primary-rgb))]">
                    <span>${strings.viewProperty}</span>
                    <svg class="h-3.5 w-3.5 ${lang === 'ar' ? 'rotate-180' : ''}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </div>
            </div>
        </article>
    `;
}

function feedCardHtml(unit) {
    const title = escapeHtml(unit.translated_title || unit.title || unit.code);
    const location = escapeHtml(locationText(unit));
    const beds = unit.bedrooms ?? unit.beds ?? 0;
    const baths = unit.bathrooms ?? unit.baths ?? 0;
    const area = unit.sqft ?? unit.area_m2 ?? 0;

    return `
        <article class="mts-unit-card">
            <div class="relative">
                ${galleryHtml(unit, { compact: true })}
                <a href="/mobile/units/${escapeHtml(unit.code)}" class="absolute inset-0 z-[4]" aria-label="${title}"></a>
                <div class="mts-unit-overlay pointer-events-none">
                    <span class="mts-badge ${unit.listing_type === 'sale' ? 'sale' : 'rent'}">${typeLabel(unit.listing_type)}</span>
                    <span class="mts-badge soft">${escapeHtml(categoryText(unit))}</span>
                </div>
                <span class="mts-price pointer-events-none">${money(priceValue(unit), unit.currency ?? 'JOD')}</span>
            </div>
            <div class="mts-unit-body">
                <div class="text-[11px] font-extrabold uppercase tracking-[0.16em] text-[color:var(--tenant-brass)]">${escapeHtml(categoryText(unit))}</div>
                <h3>${title}</h3>
                <p class="mt-2 text-sm leading-6 text-slate-500">${location}</p>
                <div class="mts-unit-meta">
                    <span class="mts-pill palm">${formatNumber(beds)} ${strings.beds}</span>
                    <span class="mts-pill brass">${formatNumber(baths)} ${strings.baths}</span>
                    ${area ? `<span class="mts-pill clay">${formatNumber(area)} ${strings.sqft}</span>` : ''}
                    ${attributeHighlightsHtml(unit)}
                </div>
            </div>
        </article>
    `;
}

function bindSliders(scope = document) {
    scope.querySelectorAll('[data-slider]').forEach((slider) => {
        if (slider.dataset.bound === 'true') {
            return;
        }

        slider.dataset.bound = 'true';

        const track = slider.querySelector('[data-slider-track]');
        const dots = Array.from(slider.querySelectorAll('[data-slider-dot]'));
        const prev = slider.querySelector('[data-slider-prev]');
        const next = slider.querySelector('[data-slider-next]');

        if (!track) {
            return;
        }

        const sync = () => {
            if (!dots.length) {
                return;
            }

            const width = track.clientWidth || 1;
            const index = Math.max(0, Math.min(dots.length - 1, Math.round(track.scrollLeft / width)));

            dots.forEach((dot, dotIndex) => dot.classList.toggle('is-active', dotIndex === index));
        };

        const go = (index) => {
            const width = track.clientWidth || 1;
            track.scrollTo({ left: width * index, behavior: 'smooth' });
        };

        prev?.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            const width = track.clientWidth || 1;
            const index = Math.round(track.scrollLeft / width);
            go(index <= 0 ? dots.length - 1 : index - 1);
        });

        next?.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            const width = track.clientWidth || 1;
            const index = Math.round(track.scrollLeft / width);
            go(index >= dots.length - 1 ? 0 : index + 1);
        });

        dots.forEach((dot, index) => {
            dot.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                go(index);
            });
        });

        track.addEventListener('scroll', sync, { passive: true });
        window.addEventListener('resize', sync, { passive: true });
        sync();
    });
}

function renderFeatured(units) {
    const container = document.getElementById('mts-featured');

    if (!container) {
        return;
    }

    if (!units.length) {
        container.innerHTML = `
            <div class="mts-empty w-full">
                <p class="text-base font-black tracking-[-0.03em] text-[color:var(--tenant-ink)]">${strings.emptyTitle}</p>
                <p class="mt-2 text-sm leading-7 text-slate-500">${strings.emptyText}</p>
            </div>
        `;
        return;
    }

    container.innerHTML = units.map(featuredCardHtml).join('');
    bindSliders(container);
}

function renderFeed(units, page) {
    const container = document.getElementById('mts-feed');
    const empty = document.getElementById('mts-empty');

    if (!container || !empty) {
        return;
    }

    if (page === 1) {
        container.innerHTML = '';
    }

    if (!units.length) {
        if (page === 1) {
            empty.classList.remove('hidden');
        }
        return;
    }

    empty.classList.add('hidden');
    container.insertAdjacentHTML('beforeend', units.map(feedCardHtml).join(''));
    bindSliders(container);
}

function updateSummary(summary, meta) {
    const total = summary?.total_active ?? meta?.total ?? 0;
    const rent = summary?.rent_count ?? 0;
    const sale = summary?.sale_count ?? 0;
    const feedCaption = document.getElementById('mts-feed-caption');
    const modeLabel = currentListingType === 'sale' ? strings.saleMode : strings.rentMode;

    document.getElementById('mts-total-stat').textContent = formatNumber(total);
    document.getElementById('mts-rent-stat').textContent = formatNumber(rent);
    document.getElementById('mts-sale-stat').textContent = formatNumber(sale);
    document.getElementById('mts-current-mode').textContent = currentListingType === 'sale' ? strings.sale : strings.rent;

    if (feedCaption) {
        feedCaption.textContent = strings.resultsLine
            .replace(':count', formatNumber(meta?.total ?? 0))
            .replace(':mode', modeLabel);
    }

    if (!heroHasCustomDescription && heroDescriptionEl) {
        heroDescriptionEl.textContent = lang === 'ar'
            ? `تعرض ${tenantName} حالياً ${formatNumber(total)} عقاراً بين الإيجار والبيع مع صور حقيقية وتفاصيل محدثة مباشرة على الجوال.`
            : `${tenantName} currently shows ${formatNumber(total)} live listings across rent and sale opportunities with real photos and up-to-date mobile details.`;
    }
}

function uniqueUnits(units) {
    const seen = new Set();

    return units.filter((unit) => {
        if (seen.has(unit.code)) {
            return false;
        }

        seen.add(unit.code);
        return true;
    });
}

function renderMap(units) {
    const mapElement = document.getElementById('mts-map');
    const placeholder = document.getElementById('mts-map-placeholder');

    if (!mapElement || typeof L === 'undefined') {
        return;
    }

    const geoUnits = units.filter((unit) => unit.lat && unit.lng && Number(unit.lat) !== 0 && Number(unit.lng) !== 0);

    if (!geoUnits.length) {
        if (placeholder) {
            placeholder.style.display = 'flex';
        }
        return;
    }

    if (placeholder) {
        placeholder.style.display = 'none';
    }

    if (!mapInstance) {
        mapInstance = L.map(mapElement, {
            zoomControl: false,
            attributionControl: false,
            scrollWheelZoom: false,
        }).setView([Number(geoUnits[0].lat), Number(geoUnits[0].lng)], 11);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
        }).addTo(mapInstance);

        L.control.zoom({ position: 'topright' }).addTo(mapInstance);

        setTimeout(() => mapInstance.invalidateSize(), 180);
    }

    mapMarkers.forEach((marker) => marker.remove());
    mapMarkers = [];

    const bounds = L.latLngBounds();

    geoUnits.forEach((unit) => {
        const marker = L.circleMarker([Number(unit.lat), Number(unit.lng)], {
            radius: 8,
            color: '#fff4dc',
            weight: 3,
            fillColor: unit.listing_type === 'sale' ? '#b6842f' : '#2f7a72',
            fillOpacity: 1,
        }).addTo(mapInstance);

        marker.bindPopup(`
            <div class="mts-map-popup" dir="${lang === 'ar' ? 'rtl' : 'ltr'}">
                <div class="mts-map-popup-title">${escapeHtml(unit.translated_title || unit.title || unit.code)}</div>
                <div class="mts-map-popup-meta">${money(priceValue(unit), unit.currency ?? 'JOD')} · ${escapeHtml(locationText(unit))}</div>
                <a class="mts-map-popup-link" href="/mobile/units/${escapeHtml(unit.code)}">${strings.viewProperty}</a>
            </div>
        `);

        marker.on('click', () => marker.openPopup());
        bounds.extend([Number(unit.lat), Number(unit.lng)]);
        mapMarkers.push(marker);
    });

    if (bounds.isValid()) {
        mapInstance.fitBounds(bounds, { padding: [28, 28], maxZoom: 13 });
    }
}

async function loadTenantHome(page = 1) {
    const params = new URLSearchParams({
        listing_type: currentListingType,
        page: String(page),
        per_page: '10',
    });

    if (searchQuery) {
        params.set('q', searchQuery);
    }

    const response = await fetch(`${apiBase}/api/mobile/tenants/${tenantSlug}/home?${params.toString()}`, {
        headers: { Accept: 'application/json' },
    });

    if (!response.ok) {
        throw new Error(`Failed to load tenant home: ${response.status}`);
    }

    const json = await response.json();
    const nextFeatured = Array.isArray(json.featured_units) ? json.featured_units : [];
    const nextUnits = Array.isArray(json.units) ? json.units : [];
    const summary = json.summary || {};
    const meta = json.meta || {};

    currentPage = meta.current_page || 1;
    lastPage = meta.last_page || 1;

    if (page === 1) {
        featuredUnits = nextFeatured;
        feedUnits = nextUnits;
        renderFeatured(featuredUnits);
    } else {
        feedUnits = feedUnits.concat(nextUnits);
    }

    renderFeed(nextUnits, page);
    updateSummary(summary, meta);
    renderMap(uniqueUnits(featuredUnits.concat(feedUnits)));
    loadMoreWrap?.classList.toggle('hidden', currentPage >= lastPage);
}

document.querySelectorAll('.mts-listing-toggle').forEach((button) => {
    button.addEventListener('click', async () => {
        document.querySelectorAll('.mts-listing-toggle').forEach((item) => item.classList.remove('is-active'));
        button.classList.add('is-active');
        currentListingType = button.dataset.value || 'rent';
        syncTenantSearchHref();

        try {
            await loadTenantHome(1);
        } catch (error) {
            console.error(error);
        }
    });
});

let searchTimer;
searchField?.addEventListener('input', () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(async () => {
        searchQuery = searchField.value.trim();
        syncTenantSearchHref();
        try {
            await loadTenantHome(1);
        } catch (error) {
            console.error(error);
        }
    }, 420);
});

document.getElementById('mts-search-form')?.addEventListener('submit', async (event) => {
    event.preventDefault();
    window.location.href = tenantSearchUrl();
});

loadMoreButton?.addEventListener('click', async () => {
    try {
        await loadTenantHome(currentPage + 1);
    } catch (error) {
        console.error(error);
    }
});

const loginModal = document.getElementById('mts-login-modal');
const loginButton = document.getElementById('mts-login-btn');
const loginClose = document.getElementById('mts-login-close');
const loginBackdrop = document.getElementById('mts-login-backdrop');
const loginForm = document.getElementById('mts-login-form');
const loginError = document.getElementById('mts-login-error');
const loginSubmit = document.getElementById('mts-login-submit');

const openLogin = () => {
    if (loginModal) {
        loginModal.style.display = 'flex';
    }
};

const closeLogin = () => {
    if (loginModal) {
        loginModal.style.display = 'none';
    }
};

if (!localStorage.getItem('aqari_mobile_token')) {
    loginButton?.classList.remove('hidden');
    loginButton?.classList.add('inline-flex');
}

loginButton?.addEventListener('click', openLogin);
loginClose?.addEventListener('click', closeLogin);
loginBackdrop?.addEventListener('click', closeLogin);

loginForm?.addEventListener('submit', async (event) => {
    event.preventDefault();
    loginError?.classList.add('hidden');

    if (loginSubmit) {
        loginSubmit.disabled = true;
        loginSubmit.textContent = strings.signingIn;
    }

    try {
        const formData = new FormData(loginForm);
        const response = await fetch(`${apiBase}/api/mobile/auth/login`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
            body: JSON.stringify({
                email: formData.get('email'),
                password: formData.get('password'),
                tenant_slug: tenantSlug,
            }),
        });

        const json = await response.json();

        if (!response.ok) {
            const message = json.message || json.errors?.login?.[0] || json.errors?.email?.[0] || strings.invalidCredentials;
            if (loginError) {
                loginError.textContent = message;
                loginError.classList.remove('hidden');
            }
            return;
        }

        localStorage.setItem('aqari_mobile_token', json.token);
        localStorage.setItem('aqari_mobile_tenant_slug', json.current_tenant?.slug || tenantSlug);
        localStorage.setItem('aqari_mobile_user_name', json.user?.name || '');

        if (json.tenant_role || json.user?.tenant_role) {
            localStorage.setItem('aqari_mobile_user_role', json.tenant_role || json.user.tenant_role);
        } else {
            localStorage.removeItem('aqari_mobile_user_role');
        }

        window.location.href = '{{ route("mobile.dashboard") }}';
    } catch (error) {
        if (loginError) {
            loginError.textContent = strings.somethingWrong;
            loginError.classList.remove('hidden');
        }
    } finally {
        if (loginSubmit) {
            loginSubmit.disabled = false;
            loginSubmit.textContent = strings.signIn;
        }
    }
});

syncTenantSearchHref();
loadTenantHome(1).catch((error) => {
    console.error(error);
    document.getElementById('mts-empty')?.classList.remove('hidden');
});
</script>
@endpush
