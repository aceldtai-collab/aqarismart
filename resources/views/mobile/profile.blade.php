@extends('mobile.layouts.app', [
    'title' => app()->getLocale() === 'ar' ? 'الملف الشخصي' : 'My Profile',
    'show_back_button' => false,
    'body_class' => 'mobile-account-shell',
])

@php
    $isAr = app()->getLocale() === 'ar';
    $strings = [
        'guestKicker' => $isAr ? 'رحلتك الشخصية' : 'Your personal journey',
        'guestTitle' => $isAr ? 'سجّل الدخول لتفتح مركزك الشخصي' : 'Sign in to unlock your personal hub',
        'guestText' => $isAr ? 'من هنا تصل إلى نشاطك، ملفك، ولوحات الوكالات التي تنتمي إليها ضمن نفس اللغة البصرية الجديدة.' : 'This is where your activity, profile, and any agency dashboards you belong to come together under the new mobile system.',
        'signIn' => $isAr ? 'تسجيل الدخول' : 'Sign In',
        'createAccount' => $isAr ? 'إنشاء حساب' : 'Create account',
        'marketplace' => $isAr ? 'العودة إلى السوق' : 'Back to marketplace',
        'heroKicker' => $isAr ? 'الملف الشخصي' : 'Profile hub',
        'defaultStory' => $isAr ? 'هذا هو مركزك الشخصي داخل عقاري سمارت. من هنا تتابع نشاطك، الوكالات المرتبطة بك، والخطوة التالية في رحلتك العقارية.' : 'This is your personal hub inside Aqari Smart, where your activity, connected agencies, and next steps stay in one place.',
        'staffStory' => $isAr ? 'أنت الآن ضمن فريق عمل عقاري. استخدم هذا الملف للانتقال السريع بين الهوية الشخصية ولوحة الوكالة.' : 'You are currently operating inside a property team. Use this profile to move quickly between your personal identity and the tenant dashboard.',
        'residentStory' => $isAr ? 'ملفك يجمع رحلتك كمقيم أو مستأجر داخل الوكالة الحالية، مع وصول سريع إلى الإيجارات والسوق.' : 'Your profile brings together your resident journey inside the current agency, with quick access to leases and the marketplace.',
        'marketUserStory' => $isAr ? 'أنت تستخدم السوق بحساب موحّد للبحث والمقارنة وحفظ نشاطك قبل بدء أي خطوة أعمق.' : 'You are using the marketplace through a unified account for browsing, comparing, and keeping your activity before taking any deeper step.',
        'verified' => $isAr ? 'موثّق' : 'Verified',
        'notVerified' => $isAr ? 'غير موثّق' : 'Not verified',
        'staff' => $isAr ? 'فريق العمل' : 'Staff',
        'resident' => $isAr ? 'مقيم' : 'Resident',
        'marketUser' => $isAr ? 'مستخدم السوق' : 'Marketplace user',
        'workspace' => $isAr ? 'الوكالة الحالية' : 'Current workspace',
        'access' => $isAr ? 'نوع الوصول' : 'Access',
        'status' => $isAr ? 'الحالة' : 'Status',
        'overviewTitle' => $isAr ? 'تفاصيل الحساب' : 'Account details',
        'overviewText' => $isAr ? 'المعلومات الأساسية التي تعرّف حسابك الحالي على الموبايل.' : 'The core details that define your current mobile identity.',
        'tenantTitle' => $isAr ? 'الوكالة المرتبطة' : 'Connected agency',
        'tenantText' => $isAr ? 'نظرة سريعة على الوكالة الحالية وخطة الاشتراك والحضور العام.' : 'A quick read on the active tenant workspace, subscription, and public presence.',
        'actionsTitle' => $isAr ? 'الخطوة التالية' : 'What to do next',
        'actionsText' => $isAr ? 'روابط سريعة تكمل رحلتك من هذا الملف.' : 'Quick destinations that carry the journey forward from this profile.',
        'sessionTitle' => $isAr ? 'الجلسة الحالية' : 'Current session',
        'sessionText' => $isAr ? 'عند تسجيل الخروج ستعود إلى السوق وسيتم تنظيف حالة الهاتف المحلية.' : 'Signing out returns you to the marketplace and clears the local mobile session on this device.',
        'signOut' => $isAr ? 'تسجيل الخروج' : 'Sign Out',
        'email' => $isAr ? 'البريد الإلكتروني' : 'Email',
        'phone' => $isAr ? 'الهاتف' : 'Phone',
        'role' => $isAr ? 'الدور' : 'Role',
        'tenantRole' => $isAr ? 'الدور داخل الوكالة' : 'Tenant role',
        'plan' => $isAr ? 'الخطة' : 'Plan',
        'coverage' => $isAr ? 'التغطية' : 'Coverage',
        'address' => $isAr ? 'العنوان' : 'Address',
        'website' => $isAr ? 'الموقع' : 'Website',
        'subscription' => $isAr ? 'الاشتراك' : 'Subscription',
        'units' => $isAr ? 'الوحدات' : 'Units',
        'agents' => $isAr ? 'الوكلاء' : 'Agents',
        'description' => $isAr ? 'الوصف' : 'Description',
        'emptyAccount' => $isAr ? 'لا توجد تفاصيل إضافية لهذا الحساب بعد.' : 'No extra details are available for this account yet.',
        'emptyTenant' => $isAr ? 'لا توجد وكالة مرتبطة بهذا الحساب حالياً.' : 'There is no active tenant workspace attached to this account right now.',
        'dashboard' => $isAr ? 'لوحة التحكم' : 'Dashboard',
        'myUnits' => $isAr ? 'وحداتي' : 'My units',
        'agencies' => $isAr ? 'الوكالات' : 'Agencies',
        'sellWithUs' => $isAr ? 'بيع معنا' : 'Sell with us',
        'tenantWebsite' => $isAr ? 'موقع الوكالة' : 'Tenant website',
        'profile' => $isAr ? 'ملفي' : 'My profile',
        'manageWorkspace' => $isAr ? 'إدارة الوكالة' : 'Manage workspace',
        'reviewInventory' => $isAr ? 'استعراض المخزون' : 'Review inventory',
        'continueBrowsing' => $isAr ? 'واصل التصفح' : 'Continue browsing',
        'discoverAgencies' => $isAr ? 'اكتشف الوكالات' : 'Discover agencies',
        'launchWorkspace' => $isAr ? 'ابدأ مساحة عمل' : 'Launch a workspace',
        'openPublicSite' => $isAr ? 'افتح الموقع العام' : 'Open the public site',
        'fallback' => $isAr ? 'غير متاح' : 'Not available',
        'workspaceCount' => $isAr ? 'المساحات' : 'Workspaces',
        'yes' => $isAr ? 'نعم' : 'Yes',
        'no' => $isAr ? 'لا' : 'No',
    ];
