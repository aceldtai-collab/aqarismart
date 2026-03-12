<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale()==='ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Register') }} &mdash; RentoJo</title>
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

        /* Showcase */
        .auth-showcase{background:linear-gradient(155deg,#041c28 0%,#074860 40%,#053347 100%);position:relative;overflow:hidden}
        .auth-showcase::before{content:'';position:absolute;top:-20%;right:-10%;width:50%;height:50%;background:radial-gradient(circle,rgba(255,41,41,.12) 0%,transparent 65%);pointer-events:none}
        .auth-showcase::after{content:'';position:absolute;bottom:-20%;left:-8%;width:45%;height:45%;background:radial-gradient(circle,rgba(60,156,63,.08) 0%,transparent 65%);pointer-events:none}

        /* Inputs */
        .r-input{display:block;width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.8125rem;background:#fff;transition:all .2s;color:var(--dark)}
        .r-input:focus{outline:none;border-color:var(--brand);box-shadow:0 0 0 3px rgba(var(--brand-rgb),.1)}
        .r-input::placeholder{color:#94a3b8}
        .r-input.has-icon{padding-left:36px}
        html[dir="rtl"] .r-input.has-icon{padding-left:12px;padding-right:36px}

        /* Buttons */
        .r-btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:10px 20px;border-radius:8px;font-size:.8125rem;font-weight:600;cursor:pointer;transition:all .2s;border:none;position:relative;overflow:hidden}
        .r-btn:disabled{opacity:.65;cursor:not-allowed;transform:none!important}
        .r-btn-primary{background:var(--brand);color:#fff;box-shadow:0 2px 8px rgba(var(--brand-rgb),.3)}
        .r-btn-primary:hover:not(:disabled){box-shadow:0 4px 16px rgba(var(--brand-rgb),.4);transform:translateY(-1px)}
        .r-btn-outline{background:transparent;color:var(--dark);border:1.5px solid #e2e8f0}
        .r-btn-outline:hover{border-color:#cbd5e1;background:#f8fafc}
        .r-btn .shimmer{position:absolute;top:0;left:0;width:100%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,.12),transparent);animation:shimmer-slide 3s ease-in-out infinite}

        /* Screenshot */
        .screenshot-frame{border-radius:14px;overflow:hidden;box-shadow:0 20px 50px rgba(0,0,0,.4),0 0 0 1px rgba(255,255,255,.06)}
        .screenshot-frame img{display:block;width:100%;height:auto}
        .screenshot-frame .browser-bar{height:28px;background:rgba(255,255,255,.06);display:flex;align-items:center;padding:0 12px;gap:6px}
        .screenshot-frame .browser-dot{width:8px;height:8px;border-radius:50%;background:rgba(255,255,255,.12)}
        .stat-float{background:rgba(255,255,255,.08);backdrop-filter:blur(16px);border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:12px 16px;position:absolute;z-index:10}

        /* Wizard step indicator */
        .wizard-step{display:flex;flex-direction:column;align-items:center;gap:6px;flex:1}
        .wizard-dot{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;transition:all .3s;border:2px solid #e2e8f0;background:#fff;color:#94a3b8}
        .wizard-dot.active{border-color:var(--brand);background:var(--brand);color:#fff;box-shadow:0 2px 8px rgba(var(--brand-rgb),.25)}
        .wizard-dot.done{border-color:var(--brand);background:var(--brand);color:#fff}
        .wizard-line{flex:1;height:2px;background:#e2e8f0;transition:background .3s;margin:0 -2px}
        .wizard-line.active{background:var(--brand)}
        .pw-bar{height:3px;border-radius:2px;transition:all .3s}

        /* Animations */
        @keyframes float-y{0%,100%{transform:translateY(0)}50%{transform:translateY(-8px)}}
        @keyframes fade-up{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
        @keyframes particle-drift{0%{transform:translateY(0) translateX(0);opacity:0}10%{opacity:.5}90%{opacity:.5}100%{transform:translateY(-100vh) translateX(20px);opacity:0}}
        @keyframes shake{0%,100%{transform:translateX(0)}15%,45%,75%{transform:translateX(-4px)}30%,60%,90%{transform:translateX(4px)}}
        @keyframes shimmer-slide{0%{transform:translateX(-100%)}100%{transform:translateX(100%)}}

        .float-y{animation:float-y 4s ease-in-out infinite}
        .float-y-d{animation:float-y 4s ease-in-out 1.5s infinite}
        .fade-up{animation:fade-up .5s ease-out both}
        .fade-up-1{animation:fade-up .5s ease-out .08s both}
        .fade-up-2{animation:fade-up .5s ease-out .16s both}
        .fade-up-3{animation:fade-up .5s ease-out .24s both}
        .shake-it{animation:shake .45s ease-in-out}
        .auth-particle{position:absolute;width:2px;height:2px;border-radius:50%;background:rgba(255,255,255,.25);animation:particle-drift linear infinite;pointer-events:none}
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

                <div class="fade-up relative z-10">
                    <a href="{{ url('/') }}" class="flex items-center gap-3 mb-10">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:var(--brand)">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/></svg>
                        </div>
                        <span class="text-xl font-bold tracking-tight">RentoJo</span>
                    </a>
                    <h1 class="text-[26px] xl:text-[30px] font-extrabold leading-[1.2] tracking-tight mb-3">
                        {{ $isAr ? 'ابدأ بإدارة عقاراتك اليوم' : 'Start managing your properties today' }}
                    </h1>
                    <p class="text-[14px] text-white/50 leading-relaxed max-w-sm mb-8">
                        {{ $isAr ? 'أنشئ مساحة عملك في أقل من دقيقتين. بدون بطاقة ائتمان.' : 'Set up your workspace in under 2 minutes. No credit card required.' }}
                    </p>
                    <div class="space-y-0.5 mb-6 fade-up-1">
                        @php
                            $regFeatures = $isAr
                                ? ['تجربة مجانية 14 يوم','بدون بطاقة ائتمان','إعداد في دقيقتين','دعم عربي وإنجليزي']
                                : ['14-day free trial','No credit card required','Setup in 2 minutes','Arabic & English support'];
                        @endphp
                        @foreach($regFeatures as $feat)
                            <div class="feat-item">
                                <div class="feat-dot"></div>
                                <span class="text-[13px] text-white/55 font-medium">{{ $feat }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

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

                <div class="fade-up-3 relative z-10">
                    <div class="flex items-center gap-5 mb-5">
                        <div class="flex -space-x-2 rtl:space-x-reverse">
                            @for($i = 0; $i < 4; $i++)
                                <div class="w-7 h-7 rounded-full border-2 border-[#074860] bg-gradient-to-br {{ ['from-red-400 to-red-600','from-emerald-400 to-emerald-600','from-amber-400 to-amber-600','from-sky-400 to-sky-600'][$i] }} flex items-center justify-center text-[9px] font-bold text-white">{{ ['S','R','A','M'][$i] }}</div>
                            @endfor
                        </div>
                        <div>
                            <div class="flex gap-0.5 mb-0.5">@for($s = 0; $s < 5; $s++)<svg class="w-3 h-3 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>@endfor</div>
                            <p class="text-[11px] text-white/30">{{ $isAr ? 'موثوق من 500+ فريق عقاري' : 'Trusted by 500+ property teams' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-5 border-t border-white/[.06] text-[12px] text-white/20">
                        <span>&copy; {{ date('Y') }} RentoJo</span>
                        <div class="flex gap-4"><a href="#" class="hover:text-white/40 transition">{{ __('Privacy') }}</a><a href="#" class="hover:text-white/40 transition">{{ __('Terms') }}</a></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right — Wizard Form --}}
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
                <div class="w-full max-w-[400px] fade-up-1">
                    @php
                        $stepFromErrors = 1;
                        $fieldSteps = [1 => ['name'], 2 => ['email','phone'], 3 => ['agent','subdomain'], 4 => ['password','password_confirmation']];
                        foreach ($fieldSteps as $sn => $flds) { foreach ($flds as $f) { if ($errors->has($f)) $stepFromErrors = $sn; } }
                        $stepLabels = $isAr ? ['الأساسيات','التواصل','المساحة','الأمان'] : ['Basics','Contact','Workspace','Security'];
                    @endphp

                    <div class="mb-6">
                        <h2 class="text-[22px] font-extrabold tracking-tight leading-tight" style="color:var(--dark)">{{ __('Create your account') }}</h2>
                        <p class="text-[14px] text-slate-500 mt-1">{{ __('Set up your property management workspace') }}</p>
                    </div>

                    <form method="POST" action="{{ route('register') }}" x-data="{
                        step: {{ $stepFromErrors }},
                        totalSteps: 4,
                        loading: false,
                        showPw: false,
                        pw: '',
                        hasError: {{ $errors->any() ? 'true' : 'false' }},
                        next() { if (this.step < this.totalSteps) this.step++ },
                        prev() { if (this.step > 1) this.step-- },
                        get pwStrength() {
                            let s = 0;
                            if (this.pw.length >= 8) s++;
                            if (/[A-Z]/.test(this.pw)) s++;
                            if (/[0-9]/.test(this.pw)) s++;
                            if (/[^A-Za-z0-9]/.test(this.pw)) s++;
                            return s;
                        },
                        get pwLabel() {
                            const labels = {!! $isAr ? "['ضعيفة','متوسطة','جيدة','قوية']" : "['Weak','Fair','Good','Strong']" !!};
                            return this.pw.length ? labels[Math.max(0, this.pwStrength - 1)] || labels[0] : '';
                        },
                        get pwColor() {
                            return ['bg-red-400','bg-amber-400','bg-emerald-400','bg-emerald-500'][Math.max(0, this.pwStrength - 1)] || 'bg-slate-200';
                        }
                    }" x-on:submit="loading = true" :class="hasError && 'shake-it'" x-init="if(hasError) setTimeout(() => hasError = false, 500)">
                        @csrf

                        {{-- Wizard step indicator --}}
                        <div class="flex items-start mb-8">
                            <template x-for="i in totalSteps" :key="i">
                                <div class="flex items-start" :class="i < totalSteps ? 'flex-1' : ''">
                                    <div class="wizard-step">
                                        <div class="wizard-dot" :class="{ 'active': step === i, 'done': step > i }">
                                            <template x-if="step > i">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                            </template>
                                            <template x-if="step <= i"><span x-text="i"></span></template>
                                        </div>
                                        <span class="text-[10px] font-medium text-center leading-tight transition-colors" :class="step >= i ? 'text-slate-700' : 'text-slate-400'" x-text="{{ json_encode($stepLabels) }}[i-1]"></span>
                                    </div>
                                    <div x-show="i < totalSteps" class="wizard-line mt-4" :class="step > i && 'active'"></div>
                                </div>
                            </template>
                        </div>

                        {{-- Step 1: Basics --}}
                        <div x-show="step === 1" x-cloak x-transition:enter="transition ease-out duration-250" x-transition:enter-start="opacity-0 translate-x-3" x-transition:enter-end="opacity-100 translate-x-0">
                            <h3 class="text-sm font-bold mb-4" style="color:var(--dark)">{{ __('Account Basics') }}</h3>
                            <div>
                                <label for="name" class="block text-[13px] font-semibold mb-1.5" style="color:var(--dark)">{{ __('Full Name') }}</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 ltr:left-0 rtl:right-0 ltr:pl-3 rtl:pr-3 flex items-center pointer-events-none">
                                        <svg class="w-[17px] h-[17px] text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                    </div>
                                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="r-input has-icon" placeholder="{{ __('Enter your full name') }}" />
                                </div>
                                <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
                            </div>
                            <div class="mt-5">
                                <button type="button" x-on:click="next()" class="r-btn r-btn-primary w-full">
                                    {{ __('Continue') }}
                                    <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                                    <div class="shimmer"></div>
                                </button>
                            </div>
                        </div>

                        {{-- Step 2: Contact --}}
                        <div x-show="step === 2" x-cloak x-transition:enter="transition ease-out duration-250" x-transition:enter-start="opacity-0 translate-x-3" x-transition:enter-end="opacity-100 translate-x-0">
                            <h3 class="text-sm font-bold mb-4" style="color:var(--dark)">{{ __('Contact Information') }}</h3>
                            <div class="space-y-4">
                                <div>
                                    <label for="email" class="block text-[13px] font-semibold mb-1.5" style="color:var(--dark)">{{ __('Email Address') }}</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 ltr:left-0 rtl:right-0 ltr:pl-3 rtl:pr-3 flex items-center pointer-events-none">
                                            <svg class="w-[17px] h-[17px] text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                                        </div>
                                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="r-input has-icon" placeholder="{{ __('you@company.com') }}" />
                                    </div>
                                    <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                                </div>
                                <div>
                                    <label for="phone" class="block text-[13px] font-semibold mb-1.5" style="color:var(--dark)">{{ __('Phone Number') }} <span class="text-slate-400 font-normal">({{ __('optional') }})</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 ltr:left-0 rtl:right-0 ltr:pl-3 rtl:pr-3 flex items-center pointer-events-none">
                                            <svg class="w-[17px] h-[17px] text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                                        </div>
                                        <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel" class="r-input has-icon" placeholder="{{ __('+962 7X XXX XXXX') }}" />
                                    </div>
                                    <x-input-error :messages="$errors->get('phone')" class="mt-1.5" />
                                </div>
                            </div>
                            <div class="mt-5 flex gap-3">
                                <button type="button" x-on:click="prev()" class="r-btn r-btn-outline">{{ __('Back') }}</button>
                                <button type="button" x-on:click="next()" class="r-btn r-btn-primary flex-1">
                                    {{ __('Continue') }}
                                    <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                                </button>
                            </div>
                        </div>

                        {{-- Step 3: Workspace --}}
                        <div x-show="step === 3" x-cloak x-transition:enter="transition ease-out duration-250" x-transition:enter-start="opacity-0 translate-x-3" x-transition:enter-end="opacity-100 translate-x-0">
                            <h3 class="text-sm font-bold mb-4" style="color:var(--dark)">{{ __('Workspace Setup') }}</h3>
                            <div class="space-y-4">
                                <div>
                                    <label for="agent" class="block text-[13px] font-semibold mb-1.5" style="color:var(--dark)">{{ __('Organization Name') }}</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 ltr:left-0 rtl:right-0 ltr:pl-3 rtl:pr-3 flex items-center pointer-events-none">
                                            <svg class="w-[17px] h-[17px] text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                                        </div>
                                        <input id="agent" type="text" name="agent" value="{{ old('agent') }}" class="r-input has-icon" placeholder="{{ __('Acme Properties') }}" />
                                    </div>
                                    <x-input-error :messages="$errors->get('agent')" class="mt-1.5" />
                                </div>
                                <div>
                                    <label for="subdomain" class="block text-[13px] font-semibold mb-1.5" style="color:var(--dark)">{{ __('Subdomain') }}</label>
                                    <div class="flex items-center gap-2">
                                        <div class="relative flex-1">
                                            <div class="absolute inset-y-0 ltr:left-0 rtl:right-0 ltr:pl-3 rtl:pr-3 flex items-center pointer-events-none">
                                                <svg class="w-[17px] h-[17px] text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418"/></svg>
                                            </div>
                                            <input id="subdomain" type="text" name="subdomain" value="{{ old('subdomain') }}" class="r-input has-icon" placeholder="acme" />
                                        </div>
                                        <span class="text-sm text-slate-400 font-medium whitespace-nowrap px-3 py-2.5 bg-slate-50 rounded-lg border border-slate-200">.{{ config('tenancy.base_domain') }}</span>
                                    </div>
                                    <x-input-error :messages="$errors->get('subdomain')" class="mt-1.5" />
                                </div>
                            </div>
                            <div class="mt-5 flex gap-3">
                                <button type="button" x-on:click="prev()" class="r-btn r-btn-outline">{{ __('Back') }}</button>
                                <button type="button" x-on:click="next()" class="r-btn r-btn-primary flex-1">
                                    {{ __('Continue') }}
                                    <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                                </button>
                            </div>
                        </div>

                        {{-- Step 4: Security --}}
                        <div x-show="step === 4" x-cloak x-transition:enter="transition ease-out duration-250" x-transition:enter-start="opacity-0 translate-x-3" x-transition:enter-end="opacity-100 translate-x-0">
                            <h3 class="text-sm font-bold mb-4" style="color:var(--dark)">{{ __('Set your password') }}</h3>
                            <div class="space-y-4">
                                <div>
                                    <label for="password" class="block text-[13px] font-semibold mb-1.5" style="color:var(--dark)">{{ __('Password') }}</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 ltr:left-0 rtl:right-0 ltr:pl-3 rtl:pr-3 flex items-center pointer-events-none">
                                            <svg class="w-[17px] h-[17px] text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                                        </div>
                                        <input id="password" :type="showPw ? 'text' : 'password'" name="password" required autocomplete="new-password" class="r-input has-icon" style="padding-right:40px" placeholder="{{ __('Min. 8 characters') }}" x-model="pw" />
                                        <button type="button" x-on:click="showPw = !showPw" class="absolute inset-y-0 ltr:right-0 rtl:left-0 ltr:pr-3 rtl:pl-3 flex items-center text-slate-400 hover:text-slate-600 transition-colors" tabindex="-1">
                                            <svg x-show="!showPw" class="w-[17px] h-[17px]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            <svg x-show="showPw" x-cloak class="w-[17px] h-[17px]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                                        </button>
                                    </div>
                                    <div x-show="pw.length > 0" x-cloak class="mt-2">
                                        <div class="flex gap-1.5 mb-1">
                                            <template x-for="bar in 4" :key="bar">
                                                <div class="pw-bar flex-1" :class="pwStrength >= bar ? pwColor : 'bg-slate-200'"></div>
                                            </template>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-[11px] font-medium" :class="pwStrength <= 1 ? 'text-red-500' : pwStrength <= 2 ? 'text-amber-500' : 'text-emerald-500'" x-text="pwLabel"></span>
                                            <div class="flex items-center gap-3 text-[11px] text-slate-400">
                                                <span :class="pw.length >= 8 && 'text-emerald-500 font-medium'">8+ {{ $isAr ? 'أحرف' : 'chars' }}</span>
                                                <span :class="/[A-Z]/.test(pw) && 'text-emerald-500 font-medium'">A-Z</span>
                                                <span :class="/[0-9]/.test(pw) && 'text-emerald-500 font-medium'">0-9</span>
                                            </div>
                                        </div>
                                    </div>
                                    <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                                </div>
                                <div>
                                    <label for="password_confirmation" class="block text-[13px] font-semibold mb-1.5" style="color:var(--dark)">{{ __('Confirm Password') }}</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 ltr:left-0 rtl:right-0 ltr:pl-3 rtl:pr-3 flex items-center pointer-events-none">
                                            <svg class="w-[17px] h-[17px] text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                                        </div>
                                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="r-input has-icon" placeholder="{{ __('Re-enter password') }}" />
                                    </div>
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
                                </div>
                                <label class="flex items-start gap-2.5 cursor-pointer group">
                                    <input id="terms" type="checkbox" name="terms" required class="mt-0.5 rounded border-slate-300 transition" style="color:var(--brand)">
                                    <span class="text-sm text-slate-500 group-hover:text-slate-700 transition-colors">{{ __('I agree to the') }} <a href="#" class="font-medium hover:underline" style="color:var(--brand)">{{ __('Terms') }}</a> {{ __('and') }} <a href="#" class="font-medium hover:underline" style="color:var(--brand)">{{ __('Privacy Policy') }}</a></span>
                                </label>
                            </div>
                            <div class="mt-5 flex gap-3">
                                <button type="button" x-on:click="prev()" class="r-btn r-btn-outline">{{ __('Back') }}</button>
                                <button type="submit" class="r-btn r-btn-primary flex-1" :disabled="loading">
                                    <template x-if="!loading">
                                        <span class="flex items-center gap-2">
                                            {{ __('Create Account') }}
                                            <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                                        </span>
                                    </template>
                                    <template x-if="loading">
                                        <span class="flex items-center gap-2">
                                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                            {{ $isAr ? 'جارٍ الإنشاء...' : 'Creating...' }}
                                        </span>
                                    </template>
                                    <div class="shimmer"></div>
                                </button>
                            </div>
                        </div>

                        <p class="text-center text-sm text-slate-500 mt-5 pt-3">
                            {{ __('Already have an account?') }}
                            <a href="{{ route('login') }}" class="font-semibold ltr:ml-1 rtl:mr-1 hover:underline transition-colors" style="color:var(--brand)">{{ __('Sign in') }}</a>
                        </p>
                    </form>
                </div>
            </div>

            <div class="lg:hidden px-6 pb-6 text-center">
                <p class="text-xs text-slate-400">&copy; {{ date('Y') }} RentoJo. {{ __('All rights reserved.') }}</p>
            </div>
        </div>
    </div>
</body>
</html>
