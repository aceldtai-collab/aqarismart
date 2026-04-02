@extends('mobile.layouts.app', ['title' => $tenant->name])

@section('full_width'){{-- edge-to-edge --}}@endsection
@section('content')
@php
    $settings  = is_array($tenant->settings ?? null) ? $tenant->settings : [];
    $logo      = $settings['logo_url'] ?? null;
    $desc      = $settings['about']['description'] ?? $settings['description'] ?? $settings['public_description'] ?? null;
    $phone     = $settings['phone'] ?? null;
    $email     = $settings['email'] ?? null;
    $address   = $settings['address'] ?? null;
    $headerBg  = $settings['header_bg_url'] ?? null;
    $isAr      = app()->getLocale() === 'ar';
@endphp

{{-- ═══ 1. Hero Header ═══ --}}
<header class="relative isolate flex items-center justify-center overflow-hidden min-h-[52vh] px-5"
    style="{{ $headerBg
        ? "background-image:url('" . (Str::startsWith($headerBg, ['http://','https://']) ? $headerBg : asset($headerBg)) . "');background-size:cover;background-position:center;"
        : 'background:linear-gradient(135deg,#059669,#047857);' }}">
    <div class="absolute inset-0 bg-black/30"></div>
    <div class="absolute inset-0 opacity-5" style="background-image:radial-gradient(white 1px,transparent 1px);background-size:24px 24px;"></div>

    <div class="relative z-10 w-full max-w-md mx-auto text-center text-white space-y-4">
        {{-- Logo + Name --}}
        <div class="flex flex-col items-center gap-3">
            <div class="h-16 w-16 overflow-hidden rounded-2xl bg-white/20 ring-2 ring-white/30 shadow-lg">
                @if($logo)
                    <img src="{{ $logo }}" alt="{{ $tenant->name }}" class="h-full w-full object-cover">
                @else
                    <div class="flex h-full w-full items-center justify-center text-xl font-bold text-white/90">{{ strtoupper(substr($tenant->name, 0, 2)) }}</div>
                @endif
            </div>
            <div>
                <h1 class="text-2xl font-bold drop-shadow-lg">{{ $tenant->name }}</h1>
                @if($desc)
                    <p class="mt-1 text-sm text-white/80 line-clamp-2 max-w-xs mx-auto">{{ $desc }}</p>
                @endif
            </div>
        </div>

        {{-- Search --}}
        <form id="tenant-search-form" class="relative w-full">
            <div class="pointer-events-none absolute inset-y-0 {{ $isAr ? 'right-3.5' : 'left-3.5' }} flex items-center">
                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input type="search" name="q" id="tenant-search-q"
                   placeholder="{{ $isAr ? 'ابحث عن العقارات...' : 'Search listings...' }}"
                   class="block w-full rounded-2xl border-0 bg-white/95 backdrop-blur-sm py-3 {{ $isAr ? 'pr-10 pl-4' : 'pl-10 pr-4' }} text-sm text-slate-900 ring-1 ring-white/20 placeholder:text-slate-400 focus:ring-2 focus:ring-white/50 shadow-xl">
        </form>

        {{-- Listing type toggle --}}
        <div class="inline-flex rounded-full bg-white/15 backdrop-blur-sm p-1 ring-1 ring-white/20">
            <button type="button" class="th-listing-toggle rounded-full px-5 py-2 text-xs font-bold transition bg-white text-emerald-800 shadow-sm" data-value="rent">{{ $isAr ? 'إيجار' : 'Rent' }}</button>
            <button type="button" class="th-listing-toggle rounded-full px-5 py-2 text-xs font-bold transition text-white/70" data-value="sale">{{ $isAr ? 'شراء' : 'Buy' }}</button>
        </div>

        {{-- Staff Login Button (only for non-authed users) --}}
        <div id="th-guest-actions" class="flex gap-3 w-full max-w-xs" style="display:none;">
            <button type="button" id="th-login-btn" class="flex-1 rounded-xl bg-white/10 backdrop-blur-sm border border-white/30 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/20">
                {{ $isAr ? 'تسجيل الدخول' : 'Staff Login' }}
            </button>
        </div>
    </div>
