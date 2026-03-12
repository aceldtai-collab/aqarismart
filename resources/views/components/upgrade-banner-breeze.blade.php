@php
    $tm = app(\App\Services\Tenancy\TenantManager::class);
@endphp
@if($tm->tenant() && ! $tm->tenant()->canUse('files'))
    <div class="mb-4 rounded-md border border-amber-300 bg-amber-50 p-4 text-amber-800">
        <div class="flex items-center justify-between gap-3">
            <div>
                <span class="font-semibold">Upgrade available:</span>
                Unlock file uploads and more with Pro.
            </div>
            <a class="inline-flex items-center rounded-md bg-amber-500 px-3 py-1.5 text-white hover:bg-amber-600" href="{{ url('/billing') }}">View Plans</a>
        </div>
    </div>
@endif
