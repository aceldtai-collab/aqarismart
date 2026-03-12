<x-guest-layout>
    @php $isAr = app()->getLocale() === 'ar'; @endphp

    <div class="mb-7">
        <h2 class="text-[24px] font-extrabold tracking-tight leading-tight" style="color:var(--dark)">{{ __('Forgot password?') }}</h2>
        <p class="text-[14px] text-slate-500 mt-1.5">{{ __('Enter your email and we\'ll send you a link to reset your password.') }}</p>
    </div>

    @if (session('status'))
        <div class="mb-5 rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700 flex items-start gap-3" style="animation:fade-in-up .4s ease-out both">
            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
            <span>{{ $isAr ? 'تم إرسال رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني.' : 'We\'ve sent a password reset link to your email address.' }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4"
          x-data="{ loading: false, hasError: {{ $errors->any() ? 'true' : 'false' }} }"
          x-on:submit="loading = true"
          :class="hasError && 'shake-it'"
          x-init="if(hasError) setTimeout(() => hasError = false, 500)">
        @csrf
        <div>
            <label for="email" class="block text-[13px] font-semibold mb-1.5" style="color:var(--dark)">{{ __('Email address') }}</label>
            <div class="relative">
                <div class="absolute inset-y-0 ltr:left-0 rtl:right-0 ltr:pl-3 rtl:pr-3 flex items-center pointer-events-none">
                    <svg class="w-[17px] h-[17px] text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                </div>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="auth-input has-icon-left" placeholder="{{ __('you@company.com') }}" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <button type="submit" class="auth-btn auth-btn-primary" :disabled="loading">
            <template x-if="!loading">
                <span class="flex items-center gap-2">
                    {{ __('Send Reset Link') }}
                    <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
                </span>
            </template>
            <template x-if="loading">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    {{ $isAr ? 'جارٍ الإرسال...' : 'Sending...' }}
                </span>
            </template>
            <div class="shimmer"></div>
        </button>

        <p class="text-center text-sm text-slate-500 pt-2">
            <a href="{{ route('login') }}" class="font-semibold hover:underline transition-colors inline-flex items-center gap-1.5" style="color:var(--brand)">
                <svg class="w-3.5 h-3.5 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                {{ __('Back to sign in') }}
            </a>
        </p>
    </form>
</x-guest-layout>
