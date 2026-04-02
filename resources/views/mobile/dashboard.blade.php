@extends('mobile.layouts.app', ['title' => app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Dashboard', 'subtitle' => ''])

@section('content')
    @php $locale = app()->getLocale() === 'ar' ? 'ar' : 'en'; @endphp

    <!-- Loading state -->
    <div id="dash-loading" class="flex flex-col items-center justify-center py-20">
        <div class="h-8 w-8 animate-spin rounded-full border-[3px] border-slate-200 border-t-emerald-600"></div>
        <p class="mt-3 text-xs font-medium text-slate-400">{{ $locale === 'ar' ? 'جاري التحميل...' : 'Loading dashboard...' }}</p>
    </div>

    <!-- Not logged in -->
    <div id="dash-no-auth" class="hidden">
        <div class="flex flex-col items-center justify-center rounded-2xl bg-white p-8 text-center shadow-sm ring-1 ring-slate-200">
            <svg class="h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <h3 class="mt-4 text-base font-bold text-slate-800">{{ $locale === 'ar' ? 'تسجيل الدخول مطلوب' : 'Sign in required' }}</h3>
            <p class="mt-1 text-xs text-slate-400">{{ $locale === 'ar' ? 'يرجى تسجيل الدخول للوصول إلى لوحة التحكم' : 'Please sign in to access your dashboard' }}</p>
            <a href="{{ route('mobile.login') }}" class="mt-5 inline-flex items-center rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">{{ $locale === 'ar' ? 'تسجيل الدخول' : 'Sign in' }}</a>
        </div>
    </div>

    <!-- Error state -->
    <div id="dash-error" class="hidden">
        <div class="flex flex-col items-center justify-center rounded-2xl bg-red-50 p-8 text-center ring-1 ring-red-200">
            <svg class="h-10 w-10 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            <h3 class="mt-3 text-sm font-bold text-red-700" id="dash-error-title">Error</h3>
            <p class="mt-1 text-xs text-red-500" id="dash-error-msg"></p>
        </div>
    </div>

    <!-- Dashboard content (hidden until loaded) -->
    <div id="dash-content" class="hidden space-y-5">

        <!-- Tenant card -->
        <div class="overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-600 to-emerald-700 p-5 text-white shadow-lg">
            <div class="flex items-start justify-between">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2">
                        <div id="dash-tenant-logo" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/20">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div class="min-w-0">
                            <h2 class="truncate text-lg font-bold" id="dash-tenant-name"></h2>
                            <p class="text-xs text-white/70" id="dash-user-info"></p>
                        </div>
                    </div>
                </div>
                <span class="shrink-0 rounded-full bg-white/20 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider" id="dash-plan-badge"></span>
            </div>
            <a id="dash-web-link" href="#" target="_blank" class="mt-4 flex items-center justify-center gap-2 rounded-xl bg-white/15 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-white/25">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                {{ $locale === 'ar' ? 'فتح لوحة التحكم الكاملة' : 'Open full web dashboard' }}
            </a>
        </div>

        <!-- Staff dashboard -->
        <div id="dash-staff" class="hidden space-y-5">
            <!-- Metrics grid -->
            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                    <div class="flex items-center gap-2">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50"><svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg></div>
                        <span class="text-[11px] font-medium text-slate-400">{{ $locale === 'ar' ? 'العقارات' : 'Properties' }}</span>
                    </div>
                    <div class="mt-2 text-2xl font-bold text-slate-800" id="m-properties">0</div>
                </div>
                <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                    <div class="flex items-center gap-2">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50"><svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/></svg></div>
                        <span class="text-[11px] font-medium text-slate-400">{{ $locale === 'ar' ? 'الوحدات' : 'Units' }}</span>
                    </div>
                    <div class="mt-2 text-2xl font-bold text-slate-800" id="m-units">0</div>
                </div>
                <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                    <div class="flex items-center gap-2">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50"><svg class="h-4 w-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                        <span class="text-[11px] font-medium text-slate-400">{{ $locale === 'ar' ? 'العقود' : 'Leases' }}</span>
                    </div>
                    <div class="mt-2 text-2xl font-bold text-slate-800" id="m-leases">0</div>
                </div>
                <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                    <div class="flex items-center gap-2">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-50"><svg class="h-4 w-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></div>
                        <span class="text-[11px] font-medium text-slate-400">{{ $locale === 'ar' ? 'الإشغال' : 'Occupancy' }}</span>
                    </div>
                    <div class="mt-2 text-2xl font-bold text-slate-800"><span id="m-occupancy">0</span>%</div>
                </div>
            </div>

            <!-- Rent & Maintenance row -->
            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                    <span class="text-[11px] font-medium text-slate-400">{{ $locale === 'ar' ? 'الإيجار الشهري' : 'Monthly rent' }}</span>
                    <div class="mt-1.5 text-lg font-bold text-emerald-700"><span id="m-currency">JOD</span> <span id="m-rent">0</span></div>
                    <div class="mt-1 text-[10px] text-slate-400"><span id="m-occupied">0</span> {{ $locale === 'ar' ? 'مشغولة' : 'occupied' }} · <span id="m-vacant">0</span> {{ $locale === 'ar' ? 'شاغرة' : 'vacant' }}</div>
                </div>
                <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                    <span class="text-[11px] font-medium text-slate-400">{{ $locale === 'ar' ? 'الصيانة' : 'Maintenance' }}</span>
                    <div class="mt-1.5 text-lg font-bold text-slate-800" id="m-maintenance">0</div>
                    <div class="mt-1 text-[10px] text-slate-400">{{ $locale === 'ar' ? 'طلبات مفتوحة' : 'open requests' }}</div>
                </div>
            </div>

            <!-- Upcoming leases -->
            <div id="dash-upcoming-wrap" class="hidden">
                <h3 class="mb-3 text-sm font-bold text-slate-800">{{ $locale === 'ar' ? 'عقود تنتهي قريباً' : 'Upcoming lease expirations' }}</h3>
                <div id="dash-upcoming" class="space-y-2"></div>
            </div>

            <!-- Quick actions -->
            <div class="space-y-2">
                <h3 class="text-sm font-bold text-slate-800">{{ $locale === 'ar' ? 'إجراءات سريعة' : 'Quick actions' }}</h3>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('mobile.units.index') }}" class="flex items-center gap-3 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 transition hover:ring-emerald-400">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50"><svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg></div>
                        <span class="text-xs font-semibold text-slate-700">{{ $locale === 'ar' ? 'الوحدات' : 'My units' }}</span>
                    </a>
                    <a href="{{ route('mobile.units.create') }}" class="flex items-center gap-3 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 transition hover:ring-emerald-400">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50"><svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg></div>
                        <span class="text-xs font-semibold text-slate-700">{{ $locale === 'ar' ? 'وحدة جديدة' : 'Add unit' }}</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Resident dashboard -->
        <div id="dash-resident" class="hidden space-y-5">
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <h3 class="text-sm font-bold text-slate-800">{{ $locale === 'ar' ? 'معلوماتك' : 'Your info' }}</h3>
                <div class="mt-3 space-y-2 text-xs text-slate-500" id="dash-resident-info"></div>
            </div>
            <div id="dash-leases-wrap" class="hidden">
                <h3 class="mb-3 text-sm font-bold text-slate-800">{{ $locale === 'ar' ? 'عقود الإيجار' : 'Your leases' }}</h3>
                <div id="dash-leases" class="space-y-2"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(async () => {
    const lang = @json($locale);
    const token = localStorage.getItem('aqari_mobile_token');
    const tenantSlug = localStorage.getItem('aqari_mobile_tenant_slug');
    const loading = document.getElementById('dash-loading');
    const noAuth = document.getElementById('dash-no-auth');
    const errorEl = document.getElementById('dash-error');
    const content = document.getElementById('dash-content');

    if (!token) {
        loading.classList.add('hidden');
        noAuth.classList.remove('hidden');
        return;
    }

    try {
        const res = await fetch((window.__AQARI_API_BASE || '') + '/api/mobile/dashboard', {
            headers: { Accept: 'application/json', Authorization: `Bearer ${token}`, 'X-Tenant-Slug': tenantSlug || '' },
        });

        if (res.status === 401) {
            localStorage.removeItem('aqari_mobile_token');
            loading.classList.add('hidden');
            noAuth.classList.remove('hidden');
            return;
        }

        if (!res.ok) {
            throw new Error(res.statusText || 'Failed to load');
        }

        const data = await res.json();
        loading.classList.add('hidden');
        content.classList.remove('hidden');

        // Tenant card
        const tenant = data.tenant || {};
        const user = data.user || {};
        document.getElementById('dash-tenant-name').textContent = tenant.name || 'My Workspace';
        document.getElementById('dash-user-info').textContent = (user.name || '') + (user.tenant_role ? ` · ${user.tenant_role}` : '');
        document.getElementById('dash-plan-badge').textContent = (tenant.plan || 'starter').toUpperCase();

        if (tenant.branding?.logo_url) {
            document.getElementById('dash-tenant-logo').innerHTML = `<img src="${tenant.branding.logo_url}" class="h-10 w-10 rounded-xl object-cover" alt="">`;
        }

        const webUrl = tenant.url || '#';
        document.getElementById('dash-web-link').href = webUrl;

        if (data.role === 'resident') {
            // Resident view
            document.getElementById('dash-resident').classList.remove('hidden');
            const ri = document.getElementById('dash-resident-info');
            const resident = data.resident;
            if (resident) {
                ri.innerHTML = `
                    <div class="flex justify-between"><span class="text-slate-400">${lang === 'ar' ? 'الاسم' : 'Name'}</span><span class="font-medium text-slate-700">${resident.name || '-'}</span></div>
                    <div class="flex justify-between"><span class="text-slate-400">${lang === 'ar' ? 'البريد' : 'Email'}</span><span class="font-medium text-slate-700">${resident.email || '-'}</span></div>
                    <div class="flex justify-between"><span class="text-slate-400">${lang === 'ar' ? 'الهاتف' : 'Phone'}</span><span class="font-medium text-slate-700">${resident.phone || '-'}</span></div>`;
            }

            const leases = data.leases || [];
            if (leases.length) {
                document.getElementById('dash-leases-wrap').classList.remove('hidden');
                document.getElementById('dash-leases').innerHTML = leases.map(l => `
                    <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold text-slate-800">${l.unit?.title || l.unit?.code || '-'}</span>
                            <span class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase ${l.status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500'}">${l.status}</span>
                        </div>
                        <div class="mt-1 text-xs text-slate-400">${l.unit?.property_name || ''}</div>
                        <div class="mt-2 flex items-center gap-3 text-[11px] text-slate-500">
                            <span>${l.start_date || '-'} → ${l.end_date || '-'}</span>
                        </div>
                    </div>`).join('');
            }
        } else {
            // Staff / owner view
            document.getElementById('dash-staff').classList.remove('hidden');
            const m = data.dashboard?.metrics || {};
            document.getElementById('m-properties').textContent = m.properties ?? 0;
            document.getElementById('m-units').textContent = m.units ?? 0;
            document.getElementById('m-leases').textContent = m.active_leases ?? 0;
            document.getElementById('m-occupancy').textContent = m.occupancy_rate ?? 0;
            document.getElementById('m-currency').textContent = m.rent_currency || 'JOD';
            document.getElementById('m-rent').textContent = new Intl.NumberFormat().format(m.monthly_rent ?? 0);
            document.getElementById('m-occupied').textContent = m.occupied_units ?? 0;
            document.getElementById('m-vacant').textContent = m.vacant_units ?? 0;
            document.getElementById('m-maintenance').textContent = m.open_maintenance ?? 0;

            // Upcoming leases
            const upcoming = data.dashboard?.upcomingLeases || [];
            if (upcoming.length) {
                document.getElementById('dash-upcoming-wrap').classList.remove('hidden');
                document.getElementById('dash-upcoming').innerHTML = upcoming.map(l => `
                    <div class="flex items-center justify-between rounded-xl bg-white p-3.5 shadow-sm ring-1 ring-slate-200">
                        <div>
                            <div class="text-xs font-bold text-slate-800">${l.unit?.title || l.unit?.code || '-'}</div>
                            <div class="text-[10px] text-slate-400">${l.property?.name || ''}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-[10px] font-semibold text-amber-600">${lang === 'ar' ? 'ينتهي' : 'Expires'} ${l.end_date || ''}</div>
                        </div>
                    </div>`).join('');
            }
        }
    } catch (e) {
        loading.classList.add('hidden');
        errorEl.classList.remove('hidden');
        document.getElementById('dash-error-title').textContent = lang === 'ar' ? 'خطأ' : 'Error';
        document.getElementById('dash-error-msg').textContent = e.message || 'Connection error';
    }
})();
</script>
@endpush
