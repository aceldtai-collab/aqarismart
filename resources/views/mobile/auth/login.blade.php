@extends('mobile.layouts.app', [
    'title' => app()->getLocale() === 'ar' ? 'تسجيل الدخول' : 'Sign In',
    'show_back_button' => false,
    'body_class' => 'mobile-auth-shell',
])

@php
    $locale = app()->getLocale() === 'ar' ? 'ar' : 'en';
    $authData = config('mobiletestdata.auth.login', []);
    $copy = fn ($key, $fallback = '') => data_get($authData, $key . '.' . $locale, $fallback);
    $isAr = $locale === 'ar';
    $ui = [
        'badge' => $isAr ? 'دخول موحد' : 'Unified access',
        'heroTitle' => $copy('title', $isAr ? 'سجّل دخولك وواصل رحلتك العقارية' : 'Sign in and continue your property journey'),
        'heroText' => $copy('subtitle', $isAr ? 'واجهة واحدة للمشترين والمستأجرين وفرق الوكالات، مع تجربة دافئة وواضحة تشبه السوق الجديد على الموبايل.' : 'One entry point for buyers, renters, and agency teams in the same warmer mobile journey as the new marketplace.'),
        'hintTitle' => $copy('hint', $isAr ? 'إذا كنت تريد إدارة وكالة أو عرض عقاراتك، أنشئ مساحة عمل مخصصة من بيع معنا.' : 'If you want to manage an agency or publish listings, create a dedicated workspace from Sell with us.'),
        'createWorkspace' => $isAr ? 'أنشئ مساحة عمل' : 'Create workspace',
        'buyerTrack' => $isAr ? 'مسار الباحثين' : 'Buyer track',
        'buyerTrackText' => $isAr ? 'تصفّح السوق، احفظ الحساب، وانتقل إلى ملفك مباشرة.' : 'Browse the marketplace, keep one account, and land in your profile immediately.',
        'workspaceTrack' => $isAr ? 'مسار الوكالات' : 'Agency track',
        'workspaceTrackText' => $isAr ? 'دخول واحد يوصلك إلى لوحة الوكالة عندما تكون مرتبطاً بمساحة عمل.' : 'The same login moves you into the tenant dashboard when your account belongs to a workspace.',
        'formKicker' => $isAr ? 'عودة سريعة' : 'Quick return',
        'formTitle' => $isAr ? 'أدخل بياناتك وابدأ من حيث توقفت' : 'Enter your details and pick up where you left off',
        'formText' => $isAr ? 'سجّل الدخول للوصول إلى ملفك، الوحدات المحفوظة، أو لوحة الوكالة إذا كنت ضمن فريق العمل.' : 'Sign in to access your profile, saved activity, or your agency dashboard when your role includes tenant access.',
        'email' => $isAr ? 'البريد الإلكتروني' : 'Email address',
        'emailPlaceholder' => $isAr ? 'name@example.com' : 'name@example.com',
        'password' => $isAr ? 'كلمة المرور' : 'Password',
        'passwordPlaceholder' => $isAr ? 'أدخل كلمة المرور' : 'Enter your password',
        'signIn' => $isAr ? 'تسجيل الدخول' : 'Sign In',
        'signingIn' => $isAr ? 'جارٍ تسجيل الدخول...' : 'Signing in...',
        'registerPrompt' => $isAr ? 'تريد حساباً عادياً للبحث والشراء أو الإيجار؟' : 'Need a regular buyer account for browsing and renting or buying?',
        'registerLink' => $isAr ? 'إنشاء حساب' : 'Create account',
        'sellPrompt' => $isAr ? 'تريد عرض عقاراتك أو فتح وكالة؟' : 'Want to list property or open an agency?',
        'sellLink' => $isAr ? 'بيع معنا' : 'Sell with us',
        'connectionError' => $isAr ? 'حدث خطأ في الاتصال' : 'Connection error',
        'trustOne' => $isAr ? 'جلسة محلية محفوظة على الهاتف' : 'Session stored locally on your phone',
        'trustTwo' => $isAr ? 'الحساب نفسه يعمل في السوق والملف الشخصي والوكالة' : 'The same account works across marketplace, profile, and tenant workflow',
    ];
