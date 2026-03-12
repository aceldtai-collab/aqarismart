<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ __('Edit Resident') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <x-flash-status />
            <x-form-errors />
            <div class="rounded-md bg-white p-6 shadow">
                <form method="post" action="{{ route('residents.update', $resident) }}" class="space-y-4">
                    @csrf @method('PUT')
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <x-input-label for="first_name" :value="__('First name')" />
                            <x-text-input id="first_name" name="first_name" class="mt-1 block w-full" value="{{ old('first_name', $resident->first_name) }}" required />
                            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="last_name" :value="__('Last name')" />
                            <x-text-input id="last_name" name="last_name" class="mt-1 block w-full" value="{{ old('last_name', $resident->last_name) }}" required />
                            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" value="{{ old('email', $resident->email) }}" />
                        </div>
                        <div>
                            <x-input-label for="phone" :value="__('Phone')" />
                            <x-text-input id="phone" name="phone" class="mt-1 block w-full" value="{{ old('phone', $resident->phone) }}" />
                        </div>
                    </div>
                    <div>
                        <x-input-label for="notes" :value="__('Notes')" />
                        <textarea id="notes" name="notes" class="mt-1 block w-full rounded-md border-gray-300" rows="3">{{ old('notes', $resident->notes) }}</textarea>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('residents.index') }}" class="rounded-md border px-3 py-2">{{ __('Cancel') }}</a>
                        <x-primary-button>{{ __('Save') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

