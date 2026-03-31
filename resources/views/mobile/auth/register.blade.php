@extends('mobile.layouts.app')

@section('content')
    @php
        $locale = app()->getLocale() === 'ar' ? 'ar' : 'en';
        $authData = config('mobiletestdata.auth.register', []);
        $stepLabels = collect($authData['steps'] ?? [])->map(fn ($step) => $step[$locale] ?? ($step['en'] ?? 'Step'))->values()->all();
        $createAccountText = $locale === 'ar' ? 'إنشاء حساب' : 'Create account';
        $continueText = $locale === 'ar' ? 'التالي' : 'Continue';
        $backText = $locale === 'ar' ? 'السابق' : 'Back';
    @endphp

    <div class="mx-auto grid max-w-5xl gap-6 lg:grid-cols-[1fr_1.05fr]">
        <section class="overflow-hidden rounded-[0.5rem] bg-emerald-700 text-white shadow-xl">
            <div class="space-y-6 px-6 py-7 sm:px-8">
                <div class="inline-flex items-center rounded-full bg-white/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-white/80">
                    {{'Aqari Smart'}}
                </div>
                <div class="space-y-2">
                    <h1 class="text-3xl font-semibold leading-tight">{{ data_get($authData, 'title.' . $locale, 'Create your workspace') }}</h1>
                    <p class="text-sm leading-6 text-white/75">{{ data_get($authData, 'subtitle.' . $locale, 'A guided setup built for real-estate teams.') }}</p>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-[1.5rem] bg-white/10 p-4 backdrop-blur">
                        <div class="text-xs uppercase tracking-[0.24em] text-white/60">{{ app()->getLocale() === 'ar' ? 'تجربة حديثة' : 'Modern flow' }}</div>
                        <div class="mt-2 text-lg font-semibold">{{ app()->getLocale() === 'ar' ? 'خطوات واضحة وسريعة' : 'Clear multi-step onboarding' }}</div>
                    </div>
                    <div class="rounded-[1.5rem] bg-white/10 p-4 backdrop-blur">
                        <div class="text-xs uppercase tracking-[0.24em] text-white/60">{{ app()->getLocale() === 'ar' ? 'تجربة مجانية' : 'Free trial' }}</div>
                        <div class="mt-2 text-lg font-semibold">{{ app()->getLocale() === 'ar' ? '14 يومًا للمساحات الجديدة' : '14 days for new workspaces' }}</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-emerald-700 rounded-[0.5rem] shadow-xl text-white" x-data="mobileRegisterWizard()">
            <div class="px-6 space-y-6 py-7">
                <div class="text-center">
                    <h1 class="text-2xl font-bold text-white">{{ app()->getLocale() === 'ar' ? 'إنشاء حساب جديد' : 'Create Account' }}</h1>
                    <p class="text-sm text-white/70 mt-2">{{ app()->getLocale() === 'ar' ? 'انضم إلى عقاري سمارت' : 'Join Aqari Smart' }}</p>
                </div>
                
                <div class="flex items-start">
                    <template x-for="(label, index) in steps" :key="index">
                        <div class="flex items-start" :class="index < steps.length - 1 ? 'flex-1' : ''">
                            <div class="flex flex-col items-center gap-2">
                                <div class="flex h-9 w-9 items-center justify-center rounded-full border-2 text-xs font-bold transition" :class="currentStep === index + 1 ? 'border-white bg-white text-emerald-700' : currentStep > index + 1 ? 'border-white bg-white text-emerald-700' : 'border-white/30 bg-white/10 text-white/60'">
                                    <span x-text="index + 1"></span>
                                </div>
                                <span class="text-[11px] font-medium text-white/70" x-text="label"></span>
                            </div>
                            <div x-show="index < steps.length - 1" class="mt-4 h-0.5 flex-1 bg-white/30" :class="currentStep > index + 1 ? 'bg-white' : ''"></div>
                        </div>
                    </template>
                </div>
            </div>

            <form id="mobile-register-form" @submit.prevent="nextStep">
        
        <div class="space-y-5 px-6 py-7">
            <div id="register-errors" class="hidden rounded-xl bg-red-500/20 border border-red-400/30 px-4 py-3 text-sm text-white"></div>

            <div x-show="currentStep === 1" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-white/80 mb-2">{{ app()->getLocale() === 'ar' ? 'الاسم الكامل' : 'Full name' }}</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <svg class="h-5 w-5 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <input x-model="formData.name" name="name" class="block w-full appearance-none rounded-xl border border-white/20 bg-white/20 backdrop-blur-sm py-3.5 pl-12 pr-4 text-white placeholder-white/60 focus:bg-white/30 focus:border-white/40 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white/30 sm:text-sm sm:leading-6" placeholder="{{ app()->getLocale() === 'ar' ? 'اسم المالك' : 'Owner name' }}" />
                    </div>
                </div>
            </div>

                <div x-show="currentStep === 2" class="space-y-4" x-cloak>
                <div>
                    <label class="block text-sm font-medium text-white/80 mb-2">{{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email' }}</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <svg class="h-5 w-5 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <input x-model="formData.email" name="email" type="email" class="block w-full appearance-none rounded-xl border border-white/20 bg-white/20 backdrop-blur-sm py-3.5 pl-12 pr-4 text-white placeholder-white/60 focus:bg-white/30 focus:border-white/40 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white/30 sm:text-sm sm:leading-6" placeholder="{{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email address' }}" />
                    </div>
                </div>
            </div>

                <div x-show="currentStep === 3" class="space-y-4" x-cloak>
                <div>
                    <label class="block text-sm font-medium text-white/80 mb-2">{{ app()->getLocale() === 'ar' ? 'اسم المنشأة / المكتب' : 'Workspace / agency name' }}</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <svg class="h-5 w-5 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <input x-model="formData.agent" name="agent" class="block w-full appearance-none rounded-xl border border-white/20 bg-white/20 backdrop-blur-sm py-3.5 pl-12 pr-4 text-white placeholder-white/60 focus:bg-white/30 focus:border-white/40 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white/30 sm:text-sm sm:leading-6" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-white/80 mb-2">{{ app()->getLocale() === 'ar' ? 'المعرّف الفرعي' : 'Subdomain' }}</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <svg class="h-5 w-5 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                            </svg>
                        </div>
                        <input x-model="formData.subdomain" name="subdomain" class="block w-full appearance-none rounded-xl border border-white/20 bg-white/20 backdrop-blur-sm py-3.5 pl-12 pr-4 text-white placeholder-white/60 focus:bg-white/30 focus:border-white/40 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white/30 sm:text-sm sm:leading-6" placeholder="{{ app()->getLocale() === 'ar' ? 'النطاق الفرعي' : 'Subdomain' }}" />
                    </div>
                </div>
            </div>

                <div x-show="currentStep === 4" class="space-y-4" x-cloak>
                <div>
                    <label class="block text-sm font-medium text-white/80 mb-2">{{ app()->getLocale() === 'ar' ? 'كلمة المرور' : 'Password' }}</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <svg class="h-5 w-5 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input x-model="formData.password" name="password" type="password" class="block w-full appearance-none rounded-xl border border-white/20 bg-white/20 backdrop-blur-sm py-3.5 pl-12 pr-4 text-white placeholder-white/60 focus:bg-white/30 focus:border-white/40 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white/30 sm:text-sm sm:leading-6" placeholder="{{ app()->getLocale() === 'ar' ? 'كلمة المرور' : 'Password' }}" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-white/80 mb-2">{{ app()->getLocale() === 'ar' ? 'تأكيد كلمة المرور' : 'Confirm password' }}</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <svg class="h-5 w-5 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <input x-model="formData.password_confirmation" type="password" name="password_confirmation" class="block w-full appearance-none rounded-xl border border-white/20 bg-white/20 backdrop-blur-sm py-3.5 pl-12 pr-4 text-white placeholder-white/60 focus:bg-white/30 focus:border-white/40 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white/30 sm:text-sm sm:leading-6" />
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="button" class="w-full rounded-xl bg-white/10 backdrop-blur-sm text-white px-4 py-3 text-sm font-semibold shadow-lg transition hover:bg-white/20 hover:shadow-xl" @click="prevStep">{{ $backText }}</button>
                <button type="submit" :disabled="loading" class="w-full rounded-xl bg-white text-emerald-700 px-4 py-3 text-sm font-semibold shadow-lg transition hover:bg-white/90 hover:shadow-xl disabled:opacity-50">
                    <span x-show="!loading" x-text="currentStep === steps.length ? '{{ $createAccountText }}' : '{{ $continueText }}'"></span>
                    <span x-show="loading" x-cloak>{{ app()->getLocale() === 'ar' ? 'جاري الإنشاء...' : 'Creating...' }}</span>
                </button>
            </div>
        </div>
    </form>
        </section>
    </div>
