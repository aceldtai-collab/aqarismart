@php
    $unit = $unit ?? null;
    $tenantCtx = $tenant ?? app(\App\Services\Tenancy\TenantManager::class)->tenant();
    $primaryAgent = $primaryAgent
        ?? ($unit?->agents?->first()
            ?? $unit?->agent
            ?? optional($unit?->property)->agents?->first()
            ?? optional($unit?->property)->agent);

    $displayName = optional($primaryAgent)->name ?? $tenantCtx?->name ?? __('Leasing Advisor');
    $displayEmail = optional($primaryAgent)->email ?? $tenantCtx?->contact_email ?? null;
    $displayPhone = optional($primaryAgent)->phone ?? $tenantCtx?->contact_phone ?? null;
    $displayPhoto = optional($primaryAgent)->photo_url
        ?? 'https://ui-avatars.com/api/?name=' . urlencode($displayName) . '&background=4f46e5&color=fff&size=128';
@endphp

<div class="rounded-xl bg-white p-6 shadow space-y-4 text-center">
    <div class="flex justify-center">
        <img src="{{ $displayPhoto }}"
            alt="{{ $displayName }}"
            class="h-24 w-24 rounded-full border-4 border-indigo-100 shadow-md object-cover">
    </div>
    <h3 class="text-xl font-bold text-gray-900">
        {{ $displayName }}
    </h3>
    <p class="text-sm text-gray-500">
        @if(optional($primaryAgent)->license_id)
            {{ __('Licensed Agent · #:license', ['license' => $primaryAgent->license_id]) }}
        @else
            {{ __('Your RentoJo leasing partner') }}
        @endif
    </p>
    <div class="flex justify-center items-center space-x-1 text-yellow-400 text-sm rtl:space-x-reverse">
        <span>★</span><span>★</span><span>★</span><span>★</span><span class="text-gray-300">★</span>
        <span class="text-gray-500 ms-2 text-xs">{{ __('Verified') }}</span>
    </div>
    <div class="text-sm text-gray-700 space-y-1">
        <p><strong>{{ __('Phone') }}:</strong> {{ $displayPhone ?? __('Not provided') }}</p>
        <p><strong>{{ __('Email') }}:</strong> {{ $displayEmail ?? __('Not provided') }}</p>
        @if($tenantCtx?->name)
            <p><strong>{{ __('Office') }}:</strong> {{ $tenantCtx->name }}</p>
        @endif
    </div>
    <div class="pt-2">
        <button type="submit" form="unit-inquiry-form"
            class="inline-block rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 w-full">
            {{ __('Contact Agent') }}
        </button>
    </div>
</div>
