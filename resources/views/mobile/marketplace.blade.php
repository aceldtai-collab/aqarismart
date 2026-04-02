@extends('mobile.layouts.app', ['title' => app()->getLocale() === 'ar' ? 'عقاري سمارت' : 'Aqari Smart', 'subtitle' => '', 'show_back_button' => false])

@section('full_width', true)

@section('content')
    @php
        $locale = app()->getLocale() === 'ar' ? 'ar' : 'en';
        $mobileTestData = config('mobiletestdata');
        $hero = $mobileTestData['hero'] ?? [];

        $t = function ($value, $fallback = null) use ($locale) {
            if (is_array($value)) {
                return $value[$locale] ?? $value['en'] ?? $fallback;
            }
            return $value ?? $fallback;
        };

        $baseDomain = config('tenancy.base_domain');
        $currentHost = request()->getHost();
        $isTenantDomain = $currentHost !== $baseDomain && $currentHost !== 'www.' . $baseDomain;
        $tenantSlug = $isTenantDomain ? explode('.', $currentHost)[0] : null;
    @endphp

    <div class="bg-gray-50 min-h-screen pb-12">

        {{-- ═══ Hero Section ═══ --}}
        <section class="bg-gradient-to-br from-emerald-700 via-emerald-800 to-emerald-900 text-white">
            <div class="px-5 pb-7 pt-6">
                <div class="mb-5">
                    <h1 class="text-2xl font-bold leading-snug tracking-tight">{{ $t($hero['title'] ?? null, 'Find your next property') }}</h1>
                    <p class="mt-1.5 text-sm leading-relaxed text-emerald-100/80">{{ $t($hero['subtitle'] ?? null, 'Curated apartments, villas, offices, and investment opportunities.') }}</p>
                </div>

                {{-- Search Form --}}
                <form id="mobile-marketplace-filter" class="space-y-3">
                    {{-- Segmented Buy / Rent Toggle --}}
                    <div class="flex rounded-xl bg-white/10 p-1 backdrop-blur-sm" id="listing-type-toggle">
                        <button type="button" data-value="" class="listing-toggle flex-1 rounded-lg py-2.5 text-center text-sm font-bold transition-all active:scale-[0.97] bg-white text-emerald-800 shadow-sm">
                            {{ app()->getLocale() === 'ar' ? 'الكل' : 'All' }}
                        </button>
                        <button type="button" data-value="sale" class="listing-toggle flex-1 rounded-lg py-2.5 text-center text-sm font-bold transition-all active:scale-[0.97] text-white/70 hover:text-white">
                            {{ app()->getLocale() === 'ar' ? 'شراء' : 'Buy' }}
                        </button>
                        <button type="button" data-value="rent" class="listing-toggle flex-1 rounded-lg py-2.5 text-center text-sm font-bold transition-all active:scale-[0.97] text-white/70 hover:text-white">
                            {{ app()->getLocale() === 'ar' ? 'إيجار' : 'Rent' }}
                        </button>
                    </div>
                    <input type="hidden" name="listing_type" id="listing_type_input" value="">

                    {{-- Search Input + Button --}}
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 rtl:right-0 rtl:left-auto rtl:pr-3.5">
                                <svg class="h-4.5 w-4.5 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input name="q" placeholder="{{ $t($hero['search_placeholder'] ?? null, 'Search city, area, or agent') }}" class="w-full rounded-xl border border-white/20 bg-white/10 py-3 pl-10 pr-4 rtl:pl-4 rtl:pr-10 text-sm text-white placeholder-white/50 backdrop-blur-sm transition-all focus:border-emerald-300/50 focus:bg-white/15 focus:outline-none focus:ring-2 focus:ring-emerald-300/30">
                        </div>
                        <button type="submit" class="flex items-center justify-center rounded-xl bg-white px-5 text-sm font-bold text-emerald-800 shadow-sm transition-all hover:bg-emerald-50 active:scale-95">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </button>
                    </div>

                    <input type="hidden" name="category_id" id="filter_category_id" value="">
                    <input type="hidden" name="city_id" id="filter_city_id" value="">
                </form>
            </div>
        </section>

        {{-- ═══ 1. Browse by Category ═══ --}}
        <section class="mt-6 px-5">
            <div class="mb-3">
                <h2 class="text-lg font-bold text-slate-800">{{ app()->getLocale() === 'ar' ? 'تصفح حسب الفئة' : 'Browse by category' }}</h2>
                <p class="mt-0.5 text-xs font-medium text-slate-400">{{ app()->getLocale() === 'ar' ? 'اختر نوع العقار المناسب لك' : 'Find the right property type' }}</p>
            </div>
            <div id="mp-categories" class="flex snap-x snap-mandatory gap-2.5 overflow-x-auto pb-2 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                <button type="button" class="category-filter shrink-0 snap-start flex flex-col items-center justify-center rounded-2xl bg-white px-5 py-3.5 shadow-sm ring-1 ring-slate-200 transition-all hover:shadow-md hover:ring-emerald-400 active:scale-95" data-id="">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 mb-2">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    </div>
                    <span class="text-xs font-bold text-slate-700">{{ app()->getLocale() === 'ar' ? 'الكل' : 'All' }}</span>
                </button>
            </div>
        </section>

        {{-- ═══ 2. Explore by City ═══ --}}
        <section class="mt-7 px-5">
            <div class="mb-3">
                <h2 class="text-lg font-bold text-slate-800">{{ app()->getLocale() === 'ar' ? 'استكشف حسب المدينة' : 'Explore by city' }}</h2>
                <p class="mt-0.5 text-xs font-medium text-slate-400">{{ app()->getLocale() === 'ar' ? 'تصفح المدن ذات الطلب المرتفع' : 'Browse high-demand cities' }}</p>
            </div>
            <div id="mp-cities" class="grid grid-cols-2 gap-2.5"></div>
        </section>

        {{-- ═══ 3. Top Agencies ═══ --}}
        <section class="mt-7 px-5">
            <div class="mb-3">
                <h2 class="text-lg font-bold text-slate-800">{{ app()->getLocale() === 'ar' ? 'أبرز الوكالات' : 'Top agencies' }}</h2>
                <p class="mt-0.5 text-xs font-medium text-slate-400">{{ app()->getLocale() === 'ar' ? 'وكالات عقارية موثوقة' : 'Trusted real estate agencies' }}</p>
            </div>
            <div id="mp-tenants" class="flex snap-x snap-mandatory gap-3 overflow-x-auto pb-2 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"></div>
        </section>

        {{-- ═══ 4. Recommended for You (latest units) ═══ --}}
        <section class="mt-7 px-5">
            <div class="mb-3">
                <h2 class="text-lg font-bold text-slate-800">{{ app()->getLocale() === 'ar' ? 'موصى به لك' : 'Recommended for you' }}</h2>
                <p class="mt-0.5 text-xs font-medium text-slate-400">{{ app()->getLocale() === 'ar' ? 'أحدث العقارات المتاحة' : 'Latest available properties' }}</p>
            </div>
            <div id="mp-recommended" class="flex snap-x snap-mandatory gap-3 overflow-x-auto pb-2 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"></div>
        </section>

        {{-- ═══ 5. Live Map ═══ --}}
        <section class="mt-7 px-5" id="map-section">
            <div class="mb-3">
                <h2 class="text-lg font-bold text-slate-800">{{ app()->getLocale() === 'ar' ? 'خريطة العقارات' : 'Properties map' }}</h2>
                <p class="mt-0.5 text-xs font-medium text-slate-400">{{ app()->getLocale() === 'ar' ? 'استكشف العقارات على الخريطة' : 'Explore listings on the map' }}</p>
            </div>
            <div id="mp-map" class="relative h-[280px] w-full overflow-hidden rounded-2xl bg-slate-100 shadow-sm ring-1 ring-slate-200">
                <div id="mp-map-placeholder" class="flex h-full flex-col items-center justify-center text-center">
                    <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50">
                        <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <p class="text-sm font-semibold text-slate-600">{{ app()->getLocale() === 'ar' ? 'جاري تحميل الخريطة...' : 'Loading map...' }}</p>
                </div>
            </div>
        </section>

        {{-- ═══ 6. Search Results Feed ═══ --}}
        <section class="mt-7 px-5" id="feed-section">
            <div class="mb-3">
                <h2 class="text-lg font-bold text-slate-800">{{ app()->getLocale() === 'ar' ? 'نتائج البحث' : 'Search results' }}</h2>
                <p class="mt-0.5 text-xs font-medium text-slate-400">{{ app()->getLocale() === 'ar' ? 'العقارات المطابقة لمعايير بحثك' : 'Properties matching your criteria' }}</p>
            </div>
            <div id="mobile-marketplace-results" class="grid gap-3 sm:grid-cols-2">
                <div class="col-span-full flex flex-col items-center justify-center rounded-2xl bg-white p-8 text-center shadow-sm ring-1 ring-slate-200">
                    <div class="h-5 w-5 animate-spin rounded-full border-2 border-emerald-600 border-t-transparent mb-3"></div>
                    <p class="text-xs font-semibold text-slate-500">{{ app()->getLocale() === 'ar' ? 'جاري التحميل...' : 'Loading...' }}</p>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
{{-- Leaflet CSS + JS for map --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
const lang = document.documentElement.lang === 'ar' ? 'ar' : 'en';
const marketplaceForm = document.getElementById('mobile-marketplace-filter');
const marketplaceResults = document.getElementById('mobile-marketplace-results');
let sectionsLoaded = false;
let mapInstance = null;
let mapMarkers = [];

function t(obj, fallback) {
    if (typeof obj === 'object' && obj !== null) return obj[lang] ?? obj['en'] ?? fallback;
    return obj ?? fallback;
}

// ── Segmented Toggle ──
document.querySelectorAll('.listing-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('listing_type_input').value = btn.dataset.value;
        document.querySelectorAll('.listing-toggle').forEach(b => {
            b.classList.remove('bg-white', 'text-emerald-800', 'shadow-sm');
            b.classList.add('text-white/70');
        });
        btn.classList.remove('text-white/70');
        btn.classList.add('bg-white', 'text-emerald-800', 'shadow-sm');
        loadMarketplace();
    });
});

