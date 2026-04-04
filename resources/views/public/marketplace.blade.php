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
        'login_cta' => $isAr ? 'تسجيل الدخول' : 'Sign in',
        'register_cta' => $isAr ? 'إنشاء حساب' : 'Create account',
        'sell_cta' => $isAr ? 'بيع معنا' : 'Sell with us',
        'profile_cta' => $isAr ? 'الملف الشخصي' : 'Profile',
        'menu_cta' => $isAr ? 'القائمة' : 'Menu',
        'close_cta' => $isAr ? 'إغلاق' : 'Close',
        'account_title' => $isAr ? 'حسابك' : 'Your Account',
        'browse_title' => $isAr ? 'تصفح السوق' : 'Browse Marketplace',
        'dashboard_cta' => $isAr ? 'لوحة التحكم' : 'Dashboard',
        'logout_cta' => $isAr ? 'تسجيل الخروج' : 'Log Out',
        'welcome_cta' => $isAr ? 'أهلاً' : 'Welcome',
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

    $marketplaceUrl = route('public.marketplace');
    $marketplaceNavLinks = [
        ['label' => $tx['home'], 'href' => $marketplaceUrl],
        ['label' => $tx['featured_nav'], 'href' => '#featured-properties'],
        ['label' => $tx['sale_nav'], 'href' => '#properties-for-sale'],
        ['label' => $tx['rent_nav'], 'href' => '#properties-for-rent'],
        ['label' => $tx['contact_nav'], 'href' => '#contact-us'],
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
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&display=swap" rel="stylesheet" />
    @if($isAr)<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">@endif
    <x-vite-assets />
    <style>
        [x-cloak]{display:none!important}
        :root{
            --market-ink:#1f2a24;
            --market-palm:#0f5a46;
            --market-river:#2f7a72;
            --market-brass:#b6842f;
            --market-clay:#9d5a3b;
            --market-sand:#f2ead9;
            --market-cream:#fbf7ef;
            --market-stone:#6b5c48;
            --market-line:rgba(130,94,38,.16);
        }
        body{
            font-family:'Manrope',system-ui,sans-serif;
            background:
                radial-gradient(circle at top left, rgba(182,132,47,.12), transparent 26%),
                radial-gradient(circle at top right, rgba(15,90,70,.11), transparent 28%),
                linear-gradient(180deg, #efe4cf 0, #f7f0e2 380px, #fbf7ef 100%);
            color:var(--market-ink)
        }
        @if($isAr) body{font-family:'Cairo','Manrope',system-ui,sans-serif} @endif
        .market-shell{max-width:1320px}
        .market-hero{
            position:relative;
            isolation:isolate;
            background-image:url('{{ $heroImage ?: 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=1600&q=80' }}');
            background-size:cover;
            background-position:center;
        }
        .market-hero::before{
            content:"";
            position:absolute;
            inset:0;
            background:
                linear-gradient(118deg, rgba(11,46,38,.92), rgba(14,69,56,.84) 42%, rgba(48,30,14,.56) 76%, rgba(20,20,16,.62) 100%),
                radial-gradient(circle at 20% 22%, rgba(182,132,47,.32), transparent 26%),
                radial-gradient(circle at 86% 18%, rgba(255,255,255,.08), transparent 24%);
            z-index:-2;
        }
        .market-hero::after{
            content:"";
            position:absolute;
            inset:0;
            background-image:
                linear-gradient(90deg, rgba(255,255,255,.055) 1px, transparent 1px),
                linear-gradient(rgba(255,255,255,.055) 1px, transparent 1px);
            background-size:80px 80px;
            mask-image:linear-gradient(to bottom, rgba(0,0,0,.58), transparent 88%);
            z-index:-1;
        }
        .market-card{
            background:rgba(255,250,241,.92);
            border:1px solid var(--market-line);
            box-shadow:0 24px 54px -36px rgba(57,42,16,.38);
            backdrop-filter:blur(14px);
        }
        .market-panel{
            background:linear-gradient(180deg, rgba(255,248,235,.98), rgba(247,237,214,.88));
            border:1px solid rgba(182,132,47,.26);
            box-shadow:0 24px 58px -32px rgba(28,22,10,.42);
        }
        .market-kicker{letter-spacing:.24em}
        .market-hero-chip{
            display:inline-flex;
            align-items:center;
            gap:.5rem;
            border-radius:999px;
            border:1px solid rgba(255,255,255,.16);
            background:rgba(255,255,255,.1);
            padding:.65rem 1rem;
            font-size:.72rem;
            font-weight:700;
            letter-spacing:.18em;
            text-transform:uppercase;
            color:rgba(255,255,255,.86);
            backdrop-filter:blur(10px);
        }
        .market-hero-chip::before{
            content:"";
            width:.45rem;
            height:.45rem;
            border-radius:999px;
            background:var(--market-brass);
            box-shadow:0 0 0 4px rgba(182,132,47,.16);
        }
        .market-note{
            border:1px solid rgba(255,255,255,.12);
            background:rgba(255,255,255,.08);
            border-radius:1.5rem;
            padding:1rem 1rem .95rem;
            backdrop-filter:blur(14px);
            box-shadow:0 18px 40px -28px rgba(0,0,0,.35);
        }
        .market-note-label{
            font-size:.68rem;
            letter-spacing:.18em;
            text-transform:uppercase;
            color:rgba(255,255,255,.62);
            font-weight:700;
        }
        .market-note-value{
            margin-top:.45rem;
            font-size:1rem;
            line-height:1.2;
            font-weight:800;
            color:#fff8ec;
        }
        .market-search-wrap{
            border:1px solid rgba(182,132,47,.22);
            background:rgba(255,248,235,.97);
            box-shadow:0 30px 60px -34px rgba(19,24,20,.5);
        }
        .market-search-input{
            border-color:rgba(130,94,38,.16);
            background:rgba(255,255,255,.78);
        }
        .market-search-input:focus{border-color:rgba(182,132,47,.68)}
        .market-stat-card{
            border-radius:1.5rem;
            border:1px solid rgba(130,94,38,.14);
            background:rgba(255,252,245,.86);
            padding:1rem;
        }
        .market-ornament{
            height:10px;
            width:120px;
            border-radius:999px;
            background:
                linear-gradient(90deg, rgba(15,90,70,.16), rgba(182,132,47,.32), rgba(15,90,70,.16)),
                repeating-linear-gradient(90deg, transparent 0 10px, rgba(182,132,47,.56) 10px 14px, transparent 14px 24px);
        }
        .market-section-title{
            letter-spacing:-.03em;
        }
        .market-warm-link{color:var(--market-palm)}
        .market-warm-link:hover{color:#0a4838}
        .market-accent-warm{color:var(--market-brass)}
        .market-soft-surface{
            background:linear-gradient(180deg, rgba(255,249,239,.96), rgba(250,244,229,.86));
        }
        html[dir="rtl"] .market-hero-copy{text-align:right}
        html[dir="rtl"] .market-hero-chip-row{justify-content:flex-end}
        html[dir="rtl"] .market-hero-subtitle{margin-left:auto;margin-right:0}
        html[dir="rtl"] .market-hero-panel{text-align:right}
        @include('public.partials.market-nav-styles')
        .scroll-hidden{-ms-overflow-style:none;scrollbar-width:none}
        .scroll-hidden::-webkit-scrollbar{display:none}
    </style>
</head>
<body>
    @include('public.partials.market-nav', [
        'isAr' => $isAr,
        'navTx' => array_merge($tx, ['guest_subtitle' => $tx['hero_subtitle']]),
        'navLinks' => $marketplaceNavLinks,
        'navBrandHref' => $marketplaceUrl,
        'navBrandLabel' => $tx['brand'],
        'urlEn' => $urlEn,
        'urlAr' => $urlAr,
        'sellWithUsUrl' => Route::has('sales-flow')
            ? route('sales-flow')
            : (Route::has('book-call') ? route('book-call') : '#contact-us'),
    ])
    {{--
    <!-- Header -->
    <header class="absolute inset-x-0 top-0 z-40">
        <div class="market-shell mx-auto px-4 pt-4 text-white sm:px-6 lg:px-8">
            <div class="market-nav-shell flex items-center justify-between rounded-[28px] px-4 py-3 sm:px-5 lg:px-6">
            <div class="flex items-center gap-3">
                <button type="button" @click="mobileNavOpen = true" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/14 bg-white/8 text-white md:hidden">
                    <span class="sr-only">{{ $tx['menu_cta'] }}</span>
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16"/></svg>
                </button>
            <a href="{{ route('home') }}" class="flex items-center gap-3 font-semibold tracking-wide">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-white/14 text-sm font-bold text-white shadow-lg shadow-black/15 backdrop-blur">AS</span>
                <span>{{ $tx['brand'] }}</span>
            </a>
            </div>
            <nav class="hidden items-center gap-1 text-sm text-white/85 md:flex">
                <a href="{{ route('home') }}" class="market-nav-link">{{ $tx['home'] }}</a>
                <a href="#featured-properties" class="market-nav-link">{{ $tx['featured_nav'] }}</a>
                <a href="#properties-for-sale" class="market-nav-link">{{ $tx['sale_nav'] }}</a>
                <a href="#properties-for-rent" class="market-nav-link">{{ $tx['rent_nav'] }}</a>
                <a href="#contact-us" class="market-nav-link">{{ $tx['contact_nav'] }}</a>
            </nav>
            <div class="flex items-center gap-2 sm:gap-3">
                <div class="hidden items-center rounded-full border border-white/20 bg-white/10 p-1 text-xs font-medium backdrop-blur sm:flex">
                    <a href="{{ $urlEn }}" class="rounded-full px-3 py-1 {{ !$isAr ? 'bg-white text-slate-900' : 'text-white/80' }}">EN</a>
                    <a href="{{ $urlAr }}" class="rounded-full px-3 py-1 {{ $isAr ? 'bg-white text-slate-900' : 'text-white/80' }}">ع</a>
                </div>
                @auth
                    <div x-data="{ open: false }" @click.outside="open = false" class="relative hidden md:block">
                        <button type="button" @click="open = !open" class="inline-flex items-center gap-3 rounded-full border border-white/14 bg-white/8 py-1.5 pl-1.5 pr-3 {{ $isAr ? 'text-right' : 'text-left' }} text-white backdrop-blur">
                            <img src="{{ $userAvatar }}" alt="{{ $authUser->name }}" class="market-nav-avatar h-10 w-10 rounded-full ring-2 ring-white/20">
                            <div class="hidden min-w-0 lg:block">
                                <div class="truncate text-sm font-bold">{{ $userFirstName }}</div>
                                <div class="truncate text-xs text-white/62">{{ $tx['account_title'] }}</div>
                            </div>
                        </button>
                        <div x-show="open" x-cloak x-transition class="market-user-menu absolute {{ $isAr ? 'left-0' : 'right-0' }} mt-3 w-60 rounded-[24px] p-3 text-slate-900">
                            <div class="rounded-[18px] bg-[rgba(250,244,229,.9)] px-4 py-3">
                                <div class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">{{ $tx['welcome_cta'] }}</div>
                                <div class="mt-1 truncate text-base font-extrabold">{{ $authUser->name }}</div>
                                <div class="truncate text-sm text-slate-500">{{ $authUser->email }}</div>
                            </div>
                            <div class="mt-3 space-y-1">
                                @if($dashboardUrl)
                                    <a href="{{ $dashboardUrl }}" class="block rounded-2xl px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-[rgba(15,90,70,.06)]">{{ $tx['dashboard_cta'] }}</a>
                                @endif
                                @if($profileUrl)
                                    <a href="{{ $profileUrl }}" class="block rounded-2xl px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-[rgba(15,90,70,.06)]">{{ $tx['profile_cta'] }}</a>
                                @endif
                                <a href="{{ $sellWithUsUrl }}" class="block rounded-2xl px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-[rgba(15,90,70,.06)]">{{ $tx['sell_cta'] }}</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full rounded-2xl px-4 py-3 {{ $isAr ? 'text-right' : 'text-left' }} text-sm font-semibold text-[color:var(--market-clay)] transition hover:bg-[rgba(157,90,59,.08)]">{{ $tx['logout_cta'] }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    @if($loginUrl)
                        <a href="{{ $loginUrl }}" class="market-nav-action market-nav-ghost hidden md:inline-flex">{{ $tx['login_cta'] }}</a>
                    @endif
                    @if($registerUrl)
                        <a href="{{ $registerUrl }}" class="market-nav-action market-nav-solid hidden md:inline-flex">{{ $tx['register_cta'] }}</a>
                    @endif
                    <a href="{{ $sellWithUsUrl }}" class="market-nav-action market-nav-ghost hidden lg:inline-flex">{{ $tx['sell_cta'] }}</a>
                @endauth
            </div>
            </div>
        </div>
    </header>

    <div x-show="mobileNavOpen" x-cloak @click="mobileNavOpen = false" class="fixed inset-0 z-40 bg-black/55 backdrop-blur-sm md:hidden"></div>
    <aside x-show="mobileNavOpen" x-cloak class="market-mobile-sheet fixed top-0 {{ $isAr ? 'right-0' : 'left-0' }} z-50 h-full w-[88vw] max-w-sm overflow-y-auto md:hidden" x-transition:enter="transition ease-out duration-250" x-transition:enter-start="{{ $isAr ? 'translate-x-full' : '-translate-x-full' }}" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-180" x-transition:leave-start="translate-x-0" x-transition:leave-end="{{ $isAr ? 'translate-x-full' : '-translate-x-full' }}">
        <div class="px-5 pb-6 pt-5">
            <div class="flex items-center justify-between text-white">
                <div class="flex items-center gap-3">
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/12 bg-white/12 text-sm font-bold">AS</span>
                    <div>
                        <div class="text-sm font-bold uppercase tracking-[0.18em] text-white/60">{{ $tx['menu_cta'] }}</div>
                        <div class="text-base font-extrabold">{{ $tx['brand'] }}</div>
                    </div>
                </div>
                <button type="button" @click="mobileNavOpen = false" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/12 bg-white/10 text-white">
                    <span class="sr-only">{{ $tx['close_cta'] }}</span>
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="mt-6 rounded-[24px] bg-white/10 p-4 text-white backdrop-blur">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <div class="text-xs font-bold uppercase tracking-[0.18em] text-white/60">{{ $tx['account_title'] }}</div>
                        @auth
                            <div class="mt-1 text-lg font-extrabold">{{ $authUser->name }}</div>
                            <div class="text-sm text-white/70">{{ $authUser->email }}</div>
                        @else
                            <div class="mt-1 text-lg font-extrabold">{{ $tx['welcome_cta'] }}</div>
                            <div class="text-sm text-white/70">{{ $tx['hero_subtitle'] }}</div>
                        @endauth
                    </div>
                    @auth
                        <img src="{{ $userAvatar }}" alt="{{ $authUser->name }}" class="h-14 w-14 rounded-full ring-2 ring-white/16">
                    @endauth
                </div>
            </div>

            <div class="mt-5 grid gap-3">
                @auth
                    @if($dashboardUrl)
                        <a href="{{ $dashboardUrl }}" @click="mobileNavOpen = false" class="market-mobile-link market-mobile-link-dark">{{ $tx['dashboard_cta'] }}</a>
                    @endif
                    @if($profileUrl)
                        <a href="{{ $profileUrl }}" @click="mobileNavOpen = false" class="market-mobile-link market-mobile-link-dark">{{ $tx['profile_cta'] }}</a>
                    @endif
                    <a href="{{ $sellWithUsUrl }}" @click="mobileNavOpen = false" class="market-mobile-link market-mobile-link-dark">{{ $tx['sell_cta'] }}</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="market-mobile-link market-mobile-link-dark w-full {{ $isAr ? 'text-right' : 'text-left' }}">{{ $tx['logout_cta'] }}</button>
                    </form>
                @else
                    @if($loginUrl)
                        <a href="{{ $loginUrl }}" @click="mobileNavOpen = false" class="market-mobile-link market-mobile-link-dark">{{ $tx['login_cta'] }}</a>
                    @endif
                    @if($registerUrl)
                        <a href="{{ $registerUrl }}" @click="mobileNavOpen = false" class="market-mobile-link">{{ $tx['register_cta'] }}</a>
                    @endif
                    <a href="{{ $sellWithUsUrl }}" @click="mobileNavOpen = false" class="market-mobile-link">{{ $tx['sell_cta'] }}</a>
                @endauth
            </div>

            <div class="mt-6 rounded-[26px] bg-white p-5">
                <div class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">{{ $tx['browse_title'] }}</div>
                <div class="mt-4 grid gap-3">
                    <a href="{{ route('home') }}" @click="mobileNavOpen = false" class="market-mobile-link">{{ $tx['home'] }}</a>
                    <a href="#featured-properties" @click="mobileNavOpen = false" class="market-mobile-link">{{ $tx['featured_nav'] }}</a>
                    <a href="#properties-for-sale" @click="mobileNavOpen = false" class="market-mobile-link">{{ $tx['sale_nav'] }}</a>
                    <a href="#properties-for-rent" @click="mobileNavOpen = false" class="market-mobile-link">{{ $tx['rent_nav'] }}</a>
                    <a href="#contact-us" @click="mobileNavOpen = false" class="market-mobile-link">{{ $tx['contact_nav'] }}</a>
                </div>
            </div>

            <div class="mt-6 rounded-[26px] bg-white p-5">
                <div class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">{{ __('Switch language') }}</div>
                <div class="mt-4 grid grid-cols-2 gap-3">
                    <a href="{{ $urlEn }}" class="market-mobile-link text-center {{ !$isAr ? 'ring-2 ring-[color:var(--market-brass)]' : '' }}">EN</a>
                    <a href="{{ $urlAr }}" class="market-mobile-link text-center {{ $isAr ? 'ring-2 ring-[color:var(--market-brass)]' : '' }}">العربية</a>
                </div>
            </div>
        </div>
    </aside>
    --}}

    <!-- Hero -->
    <section class="market-hero relative min-h-[820px] pb-24 pt-36 text-white">
        <div class="market-shell mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid gap-10 lg:grid-cols-[1.08fr_.92fr] lg:items-end">
                <div class="market-hero-copy max-w-3xl text-center lg:text-left">
                    <div class="market-hero-chip-row mb-6 flex flex-wrap justify-center gap-3 lg:justify-start">
                        <span class="market-hero-chip">{{ $tx['featured_nav'] }}</span>
                        <span class="market-hero-chip">{{ $tx['sale_nav'] }}</span>
                        <span class="market-hero-chip">{{ $tx['rent_nav'] }}</span>
                    </div>
                    <p class="market-kicker mb-4 text-xs font-semibold uppercase text-white/68">{{ $tx['hero_eyebrow'] }}</p>
                    <h1 class="mb-5 text-4xl font-extrabold leading-[1.02] tracking-[-0.04em] sm:text-5xl lg:text-[4.2rem]">{{ $tx['hero_title'] }}</h1>
                    <p class="market-hero-subtitle mx-auto mb-8 max-w-2xl text-base leading-8 text-white/78 sm:text-lg lg:mx-0">{{ $tx['hero_subtitle'] }}</p>
                    <div class="grid gap-3 sm:grid-cols-3">
                        <div class="market-note"><div class="market-note-label">{{ $tx['properties'] }}</div><div class="market-note-value">{{ $tx['categories_title'] }}</div></div>
                        <div class="market-note"><div class="market-note-label">{{ $tx['agencies_title'] }}</div><div class="market-note-value">{{ $tx['agencies_subtitle'] }}</div></div>
                        <div class="market-note"><div class="market-note-label">{{ $tx['catalog_title'] }}</div><div class="market-note-value">{{ $tx['why_2_title'] }}</div></div>
                    </div>
                </div>
                <aside class="market-panel market-hero-panel rounded-[32px] p-6 text-left text-slate-900 sm:p-7 lg:p-8">
                    <div class="market-ornament"></div>
                    <p class="market-kicker mt-6 text-xs font-semibold uppercase market-accent-warm">{{ $tx['featured_eyebrow'] }}</p>
                    <h2 class="mt-3 text-3xl font-extrabold leading-tight tracking-[-0.03em] text-slate-900">{{ $tx['why_title'] }}</h2>
                    <p class="mt-3 text-sm leading-7 text-slate-600">{{ $tx['contact_text'] }}</p>
                    <div id="web-stats" class="mt-8 grid grid-cols-2 gap-3">
                        <div class="market-stat-card"><div class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">{{ $tx['properties'] }}</div><div class="mt-2 text-3xl font-extrabold leading-none text-slate-900" data-stat="properties">&mdash;</div></div>
                        <div class="market-stat-card"><div class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">{{ $tx['sale'] }}</div><div class="mt-2 text-3xl font-extrabold leading-none text-[color:var(--market-brass)]" data-stat="sale">&mdash;</div></div>
                        <div class="market-stat-card"><div class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">{{ $tx['rent'] }}</div><div class="mt-2 text-3xl font-extrabold leading-none text-[color:var(--market-palm)]" data-stat="rent">&mdash;</div></div>
                        <div class="market-stat-card"><div class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">{{ $tx['managers'] }}</div><div class="mt-2 text-3xl font-extrabold leading-none text-slate-900" data-stat="managers">&mdash;</div></div>
                    </div>
                </aside>
            </div>
            <form id="hero-search-form" class="market-search-wrap mx-auto mt-12 max-w-6xl rounded-[30px] p-4 sm:p-5">
                <div class="grid grid-cols-1 gap-3 md:grid-cols-5">
                    <div class="md:col-span-2">
                        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="{{ $tx['keyword'] }}" class="market-search-input h-14 w-full rounded-2xl border px-4 text-sm font-medium text-slate-900 outline-none transition">
                    </div>
                    <div>
                        <select name="city_id" class="market-search-input h-14 w-full rounded-2xl border px-4 text-sm font-medium text-slate-900 outline-none transition">
                            <option value="">{{ $tx['city'] }}</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" @selected(($filters['city_id'] ?? null) == $city->id)>{{ $city['name_'.$loc] ?? $city->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="listing_type" class="market-search-input h-14 w-full rounded-2xl border px-4 text-sm font-medium text-slate-900 outline-none transition">
                            <option value="">{{ $tx['type'] }}</option>
                            <option value="sale" @selected(($filters['listing_type'] ?? null) === 'sale')>{{ __('For Sale') }}</option>
                            <option value="rent" @selected(($filters['listing_type'] ?? null) === 'rent')>{{ __('For Rent') }}</option>
                        </select>
                    </div>
                    <button type="submit" class="h-14 rounded-2xl px-6 text-sm font-semibold text-white transition hover:opacity-95" style="background:linear-gradient(135deg,var(--market-palm),var(--market-river));box-shadow:0 18px 34px -18px rgba(15,90,70,.8)">{{ $tx['search'] }}</button>
                </div>
            </form>
        </div>
    </section>

    <main class="-mt-12 pb-20">
        <div class="market-shell mx-auto space-y-16 px-4 sm:px-6 lg:px-8">

            <!-- Categories (from mobile design) — populated by JS -->
            <section class="market-card market-soft-surface rounded-[32px] px-6 py-10 sm:px-8">
                <div class="mb-8"><div class="market-ornament mb-5"></div><h2 class="market-section-title text-2xl font-extrabold text-slate-900">{{ $tx['categories_title'] }}</h2><p class="mt-2 text-sm leading-7 text-slate-500">{{ $tx['categories_subtitle'] }}</p></div>
                <div id="web-categories" class="flex snap-x snap-mandatory gap-4 overflow-x-auto scroll-hidden pb-2">
                    <div class="text-sm text-slate-400 py-8">{{ $tx['loading'] }}</div>
                </div>
            </section>

            <!-- Featured Properties — populated by JS -->
            <section id="featured-properties" class="market-card rounded-[32px] px-6 py-10 sm:px-8">
                <div class="mb-8 text-center">
                    <div class="mx-auto mb-5 market-ornament"></div>
                    <p class="market-kicker mb-3 text-xs font-semibold uppercase market-accent-warm">{{ $tx['featured_eyebrow'] }}</p>
                    <h2 class="market-section-title text-3xl font-extrabold text-slate-900">{{ $tx['featured_title'] }}</h2>
                </div>
                <div id="web-featured" class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                    <div class="col-span-full text-center text-sm text-slate-400 py-8">{{ $tx['loading'] }}</div>
                </div>
            </section>

            <!-- Why Choose Us (static) -->
            <section>
                <div class="mb-8 text-center">
                    <div class="mx-auto mb-5 market-ornament"></div>
                    <p class="market-kicker mb-3 text-xs font-semibold uppercase market-accent-warm">{{ $tx['featured_eyebrow'] }}</p>
                    <h2 class="market-section-title text-3xl font-extrabold text-slate-900">{{ $tx['why_title'] }}</h2>
                </div>
                <div class="grid gap-6 lg:grid-cols-4">
                    <div class="market-card rounded-[28px] p-7 text-center"><div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-[color:rgba(182,132,47,.12)] text-[color:var(--market-brass)]"><svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-2.21 0-4 1.79-4 4m8 0a4 4 0 10-8 0m8 0c0 1.977-1.523 3.6-3.46 3.96M12 4v4m0 8v4m8-8h-4M8 12H4"/></svg></div><h3 class="mb-2 text-lg font-bold text-slate-900">{{ $tx['why_1_title'] }}</h3><p class="text-sm leading-7 text-slate-600">{{ $tx['why_1_text'] }}</p></div>
                    <div class="market-card rounded-[28px] p-7 text-center"><div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-[color:rgba(15,90,70,.1)] text-[color:var(--market-palm)]"><svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div><h3 class="mb-2 text-lg font-bold text-slate-900">{{ $tx['why_2_title'] }}</h3><p class="text-sm leading-7 text-slate-600">{{ $tx['why_2_text'] }}</p></div>
                    <div class="market-card rounded-[28px] p-7 text-center"><div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-[color:rgba(47,122,114,.12)] text-[color:var(--market-river)]"><svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5V4H2v16h5m10 0v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6m10 0H7"/></svg></div><h3 class="mb-2 text-lg font-bold text-slate-900">{{ $tx['why_3_title'] }}</h3><p class="text-sm leading-7 text-slate-600">{{ $tx['why_3_text'] }}</p></div>
                    <div class="market-card rounded-[28px] p-7 text-center"><div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-[color:rgba(157,90,59,.1)] text-[color:var(--market-clay)]"><svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z"/></svg></div><h3 class="mb-2 text-lg font-bold text-slate-900">{{ $tx['why_4_title'] }}</h3><p class="text-sm leading-7 text-slate-600">{{ $tx['why_4_text'] }}</p></div>
                </div>
            </section>

            <!-- Properties For Sale — populated by JS -->
            <section id="properties-for-sale" class="space-y-6">
                <div class="flex items-end justify-between gap-4"><div><p class="market-kicker mb-2 text-xs font-semibold uppercase market-accent-warm">{{ $tx['sale_eyebrow'] }}</p><h2 class="market-section-title text-3xl font-extrabold text-slate-900">{{ $tx['sale_title'] }}</h2></div><a href="?listing_type=sale#catalog" class="market-warm-link text-sm font-semibold">{{ $tx['view_all'] }}</a></div>
                <div id="web-sale" class="flex snap-x snap-mandatory gap-5 overflow-x-auto scroll-hidden pb-4">
                    <div class="text-sm text-slate-400 py-8">{{ $tx['loading'] }}</div>
                </div>
            </section>

            <!-- Properties For Rent — populated by JS -->
            <section id="properties-for-rent" class="space-y-6">
                <div class="flex items-end justify-between gap-4"><div><p class="market-kicker mb-2 text-xs font-semibold uppercase market-accent-warm">{{ $tx['rent_eyebrow'] }}</p><h2 class="market-section-title text-3xl font-extrabold text-slate-900">{{ $tx['rent_title'] }}</h2></div><a href="?listing_type=rent#catalog" class="market-warm-link text-sm font-semibold">{{ $tx['view_all'] }}</a></div>
                <div id="web-rent" class="flex snap-x snap-mandatory gap-5 overflow-x-auto scroll-hidden pb-4">
                    <div class="text-sm text-slate-400 py-8">{{ $tx['loading'] }}</div>
                </div>
            </section>

            <!-- Popular Cities — populated by JS -->
            <section class="space-y-6">
                <div class="text-center"><div class="mx-auto mb-5 market-ornament"></div><p class="market-kicker mb-2 text-xs font-semibold uppercase market-accent-warm">{{ $tx['cities_eyebrow'] }}</p><h2 class="market-section-title text-3xl font-extrabold text-slate-900">{{ $tx['cities_title'] }}</h2></div>
                <div id="web-cities" class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">
                    <div class="col-span-full text-center text-sm text-slate-400 py-8">{{ $tx['loading'] }}</div>
                </div>
            </section>

            <!-- Top Agencies (from mobile design) — populated by JS -->
            <section class="space-y-6">
                <div class="text-center"><div class="mx-auto mb-5 market-ornament"></div><p class="market-kicker mb-2 text-xs font-semibold uppercase market-accent-warm">{{ $tx['agencies_eyebrow'] }}</p><h2 class="market-section-title text-3xl font-extrabold text-slate-900">{{ $tx['agencies_title'] }}</h2><p class="mt-2 text-sm text-slate-500">{{ $tx['agencies_subtitle'] }}</p></div>
                <div id="web-agencies" class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
                    <div class="col-span-full text-center text-sm text-slate-400 py-8">{{ $tx['loading'] }}</div>
                </div>
            </section>

            <!-- Contact (static) -->
            <section id="contact-us" class="grid gap-6 lg:grid-cols-[1.15fr_.85fr]">
                <div class="overflow-hidden rounded-[30px] p-8 text-white shadow-[0_24px_64px_-28px_rgba(15,23,42,.55)]" style="background:radial-gradient(circle at top left, rgba(255,255,255,.12), transparent 28%), linear-gradient(145deg, #14392f, #0f5a46 55%, #2f7a72);"><p class="market-kicker mb-3 text-xs font-semibold uppercase text-[color:rgba(255,245,223,.7)]">{{ $tx['contact_eyebrow'] }}</p><h2 class="mb-4 text-3xl font-extrabold tracking-[-0.03em]">{{ $tx['contact_title'] }}</h2><p class="mb-8 max-w-xl text-sm leading-7 text-white/78">{{ $tx['contact_text'] }}</p><form action="{{ route('book-call') }}" method="GET" class="grid gap-4 sm:grid-cols-2"><input type="text" name="name" placeholder="{{ $tx['name'] }}" class="h-12 rounded-2xl border border-white/10 bg-white/10 px-4 text-sm text-white placeholder:text-slate-300 focus:border-[color:rgba(182,132,47,.7)] focus:outline-none"><input type="email" name="email" placeholder="{{ $tx['email'] }}" class="h-12 rounded-2xl border border-white/10 bg-white/10 px-4 text-sm text-white placeholder:text-slate-300 focus:border-[color:rgba(182,132,47,.7)] focus:outline-none"><input type="text" name="phone" placeholder="{{ $tx['phone'] }}" class="h-12 rounded-2xl border border-white/10 bg-white/10 px-4 text-sm text-white placeholder:text-slate-300 focus:border-[color:rgba(182,132,47,.7)] focus:outline-none sm:col-span-2"><button type="submit" class="inline-flex h-12 items-center justify-center rounded-2xl bg-white px-6 text-sm font-semibold text-slate-900 transition hover:bg-white/92 sm:col-span-2">{{ $tx['submit'] }}</button></form></div>
                <div class="market-card market-soft-surface rounded-[30px] p-8"><p class="market-kicker mb-3 text-xs font-semibold uppercase market-accent-warm">{{ $tx['sidebar_eyebrow'] }}</p><h3 class="mb-4 text-2xl font-extrabold tracking-[-0.03em] text-slate-900">{{ $tx['sidebar_title'] }}</h3><ul class="space-y-4 text-sm leading-7 text-slate-600"><li class="flex gap-3"><span class="mt-2 h-2.5 w-2.5 rounded-full bg-[color:var(--market-brass)]"></span><span>{{ $tx['sidebar_1'] }}</span></li><li class="flex gap-3"><span class="mt-2 h-2.5 w-2.5 rounded-full bg-[color:var(--market-palm)]"></span><span>{{ $tx['sidebar_2'] }}</span></li><li class="flex gap-3"><span class="mt-2 h-2.5 w-2.5 rounded-full bg-[color:var(--market-river)]"></span><span>{{ $tx['sidebar_3'] }}</span></li><li class="flex gap-3"><span class="mt-2 h-2.5 w-2.5 rounded-full bg-[color:var(--market-clay)]"></span><span>{{ $tx['sidebar_4'] }}</span></li></ul></div>
            </section>

            <!-- Catalog Browse — JS search with API pagination -->
            <section id="catalog" class="market-card market-soft-surface rounded-[32px] px-6 py-10 sm:px-8">
                <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div><div class="mb-4 market-ornament"></div><p class="market-kicker mb-2 text-xs font-semibold uppercase market-accent-warm">{{ $tx['catalog_eyebrow'] }}</p><h2 class="market-section-title text-3xl font-extrabold text-slate-900">{{ $tx['catalog_title'] }}</h2></div>
                    <div id="catalog-count" class="text-sm text-slate-500"></div>
                </div>
                <form id="catalog-filter-form" class="mb-8 grid gap-4 rounded-[28px] border border-[color:rgba(182,132,47,.16)] bg-[color:rgba(255,252,246,.88)] p-5 md:grid-cols-2 xl:grid-cols-6">
                    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="{{ $tx['keyword'] }}" class="market-search-input h-12 rounded-2xl border px-4 text-sm font-medium text-slate-900 outline-none xl:col-span-2">
                    <select name="subcategory_id" class="market-search-input h-12 rounded-2xl border px-4 text-sm font-medium text-slate-900 outline-none"><option value="">{{ $tx['all_categories'] }}</option>@foreach($categories as $category)<optgroup label="{{ $category['name_'.$loc] ?? $category['name_en'] }}">@foreach($category->subcategories as $subcategory)<option value="{{ $subcategory->id }}" @selected(($filters['subcategory_id'] ?? null) == $subcategory->id)>{{ $subcategory['name_'.$loc] ?? $subcategory['name_en'] }}</option>@endforeach</optgroup>@endforeach</select>
                    <select name="bedrooms" class="market-search-input h-12 rounded-2xl border px-4 text-sm font-medium text-slate-900 outline-none"><option value="">{{ $tx['bedrooms'] }}</option>@for($i = 1; $i <= 6; $i++)<option value="{{ $i }}" @selected(($filters['bedrooms'] ?? null) == $i)>{{ $i }}+</option>@endfor</select>
                    <select name="sort" class="market-search-input h-12 rounded-2xl border px-4 text-sm font-medium text-slate-900 outline-none"><option value="latest" @selected(($filters['sort'] ?? 'latest') === 'latest')>{{ __('Latest') }}</option><option value="price_asc" @selected(($filters['sort'] ?? null) === 'price_asc')>{{ __('Price: Low to High') }}</option><option value="price_desc" @selected(($filters['sort'] ?? null) === 'price_desc')>{{ __('Price: High to Low') }}</option><option value="oldest" @selected(($filters['sort'] ?? null) === 'oldest')>{{ __('Oldest') }}</option></select>
                    <div class="flex gap-3 xl:col-span-1"><button type="submit" class="flex-1 rounded-2xl px-4 text-sm font-semibold text-white transition hover:opacity-95" style="background:linear-gradient(135deg,var(--market-palm),var(--market-river))">{{ __('Apply Filters') }}</button><button type="button" id="catalog-clear-btn" class="inline-flex h-12 items-center justify-center rounded-2xl border border-[color:rgba(130,94,38,.18)] px-4 text-sm font-semibold text-slate-700 transition hover:bg-white">{{ __('Clear') }}</button></div>
                </form>
                <div id="catalog-results" class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                    <div class="col-span-full text-center text-sm text-slate-400 py-8">{{ $tx['loading'] }}</div>
                </div>
                <div id="catalog-pagination" class="mt-8 flex items-center justify-center gap-2"></div>
            </section>
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-[color:rgba(182,132,47,.15)] bg-[#12231f] py-12 text-slate-300">
        <div class="market-shell mx-auto grid gap-10 px-4 sm:px-6 lg:grid-cols-4 lg:px-8">
            <div><div class="mb-4 text-lg font-semibold text-white">{{ $tx['brand'] }}</div><p class="text-sm leading-7 text-slate-400">{{ $tx['footer_text'] }}</p></div>
            <div><div class="mb-4 text-sm font-semibold uppercase tracking-[0.18em] text-white">{{ $tx['footer_links'] }}</div><div class="space-y-3 text-sm"><a href="{{ route('home') }}" class="block hover:text-white">{{ $tx['home'] }}</a><a href="#properties-for-sale" class="block hover:text-white">{{ $tx['sale_nav'] }}</a><a href="#properties-for-rent" class="block hover:text-white">{{ $tx['rent_nav'] }}</a></div></div>
            <div><div class="mb-4 text-sm font-semibold uppercase tracking-[0.18em] text-white">{{ $tx['footer_contact'] }}</div><div class="space-y-3 text-sm text-slate-400"><div>support@aqarismart.com</div><div>+962 7 9000 0000</div><div>{{ $tx['footer_address'] }}</div></div></div>
            <div><div class="mb-4 text-sm font-semibold uppercase tracking-[0.18em] text-white">{{ $tx['footer_cta'] }}</div><a href="{{ route('book-call') }}" class="inline-flex rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-900 transition hover:bg-white/92">{{ $tx['book'] }}</a></div>
        </div>
    </footer>

    <script>
    const lang = '{{ $loc }}';
    const isRtl = lang === 'ar';
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
        const url = new URL(window.location.origin);
        url.hostname = `${slug}.${baseDomain}`;
        return url.toString().replace(/\/$/, '');
    }

    function fallbackImage(seed) {
        return `https://picsum.photos/seed/${encodeURIComponent(seed)}/900/640`;
    }

    function listingMeta(u) {
        const cityName = lang === 'ar' ? (u.city?.name_ar ?? u.city?.name_en ?? '') : (u.city?.name_en ?? u.city?.name_ar ?? '');
        const propName = u.property?.name ?? '';
        return propName && cityName ? `${propName} - ${cityName}` : (propName || cityName);
    }

    function badgeTone(isSale) {
        if (isSale) {
            return 'background:rgba(182,132,47,.92);color:#fff9ef;box-shadow:0 16px 28px -18px rgba(182,132,47,.9)';
        }

        return 'background:rgba(15,90,70,.92);color:#f3fbf8;box-shadow:0 16px 28px -18px rgba(15,90,70,.86)';
    }

    function initials(name) {
        return (name ?? '')
            .split(/\s+/)
            .filter(Boolean)
            .slice(0, 2)
            .map(part => part[0]?.toUpperCase() ?? '')
            .join('') || 'AS';
    }

    function edgeClass(startLtr, startRtl) {
        return isRtl ? startRtl : startLtr;
    }

    function textAlignClass() {
        return isRtl ? 'text-right' : 'text-left';
    }

    function inlineDirectionClass() {
        return isRtl ? 'flex-row-reverse text-right' : '';
    }

    function unitCardHtml(u) {
        const title = u.translated_title ?? u.title ?? u.code;
        const photo = (u.photos && u.photos[0]) ? u.photos[0] : fallbackImage(`aqarismart-${u.code ?? u.id ?? title}`);
        const loc = listingMeta(u);
        const isSale = u.listing_type === 'sale';
        const badge = isSale ? TX.sale : TX.rent;
        const slug = u.tenant?.slug;
        const href = slug ? `${tenantUrl(slug)}/listings/${u.code ?? u.id}` : '#';
        return `<article class="group overflow-hidden rounded-[28px] border border-[rgba(130,94,38,.16)] bg-[rgba(255,252,246,.96)] shadow-[0_24px_54px_-34px_rgba(55,38,12,.4)] transition duration-300 hover:-translate-y-1.5 hover:shadow-[0_34px_72px_-34px_rgba(55,38,12,.48)]">
            <a href="${href}" class="block relative">
                <div class="relative h-64 overflow-hidden bg-[rgba(34,38,30,.08)]">
                    <img src="${photo}" alt="${title}" class="h-full w-full object-cover transition duration-700 group-hover:scale-110" loading="lazy" onerror="this.src='${fallbackImage('aqarismart-fallback')}'">
                    <div class="absolute inset-0 bg-gradient-to-t from-[rgba(14,22,17,.9)] via-[rgba(14,22,17,.18)] to-transparent"></div>
                    <div class="absolute ${edgeClass('left-4', 'right-4')} top-4"><span class="inline-flex items-center rounded-full px-3 py-1.5 text-[11px] font-extrabold uppercase tracking-[0.2em]" style="${badgeTone(isSale)}">${badge}</span></div>
                    <div class="absolute bottom-4 left-4 right-4 flex items-end justify-between gap-3 ${isRtl ? 'flex-row-reverse text-right' : ''}">
                        <div class="max-w-[70%] rounded-2xl bg-[rgba(255,248,235,.92)] px-4 py-2 text-sm font-extrabold text-slate-900 shadow-[0_18px_30px_-18px_rgba(15,20,16,.58)]">${u.currency ?? 'JOD'} ${fmt.format(u.price ?? 0)}</div>
                        <div class="rounded-full border border-white/14 bg-white/10 px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.18em] text-white backdrop-blur">${u.code ?? ''}</div>
                    </div>
                </div>
            </a>
            <div class="p-6 ${textAlignClass()}">
                <a href="${href}"><h3 class="text-xl font-extrabold leading-tight tracking-[-0.03em] text-slate-900 transition group-hover:text-[color:var(--market-palm)] line-clamp-1">${title}</h3></a>
                ${loc ? `<p class="mt-2 flex items-center text-sm font-medium text-slate-500 line-clamp-1 ${inlineDirectionClass()}"><svg class="${isRtl ? 'ml-1.5' : 'mr-1.5'} h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>${loc}</p>` : `<p class="mt-2 text-sm font-medium text-slate-400">${TX.properties}</p>`}
                <div class="mt-5 flex items-center gap-3 border-t border-[rgba(130,94,38,.12)] pt-4 text-[11px] font-extrabold uppercase tracking-[0.14em] text-slate-600 ${isRtl ? 'flex-row-reverse justify-end' : ''}">
                    <span class="rounded-full bg-[rgba(15,90,70,.08)] px-3 py-1.5 text-[color:var(--market-palm)]">${u.bedrooms ?? u.beds ?? 0} ${TX.beds}</span>
                    <span class="rounded-full bg-[rgba(182,132,47,.11)] px-3 py-1.5 text-[color:var(--market-brass)]">${u.bathrooms ?? u.baths ?? 0} ${TX.baths}</span>
                    ${u.sqft ? `<span>${fmt.format(u.sqft)} ${TX.sqft}</span>` : ''}
                </div>
            </div>
        </article>`;
    }

    function scrollCardHtml(u) {
        const title = u.translated_title ?? u.title ?? u.code;
        const photo = (u.photos && u.photos[0]) ? u.photos[0] : fallbackImage(`scroll-${u.code ?? u.id ?? title}`);
        const isSale = u.listing_type === 'sale';
        const badge = isSale ? TX.sale : TX.rent;
        const loc = listingMeta(u);
        const slug = u.tenant?.slug;
        const href = slug ? `${tenantUrl(slug)}/listings/${u.code ?? u.id}` : '#';
        return `<a href="${href}" class="group block min-w-[296px] max-w-[336px] flex-1 snap-start overflow-hidden rounded-[28px] border border-[rgba(130,94,38,.16)] bg-[rgba(255,252,246,.96)] shadow-[0_22px_46px_-34px_rgba(55,38,12,.46)] transition duration-300 hover:-translate-y-1.5 hover:shadow-[0_30px_62px_-32px_rgba(55,38,12,.52)]">
            <div class="relative h-52 overflow-hidden bg-[rgba(34,38,30,.08)]"><img src="${photo}" alt="${title}" class="h-full w-full object-cover transition duration-700 group-hover:scale-110" loading="lazy" onerror="this.src='${fallbackImage('aqarismart-fallback')}'">
            <div class="absolute inset-0 bg-gradient-to-t from-[rgba(12,18,14,.88)] via-transparent to-transparent"></div>
            <div class="absolute ${edgeClass('left-3', 'right-3')} top-3"><span class="inline-flex items-center rounded-full px-3 py-1.5 text-[10px] font-extrabold uppercase tracking-[0.2em]" style="${badgeTone(isSale)}">${badge}</span></div>
            <div class="absolute ${edgeClass('left-3', 'right-3')} bottom-3"><span class="inline-flex items-center rounded-2xl bg-[rgba(255,248,235,.94)] px-3 py-1.5 text-sm font-extrabold text-slate-900 shadow-[0_14px_26px_-18px_rgba(11,17,13,.62)]">${u.currency ?? 'JOD'} ${fmt.format(u.price ?? 0)}</span></div></div>
            <div class="p-4 ${textAlignClass()}"><h3 class="text-lg font-extrabold tracking-[-0.02em] text-slate-900 line-clamp-1 transition group-hover:text-[color:var(--market-palm)]">${title}</h3>
            ${loc ? `<p class="mt-1 line-clamp-1 text-sm font-medium text-slate-500">${loc}</p>` : ''}
            <div class="mt-4 flex items-center gap-3 text-[11px] font-extrabold uppercase tracking-[0.14em] text-slate-600 ${isRtl ? 'flex-row-reverse justify-end' : ''}"><span class="rounded-full bg-[rgba(15,90,70,.08)] px-3 py-1.5 text-[color:var(--market-palm)]">${u.bedrooms ?? u.beds ?? 0} ${TX.beds}</span><span class="rounded-full bg-[rgba(182,132,47,.11)] px-3 py-1.5 text-[color:var(--market-brass)]">${u.bathrooms ?? u.baths ?? 0} ${TX.baths}</span></div></div></a>`;
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
            const photo = cat.image || fallbackImage(`category-${cat.id}`);
            return `<button type="button" class="category-btn shrink-0 snap-start group relative w-[172px] overflow-hidden rounded-[26px] border border-[rgba(130,94,38,.16)] bg-[rgba(255,252,246,.96)] shadow-[0_20px_40px_-32px_rgba(55,38,12,.45)] transition duration-300 hover:-translate-y-1.5 hover:shadow-[0_28px_58px_-32px_rgba(55,38,12,.52)] active:scale-[.98]" data-id="${cat.id}">
                <div class="relative h-28 w-full overflow-hidden"><img src="${photo}" alt="${name}" class="h-full w-full object-cover transition duration-700 group-hover:scale-110" loading="lazy" onerror="this.src='${fallbackImage('category-fallback')}'"><div class="absolute inset-0 bg-gradient-to-t from-[rgba(17,24,20,.76)] via-transparent to-transparent"></div></div>
                <div class="p-4 ${textAlignClass()}"><div class="mb-3 h-1.5 w-12 rounded-full bg-[color:rgba(182,132,47,.55)] ${isRtl ? 'mr-auto' : ''}"></div><h3 class="truncate text-sm font-extrabold tracking-[-0.01em] text-slate-900">${name}</h3><p class="mt-1 text-[11px] font-bold uppercase tracking-[0.14em] text-slate-500">${fmt.format(cat.count ?? 0)} ${TX.listings}</p></div></button>`;
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
            const photo = city.image || fallbackImage(`city-${city.id}`);
            return `<button type="button" class="city-btn group overflow-hidden rounded-[28px] border border-[rgba(130,94,38,.16)] bg-[rgba(255,252,246,.96)] p-4 ${textAlignClass()} shadow-[0_20px_42px_-32px_rgba(55,38,12,.44)] transition duration-300 hover:-translate-y-1.5 hover:shadow-[0_30px_62px_-30px_rgba(55,38,12,.5)]" data-id="${city.id}">
                <div class="relative mb-5 h-40 overflow-hidden rounded-[22px]"><img src="${photo}" alt="${name}" class="h-full w-full object-cover transition duration-700 group-hover:scale-110" loading="lazy" onerror="this.src='${fallbackImage('city-fallback')}'"><div class="absolute inset-0 bg-gradient-to-t from-[rgba(15,24,19,.88)] via-[rgba(15,24,19,.2)] to-transparent"></div>
                <div class="absolute bottom-4 left-4 right-4 ${textAlignClass()}"><h3 class="text-xl font-extrabold tracking-[-0.03em] text-white">${name}</h3><p class="mt-1 text-[11px] font-extrabold uppercase tracking-[0.16em] text-[rgba(255,241,212,.86)]">${fmt.format(city.units_count ?? 0)} ${TX.properties}</p></div></div>
                <div class="flex items-center justify-between px-1 ${isRtl ? 'flex-row-reverse' : ''}"><span class="text-sm font-bold text-slate-700">${lang === 'ar' ? 'استكشف المنطقة' : 'Explore area'}</span><span class="rounded-full bg-[rgba(15,90,70,.1)] px-3 py-1 text-[11px] font-extrabold uppercase tracking-[0.14em] text-[color:var(--market-palm)]">${lang === 'ar' ? 'عرض' : 'Browse'}</span></div></button>`;
        }).join('');
        c.querySelectorAll('.city-btn').forEach(btn => {
            btn.addEventListener('click', () => { catalogSetFilter('city_id', btn.dataset.id); });
        });
    }

    function renderAgencies(tenants) {
        const c = document.getElementById('web-agencies');
        if (!c || !tenants?.length) { if(c) c.innerHTML = ''; return; }
        c.innerHTML = tenants.map(t => {
            const logo = t.branding?.logo_url ?? '';
            const desc = t.summary?.description ?? (lang === 'ar' ? 'وكالة عقارية بطابع محلي وخدمة موثوقة.' : 'A trusted real estate agency with a local market feel.');
            const avatar = logo
                ? `<img src="${logo}" alt="${t.name}" class="h-full w-full object-cover" loading="lazy" onerror="this.parentElement.innerHTML='<span class=&quot;text-lg font-extrabold text-[color:var(--market-palm)]&quot;>${initials(t.name)}</span>'">`
                : `<span class="text-lg font-extrabold text-[color:var(--market-palm)]">${initials(t.name)}</span>`;
            return `<a href="${tenantUrl(t.slug)}" class="block rounded-[28px] border border-[rgba(130,94,38,.16)] bg-[rgba(255,252,246,.96)] p-6 shadow-[0_20px_42px_-32px_rgba(55,38,12,.44)] transition duration-300 hover:-translate-y-1.5 hover:shadow-[0_30px_62px_-30px_rgba(55,38,12,.5)]">
                <div class="flex items-start gap-4 ${isRtl ? 'flex-row-reverse text-right' : ''}"><div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-[rgba(15,90,70,.08)] ring-1 ring-[rgba(15,90,70,.12)]">${avatar}</div>
                <div class="flex-1 space-y-1"><div class="inline-flex rounded-full bg-[rgba(182,132,47,.12)] px-3 py-1 text-[10px] font-extrabold uppercase tracking-[0.16em] text-[color:var(--market-brass)]">${lang === 'ar' ? 'وكالة موثوقة' : 'Trusted agency'}</div><h3 class="pt-1 text-xl font-extrabold tracking-[-0.03em] text-slate-900 line-clamp-1">${t.name}</h3><p class="text-sm leading-6 text-slate-500 line-clamp-2">${desc}</p></div></div>
                <div class="mt-5 flex items-center justify-between border-t border-[rgba(130,94,38,.12)] pt-5 ${isRtl ? 'flex-row-reverse text-right' : ''}">
                    <div class="text-center"><div class="text-lg font-extrabold text-slate-800">${t.stats?.units_count ?? 0}</div><div class="mt-0.5 text-[10px] font-extrabold uppercase tracking-[0.16em] text-slate-400">${TX.properties}</div></div>
                    <div class="text-center"><div class="text-lg font-extrabold text-[color:var(--market-palm)]">${t.stats?.active_units_count ?? t.stats?.units_count ?? 0}</div><div class="mt-0.5 text-[10px] font-extrabold uppercase tracking-[0.16em] text-slate-400">${lang === 'ar' ? 'نشطة' : 'Active'}</div></div>
                    <span class="inline-flex items-center rounded-full px-4 py-2 text-[11px] font-extrabold uppercase tracking-[0.16em] text-white" style="background:linear-gradient(135deg,var(--market-palm),var(--market-river))">${lang === 'ar' ? 'زيارة' : 'Visit'}</span>
                </div></a>`;
        }).join('');
    }

    function renderScrollUnits(containerId, units) {
        const c = document.getElementById(containerId);
        if (!c) return;
        if (!units?.length) { c.innerHTML = `<div class="text-sm text-slate-400 py-8">${TX.no_results}</div>`; return; }
        c.innerHTML = units.map(scrollCardHtml).join('');
    }

    function renderGridUnits(containerId, units) {
        const c = document.getElementById(containerId);
        if (!c) return;
        if (!units?.length) { c.innerHTML = `<div class="col-span-full text-center text-sm text-slate-400 py-8">${TX.no_results}</div>`; return; }
        c.innerHTML = units.map(unitCardHtml).join('');
    }

    function renderCatalog(units, meta) {
        const c = document.getElementById('catalog-results');
        const countEl = document.getElementById('catalog-count');
        const pagEl = document.getElementById('catalog-pagination');
        if (!c) return;
        if (countEl && meta) countEl.textContent = `${lang === 'ar' ? 'عرض' : 'Showing'} ${units.length} ${lang === 'ar' ? 'من' : 'of'} ${fmt.format(meta.total)} ${lang === 'ar' ? 'عقار' : 'properties'}`;
        if (!units?.length) {
            c.innerHTML = `<div class="col-span-full rounded-[28px] border border-dashed border-[rgba(130,94,38,.28)] bg-[rgba(255,252,246,.75)] px-6 py-14 text-center text-slate-500"><div class="mb-2 text-xl font-extrabold tracking-[-0.02em] text-slate-900">${TX.no_results}</div><p class="text-sm leading-7">${TX.no_results_hint}</p></div>`;
            if (pagEl) pagEl.innerHTML = '';
            return;
        }
        c.innerHTML = units.map(unitCardHtml).join('');
        if (pagEl && meta && meta.last_page > 1) {
            let html = '';
            for (let i = 1; i <= meta.last_page; i++) {
                html += `<button type="button" class="page-btn inline-flex h-11 w-11 items-center justify-center rounded-2xl text-sm font-extrabold transition ${i === meta.current_page ? 'text-white shadow-[0_18px_30px_-18px_rgba(15,90,70,.8)]' : 'bg-[rgba(255,252,246,.92)] text-slate-700 ring-1 ring-[rgba(130,94,38,.16)] hover:bg-white'}" ${i === meta.current_page ? 'style="background:linear-gradient(135deg,var(--market-palm),var(--market-river))"' : ''} data-page="${i}">${i}</button>`;
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
            renderGridUnits('web-featured', json.featured_units ?? []);
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
