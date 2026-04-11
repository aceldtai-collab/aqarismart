@php
    $isAr = app()->getLocale() === 'ar';
    $tenant = $profileTenant ?? null;
    $role = strtolower((string) ($profileRole ?? 'marketplace'));
    $tenantUrl = $tenant ? app(\App\Services\Tenancy\TenantManager::class)->tenantUrl($tenant, '/') : null;
    $sellWithUsUrl = Route::has('sales-flow') ? route('sales-flow') : (Route::has('book-call') ? route('book-call') : '#');
    $navLinks = [
        ['label' => $isAr ? 'الرئيسية' : 'Home', 'href' => route('home')],
        ['label' => $isAr ? 'السوق' : 'Marketplace', 'href' => route('public.marketplace')],
        ['label' => $isAr ? 'البحث' : 'Search', 'href' => route('public.search')],
        ['label' => $isAr ? 'الوكالات' : 'Agencies', 'href' => route('public.marketplace') . '#top-agencies'],
    ];
    $navTx = [
        'brand' => $isAr ? 'عقاري سمارت' : 'Aqari Smart',
        'login_cta' => $isAr ? 'تسجيل الدخول' : 'Sign in',
        'register_cta' => $isAr ? 'إنشاء حساب' : 'Create account',
        'sell_cta' => $isAr ? 'بيع معنا' : 'Sell with us',
        'profile_cta' => $isAr ? 'الملف الشخصي' : 'Profile',
        'menu_cta' => $isAr ? 'القائمة' : 'Menu',
        'close_cta' => $isAr ? 'إغلاق' : 'Close',
        'account_title' => $isAr ? 'حسابك' : 'Your Account',
        'browse_title' => $isAr ? 'تصفح السوق' : 'Browse marketplace',
        'dashboard_cta' => $isAr ? 'لوحة التحكم' : 'Dashboard',
        'logout_cta' => $isAr ? 'تسجيل الخروج' : 'Log Out',
        'welcome_cta' => $isAr ? 'أهلاً' : 'Welcome',
        'guest_subtitle' => $isAr ? 'رحلة موحدة بين السوق والحساب.' : 'A unified journey between marketplace and account.',
        'switch_language' => $isAr ? 'تغيير اللغة' : 'Switch language',
    ];
    $roleLabel = match ($role) {
        'owner', 'admin', 'manager', 'staff' => $isAr ? 'فريق العمل' : 'Staff',
        'resident' => $isAr ? 'مقيم' : 'Resident',
        default => $isAr ? 'مستخدم السوق' : 'Marketplace user',
    };
    $story = match ($role) {
        'owner', 'admin', 'manager', 'staff' => $isAr ? 'هذا ملفك الشخصي داخل رحلة عقاري سمارت. من هنا تنتقل بين هويتك الشخصية ولوحة الوكالة بسرعة ووضوح.' : 'This is your personal hub inside Aqari Smart, connecting your identity with your workspace and next steps.',
        'resident' => $isAr ? 'حسابك الآن جزء من رحلة السوق العامة. من هنا تتابع بياناتك، الوكالات المرتبطة بك، وتعود إلى البحث أو العقارات المحفوظة.' : 'Your account now lives inside the public marketplace journey, with quick access to your details, connected agencies, and next browsing steps.',
        default => $isAr ? 'هذا هو حسابك الموحد داخل السوق. منه تتابع نشاطك وتنتقل إلى الوكالات والعقارات بسهولة.' : 'This is your unified marketplace account, where your activity and next property steps stay in one place.',
    };
    $tenantDescription = $tenant?->settings['description'] ?? ($isAr ? 'واجهة عامة منظّمة لوكالتك الحالية.' : 'A polished public presence for your current agency.');
    $heroStats = [
        ['label' => $isAr ? 'الوصول' : 'Access', 'value' => $roleLabel],
        ['label' => $isAr ? 'الحالة' : 'Status', 'value' => $user->email_verified_at ? ($isAr ? 'موثق' : 'Verified') : ($isAr ? 'غير موثق' : 'Not verified')],
        ['label' => $isAr ? 'المساحات' : 'Workspaces', 'value' => (string) $user->tenants->count()],
    ];
    $accountRows = array_filter([
        ['label' => $isAr ? 'الاسم' : 'Name', 'value' => $user->name],
        ['label' => $isAr ? 'البريد الإلكتروني' : 'Email', 'value' => $user->email],
        ['label' => $isAr ? 'الهاتف' : 'Phone', 'value' => $user->phone],
        ['label' => $isAr ? 'الدور' : 'Role', 'value' => $roleLabel],
        ['label' => $isAr ? 'موثق' : 'Verified', 'value' => $user->email_verified_at ? ($isAr ? 'نعم' : 'Yes') : ($isAr ? 'لا' : 'No')],
    ], fn ($row) => filled($row['value']));
    $tenantRows = $tenant ? array_filter([
        ['label' => $isAr ? 'الوكالة' : 'Agency', 'value' => $tenant->name],
        ['label' => $isAr ? 'الرابط' : 'Slug', 'value' => $tenant->slug],
        ['label' => $isAr ? 'الخطة' : 'Plan', 'value' => $tenant->activeSubscription?->package?->name ?? $tenant->plan],
        ['label' => $isAr ? 'الموقع العام' : 'Public site', 'value' => $tenantUrl],
        ['label' => $isAr ? 'الوصف' : 'Description', 'value' => $tenantDescription],
    ], fn ($row) => filled($row['value'])) : [];
    $actionCards = array_values(array_filter([
        ['href' => route('public.marketplace'), 'kicker' => $isAr ? 'السوق' : 'Marketplace', 'title' => $isAr ? 'واصل التصفح' : 'Continue browsing', 'text' => $isAr ? 'ارجع إلى السوق واكمل البحث عن العقار المناسب.' : 'Return to the marketplace and continue your property search.'],
        ['href' => route('public.search'), 'kicker' => $isAr ? 'البحث' : 'Search', 'title' => $isAr ? 'افتح البحث الكامل' : 'Open full search', 'text' => $isAr ? 'استخدم الخريطة والفلاتر والنتائج المتقدمة.' : 'Use maps, filters, and the full professional search flow.'],
        ['href' => route('my-listings.index'), 'kicker' => $isAr ? 'إعلاناتي' : 'My listings', 'title' => $isAr ? 'أدر إعلاناتك' : 'Manage your listings', 'text' => $isAr ? 'أنشئ أو عدل أو راجع إعلاناتك المباشرة من المالك.' : 'Create, edit, and review your direct-owner listings.'],
        ['href' => $sellWithUsUrl, 'kicker' => $isAr ? 'بيع معنا' : 'Sell with us', 'title' => $isAr ? 'ابدأ مساحة عمل' : 'Launch a workspace', 'text' => $isAr ? 'إذا كنت وكالة أو مدير عقارات، ابدأ من هنا.' : 'If you are an agency or property manager, start here.'],
        $tenantUrl ? ['href' => $tenantUrl, 'kicker' => $isAr ? 'الوكالة' : 'Agency', 'title' => $isAr ? 'افتح الموقع العام' : 'Open public site', 'text' => $isAr ? 'انتقل إلى الواجهة العامة للوكالة المرتبطة بك.':'Go to the public site of your connected agency.'] : null,
    ]));
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isAr ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $isAr ? 'ملفي الشخصي' : 'My Profile' }} | {{ config('app.name') }}</title>
    <x-vite-assets />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root{
            --market-ink:#1f2a24;
            --market-palm:#0f5a46;
            --market-river:#2f7a72;
            --market-brass:#b6842f;
            --market-clay:#9d5a3b;
            --market-sand:#efe4cf;
            --market-cream:#fbf7ef;
            --market-line:rgba(130,94,38,.16);
            --account-primary:#0f5a46;
            --account-accent:#b6842f;
            --account-primary-rgb:15 90 70;
            --account-accent-rgb:182 132 47;
        }
        body{
            margin:0;
            color:var(--market-ink);
            background:
                radial-gradient(circle at top left, rgba(182,132,47,.14), transparent 22%),
                radial-gradient(circle at top right, rgba(15,90,70,.11), transparent 24%),
                linear-gradient(180deg, #eee2cc 0, #f7efdf 300px, #fbf7ef 100%);
            font-family:'{{ $isAr ? 'Cairo' : 'Manrope' }}', 'Manrope', system-ui, sans-serif;
        }
        @include('public.partials.market-nav-styles')
        .profile-shell{max-width:1320px;margin:0 auto;padding:0 1rem 4rem}
        .profile-hero{
            position:relative;overflow:hidden;border-radius:2rem;
            background:
                radial-gradient(circle at top left, rgba(255,255,255,.14), transparent 28%),
                linear-gradient(145deg, rgba(15,32,26,.96), rgba(15,90,70,.92) 52%, rgba(182,132,47,.84));
            color:#fff8ea;box-shadow:0 30px 64px -34px rgba(28,22,10,.58);
        }
        .profile-hero::after{content:"";position:absolute;inset:0;background:linear-gradient(180deg, rgba(10,16,13,.04), rgba(10,16,13,.22)),radial-gradient(circle at 85% 14%, rgba(255,255,255,.08), transparent 24%);pointer-events:none}
        .profile-hero-copy{position:relative;z-index:1;padding:2rem}
        .profile-kicker{font-size:.72rem;font-weight:800;letter-spacing:.22em;text-transform:uppercase;color:rgba(255,241,212,.74)}
        .profile-ornament{height:10px;width:106px;border-radius:999px;background:linear-gradient(90deg, rgba(15,90,70,.16), rgba(182,132,47,.34), rgba(182,132,47,.16)),repeating-linear-gradient(90deg, transparent 0 10px, rgba(182,132,47,.58) 10px 14px, transparent 14px 24px)}
        .profile-avatar{display:flex;height:5rem;width:5rem;align-items:center;justify-content:center;border-radius:1.6rem;background:rgba(255,255,255,.16);font-size:1.5rem;font-weight:900;box-shadow:inset 0 0 0 1px rgba(255,255,255,.16)}
        .profile-chip{display:inline-flex;align-items:center;gap:.5rem;border-radius:999px;border:1px solid rgba(255,255,255,.14);background:rgba(255,255,255,.08);padding:.68rem .95rem;font-size:.67rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:rgba(255,248,236,.88);backdrop-filter:blur(12px)}
        .profile-chip::before{content:"";width:.45rem;height:.45rem;border-radius:999px;background:#f6cb74;box-shadow:0 0 0 4px rgba(182,132,47,.16)}
        .profile-stat{border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.08);border-radius:1.35rem;padding:.95rem 1rem;backdrop-filter:blur(14px)}
        .profile-stat-label{font-size:.64rem;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:rgba(255,244,221,.62)}
        .profile-stat-value{margin-top:.35rem;font-size:1rem;line-height:1.2;font-weight:800;color:#fff8ea}
        .profile-card{border:1px solid var(--market-line);border-radius:1.8rem;background:rgba(255,252,246,.94);box-shadow:0 22px 46px -34px rgba(55,38,12,.36);padding:1.5rem}
        .profile-section-kicker{font-size:.68rem;font-weight:800;letter-spacing:.2em;text-transform:uppercase;color:var(--market-brass)}
        .profile-section-title{margin-top:.35rem;font-size:1.6rem;line-height:1.08;font-weight:900;letter-spacing:-.04em;color:var(--market-ink)}
        .profile-section-text{margin-top:.55rem;color:#5f655f;font-size:.95rem;line-height:1.8}
        .profile-list{display:grid;gap:.85rem;margin-top:1.25rem}
        .profile-row{display:flex;gap:.85rem;align-items:flex-start;border-radius:1.25rem;border:1px solid rgba(130,94,38,.12);background:rgba(255,255,255,.7);padding:.95rem}
        .profile-icon{display:flex;height:2.6rem;width:2.6rem;align-items:center;justify-content:center;border-radius:1rem;background:rgba(15,90,70,.1);color:var(--market-palm);font-weight:900}
        .profile-row-label{font-size:.7rem;font-weight:800;letter-spacing:.16em;text-transform:uppercase;color:#8b846f}
        .profile-row-value{margin-top:.35rem;font-size:.95rem;line-height:1.7;font-weight:800;word-break:break-word}
        .profile-actions{display:grid;gap:.85rem;grid-template-columns:repeat(2,minmax(0,1fr));margin-top:1.25rem}
        .profile-action{display:flex;flex-direction:column;gap:.75rem;border:1px solid rgba(130,94,38,.14);border-radius:1.45rem;background:rgba(255,255,255,.78);padding:1rem;text-decoration:none;color:var(--market-ink);box-shadow:0 20px 44px -34px rgba(55,38,12,.34);transition:transform .18s ease, box-shadow .18s ease, border-color .18s ease}
        .profile-action:hover{transform:translateY(-2px);border-color:rgba(182,132,47,.32);box-shadow:0 26px 48px -34px rgba(55,38,12,.44)}
        .profile-action-kicker{font-size:.74rem;line-height:1.7;font-weight:700;color:#6f746b;text-transform:uppercase;letter-spacing:.1em}
        .profile-action-title{font-size:1.08rem;font-weight:900;line-height:1.15}
        .profile-action-text{font-size:.82rem;line-height:1.7;color:#5f655f}
        .profile-button{display:inline-flex;align-items:center;justify-content:center;gap:.55rem;border-radius:1.15rem;padding:.95rem 1rem;font-size:.78rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase;text-decoration:none;transition:transform .18s ease, box-shadow .18s ease, border-color .18s ease}
        .profile-button:hover{transform:translateY(-1px)}
        .profile-button-primary{background:linear-gradient(135deg, var(--market-palm), var(--market-brass));color:#fff8ea;box-shadow:0 18px 34px -18px rgba(15,90,70,.8)}
        .profile-button-secondary{border:1px solid rgba(130,94,38,.16);background:rgba(255,255,255,.82);color:var(--market-ink);box-shadow:0 18px 32px -28px rgba(55,38,12,.35)}
        @media (max-width: 768px){
            .profile-actions{grid-template-columns:1fr}
            .profile-hero-copy{padding:1.4rem}
        }
    </style>
</head>
<body>
    @include('public.partials.market-nav', [
        'isAr' => $isAr,
        'navTx' => $navTx,
        'navLinks' => $navLinks,
        'navBrandHref' => route('home'),
        'navBrandLabel' => $navTx['brand'],
        'sellWithUsUrl' => $sellWithUsUrl,
        'tenantCtx' => null,
    ])

    <main class="profile-shell" style="padding-top:8.5rem">
        <section class="profile-hero">
            <div class="profile-hero-copy">
                <div class="profile-kicker">{{ $isAr ? 'الملف الشخصي' : 'Profile hub' }}</div>
                <div class="profile-ornament" style="margin-top:1rem"></div>
                <div style="display:flex;gap:1rem;align-items:flex-start;margin-top:1.35rem;flex-wrap:wrap">
                    <div class="profile-avatar">{{ collect(explode(' ', trim((string) $user->name)))->filter()->take(2)->map(fn($part) => mb_substr($part, 0, 1))->join('') ?: '?' }}</div>
                    <div style="flex:1;min-width:260px">
                        <h1 style="margin:0;font-size:clamp(2rem,4vw,3.35rem);line-height:1.02;font-weight:900;letter-spacing:-.05em">{{ $user->name }}</h1>
                        <p style="margin:.7rem 0 0;font-size:.98rem;font-weight:700;color:rgba(255,255,255,.8)">{{ $roleLabel }}@if($tenant) · {{ $tenant->name }}@endif</p>
                        <p style="margin:.9rem 0 0;max-width:54rem;font-size:1rem;line-height:1.85;color:rgba(255,255,255,.76)">{{ $story }}</p>
                    </div>
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:.65rem;margin-top:1.25rem">
                    <span class="profile-chip">{{ $isAr ? 'هوية موحدة' : 'Unified identity' }}</span>
                    <span class="profile-chip">{{ $isAr ? 'السوق أولاً' : 'Marketplace first' }}</span>
                    @if($tenant)<span class="profile-chip">{{ $isAr ? 'وكالة مرتبطة' : 'Connected agency' }}</span>@endif
                </div>
                <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:.75rem;margin-top:1.25rem">
                    @foreach($heroStats as $stat)
                        <div class="profile-stat">
                            <div class="profile-stat-label">{{ $stat['label'] }}</div>
                            <div class="profile-stat-value">{{ $stat['value'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <div style="display:grid;gap:1.25rem;margin-top:1.25rem;grid-template-columns:minmax(0,1.2fr) minmax(0,.8fr)">
            <section class="profile-card">
                <div class="profile-section-kicker">{{ $isAr ? 'تفاصيل الحساب' : 'Account details' }}</div>
                <h2 class="profile-section-title">{{ $isAr ? 'بياناتك الأساسية' : 'Your account details' }}</h2>
                <p class="profile-section-text">{{ $isAr ? 'المعلومات الأساسية التي تعرّف حسابك الحالي داخل السوق.' : 'The core information that defines your signed-in marketplace account.' }}</p>
                <div class="profile-list">
                    @foreach($accountRows as $row)
                        <div class="profile-row">
                            <div class="profile-icon">{{ mb_substr($row['label'], 0, 1) }}</div>
                            <div>
                                <div class="profile-row-label">{{ $row['label'] }}</div>
                                <div class="profile-row-value">{{ $row['value'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="profile-card">
                <div class="profile-section-kicker">{{ $isAr ? 'الجلسة الحالية' : 'Current session' }}</div>
                <h2 class="profile-section-title">{{ $isAr ? 'إدارة الجلسة' : 'Manage your session' }}</h2>
                <p class="profile-section-text">{{ $isAr ? 'يمكنك متابعة التصفح، الانتقال إلى البحث، أو تسجيل الخروج من هذا الحساب.' : 'Continue browsing, jump into search, or sign out of this account.' }}</p>
                <div style="display:grid;gap:.85rem;margin-top:1.25rem">
                    <a href="{{ route('public.marketplace') }}" class="profile-button profile-button-secondary">{{ $isAr ? 'العودة إلى السوق' : 'Back to marketplace' }}</a>
                    <a href="{{ route('public.search') }}" class="profile-button profile-button-secondary">{{ $isAr ? 'فتح البحث' : 'Open search' }}</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="profile-button profile-button-primary" style="width:100%">{{ $isAr ? 'تسجيل الخروج' : 'Sign Out' }}</button>
                    </form>
                </div>
            </section>
        </div>

        <div style="display:grid;gap:1.25rem;margin-top:1.25rem;grid-template-columns:minmax(0,1fr)">
            @if($tenant)
                <section class="profile-card">
                    <div class="profile-section-kicker">{{ $isAr ? 'الوكالة المرتبطة' : 'Connected agency' }}</div>
                    <h2 class="profile-section-title">{{ $tenant->name }}</h2>
                    <p class="profile-section-text">{{ $isAr ? 'الوصول السريع إلى الوكالة المرتبطة بحسابك الحالي.' : 'Quick access to the agency connected to your current account.' }}</p>
                    <div class="profile-list">
                        @foreach($tenantRows as $row)
                            <div class="profile-row">
                                <div class="profile-icon">{{ mb_substr($row['label'], 0, 1) }}</div>
                                <div>
                                    <div class="profile-row-label">{{ $row['label'] }}</div>
                                    <div class="profile-row-value">{{ $row['value'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <section class="profile-card">
                <div class="profile-section-kicker">{{ $isAr ? 'إلى أين بعد ذلك؟' : 'Where next?' }}</div>
                <h2 class="profile-section-title">{{ $isAr ? 'واصل الرحلة من هنا' : 'Continue the journey from here' }}</h2>
                <p class="profile-section-text">{{ $isAr ? 'روابط سريعة تكمل رحلة السوق والحساب بنفس اللغة البصرية الجديدة.' : 'Fast destinations that continue the marketplace and account journey in the same design language.' }}</p>
                <div class="profile-actions">
                    @foreach($actionCards as $action)
                        <a href="{{ $action['href'] }}" class="profile-action">
                            <div class="profile-action-kicker">{{ $action['kicker'] }}</div>
                            <div class="profile-action-title">{{ $action['title'] }}</div>
                            <div class="profile-action-text">{{ $action['text'] }}</div>
                        </a>
                    @endforeach
                </div>
            </section>
        </div>
    </main>
</body>
</html>
