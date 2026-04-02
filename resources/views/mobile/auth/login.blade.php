@extends('mobile.layouts.app')

@section('content')
    @php
        $locale = app()->getLocale() === 'ar' ? 'ar' : 'en';
        $authData = config('mobiletestdata.auth.login', []);
        $copy = fn ($key, $fallback = '') => data_get($authData, $key . '.' . $locale, $fallback);
        $loginText = $locale === 'ar' ? 'تسجيل الدخول' : 'Login';
    @endphp

    <div class="mx-auto grid max-w-5xl gap-6 lg:grid-cols-[1fr_1.05fr]">
        <section class="overflow-hidden rounded-[0.5rem] bg-emerald-700 text-white shadow-xl">
            <div class="space-y-6 px-6 py-7 sm:px-8">
                <div class="inline-flex items-center rounded-full bg-white/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-white/80">
                    {{ app()->getLocale() === 'ar' ? 'عقاري سمارت' : 'Aqari Smart' }}
                </div>
                <div class="space-y-2">
                    <h1 class="text-3xl font-semibold leading-tight">{{ $copy('title', 'Welcome back') }}</h1>
                    <p class="text-sm leading-6 text-white/75">{{ $copy('subtitle', 'Sign in to your marketplace account and continue browsing.') }}</p>
                </div>
                <div class="rounded-[1rem] border border-white/10 bg-white/10 p-4 backdrop-blur">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 rounded-2xl bg-white/10 p-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.25 9.75h1.5m-.75 3h.008v.008H12v-.008Zm0 8.242a9 9 0 1 0 0-17.984 9 9 0 0 0 0 17.984Z"/></svg>
                        </div>
                        <div>
                            <div class="text-sm font-semibold">{{ $copy('hint', 'Need a workspace to list property? Create one, or register a buyer account from the marketplace menu.') }}</div>
                            <a href="{{ route('mobile.register') }}" class="mt-3 inline-flex items-center rounded-full bg-white px-4 py-2 text-xs font-semibold text-slate-900 shadow-sm">{{ app()->getLocale() === 'ar' ? 'إنشاء مساحة عمل' : 'Create workspace' }}</a>
                        </div>
                    </div>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-[1.5rem] bg-white/10 p-4 backdrop-blur">
                        <div class="text-xs uppercase tracking-[0.24em] text-white/60">{{ app()->getLocale() === 'ar' ? 'لجميع المستخدمين' : 'For all users' }}</div>
                        <div class="mt-2 text-lg font-semibold">{{ app()->getLocale() === 'ar' ? 'دخول موحد لحساب السوق' : 'Unified marketplace sign-in' }}</div>
                    </div>
                    <div class="rounded-[1.5rem] bg-white/10 p-4 backdrop-blur">
                        <div class="text-xs uppercase tracking-[0.24em] text-white/60">{{ app()->getLocale() === 'ar' ? 'وصول سريع' : 'Quick access' }}</div>
                        <div class="mt-2 text-lg font-semibold">{{ app()->getLocale() === 'ar' ? 'يحفظ الجلسة محلياً على الهاتف' : 'Stores your session locally' }}</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="rounded-[0.5rem] bg-emerald-700 text-white shadow-xl" x-data="mobileLoginWizard()">
            <div class="space-y-6 px-6 py-7">
                <div class="text-center">
                    <h1 class="text-2xl font-bold text-white">{{ app()->getLocale() === 'ar' ? 'تسجيل الدخول' : 'Sign In' }}</h1>
                    <p class="mt-2 text-sm text-white/70">{{ app()->getLocale() === 'ar' ? 'أهلاً بعودتك' : 'Welcome back' }}</p>
                </div>
            </div>

            <form id="mobile-login-form" class="space-y-5 px-6 py-7" @submit.prevent="submitLogin">
                <div id="login-errors" class="hidden rounded-xl border border-red-400/30 bg-red-500/20 px-4 py-3 text-sm text-white"></div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-white/80">{{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email address' }}</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <svg class="h-5 w-5 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                            </svg>
                        </div>
                        <input x-model="email" name="email" type="email" required autofocus autocomplete="username" class="block w-full appearance-none rounded-xl border border-white/20 bg-white/20 py-3.5 pl-12 pr-4 text-white placeholder-white/60 backdrop-blur-sm focus:border-white/40 focus:bg-white/30 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white/30 sm:text-sm sm:leading-6" placeholder="{{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email address' }}" />
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-white/80">{{ app()->getLocale() === 'ar' ? 'كلمة المرور' : 'Password' }}</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <svg class="h-5 w-5 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                            </svg>
                        </div>
                        <input x-model="password" name="password" type="password" required autocomplete="current-password" class="block w-full appearance-none rounded-xl border border-white/20 bg-white/20 py-3.5 pl-12 pr-4 text-white placeholder-white/60 backdrop-blur-sm focus:border-white/40 focus:bg-white/30 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white/30 sm:text-sm sm:leading-6" placeholder="{{ app()->getLocale() === 'ar' ? 'كلمة المرور' : 'Password' }}" />
                    </div>
                </div>

                <button type="submit" :disabled="loading" class="w-full rounded-xl bg-white px-4 py-3 text-sm font-semibold text-emerald-700 shadow-lg transition hover:bg-white/90 hover:shadow-xl disabled:opacity-50">
                    <span x-show="!loading">{{ $loginText }}</span>
                    <span x-show="loading" x-cloak>{{ app()->getLocale() === 'ar' ? 'جاري التحميل...' : 'Signing in...' }}</span>
                </button>

                <div class="text-center">
                    <p class="text-sm text-white/70">
                        {{ app()->getLocale() === 'ar' ? 'كمستخدم عادي؟' : 'Need a buyer account?' }}
                        <a href="{{ route('mobile.marketplace', ['auth' => 'register']) }}" class="font-semibold text-white hover:underline">{{ app()->getLocale() === 'ar' ? 'إنشاء حساب' : 'Create account' }}</a>
                    </p>
                    <p class="mt-2 text-xs text-white/60">
                        {{ app()->getLocale() === 'ar' ? 'لإضافة عقار أو إنشاء وكالة:' : 'To list property or open an agency:' }}
                        <a href="{{ route('mobile.register') }}" class="font-semibold text-white hover:underline">{{ app()->getLocale() === 'ar' ? 'بيع معنا' : 'Sell with us' }}</a>
                    </p>
                </div>
            </form>
        </section>
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
                errBox.textContent = '{{ app()->getLocale() === "ar" ? "خطأ في الاتصال" : "Connection error" }}';
                errBox.classList.remove('hidden');
                this.loading = false;
            }
        }
    };
}
</script>
@endpush
