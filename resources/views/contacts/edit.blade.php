@php
    $tenantCtx = app(\App\Services\Tenancy\TenantManager::class)->tenant();
    $updateRoute = $tenantCtx ? route('contacts.update', $contact) : route('admin.contacts.update', $contact);
    $indexRoute = $tenantCtx ? route('contacts.index') : route('admin.contacts.index');
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ __('Edit Contact') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-md bg-white p-6 shadow">
                <form method="post" action="{{ $updateRoute }}">
                    @csrf @method('PUT')
                    @if(!$tenantCtx)
                    <div class="mb-4">
                        <x-input-label for="tenant_id" value="{{ __('Tenant') }}" />
                        <select id="tenant_id" name="tenant_id" class="mt-1 block w-full rounded-md border-gray-300" required>
                            @foreach(($tenants ?? []) as $id => $name)
                                <option value="{{ $id }}" @selected($contact->tenant_id == $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('tenant_id')" class="mt-2" />
                    </div>
                    @endif
                    <div class="mb-4">
                        <x-input-label for="name" value="{{ __('Name') }}" />
                        <x-text-input id="name" name="name" class="mt-1 block w-full" required :value="$contact->name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div class="mb-4">
                        <x-input-label for="agent_id" value="{{ __('Agent') }}" />
                        <select id="agent_id" name="agent_id" class="mt-1 block w-full rounded-md border-gray-300">
                            <option value="">{{ __('None') }}</option>
                            @foreach($agents as $id => $name)
                                <option value="{{ $id }}" @selected(old('agent_id', $contact->agent_id) == $id)>{{ $name }}</option>
                            @endforeach
                        </select>   
                        <x-input-error :messages="$errors->get('agent_id')" class="mt-2" />
                    </div>
                    <div class="mb-4">
                        <x-input-label for="email" value="{{ __('Email') }}" />
                        <x-text-input id="email" type="email" name="email" class="mt-1 block w-full" :value="$contact->email" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div class="mb-6">
                        <x-input-label for="phone" value="{{ __('Phone') }}" />
                        <x-text-input id="phone" name="phone" class="mt-1 block w-full" :value="$contact->phone" />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ $indexRoute }}" class="rounded-md border px-3 py-2 text-gray-700">{{ __('Cancel') }}</a>
                        <button class="inline-flex items-center rounded-md px-3 py-2 text-white {{ $tenantCtx ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-[#e8604c] hover:bg-[#d4503e]' }}" type="submit">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
