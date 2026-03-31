@extends('mobile.layouts.app', ['title' => app()->getLocale() === 'ar' ? 'عقاري سمارت' : 'Aqari Smart', 'subtitle' => '', 'show_back_button' => false])

@section('content')
    @php
        $locale = app()->getLocale() === 'ar' ? 'ar' : 'en';
        $mobileTestData = config('mobiletestdata');
        $hero = $mobileTestData['hero'] ?? [];
        $banners = $mobileTestData['banners'] ?? [];
        $quickActions = $mobileTestData['quick_actions'] ?? [];
        
        $t = function ($value, $fallback = null) use ($locale) {
            if (is_array($value)) {
                return $value[$locale] ?? $value['en'] ?? $fallback;
            }
            return $value ?? $fallback;
        };
        $bannerThemes = [
            'primary' => 'from-brand-600 via-rose-600 to-orange-500 text-white',
            'rose' => 'from-rose-500 via-pink-500 to-orange-400 text-white',
            'dark' => 'from-slate-900 via-slate-800 to-slate-700 text-white',
        ];
        
        // Check if we're on a tenant subdomain
        $baseDomain = config('tenancy.base_domain');
        $currentHost = request()->getHost();
        $isTenantDomain = $currentHost !== $baseDomain && $currentHost !== 'www.' . $baseDomain;
        $tenantSlug = $isTenantDomain ? explode('.', $currentHost)[0] : null;
    @endphp

    <div class="space-y-6 bg-gray-50 min-h-screen pb-10">
        <!-- Hero Section -->
        <section class="overflow-hidden bg-gradient-to-br from-emerald-700 via-emerald-800 to-emerald-900 text-white shadow-md">
            <div class="space-y-6 px-5 py-8">
                <div class="space-y-3">
                    <h1 class="text-3xl font-bold leading-tight">{{ $t($hero['title'] ?? null, 'Find your next property') }}</h1>
                    <p class="max-w-xl text-base leading-relaxed text-emerald-100/90">{{ $t($hero['subtitle'] ?? null, 'Browse verified listings built for mobile discovery.') }}</p>
                </div>

                <!-- Search Form -->
                <form id="mobile-marketplace-filter" class="space-y-4 rounded-2xl bg-white/10 backdrop-blur-md p-5 text-white shadow-lg border border-white/20">
                    <label class="block">
                        <span class="sr-only">Search marketplace</span>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 rtl:right-0 rtl:left-auto rtl:pr-4">
                                <svg class="h-5 w-5 text-emerald-100/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input name="q" placeholder="{{ $t($hero['search_placeholder'] ?? null, 'Search properties') }}" class="w-full rounded-xl border border-white/30 bg-white/15 backdrop-blur-sm py-3.5 pl-11 pr-4 rtl:pl-4 rtl:pr-11 text-base text-white placeholder-emerald-100/70 focus:border-emerald-300 focus:bg-white/25 focus:outline-none focus:ring-2 focus:ring-emerald-300/50 transition-all">
                        </div>
                    </label>
                    <div class="grid grid-cols-[1fr_auto] gap-3">
                        <select name="listing_type" class="rounded-xl border border-white/30 bg-white/15 backdrop-blur-sm px-4 py-3.5 text-base font-medium text-white focus:border-emerald-300 focus:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-300/50 transition-all appearance-none">
                            <option value="" class="text-slate-800">{{ app()->getLocale() === 'ar' ? 'جميع الأنواع' : 'All types' }}</option>
                            <option value="sale" class="text-slate-800">{{ app()->getLocale() === 'ar' ? 'للبيع' : 'For Sale' }}</option>
                            <option value="rent" class="text-slate-800">{{ app()->getLocale() === 'ar' ? 'للإيجار' : 'For Rent' }}</option>
                        </select>
                        <button type="submit" class="flex items-center justify-center rounded-xl bg-white text-emerald-800 px-6 py-3.5 text-base font-bold shadow-md transition-all hover:bg-emerald-50 hover:shadow-lg active:scale-95">
                            {{ app()->getLocale() === 'ar' ? 'بحث' : 'Search' }}
                        </button>
                    </div>
                    <!-- Hidden filters for programmatic filtering -->
                    <input type="hidden" name="category_id" id="filter_category_id" value="">
                    <input type="hidden" name="city_id" id="filter_city_id" value="">
                </form>

                <!-- Quick Actions -->
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 pt-2">
                    @foreach ($quickActions as $action)
                        <button type="button" class="quick-action group relative overflow-hidden rounded-xl border border-white/20 bg-white/5 backdrop-blur-sm p-4 text-left transition-all hover:border-white/40 hover:bg-white/10" data-value="{{ $action['value'] }}">
                            <div class="text-sm font-bold text-white group-hover:text-emerald-100">{{ $t($action['label'] ?? null, 'Action') }}</div>
                            <div class="mt-1 text-xs font-medium text-emerald-200/70">{{ app()->getLocale() === 'ar' ? 'تصفية سريعة' : 'Quick filter' }}</div>
                        </button>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Categories Section -->
        <section class="space-y-4 px-5">
            <div class="flex items-end justify-between">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">{{ app()->getLocale() === 'ar' ? 'تصفح حسب الفئة' : 'Browse by category' }}</h2>
                    <p class="mt-1 text-sm font-medium text-slate-500">{{ app()->getLocale() === 'ar' ? 'اختر نوع العقار المناسب لك' : 'Find the right property type for you' }}</p>
                </div>
            </div>
            
            <!-- Horizontal scrolling categories for mobile — populated by JS -->
            <div id="mp-categories" class="flex snap-x snap-mandatory gap-3 overflow-x-auto pb-4 pt-1 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                <button type="button" class="category-filter shrink-0 snap-start flex-col items-center justify-center rounded-2xl bg-white px-6 py-4 shadow-sm ring-1 ring-slate-200 transition-all hover:-translate-y-1 hover:shadow-md hover:ring-emerald-400 active:scale-95" data-id="">
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 mb-3">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    </div>
                    <div class="text-sm font-bold text-slate-800">{{ app()->getLocale() === 'ar' ? 'الكل' : 'All' }}</div>
                </button>
            </div>
        </section>

        <!-- Featured Campaigns -->
        <section class="space-y-4 px-5 pt-2">
            <div class="flex items-end justify-between">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">{{ app()->getLocale() === 'ar' ? 'الحملات المميزة' : 'Featured campaigns' }}</h2>
                    <p class="mt-1 text-sm font-medium text-slate-500">{{ app()->getLocale() === 'ar' ? 'عروض خاصة وعروض ترويجية من الوكلاء الموثوقين' : 'Special offers and promotions from trusted agents' }}</p>
                </div>
            </div>
            <div class="flex snap-x snap-mandatory gap-4 overflow-x-auto pb-4 pt-1 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                @foreach ($banners as $banner)
                    <article class="relative min-w-[300px] max-w-[320px] flex-1 snap-start overflow-hidden rounded-2xl shadow-md ring-1 ring-slate-200 transition-transform hover:-translate-y-1 hover:shadow-lg">
                        <img src="{{ $banner['image'] }}" alt="{{ $t($banner['title'] ?? null, 'Banner') }}" class="h-56 w-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t {{ $bannerThemes[$banner['theme'] ?? 'primary'] ?? $bannerThemes['primary'] }} opacity-90"></div>
                        <div class="absolute inset-0 flex flex-col justify-end p-6">
                            <div class="mb-auto">
                                <span class="inline-flex items-center rounded-full bg-white/20 px-2.5 py-1 text-[10px] font-bold uppercase tracking-widest text-white backdrop-blur-sm border border-white/30">
                                    {{ app()->getLocale() === 'ar' ? 'متميز' : 'Featured' }}
                                </span>
                            </div>
                            <h3 class="text-2xl font-bold leading-tight text-white drop-shadow-sm">{{ $t($banner['title'] ?? null, 'Featured') }}</h3>
                            <p class="mt-2 text-sm font-medium leading-relaxed text-white/90 drop-shadow-sm">{{ $t($banner['subtitle'] ?? null, '') }}</p>
                            <button type="button" class="mt-5 inline-flex w-fit items-center justify-center rounded-xl bg-white px-5 py-2.5 text-sm font-bold text-slate-900 shadow-sm transition-all hover:bg-slate-50 active:scale-95">
                                {{ $t($banner['cta'] ?? null, app()->getLocale() === 'ar' ? 'استكشف' : 'Explore') }}
                                <svg class="ml-2 h-4 w-4 rtl:rotate-180 rtl:ml-0 rtl:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <!-- Featured Listings — populated by JS -->
        <section class="space-y-4 px-5 pt-2">
            <div class="flex items-end justify-between">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">{{ app()->getLocale() === 'ar' ? 'العقارات المميزة' : 'Featured listings' }}</h2>
                    <p class="mt-1 text-sm font-medium text-slate-500">{{ app()->getLocale() === 'ar' ? 'عقارات مختارة بعناية للبيع' : 'Hand-picked properties for sale' }}</p>
                </div>
            </div>
            <div id="mp-featured" class="flex snap-x snap-mandatory gap-4 overflow-x-auto pb-4 pt-1 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"></div>
        </section>

        <!-- Popular Cities — populated by JS -->
        <section class="space-y-4 px-5 pt-2">
            <div class="flex items-end justify-between">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">{{ app()->getLocale() === 'ar' ? 'استكشف حسب المدينة' : 'Explore by city' }}</h2>
                    <p class="mt-1 text-sm font-medium text-slate-500">{{ app()->getLocale() === 'ar' ? 'تصفح المدن ذات الطلب المرتفع' : 'Browse high-demand cities and neighborhoods' }}</p>
                </div>
            </div>
            <div id="mp-cities" class="grid grid-cols-2 gap-3 sm:grid-cols-4"></div>
        </section>

        <!-- Recommended Listings — populated by JS -->
        <section class="space-y-4 px-5 pt-2">
            <div class="flex items-end justify-between">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">{{ app()->getLocale() === 'ar' ? 'موصى به لك' : 'Recommended for you' }}</h2>
                    <p class="mt-1 text-sm font-medium text-slate-500">{{ app()->getLocale() === 'ar' ? 'عقارات للإيجار قد تعجبك' : 'Properties for rent you might like' }}</p>
                </div>
            </div>
            <div id="mp-recommended" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3"></div>
        </section>

        <!-- Top Agencies — populated by JS -->
        <section class="space-y-4 px-5 pt-2">
            <div class="flex items-end justify-between">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">{{ app()->getLocale() === 'ar' ? 'أبرز الوكالات' : 'Top agencies' }}</h2>
                    <p class="mt-1 text-sm font-medium text-slate-500">{{ app()->getLocale() === 'ar' ? 'وكالات ومديرو عقارات موثوقون' : 'Trusted agencies and property managers' }}</p>
                </div>
            </div>
            <div id="mp-tenants" class="flex snap-x snap-mandatory gap-4 overflow-x-auto pb-4 pt-1 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"></div>
        </section>

        <!-- Live Feed / Search Results -->
        <section class="space-y-4 px-5 pt-4 border-t border-slate-200 mt-6" id="feed-section">
            <div class="flex items-end justify-between">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">{{ app()->getLocale() === 'ar' ? 'نتائج البحث' : 'Live marketplace feed' }}</h2>
                    <p class="mt-1 text-sm font-medium text-slate-500">{{ app()->getLocale() === 'ar' ? 'أحدث العقارات المطابقة لبحثك' : 'Latest properties matching your criteria' }}</p>
                </div>
            </div>
            <div id="mobile-marketplace-results" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Initial empty state before JS loads -->
                <div class="col-span-full flex flex-col items-center justify-center rounded-2xl bg-white p-10 text-center shadow-sm ring-1 ring-slate-200">
                    <div class="rounded-full bg-emerald-50 p-4 mb-4">
                        <div class="h-6 w-6 animate-spin rounded-full border-2 border-emerald-600 border-t-transparent"></div>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800">{{ app()->getLocale() === 'ar' ? 'جاري التحميل...' : 'Loading feed...' }}</h3>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script>
