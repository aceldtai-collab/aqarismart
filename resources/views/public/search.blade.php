@php
    $loc = app()->getLocale();
    $isAr = $loc === 'ar';
    $assets = $landing['assets'] ?? [];
    $langParam = config('locales.cookie_name', 'lang');
    $urlEn = request()->fullUrlWithQuery([$langParam => 'en']);
    $urlAr = request()->fullUrlWithQuery([$langParam => 'ar']);
@endphp
<!DOCTYPE html>
<html lang="{{ $loc }}" dir="{{ $isAr ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $isAr ? 'استكشف العقارات' : 'Explore Properties' }} — Aqari Smart</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @if($isAr)
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @endif
    <x-vite-assets />
    <style>
        [x-cloak]{display:none!important}
        *{box-sizing:border-box}
        body{font-family:'Inter',system-ui,sans-serif;margin:0;-webkit-font-smoothing:antialiased;background:#f8fafc}
        @if($isAr) body{font-family:'Noto Sans Arabic','Inter',system-ui,sans-serif} @endif
        .s-input{display:block;width:100%;padding:10px 13px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:.8125rem;background:#fff;transition:all .2s;color:#0f172a}
        .s-input:focus{outline:none;border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,.08)}
        .s-input::placeholder{color:#94a3b8}
        .s-select{display:block;width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:.8125rem;background:#fff;transition:all .2s;color:#0f172a;appearance:none;background-image:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");background-position:right 10px center;background-repeat:no-repeat;background-size:16px}
        @if($isAr) .s-select{background-position:left 10px center} @endif
        .s-select:focus{outline:none;border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,.08)}
        .u-card{border-radius:16px;overflow:hidden;border:1px solid #e2e8f0;background:#fff;transition:all .3s}
        .u-card:hover{box-shadow:0 20px 40px rgba(15,23,42,.08);transform:translateY(-4px);border-color:#c7d2fe}
        .u-card img{transition:transform .5s}
        .u-card:hover img{transform:scale(1.05)}
    </style>
</head>
<body class="antialiased">

{{-- Nav --}}
<nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-xl border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 flex items-center justify-between h-16">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-sm">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/></svg>
            </div>
            <span class="text-lg font-bold text-slate-900">Aqari Smart</span>
        </a>
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-1 rounded-lg border border-slate-200/80 p-0.5 text-xs font-medium bg-white shadow-sm">
                <a href="{{ $urlEn }}" class="px-3 py-1.5 rounded-md transition {{ $loc==='en' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-500 hover:bg-slate-50' }}">EN</a>
                <a href="{{ $urlAr }}" class="px-3 py-1.5 rounded-md transition {{ $loc==='ar' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-500 hover:bg-slate-50' }}">ع</a>
            </div>
            @if(Route::has('login'))
                <a href="{{ route('login') }}" class="hidden sm:inline-flex px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition">{{ $isAr ? 'تسجيل الدخول' : 'Sign in' }}</a>
            @endif
            @if(Route::has('register'))
                <a href="{{ route('register') }}" class="hidden sm:inline-flex px-4 py-2 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 text-white text-sm font-semibold shadow-sm hover:shadow-md transition">{{ $isAr ? 'ابدأ مجاناً' : 'Get Started' }}</a>
            @endif
        </div>
    </div>
</nav>

{{-- Hero Search --}}
<div class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 relative overflow-hidden">
    <div class="absolute inset-0 opacity-30" style="background-image:radial-gradient(circle at 30% 50%,rgba(99,102,241,.3) 0%,transparent 50%),radial-gradient(circle at 70% 80%,rgba(139,92,246,.2) 0%,transparent 50%)"></div>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-12 sm:py-16 relative z-10">
        <div class="text-center mb-8">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight mb-3">{{ $isAr ? 'ابحث عن عقارك المثالي' : 'Find your perfect property' }}</h1>
            <p class="text-base text-white/40 max-w-lg mx-auto">{{ $isAr ? 'تصفّح آلاف الوحدات من أفضل الشركات العقارية في الأردن' : 'Browse thousands of units from top property companies in Jordan' }}</p>
        </div>
        <form method="GET" action="{{ route('public.search') }}" class="bg-white rounded-2xl p-4 sm:p-5 shadow-2xl shadow-black/20">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <svg class="absolute ltr:left-3.5 rtl:right-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="s-input ltr:pl-11 rtl:pr-11" placeholder="{{ $isAr ? 'ابحث بالاسم، الكود، أو الموقع...' : 'Search by name, code, or location...' }}">
                </div>
                <select name="listing_type" class="s-select sm:w-40">
                    <option value="">{{ $isAr ? 'الكل' : 'All Types' }}</option>
                    <option value="rent" {{ ($filters['listing_type'] ?? '') === 'rent' ? 'selected' : '' }}>{{ $isAr ? 'للإيجار' : 'For Rent' }}</option>
                    <option value="sale" {{ ($filters['listing_type'] ?? '') === 'sale' ? 'selected' : '' }}>{{ $isAr ? 'للبيع' : 'For Sale' }}</option>
                </select>
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 text-white text-sm font-semibold shadow-md shadow-indigo-500/20 hover:shadow-lg hover:-translate-y-0.5 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    {{ $isAr ? 'بحث' : 'Search' }}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Main Content --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
    <div class="flex flex-col lg:flex-row gap-6">

        {{-- Sidebar --}}
        <aside class="lg:w-72 flex-shrink-0" x-data="{ open: false }">
            <button @click="open = !open" class="lg:hidden w-full flex items-center justify-between px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm font-semibold text-slate-700 mb-4">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"/></svg>
                    {{ $isAr ? 'الفلاتر' : 'Filters' }}
                </span>
                <svg class="w-4 h-4 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
            </button>
            <form method="GET" action="{{ route('public.search') }}" class="space-y-4" :class="{ 'hidden lg:block': !open }" x-cloak>
                @if(!empty($filters['q']))<input type="hidden" name="q" value="{{ $filters['q'] }}">@endif
                <div class="bg-white rounded-2xl border border-slate-100 p-5 space-y-5">
                    <h3 class="text-sm font-bold text-slate-900">{{ $isAr ? 'تصفية النتائج' : 'Filter Results' }}</h3>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">{{ $isAr ? 'نوع العقار' : 'Property Type' }}</label>
                        <select name="subcategory_id" class="s-select">
                            <option value="">{{ $isAr ? 'الكل' : 'All' }}</option>
                            @foreach($categories as $cat)
                                <optgroup label="{{ $cat->name }}">
                                    @foreach($cat->subcategories as $sub)
                                        <option value="{{ $sub->id }}" {{ ($filters['subcategory_id'] ?? '') == $sub->id ? 'selected' : '' }}>{{ $sub->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">{{ $isAr ? 'المدينة' : 'City' }}</label>
                        <select name="city_id" class="s-select">
                            <option value="">{{ $isAr ? 'الكل' : 'All Cities' }}</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ ($filters['city_id'] ?? '') == $city->id ? 'selected' : '' }}>{{ $isAr ? ($city->name_ar ?: $city->name_en) : $city->name_en }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">{{ $isAr ? 'نوع العرض' : 'Listing Type' }}</label>
                        <select name="listing_type" class="s-select">
                            <option value="">{{ $isAr ? 'الكل' : 'All' }}</option>
                            <option value="rent" {{ ($filters['listing_type'] ?? '') === 'rent' ? 'selected' : '' }}>{{ $isAr ? 'للإيجار' : 'For Rent' }}</option>
                            <option value="sale" {{ ($filters['listing_type'] ?? '') === 'sale' ? 'selected' : '' }}>{{ $isAr ? 'للبيع' : 'For Sale' }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">{{ $isAr ? 'غرف النوم' : 'Min Bedrooms' }}</label>
                        <select name="bedrooms" class="s-select">
                            <option value="">{{ $isAr ? 'أي عدد' : 'Any' }}</option>
                            @for($b = 1; $b <= 6; $b++)
                                <option value="{{ $b }}" {{ ($filters['bedrooms'] ?? '') == $b ? 'selected' : '' }}>{{ $b }}+</option>
                            @endfor
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">{{ $isAr ? 'السعر من' : 'Min Price' }}</label>
                            <input type="number" name="price_min" value="{{ $filters['price_min'] ?? '' }}" class="s-input text-xs" placeholder="0" min="0">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">{{ $isAr ? 'السعر إلى' : 'Max Price' }}</label>
                            <input type="number" name="price_max" value="{{ $filters['price_max'] ?? '' }}" class="s-input text-xs" placeholder="{{ $isAr ? 'بلا حد' : 'No limit' }}" min="0">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">{{ $isAr ? 'الشركة' : 'Company' }}</label>
                        <select name="tenant_id" class="s-select">
                            <option value="">{{ $isAr ? 'الكل' : 'All Companies' }}</option>
                            @foreach($tenants as $t)
                                <option value="{{ $t->id }}" {{ ($filters['tenant_id'] ?? '') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">{{ $isAr ? 'ترتيب' : 'Sort By' }}</label>
                        <select name="sort" class="s-select">
                            <option value="latest" {{ ($filters['sort'] ?? '') === 'latest' ? 'selected' : '' }}>{{ $isAr ? 'الأحدث' : 'Newest' }}</option>
                            <option value="price_asc" {{ ($filters['sort'] ?? '') === 'price_asc' ? 'selected' : '' }}>{{ $isAr ? 'السعر: الأقل' : 'Price: Low→High' }}</option>
                            <option value="price_desc" {{ ($filters['sort'] ?? '') === 'price_desc' ? 'selected' : '' }}>{{ $isAr ? 'السعر: الأعلى' : 'Price: High→Low' }}</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z"/></svg>
                        {{ $isAr ? 'تطبيق' : 'Apply Filters' }}
                    </button>
                    @if(collect($filters)->filter()->isNotEmpty())
                        <a href="{{ route('public.search') }}" class="block text-center text-xs font-medium text-slate-400 hover:text-rose-500 transition">{{ $isAr ? 'مسح الفلاتر' : 'Clear all filters' }}</a>
                    @endif
                </div>
            </form>
        </aside>

        {{-- Results --}}
        <main class="flex-1 min-w-0">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">
                        @if(!empty($filters['q']))
                            {{ $isAr ? 'نتائج البحث عن' : 'Results for' }} "{{ $filters['q'] }}"
                        @else
                            {{ $isAr ? 'جميع الوحدات المتاحة' : 'All Available Units' }}
                        @endif
                    </h2>
                    <p class="text-sm text-slate-400 mt-0.5">{{ $units->total() }} {{ $isAr ? 'وحدة' : ($units->total() === 1 ? 'unit' : 'units') }}</p>
                </div>
            </div>

            @if($units->count())
                <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-5">
                    @foreach($units as $unit)
                        @php
                            $photo = ($unit->photos && count($unit->photos)) ? $unit->photos[0] : null;
                            $unitTitle = $unit->translated_title ?: ($unit->code ?? '—');
                            $cityName = $unit->city ? ($isAr ? ($unit->city->name_ar ?: $unit->city->name_en) : $unit->city->name_en) : null;
                            $subName = $unit->subcategory?->name;
                            $tenantUrl = app(\App\Services\Tenancy\TenantManager::class)->tenantUrl($unit->tenant, '/listings/' . $unit->code);
                        @endphp
                        <a href="{{ $tenantUrl }}" target="_blank" class="u-card group block">
                            <div class="relative aspect-[4/3] bg-slate-100 overflow-hidden">
                                @if($photo)
                                    <img src="{{ $photo }}" alt="{{ $unitTitle }}" class="w-full h-full object-cover" loading="lazy">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-slate-50 to-slate-100">
                                        <svg class="w-12 h-12 text-slate-200" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75"/></svg>
                                    </div>
                                @endif
                                @if($unit->listing_type)
                                    <span class="absolute top-3 ltr:left-3 rtl:right-3 px-2.5 py-1 rounded-lg text-[11px] font-bold uppercase tracking-wider {{ $unit->listing_type === 'sale' ? 'bg-amber-500 text-white' : 'bg-emerald-500 text-white' }}">
                                        {{ $unit->listing_type === 'sale' ? ($isAr ? 'للبيع' : 'Sale') : ($isAr ? 'للإيجار' : 'Rent') }}
                                    </span>
                                @endif
                            </div>
                            <div class="p-4">
                                <div class="text-sm font-bold text-slate-900 mb-1 line-clamp-1 group-hover:text-indigo-600 transition-colors">{{ $unitTitle }}</div>
                                @if($cityName || $subName)
                                    <div class="text-xs text-slate-400 mb-3 flex items-center gap-1">
                                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                                        {{ collect([$subName, $cityName])->filter()->implode(' · ') }}
                                    </div>
                                @endif
                                <div class="flex items-center justify-between mb-3">
                                    @if($unit->price)
                                        <div class="text-lg font-extrabold text-indigo-600">{{ number_format($unit->price) }} <span class="text-xs font-medium text-slate-400">{{ $unit->currency ?? 'JOD' }}</span></div>
                                    @else
                                        <div class="text-sm text-slate-400">{{ $isAr ? 'السعر عند الطلب' : 'Price on request' }}</div>
                                    @endif
                                </div>
                                <div class="flex items-center gap-3 text-xs text-slate-400 mb-3">
                                    @if($unit->bedrooms)
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75"/></svg>
                                            {{ $unit->bedrooms }} {{ $isAr ? 'غرف' : 'Beds' }}
                                        </span>
                                    @endif
                                    @if($unit->bathrooms)
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            {{ $unit->bathrooms }} {{ $isAr ? 'حمام' : 'Baths' }}
                                        </span>
                                    @endif
                                    @if($unit->area_m2)
                                        <span>{{ $unit->area_m2 }} m²</span>
                                    @endif
                                </div>
                                <div class="pt-3 border-t border-slate-50 flex items-center gap-2">
                                    <div class="w-5 h-5 rounded bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-[8px] font-bold text-white flex-shrink-0">{{ mb_substr($unit->tenant->name ?? '', 0, 1) }}</div>
                                    <span class="text-[11px] text-slate-400 truncate">{{ $unit->tenant->name ?? '' }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-10">
                    {{ $units->links() }}
                </div>
            @else
                {{-- Empty State --}}
                <div class="text-center py-20">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-slate-100 flex items-center justify-center">
                        <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">{{ $isAr ? 'لا توجد نتائج' : 'No units found' }}</h3>
                    <p class="text-sm text-slate-400 max-w-sm mx-auto mb-6">{{ $isAr ? 'جرّب تعديل الفلاتر أو البحث بكلمات مختلفة' : 'Try adjusting your filters or search with different keywords' }}</p>
                    <a href="{{ route('public.search') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">
                        {{ $isAr ? 'مسح الفلاتر' : 'Clear Filters' }}
                    </a>
                </div>
            @endif
        </main>
    </div>
</div>

{{-- Footer --}}
<footer class="bg-white border-t border-slate-100 py-8 mt-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/></svg>
            </div>
            <span class="text-sm font-bold text-slate-900">Aqari Smart</span>
        </div>
        <div class="text-sm text-slate-400">&copy; {{ date('Y') }} Aqari Smart. {{ $isAr ? 'جميع الحقوق محفوظة.' : 'All rights reserved.' }}</div>
        <div class="flex items-center gap-4">
            <a href="{{ route('home') }}" class="text-sm text-slate-500 hover:text-slate-900 transition">{{ $isAr ? 'الرئيسية' : 'Home' }}</a>
            <a href="#" class="text-sm text-slate-500 hover:text-slate-900 transition">{{ $isAr ? 'الخصوصية' : 'Privacy' }}</a>
            <a href="#" class="text-sm text-slate-500 hover:text-slate-900 transition">{{ $isAr ? 'الشروط' : 'Terms' }}</a>
        </div>
    </div>
</footer>

</body>
</html>
