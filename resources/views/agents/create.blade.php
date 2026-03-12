@php
    $tenantCtx = app(\App\Services\Tenancy\TenantManager::class)->tenant();
    $storeAction = $tenantCtx ? route('agents.store') : route('admin.agents.store');
    $indexRoute = $tenantCtx ? route('agents.index') : route('admin.agents.index');
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ __('New Agent') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-md bg-white p-6 shadow">
                <form method="post" action="{{ $storeAction }}" enctype="multipart/form-data">
                    @csrf
                    @if(! $tenantCtx)
                    <div class="mb-4">
                        <x-input-label for="tenant_id" :value="__('Tenant')" />
                        <select id="tenant_id" name="tenant_id" class="mt-1 block w-full rounded-md border-gray-300" required>
                            <option value="">-- {{ __('Select') }} --</option>
                            @foreach(($tenants ?? []) as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('tenant_id')" class="mt-2" />
                    </div>
                    @endif
                    <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="name_en" :value="__('Name (EN)')" />
                            <x-text-input id="name_en" name="name[en]" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('name.en')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="name_ar" :value="__('Name (AR)')" />
                            <x-text-input id="name_ar" name="name[ar]" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('name.ar')" class="mt-2" />
                        </div>
                    </div>
                    <div class="mb-4">
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" type="email" name="email" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div class="mb-6">
                        <x-input-label for="phone" :value="__('Phone')" />
                        <x-text-input id="phone" name="phone" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>
                    <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="license_id" :value="__('License ID')" />
                            <x-text-input id="license_id" name="license_id" class="mt-1 block w-full" :value="old('license_id')" />
                            <x-input-error :messages="$errors->get('license_id')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="commission_rate" :value="__('Commission Rate (%)')" />
                            <x-text-input id="commission_rate" name="commission_rate" type="number" step="0.01" min="0" max="100" class="mt-1 block w-full" :value="old('commission_rate', '0.00')" />
                            <x-input-error :messages="$errors->get('commission_rate')" class="mt-2" />
                        </div>
                    </div>
                    <div class="mb-4">
                        <x-input-label for="photo" :value="__('Agent Logo')" />
                        <x-text-input id="photo" name="photo" type="file" accept="image/*" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('photo')" class="mt-2" />
                        <p class="mt-1 text-xs text-gray-500">{{ __('PNG, JPG, or SVG up to 2MB.') }}</p>
                    </div>
                    <div class="mb-6">
                        <x-input-label for="active" :value="__('Status')" />
                        <div class="flex items-center gap-2">
                            <input type="hidden" name="active" value="0">
                            <input id="active" type="checkbox" name="active" value="1" class="rounded border-gray-300 {{ $tenantCtx ? 'text-indigo-600 focus:ring-indigo-500' : 'text-[#e8604c] focus:ring-[#e8604c]' }}" @checked(old('active', true)) />
                            <span class="text-sm text-gray-600">{{ __('Active agents can be assigned to assets and contacts.') }}</span>
                        </div>
                        <x-input-error :messages="$errors->get('active')" class="mt-2" />
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ $indexRoute }}" class="rounded-md border px-3 py-2 text-gray-700">{{ __('Cancel') }}</a>
                        <button class="inline-flex items-center rounded-md px-3 py-2 text-white {{ $tenantCtx ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-[#e8604c] hover:bg-[#d4503e]' }}" type="submit">{{ __('Create') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