const lang = document.documentElement.lang === 'ar' ? 'ar' : 'en';
const marketplaceForm = document.getElementById('mobile-marketplace-filter');
const marketplaceResults = document.getElementById('mobile-marketplace-results');
let sectionsLoaded = false;

function t(obj, fallback) {
    if (typeof obj === 'object' && obj !== null) return obj[lang] ?? obj['en'] ?? fallback;
    return obj ?? fallback;
}

function unitCardHtml(unit) {
    const title = unit.translated_title ?? unit.title ?? unit.code;
    const photo = (unit.photos && unit.photos[0]) ? unit.photos[0] : 'https://picsum.photos/seed/aqarismart-fallback/900/640';
    const cityName = lang === 'ar' ? (unit.city?.name_ar ?? unit.city?.name_en ?? '') : (unit.city?.name_en ?? '');
    const propName = unit.property?.name ?? '';
    const loc = propName && cityName ? `${propName} · ${cityName}` : (propName || cityName);
    const typeBadge = unit.listing_type === 'sale' ? (lang === 'ar' ? 'للبيع' : 'For Sale') : (lang === 'ar' ? 'للإيجار' : 'For Rent');
    return `<a href="/mobile/units/${unit.code}" class="group block overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 transition-all hover:-translate-y-1 hover:shadow-md hover:ring-emerald-400">
        <div class="relative aspect-[16/10] bg-slate-100">
            <img src="${photo}" alt="${title}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
            <div class="absolute left-3 top-3"><span class="inline-flex items-center rounded-lg bg-emerald-600/90 backdrop-blur-sm px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-white shadow-sm">${typeBadge}</span></div>
            <div class="absolute bottom-3 left-3"><span class="inline-flex items-center rounded-xl bg-white/95 backdrop-blur-sm px-3 py-1.5 text-sm font-bold text-slate-900 shadow-sm">${unit.currency ?? 'JOD'} ${new Intl.NumberFormat().format(unit.price ?? 0)}</span></div>
        </div>
        <div class="p-4">
            <div class="mb-2 flex items-center justify-between gap-2">
                <span class="text-xs font-bold text-emerald-600">${unit.subcategory?.name ?? 'Property'}</span>
                <span class="text-[11px] font-medium text-slate-400">${unit.code}</span>
            </div>
            <h3 class="text-base font-bold text-slate-900 line-clamp-1 group-hover:text-emerald-700 transition-colors">${title}</h3>
            <p class="mt-1 flex items-center text-sm font-medium text-slate-500 line-clamp-1">
                <svg class="mr-1 h-3.5 w-3.5 shrink-0 rtl:ml-1 rtl:mr-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                ${loc}
            </p>
            <div class="mt-4 flex items-center gap-4 border-t border-slate-100 pt-4 text-xs font-semibold text-slate-600">
                <div class="flex items-center gap-1.5">
                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>${unit.bedrooms ?? unit.beds ?? 0} ${lang === 'ar' ? 'غرف' : 'beds'}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                    <span>${unit.bathrooms ?? unit.baths ?? 0} ${lang === 'ar' ? 'حمامات' : 'baths'}</span>
                </div>
            </div>
        </div>
    </a>`;
}

