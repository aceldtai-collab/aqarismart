@php
    $tenantCtx = app(\App\Services\Tenancy\TenantManager::class)->tenant();
    $storeRoute = $tenantCtx ? route('contacts.import.store') : route('admin.contacts.import.store');
    $indexRoute = $tenantCtx ? route('contacts.index') : route('admin.contacts.index');
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Import Contacts</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-md bg-white p-6 shadow">
                <p class="mb-3 text-sm text-gray-600">Upload a CSV with headers: <code>name,email,phone,agent</code> (legacy <code>company</code> is also accepted).</p>
                <form method="post" action="{{ $storeRoute }}" enctype="multipart/form-data">
                    @csrf
                    @if(!$tenantCtx)
                    <div class="mb-4">
                        <x-input-label for="tenant_id" value="{{ __('Tenant') }}" />
                        <select id="tenant_id" name="tenant_id" class="mt-1 block w-full rounded-md border-gray-300" required>
                            @foreach(\App\Models\Tenant::orderBy('name')->pluck('name','id') as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('tenant_id')" class="mt-2" />
                    </div>
                    @endif
                    <div class="mb-4">
                        <x-input-label for="file" value="CSV File" />
                        <input id="file" name="file" type="file" accept=".csv,text/csv" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('file')" class="mt-2" />
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ $indexRoute }}" class="rounded-md border px-3 py-2 text-gray-700">Cancel</a>
                        <button class="inline-flex items-center rounded-md px-3 py-2 text-white {{ $tenantCtx ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-[#e8604c] hover:bg-[#d4503e]' }}" type="submit">{{ __('Import') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