// ── Unit Card ──
function unitCardHtml(unit) {
    const title = unit.translated_title ?? unit.title ?? unit.code;
    const photo = (unit.photos && unit.photos[0]) ? unit.photos[0] : 'https://picsum.photos/seed/aqarismart-fallback/900/640';
    const cityName = lang === 'ar' ? (unit.city?.name_ar ?? unit.city?.name_en ?? '') : (unit.city?.name_en ?? '');
    const propName = unit.property?.name ?? '';
    const loc = propName && cityName ? `${propName} · ${cityName}` : (propName || cityName);
    const typeBadge = unit.listing_type === 'sale' ? (lang === 'ar' ? 'للبيع' : 'Sale') : (lang === 'ar' ? 'للإيجار' : 'Rent');
    const badgeClass = unit.listing_type === 'sale' ? 'bg-emerald-600/90' : 'bg-sky-600/90';
    return `<a href="/mobile/units/${unit.code}" class="group block overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 transition-all hover:shadow-md hover:ring-emerald-400">
        <div class="relative aspect-[16/10] bg-slate-100">
            <img src="${photo}" alt="${title}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
            <div class="absolute left-3 top-3"><span class="inline-flex items-center rounded-lg ${badgeClass} backdrop-blur-sm px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-white">${typeBadge}</span></div>
            <div class="absolute bottom-3 left-3"><span class="inline-flex items-center rounded-lg bg-white/95 backdrop-blur-sm px-2.5 py-1 text-sm font-bold text-slate-900 shadow-sm">${unit.currency ?? 'JOD'} ${new Intl.NumberFormat().format(unit.price ?? 0)}</span></div>
        </div>
        <div class="p-3.5">
            <h3 class="text-sm font-bold text-slate-900 line-clamp-1">${title}</h3>
            <p class="mt-1 flex items-center text-xs font-medium text-slate-400 line-clamp-1">
                <svg class="mr-1 h-3 w-3 shrink-0 rtl:ml-1 rtl:mr-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                ${loc}
            </p>
            <div class="mt-3 flex items-center gap-3 border-t border-slate-100 pt-3 text-[11px] font-semibold text-slate-500">
                <span>${unit.bedrooms ?? unit.beds ?? 0} ${lang === 'ar' ? 'غرف' : 'beds'}</span>
                <span class="text-slate-200">·</span>
                <span>${unit.bathrooms ?? unit.baths ?? 0} ${lang === 'ar' ? 'حمامات' : 'baths'}</span>
            </div>
        </div>
    </a>`;
}