</header>

{{-- ═══ 2. Stats Pill ═══ --}}
<div class="flex items-center justify-center -mt-5 relative z-20 px-5">
    <div class="inline-flex items-center gap-3 rounded-full bg-white px-5 py-2.5 shadow-lg ring-1 ring-slate-200">
        <div class="flex items-center gap-2">
            <div class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></div>
            <span class="text-xs font-semibold text-emerald-700">{{ $isAr ? 'تحديثات مباشرة' : 'Live Updates' }}</span>
        </div>
        <div class="h-3.5 w-px bg-slate-200"></div>
        <span id="th-units-total" class="text-xs font-bold text-slate-900">—</span>
    </div>
</div>

{{-- ═══ 3. Featured Units ═══ --}}
<section class="mt-6 px-5">
    <h2 class="text-lg font-bold text-slate-800">{{ $isAr ? 'أحدث قوائم العقارات' : 'Latest listings' }}</h2>
    <p class="mt-0.5 text-xs font-medium text-slate-400">{{ $isAr ? 'اكتشف العقارات المتاحة' : 'Discover available properties' }}</p>
    <div id="th-featured" class="mt-4 grid grid-cols-2 gap-3">
        {{-- Loading skeletons --}}
        @for($i = 0; $i < 4; $i++)
        <div class="animate-pulse rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="aspect-[16/10] bg-slate-200 rounded-t-2xl"></div>
            <div class="p-3 space-y-2"><div class="h-3 bg-slate-200 rounded w-3/4"></div><div class="h-2 bg-slate-100 rounded w-1/2"></div></div>
        </div>
        @endfor
    </div>
</section>

{{-- ═══ 4. All Units Feed ═══ --}}
<section class="mt-8 px-5">
    <h2 class="text-lg font-bold text-slate-800">{{ $isAr ? 'جميع العقارات' : 'All properties' }}</h2>
    <p class="mt-0.5 text-xs font-medium text-slate-400 mb-4">{{ $isAr ? 'تصفح جميع العقارات المتاحة' : 'Browse all available properties' }}</p>
    <div id="th-units-feed" class="grid grid-cols-2 gap-3"></div>
    <div id="th-load-more" class="mt-4 hidden">
        <button type="button" id="th-load-more-btn" class="w-full rounded-2xl bg-emerald-50 py-3 text-sm font-semibold text-emerald-700 ring-1 ring-emerald-200 transition hover:bg-emerald-100 active:scale-[0.98]">
            {{ $isAr ? 'تحميل المزيد' : 'Load more' }}
        </button>
    </div>
    <div id="th-empty" class="hidden rounded-2xl bg-white p-8 text-center shadow-sm ring-1 ring-slate-200">
        <svg class="mx-auto h-8 w-8 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
        <p class="text-sm font-semibold text-slate-700">{{ $isAr ? 'لا توجد عقارات حالياً' : 'No properties yet' }}</p>
    </div>
</section>

{{-- ═══ 5. CTA Banner ═══ --}}
<section class="mt-8 mx-5 rounded-2xl bg-gradient-to-br from-emerald-600 to-emerald-700 p-6 text-white shadow-lg">
    <h2 class="text-lg font-bold leading-snug">{{ $isAr ? 'ابحث عن عقارك اليوم' : 'Find your property today' }}</h2>
    <p class="mt-2 text-sm text-white/80 leading-relaxed">{{ $isAr ? 'عقاري سمارت يجمع سوق العقارات في بحث واحد.' : 'Aqari Smart gathers the rental market in a single search.' }}</p>
    <div class="mt-4 flex gap-3">
        <a href="{{ $tenant->url }}" target="_blank" class="flex-1 rounded-xl bg-white px-4 py-3 text-center text-sm font-semibold text-emerald-700 shadow-md transition hover:bg-white/90">
            <svg class="inline h-4 w-4 -mt-0.5 {{ $isAr ? 'ml-1' : 'mr-1' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            {{ $isAr ? 'زيارة الموقع' : 'Visit website' }}
        </a>
        <a href="{{ route('mobile.marketplace') }}" class="rounded-xl border border-white/30 bg-white/10 px-4 py-3 text-center text-sm font-semibold text-white transition hover:bg-white/20">{{ $isAr ? 'السوق' : 'Marketplace' }}</a>
    </div>
