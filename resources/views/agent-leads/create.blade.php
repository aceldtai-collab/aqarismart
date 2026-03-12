<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ __('New Lead') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-md bg-white p-6 shadow">
                <form method="post" action="{{ route('agent-leads.store') }}">
                    @csrf
                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" name="name" class="mt-1 block w-full" value="{{ old('name') }}" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div class="mb-4 grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" value="{{ old('email') }}" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="phone" :value="__('Phone')" />
                            <x-text-input id="phone" name="phone" class="mt-1 block w-full" value="{{ old('phone') }}" />
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>
                    </div>
                    <div class="mb-4 grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="source" :value="__('Source')" />
                            <x-text-input id="source" name="source" class="mt-1 block w-full" value="{{ old('source') }}" />
                            <x-input-error :messages="$errors->get('source')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300">
                                @foreach(\App\Models\AgentLead::statusLabels() as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status', \App\Models\AgentLead::STATUS_NEW) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>
                    </div>
                    @if(($agents ?? collect())->isNotEmpty())
                        <div class="mb-4">
                            <x-input-label for="agent_id" :value="__('Agent')" />
                            <select id="agent_id" name="agent_id" class="mt-1 block w-full rounded-md border-gray-300">
                                @foreach($agents as $id => $name)
                                    <option value="{{ $id }}" @selected(old('agent_id', auth()->user()?->agent_id) == $id)>{{ $name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('agent_id')" class="mt-2" />
                        </div>
                    @endif
                    <div class="mb-4">
                        <x-input-label for="notes" :value="__('Notes')" />
                        <textarea id="notes" name="notes" rows="4" class="mt-1 block w-full rounded-md border-gray-300">{{ old('notes') }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('agent-leads.index') }}" class="rounded-md border px-3 py-2 text-gray-700">{{ __('Cancel') }}</a>
                        <button class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-white hover:bg-indigo-700" type="submit">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
