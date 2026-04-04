@extends('mobile.layouts.app', [
    'title' => app()->getLocale() === 'ar' ? 'الوكالات' : 'Agencies',
    'show_back_button' => false,
    'body_class' => 'mobile-tenant-directory-shell',
])

@section('full_width', true)

@php
    $isAr = app()->getLocale() === 'ar';

    $ui = [
        'heroEyebrow' => $isAr ? 'دليل وكالات العراق' : 'Iraq Agency Directory',
        'heroTitle' => $isAr ? 'ابدأ من الوكالة المناسبة' : 'Start with the right agency',
        'heroText' => $isAr
            ? 'صفحة اكتشاف أوضح للوكالات النشطة، تقرأ هويتها بسرعة ثم تدخل مباشرة إلى واجهتها العقارية.'
            : 'A clearer discovery page for active agencies so visitors can read the brand fast and step directly into its storefront.',
        'searchLabel' => $isAr ? 'ابحث عن وكالة أو مدينة' : 'Search agency or city',
        'searchPlaceholder' => $isAr ? 'ابحث بالاسم أو النطاق أو التغطية...' : 'Search by name, slug, or coverage...',
        'searchAction' => $isAr ? 'بحث' : 'Search',
        'agencies' => $isAr ? 'الوكالات' : 'Agencies',
        'listings' => $isAr ? 'العروض النشطة' : 'Live listings',
        'agents' => $isAr ? 'الوكلاء' : 'Agents',
        'spotlightKicker' => $isAr ? 'البداية' : 'Start here',
        'spotlightTitle' => $isAr ? 'واجهات تستحق البداية منها' : 'Storefronts worth starting with',
        'directoryKicker' => $isAr ? 'الدليل الكامل' : 'Full directory',
        'directoryTitle' => $isAr ? 'كل الوكالات النشطة' : 'Every active agency',
        'directoryText' => $isAr ? 'ابحث، قارن، ثم افتح الواجهة التي تناسب رحلة العميل.' : 'Search, compare, then open the storefront that fits the customer journey.',
        'resultsLine' => $isAr ? 'نعرض :count وكالة مطابقة الآن' : 'Showing :count matching agencies',
        'openStorefront' => $isAr ? 'افتح الواجهة' : 'Open storefront',
        'browseAgency' => $isAr ? 'تصفّح الوكالة' : 'Browse agency',
        'coverage' => $isAr ? 'التغطية' : 'Coverage',
        'active' => $isAr ? 'نشط' : 'Active',
        'team' => $isAr ? 'فريق' : 'Team',
        'backMarket' => $isAr ? 'العودة إلى السوق' : 'Back to marketplace',
        'fallbackDescription' => $isAr ? 'واجهة عقارية موثوقة تعرض مخزوناً حياً وصوراً حقيقية بتجربة أوضح على الموبايل.' : 'A trusted real estate storefront with live inventory, real photos, and a clearer mobile journey.',
        'coverageFallback' => $isAr ? 'تغطية عراقية نشطة' : 'Active Iraq coverage',
        'noResultsTitle' => $isAr ? 'لا توجد وكالات مطابقة الآن' : 'No agencies match right now',
        'noResultsText' => $isAr ? 'جرّب بحثاً مختلفاً أو امسح البحث لرؤية كل الوكالات النشطة.' : 'Try another search or clear it to see every active agency.',
        'loadMore' => $isAr ? 'تحميل المزيد' : 'Load more',
        'loading' => $isAr ? 'جاري تحميل الوكالات...' : 'Loading agencies...',
        'searching' => $isAr ? 'جاري تحديث النتائج...' : 'Refreshing results...',
    ];
@endphp

