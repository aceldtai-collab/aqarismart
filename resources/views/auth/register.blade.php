<x-guest-layout>
    @php
        $isAr = app()->getLocale() === 'ar';
        $stepFromErrors = 1;
        $fieldSteps = [1 => ['name'], 2 => ['email', 'phone'], 3 => ['agent', 'subdomain'], 4 => ['password', 'password_confirmation']];
        foreach ($fieldSteps as $sn => $fields) {
            foreach ($fields as $field) {
                if ($errors->has($field)) {
                    $stepFromErrors = $sn;
                }
            }
        }
        $stepLabels = $isAr ? ['الأساسيات', 'التواصل', 'المساحة', 'الأمان'] : ['Basics', 'Contact', 'Workspace', 'Security'];
        $clientValidationMessages = [
            'name_required' => $isAr ? 'الاسم الكامل مطلوب.' : 'Full name is required.',
            'email_required' => $isAr ? 'البريد الإلكتروني مطلوب.' : 'Email address is required.',
            'email_invalid' => $isAr ? 'أدخل بريداً إلكترونياً صالحاً.' : 'Enter a valid email address.',
            'subdomain_invalid' => $isAr ? 'يمكن أن يحتوي الاسم المختصر على حروف وأرقام وشرطة فقط.' : 'Subdomain can only contain letters, numbers, dashes, and underscores.',
            'subdomain_length' => $isAr ? 'الاسم المختصر يجب ألا يتجاوز 30 حرفاً.' : 'Subdomain must be 30 characters or fewer.',
            'password_required' => $isAr ? 'كلمة المرور مطلوبة.' : 'Password is required.',
            'password_length' => $isAr ? 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.' : 'Password must be at least 8 characters.',
            'password_confirm_required' => $isAr ? 'تأكيد كلمة المرور مطلوب.' : 'Password confirmation is required.',
            'password_confirm_mismatch' => $isAr ? 'تأكيد كلمة المرور غير مطابق.' : 'Password confirmation does not match.',
            'terms_required' => $isAr ? 'يجب الموافقة على الشروط وسياسة الخصوصية.' : 'You must accept the terms and privacy policy.',
        ];
    @endphp

    <div class="mb-7">
        <p class="text-[11px] font-extrabold uppercase tracking-[0.18em] text-[color:var(--market-brass)]">{{ $isAr ? 'إنشاء الحساب' : 'Create Account' }}</p>
        <h2 class="mt-3 text-[28px] font-extrabold tracking-tight leading-tight text-[color:var(--market-ink)]">{{ $isAr ? 'افتح مساحة عملك وابدأ البيع' : 'Open your workspace and start selling' }}</h2>
        <p class="mt-2 text-[14px] leading-7 text-slate-600">{{ $isAr ? 'إعداد سريع لوكالتك حتى تنتقل من التسجيل إلى الواجهة العامة ولوحة التحكم ضمن نفس الرحلة.' : 'A guided setup for your agency so you move from registration into your storefront and dashboard inside one continuous journey.' }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}" novalidate
          x-data="{
            step: {{ $stepFromErrors }},
            totalSteps: 4,
            loading: false,
            showPw: false,
            pw: '',
            hasError: {{ $errors->any() ? 'true' : 'false' }},
            clientErrors: {},
            messages: @js($clientValidationMessages),
            touch(field) {
                if (this.clientErrors[field]) {
                    delete this.clientErrors[field];
                    this.clientErrors = { ...this.clientErrors };
                }
            },
            setError(field, message) {
                this.clientErrors[field] = message;
            },
            focusField(field) {
                queueMicrotask(() => this.$refs[field]?.focus());
            },
            clearStepErrors(step) {
                const fields = {
                    1: ['name'],
                    2: ['email'],
                    3: ['subdomain'],
                    4: ['password', 'password_confirmation', 'terms'],
                }[step] || [];
                fields.forEach((field) => delete this.clientErrors[field]);
                this.clientErrors = { ...this.clientErrors };
            },
            validateStep(step) {
                this.clearStepErrors(step);
                if (step === 1) {
                    const name = this.$refs.name?.value?.trim() || '';
                    if (!name) this.setError('name', this.messages.name_required);
                }
                if (step === 2) {
                    const email = this.$refs.email?.value?.trim() || '';
                    const emailParts = email.split('@');
                    const emailValid = emailParts.length === 2 && emailParts[0] && emailParts[1] && emailParts[1].includes('.');
                    if (!email) {
                        this.setError('email', this.messages.email_required);
                    } else if (!emailValid) {
                        this.setError('email', this.messages.email_invalid);
                    }
                }
                if (step === 3) {
                    const subdomain = this.$refs.subdomain?.value?.trim() || '';
                    if (subdomain && !/^[A-Za-z0-9_-]+$/.test(subdomain)) {
                        this.setError('subdomain', this.messages.subdomain_invalid);
                    } else if (subdomain && subdomain.length > 30) {
                        this.setError('subdomain', this.messages.subdomain_length);
                    }
                }
                if (step === 4) {
                    const password = this.$refs.password?.value || '';
                    const confirmation = this.$refs.password_confirmation?.value || '';
                    const termsAccepted = !!this.$refs.terms?.checked;
                    if (!password) {
                        this.setError('password', this.messages.password_required);
                    } else if (password.length < 8) {
                        this.setError('password', this.messages.password_length);
                    }
                    if (!confirmation) {
                        this.setError('password_confirmation', this.messages.password_confirm_required);
                    } else if (password !== confirmation) {
                        this.setError('password_confirmation', this.messages.password_confirm_mismatch);
                    }
                    if (!termsAccepted) {
                        this.setError('terms', this.messages.terms_required);
                    }
                }
                const firstError = Object.keys(this.clientErrors)[0];
                if (firstError) this.focusField(firstError);
                return !firstError;
            },
            validateAll() {
                for (let current = 1; current <= this.totalSteps; current++) {
                    if (!this.validateStep(current)) {
                        this.step = current;
                        return false;
                    }
                }
                return true;
            },
            next() {
                if (this.validateStep(this.step) && this.step < this.totalSteps) this.step++;
            },
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
          }"
          x-on:submit="if (!validateAll()) { $event.preventDefault(); loading = false; return; } loading = true"
          :class="hasError && 'shake-it'"
          x-init="if (hasError) setTimeout(() => hasError = false, 500)"
          class="space-y-5">
        @csrf

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

        <div x-show="step === 1" x-cloak x-transition:enter="transition ease-out duration-250" x-transition:enter-start="opacity-0 translate-x-3" x-transition:enter-end="opacity-100 translate-x-0">
            <h3 class="mb-4 text-sm font-bold text-[color:var(--market-ink)]">{{ __('Account Basics') }}</h3>
            <div>
                <label for="name" class="mb-1.5 block text-[13px] font-semibold text-[color:var(--market-ink)]">{{ __('Full Name') }}</label>
                <div class="relative">
                    <div class="absolute inset-y-0 ltr:left-0 rtl:right-0 flex items-center pointer-events-none ltr:pl-3 rtl:pr-3">
                        <svg class="h-[17px] w-[17px] text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                    </div>
                    <input x-ref="name" x-on:input="touch('name')" id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" maxlength="255" class="r-input has-icon" placeholder="{{ __('Enter your full name') }}">
                </div>
                <p x-show="clientErrors.name" x-cloak x-text="clientErrors.name" class="mt-1.5 text-sm text-red-600"></p>
                <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
            </div>
            <div class="mt-5">
                <button type="button" x-on:click="next()" class="r-btn r-btn-primary">
                    {{ __('Continue') }}
                    <svg class="h-4 w-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    <div class="shimmer"></div>
                </button>
            </div>
        </div>

        <div x-show="step === 2" x-cloak x-transition:enter="transition ease-out duration-250" x-transition:enter-start="opacity-0 translate-x-3" x-transition:enter-end="opacity-100 translate-x-0">
            <h3 class="mb-4 text-sm font-bold text-[color:var(--market-ink)]">{{ __('Contact Information') }}</h3>
            <div class="space-y-4">
                <div>
                    <label for="email" class="mb-1.5 block text-[13px] font-semibold text-[color:var(--market-ink)]">{{ __('Email Address') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 ltr:left-0 rtl:right-0 flex items-center pointer-events-none ltr:pl-3 rtl:pr-3">
                            <svg class="h-[17px] w-[17px] text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                        </div>
                        <input x-ref="email" x-on:input="touch('email')" id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="r-input has-icon" placeholder="{{ __('you@company.com') }}">
                    </div>
                    <p x-show="clientErrors.email" x-cloak x-text="clientErrors.email" class="mt-1.5 text-sm text-red-600"></p>
                    <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                </div>
                <div>
                    <label for="phone" class="mb-1.5 block text-[13px] font-semibold text-[color:var(--market-ink)]">{{ __('Phone Number') }} <span class="font-normal text-slate-400">({{ __('optional') }})</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 ltr:left-0 rtl:right-0 flex items-center pointer-events-none ltr:pl-3 rtl:pr-3">
                            <svg class="h-[17px] w-[17px] text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                        </div>
                        <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel" class="r-input has-icon" placeholder="{{ __('+962 7X XXX XXXX') }}">
                    </div>
                    <x-input-error :messages="$errors->get('phone')" class="mt-1.5" />
                </div>
            </div>
            <div class="mt-5 flex gap-3">
                <button type="button" x-on:click="prev()" class="r-btn r-btn-outline">{{ __('Back') }}</button>
                <button type="button" x-on:click="next()" class="r-btn r-btn-primary flex-1">
                    {{ __('Continue') }}
                    <svg class="h-4 w-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </button>
            </div>
        </div>

        <div x-show="step === 3" x-cloak x-transition:enter="transition ease-out duration-250" x-transition:enter-start="opacity-0 translate-x-3" x-transition:enter-end="opacity-100 translate-x-0">
            <h3 class="mb-4 text-sm font-bold text-[color:var(--market-ink)]">{{ __('Workspace Setup') }}</h3>
            <div class="space-y-4">
                <div>
                    <label for="agent" class="mb-1.5 block text-[13px] font-semibold text-[color:var(--market-ink)]">{{ __('Organization Name') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 ltr:left-0 rtl:right-0 flex items-center pointer-events-none ltr:pl-3 rtl:pr-3">
                            <svg class="h-[17px] w-[17px] text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                        </div>
                        <input id="agent" type="text" name="agent" value="{{ old('agent') }}" maxlength="255" class="r-input has-icon" placeholder="{{ __('Acme Properties') }}">
                    </div>
                    <x-input-error :messages="$errors->get('agent')" class="mt-1.5" />
                </div>
                <div>
                    <label for="subdomain" class="mb-1.5 block text-[13px] font-semibold text-[color:var(--market-ink)]">{{ __('Subdomain') }}</label>
                    <div class="flex items-center gap-2">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 ltr:left-0 rtl:right-0 flex items-center pointer-events-none ltr:pl-3 rtl:pr-3">
                                <svg class="h-[17px] w-[17px] text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418"/></svg>
                            </div>
                            <input x-ref="subdomain" x-on:input="touch('subdomain')" id="subdomain" type="text" name="subdomain" value="{{ old('subdomain') }}" maxlength="30" pattern="[A-Za-z0-9_-]+" class="r-input has-icon" placeholder="acme">
                        </div>
                        <span class="whitespace-nowrap rounded-2xl border border-[rgba(130,94,38,.16)] bg-[rgba(255,255,255,.84)] px-4 py-3 text-sm font-semibold text-slate-500">.{{ config('tenancy.base_domain') }}</span>
                    </div>
                    <p x-show="clientErrors.subdomain" x-cloak x-text="clientErrors.subdomain" class="mt-1.5 text-sm text-red-600"></p>
                    <x-input-error :messages="$errors->get('subdomain')" class="mt-1.5" />
                </div>
            </div>
            <div class="mt-5 flex gap-3">
                <button type="button" x-on:click="prev()" class="r-btn r-btn-outline">{{ __('Back') }}</button>
                <button type="button" x-on:click="next()" class="r-btn r-btn-primary flex-1">
                    {{ __('Continue') }}
                    <svg class="h-4 w-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </button>
            </div>
        </div>

        <div x-show="step === 4" x-cloak x-transition:enter="transition ease-out duration-250" x-transition:enter-start="opacity-0 translate-x-3" x-transition:enter-end="opacity-100 translate-x-0">
            <h3 class="mb-4 text-sm font-bold text-[color:var(--market-ink)]">{{ __('Set your password') }}</h3>
            <div class="space-y-4">
                <div>
                    <label for="password" class="mb-1.5 block text-[13px] font-semibold text-[color:var(--market-ink)]">{{ __('Password') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 ltr:left-0 rtl:right-0 flex items-center pointer-events-none ltr:pl-3 rtl:pr-3">
                            <svg class="h-[17px] w-[17px] text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                        </div>
                        <input x-ref="password" x-on:input="touch('password'); touch('password_confirmation')" id="password" :type="showPw ? 'text' : 'password'" name="password" required autocomplete="new-password" class="r-input has-icon" style="padding-right:40px" placeholder="{{ __('Min. 8 characters') }}" x-model="pw">
                        <button type="button" x-on:click="showPw = !showPw" class="absolute inset-y-0 ltr:right-0 rtl:left-0 flex items-center text-slate-400 transition-colors hover:text-slate-600 ltr:pr-3 rtl:pl-3" tabindex="-1">
                            <svg x-show="!showPw" class="h-[17px] w-[17px]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <svg x-show="showPw" x-cloak class="h-[17px] w-[17px]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                        </button>
                    </div>
                    <p x-show="clientErrors.password" x-cloak x-text="clientErrors.password" class="mt-1.5 text-sm text-red-600"></p>
                    <div x-show="pw.length > 0" x-cloak class="mt-2">
                        <div class="mb-1 flex gap-1.5">
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
                    <label for="password_confirmation" class="mb-1.5 block text-[13px] font-semibold text-[color:var(--market-ink)]">{{ __('Confirm Password') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 ltr:left-0 rtl:right-0 flex items-center pointer-events-none ltr:pl-3 rtl:pr-3">
                            <svg class="h-[17px] w-[17px] text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                        </div>
                        <input x-ref="password_confirmation" x-on:input="touch('password_confirmation')" id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="r-input has-icon" placeholder="{{ __('Re-enter password') }}">
                    </div>
                    <p x-show="clientErrors.password_confirmation" x-cloak x-text="clientErrors.password_confirmation" class="mt-1.5 text-sm text-red-600"></p>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
                </div>
                <label class="group flex cursor-pointer items-start gap-2.5">
                    <input x-ref="terms" x-on:change="touch('terms')" id="terms" type="checkbox" name="terms" required class="mt-0.5 rounded border-slate-300 transition" style="color:var(--market-palm)">
                    <span class="text-sm text-slate-500 transition-colors group-hover:text-slate-700">{{ __('I agree to the') }} <a href="#" class="font-medium hover:underline" style="color:var(--market-palm)">{{ __('Terms') }}</a> {{ __('and') }} <a href="#" class="font-medium hover:underline" style="color:var(--market-palm)">{{ __('Privacy Policy') }}</a></span>
                </label>
                <p x-show="clientErrors.terms" x-cloak x-text="clientErrors.terms" class="mt-1.5 text-sm text-red-600"></p>
            </div>
            <div class="mt-5 flex gap-3">
                <button type="button" x-on:click="prev()" class="r-btn r-btn-outline">{{ __('Back') }}</button>
                <button type="submit" class="r-btn r-btn-primary flex-1" :disabled="loading">
                    <template x-if="!loading">
                        <span class="flex items-center gap-2">
                            {{ __('Create Account') }}
                            <svg class="h-4 w-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        </span>
                    </template>
                    <template x-if="loading">
                        <span class="flex items-center gap-2">
                            <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            {{ $isAr ? 'جارٍ الإنشاء...' : 'Creating...' }}
                        </span>
                    </template>
                    <div class="shimmer"></div>
                </button>
            </div>
        </div>

        <p class="pt-2 text-center text-sm text-slate-500">
            {{ __('Already have an account?') }}
            <a href="{{ route('login') }}" class="font-semibold transition-colors hover:underline ltr:ml-1 rtl:mr-1" style="color:var(--market-palm)">{{ __('Sign in') }}</a>
        </p>
    </form>
</x-guest-layout>
