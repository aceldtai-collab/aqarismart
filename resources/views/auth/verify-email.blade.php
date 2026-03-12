<x-guest-layout>
    @php $isAr = app()->getLocale() === 'ar'; @endphp

    <div class="mb-7">
        <h2 class="text-[24px] font-extrabold tracking-tight leading-tight" style="color:var(--dark)">{{ __('Verify your email') }}</h2>
        <p class="text-[14px] text-slate-500 mt-1.5 leading-relaxed">{{ __('Thanks for signing up! Please verify your email address by clicking the link we just sent you.') }}</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-5 rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700 flex items-start gap-3" style="animation:fade-in-up .4s ease-out both">
            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
            <span>{{ $isAr ? 'تم إرسال رابط تحقق جديد إلى بريدك الإلكتروني.' : 'A new verification link has been sent to your email address.' }}</span>
        </div>
    @endif

    <div class="rounded-lg bg-slate-50 border border-slate-200 p-4 mb-5">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-slate-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
            <div class="text-sm text-slate-600 leading-relaxed">
                {{ $isAr ? 'تحقق من صندوق الوارد (والبريد غير المرغوب فيه) بحثًا عن رسالة من RentoJo.' : 'Check your inbox (and spam folder) for an email from RentoJo.' }}
            </div>
        </div>
    </div>

    <div class="flex items-center gap-4" x-data="{ cooldown: 0, loading: false }" x-init="@if(session('status') == 'verification-link-sent') cooldown = 60; let t = setInterval(() => { cooldown--; if(cooldown <= 0) clearInterval(t); }, 1000); @endif">
        <form method="POST" action="{{ route('verification.send') }}" class="flex-1" x-on:submit="loading = true; cooldown = 60; let t = setInterval(() => { cooldown--; if(cooldown <= 0) clearInterval(t); }, 1000);">
            @csrf
            <button type="submit" class="auth-btn auth-btn-primary w-full" :disabled="loading || cooldown > 0">
                <template x-if="loading && cooldown <= 1">
                    <span class="flex items-center gap-2"><svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>{{ $isAr ? 'جارٍ الإرسال...' : 'Sending...' }}</span>
                </template>
                <template x-if="cooldown > 0">
                    <span class="flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $isAr ? 'إعادة الإرسال خلال' : 'Resend in' }} <span x-text="cooldown + 's'"></span></span>
                </template>
                <template x-if="!loading && cooldown <= 0">
                    <span class="flex items-center gap-2">{{ __('Resend Email') }}<svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg></span>
                </template>
                <div class="shimmer"></div>
            </button>
        </form>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm font-semibold text-slate-500 hover:text-slate-700 transition-colors px-4 py-3">{{ __('Log Out') }}</button>
        </form>
    </div>
</x-guest-layout>