function renderCategories(categories) {
    const container = document.getElementById('mp-categories');
    if (!container || !categories?.length) return;
    const allBtn = container.querySelector('.category-filter');
    container.innerHTML = '';
    if (allBtn) container.appendChild(allBtn);
    categories.forEach(cat => {
        const name = t(cat.name, 'Category');
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'category-filter shrink-0 snap-start group relative overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 transition-all hover:-translate-y-1 hover:shadow-md hover:ring-emerald-400 active:scale-95 w-[140px]';
        btn.dataset.id = cat.id ?? '';
        btn.innerHTML = `<div class="h-20 w-full overflow-hidden"><img src="${cat.image}" alt="${name}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110" loading="lazy"><div class="absolute inset-x-0 top-0 h-20 bg-gradient-to-t from-black/60 to-transparent"></div></div><div class="bg-white p-3 text-center"><h3 class="text-sm font-bold text-slate-800 truncate">${name}</h3><p class="mt-0.5 text-[11px] font-medium text-slate-500">${new Intl.NumberFormat().format(cat.count ?? 0)} ${lang === 'ar' ? 'إعلان' : 'listings'}</p></div>`;
        container.appendChild(btn);
    });
    bindCategoryFilters();
}

function renderCities(cities) {
    const container = document.getElementById('mp-cities');
    if (!container || !cities?.length) return;
    container.innerHTML = cities.map(city => {
        const name = lang === 'ar' ? (city.name_ar ?? city.name_en ?? '') : (city.name_en ?? '');
        return `<button type="button" class="city-filter text-left group overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 transition-all hover:-translate-y-1 hover:shadow-md hover:ring-emerald-400 active:scale-95" data-id="${city.id ?? ''}">
            <div class="relative h-24 w-full"><img src="${city.image}" alt="${name}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110" loading="lazy"><div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 to-transparent"></div>
            <div class="absolute bottom-3 left-3 right-3"><h3 class="text-sm font-bold text-white shadow-sm">${name}</h3><p class="mt-0.5 text-[10px] font-bold text-emerald-300">${new Intl.NumberFormat().format(city.units_count ?? 0)} ${lang === 'ar' ? 'عقار' : 'properties'}</p></div></div></button>`;
    }).join('');
    bindCityFilters();
}

