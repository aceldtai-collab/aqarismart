@extends('mobile.layouts.app', ['title' => 'Dashboard', 'subtitle' => 'Staff and resident overview'])

@section('content')
    <div class="space-y-4">
        <div class="rounded-3xl bg-emerald-300/10 backdrop-blur-sm p-5 shadow-lg ring-1 ring-emerald-300/30">
            <div class="text-sm text-white/70">Authenticated mobile context</div>
            <pre id="mobile-dashboard-result" class="mt-3 overflow-auto rounded-2xl bg-gray-50 p-4 text-xs text-emerald-300"></pre>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(async () => {
    const token = localStorage.getItem('aqari_mobile_token');
    const tenantSlug = localStorage.getItem('aqari_mobile_tenant_slug');
    const el = document.getElementById('mobile-dashboard-result');
    if (!token) {
        el.textContent = 'Login first to load the mobile dashboard.';
        return;
    }
    const response = await fetch('/api/mobile/dashboard', {
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
