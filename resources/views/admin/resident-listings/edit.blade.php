<x-admin-layout>
    <x-slot name="header">{{ __('Edit Listing') }}: {{ $residentListing->code }}</x-slot>
    <x-slot name="headerActions">
        <a href="{{ route('admin.resident-listings.show', $residentListing) }}" class="gz-btn gz-btn-outline">{{ __('Back') }}</a>
    </x-slot>

    <div class="max-w-3xl">
        <div class="gz-widget">
            <div class="p-6">
                <form method="post" action="{{ route('admin.resident-listings.update', $residentListing) }}" class="space-y-6">
                    @csrf
                    @method('put')

                    {{-- Titles --}}
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-3">{{ __('Title') }}</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('EN') }}</label>
                                <input name="title[en]" value="{{ old('title.en', $residentListing->title['en'] ?? '') }}" class="gz-search w-full" />
                                @error('title.en')<div class="text-sm text-[#e8604c] mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('AR') }}</label>
                                <input name="title[ar]" value="{{ old('title.ar', $residentListing->title['ar'] ?? '') }}" class="gz-search w-full" dir="rtl" />
                                @error('title.ar')<div class="text-sm text-[#e8604c] mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- Descriptions --}}
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-3">{{ __('Description') }}</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('EN') }}</label>
                                <textarea name="description[en]" rows="5" class="gz-search w-full">{{ old('description.en', $residentListing->description['en'] ?? '') }}</textarea>
                                @error('description.en')<div class="text-sm text-[#e8604c] mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('AR') }}</label>
                                <textarea name="description[ar]" rows="5" class="gz-search w-full" dir="rtl">{{ old('description.ar', $residentListing->description['ar'] ?? '') }}</textarea>
                                @error('description.ar')<div class="text-sm text-[#e8604c] mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- Property Details --}}
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-3">{{ __('Property Details') }}</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Type') }}</label>
                                <select name="subcategory_id" class="gz-search w-full">
                                    <option value="">—</option>
                                    @foreach($subcategories as $sub)
                                        <option value="{{ $sub->id }}" {{ old('subcategory_id', $residentListing->subcategory_id) == $sub->id ? 'selected' : '' }}>{{ $sub->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Listing Type') }}</label>
                                <select name="listing_type" class="gz-search w-full">
                                    <option value="sale" {{ old('listing_type', $residentListing->listing_type) === 'sale' ? 'selected' : '' }}>{{ __('Sale') }}</option>
                                    <option value="rent" {{ old('listing_type', $residentListing->listing_type) === 'rent' ? 'selected' : '' }}>{{ __('Rent') }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('City') }}</label>
                                <select name="city_id" class="gz-search w-full">
                                    <option value="">—</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}" {{ old('city_id', $residentListing->city_id) == $city->id ? 'selected' : '' }}>{{ $city->name_en }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Bedrooms') }}</label>
                                <input type="number" name="bedrooms" min="0" value="{{ old('bedrooms', $residentListing->bedrooms) }}" class="gz-search w-full" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Bathrooms') }}</label>
                                <input type="number" name="bathrooms" min="0" step="0.5" value="{{ old('bathrooms', $residentListing->bathrooms) }}" class="gz-search w-full" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Area (m²)') }}</label>
                                <input type="number" name="area_m2" min="0" step="0.01" value="{{ old('area_m2', $residentListing->area_m2) }}" class="gz-search w-full" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Price') }}</label>
                                <input type="number" name="price" min="0" step="0.01" value="{{ old('price', $residentListing->price) }}" class="gz-search w-full" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Currency') }}</label>
                                <input name="currency" value="{{ old('currency', $residentListing->currency ?? 'IQD') }}" class="gz-search w-full" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Location') }}</label>
                                <input name="location" value="{{ old('location', $residentListing->location) }}" class="gz-search w-full" />
                            </div>
                        </div>
                    </div>

                    {{-- Status & Ad --}}
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-3">{{ __('Status & Ad') }}</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Status') }}</label>
                                <select name="status" class="gz-search w-full">
                                    @foreach(['active', 'pending', 'rejected', 'suspended'] as $s)
                                        <option value="{{ $s }}" {{ old('status', $residentListing->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Ad Status') }}</label>
                                <select name="ad_status" class="gz-search w-full">
                                    @foreach(['active', 'pending', 'expired'] as $s)
                                        <option value="{{ $s }}" {{ old('ad_status', $residentListing->ad_status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Ad Duration') }}</label>
                                <select name="ad_duration_id" class="gz-search w-full">
                                    <option value="">—</option>
                                    @foreach($adDurations as $d)
                                        <option value="{{ $d->id }}" {{ old('ad_duration_id', $residentListing->ad_duration_id) == $d->id ? 'selected' : '' }}>
                                            {{ $d->name_en }} ({{ $d->days }} {{ __('days') }} — {{ $d->formatted_price }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Moderation Notes --}}
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Moderation Notes') }}</label>
                        <textarea name="moderation_notes" rows="3" class="gz-search w-full" placeholder="{{ __('Notes visible to admins only...') }}">{{ old('moderation_notes', $residentListing->moderation_notes) }}</textarea>
                    </div>

                    <div class="pt-3 flex gap-2 border-t border-[#e8ecf3]">
                        <a href="{{ route('admin.resident-listings.show', $residentListing) }}" class="gz-btn gz-btn-outline">{{ __('Cancel') }}</a>
                        <button class="gz-btn gz-btn-primary">{{ __('Save Changes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
