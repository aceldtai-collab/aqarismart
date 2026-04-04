@extends('mobile.layouts.app', [
    'title' => app()->getLocale() === 'ar' ? 'بيع معنا' : 'Sell with us',
    'show_back_button' => false,
    'body_class' => 'mobile-auth-shell',
])

@php
    $locale = app()->getLocale() === 'ar' ? 'ar' : 'en';
    $authData = config('mobiletestdata.auth.register', []);
    $stepLabels = collect($authData['steps'] ?? [])->map(fn ($step) => $step[$locale] ?? ($step['en'] ?? 'Step'))->values()->all();
    $isAr = $locale === 'ar';
    $ui = [
        'badge' => $isAr ? 'بداية الوكالة' : 'Agency onboarding',
        'heroTitle' => data_get($authData, 'title.' . $locale, $isAr ? 'أنشئ واجهتك العقارية على الموبايل والويب' : 'Create your property storefront for mobile and web'),
        'heroText' => data_get($authData, 'subtitle.' . $locale, $isAr ? 'تدفق واضح لفتح مساحة عمل، نشر العقارات، والوصول إلى لوحة التحكم ضمن نفس اللغة البصرية الجديدة.' : 'A clearer flow to launch your workspace, publish listings, and move into the dashboard under the same warmer visual system.'),
        'flowOne' => $isAr ? 'تأسيس الوكالة' : 'Launch the workspace',
        'flowOneText' => $isAr ? 'اسم المالك، البريد، اسم الوكالة، والنطاق الفرعي في خطوات قصيرة.' : 'Owner details, email, agency name, and subdomain in a short guided sequence.',
        'flowTwo' => $isAr ? 'استلام لوحة العمل' : 'Land in the dashboard',
        'flowTwoText' => $isAr ? 'بعد الإنشاء تنتقل مباشرة إلى لوحة الوكالة وتبدأ إدارة العقارات.' : 'After creation you move straight into the tenant dashboard and start managing listings.',
        'whyTitle' => $isAr ? 'لماذا هذا المسار؟' : 'Why this flow?',
        'whyText' => $isAr ? 'نحافظ على البداية خفيفة وسريعة، ثم نكمل بيانات الوكالة من الداخل بعد الدخول.' : 'The first step stays light and fast, then you complete the deeper agency details after signing in.',
        'freeTrial' => $isAr ? 'تجربة أولية للوكلاء الجدد' : 'Starter setup for new teams',
        'formKicker' => $isAr ? 'بيع معنا' : 'Sell with us',
        'formTitle' => $isAr ? 'أكمل إنشاء مساحة العمل' : 'Finish creating the workspace',
        'formText' => $isAr ? 'أدخل بياناتك عبر الخطوات الأربع. سننشئ الحساب، الوكالة، والنطاق الفرعي ثم ننقلك مباشرة إلى لوحة التحكم.' : 'Move through the four steps below. We create the user, tenant workspace, and subdomain, then take you directly into the dashboard.',
        'accountBasics' => $isAr ? 'معلومات المالك' : 'Owner profile',
        'contactStep' => $isAr ? 'بيانات التواصل' : 'Contact details',
        'workspaceStep' => $isAr ? 'الوكالة والنطاق' : 'Agency and subdomain',
        'securityStep' => $isAr ? 'حماية الحساب' : 'Account security',
        'name' => $isAr ? 'الاسم الكامل' : 'Full name',
        'namePlaceholder' => $isAr ? 'اسم المالك أو المدير' : 'Owner or manager name',
        'email' => $isAr ? 'البريد الإلكتروني' : 'Email',
        'emailPlaceholder' => $isAr ? 'name@example.com' : 'name@example.com',
        'agency' => $isAr ? 'اسم الوكالة / المكتب' : 'Workspace / agency name',
        'agencyPlaceholder' => $isAr ? 'مثال: جوليجو للعقارات' : 'Example: Jolijo Realty',
        'subdomain' => $isAr ? 'النطاق الفرعي' : 'Subdomain',
        'subdomainPlaceholder' => $isAr ? 'مثال: jolijo' : 'Example: jolijo',
        'subdomainHint' => $isAr ? 'يُستخدم في رابط الوكالة العام ولوحة التحكم.' : 'Used in the tenant public URL and dashboard URL.',
        'password' => $isAr ? 'كلمة المرور' : 'Password',
        'passwordPlaceholder' => $isAr ? 'ثمانية أحرف على الأقل' : 'At least 8 characters',
        'passwordConfirm' => $isAr ? 'تأكيد كلمة المرور' : 'Confirm password',
        'passwordConfirmPlaceholder' => $isAr ? 'أعد كتابة كلمة المرور' : 'Re-enter password',
        'back' => $isAr ? 'السابق' : 'Back',
        'continue' => $isAr ? 'التالي' : 'Continue',
        'createAccount' => $isAr ? 'إنشاء مساحة العمل' : 'Create workspace',
        'creating' => $isAr ? 'جارٍ الإنشاء...' : 'Creating...',
        'alreadyHaveAccount' => $isAr ? 'لديك حساب بالفعل؟' : 'Already have an account?',
        'signIn' => $isAr ? 'تسجيل الدخول' : 'Sign in',
        'needBuyerAccount' => $isAr ? 'تريد حساباً عادياً للشراء أو الإيجار فقط؟' : 'Need a regular buyer account instead?',
        'buyerAccount' => $isAr ? 'سجّل من السوق' : 'Register from marketplace',
        'nameError' => $isAr ? 'الاسم الكامل مطلوب.' : 'Full name is required.',
        'emailError' => $isAr ? 'أدخل بريداً إلكترونياً صحيحاً.' : 'Enter a valid email address.',
        'agencyError' => $isAr ? 'اسم الوكالة مطلوب.' : 'Agency name is required.',
        'subdomainError' => $isAr ? 'النطاق الفرعي يجب أن يكون بين 3 و 30 حرفاً ويحتوي على أحرف إنجليزية صغيرة أو أرقام أو شرطات.' : 'Subdomain must be 3-30 characters using lowercase letters, numbers, or hyphens.',
        'passwordError' => $isAr ? 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.' : 'Password must be at least 8 characters.',
        'passwordConfirmError' => $isAr ? 'تأكيد كلمة المرور غير مطابق.' : 'Password confirmation does not match.',
        'connectionError' => $isAr ? 'حدث خطأ في الاتصال' : 'Connection error',
    ];
    $clientTranslations = [
        'nameError' => $ui['nameError'],
        'emailError' => $ui['emailError'],
        'agencyError' => $ui['agencyError'],
        'subdomainError' => $ui['subdomainError'],
        'passwordError' => $ui['passwordError'],
        'passwordConfirmError' => $ui['passwordConfirmError'],
        'connectionError' => $ui['connectionError'],
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
                            <div class="ma-chip">{{ $isAr ? 'واجهة عامة + لوحة تحكم' : 'Public storefront + dashboard' }}</div>
                            <div class="ma-chip">{{ $isAr ? 'مسار قصير وواضح' : 'Clear 4-step flow' }}</div>
                        </div>

                        <div class="mt-6 ma-note">
                            <div class="space-y-3">
                                <div class="text-sm font-extrabold text-white">{{ $ui['whyTitle'] }}</div>
                                <p class="text-sm leading-7 text-white/78">{{ $ui['whyText'] }}</p>
                                <div class="ma-note-link !inline-flex">{{ $ui['freeTrial'] }}</div>
                            </div>
                        </div>

                        <div class="mt-5 grid gap-3 sm:grid-cols-2">
                            <div class="ma-stat">
                                <div class="ma-stat-label">{{ $ui['flowOne'] }}</div>
                                <div class="ma-stat-value">{{ $ui['flowOneText'] }}</div>
                            </div>
                            <div class="ma-stat">
                                <div class="ma-stat-label">{{ $ui['flowTwo'] }}</div>
                                <div class="ma-stat-value">{{ $ui['flowTwoText'] }}</div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="ma-form-shell overflow-hidden" x-data="mobileRegisterWizard()">
                    <div class="ma-form-header space-y-5 px-5 py-6 sm:px-6">
                        <div>
                            <div class="ma-section-kicker">{{ $ui['formKicker'] }}</div>
                            <h2 class="ma-section-title">{{ $ui['formTitle'] }}</h2>
                            <p class="ma-section-text">{{ $ui['formText'] }}</p>
                        </div>

                        <div class="ma-step-track">
                            <template x-for="(label, index) in steps" :key="index">
                                <div class="ma-step-item">
                                    <div class="ma-step-pill">
                                        <div class="ma-step-dot" :class="currentStep === index + 1 ? 'is-active' : (currentStep > index + 1 ? 'is-done' : '')">
                                            <span x-text="index + 1"></span>
                                        </div>
                                        <div class="ma-step-label" x-text="label"></div>
                                    </div>
                                    <div x-show="index < steps.length - 1" class="ma-step-line" :class="currentStep > index + 1 ? 'is-done' : ''"></div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <form id="mobile-register-form" class="space-y-5 px-5 py-5 sm:px-6 sm:py-6" @submit.prevent="nextStep">
                        <div id="register-errors" class="ma-error hidden"></div>

                        <div class="ma-panel p-4 sm:p-5">
                            <div x-show="currentStep === 1" class="space-y-4">
                                <div>
                                    <div class="ma-section-kicker">{{ $ui['accountBasics'] }}</div>
                                    <p class="mt-2 text-sm leading-7 text-[#5c635c]">{{ $isAr ? 'ابدأ بالاسم الذي سيظهر كصاحب الحساب الرئيسي.' : 'Start with the name that will own the main account.' }}</p>
                                </div>
                                <div>
                                    <label class="ma-label">{{ $ui['name'] }}</label>
                                    <div class="ma-input-wrap">
                                        <div class="ma-input-icon">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0ZM12 14a7 7 0 0 0-7 7h14a7 7 0 0 0-7-7Z"/>
                                            </svg>
                                        </div>
                                        <input
                                            x-model="formData.name"
                                            @input="clearFieldError('name')"
                                            name="name"
                                            class="ma-input"
                                            :class="{ 'is-invalid': stepErrors.name }"
                                            placeholder="{{ $ui['namePlaceholder'] }}"
                                        />
                                    </div>
                                    <p x-show="stepErrors.name" x-text="stepErrors.name" class="ma-field-error"></p>
                                </div>
                            </div>

                            <div x-show="currentStep === 2" x-cloak class="space-y-4">
                                <div>
                                    <div class="ma-section-kicker">{{ $ui['contactStep'] }}</div>
                                    <p class="mt-2 text-sm leading-7 text-[#5c635c]">{{ $isAr ? 'هذا البريد سيكون مفتاح الدخول الرئيسي للوكالة.' : 'This email becomes the main sign-in for the workspace.' }}</p>
                                </div>
                                <div>
                                    <label class="ma-label">{{ $ui['email'] }}</label>
                                    <div class="ma-input-wrap">
                                        <div class="ma-input-icon">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21.75 6.75v10.5A2.25 2.25 0 0 1 19.5 19.5h-15A2.25 2.25 0 0 1 2.25 17.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15A2.25 2.25 0 0 0 2.25 6.75m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
                                            </svg>
                                        </div>
                                        <input
                                            x-model="formData.email"
                                            @input="clearFieldError('email')"
                                            name="email"
                                            type="email"
                                            class="ma-input"
                                            :class="{ 'is-invalid': stepErrors.email }"
                                            placeholder="{{ $ui['emailPlaceholder'] }}"
                                        />
                                    </div>
                                    <p x-show="stepErrors.email" x-text="stepErrors.email" class="ma-field-error"></p>
                                </div>
                            </div>

                            <div x-show="currentStep === 3" x-cloak class="space-y-4">
                                <div>
                                    <div class="ma-section-kicker">{{ $ui['workspaceStep'] }}</div>
                                    <p class="mt-2 text-sm leading-7 text-[#5c635c]">{{ $isAr ? 'اختر اسم الوكالة والنطاق الذي سيظهر في رابط الموقع العام.' : 'Choose the agency name and the subdomain that will appear in the public site URL.' }}</p>
                                </div>

                                <div>
                                    <label class="ma-label">{{ $ui['agency'] }}</label>
                                    <div class="ma-input-wrap">
                                        <div class="ma-input-icon">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5m-4 0h4"/>
                                            </svg>
                                        </div>
                                        <input
                                            x-model="formData.agent"
                                            @input="clearFieldError('agent')"
                                            name="agent"
                                            class="ma-input"
                                            :class="{ 'is-invalid': stepErrors.agent }"
                                            placeholder="{{ $ui['agencyPlaceholder'] }}"
                                        />
                                    </div>
                                    <p x-show="stepErrors.agent" x-text="stepErrors.agent" class="ma-field-error"></p>
                                </div>

                                <div>
                                    <label class="ma-label">{{ $ui['subdomain'] }}</label>
                                    <div class="ma-input-wrap">
                                        <div class="ma-input-icon">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21 12a9 9 0 0 1-9 9m9-9a9 9 0 0 0-9-9m9 9H3m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/>
                                            </svg>
                                        </div>
                                        <input
                                            x-model="formData.subdomain"
                                            @input="sanitizeSubdomain(); clearFieldError('subdomain')"
                                            name="subdomain"
                                            class="ma-input"
                                            :class="{ 'is-invalid': stepErrors.subdomain }"
                                            placeholder="{{ $ui['subdomainPlaceholder'] }}"
                                        />
                                    </div>
                                    <p class="mt-2 text-xs leading-6 text-[#7a766e]">{{ $ui['subdomainHint'] }}</p>
                                    <p x-show="stepErrors.subdomain" x-text="stepErrors.subdomain" class="ma-field-error"></p>
                                </div>
                            </div>

                            <div x-show="currentStep === 4" x-cloak class="space-y-4">
                                <div>
                                    <div class="ma-section-kicker">{{ $ui['securityStep'] }}</div>
                                    <p class="mt-2 text-sm leading-7 text-[#5c635c]">{{ $isAr ? 'أنشئ كلمة مرور قوية للحساب الرئيسي، ثم أكمل لاحقاً بقية إعدادات الوكالة من الداخل.' : 'Create a strong password for the main account, then complete the rest of the agency profile from inside the dashboard.' }}</p>
                                </div>

                                <div class="ma-meter">
                                    <span>{{ $isAr ? 'حساب رئيسي' : 'Primary account' }}</span>
                                    <span>{{ $isAr ? 'دخول المالك' : 'Owner login' }}</span>
                                    <span>{{ $isAr ? 'وصول إلى اللوحة' : 'Dashboard access' }}</span>
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
                                            x-model="formData.password"
                                            @input="clearFieldError('password')"
                                            name="password"
                                            type="password"
                                            class="ma-input"
                                            :class="{ 'is-invalid': stepErrors.password }"
                                            placeholder="{{ $ui['passwordPlaceholder'] }}"
                                        />
                                    </div>
                                    <p x-show="stepErrors.password" x-text="stepErrors.password" class="ma-field-error"></p>
                                </div>

                                <div>
                                    <label class="ma-label">{{ $ui['passwordConfirm'] }}</label>
                                    <div class="ma-input-wrap">
                                        <div class="ma-input-icon">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12.75 11.25 15 15 9.75m5.618-4.016A11.955 11.955 0 0 1 12 2.944a11.955 11.955 0 0 1-8.618 3.04A12.02 12.02 0 0 0 3 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016Z"/>
                                            </svg>
                                        </div>
                                        <input
                                            x-model="formData.password_confirmation"
                                            @input="clearFieldError('password_confirmation')"
                                            type="password"
                                            name="password_confirmation"
                                            class="ma-input"
                                            :class="{ 'is-invalid': stepErrors.password_confirmation }"
                                            placeholder="{{ $ui['passwordConfirmPlaceholder'] }}"
                                        />
                                    </div>
                                    <p x-show="stepErrors.password_confirmation" x-text="stepErrors.password_confirmation" class="ma-field-error"></p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <button type="button" class="ma-secondary" @click="prevStep" :disabled="currentStep === 1">{{ $ui['back'] }}</button>
                            <button type="submit" :disabled="loading" class="ma-submit">
                                <span x-show="!loading" x-text="currentStep === steps.length ? '{{ $ui['createAccount'] }}' : '{{ $ui['continue'] }}'"></span>
                                <span x-show="loading" x-cloak>{{ $ui['creating'] }}</span>
                            </button>
                        </div>

                        <div class="ma-footer-note space-y-3 pt-4 text-sm leading-7 text-[#5a6059]">
                            <p>
                                {{ $ui['alreadyHaveAccount'] }}
                                <a href="{{ route('mobile.login') }}" class="ma-inline-link">{{ $ui['signIn'] }}</a>
                            </p>
                            <p>
                                {{ $ui['needBuyerAccount'] }}
                                <a href="{{ route('mobile.marketplace', ['auth' => 'register']) }}" class="ma-inline-link">{{ $ui['buyerAccount'] }}</a>
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
function mobileRegisterWizard() {
    return {
        currentStep: 1,
        loading: false,
        steps: @json($stepLabels),
        stepErrors: {},
        formData: { name: '', email: '', agent: '', subdomain: '', password: '', password_confirmation: '' },
        translations: @json($clientTranslations),
        clearFieldError(field) {
            if (this.stepErrors[field]) {
                delete this.stepErrors[field];
            }
        },
        sanitizeSubdomain() {
            this.formData.subdomain = this.formData.subdomain
                .toLowerCase()
                .replace(/[^a-z0-9-]/g, '')
                .replace(/-{2,}/g, '-')
                .replace(/^-+|-+$/g, '');
        },
        isValidEmail(value) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
        },
        isValidSubdomain(value) {
            return /^[a-z0-9](?:[a-z0-9-]{1,28}[a-z0-9])?$/.test(value);
        },
        validateStep(step = this.currentStep) {
            const errors = {};

            if (step === 1 && !this.formData.name.trim()) {
                errors.name = this.translations.nameError;
            }

            if (step === 2 && !this.isValidEmail(this.formData.email.trim())) {
                errors.email = this.translations.emailError;
            }

            if (step === 3) {
                if (!this.formData.agent.trim()) {
                    errors.agent = this.translations.agencyError;
                }

                if (!this.isValidSubdomain(this.formData.subdomain.trim())) {
                    errors.subdomain = this.translations.subdomainError;
                }
            }

            if (step === 4) {
                if (this.formData.password.length < 8) {
                    errors.password = this.translations.passwordError;
                }

                if (this.formData.password_confirmation !== this.formData.password || !this.formData.password_confirmation) {
                    errors.password_confirmation = this.translations.passwordConfirmError;
                }
            }

            this.stepErrors = errors;
            return Object.keys(errors).length === 0;
        },
        nextStep() {
            const errBox = document.getElementById('register-errors');
            errBox.classList.add('hidden');
            errBox.innerHTML = '';

            if (!this.validateStep()) {
                return;
            }

            if (this.currentStep < this.steps.length) {
                this.currentStep++;
                return;
            }

            this.submitRegister();
        },
        prevStep() {
            if (this.currentStep > 1) {
                this.currentStep--;
            }
        },
        async submitRegister() {
            this.loading = true;
            const errBox = document.getElementById('register-errors');
            errBox.classList.add('hidden');
            errBox.innerHTML = '';

            try {
                const res = await fetch((window.__AQARI_API_BASE || '') + '/api/mobile/auth/register-business', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(this.formData),
                });
                const json = await res.json();

                if (!res.ok) {
                    const msgs = json.errors ? Object.values(json.errors).flat().join('<br>') : (json.message || 'Registration failed');
                    errBox.innerHTML = msgs;
                    errBox.classList.remove('hidden');
                    this.loading = false;
                    return;
                }

                localStorage.setItem('aqari_mobile_token', json.token);
                if (json.current_tenant?.slug) localStorage.setItem('aqari_mobile_tenant_slug', json.current_tenant.slug);
                if (json.user?.name) localStorage.setItem('aqari_mobile_user_name', json.user.name);
                if (json.tenant_role || json.user?.tenant_role) {
                    localStorage.setItem('aqari_mobile_user_role', json.tenant_role || json.user.tenant_role);
                } else {
                    localStorage.removeItem('aqari_mobile_user_role');
                }

                window.location.href = '{{ route('mobile.dashboard') }}';
            } catch (e) {
                errBox.textContent = this.translations.connectionError;
                errBox.classList.remove('hidden');
                this.loading = false;
            }
        }
    };
}
</script>
@endpush
