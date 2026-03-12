<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ __('Edit Viewing') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-md bg-white p-6 shadow">
                @php
                    $viewingId = $viewing->getKey();
                @endphp
                <form method="post" action="{{ route('property-viewings.update', ['tenant_slug' => request()->route('tenant_slug'), 'property_viewing' => $viewingId]) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <x-input-label for="lead_id" :value="__('Lead')" />
                        <select id="lead_id" name="lead_id" class="mt-1 block w-full rounded-md border-gray-300" required>
                            @foreach(($leads ?? collect()) as $id => $name)
                                <option value="{{ $id }}" @selected(old('lead_id', $viewing->lead_id) == $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('lead_id')" class="mt-2" />
                    </div>
                    <div class="mb-4">
                        <x-input-label for="property_id" :value="__('Property')" />
                        <select id="property_id" name="property_id" class="mt-1 block w-full rounded-md border-gray-300" required>
                            @foreach(($properties ?? collect()) as $id => $name)
                                <option value="{{ $id }}" @selected(old('property_id', $viewing->property_id) == $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('property_id')" class="mt-2" />
                    </div>
                    @if(($agents ?? collect())->isNotEmpty())
                        <div class="mb-4">
                            <x-input-label for="agent_id" :value="__('Agent')" />
                            <select id="agent_id" name="agent_id" class="mt-1 block w-full rounded-md border-gray-300" required>
                                @foreach($agents as $id => $name)
                                    <option value="{{ $id }}" @selected(old('agent_id', $viewing->agent_id) == $id)>{{ $name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('agent_id')" class="mt-2" />
                        </div>
                    @endif
                    <div class="mb-4 grid gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label for="appointment_at" :value="__('Appointment')" />
                            <x-text-input
                                id="appointment_at"
                                name="appointment_at"
                                type="datetime-local"
                                class="mt-1 block w-full"
                                value="{{ old('appointment_at', optional($viewing->appointment_at)->format('Y-m-d\\TH:i')) }}"
                                required
                            />
                            <x-input-error :messages="$errors->get('appointment_at')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300" required>
                                @foreach(\App\Models\PropertyViewing::statusLabels() as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status', $viewing->status ?? \App\Models\PropertyViewing::STATUS_SCHEDULED) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>
                    </div>
                    <div class="mb-6">
                        <x-input-label for="notes" :value="__('Notes')" />
                        <textarea id="notes" name="notes" rows="4" class="mt-1 block w-full rounded-md border-gray-300">{{ old('notes', $viewing->notes) }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('property-viewings.index') }}" class="rounded-md border px-3 py-2 text-gray-700">{{ __('Cancel') }}</a>
                        <button class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-white hover:bg-indigo-700" type="submit">{{ __('Update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