function renderTenants(tenants) {
    const container = document.getElementById('mp-tenants');
    if (!container || !tenants?.length) return;
    container.innerHTML = tenants.map(t => {
        const logo = t.branding?.logo_url ?? `https://ui-avatars.com/api/?name=${encodeURIComponent(t.name)}&color=059669&background=ecfdf5`;
        const desc = t.summary?.description ?? (lang === 'ar' ? 'وكالة عقارية محترفة' : 'Professional real estate agency');
        return `<a href="/mobile/tenants/${t.slug}" class="block min-w-[260px] snap-start rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 transition-all hover:-translate-y-1 hover:shadow-md hover:ring-emerald-400">
            <div class="flex items-start gap-4"><div class="h-14 w-14 shrink-0 overflow-hidden rounded-xl bg-slate-50 ring-1 ring-slate-100"><img src="${logo}" alt="${t.name}" class="h-full w-full object-cover" loading="lazy"></div>
            <div class="flex-1 space-y-1"><h3 class="font-bold text-slate-900 line-clamp-1">${t.name}</h3><p class="text-xs font-medium text-slate-500 line-clamp-1">${desc}</p></div></div>
            <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-4">
                <div class="text-center"><div class="text-sm font-bold text-slate-800">${t.stats?.units_count ?? 0}</div><div class="mt-0.5 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">${lang === 'ar' ? 'عقار' : 'Props'}</div></div>
                <div class="text-center"><div class="text-sm font-bold text-emerald-600">${t.stats?.active_units_count ?? t.stats?.units_count ?? 0}</div><div class="mt-0.5 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">${lang === 'ar' ? 'نشطة' : 'Active'}</div></div>
            </div></a>`;
    }).join('');
}