@endphp

@push('head')
    @include('mobile.auth.partials.theme')
@endpush

@section('content')
    <div class="ma-page">
        <div class="ma-shell">
            <div class="ma-grid">
                <section class="ma-hero">
                    <div class="ma-hero-copy px-5 py-6 sm:px-6 sm:py-7">
                        <div class="ma-kicker">{{ $ui['badge'] }}</div>
                        <div class="mt-4 ma-ornament"></div>

                        <div class="mt-5 space-y-3">
                            <h1 class="text-[2rem] font-black leading-[1.02] tracking-[-0.05em] text-[#fff8ea] sm:text-[2.35rem]">
                                {{ $ui['heroTitle'] }}
                            </h1>
                            <p class="max-w-xl text-[0.96rem] leading-8 text-white/78">
                                {{ $ui['heroText'] }}
                            </p>
                        </div>

                        <div class="mt-5 flex flex-wrap gap-2.5">
                            <div class="ma-chip">{{ $isAr ? 'للمستخدمين والوكالات' : 'For buyers and agencies' }}</div>
                            <div class="ma-chip">{{ $isAr ? 'موبايل أولاً' : 'Mobile-first' }}</div>
                        </div>

                        <div class="mt-6 ma-note">
                            <div class="flex items-start gap-3">
                                <div class="ma-mini-icon">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M11.25 9.75h1.5m-.75 3h.008v.008H12v-.008Zm0 8.242a9 9 0 1 0 0-17.984 9 9 0 0 0 0 17.984Z"/>
                                    </svg>
                                </div>
                                <div class="space-y-3">
                                    <div class="text-sm font-extrabold text-white">{{ $ui['hintTitle'] }}</div>
                                    <a href="{{ route('mobile.register') }}" class="ma-note-link">{{ $ui['createWorkspace'] }}</a>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 grid gap-3 sm:grid-cols-2">
                            <div class="ma-stat">
                                <div class="ma-stat-label">{{ $ui['buyerTrack'] }}</div>
                                <div class="ma-stat-value">{{ $ui['buyerTrackText'] }}</div>
                            </div>
                            <div class="ma-stat">
                                <div class="ma-stat-label">{{ $ui['workspaceTrack'] }}</div>
                                <div class="ma-stat-value">{{ $ui['workspaceTrackText'] }}</div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="ma-form-shell overflow-hidden" x-data="mobileLoginWizard()">
                    <div class="ma-form-header px-5 py-6 sm:px-6">
                        <div class="ma-section-kicker">{{ $ui['formKicker'] }}</div>
                        <h2 class="ma-section-title">{{ $ui['formTitle'] }}</h2>
                        <p class="ma-section-text">{{ $ui['formText'] }}</p>
                    </div>

                    <form id="mobile-login-form" class="space-y-5 px-5 py-5 sm:px-6 sm:py-6" @submit.prevent="submitLogin">
                        <div id="login-errors" class="ma-error hidden"></div>

                        <div class="ma-panel p-4 sm:p-5">
                            <div class="space-y-4">
                                <div>
                                    <label class="ma-label">{{ $ui['email'] }}</label>
                                    <div class="ma-input-wrap">
                                        <div class="ma-input-icon">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21.75 6.75v10.5A2.25 2.25 0 0 1 19.5 19.5h-15A2.25 2.25 0 0 1 2.25 17.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15A2.25 2.25 0 0 0 2.25 6.75m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
                                            </svg>
                                        </div>
                                        <input
                                            x-model="email"
                                            name="email"
                                            type="email"
                                            required
                                            autofocus
                                            autocomplete="username"
                                            class="ma-input"
                                            placeholder="{{ $ui['emailPlaceholder'] }}"
                                        />
                                    </div>
                                </div>

                                <div>
                                    <label class="ma-label">{{ $ui['password'] }}</label>
                                    <div class="ma-input-wrap">
                                        <div class="ma-input-icon">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5A2.25 2.25 0 0 0 19.5 19.5v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75A2.25 2.25 0 0 0 4.5 12.75v6.75A2.25 2.25 0 0 0 6.75 21.75Z"/>
                                            </svg>
                                        </div>
                                        <input
                                            x-model="password"
                                            name="password"
                                            type="password"
                                            required
                                            autocomplete="current-password"
                                            class="ma-input"
                                            placeholder="{{ $ui['passwordPlaceholder'] }}"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" :disabled="loading" class="ma-submit">
                            <span x-show="!loading">{{ $ui['signIn'] }}</span>
                            <span x-show="loading" x-cloak>{{ $ui['signingIn'] }}</span>
                        </button>

                        <div class="ma-panel p-4 sm:p-5">
                            <div class="ma-mini-list">
                                <div class="ma-mini-item">
                                    <div class="ma-mini-icon bg-[rgba(15,90,70,.1)] text-[color:var(--ma-palm)]">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12.75 11.25 15 15 9.75m6 2.25a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm leading-7 text-[#505751]">{{ $ui['trustOne'] }}</p>
                                </div>
                                <div class="ma-mini-item">
                                    <div class="ma-mini-icon bg-[rgba(182,132,47,.12)] text-[color:var(--ma-brass)]">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3.75 5.25h16.5M3.75 12h16.5m-16.5 6.75h16.5"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm leading-7 text-[#505751]">{{ $ui['trustTwo'] }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="ma-footer-note space-y-3 pt-4 text-sm leading-7 text-[#5a6059]">
                            <p>
                                {{ $ui['registerPrompt'] }}
                                <a href="{{ route('mobile.marketplace', ['auth' => 'register']) }}" class="ma-inline-link">{{ $ui['registerLink'] }}</a>
                            </p>
                            <p>
                                {{ $ui['sellPrompt'] }}
                                <a href="{{ route('mobile.register') }}" class="ma-inline-link">{{ $ui['sellLink'] }}</a>
                            </p>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function mobileLoginWizard() {
    return {
        email: '',
        password: '',
        loading: false,
        async submitLogin() {
            this.loading = true;
            const errBox = document.getElementById('login-errors');
            errBox.classList.add('hidden');
            errBox.textContent = '';

            try {
                const res = await fetch((window.__AQARI_API_BASE || '') + '/api/mobile/auth/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ email: this.email, password: this.password }),
                });
                const json = await res.json();

                if (!res.ok) {
                    const msgs = json.errors ? Object.values(json.errors).flat().join('<br>') : (json.message || 'Login failed');
                    errBox.innerHTML = msgs;
                    errBox.classList.remove('hidden');
                    this.loading = false;
                    return;
                }

                localStorage.setItem('aqari_mobile_token', json.token);

                if (json.current_tenant?.slug) {
                    localStorage.setItem('aqari_mobile_tenant_slug', json.current_tenant.slug);
                } else {
                    localStorage.removeItem('aqari_mobile_tenant_slug');
                }

                if (json.user?.name) {
                    localStorage.setItem('aqari_mobile_user_name', json.user.name);
                } else {
                    localStorage.removeItem('aqari_mobile_user_name');
                }

                if (json.tenant_role || json.user?.tenant_role) {
                    localStorage.setItem('aqari_mobile_user_role', json.tenant_role || json.user.tenant_role);
                } else {
                    localStorage.removeItem('aqari_mobile_user_role');
                }

                window.location.href = json.current_tenant?.slug && (json.tenant_role || json.user?.tenant_role)
                    ? '{{ route('mobile.dashboard') }}'
                    : '{{ route('mobile.profile') }}';
            } catch (e) {
                errBox.textContent = @json($ui['connectionError']);
                errBox.classList.remove('hidden');
                this.loading = false;
            }
        }
    };
}
</script>
@endpush
