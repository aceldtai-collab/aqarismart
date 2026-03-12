<x-guest-layout>
    @php $isAr = app()->getLocale() === 'ar'; @endphp

    <div class="mb-7">
        <h2 class="text-[24px] font-extrabold tracking-tight leading-tight" style="color:var(--dark)">{{ __('Welcome back') }}</h2>
        <p class="text-[14px] text-slate-500 mt-1.5">{{ __('Sign in to your property management dashboard') }}</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4"
          x-data="{ showPw: false, loading: false, hasError: {{ $errors->any() ? 'true' : 'false' }} }"
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
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                       class="auth-input has-icon-left" placeholder="{{ __('you@company.com') }}" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-[13px] font-semibold" style="color:var(--dark)">{{ __('Password') }}</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-[12px] font-medium hover:underline transition-colors" style="color:var(--brand)">{{ __('Forgot password?') }}</a>
                @endif
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 ltr:left-0 rtl:right-0 ltr:pl-3 rtl:pr-3 flex items-center pointer-events-none">
                    <svg class="w-[17px] h-[17px] text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                </div>
                <input id="password" :type="showPw ? 'text' : 'password'" name="password" required autocomplete="current-password"
                       class="auth-input has-icon-left has-icon-right" placeholder="{{ __('Enter your password') }}" />
                <button type="button" x-on:click="showPw = !showPw" class="absolute inset-y-0 ltr:right-0 rtl:left-0 ltr:pr-3 rtl:pl-3 flex items-center text-slate-400 hover:text-slate-600 transition-colors" tabindex="-1">
                    <svg x-show="!showPw" class="w-[17px] h-[17px]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <svg x-show="showPw" x-cloak class="w-[17px] h-[17px]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <div class="flex items-center">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <input id="remember_me" type="checkbox" name="remember" class="rounded border-slate-300 transition" style="color:var(--brand)">
                <span class="ltr:ml-2 rtl:mr-2 text-sm text-slate-500 group-hover:text-slate-700 transition-colors">{{ __('Remember me') }}</span>
            </label>
        </div>

        <button type="submit" class="auth-btn auth-btn-primary" :disabled="loading">
            <template x-if="!loading">
                <span class="flex items-center gap-2">
                    {{ __('Sign in') }}
                    <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </span>
            </template>
            <template x-if="loading">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    {{ $isAr ? 'جارٍ تسجيل الدخول...' : 'Signing in...' }}
                </span>
            </template>
            <div class="shimmer"></div>
        </button>

        @if (Route::has('register'))
            <p class="text-center text-sm text-slate-500 pt-2">
                {{ __("Don't have an account?") }}
                <a href="{{ route('register') }}" class="font-semibold ltr:ml-1 rtl:mr-1 hover:underline transition-colors" style="color:var(--brand)">{{ __('Create one') }}</a>
            </p>
        @endif
    </form>
</x-guest-layout>
