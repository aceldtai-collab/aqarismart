@extends('mobile.layouts.app', ['title' => 'Tenants', 'subtitle' => 'Public directory'])

@section('content')
    <div class="space-y-4">
        <form id="mobile-tenant-search" class="rounded-3xl bg-emerald-300/10 backdrop-blur-sm p-4 shadow-lg ring-1 ring-emerald-300/30">
            <div class="flex gap-2">
                <input name="q" placeholder="Search tenants" class="flex-1 rounded-2xl border border-emerald-300/20 bg-emerald-600/50 px-4 py-3 text-white placeholder-emerald-300/60 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30">
                <button type="submit" class="rounded-2xl bg-white text-emerald-700 px-6 py-3 text-sm font-semibold shadow-lg transition hover:bg-white/90 hover:shadow-xl">
                    {{ app()->getLocale() === 'ar' ? 'بحث' : 'Search' }}
                </button>
            </div>
        </form>
        <div id="mobile-tenants-results" class="grid gap-4"></div>
    </div>
@endsection
+
@push('scripts')
<script>
const tenantSearch = document.getElementById('mobile-tenant-search');
const tenantResults = document.getElementById('mobile-tenants-results');
async function loadTenants() {
    const params = new URLSearchParams(new FormData(tenantSearch));
    const response = await fetch(`${window.__AQARI_API_BASE || ''}/api/mobile/tenants?${params.toString()}`, { headers: { Accept: 'application/json' } });
    const data = await response.json();
    tenantResults.innerHTML = (data.data || []).map(tenant => `
        <a href="/mobile/tenants/${tenant.slug}" class="rounded-3xl bg-emerald-300/10 backdrop-blur-sm p-5 shadow-lg ring-1 ring-emerald-300/30 transition hover:shadow-xl hover:ring-emerald-300/50">
            <div class="text-lg font-semibold text-slate-800">${tenant.name}</div>
            <div class="mt-2 text-sm text-white/70">${tenant.summary?.description ?? 'No description available.'}</div>
            <div class="mt-3 text-xs uppercase tracking-[0.2em] text-emerald-300">${tenant.slug}</div>
        </a>
    `).join('') || '<div class="rounded-3xl bg-emerald-300/10 backdrop-blur-sm p-6 text-sm text-white/70 shadow-lg ring-1 ring-emerald-300/30">No tenants found.</div>';
}
tenantSearch?.addEventListener('submit', (e) => {
    e.preventDefault();
    loadTenants();
});
tenantSearch?.addEventListener('input', loadTenants);
loadTenants();
</script>
@endpush
