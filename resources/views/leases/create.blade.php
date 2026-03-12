<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">{{ __('New Lease') }}</h2>
            <p class="mt-1 text-xs text-slate-500">{{ __('Create a lease to track occupancy and rent.') }}</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto space-y-4 sm:px-6 lg:px-8">
            <x-flash-status />
            <x-form-errors />
            <div class="rounded-md bg-white p-6 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Lease Details') }}</div>
                <form method="post" action="{{ route('leases.store') }}" class="mt-4 space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <x-input-label for="property_id" :value="__('Property')" />
                            <select id="property_id" name="property_id" class="mt-1 block w-full rounded-md border-slate-300 text-sm">
                                <option value="">-- {{ __('Standalone (no property)') }} --</option>
                                @foreach($properties as $p)
                                    <option value="{{ $p->id }}" @selected(old('property_id') == $p->id)>{{ $p->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('property_id')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="unit_id" :value="__('Unit')" />
                            <select id="unit_id" name="unit_id" class="mt-1 block w-full rounded-md border-slate-300 text-sm">
                                @foreach($units as $u)
                                    <option value="{{ $u->id }}" @selected(old('unit_id') == $u->id)>{{ $u->property?->name ?? __('Standalone') }} — {{ $u->code }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('unit_id')" class="mt-2" />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <x-input-label for="start_date" :value="__('Start date')" />
                            <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" value="{{ old('start_date') }}" required />
                            <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="end_date" :value="__('End date')" />
                            <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full" value="{{ old('end_date') }}" />
                        </div>
                        <div>
                            <x-input-label for="rent_cents" :value="__('Annual Rent (cents)')" />
                            <x-text-input id="rent_cents" name="rent_cents" type="number" class="mt-1 block w-full" value="{{ old('rent_cents') }}" required />
                            <x-input-error :messages="$errors->get('rent_cents')" class="mt-2" />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <x-input-label for="deposit_cents" :value="__('Deposit (cents)')" />
                            <x-text-input id="deposit_cents" name="deposit_cents" type="number" class="mt-1 block w-full" value="{{ old('deposit_cents', 0) }}" />
                        </div>
                        <div>
                            <x-input-label for="resident_ids" :value="__('Residents')" />
                            <select id="resident_ids" name="resident_ids[]" multiple class="mt-1 block w-full rounded-md border-slate-300 text-sm">
                                @foreach($residents as $r)
                                    <option value="{{ $r->id }}" @selected(collect(old('resident_ids', []))->contains($r->id))>{{ $r->name }} ({{ $r->email }})</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('resident_ids')" class="mt-2" />
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('leases.index') }}" class="rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-600 hover:bg-slate-50">{{ __('Cancel') }}</a>
                        <x-primary-button class="bg-slate-900 hover:bg-slate-800">{{ __('Save') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