// ── Render Sections ──
function renderCategories(categories) {
    const container = document.getElementById('mp-categories');
    if (!container || !categories?.length) return;
    const allBtn = container.querySelector('.category-filter');
    container.innerHTML = '';
    if (allBtn) container.appendChild(allBtn);
    categories.forEach(cat => {
        const name = t(cat.name, 'Category');
        const hasImage = cat.image && !cat.image.includes('unsplash.com/photo-1564013799919');
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'category-filter shrink-0 snap-start flex flex-col items-center justify-center rounded-2xl bg-white px-5 py-3.5 shadow-sm ring-1 ring-slate-200 transition-all hover:shadow-md hover:ring-emerald-400 active:scale-95 min-w-[100px]';
        btn.dataset.id = cat.id ?? '';
        if (hasImage) {
            btn.innerHTML = `<div class="h-12 w-12 mb-2 overflow-hidden rounded-full ring-2 ring-slate-100"><img src="${cat.image}" alt="${name}" class="h-full w-full object-cover" loading="lazy" onerror="this.parentElement.innerHTML='<div class=\\'flex h-full w-full items-center justify-center bg-emerald-50 text-emerald-600\\'><svg class=\\'h-6 w-6\\' fill=\\'none\\' stroke=\\'currentColor\\' viewBox=\\'0 0 24 24\\'><path stroke-linecap=\\'round\\' stroke-linejoin=\\'round\\' stroke-width=\\'1.5\\' d=\\'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5\\'/></svg></div>'"></div>
                <span class="text-xs font-bold text-slate-700 truncate max-w-[80px]">${name}</span>
                <span class="text-[10px] font-medium text-slate-400">${new Intl.NumberFormat().format(cat.count ?? 0)}</span>`;
        } else {
            btn.innerHTML = `<div class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 mb-2">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></div>
                <span class="text-xs font-bold text-slate-700 truncate max-w-[80px]">${name}</span>
                <span class="text-[10px] font-medium text-slate-400">${new Intl.NumberFormat().format(cat.count ?? 0)}</span>`;
        }
        container.appendChild(btn);
    });
    bindCategoryFilters();
}

