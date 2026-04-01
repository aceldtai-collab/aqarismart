@php
    $loc = app()->getLocale();
    $isAr = $loc === 'ar';
    $landing = $landing ?? [];
    $assets = $landing['assets'] ?? [];
    $langParam = config('locales.cookie_name', 'lang');
    $urlEn = request()->fullUrlWithQuery([$langParam => 'en']);
    $urlAr = request()->fullUrlWithQuery([$langParam => 'ar']);
    $heroImage = $assets['hero_image'] ?? null;
    $tx = [
        'brand' => $isAr ? 'عقاري سمارت' : 'Aqari Smart',
        'home' => $isAr ? 'الرئيسية' : 'Home',
        'featured_nav' => $isAr ? 'المميزة' : 'Featured',
        'sale_nav' => $isAr ? 'للبيع' : 'For Sale',
        'rent_nav' => $isAr ? 'للإيجار' : 'For Rent',
        'contact_nav' => $isAr ? 'تواصل' : 'Contact',
        'book' => $isAr ? 'احجز عرضاً' : 'Book Demo',
        'hero_eyebrow' => $isAr ? 'منصة السوق العقاري' : 'Real Estate Marketplace',
        'hero_title' => $isAr ? 'اعثر على المنزل الذي تحلم به' : 'Find Your Dream Place',
        'hero_subtitle' => $isAr ? 'تصفح العقارات الموثوقة من مديري العقارات والوسطاء المحترفين في مكان واحد.' : 'Browse trusted listings from professional property managers and agents in one place.',
        'keyword' => $isAr ? 'ابحث باسم العقار أو الموقع' : 'Search by property name or location',
        'city' => $isAr ? 'المدينة' : 'City',
        'type' => $isAr ? 'نوع العرض' : 'Listing Type',
        'search' => $isAr ? 'ابحث الآن' : 'Search Now',
        'properties' => $isAr ? 'العقارات' : 'Properties',
        'sale' => $isAr ? 'للبيع' : 'For Sale',
        'rent' => $isAr ? 'للإيجار' : 'For Rent',
        'managers' => $isAr ? 'الشركات' : 'Managers',
        'featured_eyebrow' => $isAr ? 'اختياراتنا' : 'Featured Collection',
        'featured_title' => $isAr ? 'عقارات مميزة' : 'Featured Properties',
        'why_title' => $isAr ? 'لماذا تختارنا' : 'Why Choose Us',
        'why_1_title' => $isAr ? 'عروض موثوقة' : 'Trusted Listings',
        'why_1_text' => $isAr ? 'عقارات محدثة من شركات إدارة عقارية فعّالة.' : 'Fresh listings from active property management teams.',
        'why_2_title' => $isAr ? 'بحث أسهل' : 'Faster Search',
        'why_2_text' => $isAr ? 'فلترة سريعة حسب المدينة والسعر ونوع العرض.' : 'Quick filtering by city, budget, and listing type.',
        'why_3_title' => $isAr ? 'تفاصيل أوضح' : 'Clearer Details',
        'why_3_text' => $isAr ? 'بطاقات عرض غنية بالصور والأسعار والخصائص.' : 'Richer property cards with photos, price, and features.',
        'why_4_title' => $isAr ? 'تواصل مباشر' : 'Direct Contact',
        'why_4_text' => $isAr ? 'الوصول السريع إلى مدير العقار أو الوسيط المناسب.' : 'Reach the right property manager or agent faster.',
        'sale_eyebrow' => $isAr ? 'أفضل العروض' : 'Best Opportunities',
        'sale_title' => $isAr ? 'عقارات للبيع' : 'Properties For Sale',
        'rent_eyebrow' => $isAr ? 'جاهزة للسكن' : 'Move-In Ready',
        'rent_title' => $isAr ? 'عقارات للإيجار' : 'Properties For Rent',
        'view_all' => $isAr ? 'عرض الكل' : 'View All',
        'contact_eyebrow' => $isAr ? 'جاهز للبدء؟' : 'Ready To Get Started?',
        'contact_title' => $isAr ? 'دعنا نساعدك في العثور على العقار المناسب' : 'Let us help you find the right property',
        'contact_text' => $isAr ? 'أرسل تفاصيل احتياجك وسيتواصل معك فريقنا أو أحد مديري العقارات المناسبين.' : 'Send your requirements and our team or a matching property manager will get back to you.',
        'name' => $isAr ? 'الاسم' : 'Name',
        'email' => $isAr ? 'البريد الإلكتروني' : 'Email',
        'phone' => $isAr ? 'رقم الهاتف' : 'Phone',
        'submit' => $isAr ? 'إرسال الطلب' : 'Submit Request',
        'sidebar_eyebrow' => $isAr ? 'لماذا الآن' : 'Currently Trending',
        'sidebar_title' => $isAr ? 'أحدث ما يميز السوق' : 'What Makes This Marketplace Better',
        'sidebar_1' => $isAr ? 'نتائج مجمعة من عدة شركات عقارية.' : 'Aggregated inventory from multiple property managers.',
        'sidebar_2' => $isAr ? 'خيارات بيع وإيجار في صفحة واحدة.' : 'Sale and rent options in one destination.',
        'sidebar_3' => $isAr ? 'عرض واضح للصور والسعر والمواصفات.' : 'Clean presentation for photos, price, and specs.',
        'sidebar_4' => $isAr ? 'قابل للتوسع لاحقاً بالخرائط والمفضلة.' : 'Ready to evolve later with maps and favorites.',
        'categories_title' => $isAr ? 'تصفح حسب الفئة' : 'Browse by Category',
        'categories_subtitle' => $isAr ? 'اختر نوع العقار المناسب لك' : 'Find the right property type for you',
        'agencies_eyebrow' => $isAr ? 'شركاؤنا' : 'Our Partners',
        'agencies_title' => $isAr ? 'أبرز الوكالات' : 'Top Agencies',
        'agencies_subtitle' => $isAr ? 'وكالات ومديرو عقارات موثوقون' : 'Trusted agencies and property managers',
        'cities_eyebrow' => $isAr ? 'مناطق شائعة' : 'Popular Places',
        'cities_title' => $isAr ? 'المدن الأكثر طلباً' : 'Most Popular Places',
        'catalog_eyebrow' => $isAr ? 'كل العروض' : 'Full Catalog',
        'catalog_title' => $isAr ? 'استكشف العقارات' : 'Browse Properties',
        'all_categories' => $isAr ? 'كل الفئات' : 'All Categories',
        'bedrooms' => $isAr ? 'غرف النوم' : 'Bedrooms',
        'footer_text' => $isAr ? 'سوق عقاري حديث يربط الباحثين عن العقارات بمديري العقارات المحترفين.' : 'A modern property marketplace connecting seekers with professional property managers.',
        'footer_links' => $isAr ? 'روابط' : 'Quick Links',
        'footer_contact' => $isAr ? 'تواصل' : 'Contact',
        'footer_cta' => $isAr ? 'ابدأ الآن' : 'Start Here',
        'footer_address' => $isAr ? 'عمّان، الأردن' : 'Amman, Jordan',
        'loading' => $isAr ? 'جاري التحميل...' : 'Loading...',
        'no_results' => $isAr ? 'لم يتم العثور على نتائج' : 'No results found',
        'no_results_hint' => $isAr ? 'حاول تغيير معايير البحث' : 'Try adjusting your search criteria',
        'listings' => $isAr ? 'إعلان' : 'listings',
        'all' => $isAr ? 'الكل' : 'All',
        'beds' => $isAr ? 'غرف' : 'beds',
        'baths' => $isAr ? 'حمامات' : 'baths',
        'sqft' => $isAr ? 'قدم²' : 'sq ft',
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ $loc }}" dir="{{ $isAr ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $tx['hero_title'] }} | {{ $tx['brand'] }}</title>
    <meta name="description" content="{{ $tx['hero_subtitle'] }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />
    @if($isAr)<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">@endif
    <x-vite-assets />
    <style>
        body{font-family:'Inter',system-ui,sans-serif;background:#f3f4f6;color:#111827}
        @if($isAr) body{font-family:'Noto Sans Arabic','Inter',system-ui,sans-serif} @endif
        .market-hero{background-image:linear-gradient(rgba(17,24,39,.62),rgba(17,24,39,.62)),url('{{ $heroImage ?: 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=1600&q=80' }}');background-size:cover;background-position:center}
        .market-shell{max-width:1280px}
        .market-card{background:#fff;border:1px solid #e5e7eb;box-shadow:0 10px 30px rgba(15,23,42,.06)}
        .market-kicker{letter-spacing:.16em}
        .scroll-hidden{-ms-overflow-style:none;scrollbar-width:none}.scroll-hidden::-webkit-scrollbar{display:none}
    </style>
</head>
<body>
    <!-- Header -->
    <header class="absolute inset-x-0 top-0 z-30">
        <div class="market-shell mx-auto flex items-center justify-between px-4 py-5 text-white sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-3 font-semibold tracking-wide">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-brand-600 text-sm font-bold text-white">AS</span>
                <span>{{ $tx['brand'] }}</span>
            </a>
            <nav class="hidden items-center gap-6 text-sm text-white/85 md:flex">
                <a href="{{ route('home') }}" class="hover:text-white">{{ $tx['home'] }}</a>
                <a href="#featured-properties" class="hover:text-white">{{ $tx['featured_nav'] }}</a>
                <a href="#properties-for-sale" class="hover:text-white">{{ $tx['sale_nav'] }}</a>
                <a href="#properties-for-rent" class="hover:text-white">{{ $tx['rent_nav'] }}</a>
                <a href="#contact-us" class="hover:text-white">{{ $tx['contact_nav'] }}</a>
            </nav>
            <div class="flex items-center gap-3">
                <div class="flex items-center rounded-full border border-white/25 bg-white/10 p-1 text-xs font-medium backdrop-blur">
                    <a href="{{ $urlEn }}" class="rounded-full px-3 py-1 {{ !$isAr ? 'bg-white text-slate-900' : 'text-white/80' }}">EN</a>
                    <a href="{{ $urlAr }}" class="rounded-full px-3 py-1 {{ $isAr ? 'bg-white text-slate-900' : 'text-white/80' }}">ع</a>
                </div>
                <a href="{{ route('book-call') }}" class="hidden rounded-full bg-brand-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-brand-500 sm:inline-flex">{{ $tx['book'] }}</a>
            </div>
        </div>
    </header>

    <!-- Hero -->
    <section class="market-hero relative min-h-[760px] pb-24 pt-36 text-white">
        <div class="market-shell mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-3xl text-center">
                <p class="market-kicker mb-4 text-xs font-semibold uppercase text-white/70">{{ $tx['hero_eyebrow'] }}</p>
                <h1 class="mb-5 text-4xl font-bold leading-tight sm:text-5xl lg:text-6xl">{{ $tx['hero_title'] }}</h1>
                <p class="mx-auto mb-10 max-w-2xl text-base text-white/80 sm:text-lg">{{ $tx['hero_subtitle'] }}</p>
            </div>
            <form id="hero-search-form" class="mx-auto mt-10 max-w-5xl rounded-2xl bg-white p-4 shadow-2xl shadow-black/30">
                <div class="grid grid-cols-1 gap-3 md:grid-cols-5">
                    <div class="md:col-span-2">
                        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="{{ $tx['keyword'] }}" class="h-14 w-full rounded-xl border border-slate-200 px-4 text-sm text-slate-900 outline-none transition focus:border-brand-400">
                    </div>
                    <div>
                        <select name="city_id" class="h-14 w-full rounded-xl border border-slate-200 px-4 text-sm text-slate-900 outline-none focus:border-brand-400">
                            <option value="">{{ $tx['city'] }}</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" @selected(($filters['city_id'] ?? null) == $city->id)>{{ $city['name_'.$loc] ?? $city->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="listing_type" class="h-14 w-full rounded-xl border border-slate-200 px-4 text-sm text-slate-900 outline-none focus:border-brand-400">
                            <option value="">{{ $tx['type'] }}</option>
                            <option value="sale" @selected(($filters['listing_type'] ?? null) === 'sale')>{{ __('For Sale') }}</option>
                            <option value="rent" @selected(($filters['listing_type'] ?? null) === 'rent')>{{ __('For Rent') }}</option>
                        </select>
                    </div>
                    <button type="submit" class="h-14 rounded-xl bg-brand-600 px-6 text-sm font-semibold text-white transition hover:bg-brand-500">{{ $tx['search'] }}</button>
                </div>
            </form>
            <!-- Stats — populated by JS -->
            <div id="web-stats" class="mx-auto mt-10 grid max-w-5xl grid-cols-2 gap-4 text-center sm:grid-cols-4">
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur"><div class="text-2xl font-bold" data-stat="properties">—</div><div class="mt-1 text-xs uppercase tracking-[0.2em] text-white/65">{{ $tx['properties'] }}</div></div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur"><div class="text-2xl font-bold" data-stat="sale">—</div><div class="mt-1 text-xs uppercase tracking-[0.2em] text-white/65">{{ $tx['sale'] }}</div></div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur"><div class="text-2xl font-bold" data-stat="rent">—</div><div class="mt-1 text-xs uppercase tracking-[0.2em] text-white/65">{{ $tx['rent'] }}</div></div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur"><div class="text-2xl font-bold" data-stat="managers">—</div><div class="mt-1 text-xs uppercase tracking-[0.2em] text-white/65">{{ $tx['managers'] }}</div></div>
            </div>
        </div>
    </section>

    <main class="-mt-12 pb-20">
        <div class="market-shell mx-auto space-y-16 px-4 sm:px-6 lg:px-8">

            <!-- Categories (from mobile design) — populated by JS -->
            <section class="market-card rounded-[28px] px-6 py-10 sm:px-8">
                <div class="mb-8"><h2 class="text-2xl font-bold text-slate-900">{{ $tx['categories_title'] }}</h2><p class="mt-2 text-sm text-slate-500">{{ $tx['categories_subtitle'] }}</p></div>
                <div id="web-categories" class="flex snap-x snap-mandatory gap-4 overflow-x-auto scroll-hidden pb-2">
                    <div class="text-sm text-slate-400 py-8">{{ $tx['loading'] }}</div>
                </div>
            </section>

            <!-- Featured Properties — populated by JS -->
            <section id="featured-properties" class="market-card rounded-[28px] px-6 py-10 sm:px-8">
                <div class="mb-8 text-center">
                    <p class="market-kicker mb-3 text-xs font-semibold uppercase text-rose-600">{{ $tx['featured_eyebrow'] }}</p>
                    <h2 class="text-3xl font-bold text-slate-900">{{ $tx['featured_title'] }}</h2>
                </div>
                <div id="web-featured" class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                    <div class="col-span-full text-center text-sm text-slate-400 py-8">{{ $tx['loading'] }}</div>
                </div>
            </section>

            <!-- Why Choose Us (static) -->
            <section>
                <div class="mb-8 text-center">
                    <p class="market-kicker mb-3 text-xs font-semibold uppercase text-brand-600">{{ $tx['featured_eyebrow'] }}</p>
                    <h2 class="text-3xl font-bold text-slate-900">{{ $tx['why_title'] }}</h2>
                </div>
                <div class="grid gap-6 lg:grid-cols-4">
                    <div class="market-card rounded-[28px] p-7 text-center"><div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-brand-50 text-brand-600"><svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-2.21 0-4 1.79-4 4m8 0a4 4 0 10-8 0m8 0c0 1.977-1.523 3.6-3.46 3.96M12 4v4m0 8v4m8-8h-4M8 12H4"/></svg></div><h3 class="mb-2 text-lg font-semibold text-slate-900">{{ $tx['why_1_title'] }}</h3><p class="text-sm leading-7 text-slate-600">{{ $tx['why_1_text'] }}</p></div>
                    <div class="market-card rounded-[28px] p-7 text-center"><div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-brand-50 text-brand-600"><svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div><h3 class="mb-2 text-lg font-semibold text-slate-900">{{ $tx['why_2_title'] }}</h3><p class="text-sm leading-7 text-slate-600">{{ $tx['why_2_text'] }}</p></div>
                    <div class="market-card rounded-[28px] p-7 text-center"><div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-brand-50 text-brand-600"><svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5V4H2v16h5m10 0v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6m10 0H7"/></svg></div><h3 class="mb-2 text-lg font-semibold text-slate-900">{{ $tx['why_3_title'] }}</h3><p class="text-sm leading-7 text-slate-600">{{ $tx['why_3_text'] }}</p></div>
                    <div class="market-card rounded-[28px] p-7 text-center"><div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-brand-50 text-brand-600"><svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z"/></svg></div><h3 class="mb-2 text-lg font-semibold text-slate-900">{{ $tx['why_4_title'] }}</h3><p class="text-sm leading-7 text-slate-600">{{ $tx['why_4_text'] }}</p></div>
                </div>
            </section>

            <!-- Properties For Sale — populated by JS -->
            <section id="properties-for-sale" class="space-y-6">
                <div class="flex items-end justify-between gap-4"><div><p class="market-kicker mb-2 text-xs font-semibold uppercase text-brand-600">{{ $tx['sale_eyebrow'] }}</p><h2 class="text-3xl font-bold text-slate-900">{{ $tx['sale_title'] }}</h2></div><a href="?listing_type=sale#catalog" class="text-sm font-semibold text-brand-600 hover:text-brand-500">{{ $tx['view_all'] }}</a></div>
                <div id="web-sale" class="flex snap-x snap-mandatory gap-5 overflow-x-auto scroll-hidden pb-4">
                    <div class="text-sm text-slate-400 py-8">{{ $tx['loading'] }}</div>
                </div>
            </section>

            <!-- Properties For Rent — populated by JS -->
            <section id="properties-for-rent" class="space-y-6">
                <div class="flex items-end justify-between gap-4"><div><p class="market-kicker mb-2 text-xs font-semibold uppercase text-brand-600">{{ $tx['rent_eyebrow'] }}</p><h2 class="text-3xl font-bold text-slate-900">{{ $tx['rent_title'] }}</h2></div><a href="?listing_type=rent#catalog" class="text-sm font-semibold text-brand-600 hover:text-brand-500">{{ $tx['view_all'] }}</a></div>
                <div id="web-rent" class="flex snap-x snap-mandatory gap-5 overflow-x-auto scroll-hidden pb-4">
                    <div class="text-sm text-slate-400 py-8">{{ $tx['loading'] }}</div>
                </div>
            </section>

            <!-- Popular Cities — populated by JS -->
            <section class="space-y-6">
                <div class="text-center"><p class="market-kicker mb-2 text-xs font-semibold uppercase text-brand-600">{{ $tx['cities_eyebrow'] }}</p><h2 class="text-3xl font-bold text-slate-900">{{ $tx['cities_title'] }}</h2></div>
                <div id="web-cities" class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">
                    <div class="col-span-full text-center text-sm text-slate-400 py-8">{{ $tx['loading'] }}</div>
                </div>
            </section>

            <!-- Top Agencies (from mobile design) — populated by JS -->
            <section class="space-y-6">
                <div class="text-center"><p class="market-kicker mb-2 text-xs font-semibold uppercase text-brand-600">{{ $tx['agencies_eyebrow'] }}</p><h2 class="text-3xl font-bold text-slate-900">{{ $tx['agencies_title'] }}</h2><p class="mt-2 text-sm text-slate-500">{{ $tx['agencies_subtitle'] }}</p></div>
                <div id="web-agencies" class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
                    <div class="col-span-full text-center text-sm text-slate-400 py-8">{{ $tx['loading'] }}</div>
                </div>
            </section>

            <!-- Contact (static) -->
            <section id="contact-us" class="grid gap-6 lg:grid-cols-[1.15fr_.85fr]">
                <div class="rounded-[28px] bg-slate-800 p-8 text-white"><p class="market-kicker mb-3 text-xs font-semibold uppercase text-brand-300">{{ $tx['contact_eyebrow'] }}</p><h2 class="mb-4 text-3xl font-bold">{{ $tx['contact_title'] }}</h2><p class="mb-8 max-w-xl text-sm leading-7 text-slate-300">{{ $tx['contact_text'] }}</p><form action="{{ route('book-call') }}" method="GET" class="grid gap-4 sm:grid-cols-2"><input type="text" name="name" placeholder="{{ $tx['name'] }}" class="h-12 rounded-xl border border-white/10 bg-white/10 px-4 text-sm text-white placeholder:text-slate-300 focus:border-brand-400 focus:outline-none"><input type="email" name="email" placeholder="{{ $tx['email'] }}" class="h-12 rounded-xl border border-white/10 bg-white/10 px-4 text-sm text-white placeholder:text-slate-300 focus:border-brand-400 focus:outline-none"><input type="text" name="phone" placeholder="{{ $tx['phone'] }}" class="h-12 rounded-xl border border-white/10 bg-white/10 px-4 text-sm text-white placeholder:text-slate-300 focus:border-brand-400 focus:outline-none sm:col-span-2"><button type="submit" class="inline-flex h-12 items-center justify-center rounded-xl bg-brand-600 px-6 text-sm font-semibold text-white transition hover:bg-brand-500 sm:col-span-2">{{ $tx['submit'] }}</button></form></div>
                <div class="rounded-[28px] bg-white p-8 shadow-sm ring-1 ring-slate-200"><p class="market-kicker mb-3 text-xs font-semibold uppercase text-brand-600">{{ $tx['sidebar_eyebrow'] }}</p><h3 class="mb-4 text-2xl font-bold text-slate-900">{{ $tx['sidebar_title'] }}</h3><ul class="space-y-4 text-sm leading-7 text-slate-600"><li class="flex gap-3"><span class="mt-2 h-2.5 w-2.5 rounded-full bg-brand-500"></span><span>{{ $tx['sidebar_1'] }}</span></li><li class="flex gap-3"><span class="mt-2 h-2.5 w-2.5 rounded-full bg-brand-500"></span><span>{{ $tx['sidebar_2'] }}</span></li><li class="flex gap-3"><span class="mt-2 h-2.5 w-2.5 rounded-full bg-brand-500"></span><span>{{ $tx['sidebar_3'] }}</span></li><li class="flex gap-3"><span class="mt-2 h-2.5 w-2.5 rounded-full bg-brand-500"></span><span>{{ $tx['sidebar_4'] }}</span></li></ul></div>
            </section>

            <!-- Catalog Browse — JS search with API pagination -->
            <section id="catalog" class="market-card rounded-[28px] px-6 py-10 sm:px-8">
                <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div><p class="market-kicker mb-2 text-xs font-semibold uppercase text-brand-600">{{ $tx['catalog_eyebrow'] }}</p><h2 class="text-3xl font-bold text-slate-900">{{ $tx['catalog_title'] }}</h2></div>
                    <div id="catalog-count" class="text-sm text-slate-500"></div>
                </div>
                <form id="catalog-filter-form" class="mb-8 grid gap-4 rounded-2xl bg-slate-50 p-5 md:grid-cols-2 xl:grid-cols-6">
                    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="{{ $tx['keyword'] }}" class="h-12 rounded-xl border border-slate-200 px-4 text-sm text-slate-900 outline-none focus:border-brand-400 xl:col-span-2">
                    <select name="subcategory_id" class="h-12 rounded-xl border border-slate-200 px-4 text-sm text-slate-900 outline-none focus:border-brand-400"><option value="">{{ $tx['all_categories'] }}</option>@foreach($categories as $category)<optgroup label="{{ $category['name_'.$loc] ?? $category['name_en'] }}">@foreach($category->subcategories as $subcategory)<option value="{{ $subcategory->id }}" @selected(($filters['subcategory_id'] ?? null) == $subcategory->id)>{{ $subcategory['name_'.$loc] ?? $subcategory['name_en'] }}</option>@endforeach</optgroup>@endforeach</select>
                    <select name="bedrooms" class="h-12 rounded-xl border border-slate-200 px-4 text-sm text-slate-900 outline-none focus:border-brand-400"><option value="">{{ $tx['bedrooms'] }}</option>@for($i = 1; $i <= 6; $i++)<option value="{{ $i }}" @selected(($filters['bedrooms'] ?? null) == $i)>{{ $i }}+</option>@endfor</select>
                    <select name="sort" class="h-12 rounded-xl border border-slate-200 px-4 text-sm text-slate-900 outline-none focus:border-brand-400"><option value="latest" @selected(($filters['sort'] ?? 'latest') === 'latest')>{{ __('Latest') }}</option><option value="price_asc" @selected(($filters['sort'] ?? null) === 'price_asc')>{{ __('Price: Low to High') }}</option><option value="price_desc" @selected(($filters['sort'] ?? null) === 'price_desc')>{{ __('Price: High to Low') }}</option><option value="oldest" @selected(($filters['sort'] ?? null) === 'oldest')>{{ __('Oldest') }}</option></select>
                    <div class="flex gap-3 xl:col-span-1"><button type="submit" class="flex-1 rounded-xl bg-brand-600 px-4 text-sm font-semibold text-white transition hover:bg-brand-500">{{ __('Apply Filters') }}</button><button type="button" id="catalog-clear-btn" class="inline-flex h-12 items-center justify-center rounded-xl border border-slate-200 px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">{{ __('Clear') }}</button></div>
                </form>
                <div id="catalog-results" class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                    <div class="col-span-full text-center text-sm text-slate-400 py-8">{{ $tx['loading'] }}</div>
                </div>
                <div id="catalog-pagination" class="mt-8 flex items-center justify-center gap-2"></div>
            </section>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-950 py-12 text-slate-300">
        <div class="market-shell mx-auto grid gap-10 px-4 sm:px-6 lg:grid-cols-4 lg:px-8">
            <div><div class="mb-4 text-lg font-semibold text-white">{{ $tx['brand'] }}</div><p class="text-sm leading-7 text-slate-400">{{ $tx['footer_text'] }}</p></div>
            <div><div class="mb-4 text-sm font-semibold uppercase tracking-[0.18em] text-white">{{ $tx['footer_links'] }}</div><div class="space-y-3 text-sm"><a href="{{ route('home') }}" class="block hover:text-white">{{ $tx['home'] }}</a><a href="#properties-for-sale" class="block hover:text-white">{{ $tx['sale_nav'] }}</a><a href="#properties-for-rent" class="block hover:text-white">{{ $tx['rent_nav'] }}</a></div></div>
            <div><div class="mb-4 text-sm font-semibold uppercase tracking-[0.18em] text-white">{{ $tx['footer_contact'] }}</div><div class="space-y-3 text-sm text-slate-400"><div>support@aqarismart.com</div><div>+962 7 9000 0000</div><div>{{ $tx['footer_address'] }}</div></div></div>
            <div><div class="mb-4 text-sm font-semibold uppercase tracking-[0.18em] text-white">{{ $tx['footer_cta'] }}</div><a href="{{ route('book-call') }}" class="inline-flex rounded-full bg-brand-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-brand-500">{{ $tx['book'] }}</a></div>
        </div>
    </footer>

    <script>
    const lang = '{{ $loc }}';
    const baseDomain = '{{ config("tenancy.base_domain") }}';
    const scheme = '{{ request()->getScheme() }}';
    const fmt = new Intl.NumberFormat();
    let catalogPage = 1;
    const TX = {
        sale: {!! json_encode($tx['sale']) !!},
        rent: {!! json_encode($tx['rent']) !!},
        beds: {!! json_encode($tx['beds']) !!},
        baths: {!! json_encode($tx['baths']) !!},
        sqft: {!! json_encode($tx['sqft']) !!},
        properties: {!! json_encode($tx['properties']) !!},
        listings: {!! json_encode($tx['listings']) !!},
        no_results: {!! json_encode($tx['no_results']) !!},
        no_results_hint: {!! json_encode($tx['no_results_hint']) !!},
    };

    function t(obj, fallback) {
        if (typeof obj === 'object' && obj !== null) return obj[lang] ?? obj['en'] ?? fallback;
        return obj ?? fallback;
    }

    function tenantUrl(slug) {
        return `${scheme}://${slug}.${baseDomain}`;
    }

    function unitCardHtml(u) {
        const title = u.translated_title ?? u.title ?? u.code;
        const photo = (u.photos && u.photos[0]) ? u.photos[0] : 'https://picsum.photos/seed/aqarismart/900/640';
        const cityName = lang === 'ar' ? (u.city?.name_ar ?? u.city?.name_en ?? '') : (u.city?.name_en ?? '');
        const propName = u.property?.name ?? '';
        const loc = propName && cityName ? `${propName} · ${cityName}` : (propName || cityName);
        const isSale = u.listing_type === 'sale';
        const badge = isSale ? TX.sale : TX.rent;
        const badgeColor = isSale ? 'bg-emerald-500' : 'bg-brand-600';
        const slug = u.tenant?.slug;
        const href = slug ? `${tenantUrl(slug)}/units/${u.code ?? u.id}` : '#';
        return `<article class="group bg-white rounded-xl shadow-sm hover:shadow-lg transition-shadow overflow-hidden">
            <a href="${href}" class="block relative">
                <div class="relative h-56 bg-slate-100 overflow-hidden">
                    <img src="${photo}" alt="${title}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy" onerror="this.style.display='none'">
                    <div class="absolute top-4 left-4"><span class="inline-flex items-center rounded-full ${badgeColor} text-white px-3 py-1.5 text-xs font-bold shadow-lg">${badge}</span></div>
                    <div class="absolute bottom-4 left-4"><span class="inline-flex items-center rounded-xl bg-white/95 backdrop-blur-sm px-3 py-1.5 text-sm font-bold text-slate-900 shadow-sm">${u.currency ?? 'JOD'} ${fmt.format(u.price ?? 0)}</span></div>
                </div>
            </a>
            <div class="p-5">
                <a href="${href}"><h3 class="text-lg font-bold text-gray-900 line-clamp-1 group-hover:text-brand-600 transition-colors">${title}</h3></a>
                ${loc ? `<p class="mt-1 flex items-center text-sm text-slate-500 line-clamp-1"><svg class="mr-1 h-3.5 w-3.5 shrink-0 rtl:ml-1 rtl:mr-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>${loc}</p>` : ''}
                <div class="mt-4 flex items-center gap-4 border-t border-gray-100 pt-4 text-xs font-semibold text-slate-600">
                    <span>${u.bedrooms ?? u.beds ?? 0} ${TX.beds}</span>
                    <span>${u.bathrooms ?? u.baths ?? 0} ${TX.baths}</span>
                    ${u.sqft ? `<span>${fmt.format(u.sqft)} ${TX.sqft}</span>` : ''}
                </div>
            </div>
        </article>`;
    }

    function scrollCardHtml(u) {
        const title = u.translated_title ?? u.title ?? u.code;
        const photo = (u.photos && u.photos[0]) ? u.photos[0] : 'https://picsum.photos/seed/aqarismart/900/640';
        const isSale = u.listing_type === 'sale';
        const badge = isSale ? TX.sale : TX.rent;
        const slug = u.tenant?.slug;
        const href = slug ? `${tenantUrl(slug)}/units/${u.code ?? u.id}` : '#';
        return `<a href="${href}" class="group block min-w-[280px] max-w-[320px] flex-1 snap-start overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 transition-all hover:-translate-y-1 hover:shadow-md hover:ring-brand-400">
            <div class="relative h-48 bg-slate-100 overflow-hidden"><img src="${photo}" alt="${title}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy" onerror="this.style.display='none'">
            <div class="absolute left-3 top-3"><span class="inline-flex items-center rounded-lg ${isSale ? 'bg-emerald-600/90' : 'bg-brand-600/90'} backdrop-blur-sm px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-white shadow-sm">${badge}</span></div>
            <div class="absolute bottom-3 left-3"><span class="inline-flex items-center rounded-xl bg-white/95 backdrop-blur-sm px-3 py-1.5 text-sm font-bold text-slate-900 shadow-sm">${u.currency ?? 'JOD'} ${fmt.format(u.price ?? 0)}</span></div></div>
            <div class="p-4"><h3 class="text-base font-bold text-slate-900 line-clamp-1">${title}</h3>
            <div class="mt-3 flex items-center gap-4 text-xs font-semibold text-slate-600"><span>${u.bedrooms ?? u.beds ?? 0} ${TX.beds}</span><span>${u.bathrooms ?? u.baths ?? 0} ${TX.baths}</span></div></div></a>`;
    }

    function renderStats(stats) {
        document.querySelectorAll('[data-stat]').forEach(el => {
            const key = el.dataset.stat;
            if (stats[key] !== undefined) el.textContent = fmt.format(stats[key]);
        });
    }

    function renderCategories(cats) {
        const c = document.getElementById('web-categories');
        if (!c || !cats?.length) return;
        c.innerHTML = cats.map(cat => {
            const name = t(cat.name, 'Category');
            return `<button type="button" class="category-btn shrink-0 snap-start group relative overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 transition-all hover:-translate-y-1 hover:shadow-md hover:ring-brand-400 active:scale-95 w-[160px]" data-id="${cat.id}">
                <div class="h-24 w-full overflow-hidden"><img src="${cat.image}" alt="${name}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110" loading="lazy"><div class="absolute inset-x-0 top-0 h-24 bg-gradient-to-t from-black/50 to-transparent"></div></div>
                <div class="bg-white p-3 text-center"><h3 class="text-sm font-bold text-slate-800 truncate">${name}</h3><p class="mt-0.5 text-[11px] font-medium text-slate-500">${fmt.format(cat.count ?? 0)} ${TX.listings}</p></div></button>`;
        }).join('');
        c.querySelectorAll('.category-btn').forEach(btn => {
            btn.addEventListener('click', () => { catalogSetFilter('category_id', btn.dataset.id); });
        });
    }

    function renderCities(cities) {
        const c = document.getElementById('web-cities');
        if (!c || !cities?.length) { if(c) c.innerHTML = ''; return; }
        c.innerHTML = cities.map(city => {
            const name = lang === 'ar' ? (city.name_ar ?? city.name_en) : (city.name_en ?? '');
            return `<button type="button" class="city-btn group rounded-[24px] bg-white p-6 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-1 hover:shadow-lg text-left" data-id="${city.id}">
                <div class="relative mb-6 h-32 rounded-[20px] overflow-hidden"><img src="${city.image}" alt="${name}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110" loading="lazy"><div class="absolute inset-0 bg-gradient-to-t from-slate-900/70 to-transparent"></div>
                <div class="absolute bottom-3 left-4"><h3 class="text-lg font-bold text-white">${name}</h3><p class="text-xs font-semibold text-emerald-300">${fmt.format(city.units_count ?? 0)} ${TX.properties}</p></div></div></button>`;
        }).join('');
        c.querySelectorAll('.city-btn').forEach(btn => {
            btn.addEventListener('click', () => { catalogSetFilter('city_id', btn.dataset.id); });
        });
    }

    function renderAgencies(tenants) {
        const c = document.getElementById('web-agencies');
        if (!c || !tenants?.length) { if(c) c.innerHTML = ''; return; }
        c.innerHTML = tenants.map(t => {
            const logo = t.branding?.logo_url ?? `https://ui-avatars.com/api/?name=${encodeURIComponent(t.name)}&color=059669&background=ecfdf5`;
            const desc = t.summary?.description ?? (lang === 'ar' ? 'وكالة عقارية محترفة' : 'Professional real estate agency');
            return `<a href="${tenantUrl(t.slug)}" class="block rounded-[24px] bg-white p-6 shadow-sm ring-1 ring-slate-200 transition-all hover:-translate-y-1 hover:shadow-md hover:ring-brand-400">
                <div class="flex items-start gap-4"><div class="h-16 w-16 shrink-0 overflow-hidden rounded-xl bg-slate-50 ring-1 ring-slate-100"><img src="${logo}" alt="${t.name}" class="h-full w-full object-cover" loading="lazy"></div>
                <div class="flex-1 space-y-1"><h3 class="text-lg font-bold text-slate-900 line-clamp-1">${t.name}</h3><p class="text-sm text-slate-500 line-clamp-2">${desc}</p></div></div>
                <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-5">
                    <div class="text-center"><div class="text-lg font-bold text-slate-800">${t.stats?.units_count ?? 0}</div><div class="mt-0.5 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">${TX.properties}</div></div>
                    <div class="text-center"><div class="text-lg font-bold text-emerald-600">${t.stats?.active_units_count ?? t.stats?.units_count ?? 0}</div><div class="mt-0.5 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">${lang === 'ar' ? 'نشطة' : 'Active'}</div></div>
                    <span class="inline-flex items-center rounded-full bg-brand-50 px-4 py-2 text-xs font-semibold text-brand-700">${lang === 'ar' ? 'زيارة' : 'Visit'} →</span>
                </div></a>`;
        }).join('');
    }

    function renderScrollUnits(containerId, units) {
        const c = document.getElementById(containerId);
        if (!c) return;
        if (!units?.length) { c.innerHTML = `<div class="text-sm text-slate-400 py-8">${TX.no_results}</div>`; return; }
        c.innerHTML = units.map(scrollCardHtml).join('');
    }

    function renderCatalog(units, meta) {
        const c = document.getElementById('catalog-results');
        const countEl = document.getElementById('catalog-count');
        const pagEl = document.getElementById('catalog-pagination');
        if (!c) return;
        if (countEl && meta) countEl.textContent = `${lang === 'ar' ? 'عرض' : 'Showing'} ${units.length} ${lang === 'ar' ? 'من' : 'of'} ${fmt.format(meta.total)} ${lang === 'ar' ? 'عقار' : 'properties'}`;
        if (!units?.length) {
            c.innerHTML = `<div class="col-span-full rounded-2xl border border-dashed border-slate-300 px-6 py-14 text-center text-slate-500"><div class="mb-2 text-xl font-semibold text-slate-900">${TX.no_results}</div><p>${TX.no_results_hint}</p></div>`;
            if (pagEl) pagEl.innerHTML = '';
            return;
        }
        c.innerHTML = units.map(unitCardHtml).join('');
        if (pagEl && meta && meta.last_page > 1) {
            let html = '';
            for (let i = 1; i <= meta.last_page; i++) {
                html += `<button type="button" class="page-btn inline-flex h-10 w-10 items-center justify-center rounded-xl text-sm font-semibold transition ${i === meta.current_page ? 'bg-brand-600 text-white' : 'bg-white text-slate-700 ring-1 ring-slate-200 hover:bg-slate-50'}" data-page="${i}">${i}</button>`;
            }
            pagEl.innerHTML = html;
            pagEl.querySelectorAll('.page-btn').forEach(btn => {
                btn.addEventListener('click', () => { catalogPage = parseInt(btn.dataset.page); loadCatalog(); });
            });
        } else if (pagEl) { pagEl.innerHTML = ''; }
    }

    async function loadShowcase() {
        try {
            const res = await fetch('/api/mobile/marketplace?per_page=12', { headers: { Accept: 'application/json' } });
            const json = await res.json();
            renderStats(json.stats ?? {});
            renderCategories(json.categories ?? []);
            renderScrollUnits('web-featured', json.featured_units ?? []);
            renderScrollUnits('web-sale', (json.featured_units ?? []).filter(u => u.listing_type === 'sale'));
            renderScrollUnits('web-rent', (json.recommended_units ?? []).filter(u => u.listing_type === 'rent'));
            renderCities(json.cities ?? []);
            renderAgencies(json.tenants ?? []);
        } catch (e) { console.error('Marketplace API error:', e); }
    }

    async function loadCatalog() {
        const form = document.getElementById('catalog-filter-form');
        const params = new URLSearchParams(new FormData(form));
        params.set('page', catalogPage);
        params.set('per_page', '12');
        try {
            const res = await fetch(`/api/mobile/marketplace?${params}`, { headers: { Accept: 'application/json' } });
            const json = await res.json();
            renderCatalog(json.data ?? [], json.meta ?? {});
        } catch (e) { console.error('Catalog API error:', e); }
    }

    function catalogSetFilter(name, value) {
        const form = document.getElementById('catalog-filter-form');
        let input = form.querySelector(`[name="${name}"]`);
        if (!input) { input = document.createElement('input'); input.type = 'hidden'; input.name = name; form.appendChild(input); }
        input.value = value;
        catalogPage = 1;
        loadCatalog();
        document.getElementById('catalog')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    document.getElementById('hero-search-form')?.addEventListener('submit', e => {
        e.preventDefault();
        const heroForm = e.target;
        const catalogForm = document.getElementById('catalog-filter-form');
        catalogForm.querySelector('[name="q"]').value = heroForm.querySelector('[name="q"]').value;
        ['city_id', 'listing_type'].forEach(name => {
            const heroVal = heroForm.querySelector(`[name="${name}"]`)?.value ?? '';
            let catalogInput = catalogForm.querySelector(`[name="${name}"]`);
            if (!catalogInput) { catalogInput = document.createElement('input'); catalogInput.type = 'hidden'; catalogInput.name = name; catalogForm.appendChild(catalogInput); }
            catalogInput.value = heroVal;
        });
        catalogPage = 1;
        loadCatalog();
        document.getElementById('catalog')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    document.getElementById('catalog-filter-form')?.addEventListener('submit', e => {
        e.preventDefault();
        catalogPage = 1;
        loadCatalog();
    });

    document.getElementById('catalog-clear-btn')?.addEventListener('click', () => {
        const form = document.getElementById('catalog-filter-form');
        form.querySelectorAll('input, select').forEach(el => { if (el.name) el.value = ''; });
        catalogPage = 1;
        loadCatalog();
    });

    loadShowcase();
    loadCatalog();
    </script>
</body>
</html>
