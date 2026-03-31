<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">{{ __('Edit Property') }}</h2>
            <p class="mt-1 text-xs text-slate-500">{{ __('Update details, assignments, and media for this property.') }}</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto space-y-4 sm:px-6 lg:px-8">
            <x-flash-status />
            <x-form-errors />
            <div class="rounded-md bg-white p-6 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Property Details') }}</div>
                <form method="post" action="{{ route('properties.update', $property) }}" class="mt-4 space-y-6">
                    @csrf @method('PUT')
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="name_en" value="Name (EN)" />
                            <x-text-input id="name_en" name="name[en]" class="mt-1 block w-full" value="{{ old('name.en', data_get($property,'name_translations.en', $property->getRawOriginal('name'))) }}" required />
                            <x-input-error :messages="$errors->get('name.en')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="name_ar" value="Name (AR)" />
                            <x-text-input id="name_ar" name="name[ar]" class="mt-1 block w-full" value="{{ old('name.ar', data_get($property,'name_translations.ar')) }}" />
                            <x-input-error :messages="$errors->get('name.ar')" class="mt-2" />
                        </div>
                    </div>
                    @if(($agents ?? collect())->isNotEmpty())
                    <div>
                        <x-input-label for="agent_ids" value="{{ __('Assign Agents') }}" />
                        @php
                            $selectedAgents = collect(old('agent_ids', $property->agents->pluck('id')->all() ?: ($property->agent_id ? [$property->agent_id] : [])))->map(fn($id) => (int) $id);
                        @endphp
                        <select id="agent_ids" name="agent_ids[]" multiple class="mt-1 block w-full rounded-md border-slate-300 text-sm">
                            @foreach($agents as $id => $name)
                                <option value="{{ $id }}" @selected($selectedAgents->contains((int) $id))>{{ $name }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-slate-500">{{ __('First selected agent becomes the primary assignment.') }}</p>
                        <x-input-error :messages="$errors->get('agent_ids')" class="mt-2" />
                    </div>
                    @endif
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="description_en" value="Description (EN)" />
                            <textarea id="description_en" name="description[en]" rows="3" class="mt-1 block w-full rounded-md border-slate-300">{{ old('description.en', data_get($property,'description_translations.en', $property->getRawOriginal('description'))) }}</textarea>
                            <x-input-error :messages="$errors->get('description.en')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="description_ar" value="Description (AR)" />
                            <textarea id="description_ar" name="description[ar]" rows="3" class="mt-1 block w-full rounded-md border-slate-300">{{ old('description.ar', data_get($property,'description_translations.ar')) }}</textarea>
                            <x-input-error :messages="$errors->get('description.ar')" class="mt-2" />
                        </div>
                    </div>
                    <div>
                        <x-input-label for="category_id" value="Category" />
                        <select id="category_id" name="category_id" class="mt-1 block w-full rounded-md border-slate-300 text-sm">
                            <option value="">-- Select --</option>
                            @foreach(($categories ?? []) as $c)
                                <option value="{{ $c->id }}" @selected(old('category_id', $property->category_id) == $c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="address" value="Address" />
                        <x-text-input id="address" name="address" class="mt-1 block w-full" value="{{ old('address', $property->address) }}" />
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <x-input-label for="postal" value="Postal" />
                            <x-text-input id="postal" name="postal" class="mt-1 block w-full" value="{{ old('postal', $property->postal) }}" />
                            <x-input-error :messages="$errors->get('postal')" class="mt-2" />
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('properties.index') }}" class="rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-600 hover:bg-slate-50">{{ __('Cancel') }}</a>
                        <x-primary-button class="bg-gray-50 hover:bg-slate-800">{{ __('Save') }}</x-primary-button>
                    </div>
                </form>
            </div>
            <div class="rounded-md bg-white p-6 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Photos') }}</div>
                @if(is_array($property->photos) && count($property->photos))
                    <div class="mt-4 grid grid-cols-2 gap-3 md:grid-cols-4">
                        @foreach($property->photos as $photo)
                            @php
                                $path = is_string($photo) ? $photo : ($photo['path'] ?? null);
                            @endphp
                            @if($path)
                                @php
                                    $norm = (!filter_var($path, FILTER_VALIDATE_URL) && !\Illuminate\Support\Str::startsWith($path, 'storage/')) ? 'storage/'.$path : $path;
                                    $src = filter_var($norm, FILTER_VALIDATE_URL) ? $norm : asset($norm);
                                @endphp
                                <img src="{{ $src }}" class="h-28 w-full rounded object-cover" />
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="mt-4 rounded-md bg-slate-50 p-4">
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-full bg-white text-slate-500 shadow-sm ring-1 ring-slate-200/60">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 6.75h15M7.5 9.75h9M7.5 12.75h9M4.5 15.75h15" />
                                </svg>
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-slate-700">{{ __('No photos uploaded yet.') }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ __('Add a few images to help teams recognize the property.') }}</p>
                            </div>
                        </div>
                    </div>
                @endif
                <form method="post" action="{{ route('properties.update', $property) }}" enctype="multipart/form-data" class="mt-4">
                    @csrf @method('PUT')
                    <input id="photos" name="photos[]" type="file" accept="image/*" multiple class="mt-1 block w-full rounded-md border-slate-300 text-sm" />
                    <div class="mt-3">
                        <button type="submit" class="inline-flex items-center rounded-md bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">{{ __('Upload Photos') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