@endphp

@push('head')
    @include('mobile.partials.account-theme')
@endpush

@section('content')
    <div class="mpa-page">
        <div class="mpa-shell">
            <div id="profile-loading" class="mpa-state">
                <div class="mpa-spinner"></div>
                <p class="mt-4 text-sm font-semibold text-[#6d726c]">{{ $isAr ? 'جارٍ تحميل ملفك...' : 'Loading your profile...' }}</p>
            </div>

            <div id="profile-guest" class="hidden space-y-5">
                <section class="mpa-hero">
                    <div class="mpa-hero-copy px-5 py-6 sm:px-6 sm:py-7">
                        <div class="mpa-kicker">{{ $strings['guestKicker'] }}</div>
                        <div class="mt-4 mpa-ornament"></div>
                        <div class="mt-5 space-y-3">
                            <h1 class="text-[2rem] font-black leading-[1.02] tracking-[-0.05em] text-[#fff8ea] sm:text-[2.25rem]">{{ $strings['guestTitle'] }}</h1>
                            <p class="max-w-xl text-[0.96rem] leading-8 text-white/78">{{ $strings['guestText'] }}</p>
                        </div>
                        <div class="mt-5 flex flex-wrap gap-2.5">
                            <div class="mpa-chip">{{ $isAr ? 'هوية موحّدة' : 'Unified identity' }}</div>
                            <div class="mpa-chip">{{ $isAr ? 'سوق + وكالة' : 'Marketplace + workspace' }}</div>
                        </div>
                        <div class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <a href="{{ route('mobile.login') }}" class="mpa-button mpa-button-primary">{{ $strings['signIn'] }}</a>
                            <a href="{{ route('mobile.marketplace', ['auth' => 'register']) }}" class="mpa-button mpa-button-secondary">{{ $strings['createAccount'] }}</a>
                        </div>
                    </div>
                </section>

                <section class="mpa-card p-5">
                    <div class="mpa-section-head">
                        <div>
                            <div class="mpa-section-kicker">{{ $strings['marketplace'] }}</div>
                            <h2 class="mpa-section-title">{{ $isAr ? 'ابدأ من السوق أولاً' : 'Start from the marketplace first' }}</h2>
                        </div>
                    </div>
                    <p class="mpa-section-text">{{ $isAr ? 'إذا لم يكن لديك حساب بعد، يمكنك العودة إلى السوق، إنشاء حساب عادي، أو بدء وكالة جديدة من بيع معنا.' : 'If you do not have an account yet, return to the marketplace, create a normal account, or start a new agency from Sell with us.' }}</p>
                    <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <a href="{{ route('mobile.marketplace') }}" class="mpa-button mpa-button-secondary">{{ $strings['marketplace'] }}</a>
                        <a href="{{ route('mobile.register') }}" class="mpa-button mpa-button-secondary">{{ $strings['sellWithUs'] }}</a>
                    </div>
                </section>
            </div>

            <div id="profile-content" class="hidden space-y-5 pb-8">
                <section class="mpa-hero">
                    <div class="mpa-hero-copy px-5 py-6 sm:px-6 sm:py-7">
                        <div class="mpa-kicker">{{ $strings['heroKicker'] }}</div>
                        <div class="mt-4 mpa-ornament"></div>

                        <div class="mt-5 flex items-start gap-4">
                            <div id="profile-avatar" class="mpa-avatar"></div>
                            <div class="min-w-0 flex-1">
                                <h1 id="profile-name" class="truncate text-[1.9rem] font-black leading-[1.02] tracking-[-0.05em] text-[#fff8ea]"></h1>
                                <p id="profile-role" class="mt-2 text-sm font-semibold text-white/78"></p>
                                <p id="profile-story" class="mt-3 text-[0.95rem] leading-8 text-white/74"></p>
                            </div>
                        </div>

                        <div id="profile-hero-chips" class="mt-5 flex flex-wrap gap-2.5"></div>
                        <div id="profile-hero-stats" class="mt-5 grid grid-cols-3 gap-3"></div>
                    </div>
                </section>

                <section class="mpa-card p-5">
                    <div class="mpa-section-head">
                        <div>
                            <div class="mpa-section-kicker">{{ $strings['overviewTitle'] }}</div>
                            <h2 class="mpa-section-title">{{ $strings['overviewTitle'] }}</h2>
                        </div>
                    </div>
                    <p class="mpa-section-text">{{ $strings['overviewText'] }}</p>
                    <div id="profile-info" class="mpa-list mt-5"></div>
                    <div id="profile-info-empty" class="mpa-note mt-5 hidden">{{ $strings['emptyAccount'] }}</div>
                </section>

                <section id="profile-tenant-section" class="mpa-card hidden p-5">
                    <div class="mpa-section-head">
                        <div>
                            <div class="mpa-section-kicker">{{ $strings['tenantTitle'] }}</div>
                            <h2 class="mpa-section-title">{{ $strings['tenantTitle'] }}</h2>
                        </div>
                    </div>
                    <p class="mpa-section-text">{{ $strings['tenantText'] }}</p>
                    <div id="profile-tenant-info" class="mpa-list mt-5"></div>
                    <div id="profile-tenant-empty" class="mpa-note mt-5 hidden">{{ $strings['emptyTenant'] }}</div>
                </section>

                <section class="mpa-card p-5">
                    <div class="mpa-section-head">
                        <div>
                            <div class="mpa-section-kicker">{{ $strings['actionsTitle'] }}</div>
                            <h2 class="mpa-section-title">{{ $strings['actionsTitle'] }}</h2>
                        </div>
                    </div>
                    <p class="mpa-section-text">{{ $strings['actionsText'] }}</p>
                    <div id="profile-actions" class="mpa-action-grid mt-5"></div>
                </section>

                <section class="mpa-card mpa-danger p-5">
                    <div class="mpa-section-head">
                        <div>
                            <div class="mpa-section-kicker">{{ $strings['sessionTitle'] }}</div>
                            <h2 class="mpa-section-title">{{ $strings['sessionTitle'] }}</h2>
                        </div>
                    </div>
                    <p class="mpa-section-text">{{ $strings['sessionText'] }}</p>
                    <button type="button" id="profile-signout" class="mpa-button mpa-button-primary mt-5">{{ $strings['signOut'] }}</button>
                </section>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
