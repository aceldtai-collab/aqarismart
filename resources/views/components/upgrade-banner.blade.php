@php
    $tm = app(\App\Services\Tenancy\TenantManager::class);
@endphp
@if($tm->tenant() && ! $tm->tenant()->canUse('files'))
    <div class="panel" style="border-color:#fb923c">
        <strong>Upgrade available:</strong> Unlock file uploads and more with Pro.
        <a class="btn" style="margin-left:8px" href="{{ url('/billing') }}">View Plans</a>
    </div>
@endif
