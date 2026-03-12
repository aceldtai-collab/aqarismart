<x-guest-layout>
    @php $isAr = app()->getLocale() === 'ar'; @endphp

    <div class="mb-7">
        <h2 class="text-[24px] font-extrabold tracking-tight leading-tight" style="color:var(--dark)">{{ __('Reset your password') }}</h2>
        <p class="text-[14px] text-slate-500 mt-1.5">{{ __('Choose a strong password for your account') }}</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4"
          x-data="{
              showPw: false, loading: false, pw: '',
              hasError: {{ $errors->any() ? 'true' : 'false' }},
              get pwStrength() { let s=0; if(this.pw.length>=8)s++; if(/[A-Z]/.test(this.pw))s++; if(/[0-9]/.test(this.pw))s++; if(/[^A-Za-z0-9]/.test(this.pw))s++; return s; },
              get pwLabel() { const l={!! $isAr ? "['ضعيفة','متوسطة','جيدة','قوية']" : "['Weak','Fair','Good','Strong']" !!}; return this.pw.length ? l[Math.max(0,this.pwStrength-1)]||l[0] : ''; },
              get pwColor() { return ['bg-red-400','bg-amber-400','bg-emerald-400','bg-emerald-500'][Math.max(0,this.pwStrength-1)]||'bg-slate-200'; }
          }"
          x-on:submit="loading = true" :class="hasError && 'shake-it'" x-init="if(hasError) setTimeout(() => hasError = false, 500)">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <label for="email" class="block text-[13px] font-semibold mb-1.5" style="color:var(--dark)">{{ __('Email address') }}</label>
            <div class="relative">
                <div class="absolute inset-y-0 ltr:left-0 rtl:right-0 ltr:pl-3 rtl:pr-3 flex items-center pointer-events-none">
                    <svg class="w-[17px] h-[17px] text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                </div>
                <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" class="auth-input has-icon-left" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div>
            <label for="password" class="block text-[13px] font-semibold mb-1.5" style="color:var(--dark)">{{ __('New Password') }}</label>
            <div class="relative">
                <div class="absolute inset-y-0 ltr:left-0 rtl:right-0 ltr:pl-3 rtl:pr-3 flex items-center pointer-events-none">
                    <svg class="w-[17px] h-[17px] text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                </div>
                <input id="password" :type="showPw ? 'text' : 'password'" name="password" required autocomplete="new-password" class="auth-input has-icon-left has-icon-right" placeholder="{{ __('Min. 8 characters') }}" x-model="pw" />
                <button type="button" x-on:click="showPw = !showPw" class="absolute inset-y-0 ltr:right-0 rtl:left-0 ltr:pr-3 rtl:pl-3 flex items-center text-slate-400 hover:text-slate-600 transition-colors" tabindex="-1">
                    <svg x-show="!showPw" class="w-[17px] h-[17px]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <svg x-show="showPw" x-cloak class="w-[17px] h-[17px]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                </button>
            </div>
            <div x-show="pw.length > 0" x-cloak class="mt-2">
                <div class="flex gap-1.5 mb-1">
                    <template x-for="bar in 4" :key="bar">
                        <div class="h-[3px] rounded-full flex-1 transition-all duration-300" :class="pwStrength >= bar ? pwColor : 'bg-slate-200'"></div>
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
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="auth-input has-icon-left" placeholder="{{ __('Re-enter password') }}" />
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
        </div>

        <button type="submit" class="auth-btn auth-btn-primary" :disabled="loading">
            <template x-if="!loading">
                <span class="flex items-center gap-2">{{ __('Reset Password') }}<svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg></span>
            </template>
            <template x-if="loading">
                <span class="flex items-center gap-2"><svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>{{ $isAr ? 'جارٍ إعادة التعيين...' : 'Resetting...' }}</span>
            </template>
            <div class="shimmer"></div>
        </button>
    </form>
</x-guest-layout>