function renderCities(cities) {
    const container = document.getElementById('mp-cities');
    if (!container || !cities?.length) return;
    container.innerHTML = cities.map(city => {
        const name = lang === 'ar' ? (city.name_ar ?? city.name_en ?? '') : (city.name_en ?? '');
        return `<button type="button" class="city-filter text-left group overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 transition-all hover:shadow-md hover:ring-emerald-400 active:scale-[0.97]" data-id="${city.id ?? ''}">
            <div class="relative h-24 w-full"><img src="${city.image}" alt="${name}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy"><div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 to-transparent"></div>
            <div class="absolute bottom-2.5 left-3 right-3"><h3 class="text-sm font-bold text-white">${name}</h3><p class="text-[10px] font-bold text-emerald-300">${new Intl.NumberFormat().format(city.units_count ?? 0)} ${lang === 'ar' ? 'عقار' : 'properties'}</p></div></div></button>`;
    }).join('');
    bindCityFilters();
}

function renderTenants(tenants) {
    const container = document.getElementById('mp-tenants');
    if (!container || !tenants?.length) return;
    container.innerHTML = tenants.map(t => {
        const logo = t.branding?.logo_url ?? `https://ui-avatars.com/api/?name=${encodeURIComponent(t.name)}&color=059669&background=ecfdf5&bold=true`;
        const desc = t.summary?.description ?? (lang === 'ar' ? 'وكالة عقارية' : 'Real estate agency');
        return `<a href="/mobile/tenants/${t.slug}" class="block min-w-[240px] snap-start rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 transition-all hover:shadow-md hover:ring-emerald-400">
            <div class="flex items-center gap-3"><div class="h-11 w-11 shrink-0 overflow-hidden rounded-xl bg-slate-50 ring-1 ring-slate-100"><img src="${logo}" alt="${t.name}" class="h-full w-full object-cover" loading="lazy"></div>
            <div class="flex-1 min-w-0"><h3 class="text-sm font-bold text-slate-900 truncate">${t.name}</h3><p class="text-[11px] font-medium text-slate-400 truncate">${desc}</p></div></div>
            <div class="mt-3 flex items-center gap-4 border-t border-slate-100 pt-3">
                <div><span class="text-sm font-bold text-slate-800">${t.stats?.units_count ?? 0}</span> <span class="text-[10px] font-medium text-slate-400">${lang === 'ar' ? 'عقار' : 'listings'}</span></div>
                <div><span class="text-sm font-bold text-emerald-600">${t.stats?.active_units_count ?? 0}</span> <span class="text-[10px] font-medium text-slate-400">${lang === 'ar' ? 'نشط' : 'active'}</span></div>
            </div></a>`;
    }).join('');
}

