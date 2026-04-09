<script>
(async () => {
    const lang = @json(app()->getLocale() === 'ar' ? 'ar' : 'en');
    const strings = @json($strings);
    const token = localStorage.getItem('aqari_mobile_token');
    const tenantSlug = localStorage.getItem('aqari_mobile_tenant_slug');
    const loading = document.getElementById('dash-loading');
    const noAuth = document.getElementById('dash-no-auth');
    const errorEl = document.getElementById('dash-error');
    const content = document.getElementById('dash-content');

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function hexToRgbTriplet(hex, fallback) {
        const value = String(hex || fallback || '').trim().replace('#', '');
        const chosen = /^[0-9a-fA-F]{6}$/.test(value) ? value : String(fallback || '0f5a46').replace('#', '');
        return `${parseInt(chosen.slice(0, 2), 16)} ${parseInt(chosen.slice(2, 4), 16)} ${parseInt(chosen.slice(4, 6), 16)}`;
    }

    function applyBranding(tenant) {
        const primary = tenant?.branding?.primary_color || '#0f5a46';
        const accent = tenant?.branding?.accent_color || '#b6842f';
        document.body.style.setProperty('--account-primary', primary);
        document.body.style.setProperty('--account-accent', accent);
        document.body.style.setProperty('--account-primary-rgb', hexToRgbTriplet(primary, '#0f5a46'));
        document.body.style.setProperty('--account-accent-rgb', hexToRgbTriplet(accent, '#b6842f'));
    }

    function formatNumber(value) {
        return new Intl.NumberFormat(lang === 'ar' ? 'ar-JO' : 'en-JO').format(Number(value || 0));
    }

    function formatCurrency(value, currency) {
        return `${currency || 'JOD'} ${formatNumber(value)}`;
    }

    function iconBox(path, tone = 'primary') {
        const styles = tone === 'accent'
            ? 'background:rgba(182,132,47,.12);color:rgb(var(--account-accent-rgb));'
            : tone === 'soft'
                ? 'background:rgba(130,94,38,.08);color:#6e6759;'
                : 'background:rgba(15,90,70,.1);color:var(--account-primary);';

        return `<div class="mpa-icon-box" style="${styles}"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="${path}"/></svg></div>`;
    }

    function metricCard(label, value, icon, meta = '', tone = 'primary') {
        return `<div class="mpa-metric-card">${iconBox(icon, tone)}<div class="mpa-metric-label">${escapeHtml(label)}</div><div class="mpa-metric-value">${escapeHtml(value)}</div>${meta ? `<div class="mpa-action-meta">${escapeHtml(meta)}</div>` : ''}</div>`;
    }

    function actionCard(action) {
        const attrs = action.external ? ' target="_blank" rel="noreferrer"' : '';
        return `<a href="${escapeHtml(action.href)}" class="mpa-action-card"${attrs}>${iconBox(action.icon, action.tone || 'primary')}<div class="mpa-action-text">${escapeHtml(action.kicker)}</div><div class="mpa-action-title">${escapeHtml(action.title)}</div><div class="mpa-action-meta">${escapeHtml(action.text)}</div></a>`;
    }

    function listRow(icon, label, value, tone = 'primary') {
        return `<div class="mpa-list-row">${iconBox(icon, tone)}<div class="mpa-row-meta"><div class="mpa-row-label">${escapeHtml(label)}</div><div class="mpa-row-value">${escapeHtml(value)}</div></div></div>`;
    }

    function progressRow(label, value, total) {
        const safeTotal = Math.max(Number(total || 0), 1);
        const percent = Math.max(0, Math.min(100, Math.round((Number(value || 0) / safeTotal) * 100)));
        return `<div class="mpa-progress-row"><div class="mpa-progress-label"><span>${escapeHtml(label)}</span><span>${percent}%</span></div><div class="mpa-progress-bar"><div class="mpa-progress-fill" style="width:${percent}%"></div></div></div>`;
    }

    function timelineItem(title, subtitle, badge, meta) {
        return `<div class="mpa-timeline-item"><div><div class="text-sm font-black text-[color:var(--account-ink)]">${escapeHtml(title)}</div><div class="mt-1 text-sm leading-7 text-[#5f655f]">${escapeHtml(subtitle)}</div></div><div class="text-right"><div class="mpa-pill ${badge.tone}">${escapeHtml(badge.label)}</div><div class="mt-3 text-[0.72rem] font-semibold uppercase tracking-[0.12em] text-[#8b846f]">${escapeHtml(meta)}</div></div></div>`;
    }

    if (!token) {
        localStorage.removeItem('aqari_mobile_user_role');
        loading.classList.add('hidden');
        noAuth.classList.remove('hidden');
        return;
    }

    try {
        const res = await fetch((window.__AQARI_API_BASE || '') + '/api/mobile/dashboard', {
            headers: {
                Accept: 'application/json',
                Authorization: `Bearer ${token}`,
                'X-Tenant-Slug': tenantSlug || '',
            },
        });

        if (res.status === 401) {
            localStorage.removeItem('aqari_mobile_token');
            localStorage.removeItem('aqari_mobile_tenant_slug');
            localStorage.removeItem('aqari_mobile_user_name');
            localStorage.removeItem('aqari_mobile_user_role');
            loading.classList.add('hidden');
            noAuth.classList.remove('hidden');
            return;
        }

        if (!res.ok) {
            throw new Error(res.status === 404 ? strings.errorText : (res.statusText || strings.errorText));
        }

        const data = await res.json();
        const tenant = data.tenant || {};
        const user = data.user || {};
        const isResident = data.role === 'resident';
        const metrics = data.dashboard?.metrics || {};
        const summaryText = tenant.summary?.description || tenant.summary?.coverage || strings.heroFallback;

        applyBranding(tenant);

        loading.classList.add('hidden');
        content.classList.remove('hidden');

        const logo = document.getElementById('dash-tenant-logo');
        if (tenant.branding?.logo_url) {
            logo.innerHTML = `<img src="${escapeHtml(tenant.branding.logo_url)}" alt="${escapeHtml(tenant.name || '')}">`;
        } else {
            logo.textContent = String(tenant.name || strings.dashboard).trim().split(/\s+/).slice(0, 2).map(part => part.charAt(0).toUpperCase()).join('') || 'A';
        }

        document.getElementById('dash-hero-kicker').textContent = isResident ? strings.residentRole : strings.staffRole;
        document.getElementById('dash-tenant-name').textContent = tenant.name || strings.fallback;
        document.getElementById('dash-user-info').textContent = `${user.name || strings.fallback} · ${isResident ? strings.residentRole : strings.staffRole}`;
        document.getElementById('dash-hero-summary').textContent = summaryText;

        const heroChips = [
            `<div class="mpa-chip">${escapeHtml(isResident ? strings.residentRole : strings.staffRole)}</div>`,
            tenant.plan ? `<div class="mpa-chip">${escapeHtml(tenant.plan)}</div>` : '',
            tenant.summary?.coverage ? `<div class="mpa-chip">${escapeHtml(tenant.summary.coverage)}</div>` : '',
        ].filter(Boolean);
        document.getElementById('dash-hero-chips').innerHTML = heroChips.join('');

        if (isResident) {
            const leaseCount = Array.isArray(data.leases) ? data.leases.length : 0;
            document.getElementById('dash-hero-stats').innerHTML = [
                `<div class="mpa-stat"><div class="mpa-stat-label">${escapeHtml(strings.heroWorkspace)}</div><div class="mpa-stat-value">${escapeHtml(tenant.name || strings.fallback)}</div></div>`,
                `<div class="mpa-stat"><div class="mpa-stat-label">${escapeHtml(strings.heroAccess)}</div><div class="mpa-stat-value">${escapeHtml(strings.residentRole)}</div></div>`,
                `<div class="mpa-stat"><div class="mpa-stat-label">${escapeHtml(strings.leases)}</div><div class="mpa-stat-value">${leaseCount}</div></div>`,
            ].join('');
        } else {
            document.getElementById('dash-hero-stats').innerHTML = [
                `<div class="mpa-stat"><div class="mpa-stat-label">${escapeHtml(strings.units)}</div><div class="mpa-stat-value">${metrics.units ?? 0}</div></div>`,
                `<div class="mpa-stat"><div class="mpa-stat-label">${escapeHtml(strings.occupancy)}</div><div class="mpa-stat-value">${formatNumber(metrics.occupancy_rate ?? 0)}%</div></div>`,
                `<div class="mpa-stat"><div class="mpa-stat-label">${escapeHtml(strings.monthlyRent)}</div><div class="mpa-stat-value">${escapeHtml(formatCurrency(metrics.monthly_rent ?? 0, metrics.rent_currency || 'JOD'))}</div></div>`,
            ].join('');
        }

        const heroActions = [];
        if (!isResident) {
            heroActions.push(`<a href="#" id="dash-web-link" class="mpa-button mpa-button-primary">${escapeHtml(strings.openWeb)}</a>`);
        } else if (tenant.url) {
            heroActions.push(`<a href="${escapeHtml(tenant.url)}" target="_blank" rel="noreferrer" class="mpa-button mpa-button-primary">${escapeHtml(strings.openSite)}</a>`);
        }
        heroActions.push(`<a href="/mobile/profile" class="mpa-button mpa-button-secondary">${escapeHtml(strings.profile)}</a>`);
        document.getElementById('dash-hero-actions').innerHTML = heroActions.join('');

        const webLink = document.getElementById('dash-web-link');
        if (webLink) {
            const webUrl = tenant.url || '#';
            webLink.addEventListener('click', async (event) => {
                event.preventDefault();
                try {
                    const bridgeRes = await fetch((window.__AQARI_API_BASE || '') + '/api/mobile/auth/web-dashboard-link', {
                        method: 'POST',
                        headers: {
                            Accept: 'application/json',
                            Authorization: `Bearer ${token}`,
                            'X-Tenant-Slug': tenantSlug || '',
                        },
                    });
                    if (!bridgeRes.ok) {
                        throw new Error('Bridge request failed');
                    }
                    const bridgeData = await bridgeRes.json();
                    window.location.href = bridgeData.url || webUrl;
                } catch (_error) {
                    window.location.href = webUrl;
                }
            });
        }

        if (isResident) {
            document.getElementById('dash-resident').classList.remove('hidden');

            const resident = data.resident || {};
            const leases = Array.isArray(data.leases) ? data.leases : [];

            document.getElementById('dash-resident-stats').innerHTML = [
                metricCard(strings.leases, String(leases.length), 'M7.5 3.75h9A2.25 2.25 0 0 1 18.75 6v12A2.25 2.25 0 0 1 16.5 20.25h-9A2.25 2.25 0 0 1 5.25 18V6A2.25 2.25 0 0 1 7.5 3.75Z', tenant.name || strings.fallback, 'accent'),
                metricCard(strings.heroStatus, resident.email ? strings.active : strings.fallback, 'M9 12.75 11.25 15 15 9.75m6 2.25a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z', user.email || resident.phone || strings.fallback),
                metricCard(strings.marketplace, strings.marketplace, 'M21 21l-4.35-4.35m1.35-5.4a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z', strings.marketplaceText, 'soft'),
                metricCard(strings.profile, strings.profile, 'M15.75 6.75a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z', user.name || strings.fallback),
            ].join('');

            document.getElementById('dash-resident-info').innerHTML = [
                listRow('M15.75 6.75a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z', strings.name, resident.name || user.name || strings.fallback),
                listRow('M21.75 6.75v10.5A2.25 2.25 0 0 1 19.5 19.5h-15A2.25 2.25 0 0 1 2.25 17.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15A2.25 2.25 0 0 0 2.25 6.75m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75', strings.email, resident.email || user.email || strings.fallback),
                listRow('M3 5.25A2.25 2.25 0 0 1 5.25 3h2.386a1.5 1.5 0 0 1 1.455 1.136l.877 3.508a1.5 1.5 0 0 1-.813 1.728l-1.293.646a11.055 11.055 0 0 0 5.121 5.121l.646-1.293a1.5 1.5 0 0 1 1.728-.813l3.508.877A1.5 1.5 0 0 1 21 16.364v2.386A2.25 2.25 0 0 1 18.75 21h-.75C9.82 21 3 14.18 3 6V5.25Z', strings.phone, resident.phone || user.phone || strings.fallback, 'accent'),
                listRow('M19.5 21V6a2.25 2.25 0 0 0-2.25-2.25H6.75A2.25 2.25 0 0 0 4.5 6v15M19.5 21h1.125M19.5 21h-4.875M4.5 21H3.375M4.5 21h4.875M9 7.5h1.5m-1.5 3h1.5m4.5-3h1.5m-1.5 3h1.5m-6 10.5v-5.25A1.125 1.125 0 0 1 10.125 15h3.75A1.125 1.125 0 0 1 15 16.125V21', strings.tenant, tenant.name || strings.fallback, 'soft'),
            ].join('');

            document.getElementById('dash-leases').innerHTML = leases.map(lease => timelineItem(
                lease.unit?.title || lease.unit?.code || strings.fallback,
                lease.unit?.property_name || tenant.name || strings.fallback,
                { label: lease.status || strings.fallback, tone: lease.status === 'active' ? 'success' : 'soft' },
                `${lease.start_date || strings.fallback} · ${lease.end_date || strings.fallback}`
            )).join('');
            document.getElementById('dash-leases-empty').classList.toggle('hidden', leases.length > 0);

            const residentActions = [
                { href: '/mobile/profile', icon: 'M15.75 6.75a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z', kicker: strings.profile, title: strings.profile, text: strings.profileText },
                { href: '/mobile/marketplace', icon: 'M21 21l-4.35-4.35m1.35-5.4a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z', kicker: strings.marketplace, title: strings.marketplace, text: strings.marketplaceText, tone: 'soft' },
            ];
            if (tenant.url) {
                residentActions.push({ href: tenant.url, icon: 'M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0 0c1.657 0 3-4.03 3-9s-1.343-9-3-9-3 4.03-3 9 1.343 9 3 9Zm-9-9h18', kicker: strings.openSite, title: strings.openSite, text: strings.openSite, external: true, tone: 'accent' });
            }
            document.getElementById('dash-resident-actions').innerHTML = residentActions.map(actionCard).join('');
            return;
        }

        document.getElementById('dash-staff').classList.remove('hidden');

        const leadStats = data.dashboard?.leadStats || {};
        const maintenanceBreakdown = data.dashboard?.maintenanceBreakdown || {};
        const propertyOccupancy = Array.isArray(data.dashboard?.propertyOccupancy) ? data.dashboard.propertyOccupancy : [];
        const upcoming = Array.isArray(data.dashboard?.upcomingLeases) ? data.dashboard.upcomingLeases : [];

        document.getElementById('dash-metric-grid').innerHTML = [
            metricCard(strings.properties, String(metrics.properties ?? 0), 'M3.75 21V6a2.25 2.25 0 0 1 2.25-2.25h12A2.25 2.25 0 0 1 20.25 6v15M3.75 21h16.5', tenant.name || strings.fallback),
            metricCard(strings.units, String(metrics.units ?? 0), 'M3.75 6.75h16.5m-16.5 5.25h16.5m-16.5 5.25h16.5', `${metrics.occupied_units ?? 0} ${strings.occupiedWord}`, 'accent'),
            metricCard(strings.leases, String(metrics.active_leases ?? 0), 'M7.5 3.75h9A2.25 2.25 0 0 1 18.75 6v12A2.25 2.25 0 0 1 16.5 20.25h-9A2.25 2.25 0 0 1 5.25 18V6A2.25 2.25 0 0 1 7.5 3.75Z', `${formatNumber(metrics.occupancy_rate ?? 0)}%`, 'soft'),
            metricCard(strings.maintenance, String(metrics.open_maintenance ?? 0), 'M3.75 12h16.5m-16.5 4.5h9m-9-9h16.5', strings.openRequests),
            metricCard(strings.viewings, String(metrics.viewings_scheduled ?? 0), 'M8.25 3.75v2.25m7.5-2.25v2.25M3.75 8.25h16.5M5.25 6h13.5A1.5 1.5 0 0 1 20.25 7.5v10.5a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V7.5A1.5 1.5 0 0 1 5.25 6Z', strings.leadTitle, 'accent'),
            metricCard(strings.leads, String(leadStats.this_month ?? 0), 'M3.75 18.75 8.25 14.25l3 3L20.25 8.25', `${leadStats.change_pct ?? 0}%`, 'soft'),
        ].join('');

        document.getElementById('dash-ops-stack').innerHTML = [
            listRow('M3.75 12h16.5M12 3.75v16.5', strings.monthlyRent, formatCurrency(metrics.monthly_rent ?? 0, metrics.rent_currency || 'JOD')),
            listRow('M4.5 12.75 8.25 16.5l11.25-11.25', strings.occupancy, `${metrics.occupied_units ?? 0} / ${metrics.vacant_units ?? 0}`, 'accent'),
            listRow('M12 6v6l4.5 2.25', strings.averageAge, `${formatNumber(metrics.avg_open_days ?? 0)} ${lang === 'ar' ? 'يوم' : 'days'}`, 'soft'),
        ].join('');

        const totalMaint = Object.values(maintenanceBreakdown).reduce((sum, entry) => sum + Number(entry || 0), 0);
        const leadChange = Number(leadStats.change_pct ?? 0);
        document.getElementById('dash-lead-stack').innerHTML = [
            listRow('M3.75 18.75 8.25 14.25l3 3L20.25 8.25', strings.leads, `${leadStats.this_month ?? 0}`, leadChange >= 0 ? 'primary' : 'accent'),
            listRow('M8.25 3.75v2.25m7.5-2.25v2.25M3.75 8.25h16.5M5.25 6h13.5A1.5 1.5 0 0 1 20.25 7.5v10.5a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V7.5A1.5 1.5 0 0 1 5.25 6Z', strings.viewings, `${metrics.viewings_scheduled ?? 0}`, 'soft'),
            totalMaint > 0 ? Object.entries(maintenanceBreakdown).map(([status, count]) => progressRow(strings.maintenanceStatuses?.[status] || status.replace('_', ' '), count, totalMaint)).join('') : `<div class="mpa-note">${escapeHtml(strings.openRequests)}: 0</div>`,
        ].join('');

        document.getElementById('dash-property-mix').innerHTML = propertyOccupancy.map(property => progressRow(property.name || strings.fallback, property.rate ?? 0, 100)).join('');
        document.getElementById('dash-property-mix-empty').classList.toggle('hidden', propertyOccupancy.length > 0);

        document.getElementById('dash-upcoming').innerHTML = upcoming.map(lease => timelineItem(
            lease.unit?.title || lease.unit?.code || strings.fallback,
            lease.property?.name || tenant.name || strings.fallback,
            { label: strings.expires, tone: 'warn' },
            lease.end_date || strings.fallback
        )).join('');
        document.getElementById('dash-upcoming-empty').classList.toggle('hidden', upcoming.length > 0);

        const staffActions = [
            { href: '/mobile/units', icon: 'M3.75 6.75h16.5m-16.5 5.25h16.5m-16.5 5.25h16.5', kicker: strings.myUnits, title: strings.myUnits, text: strings.inventoryText },
            { href: '/mobile/units/create', icon: 'M12 4.5v15m7.5-7.5h-15', kicker: strings.addUnit, title: strings.addUnit, text: strings.newUnitText, tone: 'accent' },
            { href: '/mobile/profile', icon: 'M15.75 6.75a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z', kicker: strings.profile, title: strings.profile, text: strings.profileText, tone: 'soft' },
            { href: '/mobile/marketplace', icon: 'M21 21l-4.35-4.35m1.35-5.4a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z', kicker: strings.marketplace, title: strings.marketplace, text: strings.marketplaceText },
        ];
        document.getElementById('dash-staff-actions').innerHTML = staffActions.map(actionCard).join('');

        // Load expiring resident listings
        loadExpiringListings();
    } catch (error) {
        loading.classList.add('hidden');
        errorEl.classList.remove('hidden');
        document.getElementById('dash-error-title').textContent = strings.errorTitle;
        document.getElementById('dash-error-msg').textContent = error.message || strings.errorText;
    }

    async function loadExpiringListings() {
        try {
            const response = await fetch(`${window.__AQARI_API_BASE || ''}/api/mobile/my-listings/expiring-soon`, {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            
            if (!response.ok) return;
            
            const result = await response.json();
            const listings = result.data || [];
            
            if (listings.length === 0) return;
            
            const alertContainer = document.getElementById('expiring-listings-alert');
            alertContainer.classList.remove('hidden');
            alertContainer.className = 'mpa-card p-5 bg-orange-50 border-2 border-orange-200';
            
            alertContainer.innerHTML = `
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-orange-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-900 mb-2">${listings.length} ${listings.length === 1 ? 'Listing' : 'Listings'} Expiring Soon</h3>
                        <p class="text-sm text-gray-700 mb-3">The following listings will expire in 2 days. Renew them to keep them active.</p>
                        <div class="space-y-2">
                            ${listings.map(listing => `
                                <div class="bg-white rounded-lg p-3 flex justify-between items-center">
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-900">${listing.title?.en || listing.code}</div>
                                        <div class="text-xs text-gray-600">Expires in ${listing.days_until_expiration} days</div>
                                    </div>
                                    <a href="/mobile/my-listings/${listing.code}/edit" class="text-sm text-blue-600 font-medium">Renew</a>
                                </div>
                            `).join('')}
                        </div>
                        <a href="/mobile/my-listings" class="inline-block mt-3 text-sm text-orange-700 font-semibold">View All My Listings →</a>
                    </div>
                </div>
            `;
        } catch (error) {
            console.error('Failed to load expiring listings:', error);
        }
    }
})();
</script>
