@extends('mobile.layouts.app', ['title' => app()->getLocale() === 'ar' ? 'الملف الشخصي' : 'My Profile'])

@section('content')
@php $isAr = app()->getLocale() === 'ar'; @endphp

<div id="profile-loading" class="flex flex-col items-center justify-center py-20">
    <div class="h-10 w-10 animate-spin rounded-full border-4 border-emerald-200 border-t-emerald-600"></div>
    <p class="mt-4 text-sm font-medium text-slate-500">{{ $isAr ? 'جاري التحميل...' : 'Loading...' }}</p>
</div>

<div id="profile-guest" class="hidden px-5 py-16 text-center">
    <div class="mx-auto h-16 w-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
        <svg class="h-8 w-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
    </div>
    <h2 class="text-lg font-bold text-slate-800 mb-2">{{ $isAr ? 'سجّل الدخول لعرض ملفك' : 'Sign in to view your profile' }}</h2>
    <p class="text-sm text-slate-500 mb-6 max-w-xs mx-auto">{{ $isAr ? 'قم بتسجيل الدخول للوصول إلى معلوماتك الشخصية ونشاطك.' : 'Log in to access your personal info and activity.' }}</p>
    <a href="{{ route('mobile.login') }}" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow-lg transition hover:bg-emerald-700">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14"/></svg>
        {{ $isAr ? 'تسجيل الدخول' : 'Sign in' }}
    </a>
</div>

