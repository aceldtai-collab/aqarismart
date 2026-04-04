<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $isAr = app()->getLocale() === 'ar';
        $routeName = Route::currentRouteName();
        $langParam = config('locales.cookie_name', 'lang');
        $urlEn = request()->fullUrlWithQuery([$langParam => 'en']);
        $urlAr = request()->fullUrlWithQuery([$langParam => 'ar']);
        $scheme = request()->getScheme() ?: 'http';
        $port = request()->getPort();
        $defaultPort = $scheme === 'https' ? 443 : 80;
        $portPart = $port && $port !== $defaultPort ? ':' . $port : '';
        $centralMarketplaceUrl = sprintf('%s://%s%s/marketplace', $scheme, config('tenancy.base_domain'), $portPart);
        $landing = app(\App\Services\PublicLandingService::class)->forPublicDomain();
        $authScreenshot = $landing['assets']['auth_screenshot'] ?? ($landing['assets']['hero_image'] ?? null);
        $authContext = match ($routeName) {
            'register' => [
                'title' => $isAr ? 'أنشئ حسابك وابدأ واجهتك العامة' : 'Create your account and launch your storefront',
                'lead' => $isAr ? 'افتح مساحة عملك، جهّز وكالتك، وابدأ رحلتك من نفس اللغة البصرية التي يراها عملاؤك في السوق العام.' : 'Open your workspace, set up your agency, and start from the same public design language your clients already see in the marketplace.',
                'eyebrow' => $isAr ? 'بوابة الانطلاق' : 'Launch Point',
                'kicker' => $isAr ? 'إنشاء مساحة العمل' : 'Create Workspace',
                'features' => $isAr
                    ? ['واجهة عامة متناسقة مع السوق', 'لوحة تحكم لإدارة العقارات', 'جاهز للعربية والإنجليزية']
                    : ['Public storefront aligned with the marketplace', 'Operational dashboard for property management', 'Ready for Arabic and English'],
                'chips' => $isAr
                    ? [['label' => 'الواجهة', 'value' => 'Storefront'], ['label' => 'التدفق', 'value' => 'Guided setup'], ['label' => 'النتيجة', 'value' => 'Tenant-ready']]
                    : [['label' => 'Surface', 'value' => 'Storefront'], ['label' => 'Flow', 'value' => 'Guided setup'], ['label' => 'Outcome', 'value' => 'Tenant-ready']],
                'meta_title' => $isAr ? 'إنشاء حساب' : 'Create Account',
            ],
            'password.request' => [
                'title' => $isAr ? 'استعد الوصول إلى حسابك' : 'Recover access to your account',
                'lead' => $isAr ? 'أرسل رابط إعادة التعيين ثم ارجع مباشرة إلى نفس الرحلة داخل السوق والواجهة العامة.' : 'Send a reset link, then return directly to the same marketplace and storefront journey.',
                'eyebrow' => $isAr ? 'استعادة الوصول' : 'Recover Access',
                'kicker' => $isAr ? 'إعادة ضبط كلمة المرور' : 'Reset Password',
                'features' => $isAr
                    ? ['إرسال رابط آمن للبريد الإلكتروني', 'تصميم موحّد مع صفحات السوق', 'عودة سريعة إلى لوحة التحكم']
                    : ['Secure email reset link', 'Unified look with public marketplace pages', 'Fast return to your dashboard'],
                'chips' => $isAr
                    ? [['label' => 'الأمان', 'value' => 'Secure link'], ['label' => 'الرحلة', 'value' => 'Continuous'], ['label' => 'الخطوة', 'value' => 'Email reset']]
                    : [['label' => 'Security', 'value' => 'Secure link'], ['label' => 'Journey', 'value' => 'Continuous'], ['label' => 'Step', 'value' => 'Email reset']],
                'meta_title' => $isAr ? 'إعادة تعيين كلمة المرور' : 'Reset Password',
            ],
            'password.reset' => [
                'title' => $isAr ? 'اضبط كلمة مرور جديدة' : 'Set a new password',
                'lead' => $isAr ? 'أكمل استعادة الحساب ثم عد إلى متابعة السوق وواجهتك بثقة.' : 'Finish account recovery, then return to the marketplace and your storefront with a fresh session.',
                'eyebrow' => $isAr ? 'تأمين الحساب' : 'Secure Account',
                'kicker' => $isAr ? 'كلمة مرور جديدة' : 'New Password',
                'features' => $isAr
                    ? ['استعادة وصول سريعة', 'واجهة موحّدة', 'متابعة مباشرة للرحلة']
                    : ['Fast access recovery', 'Unified public shell', 'Direct continuation of the journey'],
                'chips' => $isAr
                    ? [['label' => 'الوصول', 'value' => 'Recovered'], ['label' => 'التجربة', 'value' => 'Consistent'], ['label' => 'الحالة', 'value' => 'Protected']]
                    : [['label' => 'Access', 'value' => 'Recovered'], ['label' => 'Experience', 'value' => 'Consistent'], ['label' => 'State', 'value' => 'Protected']],
                'meta_title' => $isAr ? 'كلمة مرور جديدة' : 'Set New Password',
            ],
            'verification.notice' => [
                'title' => $isAr ? 'أكد بريدك الإلكتروني' : 'Confirm your email address',
                'lead' => $isAr ? 'خطوة بسيطة حتى تستكمل رحلتك داخل المنصة وتصل إلى واجهتك ولوحاتك.' : 'One short step before you continue into the platform, your dashboards, and your storefront.',
                'eyebrow' => $isAr ? 'تأكيد الهوية' : 'Verify Identity',
                'kicker' => $isAr ? 'تأكيد البريد' : 'Email Verification',
                'features' => $isAr
                    ? ['تأكيد سريع', 'استمرار الرحلة', 'وصول أوضح للمنصة']
                    : ['Quick confirmation', 'Journey continuity', 'Cleaner platform access'],
                'chips' => $isAr
                    ? [['label' => 'الخطوة', 'value' => 'Verification'], ['label' => 'الأثر', 'value' => 'Access'], ['label' => 'الرحلة', 'value' => 'Continues']]
                    : [['label' => 'Step', 'value' => 'Verification'], ['label' => 'Effect', 'value' => 'Access'], ['label' => 'Journey', 'value' => 'Continues']],
                'meta_title' => $isAr ? 'تأكيد البريد الإلكتروني' : 'Verify Email',
            ],
            'password.confirm' => [
                'title' => $isAr ? 'أكد هويتك للمتابعة' : 'Confirm your identity to continue',
                'lead' => $isAr ? 'قبل الوصول إلى الإجراء الحساس، أكمل التحقق ضمن نفس الواجهة العامة الموحّدة.' : 'Before the sensitive action, confirm your identity inside the same unified public-facing shell.',
                'eyebrow' => $isAr ? 'تحقق إضافي' : 'Extra Verification',
                'kicker' => $isAr ? 'تأكيد كلمة المرور' : 'Confirm Password',
                'features' => $isAr
                    ? ['حماية للإجراءات الحساسة', 'تجربة موحّدة', 'وصول سريع بعد التحقق']
                    : ['Protection for sensitive actions', 'Unified experience', 'Fast access after verification'],
                'chips' => $isAr
                    ? [['label' => 'الأمان', 'value' => 'Protected'], ['label' => 'الحالة', 'value' => 'Confirm'], ['label' => 'المسار', 'value' => 'Continue']]
                    : [['label' => 'Security', 'value' => 'Protected'], ['label' => 'State', 'value' => 'Confirm'], ['label' => 'Path', 'value' => 'Continue']],
                'meta_title' => $isAr ? 'تأكيد كلمة المرور' : 'Confirm Password',
            ],
            default => [
                'title' => $isAr ? 'ارجع إلى حسابك وأكمل الرحلة' : 'Return to your account and continue the journey',
                'lead' => $isAr ? 'من السوق العام إلى لوحة التحكم، نفس الهوية البصرية تستمر هنا حتى لا يشعر المستخدم أنه انتقل إلى منتج مختلف.' : 'From the public marketplace to the dashboard, the same visual language continues here so the user never feels dropped into a different product.',
                'eyebrow' => $isAr ? 'بوابة الدخول' : 'Entry Point',
                'kicker' => $isAr ? 'تسجيل الدخول' : 'Sign In',
                'features' => $isAr
                    ? ['نفس تصميم السوق والواجهة العامة', 'متابعة سهلة من الويب إلى لوحة التحكم', 'دعم واضح للعربية والإنجليزية']
                    : ['Same language as the marketplace and storefront', 'Clean continuation from public web to dashboard', 'Clear Arabic and English support'],
                'chips' => $isAr
                    ? [['label' => 'المسار', 'value' => 'Marketplace'], ['label' => 'الوجهة', 'value' => 'Dashboard'], ['label' => 'اللغة', 'value' => 'AR / EN']]
                    : [['label' => 'Path', 'value' => 'Marketplace'], ['label' => 'Destination', 'value' => 'Dashboard'], ['label' => 'Language', 'value' => 'AR / EN']],
                'meta_title' => $isAr ? 'تسجيل الدخول' : 'Sign In',
            ],
        };
        $navTx = [
            'brand' => $isAr ? 'عقاري سمارت' : 'Aqari Smart',
            'home' => $isAr ? 'الرئيسية' : 'Home',
            'featured_nav' => $isAr ? 'السوق' : 'Marketplace',
            'sale_nav' => $isAr ? 'الوكالات' : 'Agencies',
            'rent_nav' => $isAr ? 'بيع معنا' : 'Sell with us',
            'contact_nav' => $isAr ? 'تواصل' : 'Contact',
            'login_cta' => $isAr ? 'تسجيل الدخول' : 'Sign in',
            'register_cta' => $isAr ? 'إنشاء حساب' : 'Create account',
            'sell_cta' => $isAr ? 'بيع معنا' : 'Sell with us',
            'profile_cta' => $isAr ? 'الملف الشخصي' : 'Profile',
            'menu_cta' => $isAr ? 'القائمة' : 'Menu',
            'close_cta' => $isAr ? 'إغلاق' : 'Close',
            'account_title' => $isAr ? 'حسابك' : 'Your Account',
            'browse_title' => $isAr ? 'تنقل داخل الواجهة' : 'Browse Aqari Smart',
            'dashboard_cta' => $isAr ? 'لوحة التحكم' : 'Dashboard',
            'logout_cta' => $isAr ? 'تسجيل الخروج' : 'Log Out',
            'welcome_cta' => $isAr ? 'أهلاً' : 'Welcome',
            'guest_subtitle' => $isAr ? 'ارجع إلى السوق أو أكمل تسجيل الدخول من نفس الواجهة.' : 'Head back to the marketplace or continue signing in from the same shell.',
            'switch_language' => $isAr ? 'تغيير اللغة' : 'Switch language',
        ];
        $sellWithUsUrl = Route::has('sales-flow') ? route('sales-flow') : (Route::has('book-call') ? route('book-call') : '#');
        $navLinks = [
            ['label' => $navTx['home'], 'href' => url('/')],
            ['label' => $navTx['featured_nav'], 'href' => $centralMarketplaceUrl],
            ['label' => $navTx['sale_nav'], 'href' => $centralMarketplaceUrl . '#top-agencies'],
            ['label' => $navTx['rent_nav'], 'href' => $sellWithUsUrl],
        ];
    @endphp
    <title>{{ $authContext['meta_title'] }} — Aqari Smart</title>
    <x-vite-assets />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Manrope:wght@400;500;600;700;800&display=swap');
        [x-cloak]{display:none!important}
        @include('public.partials.market-nav-styles')
        :root{--market-ink:#1f2a24;--market-palm:#0f5a46;--market-brass:#b6842f;--market-clay:#9d5a3b;--market-sand:#ece2cf;--market-cream:#fbf7ef;--market-line:rgba(130,94,38,.16);--brand:#0f5a46;--brand-rgb:15,90,70;--dark:#1f2a24}
        *{box-sizing:border-box}
        html,body{margin:0;min-height:100%}
        body{background:radial-gradient(circle at top left,rgba(182,132,47,.14),transparent 24%),radial-gradient(circle at top right,rgba(15,90,70,.12),transparent 24%),linear-gradient(180deg,#ece2cf 0,#f5ecdc 320px,#fbf7ef 100%);color:var(--market-ink);font-family:'Manrope',system-ui,sans-serif;-webkit-font-smoothing:antialiased}
        html[dir="rtl"] body{font-family:'Cairo','Noto Sans Arabic',sans-serif}
        .auth-shell{max-width:1320px;margin:0 auto;padding-inline:1rem}
        .auth-card{border:1px solid var(--market-line);background:rgba(255,252,246,.94);box-shadow:0 28px 60px -38px rgba(57,42,16,.34)}
        .auth-panel{position:relative;overflow:hidden;border-radius:2.1rem}
        .auth-ornament{height:10px;width:112px;border-radius:999px;background:linear-gradient(90deg,rgba(15,90,70,.16),rgba(182,132,47,.32),rgba(15,90,70,.16)),repeating-linear-gradient(90deg,transparent 0 10px,rgba(182,132,47,.58) 10px 14px,transparent 14px 24px)}
        .auth-hero{background:radial-gradient(circle at top left,rgba(255,255,255,.14),transparent 28%),linear-gradient(145deg,rgba(15,32,26,.96),rgba(15,90,70,.88) 54%,rgba(48,33,15,.84));color:#fff8ea}
        .auth-hero::after{content:'';position:absolute;inset:0;background:linear-gradient(180deg,rgba(15,18,14,.08),rgba(15,18,14,.22));pointer-events:none}
        .auth-hero-content,.auth-form-content{position:relative;z-index:1}
        .auth-chip{border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.08);backdrop-filter:blur(14px)}
        .auth-chip-label{font-size:.67rem;letter-spacing:.18em;text-transform:uppercase;color:rgba(255,244,221,.64);font-weight:700}
        .auth-chip-value{margin-top:.45rem;font-size:1rem;line-height:1.2;font-weight:800;color:#fff8ea}
        .auth-kicker{font-size:.72rem;font-weight:800;letter-spacing:.22em;text-transform:uppercase;color:rgba(255,241,212,.72)}
        .auth-form-shell{background:linear-gradient(180deg,rgba(255,249,239,.98),rgba(247,237,214,.9))}
        .auth-mini-link{display:inline-flex;align-items:center;gap:.5rem;border-radius:999px;border:1px solid rgba(130,94,38,.16);background:rgba(255,255,255,.76);padding:.65rem 1rem;font-size:.76rem;font-weight:800;text-transform:uppercase;letter-spacing:.14em;color:var(--market-palm);text-decoration:none}
        .auth-visual{overflow:hidden;border-radius:1.8rem;border:1px solid rgba(255,255,255,.1);background:rgba(255,255,255,.08);box-shadow:0 28px 60px -38px rgba(0,0,0,.42)}
        .auth-visual-bar{display:flex;align-items:center;gap:.45rem;padding:.9rem 1rem;background:rgba(255,255,255,.08)}
        .auth-visual-dot{height:.55rem;width:.55rem;border-radius:999px;background:rgba(255,255,255,.16)}
        .auth-visual img{display:block;height:auto;width:100%}
        .auth-feature{display:flex;align-items:flex-start;gap:.8rem;font-size:.92rem;line-height:1.75;color:rgba(255,248,236,.82)}
        .auth-feature-dot{margin-top:.55rem;height:.5rem;width:.5rem;flex-shrink:0;border-radius:999px;background:rgba(255,231,176,.88)}
        .auth-toggle{display:flex;align-items:center;gap:.35rem;border-radius:999px;border:1px solid rgba(130,94,38,.16);background:#fff;padding:.3rem}
        .auth-toggle a{text-decoration:none;padding:.55rem .95rem;border-radius:999px;font-size:.76rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:#5f655d}
        .auth-toggle a.active{background:linear-gradient(135deg,var(--market-palm),var(--market-brass));color:#fff}
        .auth-input,.r-input{display:block;width:100%;padding:.9rem 1rem;border:1px solid rgba(130,94,38,.18);border-radius:1rem;background:rgba(255,255,255,.84);font-size:.92rem;line-height:1.5;color:var(--market-ink);transition:border-color .2s,box-shadow .2s,transform .2s}
        .auth-input:focus,.r-input:focus{outline:none;border-color:rgba(182,132,47,.72);box-shadow:0 0 0 4px rgba(182,132,47,.12)}
        .auth-input::placeholder,.r-input::placeholder{color:#8b8e87}
        .auth-input.has-icon-left,.r-input.has-icon,.auth-input.has-icon{padding-left:3rem}
        .auth-input.has-icon-right{padding-right:3rem}
        html[dir="rtl"] .auth-input.has-icon-left,html[dir="rtl"] .r-input.has-icon,html[dir="rtl"] .auth-input.has-icon{padding-left:1rem;padding-right:3rem}
        html[dir="rtl"] .auth-input.has-icon-right{padding-right:1rem;padding-left:3rem}
        .auth-btn,.r-btn{display:inline-flex;align-items:center;justify-content:center;gap:.55rem;width:100%;padding:.95rem 1.15rem;border:none;border-radius:1rem;font-size:.9rem;font-weight:800;cursor:pointer;transition:transform .2s,box-shadow .2s,opacity .2s;position:relative;overflow:hidden}
        .auth-btn:disabled,.r-btn:disabled{opacity:.68;cursor:not-allowed;transform:none!important}
        .auth-btn-primary,.r-btn-primary{background:linear-gradient(135deg,var(--market-palm),var(--market-brass));color:#fff;box-shadow:0 24px 40px -24px rgba(15,90,70,.75)}
        .auth-btn-primary:hover:not(:disabled),.r-btn-primary:hover:not(:disabled){transform:translateY(-1px);box-shadow:0 28px 48px -24px rgba(15,90,70,.82)}
        .auth-btn-outline,.r-btn-outline{background:#fff;color:var(--market-ink);border:1px solid rgba(130,94,38,.16)}
        .auth-btn-outline:hover,.r-btn-outline:hover{background:rgba(255,249,239,.94)}
        .auth-btn .shimmer,.r-btn .shimmer{position:absolute;inset:0;background:linear-gradient(90deg,transparent,rgba(255,255,255,.14),transparent);animation:auth-shimmer 3.2s ease-in-out infinite}
        .wizard-step{display:flex;flex-direction:column;align-items:center;gap:.4rem;flex:1}
        .wizard-dot{width:2rem;height:2rem;border-radius:999px;display:flex;align-items:center;justify-content:center;font-size:.78rem;font-weight:800;transition:all .25s;border:2px solid rgba(130,94,38,.16);background:#fff;color:#8b8e87}
        .wizard-dot.active{border-color:var(--market-brass);background:linear-gradient(135deg,var(--market-palm),var(--market-brass));color:#fff}
        .wizard-dot.done{border-color:var(--market-brass);background:rgba(182,132,47,.16);color:var(--market-palm)}
        .wizard-line{flex:1;height:2px;background:rgba(130,94,38,.14);transition:background .25s;margin:0 -.12rem}
        .wizard-line.active{background:linear-gradient(90deg,var(--market-palm),var(--market-brass))}
        .pw-bar{height:4px;border-radius:999px;transition:all .25s}
        .shake-it{animation:auth-shake .45s ease-in-out}
        @keyframes auth-shimmer{0%{transform:translateX(-100%)}100%{transform:translateX(100%)}}
        @keyframes auth-shake{0%,100%{transform:translateX(0)}15%,45%,75%{transform:translateX(-4px)}30%,60%,90%{transform:translateX(4px)}}
        @media (max-width:1023px){.auth-shell{padding-inline:.9rem}}
    </style>
</head>
<body>
    <div class="min-h-screen">
        @include('public.partials.market-nav', [
            'isAr' => $isAr,
            'navTx' => $navTx,
            'navLinks' => $navLinks,
            'navBrandHref' => $centralMarketplaceUrl,
            'navBrandLabel' => $navTx['brand'],
            'urlEn' => $urlEn,
            'urlAr' => $urlAr,
            'sellWithUsUrl' => $sellWithUsUrl,
        ])

        <main class="auth-shell px-4 pb-10 pt-28 sm:px-6 sm:pt-32 lg:px-8 lg:pt-36">
            <div class="grid gap-6 xl:grid-cols-[1.06fr_.94fr]">
                <section class="auth-panel auth-card auth-hero px-6 py-8 sm:px-8 sm:py-10 lg:px-10 lg:py-11">
                    <div class="auth-hero-content">
                        <div class="auth-ornament"></div>
                        <p class="auth-kicker mt-6">{{ $authContext['eyebrow'] }}</p>
                        <h1 class="mt-4 max-w-3xl text-4xl font-extrabold leading-[1.04] tracking-[-0.04em] text-[#fff8ea] sm:text-5xl">{{ $authContext['title'] }}</h1>
                        <p class="mt-5 max-w-2xl text-base leading-8 text-white/78">{{ $authContext['lead'] }}</p>

                        <div class="mt-7 grid gap-3 sm:grid-cols-3">
                            @foreach ($authContext['chips'] as $chip)
                                <div class="auth-chip rounded-[1.35rem] px-4 py-4">
                                    <div class="auth-chip-label">{{ $chip['label'] }}</div>
                                    <div class="auth-chip-value">{{ $chip['value'] }}</div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8 space-y-3">
                            @foreach ($authContext['features'] as $feature)
                                <div class="auth-feature">
                                    <span class="auth-feature-dot"></span>
                                    <span>{{ $feature }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8 auth-visual">
                            <div class="auth-visual-bar">
                                <span class="auth-visual-dot"></span>
                                <span class="auth-visual-dot"></span>
                                <span class="auth-visual-dot"></span>
                            </div>
                            @if ($authScreenshot)
                                <img src="{{ $authScreenshot }}" alt="Aqari Smart" loading="eager">
                            @else
                                <div class="flex aspect-[16/10] items-center justify-center bg-[linear-gradient(145deg,rgba(255,255,255,.06),rgba(255,255,255,.02))] px-8 text-center">
                                    <div>
                                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-[1.4rem] border border-white/10 bg-white/10 text-lg font-black text-white">AS</div>
                                        <p class="mt-4 text-sm font-semibold uppercase tracking-[0.2em] text-white/58">{{ $authContext['kicker'] }}</p>
                                        <p class="mt-2 text-xl font-extrabold text-white/92">Aqari Smart</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </section>

                <section class="auth-panel auth-card auth-form-shell px-5 py-6 sm:px-7 sm:py-7 lg:px-8 lg:py-8">
                    <div class="auth-form-content">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <a href="{{ $centralMarketplaceUrl }}" class="auth-mini-link">
                                <svg class="h-4 w-4 {{ $isAr ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                                <span>{{ $isAr ? 'العودة إلى السوق' : 'Back to marketplace' }}</span>
                            </a>
                            <div class="auth-toggle">
                                <a href="{{ $urlEn }}" class="{{ ! $isAr ? 'active' : '' }}">EN</a>
                                <a href="{{ $urlAr }}" class="{{ $isAr ? 'active' : '' }}">{{ $isAr ? 'العربية' : 'AR' }}</a>
                            </div>
                        </div>

                        <div class="mt-6 rounded-[1.85rem] border border-[rgba(130,94,38,.14)] bg-[rgba(255,255,255,.7)] p-5 shadow-[0_24px_42px_-32px_rgba(57,42,16,.22)] sm:p-6 lg:p-7">
                            {{ $slot }}
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