</section>

{{-- ═══ 6. Contact Info ═══ --}}
@if($phone || $email || $address)
<section class="mt-6 mx-5 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 space-y-3 mb-6">
    <h3 class="text-sm font-bold text-slate-800">{{ $isAr ? 'معلومات التواصل' : 'Contact info' }}</h3>
    @if($phone)
    <a href="tel:{{ $phone }}" class="flex items-center gap-3 text-sm text-slate-600">
        <svg class="h-4 w-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
        <span>{{ $phone }}</span>
    </a>
    @endif
    @if($email)
    <a href="mailto:{{ $email }}" class="flex items-center gap-3 text-sm text-slate-600">
        <svg class="h-4 w-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        <span>{{ $email }}</span>
    </a>
    @endif
    @if($address)
    <div class="flex items-center gap-3 text-sm text-slate-600">
        <svg class="h-4 w-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        <span>{{ $address }}</span>
    </div>
    @endif
</section>
@else
<div class="h-6"></div>
@endif

{{-- ═══ 7. Staff Login Modal ═══ --}}
<div id="th-login-modal" class="fixed inset-0 z-[60] flex items-end sm:items-center justify-center" style="display:none;">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" id="th-login-backdrop"></div>
    <div class="relative w-full max-w-md bg-white rounded-t-2xl sm:rounded-2xl shadow-2xl p-6 z-10 animate-slide-up">
        <button type="button" id="th-login-close" class="absolute top-4 {{ $isAr ? 'left-4' : 'right-4' }} text-slate-400 hover:text-slate-600 transition">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div class="flex items-center gap-3 mb-5">
            <div class="h-10 w-10 overflow-hidden rounded-xl bg-emerald-100 flex items-center justify-center">
                @if($logo)
                    <img src="{{ $logo }}" alt="{{ $tenant->name }}" class="h-full w-full object-cover">
                @else
                    <span class="text-sm font-bold text-emerald-700">{{ strtoupper(substr($tenant->name, 0, 2)) }}</span>
                @endif
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-900">{{ $isAr ? 'تسجيل الدخول' : 'Staff Login' }}</h2>
                <p class="text-xs text-slate-500">{{ $tenant->name }}</p>
            </div>
        </div>

        <div id="th-login-error" class="hidden mb-4 rounded-xl bg-red-50 p-3 text-sm text-red-700 ring-1 ring-red-200"></div>

        <form id="th-login-form" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ $isAr ? 'البريد الإلكتروني' : 'Email' }}</label>
                <input type="email" name="email" required
                       class="block w-full rounded-xl border border-slate-300 bg-white py-3 px-4 text-sm text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ $isAr ? 'كلمة المرور' : 'Password' }}</label>
                <input type="password" name="password" required
                       class="block w-full rounded-xl border border-slate-300 bg-white py-3 px-4 text-sm text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
            </div>
            <button type="submit" id="th-login-submit"
                    class="w-full rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-700 px-4 py-3 text-sm font-semibold text-white shadow-lg transition hover:from-emerald-700 hover:to-emerald-800 active:scale-[0.98] disabled:opacity-50">
                {{ $isAr ? 'تسجيل الدخول' : 'Sign In' }}
            </button>
        </form>
    </div>
