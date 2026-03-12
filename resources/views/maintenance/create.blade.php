<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ __('New Maintenance Request') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <x-flash-status />
            <x-form-errors />
            <div class="rounded-md bg-white p-6 shadow">
                <form method="post" action="{{ route('maintenance.store') }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <x-input-label for="property_id" :value="__('Property')" />
                            <select id="property_id" name="property_id" class="mt-1 block w-full rounded-md border-gray-300">
                                <option value="">-- {{ __('Standalone (no property)') }} --</option>
                                @foreach($properties as $p)
                                    <option value="{{ $p->id }}" @selected(old('property_id') == $p->id)>{{ $p->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('property_id')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="unit_id" :value="__('Unit (optional)')" />
                            <select id="unit_id" name="unit_id" class="mt-1 block w-full rounded-md border-gray-300">
                                <option value="">{{ __('—') }}</option>
                                @foreach($units as $u)
                                    <option value="{{ $u->id }}" @selected(old('unit_id') == $u->id)>{{ $u->property?->name ?? __('Standalone') }} — {{ $u->code }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('unit_id')" class="mt-2" />
                        </div>
                    </div>
                    <div>
                        <x-input-label for="resident_id" :value="__('Resident (optional)')" />
                        <select id="resident_id" name="resident_id" class="mt-1 block w-full rounded-md border-gray-300">
                            <option value="">{{ __('—') }}</option>
                            @foreach($residents as $r)
                                <option value="{{ $r->id }}" @selected(old('resident_id') == $r->id)>{{ $r->name }} ({{ $r->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="title" :value="__('Title')" />
                        <x-text-input id="title" name="title" class="mt-1 block w-full" value="{{ old('title') }}" required />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="details" :value="__('Details')" />
                        <textarea id="details" name="details" class="mt-1 block w-full rounded-md border-gray-300" rows="4">{{ old('details') }}</textarea>
                    </div>
                    <div>
                        <x-input-label for="priority" :value="__('Priority')" />
                        <select id="priority" name="priority" class="mt-1 block w-full rounded-md border-gray-300">
                            @foreach(['low','normal','high'] as $p)
                                <option value="{{ $p }}" @selected(old('priority','normal') === $p)>{{ ucfirst($p) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('maintenance.index') }}" class="rounded-md border px-3 py-2">{{ __('Cancel') }}</a>
                        <x-primary-button>{{ __('Save') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
