<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-900">{{ __('Edit Unit') }}</h2>
    </x-slot>

    @include('units._map-assets')
    @include('units._form-scripts')
    @include('units._map-script')
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6">
            <x-flash-status />
            <x-form-errors />
            
            <form method="POST" action="{{ route('units.update', $unit) }}" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-4 sm:p-6 space-y-6">
                @csrf
                @method('PUT')
                
                <!-- Property Info -->
                <div class="border-b pb-4">
                    <h3 class="font-medium text-gray-900 mb-4">{{ __('Official Property Info') }}</h3>
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <input name="official[directorate]" placeholder="{{ __('Directorate') }}" class="border rounded px-3 py-2" value="{{ old('official.directorate', $unit->officialInfo?->directorate ?? '') }}">
                            <input name="official[village]" placeholder="{{ __('Village') }}" class="border rounded px-3 py-2" value="{{ old('official.village', $unit->officialInfo?->village ?? '') }}">
                            <input name="official[basin_number]" placeholder="{{ __('Basin Number') }}" class="border rounded px-3 py-2" value="{{ old('official.basin_number', $unit->officialInfo?->basin_number ?? '') }}">
                            <input name="official[basin_name]" placeholder="{{ __('Basin Name') }}" class="border rounded px-3 py-2" value="{{ old('official.basin_name', $unit->officialInfo?->basin_name ?? '') }}">
                            <input name="official[plot_number]" placeholder="{{ __('Plot Number') }}" class="border rounded px-3 py-2" value="{{ old('official.plot_number', $unit->officialInfo?->plot_number ?? '') }}">
                            <input name="official[apartment_number]" placeholder="{{ __('Apartment Number') }}" class="border rounded px-3 py-2" value="{{ old('official.apartment_number', $unit->officialInfo?->apartment_number ?? '') }}">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <input type="number" step="0.01" name="official[areas][land_sqm]" placeholder="{{ __('Land (sqm)') }}" class="border rounded px-3 py-2" value="{{ old('official.areas.land_sqm', ($unit->officialInfo?->areas['land_sqm'] ?? '')) }}">
                            <input type="number" step="0.01" name="official[areas][built_sqm]" placeholder="{{ __('Built (sqm)') }}" class="border rounded px-3 py-2" value="{{ old('official.areas.built_sqm', ($unit->officialInfo?->areas['built_sqm'] ?? '')) }}">
                            <input type="number" step="0.01" name="official[areas][total_sqm]" placeholder="{{ __('Total (sqm)') }}" class="border rounded px-3 py-2" value="{{ old('official.areas.total_sqm', ($unit->officialInfo?->areas['total_sqm'] ?? '')) }}">
                        </div>
                        <textarea name="official[areas][notes]" placeholder="{{ __('Notes') }}" rows="2" class="w-full border rounded px-3 py-2">{{ old('official.areas.notes', ($unit->officialInfo?->areas['notes'] ?? '')) }}</textarea>
                    </div>
                </div>

                <!-- Owner Info -->
                <div class="border-b pb-4">
                    <h3 class="font-medium text-gray-900 mb-4">{{ __('Property Owner') }}</h3>
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <input name="owner[name]" placeholder="{{ __('Owner Name') }}" class="border rounded py-2 px-3" value="{{ old('owner.name', $unit->owner?->name ?? '') }}">
                            <input name="owner[phone]" placeholder="{{ __('Phone') }}" class="border rounded py-2 px-3" value="{{ old('owner.phone', $unit->owner?->phone ?? '') }}">
                        </div>
                        <input name="owner[email]" type="email" placeholder="{{ __('Email') }}" class="w-full border rounded py-2 px-3" value="{{ old('owner.email', $unit->owner?->email ?? '') }}">
                        @if (($agents ?? collect())->isNotEmpty())
                            <select name="agent_ids[]" id="agent-select" class="border rounded py-2 px-3 w-full">
                                <option value="">{{ __('Select Agent') }}</option>
                                @php
                                    $selectedAgents = collect(old('agent_ids', $unit->agents->pluck('id')->all() ?: ($unit->agent_id ? [$unit->agent_id] : [])))->map(fn($id) => (int) $id);
                                @endphp
                                @foreach ($agents as $id => $name)
                                    <option value="{{ $id }}" @selected($selectedAgents->contains((int) $id))>{{ $name }}</option>
                                @endforeach
                            </select>
                        @endif
                        <textarea name="owner[notes]" placeholder="{{ __('Notes') }}" rows="2" class="w-full border rounded py-2 px-3">{{ old('owner.notes', $unit->owner?->notes ?? '') }}</textarea>
                    </div>
                </div>

                <!-- Basic Info -->
                <div class="border-b pb-4">
                    <h3 class="font-medium text-gray-900 mb-4">{{ __('Basic Information') }}</h3>
                    <div class="space-y-4">
                        <input type="hidden" name="property_id" id="property_id" value="" data-category-id="all">
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <select name="category_id" id="category-select" class="border rounded py-2 px-3">
                                <option value="">{{ __('Select Category') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('category_id', $unit->subcategory->category_id ?? '') == $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            
                            <select name="subcategory_id" id="subcategory_id" class="border rounded py-2 px-3" disabled>
                                <option value="">{{ __('Select Subcategory') }}</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <input name="title[en]" placeholder="{{ __('Unit Name (English)') }}" class="border rounded px-3 py-2" value="{{ old('title.en', $unit->title['en'] ?? '') }}">
                            <input name="title[ar]" placeholder="{{ __('اسم الوحدة (إجباري)') }}" class="border rounded px-3 py-2" dir="rtl" value="{{ old('title.ar', $unit->title['ar'] ?? '') }}" required>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <textarea name="description[en]" placeholder="{{ __('Description (English)') }}" rows="3" class="border rounded px-3 py-2">{{ old('description.en', $unit->description['en'] ?? '') }}</textarea>
                            <textarea name="description[ar]" placeholder="{{ __('الوصف (العربية)') }}" rows="3" class="border rounded px-3 py-2" dir="rtl">{{ old('description.ar', $unit->description['ar'] ?? '') }}</textarea>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <input type="number" step="0.01" name="price" placeholder="{{ __('Price') }}" class="border rounded px-3 py-2" value="{{ old('price', $unit->price) }}">
                            
                            <select name="currency" class="border rounded py-2 px-3">
                                <option value="JOD" @selected(old('currency', $unit->currency ?? 'JOD') == 'JOD')>{{ __('JOD') }}</option>
                                <option value="USD" @selected(old('currency', $unit->currency ?? 'JOD') == 'USD')>{{ __('USD') }}</option>
                            </select>
                            
                            <select name="listing_type" class="border rounded py-2 px-3">
                                @foreach (\App\Models\Unit::listingTypeLabels() as $value => $label)
                                    <option value="{{ $value }}" @selected(old('listing_type', $unit->listing_type ?? \App\Models\Unit::LISTING_RENT) == $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            
                            <select name="status" class="border rounded py-2 px-3">
                                @foreach (\App\Models\Unit::statusLabels() as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status', $unit->status ?? \App\Models\Unit::STATUS_SOLD) == $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <input name="location" placeholder="{{ __('Location') }}" class="w-full border rounded py-2 px-3" value="{{ old('location', $unit->location) }}">
                        <input name="location_url" placeholder="{{ __('Location URL (Google Maps, etc.)') }}" class="w-full border rounded py-2 px-3" value="{{ old('location_url', $unit->location_url) }}">
                    </div>
                </div>

                <!-- Dynamic Attributes -->
                <div class="border-b pb-4">
                    <h3 class="font-medium text-gray-900 mb-4">{{ __('Additional Attributes') }}</h3>
                    <div id="attributes">
                        <div class="grid grid-cols-2 gap-4" style="display: none;"></div>
                        <div class="text-center py-8 text-gray-500">
                            {{ __('Select a subcategory to see additional fields') }}
                        </div>
                    </div>
                </div>

                <!-- Media -->
                <div class="border-b pb-4">
                    <h3 class="font-medium text-gray-900 mb-4">{{ __('Photos') }}</h3>
                    <div>
                        @if (count($unit->photos) > 0)
                            <div class="mb-4">
                                <p class="mb-2 text-sm font-medium text-gray-700">{{ __('Current Photos') }}</p>
                                <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                                    @foreach ($unit->photos as $photo)
                                        <div class="relative group">
                                            <img src="{{ $photo }}" alt="Unit photo" 
                                                 class="h-32 w-full rounded-md object-cover cursor-pointer hover:opacity-75 transition"
                                                 onclick="window.open('{{ $photo }}', '_blank')"
                                                 onerror="this.onerror=null;this.src='https://placehold.co/300x200?text=Photo';" />
                                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition rounded-md pointer-events-none"></div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <div id="photo-preview" class="mb-4 grid grid-cols-2 gap-4 md:grid-cols-4" style="display: none;"></div>
                        <input type="file" name="photos[]" id="photo-input" multiple accept="image/*" class="w-full border rounded px-3 py-2">
                        <p class="mt-1 text-xs text-gray-500">{{ __('You can upload unlimited images. Existing photos will be kept. Click on any image to view it full size.') }}</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3 pt-4 border-t">
                    <a href="{{ route('units.index') }}" class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-50 text-center">{{ __('Cancel') }}</a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">{{ __('Update Unit') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

<script>
    // Initialize Select2 for searchable selects
    $(document).ready(function() {
        $('#agent-select').select2({
            placeholder: '{{ __('Select Agent') }}',
            allowClear: true,
            width: '100%'
        });
        
        $('#category-select').select2({
            placeholder: '{{ __('Select Category') }}',
            allowClear: true,
            width: '100%'
        });
        
        $('#subcategory_id').select2({
            placeholder: '{{ __('Select Subcategory') }}',
            allowClear: true,
            width: '100%'
        });
        
        // Handle category change for Select2
        $('#category-select').on('select2:select select2:clear', function() {
            const categoryId = $(this).val();
            const subcatSelect = $('#subcategory_id');
            
            // Clear and reset subcategory
            subcatSelect.empty().append('<option value="">{{ __('Select Subcategory') }}</option>');
            
            if (categoryId) {
                subcatSelect.prop('disabled', false);
                const category = window._unitCats.find(c => c.id == categoryId);
                if (category && category.subs) {
                    category.subs.forEach(sub => {
                        subcatSelect.append(new Option(sub.name, sub.id));
                    });
                }
            } else {
                subcatSelect.prop('disabled', true);
            }
            
            // Trigger Select2 update and clear attributes
            subcatSelect.trigger('change');
            renderFields([]);
        });
        
        // Handle subcategory change for Select2
        $('#subcategory_id').on('select2:select select2:clear', function() {
            const subcategoryId = $(this).val();
            
            if (subcategoryId) {
                renderFieldsForSubcategory(subcategoryId);
            } else {
                renderFields([]);
            }
        });
        
        // Load existing data and render attributes immediately
        const categorySelect = $('#category-select');
        const subcatSelect = $('#subcategory_id');
        const existingSubcategoryId = '{{ $unit->subcategory_id ?? '' }}';
        
        if (categorySelect.val()) {
            // Populate subcategories
            const categoryId = categorySelect.val();
            const category = window._unitCats.find(c => c.id == categoryId);
            if (category && category.subs) {
                subcatSelect.prop('disabled', false);
                category.subs.forEach(sub => {
                    subcatSelect.append(new Option(sub.name, sub.id, false, sub.id == existingSubcategoryId));
                });
                subcatSelect.trigger('change');
            }
            
            // Render attributes immediately if subcategory exists
            if (existingSubcategoryId) {
                renderFieldsForSubcategory(existingSubcategoryId);
            }
        }
    });
    
    // Helper functions for attributes
    function renderFieldsForSubcategory(subcategoryId) {
        const fields = Array.isArray(window._attrFields)
            ? window._attrFields.filter(f => f.subcategory_id == subcategoryId)
            : [];
        renderFields(fields);
    }
    
    function renderFields(fields) {
        const attributesGrid = document.querySelector('#attributes .grid');
        const noFieldsMessage = document.querySelector('#attributes .text-center');
        
        if (!attributesGrid || !noFieldsMessage) return;
        
        if (fields.length === 0) {
            attributesGrid.style.display = 'none';
            noFieldsMessage.style.display = 'block';
            return;
        }
        
        attributesGrid.style.display = 'grid';
        noFieldsMessage.style.display = 'none';
        attributesGrid.innerHTML = '';
        
        const locale = '{{ app()->getLocale() }}';
        
        fields.forEach(field => {
            const div = document.createElement('div');
            const label = field.label_translations?.[locale] || field.label || field.key;
            const requiredAttr = field.required ? 'required' : '';
            
            let existingValue = '';
            if (window._unitAttributes) {
                const attrsArray = Array.isArray(window._unitAttributes)
                    ? window._unitAttributes
                    : Object.values(window._unitAttributes);
                const attr = attrsArray.find(a => a.attribute_field_id == field.id);
                if (attr) {
                    if (attr.int_value !== null && attr.int_value !== undefined) existingValue = attr.int_value;
                    else if (attr.decimal_value !== null && attr.decimal_value !== undefined) existingValue = attr.decimal_value;
                    else if (attr.string_value !== null && attr.string_value !== undefined) existingValue = attr.string_value;
                    else if (attr.bool_value !== null && attr.bool_value !== undefined) existingValue = attr.bool_value ? 'checked' : '';
                    else if (attr.json_value !== null && attr.json_value !== undefined) {
                        existingValue = field.type === 'multi_enum' ? attr.json_value : JSON.stringify(attr.json_value, null, 2);
                    }
                }
            }
            
            if (field.type === 'bool') {
                div.innerHTML = `
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="attributes[${field.id}][bool_value]" class="rounded" ${existingValue} ${requiredAttr}>
                        <span>${label}</span>
                    </label>
                `;
            } else if (field.type === 'enum') {
                let options = '';
                if (field.options) {
                    if (Array.isArray(field.options)) {
                        field.options.forEach(option => {
                            const selected = existingValue === option ? 'selected' : '';
                            options += `<option value="${option}" ${selected}>${option}</option>`;
                        });
                    } else if (typeof field.options === 'object') {
                        Object.entries(field.options).forEach(([key, value]) => {
                            const selected = existingValue === key ? 'selected' : '';
                            options += `<option value="${key}" ${selected}>${value}</option>`;
                        });
                    }
                }
                div.innerHTML = `
                    <select name="attributes[${field.id}][string_value]" class="w-full border rounded py-2" ${requiredAttr}>
                        <option value="">{{ __('Select') }} ${label}</option>
                        ${options}
                    </select>
                `;
            } else if (field.type === 'int') {
                div.innerHTML = `<input type="number" step="1" placeholder="${label}" name="attributes[${field.id}][int_value]" class="w-full border rounded py-2" value="${existingValue}" ${requiredAttr}>`;
            } else if (field.type === 'decimal') {
                div.innerHTML = `<input type="number" step="0.01" placeholder="${label}" name="attributes[${field.id}][decimal_value]" class="w-full border rounded py-2" value="${existingValue}" ${requiredAttr}>`;
            } else {
                div.innerHTML = `<input type="text" placeholder="${label}" name="attributes[${field.id}][string_value]" class="w-full border rounded py-2" value="${existingValue}" ${requiredAttr}>`;
            }
            
            attributesGrid.appendChild(div);
        });
    }
    
    // Photo preview functionality for new uploads
    document.getElementById('photo-input').addEventListener('change', function(e) {
        const previewContainer = document.getElementById('photo-preview');
        const files = Array.from(e.target.files);
        
        if (files.length > 0) {
            previewContainer.style.display = 'grid';
            previewContainer.innerHTML = '';
            
            files.forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'relative group';
                        div.innerHTML = `
                            <img src="${e.target.result}" alt="New upload ${index + 1}" 
                                 class="h-32 w-full rounded-md object-cover cursor-pointer hover:opacity-75 transition"
                                 onclick="window.open(this.src, '_blank')" />
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition rounded-md pointer-events-none"></div>
                        `;
                        previewContainer.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                }
            });
        } else {
            previewContainer.style.display = 'none';
        }
    });
</script>