@extends('mobile.layouts.app', [
    'title' => app()->getLocale() === 'ar' ? 'الوحدات' : 'My Units',
    'subtitle' => '',
])

@section('content')
<div class="min-h-screen bg-[#f8f9fa] pb-20">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="px-4 py-4 flex items-center justify-between">
            <a href="{{ route('mobile.dashboard') }}" class="text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-xl font-bold text-gray-900">{{ app()->getLocale() === 'ar' ? 'الوحدات' : 'My Units' }}</h1>
            <a href="{{ route('mobile.units.create') }}" class="text-emerald-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </a>
        </div>
    </div>

    <!-- Search -->
    <div class="px-4 pt-4 pb-2">
        <input id="units-search" type="text" placeholder="{{ app()->getLocale() === 'ar' ? 'بحث...' : 'Search units...' }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
    </div>

    <!-- Units List -->
    <div id="units-loading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-emerald-600"></div>
    </div>

    <div id="units-container" class="px-4 py-2 space-y-3"></div>

    <div id="units-empty" class="hidden text-center py-12 px-4">
        <svg class="w-14 h-14 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
        <p class="text-gray-500 text-sm">{{ app()->getLocale() === 'ar' ? 'لا توجد وحدات بعد' : 'No units yet' }}</p>
        <a href="{{ route('mobile.units.create') }}" class="inline-block mt-4 bg-emerald-600 text-white px-5 py-2.5 rounded-xl text-sm font-semibold">
            {{ app()->getLocale() === 'ar' ? 'أضف وحدة' : 'Add Unit' }}
        </a>
    </div>

    <div id="units-no-auth" class="hidden text-center py-12 px-4">
        <p class="text-gray-600 mb-4">{{ app()->getLocale() === 'ar' ? 'سجّل الدخول أولاً' : 'Sign in to manage your units' }}</p>
        <a href="{{ route('mobile.login') }}" class="inline-block bg-emerald-600 text-white px-6 py-3 rounded-xl font-semibold">
            {{ app()->getLocale() === 'ar' ? 'تسجيل الدخول' : 'Sign In' }}
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
(async () => {
    const token = localStorage.getItem('aqari_mobile_token');
    const tenantSlug = localStorage.getItem('aqari_mobile_tenant_slug');
    const loading = document.getElementById('units-loading');
    const container = document.getElementById('units-container');
    const empty = document.getElementById('units-empty');
    const noAuth = document.getElementById('units-no-auth');
    const lang = document.documentElement.lang === 'ar' ? 'ar' : 'en';

    if (!token) {
        loading.classList.add('hidden');
        noAuth.classList.remove('hidden');
        return;
    }

    try {
        const response = await fetch((window.__AQARI_API_BASE || '') + '/api/mobile/units', {
            headers: {
                Accept: 'application/json',
                Authorization: `Bearer ${token}`,
                'X-Tenant-Slug': tenantSlug || '',
            },
        });

        if (response.status === 401) {
            localStorage.removeItem('aqari_mobile_token');
            window.location.href = '{{ route("mobile.login") }}';
            return;
        }

        const data = await response.json();
        loading.classList.add('hidden');

        const units = data.data ?? data ?? [];
        if (!Array.isArray(units) || units.length === 0) {
            empty.classList.remove('hidden');
            return;
        }

        units.forEach(unit => {
            const photo = unit.photos?.[0] || '';
            const title = unit.translated_title || unit.title || unit.code;
            const price = unit.display_price ?? unit.price ?? 0;
            const currency = unit.currency ?? 'IQD';
            const status = unit.status ?? '';
            const listingType = unit.listing_type ?? '';

            const statusColor = { vacant: 'bg-green-100 text-green-700', occupied: 'bg-blue-100 text-blue-700', sold: 'bg-gray-100 text-gray-600' }[status] ?? 'bg-gray-100 text-gray-500';
            const statusLabel = { vacant: lang === 'ar' ? 'شاغر' : 'Vacant', occupied: lang === 'ar' ? 'مشغول' : 'Occupied', sold: lang === 'ar' ? 'مباع' : 'Sold' }[status] ?? status;
            const typeLabel = listingType === 'sale' ? (lang === 'ar' ? 'للبيع' : 'For Sale') : (lang === 'ar' ? 'للإيجار' : 'For Rent');

            const card = document.createElement('div');
            card.className = 'bg-white rounded-xl shadow-sm overflow-hidden';
            card.innerHTML = `
                <div class="flex gap-3 p-3">
                    <div class="w-20 h-20 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100 flex items-center justify-center">
                        ${photo ? `<img src="${photo}" class="w-full h-full object-cover">` : `<svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>`}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <p class="font-semibold text-gray-900 text-sm truncate">${title}</p>
                            <span class="shrink-0 text-xs px-2 py-0.5 rounded-full font-medium ${statusColor}">${statusLabel}</span>
                        </div>
                        <p class="text-xs text-gray-400 mb-1.5">${unit.code}</p>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-emerald-700">${new Intl.NumberFormat().format(price)} ${currency}</span>
                            <span class="text-xs text-gray-400">· ${typeLabel}</span>
                        </div>
                        <div class="flex gap-3 mt-2.5">
                            <a href="/mobile/units/${unit.code}" class="text-xs text-gray-500 font-medium">{{ app()->getLocale() === 'ar' ? 'عرض' : 'View' }}</a>
                            <a href="/mobile/units/${unit.code}/edit" class="text-xs text-emerald-600 font-medium">{{ app()->getLocale() === 'ar' ? 'تعديل' : 'Edit' }}</a>
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(card);
        });

        // Search
        document.getElementById('units-search')?.addEventListener('input', function () {
            const q = this.value.toLowerCase();
            container.querySelectorAll('div.bg-white').forEach(card => {
                card.style.display = card.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });

    } catch (e) {
        loading.classList.add('hidden');
        container.innerHTML = `<p class="text-center text-red-500 text-sm py-8">{{ app()->getLocale() === 'ar' ? 'فشل التحميل' : 'Failed to load units' }}</p>`;
    }
})();
</script>
@endpush