function renderUnitScroll(containerId, units) {
    const container = document.getElementById(containerId);
    if (!container) return;
    if (!units?.length) { container.innerHTML = ''; return; }
    container.innerHTML = units.map(u => {
        const title = u.translated_title ?? u.title ?? u.code;
        const photo = (u.photos && u.photos[0]) ? u.photos[0] : 'https://picsum.photos/seed/aqarismart-fallback/900/640';
        const typeBadge = u.listing_type === 'sale' ? (lang === 'ar' ? 'للبيع' : 'For Sale') : (lang === 'ar' ? 'للإيجار' : 'For Rent');
        return `<a href="/mobile/units/${u.code}" class="group block min-w-[280px] max-w-[300px] snap-start overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 transition-all hover:-translate-y-1 hover:shadow-md hover:ring-emerald-400">
            <div class="relative h-48 w-full overflow-hidden bg-slate-100"><img src="${photo}" alt="${title}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
            <div class="absolute left-3 top-3"><span class="inline-flex items-center rounded-lg bg-emerald-600/90 backdrop-blur-sm px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-white shadow-sm">${typeBadge}</span></div>
            <div class="absolute bottom-3 left-3"><span class="inline-flex items-center rounded-xl bg-white/95 backdrop-blur-sm px-3 py-1.5 text-sm font-bold text-slate-900 shadow-sm">${u.currency ?? 'JOD'} ${new Intl.NumberFormat().format(u.price ?? 0)}</span></div></div>
            <div class="p-4"><h3 class="text-base font-bold text-slate-900 line-clamp-1">${title}</h3>
            <div class="mt-3 flex items-center gap-4 text-xs font-semibold text-slate-600"><span>${u.bedrooms ?? u.beds ?? 0} ${lang === 'ar' ? 'غرف' : 'beds'}</span><span>${u.bathrooms ?? u.baths ?? 0} ${lang === 'ar' ? 'حمامات' : 'baths'}</span></div></div></a>`;
    }).join('');
}