<div id="profile-content" class="hidden space-y-5 pb-8">
    {{-- Avatar + Name Card --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-600 to-emerald-700 p-6 text-white shadow-lg">
        <div class="absolute inset-0 opacity-5" style="background-image:radial-gradient(white 1px,transparent 1px);background-size:20px 20px;"></div>
        <div class="relative flex items-center gap-4">
            <div id="profile-avatar" class="h-16 w-16 shrink-0 rounded-2xl bg-white/20 ring-2 ring-white/30 flex items-center justify-center text-xl font-bold text-white"></div>
            <div class="min-w-0">
                <h1 id="profile-name" class="text-xl font-bold truncate"></h1>
                <p id="profile-role" class="mt-0.5 text-sm text-white/80"></p>
            </div>
        </div>
    </div>

    {{-- Info Card --}}
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 space-y-4">
        <h2 class="text-sm font-bold text-slate-800">{{ $isAr ? 'معلومات الحساب' : 'Account Info' }}</h2>
        <div id="profile-info" class="space-y-3"></div>
    </div>

    {{-- Tenant Card --}}
    <div id="profile-tenant-section" class="hidden rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 space-y-4">
        <h2 class="text-sm font-bold text-slate-800">{{ $isAr ? 'المنظمة' : 'Organization' }}</h2>
        <div id="profile-tenant-info" class="space-y-3"></div>
    </div>

    {{-- Quick Actions --}}
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 space-y-3">
        <h2 class="text-sm font-bold text-slate-800">{{ $isAr ? 'إجراءات سريعة' : 'Quick Actions' }}</h2>
        <div class="grid grid-cols-2 gap-3" id="profile-actions"></div>
    </div>

    {{-- Sign Out --}}
    <button type="button" id="profile-signout"
            class="w-full rounded-2xl bg-red-50 py-3.5 text-sm font-semibold text-red-600 ring-1 ring-red-200 transition hover:bg-red-100 active:scale-[0.98]">
        {{ $isAr ? 'تسجيل الخروج' : 'Sign Out' }}
    </button>
</div>
@endsection

@push('scripts')
<script>
const lang = document.documentElement.lang === 'ar' ? 'ar' : 'en';
const apiBase = window.__AQARI_API_BASE || '';

async function loadProfile() {
    const token = localStorage.getItem('aqari_mobile_token');
    if (!token) {
        document.getElementById('profile-loading').classList.add('hidden');
        document.getElementById('profile-guest').classList.remove('hidden');
        return;
    }

    try {
        const res = await fetch(apiBase + '/api/mobile/auth/me', {
            headers: {
                Authorization: 'Bearer ' + token,
                Accept: 'application/json',
                'X-Tenant-Slug': localStorage.getItem('aqari_mobile_tenant_slug') || ''
            }
        });

        if (!res.ok) {
            localStorage.removeItem('aqari_mobile_token');
            document.getElementById('profile-loading').classList.add('hidden');
            document.getElementById('profile-guest').classList.remove('hidden');
            return;
        }

        const json = await res.json();
        const user = json.user || {};
        const tenant = json.current_tenant || null;

        // Avatar
        const initials = (user.name || '?').split(' ').map(w => w[0]).join('').toUpperCase().substring(0, 2);
        document.getElementById('profile-avatar').textContent = initials;

        // Name + Role
        document.getElementById('profile-name').textContent = user.name || '—';
        const roleLabel = user.is_staff
            ? (lang === 'ar' ? 'فريق العمل' : 'Staff')
            : user.is_resident
                ? (lang === 'ar' ? 'مقيم' : 'Resident')
                : (lang === 'ar' ? 'مستخدم' : 'User');
        document.getElementById('profile-role').textContent = roleLabel;

        // Info rows
        const infoEl = document.getElementById('profile-info');
        const infoItems = [];
        if (user.email) {
            infoItems.push({ icon: 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', label: lang === 'ar' ? 'البريد الإلكتروني' : 'Email', value: user.email });
        }
        if (user.phone) {
            infoItems.push({ icon: 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', label: lang === 'ar' ? 'الهاتف' : 'Phone', value: user.phone });
        }
        if (user.email_verified_at) {
            infoItems.push({ icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', label: lang === 'ar' ? 'الحالة' : 'Status', value: lang === 'ar' ? 'تم التحقق ✓' : 'Verified ✓' });
        }
        infoEl.innerHTML = infoItems.map(item =>
            '<div class="flex items-center gap-3">' +
            '<div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">' +
            '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="' + item.icon + '"/></svg>' +
            '</div>' +
            '<div class="min-w-0">' +
            '<p class="text-[11px] font-medium text-slate-400">' + item.label + '</p>' +
            '<p class="text-sm font-semibold text-slate-800 truncate">' + item.value + '</p>' +
            '</div></div>'
        ).join('');

        // Tenant section
        if (tenant) {
            document.getElementById('profile-tenant-section').classList.remove('hidden');
            const tenantInfo = document.getElementById('profile-tenant-info');
            const tenantItems = [
                { label: lang === 'ar' ? 'الاسم' : 'Name', value: tenant.name || '—' },
                { label: lang === 'ar' ? 'الخطة' : 'Plan', value: (tenant.subscription?.package_name || tenant.plan || '—') },
            ];
            if (tenant.active_units_count !== undefined) {
                tenantItems.push({ label: lang === 'ar' ? 'الوحدات النشطة' : 'Active Units', value: tenant.active_units_count });
            }
            tenantInfo.innerHTML = tenantItems.map(item =>
                '<div class="flex items-center justify-between py-1">' +
                '<span class="text-xs font-medium text-slate-400">' + item.label + '</span>' +
                '<span class="text-sm font-semibold text-slate-800">' + item.value + '</span>' +
                '</div>'
            ).join('');
        }

        // Quick actions
        const actionsEl = document.getElementById('profile-actions');
        const actions = [];
        if (user.is_staff) {
            actions.push({ href: '/mobile/dashboard', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', label: lang === 'ar' ? 'لوحة التحكم' : 'Dashboard' });
            actions.push({ href: '/mobile/units', icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5', label: lang === 'ar' ? 'الوحدات' : 'Units' });
        }
        actions.push({ href: '/mobile/marketplace', icon: 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z', label: lang === 'ar' ? 'السوق' : 'Marketplace' });
        actions.push({ href: '/mobile/tenants', icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', label: lang === 'ar' ? 'الوكالات' : 'Agencies' });

        actionsEl.innerHTML = actions.map(a =>
            '<a href="' + a.href + '" class="flex flex-col items-center gap-2 rounded-xl bg-slate-50 p-4 transition hover:bg-emerald-50 hover:text-emerald-700">' +
            '<svg class="h-5 w-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="' + a.icon + '"/></svg>' +
            '<span class="text-xs font-semibold text-slate-700">' + a.label + '</span></a>'
        ).join('');

        // Show content
        document.getElementById('profile-loading').classList.add('hidden');
        document.getElementById('profile-content').classList.remove('hidden');

    } catch (err) {
        console.error('Profile load error:', err);
        document.getElementById('profile-loading').classList.add('hidden');
        document.getElementById('profile-guest').classList.remove('hidden');
    }
}

document.getElementById('profile-signout')?.addEventListener('click', function() {
    const token = localStorage.getItem('aqari_mobile_token');
    if (token) {
        fetch(apiBase + '/api/mobile/auth/logout', {
            method: 'POST',
            headers: { Authorization: 'Bearer ' + token, Accept: 'application/json' }
        }).catch(() => {});
    }
    localStorage.removeItem('aqari_mobile_token');
    localStorage.removeItem('aqari_mobile_tenant_slug');
    localStorage.removeItem('aqari_mobile_user_name');
    window.location.href = '/mobile/marketplace';
});

loadProfile();
</script>
@endpush