</div>
<style>
@keyframes slide-up { from { transform: translateY(100%); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
.animate-slide-up { animation: slide-up 0.3s ease-out; }
</style>
@endsection

@push('scripts')
<script>
const lang = document.documentElement.lang === 'ar' ? 'ar' : 'en';
const tenantSlug = @json($tenant->slug);
const apiBase = window.__AQARI_API_BASE || '';

let currentPage = 1;
let lastPage = 1;
let currentListingType = 'rent';
let searchQuery = '';

// ── Unit Card HTML (reuse marketplace pattern) ──
function unitCard(unit) {
    const title = unit.translated_title || unit.title || unit.code;
    const photo = (unit.photos && unit.photos[0]) ? unit.photos[0] : 'https://picsum.photos/seed/aqarismart-fallback/900/640';
    const cityName = lang === 'ar' ? (unit.city?.name_ar || unit.city?.name_en || '') : (unit.city?.name_en || '');
    const propName = unit.property?.name || '';
    const loc = propName && cityName ? propName + ' · ' + cityName : (propName || cityName);
    const typeBadge = unit.listing_type === 'sale' ? (lang === 'ar' ? 'للبيع' : 'Sale') : (lang === 'ar' ? 'للإيجار' : 'Rent');
    const badgeClass = unit.listing_type === 'sale' ? 'bg-emerald-600/90' : 'bg-sky-600/90';
    return '<a href="/mobile/units/' + unit.code + '" class="group block overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 transition-all hover:shadow-md hover:ring-emerald-400">'
        + '<div class="relative aspect-[16/10] bg-slate-100">'
        + '<img src="' + photo + '" alt="' + title + '" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">'
        + '<div class="absolute left-3 top-3"><span class="inline-flex items-center rounded-lg ' + badgeClass + ' backdrop-blur-sm px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-white">' + typeBadge + '</span></div>'
        + '<div class="absolute bottom-3 left-3"><span class="inline-flex items-center rounded-lg bg-white/95 backdrop-blur-sm px-2.5 py-1 text-sm font-bold text-slate-900 shadow-sm">' + (unit.currency || 'JOD') + ' ' + new Intl.NumberFormat().format(unit.price || 0) + '</span></div>'
        + '</div>'
        + '<div class="p-3">'
        + '<h3 class="text-xs font-bold text-slate-900 line-clamp-1">' + title + '</h3>'
        + '<p class="mt-0.5 text-[11px] font-medium text-slate-400 line-clamp-1">' + loc + '</p>'
        + '<div class="mt-2 flex items-center gap-2 border-t border-slate-100 pt-2 text-[10px] font-semibold text-slate-500">'
        + '<span>' + (unit.bedrooms || unit.beds || 0) + ' ' + (lang === 'ar' ? 'غرف' : 'beds') + '</span>'
        + '<span class="text-slate-200">·</span>'
        + '<span>' + (unit.bathrooms || unit.baths || 0) + ' ' + (lang === 'ar' ? 'حمامات' : 'baths') + '</span>'
        + '</div></div></a>';
}

// ── Fetch & Render ──
async function loadTenantHome(page) {
    page = page || 1;
    const params = new URLSearchParams({
        listing_type: currentListingType,
        page: page,
        per_page: 12
    });
    if (searchQuery) params.set('q', searchQuery);

    const response = await fetch(apiBase + '/api/mobile/tenants/' + tenantSlug + '/home?' + params.toString(), {
        headers: { Accept: 'application/json' }
    });
    const json = await response.json();

    const featured = json.featured_units || [];
    const units = json.units || [];
    const meta = json.meta || {};
    currentPage = meta.current_page || 1;
    lastPage = meta.last_page || 1;

    // Stats pill
    const totalEl = document.getElementById('th-units-total');
    if (totalEl) {
        const total = meta.total || 0;
        totalEl.textContent = new Intl.NumberFormat().format(total) + ' ' + (lang === 'ar' ? 'عقار متاح' : 'Properties Available');
    }

    // Featured section (only on first load)
    if (page === 1) {
        const featuredEl = document.getElementById('th-featured');
        if (featuredEl) {
            if (featured.length) {
                featuredEl.innerHTML = featured.slice(0, 4).map(unitCard).join('');
            } else if (units.length) {
                featuredEl.innerHTML = units.slice(0, 4).map(unitCard).join('');
            } else {
                featuredEl.innerHTML = '';
            }
        }
    }

    // Units feed
    const feedEl = document.getElementById('th-units-feed');
    const emptyEl = document.getElementById('th-empty');
    if (feedEl) {
        if (page === 1) feedEl.innerHTML = '';
        if (units.length) {
            feedEl.insertAdjacentHTML('beforeend', units.map(unitCard).join(''));
        }
        if (!units.length && page === 1) {
            emptyEl?.classList.remove('hidden');
        } else {
            emptyEl?.classList.add('hidden');
        }
    }

    // Load more button
    const loadMoreEl = document.getElementById('th-load-more');
    if (loadMoreEl) {
        if (currentPage < lastPage) {
            loadMoreEl.classList.remove('hidden');
        } else {
            loadMoreEl.classList.add('hidden');
        }
    }
}

// ── Event Bindings ──
// Listing type toggle
document.querySelectorAll('.th-listing-toggle').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.th-listing-toggle').forEach(function(b) {
            b.classList.remove('bg-white', 'text-emerald-800', 'shadow-sm');
            b.classList.add('text-white/70');
        });
        btn.classList.remove('text-white/70');
        btn.classList.add('bg-white', 'text-emerald-800', 'shadow-sm');
        currentListingType = btn.dataset.value;
        loadTenantHome(1);
    });
});