function renderUnitScroll(containerId, units) {
    const container = document.getElementById(containerId);
    if (!container) return;
    if (!units?.length) { container.innerHTML = `<div class="flex items-center justify-center rounded-2xl bg-white p-6 text-xs font-medium text-slate-400 shadow-sm ring-1 ring-slate-200">${lang === 'ar' ? 'لا توجد عقارات حالياً' : 'No properties yet'}</div>`; return; }
    container.innerHTML = units.map(u => {
        const title = u.translated_title ?? u.title ?? u.code;
        const photo = (u.photos && u.photos[0]) ? u.photos[0] : 'https://picsum.photos/seed/aqarismart-fallback/900/640';
        const typeBadge = u.listing_type === 'sale' ? (lang === 'ar' ? 'للبيع' : 'Sale') : (lang === 'ar' ? 'للإيجار' : 'Rent');
        const badgeClass = u.listing_type === 'sale' ? 'bg-emerald-600/90' : 'bg-sky-600/90';
        return `<a href="/mobile/units/${u.code}" class="group block w-[260px] shrink-0 snap-start overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 transition-all hover:shadow-md hover:ring-emerald-400">
            <div class="relative h-40 w-full overflow-hidden bg-slate-100"><img src="${photo}" alt="${title}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
            <div class="absolute left-2.5 top-2.5"><span class="inline-flex items-center rounded-lg ${badgeClass} backdrop-blur-sm px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-white">${typeBadge}</span></div>
            <div class="absolute bottom-2.5 left-2.5"><span class="inline-flex items-center rounded-lg bg-white/95 backdrop-blur-sm px-2 py-1 text-xs font-bold text-slate-900 shadow-sm">${u.currency ?? 'JOD'} ${new Intl.NumberFormat().format(u.price ?? 0)}</span></div></div>
            <div class="p-3"><h3 class="text-sm font-bold text-slate-900 line-clamp-1">${title}</h3>
            <div class="mt-2 flex items-center gap-3 text-[11px] font-semibold text-slate-400"><span>${u.bedrooms ?? u.beds ?? 0} ${lang === 'ar' ? 'غرف' : 'beds'}</span><span class="text-slate-200">·</span><span>${u.bathrooms ?? u.baths ?? 0} ${lang === 'ar' ? 'حمامات' : 'baths'}</span></div></div></a>`;
    }).join('');
}

