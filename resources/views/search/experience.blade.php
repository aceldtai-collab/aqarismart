@php
    use App\Models\Unit;
    use App\Services\Tenancy\TenantManager;
    use Illuminate\Support\Str;

    $context = $context ?? 'public';
    $loc = app()->getLocale();
    $isAr = $loc === 'ar';
    $heroImage = $heroImage ?? null;
    $themePrimary = $themePrimary ?? '#0f5a46';
    $themeAccent = $themeAccent ?? '#b6842f';
    $searchAction = $searchAction ?? request()->url();
    $clearUrl = $clearUrl ?? $searchAction;
    $filters = $filters ?? [];
    $categories = $categories ?? collect();
    $cities = $cities ?? collect();
    $tenants = $tenants ?? collect();
    $tenant = $tenant ?? null;
    $sellWithUsUrl = $sellWithUsUrl ?? '#';
    $navTx = $navTx ?? [];
    $navLinks = $navLinks ?? [];
    $navBrandHref = $navBrandHref ?? url('/');
    $navBrandLabel = $navBrandLabel ?? config('app.name');
    $urlEn = $urlEn ?? request()->fullUrlWithQuery([config('locales.cookie_name', 'lang') => 'en']);
    $urlAr = $urlAr ?? request()->fullUrlWithQuery([config('locales.cookie_name', 'lang') => 'ar']);
    $activeFiltersCount = $activeFiltersCount ?? collect($filters)->filter(function ($value) {
        if (is_array($value)) {
            return collect($value)->filter(fn ($entry) => filled($entry))->isNotEmpty();
        }

        return filled($value) && ! in_array($value, [0, '0'], true);
    })->count();

    $defaultUi = $context === 'tenant'
        ? [
            'hero_eyebrow' => $isAr ? 'بحث حي داخل واجهة الوكالة' : 'Live search inside the agency storefront',
            'hero_title' => $isAr ? 'ابحث في المخزون بالطريقة التي يراه بها العميل' : 'Search the inventory the way the client sees it',
            'hero_subtitle' => $isAr ? 'صفحة بحث رئيسية تجمع الخريطة، المناطق، أنماط العقارات، والنتائج في رحلة واحدة واضحة.' : 'A flagship search page that brings map, neighborhoods, property mix, and live results into one clear journey.',
            'hero_panel_title' => $isAr ? 'حرّك البحث بسرعة' : 'Move through the search faster',
            'hero_panel_text' => $isAr ? 'ابدأ بالبحث السريع، ثم استخدم الفلاتر والخريطة لتضييق النتائج حسب الموقع ونمط الحياة ونوع العقار.' : 'Start with a fast query, then use filters and the map to narrow by location, lifestyle cues, and property type.',
            'hero_search_title' => $isAr ? 'ابدأ بكلمة مفتاحية أو نوع العرض' : 'Start with a keyword or listing type',
            'map_eyebrow' => $isAr ? 'الخريطة الحية' : 'Live search map',
            'map_title' => $isAr ? 'كل نتيجة تظهر على الخريطة' : 'Every result appears on the map',
            'map_text' => $isAr ? 'العلامات تعرض نبذة سريعة عن السعر والموقع ونوع العقار، ومع النقر تنتقل مباشرة إلى صفحة العقار.' : 'Markers give a fast read on price, location, and property type, then move directly into the property page.',
            'filters_title' => $isAr ? 'صقل البحث' : 'Refine the search',
            'filters_text' => $isAr ? 'اضبط النتائج حسب نوع العقار والميزانية والمساحة الأساسية.' : 'Tune the results by property type, budget, and core living facts.',
            'results_eyebrow' => $isAr ? 'المخزون المطابق' : 'Matched inventory',
            'results_title' => $isAr ? 'نتائج البحث' : 'Search results',
            'results_text' => $isAr ? 'بطاقات مصممة للقراءة السريعة قبل الدخول إلى صفحة العقار.' : 'Cards designed for fast reading before stepping into the property page.',
            'locations_title' => $isAr ? 'إيقاع المناطق' : 'Area rhythm',
            'locations_text' => $isAr ? 'أكثر المناطق حضوراً في النتائج الحالية.' : 'The strongest location pockets in the current result set.',
            'types_title' => $isAr ? 'مزيج العقارات' : 'Property mix',
            'types_text' => $isAr ? 'أنواع العقارات التي تسيطر على البحث الآن.' : 'The property types defining this search right now.',
            'price_title' => $isAr ? 'نطاق الأسعار' : 'Price span',
            'price_text' => $isAr ? 'قراءة سريعة للنطاق المالي في النتائج المعروضة.' : 'A quick read on the financial range inside the current results.',
        ]
        : [
            'hero_eyebrow' => $isAr ? 'مرآة السوق العامة' : 'Mirror of the public marketplace',
            'hero_title' => $isAr ? 'ابحث في السوق على خريطة واحدة' : 'Search the market on one map',
            'hero_subtitle' => $isAr ? 'صفحة بحث عامة تربط المخزون النشط من الوكالات المختلفة في تجربة واحدة احترافية.' : 'A public search experience that pulls active agency inventory into one professional journey.',
            'hero_panel_title' => $isAr ? 'انتقل من السوق إلى العقار' : 'Move from market to property',
            'hero_panel_text' => $isAr ? 'ابدأ بالبحث، راقب التوزيع على الخريطة، ثم ادخل إلى الصفحة المناسبة على موقع الوكالة مباشرة.' : 'Start with search, read the spread on the map, then enter the right property page directly on the agency site.',
            'hero_search_title' => $isAr ? 'ابدأ بالمدينة أو نوع العرض أو الوكالة' : 'Start by city, listing type, or agency',
            'map_eyebrow' => $isAr ? 'خريطة السوق' : 'Market search map',
            'map_title' => $isAr ? 'مؤشرات حيّة لكل نتيجة' : 'Live markers for every result',
            'map_text' => $isAr ? 'الخريطة تكشف التوزيع المكاني سريعاً وتعرض بطاقة مختصرة قبل الانتقال إلى صفحة العقار.' : 'The map reveals spatial spread quickly and shows a short card before entering the property page.',
            'filters_title' => $isAr ? 'فلاتر السوق' : 'Market filters',
            'filters_text' => $isAr ? 'صمّم بحثاً يعبر الوكالات والمدن وأنواع العقارات من صفحة واحدة.' : 'Build a search that spans agencies, cities, and property types from one page.',
            'results_eyebrow' => $isAr ? 'نتائج السوق' : 'Market matches',
            'results_title' => $isAr ? 'العقارات المتاحة الآن' : 'Properties available now',
            'results_text' => $isAr ? 'نتائج جاهزة للمقارنة ثم للانتقال المباشر إلى صفحة العقار على موقع الوكالة.' : 'Results prepared for comparison, then direct movement into the agency property page.',
            'locations_title' => $isAr ? 'خريطة المناطق' : 'Location spread',
            'locations_text' => $isAr ? 'المدن والمناطق الأكثر تكراراً داخل النتائج.' : 'The cities and area clusters that dominate this search.',
            'types_title' => $isAr ? 'أنماط السوق' : 'Search mix',
            'types_text' => $isAr ? 'الأنواع العقارية التي تشكل مزاج السوق الآن.' : 'The property types shaping the market right now.',
            'price_title' => $isAr ? 'قراءة الأسعار' : 'Price reading',
            'price_text' => $isAr ? 'نطاق مالي سريع لمساعدة الزائر على ضبط التوقعات.' : 'A quick price range to set expectations before deeper browsing.',
        ];
    $ui = array_merge($defaultUi, $searchUi ?? []);

    $summary = $searchExperience['summary'] ?? [];
    $mapData = $searchExperience['map'] ?? ['center' => ['lat' => 31.9539494, 'lng' => 35.9106350, 'zoom' => 7], 'markers' => []];
    $locationClusters = collect($searchExperience['locations'] ?? []);
    $typeClusters = collect($searchExperience['types'] ?? []);
    $resultTotal = (int) ($summary['total_results'] ?? $units->total() ?? 0);
    $visibleResults = (int) ($summary['visible_results'] ?? $units->count() ?? 0);
    $priceMin = $summary['price_min'] ?? null;
    $priceMax = $summary['price_max'] ?? null;
    $currency = $summary['currency'] ?? 'JOD';
    $rentCount = (int) ($summary['rent_count'] ?? 0);
    $saleCount = (int) ($summary['sale_count'] ?? 0);
    $markersJson = json_encode($mapData['markers'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $mapCenterJson = json_encode($mapData['center'] ?? ['lat' => 31.9539494, 'lng' => 35.9106350, 'zoom' => 7], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $tenantManager = app(TenantManager::class);
    $formatMoney = static function ($amount, string $currencyCode = 'JOD'): string {
        if ($amount === null || $amount === '') {
            return __('Price on request');
        }

        return strtoupper($currencyCode) . ' ' . number_format((float) $amount, 0);
    };
    $displayLocation = static function (Unit $unit) use ($isAr): string {
        $property = $unit->property;
        $propertyCity = $property?->getRelation('city');
        $propertyState = $property?->getRelation('state');

        $candidates = [
            $unit->location,
            $unit->city?->{$isAr ? 'name_ar' : 'name_en'},
            $unit->city?->name_en,
            $unit->area?->{$isAr ? 'name_ar' : 'name_en'},
            $unit->area?->name_en,
            $propertyCity?->{$isAr ? 'name_ar' : 'name_en'},
            $propertyCity?->name_en,
            $property?->getRawOriginal('city'),
            $propertyState?->{$isAr ? 'name_ar' : 'name_en'},
            $propertyState?->name_en,
            $property?->address,
        ];

        foreach ($candidates as $candidate) {
            $candidate = trim((string) $candidate);
            if ($candidate !== '') {
                return $candidate;
            }
        }

        return '';
    };
    $listingTypeLabels = Unit::listingTypeLabels();
@endphp

<style>
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Manrope:wght@400;500;600;700;800&display=swap');
    @import url('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
    @include('public.partials.market-nav-styles');
    .search-experience-page{--search-ink:#1f2a24;--search-palm:#0f5a46;--search-river:#2f7a72;--search-brass:#b6842f;--search-clay:#9d5a3b;--search-sand:#f2ead9;--search-cream:#fbf7ef;--search-line:rgba(130,94,38,.16);--tenant-primary:{{ $themePrimary }};--tenant-accent:{{ $themeAccent }};min-height:100vh;background:radial-gradient(circle at top left,rgba(182,132,47,.14),transparent 24%),radial-gradient(circle at top right,rgba(15,90,70,.12),transparent 26%),linear-gradient(180deg,#ece2cf 0,#f7f0e4 320px,#fbf7ef 100%);color:var(--search-ink);font-family:'Manrope',system-ui,sans-serif}
    .search-experience-page[dir="rtl"]{font-family:'Cairo','Noto Sans Arabic',sans-serif}
    .search-shell{max-width:1320px;margin-inline:auto;padding-inline:1rem}
    .search-surface{background:rgba(255,252,246,.94);border:1px solid var(--search-line);box-shadow:0 24px 54px -36px rgba(57,42,16,.36)}
    .search-ornament{height:10px;width:112px;border-radius:999px;background:linear-gradient(90deg,rgba(15,90,70,.16),rgba(182,132,47,.32),rgba(15,90,70,.16)),repeating-linear-gradient(90deg,transparent 0 10px,rgba(182,132,47,.58) 10px 14px,transparent 14px 24px)}
    .search-hero{position:relative;isolation:isolate;overflow:hidden;border-radius:2.35rem;padding:2rem 1.35rem 1.75rem;color:#fff7ea;background:radial-gradient(circle at top left,rgba(255,255,255,.14),transparent 28%),linear-gradient(140deg,rgba(15,32,26,.96),rgba(15,90,70,.88) 52%,rgba(48,33,15,.84))}
    .search-hero::after{content:"";position:absolute;inset:0;background-image:linear-gradient(90deg,rgba(255,255,255,.04) 1px,transparent 1px),linear-gradient(rgba(255,255,255,.04) 1px,transparent 1px);background-size:88px 88px;mask-image:linear-gradient(to bottom,rgba(0,0,0,.66),transparent 88%);pointer-events:none}
    .search-hero-backdrop{position:absolute;inset:0;opacity:.84}
    .search-hero-grid{position:relative;z-index:10;display:grid;gap:1.5rem}
    .search-hero-kicker{font-size:.72rem;font-weight:800;letter-spacing:.18em;text-transform:uppercase;color:rgba(255,245,223,.72)}
    .search-hero-title{font-size:clamp(2.35rem,4.8vw,4.75rem);font-weight:900;line-height:1.02;letter-spacing:-.05em;color:#fff7ea}
    .search-hero-text{max-width:42rem;font-size:1rem;line-height:1.95;color:rgba(255,255,255,.82)}
    .search-experience-page[dir="rtl"] .search-hero-text,.search-experience-page[dir="rtl"] .search-hero-stats{margin-inline-start:auto}
    .search-hero-stats{display:grid;gap:.75rem}
    .search-note{border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.08);border-radius:1.45rem;padding:1rem;backdrop-filter:blur(14px);box-shadow:0 18px 40px -28px rgba(0,0,0,.35)}
    .search-note-label{font-size:.68rem;letter-spacing:.18em;text-transform:uppercase;color:rgba(255,255,255,.62);font-weight:700}
    .search-note-value{margin-top:.45rem;font-size:1rem;line-height:1.25;font-weight:800;color:#fff8ec}
    .search-note-subtle{margin-top:.4rem;font-size:.82rem;line-height:1.65;color:rgba(255,248,235,.66)}
    .search-hero-panel{border-radius:2rem;padding:1.35rem;background:linear-gradient(180deg,rgba(255,249,239,.98),rgba(247,237,214,.9));color:#1f2a24}
    .search-hero-panel-badge{display:inline-flex;align-items:center;border-radius:999px;background:rgba(15,90,70,.08);padding:.55rem .9rem;font-size:.7rem;font-weight:800;letter-spacing:.18em;text-transform:uppercase;color:var(--search-palm)}
    .search-hero-panel-title{margin-top:1rem;font-size:2rem;font-weight:900;line-height:1.08;letter-spacing:-.04em;color:#111827}
    .search-hero-panel-text{margin-top:.75rem;font-size:.94rem;line-height:1.85;color:#475569}
    .search-hero-form{margin-top:1.25rem;display:grid;gap:.75rem}
    .search-hero-grid-inputs{display:grid;gap:.75rem}
    .search-field-label{display:block;font-size:.72rem;font-weight:800;letter-spacing:.14em;text-transform:uppercase;color:#667085}
    .search-field,.search-select{display:block;width:100%;min-height:3.45rem;border:1px solid rgba(130,94,38,.18);background:rgba(255,255,255,.82);border-radius:1.1rem;padding:.85rem 1rem;font-size:.92rem;font-weight:600;color:#111827;outline:none;transition:border-color .2s ease,box-shadow .2s ease,background-color .2s ease}
    .search-field::placeholder{color:#94a3b8}
    .search-field:focus,.search-select:focus{border-color:rgba(182,132,47,.72);box-shadow:0 0 0 4px rgba(182,132,47,.12);background:#fff}
    .search-select{appearance:none;background-image:url(\"data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2378786b' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.6' d='M6 8l4 4 4-4'/%3e%3c/svg%3e\");background-position:right 1rem center;background-repeat:no-repeat;background-size:1rem 1rem;padding-right:2.6rem}
    .search-experience-page[dir="rtl"] .search-select{background-position:left 1rem center;padding-right:1rem;padding-left:2.6rem}
    .search-hero-submit{display:inline-flex;min-height:3.45rem;align-items:center;justify-content:center;border-radius:1.15rem;padding:.95rem 1.15rem;font-size:.84rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:#fffaf1;background:linear-gradient(135deg,var(--tenant-primary),var(--tenant-accent));box-shadow:0 18px 34px -18px rgba(15,90,70,.82);transition:transform .2s ease,box-shadow .2s ease}
    .search-hero-submit:hover{transform:translateY(-2px);box-shadow:0 24px 42px -22px rgba(15,90,70,.86)}
    .search-insight-grid{display:grid;gap:.75rem}
    .search-insight{border-radius:1.25rem;border:1px solid rgba(130,94,38,.14);background:#fff;padding:1rem}
    .search-insight-label{font-size:.68rem;font-weight:800;letter-spacing:.16em;text-transform:uppercase;color:#64748b}
    .search-insight-value{margin-top:.45rem;font-size:1rem;font-weight:800;color:#111827}
    .search-insight-note{margin-top:.35rem;font-size:.82rem;line-height:1.65;color:#64748b}
    .search-main-grid{display:grid;gap:1.5rem}
    .search-filter-card{position:relative;border-radius:2rem;padding:1.35rem}
    .search-filter-card summary{display:flex;cursor:pointer;align-items:center;justify-content:space-between;list-style:none}
    .search-filter-card summary::-webkit-details-marker{display:none}
    .search-filter-title{font-size:1.1rem;font-weight:900;letter-spacing:-.02em;color:#111827}
    .search-filter-text{margin-top:.5rem;font-size:.9rem;line-height:1.75;color:#64748b}
    .search-filter-form{margin-top:1.1rem;display:grid;gap:1rem}
    .search-filter-grid{display:grid;gap:.9rem}
    .search-filter-actions{display:grid;gap:.75rem}
    .search-clear-link{display:inline-flex;min-height:3rem;align-items:center;justify-content:center;border-radius:1rem;border:1px solid rgba(130,94,38,.16);background:#fff;font-size:.82rem;font-weight:800;color:#475569;transition:transform .18s ease,box-shadow .18s ease}
    .search-clear-link:hover{transform:translateY(-1px);box-shadow:0 18px 34px -24px rgba(0,0,0,.22)}
    .search-map-card{border-radius:2rem;padding:1.35rem}
    .search-map-top{display:grid;gap:1rem}
    .search-map-title{font-size:2rem;font-weight:900;letter-spacing:-.04em;color:#111827}
    .search-map-text{font-size:.94rem;line-height:1.85;color:#64748b}
    .search-map-canvas{height:25rem;width:100%;border-radius:1.7rem;border:1px solid rgba(130,94,38,.16);overflow:hidden;box-shadow:inset 0 0 0 1px rgba(255,255,255,.2)}
    .search-map-mini-grid{display:grid;gap:1rem}
    .search-mini-card{border-radius:1.45rem;border:1px solid rgba(130,94,38,.14);background:linear-gradient(180deg,rgba(255,250,241,.94),rgba(248,239,219,.88));padding:1rem}
    .search-mini-title{font-size:1rem;font-weight:900;letter-spacing:-.02em;color:#111827}
    .search-mini-text{margin-top:.35rem;font-size:.85rem;line-height:1.7;color:#64748b}
    .search-chip-wrap{margin-top:.9rem;display:flex;flex-wrap:wrap;gap:.5rem}
    .search-chip{display:inline-flex;align-items:center;gap:.45rem;border-radius:999px;background:rgba(15,90,70,.08);padding:.5rem .8rem;font-size:.74rem;font-weight:800;color:var(--search-palm)}
    .search-chip--warm{background:rgba(182,132,47,.12);color:var(--search-brass)}
    .search-results-card{border-radius:2rem;padding:1.35rem}
    .search-results-header{display:grid;gap:.9rem}
    .search-results-title{font-size:2rem;font-weight:900;letter-spacing:-.04em;color:#111827}
    .search-results-text{font-size:.94rem;line-height:1.85;color:#64748b}
    .search-results-grid{display:grid;gap:1.25rem}
    .search-listing-card{display:flex;flex-direction:column;overflow:hidden;border-radius:1.8rem;border:1px solid rgba(130,94,38,.16);background:rgba(255,252,246,.98);box-shadow:0 22px 46px -30px rgba(55,38,12,.44);transition:transform .26s ease,box-shadow .26s ease,border-color .26s ease}
    .search-listing-card:hover,.search-listing-card.is-active{transform:translateY(-4px);box-shadow:0 30px 62px -30px rgba(55,38,12,.5);border-color:rgba(15,90,70,.28)}
    .search-listing-media{position:relative;height:17rem;overflow:hidden;background:rgba(34,38,30,.08)}
    .search-listing-gallery-track{display:flex;height:100%;width:100%;transition:transform .45s ease}
    .search-listing-gallery-slide{position:relative;flex:0 0 100%;height:100%}
    .search-listing-gallery-slide img{height:100%;width:100%;object-fit:cover;transition:transform .7s ease}
    .search-listing-card:hover .search-listing-gallery-slide img,.search-listing-card.is-active .search-listing-gallery-slide img{transform:scale(1.06)}
    .search-listing-media::after{content:"";position:absolute;inset:0;background:linear-gradient(to top,rgba(12,18,14,.88),rgba(12,18,14,.08) 58%,transparent)}
    .search-listing-topbar,.search-listing-bottombar{position:absolute;left:1rem;right:1rem;display:flex;align-items:center;justify-content:space-between;gap:.75rem;z-index:2}
    .search-listing-topbar{top:1rem}
    .search-listing-bottombar{bottom:1rem;align-items:flex-end}
    .search-gallery-controls{position:absolute;inset-inline:1rem;top:50%;z-index:3;display:flex;align-items:center;justify-content:space-between;transform:translateY(-50%);pointer-events:none}
    .search-gallery-btn{pointer-events:auto;display:inline-flex;height:2.45rem;width:2.45rem;align-items:center;justify-content:center;border-radius:999px;border:1px solid rgba(255,255,255,.18);background:rgba(12,18,14,.42);color:#fff7ea;backdrop-filter:blur(10px);transition:transform .18s ease,background-color .18s ease,box-shadow .18s ease}
    .search-gallery-btn:hover{transform:scale(1.05);background:rgba(12,18,14,.6);box-shadow:0 18px 30px -18px rgba(0,0,0,.5)}
    .search-gallery-dots{position:absolute;inset-inline:1rem;bottom:4.15rem;z-index:3;display:flex;gap:.45rem}
    .search-experience-page[dir="rtl"] .search-gallery-dots{justify-content:flex-end}
    .search-gallery-dot{display:inline-flex;height:.62rem;width:.62rem;border-radius:999px;border:1px solid rgba(255,255,255,.3);background:rgba(255,255,255,.36);transition:transform .18s ease,background-color .18s ease}
    .search-gallery-dot.is-active{transform:scale(1.08);background:#fffaf2}
    .search-badge{display:inline-flex;align-items:center;justify-content:center;border-radius:999px;padding:.5rem .8rem;font-size:.68rem;font-weight:900;letter-spacing:.16em;text-transform:uppercase}
    .search-badge--sale{background:rgba(182,132,47,.94);color:#fff8ef;box-shadow:0 18px 30px -20px rgba(182,132,47,.92)}
    .search-badge--rent{background:rgba(15,90,70,.92);color:#f3fbf8;box-shadow:0 18px 30px -20px rgba(15,90,70,.88)}
    .search-badge--count{background:rgba(255,248,235,.92);color:#1f2a24;box-shadow:0 18px 30px -20px rgba(16,18,14,.54)}
    .search-price-pill{display:inline-flex;align-items:center;justify-content:center;max-width:100%;border-radius:1rem;background:rgba(255,248,235,.94);padding:.72rem 1rem;font-size:1rem;font-weight:900;color:#111827;box-shadow:0 16px 28px -18px rgba(12,18,14,.62)}
    .search-code-pill{display:inline-flex;align-items:center;justify-content:center;border-radius:999px;border:1px solid rgba(255,255,255,.14);background:rgba(255,255,255,.08);padding:.45rem .75rem;font-size:.64rem;font-weight:900;letter-spacing:.16em;text-transform:uppercase;color:#fff7ea;backdrop-filter:blur(10px)}
    .search-listing-body{display:flex;flex:1;flex-direction:column;gap:1rem;padding:1.2rem 1.2rem 1.3rem}
    .search-listing-title{font-size:1.2rem;font-weight:900;line-height:1.2;letter-spacing:-.03em;color:#111827}
    .search-listing-subtitle{font-size:.9rem;line-height:1.7;color:#64748b}
    .search-listing-facts{display:flex;flex-wrap:wrap;gap:.55rem}
    .search-fact-pill{display:inline-flex;align-items:center;gap:.4rem;border-radius:999px;background:rgba(15,90,70,.08);padding:.5rem .75rem;font-size:.74rem;font-weight:800;color:var(--search-palm)}
    .search-fact-pill--warm{background:rgba(182,132,47,.12);color:var(--search-brass)}
    .search-fact-pill--soft{background:rgba(99,91,73,.08);color:#5b5a56}
    .search-fact-pill--lifestyle{background:rgba(157,90,59,.1);color:var(--search-clay)}
    .search-listing-footer{display:flex;align-items:center;justify-content:space-between;gap:1rem;border-top:1px solid rgba(130,94,38,.12);padding-top:.95rem}
    .search-listing-tenant{font-size:.82rem;font-weight:800;color:#475569}
    .search-listing-link{font-size:.8rem;font-weight:900;letter-spacing:.12em;text-transform:uppercase;color:var(--search-palm)}
    .search-empty{border-radius:1.8rem;border:1px dashed rgba(130,94,38,.28);background:rgba(255,252,246,.76);padding:3rem 1.5rem;text-align:center}
    .search-empty-title{font-size:1.4rem;font-weight:900;letter-spacing:-.03em;color:#111827}
    .search-empty-text{margin-top:.65rem;font-size:.95rem;line-height:1.8;color:#64748b}
    .search-pagination{margin-top:1.6rem}
    .search-map-popup{min-width:220px;max-width:260px;border-radius:1rem;overflow:hidden}
    .search-map-popup-media{height:112px;background:#efe5d3}
    .search-map-popup-media img{height:100%;width:100%;object-fit:cover}
    .search-map-popup-body{padding:.85rem .9rem}
    .search-map-popup-title{font-size:.96rem;font-weight:900;line-height:1.3;color:#111827}
    .search-map-popup-text{margin-top:.35rem;font-size:.82rem;line-height:1.65;color:#64748b}
    .search-map-popup-link{margin-top:.7rem;display:inline-flex;font-size:.72rem;font-weight:900;letter-spacing:.14em;text-transform:uppercase;color:var(--search-palm)}
    .search-experience-page .leaflet-container{background:#e7dfcf;font:inherit}
    .search-experience-page .leaflet-popup-content-wrapper{border-radius:1rem;background:rgba(255,252,246,.98);box-shadow:0 26px 50px -24px rgba(0,0,0,.34)}
    .search-experience-page .leaflet-popup-content{margin:0}
    .search-experience-page .leaflet-popup-tip{background:rgba(255,252,246,.98)}
    .search-experience-page .leaflet-tooltip.search-map-tooltip{border:none;background:rgba(20,31,26,.94);color:#fff8ea;border-radius:999px;padding:.45rem .75rem;box-shadow:0 18px 30px -20px rgba(0,0,0,.44)}
    @media (min-width:640px){.search-hero{padding:2.35rem 2rem 2rem}.search-hero-stats,.search-insight-grid{grid-template-columns:repeat(3,minmax(0,1fr))}.search-results-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
    @media (min-width:1024px){.search-shell{padding-inline:1.5rem}.search-hero-grid{grid-template-columns:minmax(0,1.1fr) minmax(360px,.9fr);align-items:stretch;gap:2rem}.search-experience-page[dir="rtl"] .search-hero-copy{order:2;text-align:right}.search-experience-page[dir="rtl"] .search-hero-panel{order:1;text-align:right}.search-main-grid{grid-template-columns:320px minmax(0,1fr);align-items:start}.search-filter-column{position:sticky;top:8rem}.search-map-top{grid-template-columns:minmax(0,1fr) 300px;align-items:start}.search-map-bottom{display:grid;grid-template-columns:minmax(0,1.1fr) 320px;gap:1rem;align-items:start}.search-results-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
    @media (min-width:1280px){.search-results-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
</style>

<script defer src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<div class="search-experience-page @guest pb-24 md:pb-0 @endguest" dir="{{ $isAr ? 'rtl' : 'ltr' }}">
    @include('public.partials.market-nav', [
        'isAr' => $isAr,
        'tenantCtx' => $context === 'tenant' ? $tenant : null,
        'navTx' => $navTx,
        'navLinks' => $navLinks,
        'navBrandHref' => $navBrandHref,
        'navBrandLabel' => $navBrandLabel,
        'urlEn' => $urlEn,
        'urlAr' => $urlAr,
        'sellWithUsUrl' => $sellWithUsUrl,
    ])

    <section class="search-shell pb-8 pt-28 sm:pt-32 lg:pt-36">
        <div class="search-hero">
            <div class="search-hero-backdrop" style="@if($heroImage)background:linear-gradient(118deg, rgba(8,14,11,.32), rgba(8,14,11,.58)), url('{{ $heroImage }}') center/cover no-repeat;@endif"></div>
            <div class="search-hero-grid">
                <div class="search-hero-copy {{ $isAr ? 'text-right' : 'text-left' }}">
                    <div class="search-ornament {{ $isAr ? 'ml-auto' : '' }}"></div>
                    <p class="search-hero-kicker mt-6">{{ $ui['hero_eyebrow'] }}</p>
                    <h1 class="search-hero-title mt-4">{{ $ui['hero_title'] }}</h1>
                    <p class="search-hero-text mt-5">{{ $ui['hero_subtitle'] }}</p>

                    <div class="search-hero-stats mt-8">
                        <div class="search-note">
                            <div class="search-note-label">{{ $isAr ? 'إجمالي النتائج' : 'Total matches' }}</div>
                            <div class="search-note-value">{{ number_format($resultTotal) }} {{ $isAr ? 'عقار' : 'listings' }}</div>
                            <div class="search-note-subtle">{{ $isAr ? 'عدد النتائج المطابقة للبحث الحالي.' : 'The number of listings matching the current search.' }}</div>
                        </div>
                        <div class="search-note">
                            <div class="search-note-label">{{ $ui['locations_title'] }}</div>
                            <div class="search-note-value">{{ number_format((int) ($summary['location_count'] ?? $locationClusters->count())) }} {{ $isAr ? 'مناطق' : 'areas' }}</div>
                            <div class="search-note-subtle">{{ $isAr ? 'مزيج جغرافي يساعد الزائر على فهم التوزيع بسرعة.' : 'A geographic spread that helps visitors read the market quickly.' }}</div>
                        </div>
                        <div class="search-note">
                            <div class="search-note-label">{{ $ui['types_title'] }}</div>
                            <div class="search-note-value">{{ number_format((int) ($summary['type_count'] ?? $typeClusters->count())) }} {{ $isAr ? 'أنواع' : 'types' }}</div>
                            <div class="search-note-subtle">{{ $isAr ? 'تنوع عقاري يوضح نمط المخزون المعروض.' : 'A property mix that defines the current inventory mood.' }}</div>
                        </div>
                    </div>
                </div>
                <div class="search-surface search-hero-panel {{ $isAr ? 'text-right' : 'text-left' }}">
                    <span class="search-hero-panel-badge">{{ $ui['hero_search_title'] }}</span>
                    <h2 class="search-hero-panel-title">{{ $ui['hero_panel_title'] }}</h2>
                    <p class="search-hero-panel-text">{{ $ui['hero_panel_text'] }}</p>

                    <form method="GET" action="{{ $searchAction }}" class="search-hero-form">
                        <div class="search-hero-grid-inputs">
                            <div>
                                <label class="search-field-label" for="hero-q">{{ $isAr ? 'البحث' : 'Search' }}</label>
                                <input id="hero-q" type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="search-field" placeholder="{{ $isAr ? 'العقار، الكود، أو الموقع' : 'Property, code, or location' }}">
                            </div>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <div>
                                    <label class="search-field-label" for="hero-listing-type">{{ $isAr ? 'نوع العرض' : 'Listing type' }}</label>
                                    <select id="hero-listing-type" name="listing_type" class="search-select">
                                        <option value="">{{ $context === 'tenant' ? __('For Rent') : ($isAr ? 'الكل' : 'All types') }}</option>
                                        @foreach ($listingTypeLabels as $value => $label)
                                            <option value="{{ $value }}" @selected(($filters['listing_type'] ?? '') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if ($context === 'public')
                                    <div>
                                        <label class="search-field-label" for="hero-city-id">{{ $isAr ? 'المدينة' : 'City' }}</label>
                                        <select id="hero-city-id" name="city_id" class="search-select">
                                            <option value="">{{ $isAr ? 'كل المدن' : 'All cities' }}</option>
                                            @foreach ($cities as $city)
                                                <option value="{{ $city->id }}" @selected((string) ($filters['city_id'] ?? '') === (string) $city->id)>{{ $isAr ? ($city->name_ar ?: $city->name_en) : $city->name_en }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @else
                                    <div>
                                        <label class="search-field-label" for="hero-beds">{{ $isAr ? 'غرف النوم' : 'Bedrooms' }}</label>
                                        <select id="hero-beds" name="beds" class="search-select">
                                            <option value="0">{{ $isAr ? 'أي عدد' : 'Any' }}</option>
                                            @foreach ([1, 2, 3, 4, 5] as $bedsOption)
                                                <option value="{{ $bedsOption }}" @selected((int) ($filters['beds'] ?? 0) === $bedsOption)>{{ $bedsOption }}+</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <button type="submit" class="search-hero-submit">{{ $isAr ? 'ابدأ البحث' : 'Run search' }}</button>
                    </form>

                    <div class="search-insight-grid mt-5">
                        <div class="search-insight">
                            <div class="search-insight-label">{{ $ui['price_title'] }}</div>
                            <div class="search-insight-value">
                                @if($priceMin && $priceMax)
                                    {{ $formatMoney($priceMin, $currency) }}{{ $priceMin !== $priceMax ? ' - ' . $formatMoney($priceMax, $currency) : '' }}
                                @else
                                    {{ $isAr ? 'بحسب النتائج' : 'Based on results' }}
                                @endif
                            </div>
                            <div class="search-insight-note">{{ $ui['price_text'] }}</div>
                        </div>
                        <div class="search-insight">
                            <div class="search-insight-label">{{ $isAr ? 'للإيجار / للبيع' : 'Rent / sale mix' }}</div>
                            <div class="search-insight-value">{{ number_format($rentCount) }} / {{ number_format($saleCount) }}</div>
                            <div class="search-insight-note">{{ $isAr ? 'قراءة سريعة لتوازن نتائج الإيجار والبيع.' : 'A fast read on the balance between rent and sale results.' }}</div>
                        </div>
                        <div class="search-insight">
                            <div class="search-insight-label">{{ $isAr ? 'فلاتر نشطة' : 'Active filters' }}</div>
                            <div class="search-insight-value">{{ number_format($activeFiltersCount) }}</div>
                            <div class="search-insight-note">{{ $isAr ? 'كلما زادت الفلاتر أصبحت النتائج أكثر دقة.' : 'More active filters means a tighter, more intentional result set.' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <main class="search-shell pb-20">
        <div class="search-main-grid">
            <aside class="search-filter-column">
                <details class="search-surface search-filter-card" open>
                    <summary>
                        <div>
                            <div class="search-filter-title">{{ $ui['filters_title'] }}</div>
                            <p class="search-filter-text">{{ $ui['filters_text'] }}</p>
                        </div>
                        <span class="search-chip search-chip--warm">{{ number_format($activeFiltersCount) }}</span>
                    </summary>

                    <form method="GET" action="{{ $searchAction }}" class="search-filter-form">
                        <div class="search-filter-grid">
                            <div>
                                <label class="search-field-label" for="filter-q">{{ $isAr ? 'البحث' : 'Search' }}</label>
                                <input id="filter-q" type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="search-field" placeholder="{{ $isAr ? 'العقار، الكود، أو الموقع' : 'Property, code, or location' }}">
                            </div>

                            @if ($context === 'tenant')
                                <div>
                                    <label class="search-field-label" for="filter-category">{{ $isAr ? 'الفئة الرئيسية' : 'Category' }}</label>
                                    <select id="filter-category" name="category" class="search-select">
                                        <option value="0">{{ $isAr ? 'كل الفئات' : 'All categories' }}</option>
                                        @foreach ($categories as $categoryModel)
                                            <option value="{{ $categoryModel->id }}" @selected((int) ($filters['category'] ?? 0) === (int) $categoryModel->id)>{{ $categoryModel->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="search-field-label" for="filter-subcategory">{{ $isAr ? 'نوع العقار' : 'Property type' }}</label>
                                    <select id="filter-subcategory" name="subcategory" class="search-select">
                                        <option value="0">{{ $isAr ? 'كل الأنواع' : 'All property types' }}</option>
                                        @foreach ($categories as $categoryModel)
                                            <optgroup label="{{ $categoryModel->name }}">
                                                @foreach ($categoryModel->subcategories as $subcategoryModel)
                                                    <option value="{{ $subcategoryModel->id }}" @selected((int) ($filters['subcategory'] ?? 0) === (int) $subcategoryModel->id)>{{ $subcategoryModel->name }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">
                                    <div>
                                        <label class="search-field-label" for="filter-beds">{{ $isAr ? 'غرف النوم' : 'Bedrooms' }}</label>
                                        <select id="filter-beds" name="beds" class="search-select">
                                            <option value="0">{{ $isAr ? 'أي عدد' : 'Any' }}</option>
                                            @foreach ([1, 2, 3, 4, 5] as $bedsOption)
                                                <option value="{{ $bedsOption }}" @selected((int) ($filters['beds'] ?? 0) === $bedsOption)>{{ $bedsOption }}+</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="search-field-label" for="filter-baths">{{ $isAr ? 'الحمامات' : 'Bathrooms' }}</label>
                                        <select id="filter-baths" name="baths" class="search-select">
                                            <option value="0">{{ $isAr ? 'أي عدد' : 'Any' }}</option>
                                            @foreach ([1, 2, 3, 4] as $bathsOption)
                                                <option value="{{ $bathsOption }}" @selected((float) ($filters['baths'] ?? 0) === (float) $bathsOption)>{{ $bathsOption }}+</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="search-field-label" for="filter-max">{{ $isAr ? 'الحد الأعلى' : 'Maximum budget' }}</label>
                                    <select id="filter-max" name="max" class="search-select">
                                        <option value="0">{{ $isAr ? 'بدون حد' : 'No limit' }}</option>
                                        @foreach ([5000, 10000, 15000, 20000, 30000, 50000, 100000, 250000] as $budget)
                                            <option value="{{ $budget }}" @selected((int) ($filters['max'] ?? 0) === $budget)>{{ $formatMoney($budget, 'JOD') }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @else
                                <div>
                                    <label class="search-field-label" for="filter-subcategory-id">{{ $isAr ? 'نوع العقار' : 'Property type' }}</label>
                                    <select id="filter-subcategory-id" name="subcategory_id" class="search-select">
                                        <option value="">{{ $isAr ? 'كل الأنواع' : 'All property types' }}</option>
                                        @foreach ($categories as $categoryModel)
                                            <optgroup label="{{ $categoryModel->name }}">
                                                @foreach ($categoryModel->subcategories as $subcategoryModel)
                                                    <option value="{{ $subcategoryModel->id }}" @selected((string) ($filters['subcategory_id'] ?? '') === (string) $subcategoryModel->id)>{{ $subcategoryModel->name }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">
                                    <div>
                                        <label class="search-field-label" for="filter-city-id">{{ $isAr ? 'المدينة' : 'City' }}</label>
                                        <select id="filter-city-id" name="city_id" class="search-select">
                                            <option value="">{{ $isAr ? 'كل المدن' : 'All cities' }}</option>
                                            @foreach ($cities as $city)
                                                <option value="{{ $city->id }}" @selected((string) ($filters['city_id'] ?? '') === (string) $city->id)>{{ $isAr ? ($city->name_ar ?: $city->name_en) : $city->name_en }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="search-field-label" for="filter-tenant-id">{{ $isAr ? 'الوكالة' : 'Agency' }}</label>
                                        <select id="filter-tenant-id" name="tenant_id" class="search-select">
                                            <option value="">{{ $isAr ? 'كل الوكالات' : 'All agencies' }}</option>
                                            @foreach ($tenants as $tenantOption)
                                                <option value="{{ $tenantOption->id }}" @selected((string) ($filters['tenant_id'] ?? '') === (string) $tenantOption->id)>{{ $tenantOption->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">
                                    <div>
                                        <label class="search-field-label" for="filter-bedrooms">{{ $isAr ? 'غرف النوم' : 'Bedrooms' }}</label>
                                        <select id="filter-bedrooms" name="bedrooms" class="search-select">
                                            <option value="">{{ $isAr ? 'أي عدد' : 'Any' }}</option>
                                            @foreach ([1, 2, 3, 4, 5, 6] as $bedsOption)
                                                <option value="{{ $bedsOption }}" @selected((string) ($filters['bedrooms'] ?? '') === (string) $bedsOption)>{{ $bedsOption }}+</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="search-field-label" for="filter-sort">{{ $isAr ? 'الترتيب' : 'Sort' }}</label>
                                        <select id="filter-sort" name="sort" class="search-select">
                                            <option value="latest" @selected(($filters['sort'] ?? 'latest') === 'latest')>{{ $isAr ? 'الأحدث' : 'Newest first' }}</option>
                                            <option value="price_asc" @selected(($filters['sort'] ?? '') === 'price_asc')>{{ $isAr ? 'السعر من الأقل' : 'Price: low to high' }}</option>
                                            <option value="price_desc" @selected(($filters['sort'] ?? '') === 'price_desc')>{{ $isAr ? 'السعر من الأعلى' : 'Price: high to low' }}</option>
                                            <option value="oldest" @selected(($filters['sort'] ?? '') === 'oldest')>{{ $isAr ? 'الأقدم' : 'Oldest first' }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">
                                    <div>
                                        <label class="search-field-label" for="filter-price-min">{{ $isAr ? 'السعر من' : 'Min price' }}</label>
                                        <input id="filter-price-min" type="number" name="price_min" min="0" value="{{ $filters['price_min'] ?? '' }}" class="search-field" placeholder="0">
                                    </div>
                                    <div>
                                        <label class="search-field-label" for="filter-price-max">{{ $isAr ? 'السعر إلى' : 'Max price' }}</label>
                                        <input id="filter-price-max" type="number" name="price_max" min="0" value="{{ $filters['price_max'] ?? '' }}" class="search-field" placeholder="{{ $isAr ? 'بدون حد' : 'No limit' }}">
                                    </div>
                                </div>
                            @endif

                            <div>
                                <label class="search-field-label" for="filter-listing-type">{{ $isAr ? 'نوع العرض' : 'Listing type' }}</label>
                                <select id="filter-listing-type" name="listing_type" class="search-select">
                                    @if ($context === 'public')
                                        <option value="">{{ $isAr ? 'الكل' : 'All types' }}</option>
                                    @endif
                                    @foreach ($listingTypeLabels as $value => $label)
                                        <option value="{{ $value }}" @selected(($filters['listing_type'] ?? '') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="search-filter-actions">
                            <button type="submit" class="search-hero-submit">{{ $isAr ? 'تحديث النتائج' : 'Update results' }}</button>
                            <a href="{{ $clearUrl }}" class="search-clear-link">{{ $isAr ? 'مسح الفلاتر' : 'Clear filters' }}</a>
                        </div>
                    </form>
                </details>
            </aside>

            <div class="space-y-6">
                <section id="search-map-section" class="search-surface search-map-card">
                    <div class="search-map-top">
                        <div class="{{ $isAr ? 'text-right' : 'text-left' }}">
                            <div class="search-ornament {{ $isAr ? 'ml-auto' : '' }}"></div>
                            <p class="search-hero-kicker mt-6 text-[color:rgba(31,42,36,.62)]">{{ $ui['map_eyebrow'] }}</p>
                            <h2 class="search-map-title mt-3">{{ $ui['map_title'] }}</h2>
                            <p class="search-map-text mt-3">{{ $ui['map_text'] }}</p>
                        </div>
                        <div class="search-mini-card {{ $isAr ? 'text-right' : 'text-left' }}">
                            <div class="search-mini-title">{{ $isAr ? 'ملخص البحث' : 'Search snapshot' }}</div>
                            <p class="search-mini-text">{{ $isAr ? 'قراءة مختصرة قبل النزول إلى البطاقات.' : 'A quick read before dropping into the cards.' }}</p>
                            <div class="search-chip-wrap">
                                <span class="search-chip">{{ number_format($resultTotal) }} {{ $isAr ? 'نتائج' : 'results' }}</span>
                                <span class="search-chip search-chip--warm">{{ number_format($visibleResults) }} {{ $isAr ? 'مرئية الآن' : 'visible now' }}</span>
                                <span class="search-chip">{{ number_format(count($mapData['markers'] ?? [])) }} {{ $isAr ? 'علامات' : 'markers' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="search-map-bottom mt-6">
                        <div id="search-results-map" class="search-map-canvas"></div>

                        <div class="search-map-mini-grid">
                            <div class="search-mini-card {{ $isAr ? 'text-right' : 'text-left' }}">
                                <div class="search-mini-title">{{ $ui['locations_title'] }}</div>
                                <p class="search-mini-text">{{ $ui['locations_text'] }}</p>
                                <div class="search-chip-wrap">
                                    @forelse ($locationClusters as $locationCluster)
                                        <span class="search-chip">{{ $locationCluster['name'] }} <span class="opacity-60">{{ $locationCluster['count'] }}</span></span>
                                    @empty
                                        <span class="search-chip search-chip--warm">{{ $isAr ? 'سيظهر التوزيع هنا عند توفر النتائج' : 'Area spread appears here when results are available' }}</span>
                                    @endforelse
                                </div>
                            </div>

                            <div class="search-mini-card {{ $isAr ? 'text-right' : 'text-left' }}">
                                <div class="search-mini-title">{{ $ui['types_title'] }}</div>
                                <p class="search-mini-text">{{ $ui['types_text'] }}</p>
                                <div class="search-chip-wrap">
                                    @forelse ($typeClusters as $typeCluster)
                                        <span class="search-chip search-chip--warm">{{ $typeCluster['name'] }} <span class="opacity-60">{{ $typeCluster['count'] }}</span></span>
                                    @empty
                                        <span class="search-chip">{{ $isAr ? 'سيظهر مزيج الأنواع هنا' : 'Property mix will appear here' }}</span>
                                    @endforelse
                                </div>
                            </div>

                            <div class="search-mini-card {{ $isAr ? 'text-right' : 'text-left' }}">
                                <div class="search-mini-title">{{ $ui['price_title'] }}</div>
                                <p class="search-mini-text">{{ $ui['price_text'] }}</p>
                                <div class="search-chip-wrap">
                                    @if ($priceMin)
                                        <span class="search-chip">{{ $isAr ? 'من' : 'From' }} {{ $formatMoney($priceMin, $currency) }}</span>
                                    @endif
                                    @if ($priceMax)
                                        <span class="search-chip search-chip--warm">{{ $isAr ? 'إلى' : 'Up to' }} {{ $formatMoney($priceMax, $currency) }}</span>
                                    @endif
                                    @if (! $priceMin && ! $priceMax)
                                        <span class="search-chip">{{ $isAr ? 'لا توجد قراءة سعرية بعد' : 'No price read yet' }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="search-results" class="search-surface search-results-card">
                    <div class="search-results-header {{ $isAr ? 'text-right' : 'text-left' }}">
                        <div class="search-ornament {{ $isAr ? 'ml-auto' : '' }}"></div>
                        <p class="search-hero-kicker mt-6 text-[color:rgba(31,42,36,.62)]">{{ $ui['results_eyebrow'] }}</p>
                        <h2 class="search-results-title mt-3">{{ $ui['results_title'] }}</h2>
                        <p class="search-results-text">{{ $ui['results_text'] }}</p>
                        <div class="search-chip-wrap">
                            <span class="search-chip">{{ number_format($resultTotal) }} {{ $isAr ? 'نتائج' : 'results' }}</span>
                            @if(($filters['listing_type'] ?? '') !== '')
                                <span class="search-chip search-chip--warm">{{ $listingTypeLabels[$filters['listing_type']] ?? ($filters['listing_type']) }}</span>
                            @endif
                            @if(($filters['q'] ?? '') !== '')
                                <span class="search-chip">{{ $isAr ? 'بحث:' : 'Query:' }} {{ $filters['q'] }}</span>
                            @endif
                        </div>
                    </div>

                    @if ($units->isEmpty())
                        <div class="search-empty mt-8">
                            <div class="search-empty-title">{{ $isAr ? 'لا توجد نتائج مطابقة الآن' : 'No matching properties right now' }}</div>
                            <p class="search-empty-text">{{ $isAr ? 'جرّب توسيع البحث أو تقليل عدد الفلاتر لتظهر لك خيارات أكثر.' : 'Try widening the search or reducing the number of filters to reveal more options.' }}</p>
                        </div>
                    @else
                        <div class="search-results-grid mt-8">
                            @foreach ($units as $unit)
                                @php
                                    $galleryPhotos = collect($unit->photos ?? [])->filter()->take(5)->values();
                                    $unitPhoto = $galleryPhotos->first();
                                    $unitPhotoCount = $galleryPhotos->count();
                                    $unitTitle = $unit->translated_title ?: ($unit->property?->name ?? $unit->code);
                                    $unitDescription = $unit->translated_description ?: ($unit->subcategory?->description ?? ($unit->location ?: ($isAr ? 'تفاصيل أوضح داخل صفحة العقار.' : 'More detail lives inside the property page.')));
                                    $unitPrice = ($unit->listing_type ?? Unit::LISTING_RENT) === Unit::LISTING_SALE
                                        ? $unit->price
                                        : (($unit->market_rent && $unit->market_rent > 0) ? $unit->market_rent : $unit->price);
                                    $unitLocation = $displayLocation($unit);
                                    $unitFacts = collect([
                                        ($unit->beds ?: $unit->bedrooms) ? ['label' => $isAr ? 'غرف' : 'Beds', 'value' => $unit->beds ?: $unit->bedrooms, 'tone' => ''] : null,
                                        ($unit->baths ?: $unit->bathrooms) ? ['label' => $isAr ? 'حمامات' : 'Baths', 'value' => $unit->baths ?: $unit->bathrooms, 'tone' => 'warm'] : null,
                                        $unit->sqft ? ['label' => $isAr ? 'قدم²' : 'sq ft', 'value' => number_format((float) $unit->sqft), 'tone' => 'soft'] : null,
                                        $unit->area_m2 ? ['label' => 'm²', 'value' => number_format((float) $unit->area_m2), 'tone' => 'soft'] : null,
                                    ])->filter()->take(3)->values();
                                    $attributeFacts = $unit->unitAttributes
                                        ->map(function ($attribute) {
                                            $value = $attribute->string_value
                                                ?? $attribute->int_value
                                                ?? $attribute->decimal_value
                                                ?? ($attribute->bool_value ? __('Yes') : null);
                                            if (blank($value) && filled($attribute->json_value) && is_array($attribute->json_value)) {
                                                $value = collect($attribute->json_value)->filter()->take(2)->implode(' • ');
                                            }
                                            if (! filled($value) || ! $attribute->attributeField) {
                                                return null;
                                            }

                                            return [
                                                'label' => $attribute->attributeField->translated_label,
                                                'value' => $value,
                                                'featured' => (bool) ($attribute->attributeField->promoted || $attribute->attributeField->searchable || Str::contains(Str::lower((string) $attribute->attributeField->group), ['life', 'amen', 'view', 'finish', 'feature', 'community'])),
                                            ];
                                        })
                                        ->filter()
                                        ->sortByDesc(fn ($item) => $item['featured'])
                                        ->take(3)
                                        ->values();
                                    $unitHref = $context === 'tenant'
                                        ? route('tenant.unit', ['tenant_slug' => $tenant->slug, 'unit' => $unit])
                                        : (($unit->tenant)
                                            ? $tenantManager->tenantUrl($unit->tenant, '/listings/' . $unit->code)
                                            : '#');
                                @endphp
                                <article class="search-listing-card {{ $isAr ? 'text-right' : 'text-left' }}" data-search-card data-unit-code="{{ $unit->code }}">
                                    <a href="{{ $unitHref }}" class="search-listing-media">
                                        @if ($unitPhoto)
                                            <div class="search-listing-gallery-track" data-gallery-track>
                                                @foreach ($galleryPhotos as $photoIndex => $photo)
                                                    <div class="search-listing-gallery-slide" data-gallery-slide="{{ $photoIndex }}">
                                                        <img src="{{ $photo }}" alt="{{ $unitTitle }}" loading="lazy">
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="flex h-full w-full items-center justify-center bg-[linear-gradient(135deg,rgba(255,249,239,.96),rgba(245,234,206,.88))]">
                                                <div class="rounded-full bg-[rgba(15,90,70,.08)] px-4 py-3 text-sm font-extrabold text-[color:var(--search-palm)]">{{ $unit->subcategory?->name ?? __('Property') }}</div>
                                            </div>
                                        @endif
                                        @if ($unitPhotoCount > 1)
                                            <div class="search-gallery-controls {{ $isAr ? 'flex-row-reverse' : '' }}">
                                                <button type="button" class="search-gallery-btn" data-gallery-prev aria-label="{{ $isAr ? 'الصورة السابقة' : 'Previous photo' }}">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $isAr ? 'M9 5l7 7-7 7' : 'M15 19l-7-7 7-7' }}"/></svg>
                                                </button>
                                                <button type="button" class="search-gallery-btn" data-gallery-next aria-label="{{ $isAr ? 'الصورة التالية' : 'Next photo' }}">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $isAr ? 'M15 19l-7-7 7-7' : 'M9 5l7 7-7 7' }}"/></svg>
                                                </button>
                                            </div>
                                            <div class="search-gallery-dots">
                                                @foreach ($galleryPhotos as $photoIndex => $photo)
                                                    <button type="button" class="search-gallery-dot{{ $photoIndex === 0 ? ' is-active' : '' }}" data-gallery-dot="{{ $photoIndex }}" aria-label="{{ $isAr ? 'انتقل إلى الصورة' : 'Go to photo' }} {{ $photoIndex + 1 }}"></button>
                                                @endforeach
                                            </div>
                                        @endif
                                        <div class="search-listing-topbar {{ $isAr ? 'flex-row-reverse' : '' }}">
                                            <span class="search-badge {{ ($unit->listing_type ?? Unit::LISTING_RENT) === Unit::LISTING_SALE ? 'search-badge--sale' : 'search-badge--rent' }}">
                                                {{ $listingTypeLabels[$unit->listing_type ?? Unit::LISTING_RENT] ?? __('Listing') }}
                                            </span>
                                            @if ($unitPhotoCount > 1)
                                                <span class="search-badge search-badge--count">{{ $unitPhotoCount }} {{ $isAr ? 'صور' : 'photos' }}</span>
                                            @endif
                                        </div>
                                        <div class="search-listing-bottombar {{ $isAr ? 'flex-row-reverse text-right' : '' }}">
                                            <div class="search-price-pill">{{ $formatMoney($unitPrice, $unit->currency ?? 'JOD') }}</div>
                                            <span class="search-code-pill">{{ $unit->code }}</span>
                                        </div>
                                    </a>

                                    <div class="search-listing-body">
                                        <div>
                                            <a href="{{ $unitHref }}" class="search-listing-title">{{ $unitTitle }}</a>
                                            <p class="search-listing-subtitle mt-2">{{ $unitLocation !== '' ? $unitLocation : ($isAr ? 'سيظهر الموقع هنا عندما تتوفر بياناته.' : 'Location appears here when the data is available.') }}</p>
                                            <p class="search-listing-subtitle mt-2 line-clamp-2">{{ Str::limit(strip_tags((string) $unitDescription), 138) }}</p>
                                        </div>

                                        <div class="search-listing-facts">
                                            <span class="search-fact-pill">{{ $unit->subcategory?->name ?? __('Property') }}</span>
                                            @foreach ($unitFacts as $fact)
                                                <span class="search-fact-pill{{ $fact['tone'] ? ' search-fact-pill--' . $fact['tone'] : '' }}">{{ $fact['value'] }} {{ $fact['label'] }}</span>
                                            @endforeach
                                            @foreach ($attributeFacts as $attributeFact)
                                                <span class="search-fact-pill {{ $attributeFact['featured'] ? 'search-fact-pill--lifestyle' : 'search-fact-pill--soft' }}">{{ $attributeFact['label'] }}: {{ $attributeFact['value'] }}</span>
                                            @endforeach
                                        </div>

                                        <div class="search-listing-footer {{ $isAr ? 'flex-row-reverse' : '' }}">
                                            <div class="search-listing-tenant">
                                                @if ($context === 'public')
                                                    {{ $unit->tenant?->name ?? config('app.name') }}
                                                @else
                                                    {{ $isAr ? 'افتح صفحة العقار الكاملة' : 'Open the full property page' }}
                                                @endif
                                            </div>
                                            <a href="{{ $unitHref }}" class="search-listing-link">{{ $isAr ? 'استعرض العقار' : 'Open property' }}</a>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        <div class="search-pagination">
                            {{ $units->links() }}
                        </div>
                    @endif
                </section>
            </div>
        </div>
    </main>
</div>

<script>
    window.addEventListener('load', () => {
        const isRtl = @json($isAr);
        const priceOnRequestLabel = @json($isAr ? 'السعر عند الطلب' : 'Price on request');
        const saleBadgeLabel = @json($isAr ? 'للبيع' : 'For sale');
        const rentBadgeLabel = @json($isAr ? 'للإيجار' : 'For rent');
        const approximateNoteLabel = @json($isAr ? 'الموقع تقريبي بحسب المنطقة المتاحة.' : 'Pin is approximate based on the available area.');
        const propertyPageLabel = @json($isAr ? 'اذهب إلى صفحة العقار' : 'Go to property page');
        const resultCards = Array.from(document.querySelectorAll('[data-search-card]'));
        const cardsByCode = new Map();

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
                const offset = (isRtl ? currentIndex : -currentIndex) * 100;
                track.style.transform = `translate3d(${offset}%, 0, 0)`;

                dots.forEach((dot, dotIndex) => {
                    dot.classList.toggle('is-active', dotIndex === currentIndex);
                });
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

        const mapElement = document.getElementById('search-results-map');
        if (!mapElement) {
            return;
        }

        const markers = {!! $markersJson !!} ?? [];
        const center = {!! $mapCenterJson !!} ?? { lat: 31.9539494, lng: 35.9106350, zoom: 7 };

        const map = L.map(mapElement, {
            zoomControl: false,
            scrollWheelZoom: true,
        }).setView([center.lat, center.lng], center.zoom || 7);

        L.control.zoom({ position: isRtl ? 'topleft' : 'topright' }).addTo(map);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        if (!markers.length) {
            return;
        }

        const bounds = [];
        const markersByCode = new Map();
        let lockedCode = null;

        const applyCardState = (code, active) => {
            const card = cardsByCode.get(code);
            if (card) {
                card.classList.toggle('is-active', active);
            }
        };

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

        const scrollCardIntoView = (code) => {
            const card = cardsByCode.get(code);
            if (!card) {
                return;
            }

            card.scrollIntoView({
                behavior: 'smooth',
                block: 'center',
                inline: 'nearest',
            });
        };

        const focusResult = (code, options = {}) => {
            const { pan = false, lock = false, scroll = false, openPopup = false } = options;

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
                scrollCardIntoView(code);
            }

            if (openPopup) {
                entry.markerLayer.openPopup();
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
            const badge = marker.listing_type === 'sale'
                ? saleBadgeLabel
                : rentBadgeLabel;
            const modeNote = marker.approximate
                ? `<div class="search-map-popup-text">${approximateNoteLabel}</div>`
                : '';
            const photo = marker.photo
                ? `<div class="search-map-popup-media"><img src="${marker.photo}" alt="${marker.title}"></div>`
                : '';

            mapMarker
                .bindTooltip(marker.title, {
                    direction: isRtl ? 'left' : 'right',
                    offset: [0, -10],
                    className: 'search-map-tooltip',
                })
                .bindPopup(`
                    <div class="search-map-popup">
                        ${photo}
                        <div class="search-map-popup-body">
                            <div class="search-badge ${marker.listing_type === 'sale' ? 'search-badge--sale' : 'search-badge--rent'}">${badge}</div>
                            <div class="search-map-popup-title" style="margin-top:.7rem">${marker.title}</div>
                            <div class="search-map-popup-text">${marker.type} · ${marker.location}</div>
                            <div class="search-map-popup-text">${price}</div>
                            ${modeNote}
                            <a href="${marker.href}" class="search-map-popup-link">${propertyPageLabel}</a>
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

                focusResult(marker.code, { pan: false });
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

            card.addEventListener('focusin', () => {
                focusResult(code, { pan: false });
            });

            card.addEventListener('focusout', (event) => {
                if (card.contains(event.relatedTarget)) {
                    return;
                }

                if (lockedCode === code) {
                    return;
                }

                releaseHoverState();
            });
        });

        if (bounds.length > 1) {
            map.fitBounds(bounds, { padding: [38, 38] });
        } else if (bounds.length === 1) {
            map.setView(bounds[0], center.zoom || 12);
        }
    });
</script>
