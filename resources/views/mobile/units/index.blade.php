@extends('mobile.layouts.app', ['title' => 'Units', 'subtitle' => 'Tenant dashboard units'])

@section('content')
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <a href="{{ route('mobile.units.create') }}" class="rounded-2xl bg-white text-emerald-700 px-4 py-3 text-sm font-semibold shadow-lg transition hover:bg-white/90 hover:shadow-xl">Create unit</a>
        </div>
        <div class="rounded-3xl bg-emerald-300/10 backdrop-blur-sm p-5 shadow-lg ring-1 ring-emerald-300/30">
            <pre id="mobile-units-result" class="overflow-auto rounded-2xl bg-gray-50 p-4 text-xs text-emerald-300"></pre>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(async () => {
    const token = localStorage.getItem('aqari_mobile_token');
    const tenantSlug = localStorage.getItem('aqari_mobile_tenant_slug');
    const el = document.getElementById('mobile-units-result');
    if (!token) {
        el.textContent = 'Login first to load units.';
        return;
    }
    const response = await fetch((window.__AQARI_API_BASE || '') + '/api/mobile/units', {
        headers: {
            Accept: 'application/json',
            Authorization: `Bearer ${token}`,
            'X-Tenant-Slug': tenantSlug || '',
        },
    });
    const data = await response.json();
    el.textContent = JSON.stringify(data, null, 2);
})();
</script>
@endpush