@push('head')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root{
        --mtd-ink:#1f2a24;
        --mtd-palm:#0f5a46;
        --mtd-brass:#b6842f;
        --mtd-line:rgba(130,94,38,.16);
        --mtd-soft:rgba(255,252,246,.96);
    }
    body.mobile-tenant-directory-shell{
        background:
            radial-gradient(circle at top left, rgba(182,132,47,.14), transparent 24%),
            radial-gradient(circle at top right, rgba(15,90,70,.1), transparent 24%),
            linear-gradient(180deg, #eee2cc 0, #f7efdf 300px, #fbf7ef 100%);
        color:var(--mtd-ink);
        font-family:'Manrope',system-ui,sans-serif;
    }
    html[dir="rtl"] body.mobile-tenant-directory-shell{font-family:'Cairo','Manrope',system-ui,sans-serif}
    body.mobile-tenant-directory-shell aside{background:rgba(252,248,241,.98);color:var(--mtd-ink)}
    body.mobile-tenant-directory-shell aside .bg-gradient-to-br.from-emerald-600.to-emerald-700,
    body.mobile-tenant-directory-shell header.sticky{
        background:linear-gradient(145deg, rgba(15,32,26,.96), rgba(15,90,70,.9) 56%, rgba(48,33,15,.86)) !important;
    }
    .mtd-hero{
        position:relative;overflow:hidden;border-radius:2rem;color:#fff8ea;
        background:
            radial-gradient(circle at top left, rgba(255,255,255,.14), transparent 28%),
            linear-gradient(145deg, rgba(15,32,26,.96), rgba(15,90,70,.88) 54%, rgba(48,33,15,.82));
        box-shadow:0 30px 64px -34px rgba(28,22,10,.58);
    }
    .mtd-hero::after{
        content:"";position:absolute;inset:0;
        background:
            linear-gradient(180deg, rgba(10,16,13,.04), rgba(10,16,13,.22)),
            radial-gradient(circle at 86% 14%, rgba(255,255,255,.08), transparent 24%);
        pointer-events:none;
    }
    .mtd-ornament{
        height:10px;width:104px;border-radius:999px;
        background:
            linear-gradient(90deg, rgba(15,90,70,.16), rgba(182,132,47,.34), rgba(15,90,70,.16)),
            repeating-linear-gradient(90deg, transparent 0 10px, rgba(182,132,47,.56) 10px 14px, transparent 14px 24px);
    }
    .mtd-chip{
        display:inline-flex;align-items:center;gap:.55rem;border-radius:999px;
        border:1px solid rgba(255,255,255,.14);background:rgba(255,255,255,.08);
        padding:.68rem .95rem;font-size:.68rem;font-weight:800;letter-spacing:.14em;text-transform:uppercase;color:rgba(255,248,236,.88)
    }
    .mtd-chip::before{
        content:"";width:.45rem;height:.45rem;border-radius:999px;background:var(--mtd-brass);
        box-shadow:0 0 0 4px rgba(182,132,47,.16);
    }
    .mtd-stat{
        border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.08);
        border-radius:1.35rem;padding:.95rem;backdrop-filter:blur(14px)
    }
    .mtd-stat-label{font-size:.65rem;letter-spacing:.18em;text-transform:uppercase;color:rgba(255,244,221,.62);font-weight:700}
    .mtd-stat-value{margin-top:.35rem;font-size:1rem;line-height:1.2;font-weight:800;color:#fff8ea}
    .mtd-surface{
        border:1px solid rgba(182,132,47,.22);background:rgba(255,248,235,.97);
        box-shadow:0 26px 54px -34px rgba(19,24,20,.44)
    }
    .mtd-input{
        border:1px solid rgba(130,94,38,.16);background:rgba(255,255,255,.86);color:var(--mtd-ink);
        transition:border-color .2s ease, box-shadow .2s ease
    }
    .mtd-input:focus{outline:none;border-color:rgba(182,132,47,.72);box-shadow:0 0 0 4px rgba(182,132,47,.12)}
    .mtd-submit{
        background:linear-gradient(135deg, var(--mtd-palm), var(--mtd-brass));color:#fff;
        box-shadow:0 18px 34px -18px rgba(15,90,70,.8)
    }
    .mtd-scroll{display:flex;gap:.9rem;overflow-x:auto;padding-bottom:.25rem;scroll-snap-type:x mandatory;-ms-overflow-style:none;scrollbar-width:none}
    .mtd-scroll::-webkit-scrollbar{display:none}
    .mtd-card{
        --agency-primary:#0f5a46;--agency-accent:#b6842f;
        position:relative;overflow:hidden;border-radius:1.7rem;border:1px solid var(--mtd-line);
        text-decoration:none;color:inherit;background:var(--mtd-soft);box-shadow:0 22px 46px -34px rgba(55,38,12,.36);
        transition:transform .22s ease, box-shadow .22s ease, border-color .22s ease;
    }
    .mtd-card:hover{transform:translateY(-3px);border-color:rgba(182,132,47,.24);box-shadow:0 28px 52px -30px rgba(55,38,12,.44)}
    .mtd-spotlight{min-width:308px;scroll-snap-align:start}
    .mtd-card-hero{
        position:relative;overflow:hidden;padding:1rem 1rem 1.1rem;color:#fff8ea;
        background:
            radial-gradient(circle at top right, rgba(255,255,255,.16), transparent 34%),
            linear-gradient(145deg, rgba(15,32,26,.96), var(--agency-primary) 52%, var(--agency-accent));
    }
    .mtd-logo{
        display:flex;height:4.1rem;width:4.1rem;align-items:center;justify-content:center;overflow:hidden;
        border-radius:1.25rem;border:1px solid rgba(255,255,255,.14);background:rgba(255,255,255,.12)
    }
    .mtd-logo img{height:100%;width:100%;object-fit:cover}
    .mtd-logo-fallback{font-size:1.05rem;font-weight:900;letter-spacing:.12em;color:#fff8ea}
    .mtd-badge{
        display:inline-flex;align-items:center;justify-content:center;border-radius:999px;padding:.45rem .72rem;
        font-size:.62rem;font-weight:800;letter-spacing:.14em;text-transform:uppercase;background:rgba(255,255,255,.12);color:#fff8ea;border:1px solid rgba(255,255,255,.14)
    }
    .mtd-copy{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
    .mtd-pill{border-radius:999px;padding:.38rem .7rem;font-size:.62rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase}
    .mtd-pill.palm{background:rgba(15,90,70,.08);color:var(--mtd-palm)}
    .mtd-pill.brass{background:rgba(182,132,47,.12);color:var(--mtd-brass)}
    .mtd-pill.clay{background:rgba(157,90,59,.08);color:#9d5a3b}
    .mtd-loading,.mtd-empty{
        border-radius:1.5rem;border:1px dashed rgba(130,94,38,.2);background:rgba(255,252,246,.84);padding:1.7rem 1.15rem;text-align:center
    }
    .mtd-loading{display:flex;align-items:center;justify-content:center;gap:.65rem}
    .mtd-spinner{height:1.1rem;width:1.1rem;border-radius:999px;border:2px solid rgba(15,90,70,.2);border-top-color:var(--mtd-palm);animation:mtd-spin .9s linear infinite}
    .mtd-load-more{
        display:inline-flex;align-items:center;justify-content:center;border-radius:1.1rem;border:1px solid rgba(182,132,47,.22);
        background:rgba(255,249,239,.96);padding:.95rem 1.2rem;font-size:.8rem;font-weight:900;letter-spacing:.14em;text-transform:uppercase;color:var(--mtd-palm)
    }
    @keyframes mtd-spin{to{transform:rotate(360deg)}}
</style>
@endpush

@section('content')
<div class="min-h-screen pb-12">
    <div class="space-y-6 px-4 py-4">
        <section class="mtd-hero px-5 py-6">
            <div class="relative z-[1]">
                <div class="mtd-ornament"></div>
                <div class="mt-4 inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-3 py-1 text-[11px] font-extrabold uppercase tracking-[0.22em] text-white/85">{{ $ui['heroEyebrow'] }}</div>
                <h1 class="mt-5 text-[2rem] font-black leading-[1.02] tracking-[-0.05em] text-[#fff8ea]">{{ $ui['heroTitle'] }}</h1>
                <p class="mt-4 max-w-[28rem] text-sm leading-7 text-white/78">{{ $ui['heroText'] }}</p>
                <div class="mt-5 flex flex-wrap gap-2.5">
                    <span class="mtd-chip">{{ $ui['directoryText'] }}</span>
                    <a href="{{ route('mobile.marketplace') }}" class="inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-[0.16em] text-[#fff8ea]">
                        <span>{{ $ui['backMarket'] }}</span>
                        <svg class="h-3.5 w-3.5 {{ $isAr ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                </div>
                <div class="mt-6 grid grid-cols-3 gap-3">
                    <div class="mtd-stat"><div class="mtd-stat-label">{{ $ui['agencies'] }}</div><div id="mtd-stat-agencies" class="mtd-stat-value">0</div></div>
                    <div class="mtd-stat"><div class="mtd-stat-label">{{ $ui['listings'] }}</div><div id="mtd-stat-listings" class="mtd-stat-value">0</div></div>
                    <div class="mtd-stat"><div class="mtd-stat-label">{{ $ui['agents'] }}</div><div id="mtd-stat-agents" class="mtd-stat-value">0</div></div>
                </div>
            </div>
        </section>

        <section class="mtd-surface rounded-[1.7rem] p-4">
            <form id="mtd-search-form" class="space-y-3">
                <label for="mtd-search" class="block text-[11px] font-extrabold uppercase tracking-[0.22em] text-[color:var(--mtd-brass)]">{{ $ui['searchLabel'] }}</label>
                <div class="flex items-center gap-2">
                    <input id="mtd-search" name="q" type="search" placeholder="{{ $ui['searchPlaceholder'] }}" class="mtd-input min-w-0 flex-1 rounded-[1.15rem] px-4 py-3 text-sm font-semibold">
                    <button type="submit" class="mtd-submit rounded-[1.15rem] px-4 py-3 text-sm font-extrabold uppercase tracking-[0.14em]">{{ $ui['searchAction'] }}</button>
                </div>
                <p id="mtd-results-caption" class="text-sm leading-7 text-slate-600">{{ str_replace(':count', '0', $ui['resultsLine']) }}</p>
            </form>
        </section>

        <section class="space-y-4">
            <div>
                <p class="text-[11px] font-extrabold uppercase tracking-[0.22em] text-[color:var(--mtd-brass)]">{{ $ui['spotlightKicker'] }}</p>
                <h2 class="mt-2 text-[1.55rem] font-black leading-none tracking-[-0.05em] text-[color:var(--mtd-ink)]">{{ $ui['spotlightTitle'] }}</h2>
            </div>
            <div id="mtd-spotlight" class="mtd-scroll">
                <div class="mtd-loading min-w-full"><span class="mtd-spinner"></span><span class="text-sm font-semibold text-slate-500">{{ $ui['loading'] }}</span></div>
            </div>
        </section>

        <section class="space-y-4">
            <div>
                <p class="text-[11px] font-extrabold uppercase tracking-[0.22em] text-[color:var(--mtd-brass)]">{{ $ui['directoryKicker'] }}</p>
                <h2 class="mt-2 text-[1.55rem] font-black leading-none tracking-[-0.05em] text-[color:var(--mtd-ink)]">{{ $ui['directoryTitle'] }}</h2>
                <p class="mt-3 text-sm leading-7 text-slate-600">{{ $ui['directoryText'] }}</p>
            </div>
            <div id="mtd-feed" class="grid gap-4">
                <div class="mtd-loading"><span class="mtd-spinner"></span><span class="text-sm font-semibold text-slate-500">{{ $ui['loading'] }}</span></div>
            </div>
            <div id="mtd-empty" class="mtd-empty hidden">
                <div class="text-lg font-black tracking-[-0.03em] text-[color:var(--mtd-ink)]">{{ $ui['noResultsTitle'] }}</div>
                <p class="mt-2 text-sm leading-7 text-slate-500">{{ $ui['noResultsText'] }}</p>
            </div>
            <div id="mtd-load-more-wrap" class="hidden justify-center pt-1">
                <button id="mtd-load-more" type="button" class="mtd-load-more">{{ $ui['loadMore'] }}</button>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
const lang = document.documentElement.lang.startsWith('ar') ? 'ar' : 'en';
const strings = @json($ui, JSON_UNESCAPED_UNICODE);
const apiBase = window.__AQARI_API_BASE || '';
const searchForm = document.getElementById('mtd-search-form');
const searchField = document.getElementById('mtd-search');
const spotlightEl = document.getElementById('mtd-spotlight');
const feedEl = document.getElementById('mtd-feed');
const emptyEl = document.getElementById('mtd-empty');
const loadMoreWrap = document.getElementById('mtd-load-more-wrap');
const loadMoreButton = document.getElementById('mtd-load-more');
const resultsCaption = document.getElementById('mtd-results-caption');
const statAgencies = document.getElementById('mtd-stat-agencies');
const statListings = document.getElementById('mtd-stat-listings');
const statAgents = document.getElementById('mtd-stat-agents');

let currentPage = 1;
let lastPage = 1;
let currentQuery = '';
let spotlightLocked = false;
let debounceTimer;

function formatNumber(value) {
    return new Intl.NumberFormat(lang === 'ar' ? 'ar-IQ' : 'en-US').format(Number(value || 0));
}

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[char]));
}

function initials(name) {
    return String(name || '').trim().split(/\s+/).filter(Boolean).slice(0, 2).map((part) => part.charAt(0).toUpperCase()).join('') || 'AQ';
}

function normalizeColor(color, fallback) {
    return /^#?[0-9a-fA-F]{6}$/.test(String(color || '').trim()) ? `#${String(color).trim().replace('#', '')}` : fallback;
}

function agencyDescription(tenant) {
    const direct = tenant.summary?.description?.trim();
    if (direct) return direct;
    const units = Number(tenant.stats?.active_units_count ?? tenant.stats?.units_count ?? 0);
    const coverage = tenant.summary?.coverage?.trim() || tenant.summary?.city?.trim();

    if (lang === 'ar') {
        if (coverage && units) return `${tenant.name} تعرض حالياً ${formatNumber(units)} عقاراً نشطاً مع تغطية في ${coverage}.`;
        if (units) return `${tenant.name} تعرض حالياً ${formatNumber(units)} عقاراً نشطاً بتجربة أوضح على الموبايل.`;
    } else {
        if (coverage && units) return `${tenant.name} currently shows ${formatNumber(units)} live listings with active coverage across ${coverage}.`;
        if (units) return `${tenant.name} currently shows ${formatNumber(units)} live listings with a clearer mobile flow.`;
    }

    return strings.fallbackDescription;
}

function agencyCoverage(tenant) {
    return tenant.summary?.coverage || tenant.summary?.city || tenant.summary?.address || strings.coverageFallback;
}

function agencyLogoHtml(tenant) {
    if (tenant.branding?.logo_url) {
        return `<img src="${escapeHtml(tenant.branding.logo_url)}" alt="${escapeHtml(tenant.name)}" loading="lazy">`;
    }

    return `<span class="mtd-logo-fallback">${escapeHtml(initials(tenant.name))}</span>`;
}

function agencyCardHtml(tenant, spotlight = false) {
    const primary = normalizeColor(tenant.branding?.primary_color, '#0f5a46');
    const accent = normalizeColor(tenant.branding?.accent_color, '#b6842f');
    const unitsCount = Number(tenant.stats?.units_count ?? 0);
    const activeUnits = Number(tenant.stats?.active_units_count ?? unitsCount);
    const agentsCount = Number(tenant.stats?.agents_count ?? 0);
    const description = agencyDescription(tenant);
    const coverage = agencyCoverage(tenant);

    return `
        <a href="/mobile/tenants/${encodeURIComponent(tenant.slug)}" class="mtd-card ${spotlight ? 'mtd-spotlight' : ''}" style="--agency-primary:${primary};--agency-accent:${accent};">
            <div class="mtd-card-hero">
                <div class="relative z-[1]">
                    <div class="flex items-start justify-between gap-3">
                        <div class="mtd-logo">${agencyLogoHtml(tenant)}</div>
                        <div class="flex flex-wrap justify-end gap-2">
                            <span class="mtd-badge">${escapeHtml(tenant.slug)}</span>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-[11px] font-extrabold uppercase tracking-[0.16em] text-[rgba(255,241,212,.86)]">${escapeHtml(coverage)}</div>
                        <h3 class="mt-2 text-[1.5rem] font-black leading-[1.04] tracking-[-0.05em] text-[#fff8ea]">${escapeHtml(tenant.name)}</h3>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <span class="mtd-badge">${strings.browseAgency}</span>
                    </div>
                </div>
            </div>
            <div class="p-4">
                <div class="text-[11px] font-extrabold uppercase tracking-[0.16em] text-[color:var(--mtd-brass)]">${strings.coverage}</div>
                <div class="mt-1 text-sm font-semibold leading-7 text-slate-600">${escapeHtml(coverage)}</div>
                <p class="mtd-copy mt-3 text-sm leading-7 text-slate-600">${escapeHtml(description)}</p>
                <div class="mt-4 flex flex-wrap gap-2">
                    <span class="mtd-pill palm">${formatNumber(activeUnits)} ${strings.active}</span>
                    <span class="mtd-pill brass">${formatNumber(agentsCount)} ${strings.agents}</span>
                    <span class="mtd-pill clay">${escapeHtml(tenant.slug)}</span>
                </div>
                <div class="mt-4 grid grid-cols-3 gap-2 border-t border-[rgba(130,94,38,.12)] pt-4">
                    <div><strong class="block text-base font-black text-[color:var(--mtd-ink)]">${formatNumber(unitsCount)}</strong><span class="text-[11px] font-extrabold uppercase tracking-[0.14em] text-slate-400">${strings.listings}</span></div>
                    <div><strong class="block text-base font-black text-[color:var(--mtd-ink)]">${formatNumber(activeUnits)}</strong><span class="text-[11px] font-extrabold uppercase tracking-[0.14em] text-slate-400">${strings.active}</span></div>
                    <div><strong class="block text-base font-black text-[color:var(--mtd-ink)]">${formatNumber(agentsCount)}</strong><span class="text-[11px] font-extrabold uppercase tracking-[0.14em] text-slate-400">${strings.team}</span></div>
                </div>
                <div class="mt-4 inline-flex items-center gap-2 text-[11px] font-extrabold uppercase tracking-[0.16em] text-[color:var(--mtd-palm)]">
                    <span>${strings.openStorefront}</span>
                    <svg class="h-3.5 w-3.5 ${lang === 'ar' ? 'rotate-180' : ''}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </div>
            </div>
        </a>
    `;
}

function renderSpotlight(tenants) {
    if (spotlightLocked) return;
    if (!tenants.length) {
        spotlightEl.innerHTML = `<div class="mtd-empty min-w-full"><div class="text-base font-black tracking-[-0.03em] text-[color:var(--mtd-ink)]">${strings.noResultsTitle}</div><p class="mt-2 text-sm leading-7 text-slate-500">${strings.noResultsText}</p></div>`;
        return;
    }
    spotlightEl.innerHTML = tenants.slice(0, 4).map((tenant) => agencyCardHtml(tenant, true)).join('');
    spotlightLocked = true;
}

function renderFeed(tenants, page) {
    if (page === 1) feedEl.innerHTML = '';
    if (!tenants.length) {
        if (page === 1) emptyEl.classList.remove('hidden');
        return;
    }
    emptyEl.classList.add('hidden');
    feedEl.insertAdjacentHTML('beforeend', tenants.map((tenant) => agencyCardHtml(tenant)).join(''));
}

function updateSummary(json) {
    const summary = json.summary || {};
    statAgencies.textContent = formatNumber(summary.agencies_count ?? json.meta?.total ?? 0);
    statListings.textContent = formatNumber(summary.active_units_count ?? 0);
    statAgents.textContent = formatNumber(summary.agents_count ?? 0);
    resultsCaption.textContent = strings.resultsLine.replace(':count', formatNumber(json.meta?.total ?? 0));
}

function updateLoadMore() {
    loadMoreWrap.classList.toggle('hidden', currentPage >= lastPage);
    loadMoreWrap.classList.toggle('flex', currentPage < lastPage);
}

async function loadTenants(page = 1, keepSpotlight = false) {
    const params = new URLSearchParams({ per_page: '12', page: String(page) });
    if (currentQuery) params.set('q', currentQuery);

    if (page === 1) {
        feedEl.innerHTML = `<div class="mtd-loading"><span class="mtd-spinner"></span><span class="text-sm font-semibold text-slate-500">${currentQuery ? strings.searching : strings.loading}</span></div>`;
        emptyEl.classList.add('hidden');
        if (!keepSpotlight) {
            spotlightLocked = false;
            spotlightEl.innerHTML = `<div class="mtd-loading min-w-full"><span class="mtd-spinner"></span><span class="text-sm font-semibold text-slate-500">${strings.loading}</span></div>`;
        }
    }

    const response = await fetch(`${apiBase}/api/mobile/tenants?${params.toString()}`, { headers: { Accept: 'application/json' } });
    if (!response.ok) throw new Error(`Failed to load agencies: ${response.status}`);

    const json = await response.json();
    currentPage = Number(json.meta?.current_page ?? 1);
    lastPage = Number(json.meta?.last_page ?? 1);

    if (!keepSpotlight) renderSpotlight(json.data || []);
    renderFeed(json.data || [], page);
    updateSummary(json);
    updateLoadMore();
}

searchForm?.addEventListener('submit', async (event) => {
    event.preventDefault();
    currentQuery = searchField?.value.trim() || '';
    try { await loadTenants(1); } catch (error) { console.error(error); }
});

searchField?.addEventListener('input', () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(async () => {
        currentQuery = searchField.value.trim();
        try { await loadTenants(1); } catch (error) { console.error(error); }
    }, 420);
});

loadMoreButton?.addEventListener('click', async () => {
    try { await loadTenants(currentPage + 1, true); } catch (error) { console.error(error); }
});

loadTenants(1).catch((error) => {
    console.error(error);
    feedEl.innerHTML = '';
    emptyEl.classList.remove('hidden');
});
</script>
@endpush