@endsection

@push('scripts')
<script>
function mobileRegisterWizard() {
    return {
        currentStep: 1,
        loading: false,
        steps: @json($stepLabels),
        formData: { name: '', email: '', agent: '', subdomain: '', password: '', password_confirmation: '' },
        nextStep() {
            const errBox = document.getElementById('register-errors');
            errBox.classList.add('hidden');

            const stepEl = this.$el.querySelector(`[x-show="currentStep === ${this.currentStep}"]`);
            if (stepEl) {
                const inputs = stepEl.querySelectorAll('input');
                let valid = true;
                inputs.forEach(input => {
                    if (!input.value.trim()) {
                        valid = false;
                        input.classList.add('border-2', 'border-red-400');
                        setTimeout(() => input.classList.remove('border-2', 'border-red-400'), 3000);
                    }
                });
                if (!valid) return;
            }

            if (this.currentStep < this.steps.length) {
                this.currentStep++;
            } else {
                this.submitRegister();
            }
        },
        prevStep() {
            if (this.currentStep > 1) this.currentStep--;
        },
        async submitRegister() {
            this.loading = true;
            const errBox = document.getElementById('register-errors');
            errBox.classList.add('hidden');

            try {
                const res = await fetch('/api/mobile/auth/register-business', {
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

                window.location.href = '{{ route('mobile.dashboard') }}';
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