const profileLang = document.documentElement.lang === 'ar' ? 'ar' : 'en';
const profileApiBase = window.__AQARI_API_BASE || '';
const profileStrings = @json($strings);

function profileEscape(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function profileInitials(name) {
    return String(name || '?')
        .trim()
        .split(/\s+/)
        .filter(Boolean)
        .slice(0, 2)
        .map(part => part.charAt(0).toUpperCase())
        .join('') || '?';
}

function profileHexToRgbTriplet(hex, fallback) {
    const value = String(hex || fallback || '').trim().replace('#', '');
    const chosen = /^[0-9a-fA-F]{6}$/.test(value) ? value : String(fallback || '0f5a46').replace('#', '');
    return `${parseInt(chosen.slice(0, 2), 16)} ${parseInt(chosen.slice(2, 4), 16)} ${parseInt(chosen.slice(4, 6), 16)}`;
}

function profileApplyBranding(tenant) {
    const primary = tenant?.branding?.primary_color || '#0f5a46';
    const accent = tenant?.branding?.accent_color || '#b6842f';
    document.body.style.setProperty('--account-primary', primary);
    document.body.style.setProperty('--account-accent', accent);
    document.body.style.setProperty('--account-primary-rgb', profileHexToRgbTriplet(primary, '#0f5a46'));
    document.body.style.setProperty('--account-accent-rgb', profileHexToRgbTriplet(accent, '#b6842f'));
}

function profileIcon(path, tone = 'primary') {
    const styles = tone === 'accent'
        ? 'background:rgba(182,132,47,.12);color:rgb(var(--account-accent-rgb));'
        : tone === 'soft'
            ? 'background:rgba(130,94,38,.08);color:#6e6759;'
            : 'background:rgba(15,90,70,.1);color:var(--account-primary);';

    return `<div class="mpa-icon-box" style="${styles}"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="${path}"/></svg></div>`;
}

function profileListRow(path, label, value, tone = 'primary') {
    return `<div class="mpa-list-row">${profileIcon(path, tone)}<div class="mpa-row-meta"><div class="mpa-row-label">${profileEscape(label)}</div><div class="mpa-row-value">${profileEscape(value)}</div></div></div>`;
}

function profileActionCard(action) {
    const attrs = action.external ? ' target="_blank" rel="noreferrer"' : '';
    return `<a href="${profileEscape(action.href)}" class="mpa-action-card"${attrs}>${profileIcon(action.icon, action.tone || 'primary')}<div class="mpa-action-text">${profileEscape(action.kicker)}</div><div class="mpa-action-title">${profileEscape(action.title)}</div><div class="mpa-action-meta">${profileEscape(action.text)}</div></a>`;
}

async function loadProfile() {
    const token = localStorage.getItem('aqari_mobile_token');

    try {
        let res;
        if (token) {
            res = await fetch(profileApiBase + '/api/mobile/auth/me', {
                headers: {
                    Authorization: 'Bearer ' + token,
                    Accept: 'application/json',
                    'X-Tenant-Slug': localStorage.getItem('aqari_mobile_tenant_slug') || '',
                },
            });
            if (!res.ok) {
                localStorage.removeItem('aqari_mobile_token');
                localStorage.removeItem('aqari_mobile_tenant_slug');
                localStorage.removeItem('aqari_mobile_user_name');
                localStorage.removeItem('aqari_mobile_user_role');
                res = null;
            }
        }

        // Session auth fallback: only in web browser mode (no remote API base)
        if (!res && !profileApiBase) {
            res = await fetch('/api/mobile/web/me', {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            });
        }

        if (!res.ok) {
            document.getElementById('profile-loading').classList.add('hidden');
            document.getElementById('profile-guest').classList.remove('hidden');
            return;
        }

        const json = await res.json();
        const user = json.user || {};
        const tenant = json.current_tenant || null;
        const role = user.is_staff ? profileStrings.staff : (user.is_resident ? profileStrings.resident : profileStrings.marketUser);
        const verified = user.email_verified_at ? profileStrings.verified : profileStrings.notVerified;
        const story = user.is_staff
            ? profileStrings.staffStory
            : user.is_resident
                ? profileStrings.residentStory
                : profileStrings.marketUserStory;

        profileApplyBranding(tenant);

        if (user.name) {
            localStorage.setItem('aqari_mobile_user_name', user.name);
        } else {
            localStorage.removeItem('aqari_mobile_user_name');
        }

        if (user.tenant_role) {
            localStorage.setItem('aqari_mobile_user_role', user.tenant_role);
        } else {
            localStorage.removeItem('aqari_mobile_user_role');
        }

        if (tenant?.slug) {
            localStorage.setItem('aqari_mobile_tenant_slug', tenant.slug);
        } else {
            localStorage.removeItem('aqari_mobile_tenant_slug');
        }

        const avatar = document.getElementById('profile-avatar');
        avatar.textContent = '';
        avatar.innerHTML = profileInitials(user.name);

        document.getElementById('profile-name').textContent = user.name || profileStrings.fallback;
        document.getElementById('profile-role').textContent = role + (tenant?.name ? ` · ${tenant.name}` : '');
        document.getElementById('profile-story').textContent = tenant?.summary?.description || story || profileStrings.defaultStory;

        const heroChips = [
            `<div class="mpa-chip">${profileEscape(verified)}</div>`,
            `<div class="mpa-chip">${profileEscape(role)}</div>`,
        ];
        if (tenant?.plan) {
            heroChips.push(`<div class="mpa-chip">${profileEscape(tenant.plan)}</div>`);
        }
        document.getElementById('profile-hero-chips').innerHTML = heroChips.join('');

        const workspaceCount = tenant ? 1 : (Array.isArray(user.tenants) ? user.tenants.length : 0);
        document.getElementById('profile-hero-stats').innerHTML = [
            `<div class="mpa-stat"><div class="mpa-stat-label">${profileEscape(profileStrings.workspace)}</div><div class="mpa-stat-value">${profileEscape(tenant?.name || profileStrings.fallback)}</div></div>`,
            `<div class="mpa-stat"><div class="mpa-stat-label">${profileEscape(profileStrings.access)}</div><div class="mpa-stat-value">${profileEscape(role)}</div></div>`,
            `<div class="mpa-stat"><div class="mpa-stat-label">${profileEscape(profileStrings.workspaceCount)}</div><div class="mpa-stat-value">${workspaceCount}</div></div>`,
        ].join('');

        const infoRows = [];
        if (user.email) {
            infoRows.push(profileListRow('M21.75 6.75v10.5A2.25 2.25 0 0 1 19.5 19.5h-15A2.25 2.25 0 0 1 2.25 17.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15A2.25 2.25 0 0 0 2.25 6.75m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75', profileStrings.email, user.email));
        }
        if (user.phone) {
            infoRows.push(profileListRow('M3 5.25A2.25 2.25 0 0 1 5.25 3h2.386a1.5 1.5 0 0 1 1.455 1.136l.877 3.508a1.5 1.5 0 0 1-.813 1.728l-1.293.646a11.055 11.055 0 0 0 5.121 5.121l.646-1.293a1.5 1.5 0 0 1 1.728-.813l3.508.877A1.5 1.5 0 0 1 21 16.364v2.386A2.25 2.25 0 0 1 18.75 21h-.75C9.82 21 3 14.18 3 6V5.25Z', profileStrings.phone, user.phone));
        }
        infoRows.push(profileListRow('M15.75 6.75a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z', profileStrings.role, role, 'soft'));
        if (user.tenant_role) {
            infoRows.push(profileListRow('M6.75 3.75h10.5A2.25 2.25 0 0 1 19.5 6v12a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 18V6a2.25 2.25 0 0 1 2.25-2.25Z M9 8.25h6m-6 3h6m-6 3h3', profileStrings.tenantRole, user.tenant_role, 'accent'));
        }
        infoRows.push(profileListRow('M9 12.75 11.25 15 15 9.75m6 2.25a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z', profileStrings.status, verified, user.email_verified_at ? 'primary' : 'accent'));

        document.getElementById('profile-info').innerHTML = infoRows.join('');
        document.getElementById('profile-info-empty').classList.toggle('hidden', infoRows.length > 0);

        const tenantSection = document.getElementById('profile-tenant-section');
        const tenantInfo = document.getElementById('profile-tenant-info');
        if (tenant) {
            tenantSection.classList.remove('hidden');
            const tenantRows = [];
            tenantRows.push(profileListRow('M19.5 21V6a2.25 2.25 0 0 0-2.25-2.25H6.75A2.25 2.25 0 0 0 4.5 6v15M19.5 21h1.125M19.5 21h-4.875M4.5 21H3.375M4.5 21h4.875M9 7.5h1.5m-1.5 3h1.5m4.5-3h1.5m-1.5 3h1.5m-6 10.5v-5.25A1.125 1.125 0 0 1 10.125 15h3.75A1.125 1.125 0 0 1 15 16.125V21', profileStrings.workspace, tenant.name || profileStrings.fallback));
            if (tenant.plan || tenant.subscription?.package_name) {
                tenantRows.push(profileListRow('M3.75 6.75h16.5v10.5H3.75V6.75Zm2.25 3h4.5m-4.5 3h7.5', profileStrings.plan, tenant.subscription?.package_name || tenant.plan, 'accent'));
            }
            if (tenant.summary?.coverage) {
                tenantRows.push(profileListRow('M2.25 12c0 5.385 4.365 9.75 9.75 9.75S21.75 17.385 21.75 12 17.385 2.25 12 2.25 2.25 6.615 2.25 12Zm4.5 0h10.5', profileStrings.coverage, tenant.summary.coverage));
            }
            if (tenant.summary?.address) {
                tenantRows.push(profileListRow('M12 21a8.25 8.25 0 0 0 8.25-8.25c0-5.303-8.25-10.5-8.25-10.5S3.75 7.447 3.75 12.75A8.25 8.25 0 0 0 12 21Zm0-6.75a2.25 2.25 0 1 1 0-4.5 2.25 2.25 0 0 1 0 4.5Z', profileStrings.address, tenant.summary.address, 'soft'));
            }
            if (tenant.summary?.website) {
                tenantRows.push(profileListRow('M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0 0c1.657 0 3-4.03 3-9s-1.343-9-3-9-3 4.03-3 9 1.343 9 3 9Zm-9-9h18', profileStrings.website, tenant.summary.website, 'accent'));
            }
            if (tenant.summary?.description) {
                tenantRows.push(profileListRow('M7.5 8.25h9m-9 3h9m-9 3h6', profileStrings.description, tenant.summary.description, 'soft'));
            }
            if (tenant.stats?.active_units_count !== undefined || tenant.stats?.agents_count !== undefined) {
                tenantRows.push(profileListRow('M3 7.5h18M3 12h18M3 16.5h18', `${profileStrings.units} / ${profileStrings.agents}`, `${tenant.stats?.active_units_count ?? tenant.stats?.units_count ?? 0} / ${tenant.stats?.agents_count ?? 0}`, 'accent'));
            }
            tenantInfo.innerHTML = tenantRows.join('');
            document.getElementById('profile-tenant-empty').classList.toggle('hidden', tenantRows.length > 0);
        } else {
            tenantSection.classList.add('hidden');
        }

        const actions = [];
        if (tenant) {
            actions.push({
                href: '/mobile/dashboard',
                icon: 'M3.75 18.75V9.75l8.25-6 8.25 6v9a2.25 2.25 0 0 1-2.25 2.25h-3.75V13.5A1.5 1.5 0 0 0 12.75 12h-1.5a1.5 1.5 0 0 0-1.5 1.5V21H6a2.25 2.25 0 0 1-2.25-2.25Z',
                kicker: profileStrings.dashboard,
                title: profileStrings.dashboard,
                text: user.is_staff ? profileStrings.manageWorkspace : profileStrings.profile,
            });
        }
        if (tenant && user.is_staff) {
            actions.push({
                href: '/mobile/units',
                icon: 'M3.75 6.75h16.5m-16.5 5.25h16.5m-16.5 5.25h16.5',
                kicker: profileStrings.myUnits,
                title: profileStrings.myUnits,
                text: profileStrings.reviewInventory,
                tone: 'accent',
            });
        }
        if (tenant?.url) {
            actions.push({
                href: tenant.url,
                icon: 'M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0 0c1.657 0 3-4.03 3-9s-1.343-9-3-9-3 4.03-3 9 1.343 9 3 9Zm-9-9h18',
                kicker: profileStrings.tenantWebsite,
                title: profileStrings.tenantWebsite,
                text: profileStrings.openPublicSite,
                external: true,
            });
        }
        actions.push({
            href: '/mobile/marketplace',
            icon: 'M21 21l-4.35-4.35m1.35-5.4a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z',
            kicker: profileStrings.marketplace,
            title: profileStrings.marketplace,
            text: profileStrings.continueBrowsing,
            tone: 'soft',
        });
        actions.push({
            href: '/mobile/tenants',
            icon: 'M3.75 21V6A2.25 2.25 0 0 1 6 3.75h12A2.25 2.25 0 0 1 20.25 6v15M3.75 21h16.5',
            kicker: profileStrings.agencies,
            title: profileStrings.agencies,
            text: profileStrings.discoverAgencies,
            tone: 'accent',
        });
        if (!tenant) {
            actions.push({
                href: '/mobile/register',
                icon: 'M12 4.5v15m7.5-7.5h-15',
                kicker: profileStrings.sellWithUs,
                title: profileStrings.sellWithUs,
                text: profileStrings.launchWorkspace,
            });
        }

        document.getElementById('profile-actions').innerHTML = actions.map(profileActionCard).join('');

        document.getElementById('profile-loading').classList.add('hidden');
        document.getElementById('profile-content').classList.remove('hidden');
    } catch (error) {
        console.error('Profile load error:', error);
        document.getElementById('profile-loading').classList.add('hidden');
        document.getElementById('profile-guest').classList.remove('hidden');
    }
}

document.getElementById('profile-signout')?.addEventListener('click', function () {
    const token = localStorage.getItem('aqari_mobile_token');
    if (token) {
        fetch(profileApiBase + '/api/mobile/auth/logout', {
            method: 'POST',
            headers: { Authorization: 'Bearer ' + token, Accept: 'application/json' },
        }).catch(() => {});
    }

    localStorage.removeItem('aqari_mobile_token');
    localStorage.removeItem('aqari_mobile_tenant_slug');
    localStorage.removeItem('aqari_mobile_user_name');
    localStorage.removeItem('aqari_mobile_user_role');
    window.location.href = '{{ route("mobile.marketplace") }}';
});

loadProfile();
</script>
@endpush
