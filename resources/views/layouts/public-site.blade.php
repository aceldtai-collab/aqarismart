@php
    $loc = app()->getLocale();
    $isAr = $loc === 'ar';
    $nav = $landing['navigation'] ?? [];
    $hero = $landing['hero'] ?? [];
    $feat = $landing['features'] ?? [];
    $pricing = $landing['pricing'] ?? [];
    $testimonials = $landing['testimonials'] ?? [];
    $cta = $landing['cta'] ?? [];
    $footer = $landing['footer'] ?? [];
    $seo = $landing['seo'] ?? [];
    $video = $landing['video'] ?? [];
    $assets = $landing['assets'] ?? [];
    $meta = $landing['meta'] ?? [];

    $langParam = config('locales.cookie_name', 'lang');
    $urlEn = request()->fullUrlWithQuery([$langParam => 'en']);
    $urlAr = request()->fullUrlWithQuery([$langParam => 'ar']);
@endphp
<!DOCTYPE html>
<html lang="{{ $loc }}" dir="{{ $isAr ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $seo['title'] ?? ($meta['title'] ?? 'Aqari Smart') }}</title>
    @if(!empty($seo['description']))<meta name="description" content="{{ $seo['description'] }}">@endif
    @if(!empty($seo['robots']))<meta name="robots" content="{{ $seo['robots'] }}">@endif
    @if(!empty($seo['canonical']))<link rel="canonical" href="{{ $seo['canonical'] }}">@endif
    @if(!empty($seo['og_image']))<meta property="og:image" content="{{ $seo['og_image'] }}">@endif
    @if(!empty($seo['twitter_image']))<meta name="twitter:image" content="{{ $seo['twitter_image'] }}">@endif
    @if(!empty($seo['favicon']))<link rel="icon" href="{{ $seo['favicon'] }}">@endif
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @if($isAr)<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">@endif
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{brand:'#0f172a',accent:'#6366f1'}}}}</script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        *{box-sizing:border-box}
        [x-cloak]{display:none!important}
        body{font-family:'Inter',system-ui,sans-serif;margin:0;-webkit-font-smoothing:antialiased}
        @if($isAr) body{font-family:'Noto Sans Arabic','Inter',system-ui,sans-serif} @endif
        html{scroll-behavior:smooth}
        .hero-bg{background:#08090d;position:relative;overflow:hidden}
        .hero-bg::before{content:'';position:absolute;top:-50%;right:-30%;width:80%;height:80%;background:radial-gradient(circle,rgba(99,102,241,.15) 0%,transparent 60%);pointer-events:none}
        .hero-bg::after{content:'';position:absolute;bottom:-40%;left:-20%;width:70%;height:70%;background:radial-gradient(circle,rgba(168,85,247,.1) 0%,transparent 60%);pointer-events:none}
        .feature-card{transition:all .3s ease;opacity:0;transform:translateY(20px)}
        .feature-card.shown{opacity:1;transform:translateY(0)}
        .feature-card:hover{transform:translateY(-4px);box-shadow:0 20px 40px rgba(0,0,0,.08)}
        .pricing-card{transition:all .3s ease}
        .pricing-card:hover{transform:translateY(-4px)}
        .pricing-card.highlighted{background:#08090d;color:#fff;border-color:transparent}
        .pricing-card.highlighted .price-val{color:#fff}
        .pricing-card.highlighted .plan-cta{background:#6366f1;color:#fff;border-color:#6366f1}
        .pricing-card.highlighted .plan-cta:hover{background:#4f46e5}
        .pricing-card.highlighted .feat-check{color:#818cf8}
        .pricing-card.highlighted .feat-text{color:rgba(255,255,255,.7)}
        .pricing-card.highlighted .plan-unit{color:rgba(255,255,255,.4)}
        /* Shimmer CTA */
        .shimmer-btn{position:relative;overflow:hidden}
        .shimmer-btn::after{content:'';position:absolute;top:0;left:-100%;width:60%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,.25),transparent);animation:shimmer 3s infinite}
        @keyframes shimmer{0%{left:-100%}100%{left:200%}}
        /* Marquee */
        .marquee-track{display:flex;width:max-content;animation:marquee 30s linear infinite}
        .marquee-track:hover{animation-play-state:paused}
        @keyframes marquee{0%{transform:translateX(0)}100%{transform:translateX(-50%)}}
        @if($isAr)
        .marquee-track{animation-name:marquee-rtl}
        @keyframes marquee-rtl{0%{transform:translateX(0)}100%{transform:translateX(50%)}}
        @endif
        /* Carousel */
        .snap-carousel{display:flex;overflow-x:auto;scroll-snap-type:x mandatory;-webkit-overflow-scrolling:touch;scrollbar-width:none}
        .snap-carousel::-webkit-scrollbar{display:none}
        .snap-carousel>*{scroll-snap-align:start;flex-shrink:0}
        /* FAQ */
        .faq-toggle svg{transition:transform .2s}
        .faq-toggle[aria-expanded="true"] svg{transform:rotate(180deg)}
        /* Fade-in utility */
        .fade-up{opacity:0;transform:translateY(24px);transition:opacity .6s ease,transform .6s ease}
        .fade-up.visible{opacity:1;transform:translateY(0)}
        /* Step connector */
        .step-line{position:absolute;top:28px;left:50%;width:calc(100% + 2rem);height:2px;background:linear-gradient(90deg,#e2e8f0 0%,#6366f1 100%)}
        @if($isAr) .step-line{left:auto;right:50%;background:linear-gradient(270deg,#e2e8f0 0%,#6366f1 100%)} @endif
        /* Sticky mobile CTA */
        .mobile-sticky-cta{transform:translateY(100%);transition:transform .3s ease}
        .mobile-sticky-cta.show{transform:translateY(0)}
    </style>
</head>
<body class="antialiased text-slate-900 bg-white">

{{-- ═══════════════ NAVIGATION ═══════════════ --}}
<nav class="fixed top-0 w-full bg-white/80 backdrop-blur-xl border-b border-slate-100 z-50" x-data="{ open: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            {{-- Logo --}}
            <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                @if(!empty($assets['logo_url']))
                    <img src="{{ $assets['logo_url'] }}" alt="Logo" class="h-8">
                @else
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-sm">
                        <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/></svg>
                    </div>
                    <span class="text-lg font-bold text-slate-900 tracking-tight">Aqari Smart</span>
                @endif
            </a>

            {{-- Desktop nav --}}
            <div class="hidden md:flex items-center gap-1">
                @foreach($nav as $item)
                    @if(($item['variant'] ?? '') === 'button-primary')
                        <a href="{{ $item['href'] ?? '#' }}" class="ltr:ml-3 rtl:mr-3 px-5 py-2 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition">{{ $item['label'] ?? '' }}</a>
                    @else
                        <a href="{{ $item['href'] ?? '#' }}" class="px-3 py-2 text-sm text-slate-600 hover:text-slate-900 font-medium transition">{{ $item['label'] ?? '' }}</a>
                    @endif
                @endforeach
                <div class="ltr:ml-2 rtl:mr-2 flex items-center gap-0.5 rounded-md border border-slate-200 p-0.5 text-xs font-medium">
                    <a href="{{ $urlEn }}" class="px-2 py-1 rounded transition {{ !$isAr ? 'bg-slate-900 text-white' : 'text-slate-500 hover:bg-slate-50' }}">EN</a>
                    <a href="{{ $urlAr }}" class="px-2 py-1 rounded transition {{ $isAr ? 'bg-slate-900 text-white' : 'text-slate-500 hover:bg-slate-50' }}">ع</a>
                </div>
            </div>

            {{-- Mobile toggle --}}
            <button @click="open = !open" class="md:hidden p-2 -mr-1 rounded-lg hover:bg-slate-100 transition">
                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
            </button>
        </div>
    </div>
    {{-- Mobile menu --}}
    <div x-show="open" x-cloak x-transition class="md:hidden bg-white border-t border-slate-100">
        <div class="px-4 py-4 space-y-1">
            @foreach($nav as $item)
                <a href="{{ $item['href'] ?? '#' }}" @click="open=false" class="block px-3 py-2.5 text-sm text-slate-700 hover:bg-slate-50 rounded-lg font-medium">{{ $item['label'] ?? '' }}</a>
            @endforeach
            <div class="flex items-center gap-1 pt-2">
                <a href="{{ $urlEn }}" class="px-3 py-1.5 rounded-md text-xs font-medium transition {{ !$isAr ? 'bg-slate-900 text-white' : 'text-slate-500 border border-slate-200' }}">EN</a>
                <a href="{{ $urlAr }}" class="px-3 py-1.5 rounded-md text-xs font-medium transition {{ $isAr ? 'bg-slate-900 text-white' : 'text-slate-500 border border-slate-200' }}">ع</a>
            </div>
        </div>
    </div>
</nav>

{{-- ═══════════════ HERO ═══════════════ --}}
<section id="hero-section" class="hero-bg pt-28 sm:pt-36 pb-20 sm:pb-28 relative" x-data="{ shown: false }" x-intersect.once="shown = true">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 text-center relative z-10">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-white/10 bg-white/5 mb-8 transition-all duration-700" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 -translate-y-4'">
            <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
            <span class="text-xs font-medium text-white/60">{{ $isAr ? 'موثوق من ' . ($tenantsCount ?? 0) . '+ شركة عقارية' : 'Trusted by ' . ($tenantsCount ?? 0) . '+ property companies' }}</span>
        </div>

        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-[1.1] tracking-tight mb-6 transition-all duration-700 delay-100" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'">
            {{ $hero['headline'] ?? '' }}
        </h1>

        <p class="text-lg sm:text-xl text-white/45 max-w-2xl mx-auto leading-relaxed mb-8 transition-all duration-700 delay-200" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'">
            {{ $hero['subheadline'] ?? '' }}
        </p>

        {{-- Star rating --}}
        <div class="flex items-center justify-center gap-2 mb-8 transition-all duration-700 delay-300" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'">
            <div class="flex items-center gap-0.5">
                @for($s = 0; $s < 5; $s++)
                    <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                @endfor
            </div>
            <span class="text-sm font-semibold text-white/70">4.9</span>
            <span class="text-xs text-white/30">{{ $isAr ? '— تقييم مديري العقارات في الأردن' : '— Rated by property managers in Jordan' }}</span>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 mb-10 transition-all duration-700 delay-[400ms]" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'">
            @foreach(($hero['ctas'] ?? []) as $btn)
                @if(($btn['style'] ?? 'primary') === 'primary')
                    <a href="{{ $btn['href'] ?? '#' }}" class="shimmer-btn group w-full sm:w-auto px-8 py-4 bg-white text-slate-900 font-bold text-sm rounded-xl hover:bg-slate-100 transition shadow-lg shadow-white/10 flex items-center justify-center gap-2">
                        {{ $btn['label'] ?? '' }}
                        <svg class="w-4 h-4 rtl:rotate-180 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </a>
                @else
                    <a href="{{ $btn['href'] ?? '#' }}" class="w-full sm:w-auto px-8 py-4 border border-white/15 text-white/70 font-bold text-sm rounded-xl hover:bg-white/5 hover:text-white transition flex items-center justify-center">
                        {{ $btn['label'] ?? '' }}
                    </a>
                @endif
            @endforeach
        </div>

        <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-sm text-white/30 transition-all duration-700 delay-500" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'">
            <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>{{ $isAr ? 'بدون بطاقة ائتمان' : 'No credit card' }}</span>
            <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>{{ $isAr ? '14 يوم تجربة مجانية' : '14-day free trial' }}</span>
            <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>{{ $isAr ? 'إلغاء في أي وقت' : 'Cancel anytime' }}</span>
        </div>
    </div>
</section>

{{-- ═══════════════ STATS ═══════════════ --}}
<section class="py-16 border-b border-slate-100">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            @php
                $statsData = [
                    ['target' => max(($tenantsCount ?? 0) * 10, 500), 'suffix' => '+', 'label' => $isAr ? 'عميل نشط' : 'Active Clients'],
                    ['target' => max(($unitsCount ?? 0), 100), 'suffix' => '+', 'label' => $isAr ? 'وحدة مُدارة' : 'Units Managed'],
                    ['target' => 99, 'suffix' => '%', 'label' => $isAr ? 'رضا العملاء' : 'Client Satisfaction'],
                    ['target' => 0, 'suffix' => '', 'static' => '24/7', 'label' => $isAr ? 'دعم فني' : 'Support'],
                ];
            @endphp
            @foreach($statsData as $si => $s)
                <div x-data="{ current: 0, target: {{ $s['target'] }}, started: false }"
                     x-intersect.once="if(!started){ started=true; let dur=2000, start=performance.now(), t={{ $s['target'] }}; function step(now){ let p=Math.min((now-start)/dur,1); current=Math.floor(p*t); if(p<1) requestAnimationFrame(step); else current=t; } requestAnimationFrame(step); }"
                     class="transition-all duration-500" :class="started ? 'opacity-100 scale-100' : 'opacity-0 scale-90'">
                    @if(!empty($s['static']))
                        <div class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">{{ $s['static'] }}</div>
                    @else
                        <div class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                            <span x-text="current.toLocaleString()">0</span><span>{{ $s['suffix'] }}</span>
                        </div>
                    @endif
                    <div class="text-sm text-slate-500 mt-1 font-medium">{{ $s['label'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════ FEATURES INTRO ═══════════════ --}}
<section id="features" class="py-20 sm:py-28">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-4">{{ $feat['intro']['headline'] ?? '' }}</h2>
            <p class="text-lg text-slate-500 max-w-2xl mx-auto">{{ $feat['intro']['description'] ?? '' }}</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
                $iconGradients = ['from-indigo-500 to-indigo-600','from-emerald-500 to-emerald-600','from-amber-500 to-amber-600','from-rose-500 to-rose-600','from-sky-500 to-sky-600','from-violet-500 to-violet-600'];
                $featureIcons = [
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>',
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>',
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>',
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17l-5.1-5.1a2.121 2.121 0 113-3l5.1 5.1m0 0l5.1-5.1a2.121 2.121 0 113 3l-5.1 5.1m-5.1 0L3 22.5m8.42-7.33L22.5 22.5"/>',
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>',
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418"/>',
                ];
            @endphp
            @foreach(($feat['intro']['items'] ?? []) as $i => $item)
                <div class="feature-card p-6 rounded-2xl border border-slate-100 bg-white"
                     x-data="{ show: false }"
                     x-intersect.once="setTimeout(() => show = true, {{ $i * 100 }})"
                     :class="show && 'shown'">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br {{ $iconGradients[$i % count($iconGradients)] }} flex items-center justify-center mb-4 shadow-sm">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $featureIcons[$i % count($featureIcons)] !!}</svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mb-2">{{ $item['title'] ?? '' }}</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">{{ $item['body'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════ FEATURE COLUMNS ═══════════════ --}}
@if(!empty($feat['columns']))
<section class="py-20 sm:py-28 bg-slate-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="grid md:grid-cols-2 gap-12 lg:gap-20">
            @foreach(($feat['columns'] ?? []) as $col)
                <div>
                    <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight mb-8">{{ $col['title'] ?? '' }}</h3>
                    <div class="space-y-6">
                        @foreach(($col['items'] ?? []) as $ci)
                            <div class="flex gap-4">
                                <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-slate-900">{{ $ci['title'] ?? '' }}</div>
                                    <div class="text-sm text-slate-500 mt-0.5 leading-relaxed">{{ $ci['body'] ?? '' }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═══════════════ HOW IT WORKS ═══════════════ --}}
<section class="py-20 sm:py-28" x-data="{ shown: false }" x-intersect.once="shown = true">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-16">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 text-indigo-600 text-xs font-semibold mb-4">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>
                {{ $isAr ? 'كيف يعمل' : 'How It Works' }}
            </div>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-4">{{ $isAr ? 'ابدأ في 3 خطوات بسيطة' : 'Get started in 3 simple steps' }}</h2>
            <p class="text-lg text-slate-500 max-w-2xl mx-auto">{{ $isAr ? 'من التسجيل إلى إدارة عقاراتك بالكامل — في دقائق وليس أسابيع' : 'From sign-up to managing your entire portfolio — in minutes, not weeks' }}</p>
        </div>
        <div class="grid md:grid-cols-3 gap-8 relative">
            @php
                $steps = [
                    ['num' => '01', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>', 'title' => $isAr ? 'أنشئ حسابك' : 'Create Your Account', 'desc' => $isAr ? 'سجّل مجاناً في 60 ثانية — بدون بطاقة ائتمان' : 'Sign up free in 60 seconds — no credit card needed', 'color' => 'from-indigo-500 to-indigo-600'],
                    ['num' => '02', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/>', 'title' => $isAr ? 'أضف عقاراتك' : 'Set Up Properties', 'desc' => $isAr ? 'أضف عقاراتك ووحداتك أو استوردها بسهولة' : 'Add your properties and units or import them easily', 'color' => 'from-emerald-500 to-emerald-600'],
                    ['num' => '03', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.58-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>', 'title' => $isAr ? 'انطلق!' : 'Go Live!', 'desc' => $isAr ? 'المستأجرون، الفوترة، الصيانة — كل شيء يعمل تلقائياً' : 'Tenants, billing, maintenance — all automated', 'color' => 'from-amber-500 to-orange-600'],
                ];
            @endphp
            @foreach($steps as $si => $step)
                <div class="relative text-center transition-all duration-700 delay-{{ $si * 200 }}" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'">
                    @if($si < 2)
                        <div class="hidden md:block step-line"></div>
                    @endif
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br {{ $step['color'] }} flex items-center justify-center mx-auto mb-5 shadow-lg relative z-10">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">{!! $step['icon'] !!}</svg>
                    </div>
                    <div class="text-xs font-bold text-indigo-500 mb-2">{{ $step['num'] }}</div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">{{ $step['title'] }}</h3>
                    <p class="text-sm text-slate-500 leading-relaxed max-w-xs mx-auto">{{ $step['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════ VIDEO ═══════════════ --}}
@if(!empty($video['youtube_url']))
@php
    preg_match('/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([^&?\s]+)/', $video['youtube_url'], $ytMatch);
    $ytId = $ytMatch[1] ?? null;
@endphp
@if($ytId)
<section id="video" class="py-20 sm:py-28">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-12">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-4">{{ $video['headline'] ?? '' }}</h2>
            <p class="text-lg text-slate-500 max-w-2xl mx-auto">{{ $video['description'] ?? '' }}</p>
        </div>
        <div class="relative rounded-2xl overflow-hidden shadow-2xl shadow-slate-900/10 border border-slate-100 aspect-video" x-data="{ playing: false }">
            @if(!empty($video['poster_image']) && !false)
                <img x-show="!playing" src="{{ $video['poster_image'] }}" alt="" class="absolute inset-0 w-full h-full object-cover">
                <button x-show="!playing" @click="playing = true" class="absolute inset-0 flex items-center justify-center z-10 group">
                    <div class="w-20 h-20 rounded-full bg-white/90 backdrop-blur flex items-center justify-center shadow-xl group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8 text-indigo-600 ltr:ml-1 rtl:mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </div>
                </button>
            @endif
            <iframe x-show="playing || {{ empty($video['poster_image']) ? 'true' : 'false' }}"
                    x-bind:src="playing ? 'https://www.youtube.com/embed/{{ $ytId }}?autoplay=1&rel=0' : 'https://www.youtube.com/embed/{{ $ytId }}?rel=0'"
                    class="absolute inset-0 w-full h-full" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen loading="lazy"></iframe>
        </div>
    </div>
</section>
@endif
@endif

{{-- ═══════════════ SCREENSHOTS ═══════════════ --}}
@php $gallery = $assets['feature_images']['gallery'] ?? []; @endphp
@if(!empty($gallery))
<section id="screenshots" class="py-20 sm:py-28">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-14">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-4">{{ $feat['screenshots']['headline'] ?? ($isAr ? 'نظّم عملياتك بفاعلية' : 'Bring calm to your operations') }}</h2>
            <p class="text-lg text-slate-500 max-w-2xl mx-auto">{{ $feat['screenshots']['description'] ?? '' }}</p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($gallery as $img)
                <div class="rounded-2xl overflow-hidden border border-slate-100 shadow-sm hover:shadow-lg transition">
                    <img src="{{ $img }}" alt="" class="w-full h-52 object-cover" loading="lazy">
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═══════════════ SUBSCRIBED TENANTS ═══════════════ --}}
@if(isset($subscribedTenants) && $subscribedTenants->count())
<section id="tenants" class="py-20 sm:py-28 bg-slate-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-14">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 text-indigo-600 text-xs font-semibold mb-4">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75"/></svg>
                {{ $isAr ? 'شركاؤنا' : 'Our Partners' }}
            </div>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-4">{{ $isAr ? 'شركات عقارية تثق بنا' : 'Trusted by leading property companies' }}</h2>
            <p class="text-lg text-slate-500 max-w-2xl mx-auto">{{ $isAr ? 'انضم إلى مئات الشركات العقارية التي تدير عقاراتها عبر Aqari Smart' : 'Join hundreds of property companies managing their portfolios through Aqari Smart' }}</p>
        </div>
        @php
            $tenantCards = $subscribedTenants->map(function($st, $idx) use ($isAr) {
                $tenantSettings = $st->settings ?? [];
                $tenantLogo = $tenantSettings['logo'] ?? null;
                $initials = collect(explode(' ', $st->name))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                $colors = ['from-indigo-500 to-purple-600','from-emerald-500 to-teal-600','from-amber-500 to-orange-600','from-rose-500 to-pink-600','from-sky-500 to-blue-600','from-violet-500 to-fuchsia-600','from-lime-500 to-green-600','from-cyan-500 to-teal-600'];
                return (object)['name' => $st->name, 'slug' => $st->slug, 'logo' => $tenantLogo, 'initials' => $initials, 'color' => $colors[$idx % count($colors)], 'url' => app(\App\Services\Tenancy\TenantManager::class)->tenantUrl($st)];
            });
        @endphp
        @if($subscribedTenants->count() >= 4)
            <div class="overflow-hidden">
                <div class="marquee-track">
                    @for($r = 0; $r < 2; $r++)
                        @foreach($tenantCards as $tc)
                            <a href="{{ $tc->url }}" target="_blank" class="group flex flex-col items-center gap-3 p-5 mx-2 rounded-2xl bg-white border border-slate-100 hover:border-indigo-200 hover:shadow-lg transition-all duration-200 w-48 flex-shrink-0">
                                @if($tc->logo)
                                    <img src="{{ $tc->logo }}" alt="{{ $tc->name }}" class="w-14 h-14 rounded-xl object-contain border border-slate-100 bg-white">
                                @else
                                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br {{ $tc->color }} flex items-center justify-center text-white text-lg font-bold shadow-sm">{{ $tc->initials }}</div>
                                @endif
                                <div class="text-center">
                                    <div class="text-sm font-semibold text-slate-900 group-hover:text-indigo-600 transition-colors truncate w-full">{{ $tc->name }}</div>
                                    <div class="text-xs text-slate-400 mt-0.5">{{ $tc->slug }}.{{ config('tenancy.base_domain') }}</div>
                                </div>
                            </a>
                        @endforeach
                    @endfor
                </div>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 max-w-2xl mx-auto">
                @foreach($tenantCards as $tc)
                    <a href="{{ $tc->url }}" target="_blank" class="group flex flex-col items-center gap-3 p-5 rounded-2xl bg-white border border-slate-100 hover:border-indigo-200 hover:shadow-lg transition-all duration-200">
                        @if($tc->logo)
                            <img src="{{ $tc->logo }}" alt="{{ $tc->name }}" class="w-14 h-14 rounded-xl object-contain border border-slate-100 bg-white">
                        @else
                            <div class="w-14 h-14 rounded-xl bg-gradient-to-br {{ $tc->color }} flex items-center justify-center text-white text-lg font-bold shadow-sm">{{ $tc->initials }}</div>
                        @endif
                        <div class="text-center">
                            <div class="text-sm font-semibold text-slate-900 group-hover:text-indigo-600 transition-colors">{{ $tc->name }}</div>
                            <div class="text-xs text-slate-400 mt-0.5">{{ $tc->slug }}.{{ config('tenancy.base_domain') }}</div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endif

{{-- ═══════════════ FEATURED UNITS ═══════════════ --}}
@if(isset($featuredUnits) && $featuredUnits->count())
<section id="units" class="py-20 sm:py-28">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-14">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 text-xs font-semibold mb-4">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205l3 1m1.5.5l-1.5-.5M6.75 7.364V3h-3v18m3-13.636l10.5-3.819"/></svg>
                    {{ $isAr ? 'وحدات متاحة' : 'Available Units' }}
                </div>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-3">{{ $isAr ? 'استكشف العقارات المتاحة' : 'Explore available properties' }}</h2>
                <p class="text-lg text-slate-500 max-w-xl">{{ $isAr ? 'تصفّح أحدث الوحدات المعروضة من شركائنا العقاريين' : 'Browse the latest listings from our property partners' }}</p>
            </div>
            <a href="{{ route('public.search') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 text-white text-sm font-semibold shadow-md shadow-indigo-500/20 hover:shadow-lg hover:shadow-indigo-500/30 hover:-translate-y-0.5 transition-all whitespace-nowrap">
                {{ $isAr ? 'استكشف المزيد' : 'Explore More' }}
                <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
            </a>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($featuredUnits as $fu)
                @php
                    $photo = ($fu->photos && count($fu->photos)) ? $fu->photos[0] : null;
                    $unitTitle = $fu->translated_title ?: ($fu->code ?? '—');
                    $cityName = $fu->city ? ($isAr ? ($fu->city->name_ar ?: $fu->city->name_en) : $fu->city->name_en) : null;
                    $subName = $fu->subcategory?->name;
                    $tenantUrl = app(\App\Services\Tenancy\TenantManager::class)->tenantUrl($fu->tenant, '/listings/' . $fu->code);
                @endphp
                <a href="{{ $tenantUrl }}" target="_blank" class="group rounded-2xl border border-slate-100 bg-white overflow-hidden hover:shadow-xl hover:shadow-slate-200/50 hover:-translate-y-1 transition-all duration-300">
                    <div class="relative aspect-[4/3] bg-slate-100 overflow-hidden">
                        @if($photo)
                            <img src="{{ $photo }}" alt="{{ $unitTitle }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-slate-50 to-slate-100">
                                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75"/></svg>
                            </div>
                        @endif
                        @if($fu->listing_type)
                            <span class="absolute top-3 ltr:left-3 rtl:right-3 px-2.5 py-1 rounded-lg text-[11px] font-bold uppercase tracking-wider {{ $fu->listing_type === 'sale' ? 'bg-amber-500 text-white' : 'bg-emerald-500 text-white' }}">
                                {{ $fu->listing_type === 'sale' ? ($isAr ? 'للبيع' : 'Sale') : ($isAr ? 'للإيجار' : 'Rent') }}
                            </span>
                        @endif
                        @if($fu->created_at && $fu->created_at->gt(now()->subDays(7)))
                            <span class="absolute top-3 ltr:right-3 rtl:left-3 px-2 py-0.5 rounded-md text-[10px] font-bold uppercase bg-indigo-500 text-white animate-pulse">{{ $isAr ? 'جديد' : 'New' }}</span>
                        @endif
                    </div>
                    <div class="p-4">
                        <div class="text-sm font-bold text-slate-900 mb-1 line-clamp-1 group-hover:text-indigo-600 transition-colors">{{ $unitTitle }}</div>
                        @if($cityName || $subName)
                            <div class="text-xs text-slate-400 mb-2.5 flex items-center gap-1">
                                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                                {{ collect([$subName, $cityName])->filter()->implode(' · ') }}
                            </div>
                        @endif
                        <div class="flex items-center justify-between">
                            @if($fu->price)
                                <div class="text-base font-extrabold text-indigo-600">{{ number_format($fu->price) }} <span class="text-xs font-medium text-slate-400">{{ $fu->currency ?? 'JOD' }}</span></div>
                            @endif
                            <div class="flex items-center gap-2 text-xs text-slate-400">
                                @if($fu->bedrooms)<span class="flex items-center gap-0.5"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75"/></svg>{{ $fu->bedrooms }}</span>@endif
                                @if($fu->bathrooms)<span class="flex items-center gap-0.5"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $fu->bathrooms }}</span>@endif
                                @if($fu->area_m2)<span>{{ $fu->area_m2 }}m²</span>@endif
                            </div>
                        </div>
                        <div class="mt-2.5 pt-2.5 border-t border-slate-50 flex items-center gap-2">
                            <div class="w-5 h-5 rounded bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-[8px] font-bold text-white flex-shrink-0">{{ mb_substr($fu->tenant->name ?? '', 0, 1) }}</div>
                            <span class="text-[11px] text-slate-400 truncate">{{ $fu->tenant->name ?? '' }}</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="text-center mt-10">
            <a href="{{ route('public.search') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-700 transition">
                {{ $isAr ? 'عرض جميع الوحدات المتاحة' : 'View all available units' }}
                <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
            </a>
        </div>
    </div>
</section>
@endif

{{-- ═══════════════ TRUST & SECURITY ═══════════════ --}}
<section class="py-10 bg-slate-50 border-y border-slate-100">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">
        <div class="flex flex-wrap items-center justify-center gap-x-10 gap-y-4">
            @php
                $trustItems = [
                    ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>', 'label' => $isAr ? 'اتصال مشفّر SSL' : 'SSL Encrypted'],
                    ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>', 'label' => $isAr ? 'حماية البيانات' : 'Data Protection'],
                    ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>', 'label' => $isAr ? 'دفع آمن' : 'Secure Payments'],
                    ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5a17.92 17.92 0 01-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418"/>', 'label' => $isAr ? 'مقرّنا الأردن' : 'Jordan-Based'],
                ];
            @endphp
            @foreach($trustItems as $ti)
                <div class="flex items-center gap-2 text-sm text-slate-500">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">{!! $ti['icon'] !!}</svg>
                    <span class="font-medium">{{ $ti['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════ PRICING ═══════════════ --}}
@if(!empty($pricing['plans']))
<section id="pricing" class="py-20 sm:py-28 bg-slate-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-4">{{ $pricing['headline'] ?? '' }}</h2>
            <p class="text-lg text-slate-500 max-w-xl mx-auto">{{ $pricing['subheadline'] ?? '' }}</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-{{ min(count($pricing['plans']), 3) }} gap-6 max-w-4xl mx-auto">
            @foreach(($pricing['plans'] ?? []) as $plan)
                <div class="pricing-card rounded-2xl border border-slate-200 bg-white p-7 flex flex-col {{ !empty($plan['highlighted']) ? 'highlighted ring-1 ring-indigo-500/20' : '' }}">
                    @if(!empty($plan['highlighted']))
                        <div class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-400 uppercase tracking-wider mb-3">
                            <span class="w-1.5 h-1.5 bg-indigo-400 rounded-full animate-pulse"></span>
                            {{ $isAr ? 'الأكثر شعبية' : 'Most Popular' }}
                        </div>
                    @endif
                    <h3 class="text-lg font-bold mb-1">{{ $plan['name'] ?? '' }}</h3>
                    <div class="flex items-baseline gap-1 mb-5">
                        <span class="text-3xl font-extrabold price-val text-slate-900">{{ $plan['price'] ?? '' }}</span>
                        @if(!empty($plan['unit']))<span class="text-sm plan-unit text-slate-400">{{ $plan['unit'] }}</span>@endif
                    </div>
                    <ul class="space-y-3 mb-7 flex-1">
                        @foreach(($plan['features'] ?? []) as $f)
                            <li class="flex items-start gap-2.5 text-sm">
                                <svg class="w-4 h-4 feat-check text-indigo-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                <span class="feat-text text-slate-600">{{ is_array($f) ? ($f[$loc] ?? ($f['en'] ?? '')) : $f }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ $plan['cta']['href'] ?? '#' }}" class="plan-cta block text-center px-5 py-3 rounded-xl text-sm font-semibold border border-slate-200 text-slate-700 hover:bg-slate-50 transition">
                        {{ $plan['cta']['label'] ?? '' }}
                    </a>
                </div>
            @endforeach
        </div>
        <div class="text-center mt-8">
            <div class="inline-flex items-center gap-2 text-sm text-slate-400">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                {{ $isAr ? 'ضمان استرداد الأموال خلال 14 يوماً — بدون أسئلة' : '14-day money-back guarantee — no questions asked' }}
            </div>
        </div>
    </div>
</section>
@endif

{{-- ═══════════════ TESTIMONIALS ═══════════════ --}}
@if(!empty($testimonials['items']))
@php $tItems = $testimonials['items'] ?? []; $tCount = count($tItems); @endphp
<section id="testimonials" class="py-20 sm:py-28">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-14">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">{{ $testimonials['headline'] ?? '' }}</h2>
        </div>
        <div x-data="{
                active: 0,
                total: {{ $tCount }},
                interval: null,
                startAuto() { this.interval = setInterval(() => this.next(), 5000) },
                stopAuto() { clearInterval(this.interval) },
                next() { this.active = (this.active + 1) % this.total },
                prev() { this.active = (this.active - 1 + this.total) % this.total },
             }"
             x-init="startAuto()"
             @mouseenter="stopAuto()" @mouseleave="startAuto()"
             class="relative">
            {{-- Cards --}}
            <div class="overflow-hidden">
                <div class="flex transition-transform duration-500 ease-in-out" :style="'transform: translateX(' + (active * ({{ $isAr ? '' : '-' }}100)) + '%)'">
                    @foreach($tItems as $ti => $t)
                        @php
                            $authorName = $t['author'] ?? '';
                            $authorParts = explode(',', $authorName);
                            $authorInitials = collect(explode(' ', trim($authorParts[0])))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                            $avatarColors = ['from-indigo-500 to-purple-600','from-emerald-500 to-teal-600','from-amber-500 to-orange-600','from-rose-500 to-pink-600'];
                        @endphp
                        <div class="w-full flex-shrink-0 px-2">
                            <div class="max-w-2xl mx-auto text-center p-8 sm:p-10 rounded-2xl border border-slate-100 bg-white">
                                <svg class="w-10 h-10 text-indigo-100 mx-auto mb-6" fill="currentColor" viewBox="0 0 24 24"><path d="M4.583 17.321C3.553 16.227 3 15 3 13.011c0-3.5 2.457-6.637 6.03-8.188l.893 1.378c-3.335 1.804-3.987 4.145-4.247 5.621.537-.278 1.24-.375 1.929-.311 1.804.167 3.226 1.648 3.226 3.489a3.5 3.5 0 01-3.5 3.5c-1.073 0-2.099-.49-2.748-1.179zm10 0C13.553 16.227 13 15 13 13.011c0-3.5 2.457-6.637 6.03-8.188l.893 1.378c-3.335 1.804-3.987 4.145-4.247 5.621.537-.278 1.24-.375 1.929-.311 1.804.167 3.226 1.648 3.226 3.489a3.5 3.5 0 01-3.5 3.5c-1.073 0-2.099-.49-2.748-1.179z"/></svg>
                                <p class="text-base sm:text-lg text-slate-600 leading-relaxed mb-6 italic">"{{ $t['quote'] ?? '' }}"</p>
                                <div class="flex items-center justify-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br {{ $avatarColors[$ti % count($avatarColors)] }} flex items-center justify-center text-white text-sm font-bold">{{ $authorInitials }}</div>
                                    <div class="text-start">
                                        <div class="text-sm font-bold text-slate-900">{{ trim($authorParts[0] ?? '') }}</div>
                                        @if(!empty($authorParts[1]))<div class="text-xs text-slate-400">{{ trim($authorParts[1]) }}</div>@endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            {{-- Navigation --}}
            @if($tCount > 1)
                <button @click="prev()" class="absolute top-1/2 -translate-y-1/2 ltr:-left-2 rtl:-right-2 sm:ltr:-left-5 sm:rtl:-right-5 w-10 h-10 rounded-full bg-white border border-slate-200 shadow-sm flex items-center justify-center hover:bg-slate-50 transition z-10">
                    <svg class="w-4 h-4 text-slate-600 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                </button>
                <button @click="next()" class="absolute top-1/2 -translate-y-1/2 ltr:-right-2 rtl:-left-2 sm:ltr:-right-5 sm:rtl:-left-5 w-10 h-10 rounded-full bg-white border border-slate-200 shadow-sm flex items-center justify-center hover:bg-slate-50 transition z-10">
                    <svg class="w-4 h-4 text-slate-600 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                </button>
                <div class="flex items-center justify-center gap-2 mt-6">
                    @for($d = 0; $d < $tCount; $d++)
                        <button @click="active = {{ $d }}" class="w-2 h-2 rounded-full transition-all" :class="active === {{ $d }} ? 'bg-indigo-500 w-6' : 'bg-slate-300'"></button>
                    @endfor
                </div>
            @endif
        </div>
    </div>
</section>
@endif

{{-- ═══════════════ FAQ ═══════════════ --}}
<section id="faq" class="py-20 sm:py-28 bg-slate-50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-14">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 text-indigo-600 text-xs font-semibold mb-4">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/></svg>
                {{ $isAr ? 'أسئلة شائعة' : 'FAQ' }}
            </div>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-4">{{ $isAr ? 'أسئلة متكررة' : 'Frequently asked questions' }}</h2>
            <p class="text-lg text-slate-500">{{ $isAr ? 'كل ما تحتاج معرفته عن Aqari Smart' : 'Everything you need to know about Aqari Smart' }}</p>
        </div>
        @php
            $faqs = [
                ['q' => $isAr ? 'هل يمكنني تجربة المنصة مجاناً؟' : 'Can I try the platform for free?', 'a' => $isAr ? 'نعم! نقدم تجربة مجانية لمدة 14 يوماً بدون الحاجة لبطاقة ائتمان. يمكنك استكشاف جميع المزايا قبل الاشتراك.' : 'Yes! We offer a 14-day free trial with no credit card required. Explore all features before committing.'],
                ['q' => $isAr ? 'هل يدعم Aqari Smart اللغة العربية بالكامل؟' : 'Does Aqari Smart fully support Arabic?', 'a' => $isAr ? 'بالتأكيد. المنصة بالكامل تدعم العربية والإنجليزية مع واجهة RTL كاملة لتجربة مستخدم مثالية.' : 'Absolutely. The entire platform supports both Arabic and English with full RTL interface for an optimal user experience.'],
                ['q' => $isAr ? 'كم عدد الوحدات التي يمكنني إدارتها؟' : 'How many units can I manage?', 'a' => $isAr ? 'يعتمد ذلك على خطتك. تبدأ الخطة الأساسية بـ 50 وحدة، والاحترافية بـ 200 وحدة، وخطة الشركات تدعم وحدات غير محدودة.' : 'It depends on your plan. Starter supports up to 50 units, Professional up to 200, and Enterprise offers unlimited units.'],
                ['q' => $isAr ? 'هل بياناتي آمنة؟' : 'Is my data secure?', 'a' => $isAr ? 'نعم. نستخدم تشفير SSL، ونسخ احتياطية يومية، وعزل كامل للبيانات بين المستأجرين لضمان أعلى مستويات الأمان.' : 'Yes. We use SSL encryption, daily backups, and complete data isolation between tenants to ensure the highest security standards.'],
                ['q' => $isAr ? 'هل يمكنني ترقية أو تغيير خطتي لاحقاً؟' : 'Can I upgrade or change my plan later?', 'a' => $isAr ? 'بالطبع! يمكنك الترقية أو تغيير خطتك في أي وقت من لوحة التحكم. التغييرات تسري فوراً.' : 'Of course! You can upgrade or change your plan anytime from your dashboard. Changes take effect immediately.'],
                ['q' => $isAr ? 'هل تقدمون دعماً فنياً؟' : 'Do you offer technical support?', 'a' => $isAr ? 'نعم، نقدم دعماً فنياً على مدار الساعة عبر البريد الإلكتروني، ودعم أولوية للخطط الاحترافية وخطط الشركات.' : 'Yes, we provide 24/7 email support, with priority support for Professional and Enterprise plans.'],
            ];
        @endphp
        <div class="space-y-3" x-data="{ openFaq: null }">
            @foreach($faqs as $fi => $faq)
                <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
                    <button @click="openFaq = openFaq === {{ $fi }} ? null : {{ $fi }}" class="faq-toggle w-full flex items-center justify-between gap-4 p-5 text-start" :aria-expanded="openFaq === {{ $fi }}">
                        <span class="text-sm font-bold text-slate-900">{{ $faq['q'] }}</span>
                        <svg class="w-5 h-5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                    </button>
                    <div x-show="openFaq === {{ $fi }}" x-collapse x-cloak class="px-5 pb-5">
                        <p class="text-sm text-slate-500 leading-relaxed">{{ $faq['a'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════ CTA ═══════════════ --}}
<section id="cta" class="hero-bg py-20 sm:py-28 relative">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        @for($p = 0; $p < 20; $p++)
            <div class="absolute w-1 h-1 bg-white/10 rounded-full" style="top:{{ rand(5,95) }}%;left:{{ rand(5,95) }}%;animation:float {{ rand(3,8) }}s ease-in-out infinite alternate;animation-delay:{{ $p * 0.3 }}s"></div>
        @endfor
    </div>
    <style>@keyframes float{0%{transform:translateY(0) scale(1);opacity:.3}100%{transform:translateY(-20px) scale(1.5);opacity:.1}}</style>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 text-center relative z-10">
        <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight mb-4">{{ $isAr ? 'ابدأ رحلتك معنا اليوم' : 'Ready to get started?' }}</h2>
        <p class="text-lg text-white/40 mb-6 max-w-lg mx-auto">{{ $isAr ? 'جرّب Aqari Smart مجاناً لمدة 14 يوماً. بدون بطاقة ائتمان.' : 'Try Aqari Smart free for 14 days. No credit card required.' }}</p>
        <div class="inline-flex items-center gap-2 text-sm text-white/30 mb-10">
            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/></svg>
            {{ $isAr ? 'انضم إلى ' . ($tenantsCount ?? 0) . '+ شركة عقارية' : 'Join ' . ($tenantsCount ?? 0) . '+ property companies' }}
        </div>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            @if(!empty($cta['primary']['label']))
                <a href="{{ $cta['primary']['href'] ?? '#' }}" class="shimmer-btn w-full sm:w-auto px-8 py-4 bg-white text-slate-900 font-bold text-sm rounded-xl hover:bg-slate-100 transition shadow-lg shadow-white/10">{{ $cta['primary']['label'] }}</a>
            @endif
            @if(!empty($cta['secondary']['label']))
                <a href="{{ $cta['secondary']['href'] ?? '#' }}" class="w-full sm:w-auto px-8 py-4 border border-white/15 text-white/70 font-bold text-sm rounded-xl hover:bg-white/5 hover:text-white transition">{{ $cta['secondary']['label'] }}</a>
            @endif
        </div>
    </div>
</section>

{{-- ═══════════════ FOOTER ═══════════════ --}}
<footer class="bg-slate-900 text-white pt-16 pb-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-10 mb-12">
            {{-- Brand --}}
            <div class="col-span-2 md:col-span-1">
                <div class="flex items-center gap-2.5 mb-4">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                        <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/></svg>
                    </div>
                    <span class="text-lg font-bold">Aqari Smart</span>
                </div>
                <p class="text-sm text-slate-400 leading-relaxed mb-4">{{ $isAr ? 'منصة إدارة العقارات الأذكى في الأردن. أدِر عقاراتك بكفاءة وسهولة.' : 'The smartest property management platform in Jordan. Manage your properties efficiently.' }}</p>
                <div class="flex items-center gap-3">
                    <a href="#" class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center hover:bg-white/20 transition" aria-label="Twitter">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                    <a href="#" class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center hover:bg-white/20 transition" aria-label="LinkedIn">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    </a>
                    <a href="#" class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center hover:bg-white/20 transition" aria-label="Instagram">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                </div>
            </div>
            {{-- Product --}}
            <div>
                <h4 class="text-sm font-bold mb-4">{{ $isAr ? 'المنتج' : 'Product' }}</h4>
                <ul class="space-y-2.5">
                    <li><a href="#features" class="text-sm text-slate-400 hover:text-white transition">{{ $isAr ? 'المزايا' : 'Features' }}</a></li>
                    <li><a href="#pricing" class="text-sm text-slate-400 hover:text-white transition">{{ $isAr ? 'الأسعار' : 'Pricing' }}</a></li>
                    <li><a href="{{ route('public.search') }}" class="text-sm text-slate-400 hover:text-white transition">{{ $isAr ? 'استكشف الوحدات' : 'Explore Units' }}</a></li>
                    <li><a href="#testimonials" class="text-sm text-slate-400 hover:text-white transition">{{ $isAr ? 'آراء العملاء' : 'Testimonials' }}</a></li>
                </ul>
            </div>
            {{-- Company --}}
            <div>
                <h4 class="text-sm font-bold mb-4">{{ $isAr ? 'الشركة' : 'Company' }}</h4>
                <ul class="space-y-2.5">
                    @foreach(($footer['links'] ?? []) as $link)
                        <li><a href="{{ $link['href'] ?? '#' }}" class="text-sm text-slate-400 hover:text-white transition">{{ $link['label'] ?? '' }}</a></li>
                    @endforeach
                    <li><a href="#faq" class="text-sm text-slate-400 hover:text-white transition">{{ $isAr ? 'الأسئلة الشائعة' : 'FAQ' }}</a></li>
                </ul>
            </div>
            {{-- Contact --}}
            <div>
                <h4 class="text-sm font-bold mb-4">{{ $isAr ? 'تواصل معنا' : 'Contact' }}</h4>
                <ul class="space-y-2.5">
                    <li class="flex items-center gap-2 text-sm text-slate-400">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                        support@aqarismart.com
                    </li>
                    <li class="flex items-center gap-2 text-sm text-slate-400">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                        {{ $isAr ? 'عمّان، الأردن' : 'Amman, Jordan' }}
                    </li>
                </ul>
            </div>
        </div>
        <div class="border-t border-white/10 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-sm text-slate-500">{{ $footer['copyright'] ?? ('© ' . date('Y') . ' Aqari Smart. ' . ($isAr ? 'جميع الحقوق محفوظة.' : 'All rights reserved.')) }}</div>
            <div class="flex items-center gap-2 text-sm text-slate-500">
                <span>{{ $isAr ? 'صُنع بفخر في الأردن' : 'Proudly made in Jordan' }}</span>
                <span class="text-base">&#x1F1EF;&#x1F1F4;</span>
            </div>
        </div>
    </div>
</footer>

{{-- ═══════════════ EXIT-INTENT POPUP ═══════════════ --}}
<div x-data="{
        show: false,
        dismissed: false,
        init() {
            if(localStorage.getItem('aqari_smart_exit_dismissed')) return;
            document.addEventListener('mouseleave', (e) => {
                if(e.clientY < 5 && !this.dismissed && !this.show) { this.show = true; }
            });
        },
        close() { this.show = false; this.dismissed = true; localStorage.setItem('aqari_smart_exit_dismissed', '1'); }
     }"
     x-show="show" x-cloak
     class="fixed inset-0 z-[100] flex items-center justify-center p-4"
     @keydown.escape.window="close()">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="close()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 text-center" x-transition>
        <button @click="close()" class="absolute top-4 ltr:right-4 rtl:left-4 w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center hover:bg-slate-200 transition">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center mx-auto mb-5">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 109.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1114.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
        </div>
        <h3 class="text-xl font-extrabold text-slate-900 mb-2">{{ $isAr ? 'انتظر! عرض خاص لك' : 'Wait! Special offer for you' }}</h3>
        <p class="text-sm text-slate-500 mb-6">{{ $isAr ? 'احصل على خصم 20% على أول 3 أشهر عند التسجيل اليوم' : 'Get 20% off your first 3 months when you sign up today' }}</p>
        <a href="#cta" @click="close()" class="shimmer-btn inline-flex items-center justify-center gap-2 w-full px-6 py-3.5 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-bold text-sm shadow-lg shadow-indigo-500/25 hover:shadow-xl transition">
            {{ $isAr ? 'ابدأ الآن بالخصم' : 'Claim Your Discount' }}
            <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
        </a>
        <p class="text-xs text-slate-400 mt-3">{{ $isAr ? 'بدون بطاقة ائتمان. إلغاء في أي وقت.' : 'No credit card required. Cancel anytime.' }}</p>
    </div>
</div>

{{-- ═══════════════ MOBILE STICKY CTA ═══════════════ --}}
<div x-data="{ visible: false }"
     x-init="window.addEventListener('scroll', () => { visible = window.scrollY > 600 })"
     class="fixed bottom-0 left-0 right-0 z-50 sm:hidden mobile-sticky-cta bg-white border-t border-slate-200 px-4 py-3 shadow-lg"
     :class="visible && 'show'">
    <a href="#cta" class="flex items-center justify-center gap-2 w-full px-6 py-3 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-bold text-sm shadow-md">
        {{ $isAr ? 'ابدأ مجاناً' : 'Start Free Trial' }}
        <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
    </a>
</div>

{{-- ═══════════════ SEO STRUCTURED DATA ═══════════════ --}}
@php
    $faqsSchema = [
        ['q' => $isAr ? 'هل يمكنني تجربة المنصة مجاناً؟' : 'Can I try the platform for free?', 'a' => $isAr ? 'نعم! نقدم تجربة مجانية لمدة 14 يوماً بدون الحاجة لبطاقة ائتمان. يمكنك استكشاف جميع المزايا قبل الاشتراك.' : 'Yes! We offer a 14-day free trial with no credit card required. Explore all features before committing.'],
        ['q' => $isAr ? 'هل يدعم Aqari Smart اللغة العربية بالكامل؟' : 'Does Aqari Smart fully support Arabic?', 'a' => $isAr ? 'بالتأكيد. المنصة بالكامل تدعم العربية والإنجليزية مع واجهة RTL كاملة لتجربة مستخدم مثالية.' : 'Absolutely. The entire platform supports both Arabic and English with full RTL interface for an optimal user experience.'],
        ['q' => $isAr ? 'كم عدد الوحدات التي يمكنني إدارتها؟' : 'How many units can I manage?', 'a' => $isAr ? 'يعتمد ذلك على خطتك. تبدأ الخطة الأساسية بـ 50 وحدة، والاحترافية بـ 200 وحدة، وخطة الشركات تدعم وحدات غير محدودة.' : 'It depends on your plan. Starter supports up to 50 units, Professional up to 200, and Enterprise offers unlimited units.'],
        ['q' => $isAr ? 'هل بياناتي آمنة؟' : 'Is my data secure?', 'a' => $isAr ? 'نعم. نستخدم تشفير SSL، ونسخ احتياطية يومية، وعزل كامل للبيانات بين المستأجرين لضمان أعلى مستويات الأمان.' : 'Yes. We use SSL encryption, daily backups, and complete data isolation between tenants to ensure the highest security standards.'],
        ['q' => $isAr ? 'هل يمكنني ترقية أو تغيير خطتي لاحقاً؟' : 'Can I upgrade or change my plan later?', 'a' => $isAr ? 'بالطبع! يمكنك الترقية أو تغيير خطتك في أي وقت من لوحة التحكم. التغييرات تسري فوراً.' : 'Of course! You can upgrade or change your plan anytime from your dashboard. Changes take effect immediately.'],
        ['q' => $isAr ? 'هل تقدمون دعماً فنياً؟' : 'Do you offer technical support?', 'a' => $isAr ? 'نعم، نقدم دعماً فنياً على مدار الساعة عبر البريد الإلكتروني، ودعم أولوية للخطط الاحترافية وخطط الشركات.' : 'Yes, we provide 24/7 email support, with priority support for Professional and Enterprise plans.'],
    ];
@endphp
@php
    $orgSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => 'Aqari Smart',
        'url' => url('/'),
        'description' => $seo['description'] ?? 'Professional property management platform',
        'address' => ['@type' => 'PostalAddress', 'addressLocality' => 'Amman', 'addressCountry' => 'JO'],
        'sameAs' => [],
    ];
    $siteSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => 'Aqari Smart',
        'url' => url('/'),
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => url('/explore') . '?q={search_term_string}',
            'query-input' => 'required name=search_term_string',
        ],
    ];
    $faqItems = [];
    foreach($faqsSchema as $faq) {
        $faqItems[] = ['@type' => 'Question', 'name' => $faq['q'], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['a']]];
    }
    $faqSchema = ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $faqItems];
@endphp
<script type="application/ld+json">{!! json_encode($orgSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}</script>
<script type="application/ld+json">{!! json_encode($siteSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}</script>
<script type="application/ld+json">{!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}</script>

</body>
</html>