// ── Map ──
function renderMap(units) {
    const mapEl = document.getElementById('mp-map');
    const placeholder = document.getElementById('mp-map-placeholder');
    if (!mapEl) return;

    const geoUnits = (units || []).filter(u => u.lat && u.lng && u.lat !== 0 && u.lng !== 0);

    if (!geoUnits.length) {
        if (placeholder) placeholder.innerHTML = `<div class="flex h-full flex-col items-center justify-center text-center p-6">
            <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-50"><svg class="h-6 w-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg></div>
            <p class="text-sm font-semibold text-slate-600">${lang === 'ar' ? 'لا توجد مواقع متاحة بعد' : 'No locations available yet'}</p>
            <p class="mt-1 text-xs text-slate-400">${lang === 'ar' ? 'ستظهر العقارات على الخريطة عند إضافة إحداثياتها' : 'Properties will appear once coordinates are added'}</p></div>`;
        return;
    }

    if (placeholder) placeholder.style.display = 'none';

    if (!mapInstance) {
        mapInstance = L.map(mapEl, { zoomControl: false, attributionControl: false }).setView([geoUnits[0].lat, geoUnits[0].lng], 10);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 18 }).addTo(mapInstance);
        L.control.zoom({ position: 'topright' }).addTo(mapInstance);
        L.control.attribution({ position: 'bottomright', prefix: false }).addTo(mapInstance).addAttribution('© <a href="https://osm.org">OSM</a>');
        setTimeout(() => mapInstance.invalidateSize(), 200);
    }

    mapMarkers.forEach(m => m.remove());
    mapMarkers = [];

    const bounds = L.latLngBounds();
    geoUnits.forEach(u => {
        const title = u.translated_title ?? u.title ?? u.code;
        const price = `${u.currency ?? 'JOD'} ${new Intl.NumberFormat().format(u.price ?? 0)}`;
        const marker = L.marker([u.lat, u.lng]).addTo(mapInstance);
        marker.bindPopup(`<div style="min-width:160px"><b style="font-size:13px">${title}</b><br><span style="color:#059669;font-weight:700">${price}</span><br><a href="/mobile/units/${u.code}" style="color:#059669;font-size:12px;font-weight:600">View →</a></div>`);
        mapMarkers.push(marker);
        bounds.extend([u.lat, u.lng]);
    });

    if (bounds.isValid()) mapInstance.fitBounds(bounds, { padding: [30, 30], maxZoom: 13 });
}

// ── Feed ──
function renderFeed(units) {
    if (!units?.length) {
        marketplaceResults.innerHTML = `<div class="col-span-full flex flex-col items-center justify-center rounded-2xl bg-white p-8 text-center shadow-sm ring-1 ring-slate-200">
            <svg class="h-8 w-8 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <p class="text-sm font-semibold text-slate-700">${lang === 'ar' ? 'لم يتم العثور على نتائج' : 'No results found'}</p>
            <p class="mt-1 text-xs text-slate-400">${lang === 'ar' ? 'حاول تغيير معايير البحث' : 'Try adjusting your search criteria'}</p></div>`;
        return;
    }
    marketplaceResults.innerHTML = units.map(unitCardHtml).join('');
}

// ── Load Marketplace ──
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
        sectionsLoaded = true;
    }

    renderFeed(json.data ?? []);
    renderMap(json.data ?? []);
}

// ── Bindings ──
function bindCategoryFilters() {
    document.querySelectorAll('.category-filter').forEach(btn => {
        btn.addEventListener('click', async () => {
            document.querySelectorAll('.category-filter').forEach(b => b.classList.remove('ring-emerald-500', 'bg-emerald-50'));
            btn.classList.add('ring-emerald-500', 'bg-emerald-50');
            const f = document.getElementById('filter_category_id');
            if (f) f.value = btn.dataset.id ?? '';
            await loadMarketplace();
        });
    });
}

function bindCityFilters() {
    document.querySelectorAll('.city-filter').forEach(btn => {
        btn.addEventListener('click', async () => {
            const f = document.getElementById('filter_city_id');
            if (f) f.value = btn.dataset.id ?? '';
            await loadMarketplace();
            document.getElementById('feed-section')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
}

bindCategoryFilters();

marketplaceForm?.addEventListener('submit', async (e) => {
    e.preventDefault();
    await loadMarketplace();
    document.getElementById('feed-section')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
});

let debounceTimer;
marketplaceForm?.querySelector('input[name="q"]')?.addEventListener('input', () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => loadMarketplace(), 500);
});

loadMarketplace();
</script>
@endpush