function renderFeed(units) {
    if (!units?.length) {
        marketplaceResults.innerHTML = `<div class="col-span-full flex flex-col items-center justify-center rounded-2xl bg-white p-10 text-center shadow-sm ring-1 ring-slate-200">
            <div class="rounded-full bg-slate-50 p-4 mb-4"><svg class="h-6 w-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg></div>
            <h3 class="text-sm font-bold text-slate-800">${lang === 'ar' ? 'لم يتم العثور على نتائج' : 'No results found'}</h3>
            <p class="mt-1 text-xs font-medium text-slate-500">${lang === 'ar' ? 'حاول تغيير معايير البحث الخاصة بك' : 'Try adjusting your search criteria'}</p></div>`;
        return;
    }
    marketplaceResults.innerHTML = units.map(unitCardHtml).join('');
}

async function loadMarketplace() {
    const params = new URLSearchParams(new FormData(marketplaceForm));
    const response = await fetch(`/api/mobile/marketplace?${params.toString()}`, { headers: { Accept: 'application/json' } });
    const json = await response.json();

    if (!sectionsLoaded) {
        renderCategories(json.categories ?? []);
        renderCities(json.cities ?? []);
        renderTenants(json.tenants ?? []);
        renderUnitScroll('mp-featured', json.featured_units ?? []);
        renderUnitScroll('mp-recommended', json.recommended_units ?? []);
        sectionsLoaded = true;
    }

    renderFeed(json.data ?? []);
}

function bindCategoryFilters() {
    document.querySelectorAll('.category-filter').forEach(btn => {
        btn.addEventListener('click', async () => {
            const f = document.getElementById('filter_category_id');
            if (f) f.value = btn.dataset.id ?? '';
            await loadMarketplace();
            document.getElementById('feed-section')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
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

document.querySelectorAll('.quick-action').forEach(btn => {
    btn.addEventListener('click', async () => {
        const sel = marketplaceForm?.querySelector('select[name="listing_type"]');
        if (sel) sel.value = ['sale', 'rent'].includes(btn.dataset.value) ? btn.dataset.value : '';
        await loadMarketplace();
        document.getElementById('feed-section')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
});

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
