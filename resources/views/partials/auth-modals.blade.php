{{-- ═══ Login Modal ═══ --}}
@php
    $tenantCtx = app(\App\Services\Tenancy\TenantManager::class)->tenant();
    $countryCodes = config('phone.codes', []);
    $defaultCountry = config('phone.default', '+962');
    if (empty($countryCodes)) {
        $countryCodes = [$defaultCountry => $defaultCountry];
    }
@endphp
<div x-data x-show="$store.auth.login" x-cloak
     class="fixed inset-0 z-[60] flex items-center justify-center px-4"
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="$store.auth.login = false"></div>
    {{-- Panel --}}
    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl p-6 sm:p-8 z-10"
         x-transition:enter="transition ease-out duration-200 delay-75" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0">
        <button type="button" @click="$store.auth.login = false" class="absolute top-4 {{ app()->getLocale()==='ar' ? 'left-4' : 'right-4' }} text-gray-400 hover:text-gray-600 transition">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <h2 class="text-xl font-bold text-gray-900 mb-1">{{ __('Sign In') }}</h2>
        <p class="text-sm text-gray-500 mb-6">{{ __('Sign in to your account') }}</p>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="modal-login-email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Email') }}</label>
                    <input id="modal-login-email" type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-3 px-4">
                    @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    @error('login')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="modal-login-password" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Password') }}</label>
                    <input id="modal-login-password" type="password" name="password" required
                           class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-3 px-4">
                    @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-gray-600">{{ __('Remember me') }}</span>
                    </label>
                    @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:text-indigo-500">{{ __('Forgot password?') }}</a>
                    @endif
                </div>
                <button type="submit"
                        class="w-full rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-3 text-sm font-semibold text-white shadow-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200">
                    {{ __('Sign In') }}
                </button>
            </div>
        </form>

        <p class="mt-5 text-center text-sm text-gray-500">
            {{ __("Don't have an account?") }}
            <button type="button" @click="$store.auth.login = false; $store.auth.register = true" class="font-semibold text-indigo-600 hover:text-indigo-500">{{ __('Create account') }}</button>
        </p>
    </div>
</div>

{{-- ═══ Register Modal (Resident) ═══ --}}
<div x-data x-show="$store.auth.register" x-cloak
     class="fixed inset-0 z-[60] flex items-center justify-center px-4"
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="$store.auth.register = false"></div>
    {{-- Panel --}}
    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl p-6 sm:p-8 z-10 max-h-[90vh] overflow-y-auto"
         x-transition:enter="transition ease-out duration-200 delay-75" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0">
        <button type="button" @click="$store.auth.register = false" class="absolute top-4 {{ app()->getLocale()==='ar' ? 'left-4' : 'right-4' }} text-gray-400 hover:text-gray-600 transition">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <h2 class="text-xl font-bold text-gray-900 mb-1">{{ __('Create Account') }}</h2>
        <p class="text-sm text-gray-500 mb-6">{{ __('Register as a resident') }}</p>

        <div id="modal-reg-error" class="hidden mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700"></div>

        <form id="modal-register-form" x-data="{ loading: false }" @submit.prevent="
            loading = true;
            const form = $el;
            const err = document.getElementById('modal-reg-error');
            err.classList.add('hidden'); err.textContent = '';
            fetch('/api/mobile/auth/register-resident', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({
                    name: form.querySelector('[name=name]').value,
                    country_code: form.querySelector('[name=country_code]').value,
                    phone: form.querySelector('[name=phone]').value,
                    email: form.querySelector('[name=email]').value || null,
                    password: form.querySelector('[name=password]').value,
                    password_confirmation: form.querySelector('[name=password_confirmation]').value,
                })
            }).then(r => r.json().then(json => {
                if (!r.ok) {
                    const msgs = json.errors ? Object.values(json.errors).flat().join(' | ') : (json.message || 'Registration failed');
                    err.textContent = msgs; err.classList.remove('hidden'); loading = false; return;
                }
                localStorage.setItem('aqari_mobile_token', json.token);
                if (json.user?.name) localStorage.setItem('aqari_mobile_user_name', json.user.name);
                window.location.href = '/mobile/my-listings/create';
            })).catch(() => { err.textContent = 'Connection error. Please try again.'; err.classList.remove('hidden'); loading = false; });
        ">
            <div class="space-y-4">
                <div>
                    <label for="modal-reg-name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Full Name') }}</label>
                    <input id="modal-reg-name" type="text" name="name" required
                           class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-3 px-4">
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label for="modal-reg-cc" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Code') }}</label>
                        <select id="modal-reg-cc" name="country_code" required
                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-3 px-2">
                            @foreach($countryCodes as $code => $label)
                                <option value="{{ $code }}" {{ $defaultCountry === $code ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label for="modal-reg-phone" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Phone') }}</label>
                        <input id="modal-reg-phone" type="tel" name="phone" required
                               class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-3 px-4">
                    </div>
                </div>
                <div>
                    <label for="modal-reg-email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Email') }} <span class="text-gray-400">({{ __('optional') }})</span></label>
                    <input id="modal-reg-email" type="email" name="email"
                           class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-3 px-4">
                </div>
                <div>
                    <label for="modal-reg-pass" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Password') }}</label>
                    <input id="modal-reg-pass" type="password" name="password" required
                           class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-3 px-4">
                </div>
                <div>
                    <label for="modal-reg-pass-c" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Confirm Password') }}</label>
                    <input id="modal-reg-pass-c" type="password" name="password_confirmation" required
                           class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-3 px-4">
                </div>
                <button type="submit" :disabled="loading"
                        class="w-full rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-3 text-sm font-semibold text-white shadow-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 disabled:opacity-60">
                    <span x-show="!loading">{{ __('Create Account') }}</span>
                    <span x-show="loading" x-cloak>{{ __('Creating...') }}</span>
                </button>
            </div>
        </form>

        <p class="mt-5 text-center text-sm text-gray-500">
            {{ __('Already have an account?') }}
            <button type="button" @click="$store.auth.register = false; $store.auth.login = true" class="font-semibold text-indigo-600 hover:text-indigo-500">{{ __('Sign In') }}</button>
        </p>
    </div>
</div>