// Search
var searchTimer;
document.getElementById('tenant-search-q')?.addEventListener('input', function() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(function() {
        searchQuery = document.getElementById('tenant-search-q').value.trim();
        loadTenantHome(1);
    }, 500);
});

document.getElementById('tenant-search-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    searchQuery = document.getElementById('tenant-search-q').value.trim();
    loadTenantHome(1);
});

// Load more
document.getElementById('th-load-more-btn')?.addEventListener('click', function() {
    loadTenantHome(currentPage + 1);
});

// ── Staff Login Modal ──
const loginModal = document.getElementById('th-login-modal');
const loginBtn = document.getElementById('th-login-btn');
const loginClose = document.getElementById('th-login-close');
const loginBackdrop = document.getElementById('th-login-backdrop');
const loginForm = document.getElementById('th-login-form');
const loginError = document.getElementById('th-login-error');
const guestActions = document.getElementById('th-guest-actions');

function openLoginModal() { loginModal && (loginModal.style.display = 'flex'); }
function closeLoginModal() { loginModal && (loginModal.style.display = 'none'); }

// Show guest actions only if not authed
if (!localStorage.getItem('aqari_mobile_token')) {
    guestActions && (guestActions.style.display = 'flex');
}

loginBtn?.addEventListener('click', openLoginModal);
loginClose?.addEventListener('click', closeLoginModal);
loginBackdrop?.addEventListener('click', closeLoginModal);

loginForm?.addEventListener('submit', async function(e) {
    e.preventDefault();
    loginError?.classList.add('hidden');
    const submitBtn = document.getElementById('th-login-submit');
    submitBtn && (submitBtn.disabled = true);
    submitBtn && (submitBtn.textContent = lang === 'ar' ? 'جاري التحميل...' : 'Signing in...');

    try {
        const formData = new FormData(loginForm);
        const res = await fetch(apiBase + '/api/mobile/auth/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
            body: JSON.stringify({
                email: formData.get('email'),
                password: formData.get('password'),
                tenant_slug: tenantSlug
            })
        });
        const json = await res.json();

        if (!res.ok) {
            const msg = json.message || json.errors?.login?.[0] || json.errors?.email?.[0] || (lang === 'ar' ? 'بيانات الدخول غير صحيحة' : 'Invalid credentials');
            loginError.textContent = msg;
            loginError?.classList.remove('hidden');
            return;
        }

        // Store auth data
        localStorage.setItem('aqari_mobile_token', json.token);
        localStorage.setItem('aqari_mobile_tenant_slug', json.current_tenant?.slug || tenantSlug);
        localStorage.setItem('aqari_mobile_user_name', json.user?.name || '');

        // Redirect to mobile dashboard
        window.location.href = '/mobile/dashboard';
    } catch (err) {
        loginError.textContent = lang === 'ar' ? 'حدث خطأ، حاول مجدداً' : 'Something went wrong, try again';
        loginError?.classList.remove('hidden');
    } finally {
        submitBtn && (submitBtn.disabled = false);
        submitBtn && (submitBtn.textContent = lang === 'ar' ? 'تسجيل الدخول' : 'Sign In');
    }
});

// Initial load
loadTenantHome(1);
</script>
@endpush
