<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale()==='ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('RentoJo') }} &mdash; {{ __('Property Management') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @if(app()->getLocale() === 'ar')
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @endif
    <x-vite-assets />
    @php
        $isAr = app()->getLocale() === 'ar';
        $currentLocale = in_array(app()->getLocale(), ['en','ar']) ? app()->getLocale() : 'en';
        $landing = app(\App\Services\PublicLandingService::class)->forPublicDomain();
        $authScreenshot = $landing['assets']['auth_screenshot'] ?? ($landing['assets']['hero_image'] ?? null);
        $primaryColor = config('public_site.primary_color', '#ff2929');
        $langToggleEn = url(request()->path()) . '?lang=en';
        $langToggleAr = url(request()->path()) . '?lang=ar';
    @endphp
    <style>
        [x-cloak]{display:none!important}
        *{box-sizing:border-box}
        :root{--brand:{{ $primaryColor }};--brand-rgb:255,41,41;--dark:#074860}
        body{font-family:'Inter',system-ui,sans-serif;margin:0;-webkit-font-smoothing:antialiased;color:var(--dark)}
        @if($isAr) body{font-family:'Noto Sans Arabic','Inter',system-ui,sans-serif} @endif

        /* Showcase panel */
        .auth-showcase{background:linear-gradient(155deg,#041c28 0%,#074860 40%,#053347 100%);position:relative;overflow:hidden}
        .auth-showcase::before{content:'';position:absolute;top:-20%;right:-10%;width:50%;height:50%;background:radial-gradient(circle,rgba(255,41,41,.12) 0%,transparent 65%);pointer-events:none}
        .auth-showcase::after{content:'';position:absolute;bottom:-20%;left:-8%;width:45%;height:45%;background:radial-gradient(circle,rgba(60,156,63,.08) 0%,transparent 65%);pointer-events:none}

        /* Inputs */
        .auth-input{display:block;width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.8125rem;background:#fff;transition:all .2s;color:var(--dark)}
        .auth-input:focus{outline:none;border-color:var(--brand);box-shadow:0 0 0 3px rgba(var(--brand-rgb),.1)}
        .auth-input::placeholder{color:#94a3b8}
        .auth-input.has-icon-left{padding-left:36px}
        .auth-input.has-icon-right{padding-right:36px}
        html[dir="rtl"] .auth-input.has-icon-left{padding-left:12px;padding-right:36px}
        html[dir="rtl"] .auth-input.has-icon-right{padding-right:12px;padding-left:36px}

        /* Buttons */
        .auth-btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:10px 20px;border:none;border-radius:8px;font-size:.8125rem;font-weight:600;cursor:pointer;transition:all .2s;position:relative;overflow:hidden}
        .auth-btn:disabled{opacity:.65;cursor:not-allowed;transform:none!important}
        .auth-btn-primary{background:var(--brand);color:#fff;box-shadow:0 2px 8px rgba(var(--brand-rgb),.3)}
        .auth-btn-primary:hover:not(:disabled){box-shadow:0 4px 16px rgba(var(--brand-rgb),.4);transform:translateY(-1px)}
        .auth-btn-outline{background:transparent;color:var(--dark);border:1.5px solid #e2e8f0}
        .auth-btn-outline:hover{border-color:#cbd5e1;background:#f8fafc}
        .auth-btn .shimmer{position:absolute;top:0;left:0;width:100%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,.12),transparent);animation:shimmer-slide 3s ease-in-out infinite}

        /* Screenshot */
        .screenshot-frame{border-radius:14px;overflow:hidden;box-shadow:0 20px 50px rgba(0,0,0,.4),0 0 0 1px rgba(255,255,255,.06)}
        .screenshot-frame img{display:block;width:100%;height:auto}
        .screenshot-frame .browser-bar{height:28px;background:rgba(255,255,255,.06);display:flex;align-items:center;padding:0 12px;gap:6px}
        .screenshot-frame .browser-dot{width:8px;height:8px;border-radius:50%;background:rgba(255,255,255,.12)}
        .stat-float{background:rgba(255,255,255,.08);backdrop-filter:blur(16px);border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:12px 16px;position:absolute;z-index:10}

        /* Animations */
        @keyframes float-y{0%,100%{transform:translateY(0)}50%{transform:translateY(-8px)}}
        @keyframes fade-up{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
        @keyframes particle-drift{0%{transform:translateY(0) translateX(0);opacity:0}10%{opacity:.5}90%{opacity:.5}100%{transform:translateY(-100vh) translateX(20px);opacity:0}}
        @keyframes pulse-ring{0%{transform:scale(.85);opacity:.5}50%{transform:scale(1);opacity:1}100%{transform:scale(.85);opacity:.5}}
        @keyframes shake{0%,100%{transform:translateX(0)}15%,45%,75%{transform:translateX(-4px)}30%,60%,90%{transform:translateX(4px)}}
        @keyframes shimmer-slide{0%{transform:translateX(-100%)}100%{transform:translateX(100%)}}
        @keyframes fade-in-up{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}

        .float-y{animation:float-y 4s ease-in-out infinite}
        .float-y-d{animation:float-y 4s ease-in-out 1.5s infinite}
        .fade-up{animation:fade-up .5s ease-out both}
        .fade-up-1{animation:fade-up .5s ease-out .08s both}
        .fade-up-2{animation:fade-up .5s ease-out .16s both}
        .fade-up-3{animation:fade-up .5s ease-out .24s both}
        .shake-it{animation:shake .45s ease-in-out}
        .auth-particle{position:absolute;width:2px;height:2px;border-radius:50%;background:rgba(255,255,255,.25);animation:particle-drift linear infinite;pointer-events:none}

        /* Feature list */
        .feat-item{display:flex;align-items:center;gap:10px;padding:6px 0}
        .feat-dot{width:6px;height:6px;border-radius:50%;background:var(--brand);flex-shrink:0;opacity:.7}

        @media(max-width:1023px){.showcase-wrap{display:none}}
    </style>
</head>
<body class="antialiased bg-white">
    <div class="flex min-h-screen">
        {{-- Left — Showcase --}}
        <div class="showcase-wrap lg:flex lg:w-[50%] xl:w-[52%] flex-shrink-0">
            <div class="auth-showcase w-full flex flex-col justify-between p-8 xl:p-12 text-white relative">
                @for($p = 0; $p < 8; $p++)
                    <div class="auth-particle" style="left:{{ rand(5,95) }}%;bottom:-5%;animation-duration:{{ rand(10,20) }}s;animation-delay:{{ $p * 1.1 }}s"></div>
                @endfor

                {{-- Logo + copy --}}
                <div class="fade-up relative z-10">
                    <a href="{{ url('/') }}" class="flex items-center gap-3 mb-10">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:var(--brand)">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/></svg>
                        </div>
                        <span class="text-xl font-bold tracking-tight">RentoJo</span>
                    </a>
                    <h1 class="text-[26px] xl:text-[30px] font-extrabold leading-[1.2] tracking-tight mb-3">
                        {{ $isAr ? 'كل ما تحتاجه لإدارة عقاراتك' : 'Everything you need to manage properties' }}
                    </h1>
                    <p class="text-[14px] text-white/50 leading-relaxed max-w-sm mb-8">
                        {{ $isAr ? 'منصة متكاملة للمحافظ والعقود والصيانة والفوترة.' : 'Portfolios, leases, maintenance & billing — one dashboard.' }}
                    </p>
                    <div class="space-y-0.5 mb-6 fade-up-1">
                        @php
                            $features = $isAr
                                ? ['إدارة العقود والإيجارات','طلبات الصيانة الذكية','فواتير وتحصيل آلي','تقارير ولوحة تحكم']
                                : ['Lease & contract management','Smart maintenance requests','Automated billing & collection','Advanced reports & dashboard'];
                        @endphp
                        @foreach($features as $feat)
                            <div class="feat-item">
                                <div class="feat-dot"></div>
                                <span class="text-[13px] text-white/55 font-medium">{{ $feat }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Screenshot --}}
                <div class="relative flex-1 flex items-center justify-center my-4 fade-up-2">
                    <div class="relative w-full max-w-md">
                        <div class="stat-float float-y -top-3 ltr:-left-2 rtl:-right-2 hidden xl:block">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:rgba(255,41,41,.15)">
                                    <svg class="w-4 h-4" style="color:var(--brand)" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/></svg>
                                </div>
                                <div>
                                    <div class="text-[10px] text-white/35">{{ $isAr ? 'نسبة الإشغال' : 'Occupancy' }}</div>
                                    <div class="text-base font-bold">94.2%</div>
                                </div>
                            </div>
                        </div>
                        <div class="stat-float float-y-d -bottom-3 ltr:-right-2 rtl:-left-2 hidden xl:block">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-emerald-500/15 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75"/></svg>
                                </div>
                                <div>
                                    <div class="text-[10px] text-white/35">{{ $isAr ? 'وحدات مُدارة' : 'Units' }}</div>
                                    <div class="text-base font-bold">1,247</div>
                                </div>
                            </div>
                        </div>
                        <div class="screenshot-frame">
                            <div class="browser-bar"><div class="browser-dot"></div><div class="browser-dot"></div><div class="browser-dot"></div></div>
                            @if($authScreenshot)
                                <img src="{{ $authScreenshot }}" alt="RentoJo Dashboard" loading="eager">
                            @else
                                <div class="w-full aspect-[16/10] bg-gradient-to-br from-[#053347] to-[#041c28] flex items-center justify-center">
                                    <div class="text-center px-6">
                                        <div class="w-14 h-14 mx-auto mb-3 rounded-xl flex items-center justify-center" style="background:rgba(255,41,41,.1)">
                                            <svg class="w-7 h-7" style="color:var(--brand)" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5"/></svg>
                                        </div>
                                        <p class="text-white/25 text-sm">{{ $isAr ? 'لوحة تحكم RentoJo' : 'RentoJo Dashboard' }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Social proof --}}
                <div class="fade-up-3 relative z-10">
                    <div class="flex items-center gap-5 mb-5">
                        <div class="flex -space-x-2 rtl:space-x-reverse">
                            @for($i = 0; $i < 4; $i++)
                                <div class="w-7 h-7 rounded-full border-2 border-[#074860] bg-gradient-to-br {{ ['from-red-400 to-red-600','from-emerald-400 to-emerald-600','from-amber-400 to-amber-600','from-sky-400 to-sky-600'][$i] }} flex items-center justify-center text-[9px] font-bold text-white">
                                    {{ ['S','R','A','M'][$i] }}
                                </div>
                            @endfor
                        </div>
                        <div>
                            <div class="flex gap-0.5 mb-0.5">
                                @for($s = 0; $s < 5; $s++)<svg class="w-3 h-3 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>@endfor
                            </div>
                            <p class="text-[11px] text-white/30">{{ $isAr ? 'موثوق من 500+ فريق عقاري' : 'Trusted by 500+ property teams' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-5 border-t border-white/[.06] text-[12px] text-white/20">
                        <span>&copy; {{ date('Y') }} RentoJo</span>
                        <div class="flex gap-4">
                            <a href="#" class="hover:text-white/40 transition">{{ __('Privacy') }}</a>
                            <a href="#" class="hover:text-white/40 transition">{{ __('Terms') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right — Form --}}
        <div class="flex-1 flex flex-col min-h-screen">
            <div class="flex items-center justify-between px-6 sm:px-10 py-5 fade-up">
                <a href="{{ url('/') }}" class="flex items-center gap-2 lg:hidden">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:var(--brand)">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/></svg>
                    </div>
                    <span class="font-bold text-lg" style="color:var(--dark)">RentoJo</span>
                </a>
                <div class="flex items-center gap-0.5 rounded-lg border border-slate-200 p-0.5 text-xs font-medium ltr:ml-auto rtl:mr-auto">
                    <a href="{{ $langToggleEn }}" class="px-3 py-1.5 rounded-md transition {{ $currentLocale==='en' ? 'text-white shadow-sm' : 'text-slate-500 hover:bg-slate-50' }}" @if($currentLocale==='en') style="background:var(--brand)" @endif>EN</a>
                    <a href="{{ $langToggleAr }}" class="px-3 py-1.5 rounded-md transition {{ $currentLocale==='ar' ? 'text-white shadow-sm' : 'text-slate-500 hover:bg-slate-50' }}" @if($currentLocale==='ar') style="background:var(--brand)" @endif>ع</a>
                </div>
            </div>
            <div class="flex-1 flex items-center justify-center px-6 sm:px-10 pb-10">
                <div class="w-full max-w-[380px] fade-up-1">
                    {{ $slot }}
                </div>
            </div>
            <div class="lg:hidden px-6 pb-6 text-center">
                <p class="text-xs text-slate-400">&copy; {{ date('Y') }} RentoJo. {{ __('All rights reserved.') }}</p>
            </div>
        </div>
    </div>
</body>
</html>
