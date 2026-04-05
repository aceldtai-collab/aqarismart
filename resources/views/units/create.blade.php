<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-900">{{ __('Create Unit') }}</h2>
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
            
            <form method="POST" action="{{ route('units.store') }}" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-4 sm:p-6 space-y-6">
                @csrf
                
                <!-- Property Info -->
                <div class="border-b pb-4">
                    <h3 class="font-medium text-gray-900 mb-4">{{ __('Official Property Info') }}</h3>
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <input name="official[directorate]" placeholder="{{ __('Directorate') }}" class="border rounded py-2 px-3" value="{{ old('official.directorate') }}">
                            <input name="official[village]" placeholder="{{ __('Village') }}" class="border rounded py-2 px-3" value="{{ old('official.village') }}">
                            <input name="official[basin_number]" placeholder="{{ __('Basin Number') }}" class="border rounded py-2 px-3" value="{{ old('official.basin_number') }}">
                            <input name="official[basin_name]" placeholder="{{ __('Basin Name') }}" class="border rounded py-2 px-3" value="{{ old('official.basin_name') }}">
                            <input name="official[plot_number]" placeholder="{{ __('Plot Number') }}" class="border rounded py-2 px-3" value="{{ old('official.plot_number') }}">
                            <input name="official[apartment_number]" placeholder="{{ __('Apartment Number') }}" class="border rounded py-2 px-3" value="{{ old('official.apartment_number') }}">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <input type="number" step="0.01" name="official[areas][land_sqm]" placeholder="{{ __('Land (sqm)') }}" class="border rounded py-2 px-3" value="{{ old('official.areas.land_sqm') }}">
                            <input type="number" step="0.01" name="official[areas][built_sqm]" placeholder="{{ __('Built (sqm)') }}" class="border rounded py-2 px-3" value="{{ old('official.areas.built_sqm') }}">
                            <input type="number" step="0.01" name="official[areas][total_sqm]" placeholder="{{ __('Total (sqm)') }}" class="border rounded py-2 px-3" value="{{ old('official.areas.total_sqm') }}">
                        </div>
                        <textarea name="official[areas][notes]" placeholder="{{ __('Notes') }}" rows="2" class="w-full border rounded py-2 px-3">{{ old('official.areas.notes') }}</textarea>
                    </div>
                </div>

                <!-- Owner Info -->
                <div class="border-b pb-4">
                    <h3 class="font-medium text-gray-900 mb-4">{{ __('Property Owner') }}</h3>
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <input name="owner[name]" placeholder="{{ __('Owner Name') }}" class="border rounded py-2 px-3 w-full @error('owner.name') border-red-500 @enderror" value="{{ old('owner.name') }}">
                                @error('owner.name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <input name="owner[phone]" placeholder="{{ __('Phone') }}" class="border rounded py-2 px-3 w-full @error('owner.phone') border-red-500 @enderror" value="{{ old('owner.phone') }}">
                                @error('owner.phone')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <input name="owner[email]" type="email" placeholder="{{ __('Email') }}" class="border rounded py-2 px-3 w-full @error('owner.email') border-red-500 @enderror" value="{{ old('owner.email') }}">
                                @error('owner.email')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            @if (($agents ?? collect())->isNotEmpty())
                                <div>
                                    <select name="agent_ids[]" id="agent-select" class="border rounded py-2 px-3 w-full @error('agent_ids') border-red-500 @enderror">
                                        <option value="">{{ __('Select Agent') }}</option>
                                        @foreach ($agents as $id => $name)
                                            <option value="{{ $id }}" @selected(in_array($id, old('agent_ids', [])))>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('agent_ids')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif
                        </div>
                        <div>
                            <textarea name="owner[notes]" placeholder="{{ __('Notes') }}" rows="2" class="w-full border rounded py-2 px-3 @error('owner.notes') border-red-500 @enderror">{{ old('owner.notes') }}</textarea>
                            @error('owner.notes')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Basic Info -->
                <div class="border-b pb-4">
                    <h3 class="font-medium text-gray-900 mb-4">{{ __('Basic Information') }}</h3>
                    <div class="space-y-4">
                        <input type="hidden" name="property_id" id="property_id" value="" data-category-id="all">
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <select name="category_id" id="category-select" class="border rounded py-2 px-3 w-full @error('category_id') border-red-500 @enderror">
                                    <option value="">{{ __('Select Category') }}</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <select name="subcategory_id" id="subcategory_id" class="border rounded py-2 px-3 w-full @error('subcategory_id') border-red-500 @enderror" disabled>
                                    <option value="">{{ __('Select Subcategory') }}</option>
                                </select>
                                @error('subcategory_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <input name="title[en]" placeholder="{{ __('Unit Name (English)') }}" class="border rounded py-2 px-3 w-full @error('title.en') border-red-500 @enderror" value="{{ old('title.en', old('title')) }}">
                                @error('title.en')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <input name="title[ar]" placeholder="{{ __('اسم الوحدة (إجباري)') }}" class="border rounded py-2 px-3 w-full @error('title.ar') border-red-500 @enderror" dir="rtl" value="{{ old('title.ar') }}" required>
                                @error('title.ar')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                                @error('title')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <textarea name="description[en]" placeholder="{{ __('Description (English)') }}" rows="3" class="border rounded py-2 px-3">{{ old('description.en', old('description')) }}</textarea>
                            <textarea name="description[ar]" placeholder="{{ __('الوصف (العربية)') }}" rows="3" class="border rounded py-2 px-3" dir="rtl">{{ old('description.ar') }}</textarea>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div>
                                <input type="number" step="0.01" name="price" placeholder="{{ __('Price') }}" class="border rounded py-2 px-3 w-full @error('price') border-red-500 @enderror" value="{{ old('price') }}">
                                @error('price')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <select name="currency" class="border rounded py-2 px-3 w-full @error('currency') border-red-500 @enderror">
                                    <option value="IQD" @selected(old('currency', 'IQD') == 'IQD')>{{ __('IQD') }}</option>
                                    <option value="JOD" @selected(old('currency', 'IQD') == 'JOD')>{{ __('JOD') }}</option>
                                    <option value="USD" @selected(old('currency', 'IQD') == 'USD')>{{ __('USD') }}</option>
                                </select>
                                @error('currency')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <select name="listing_type" class="border rounded py-2 px-3 w-full @error('listing_type') border-red-500 @enderror">
                                    @foreach (\App\Models\Unit::listingTypeLabels() as $value => $label)
                                        <option value="{{ $value }}" @selected(old('listing_type', \App\Models\Unit::LISTING_RENT) == $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('listing_type')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <select name="status" class="border rounded py-2 px-3 w-full @error('status') border-red-500 @enderror">
                                    @foreach (\App\Models\Unit::statusLabels() as $value => $label)
                                        <option value="{{ $value }}" @selected(old('status', \App\Models\Unit::STATUS_SOLD) == $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <input name="location" placeholder="{{ __('Location') }}" class="w-full border rounded py-2 px-3 @error('location') border-red-500 @enderror" value="{{ old('location') }}">
                            @error('location')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <input name="location_url" placeholder="{{ __('Location URL (Google Maps, etc.)') }}" class="w-full border rounded py-2 px-3 @error('location_url') border-red-500 @enderror" value="{{ old('location_url') }}">
                            @error('location_url')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
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
                        <div id="photo-preview" class="mb-4 grid grid-cols-2 gap-4 md:grid-cols-4" style="display: none;"></div>
                        <input type="file" name="photos[]" id="photo-input" multiple accept="image/*" class="w-full border rounded px-3 py-2">
                        <p class="mt-1 text-xs text-gray-500">{{ __('You can upload unlimited images. Click on any image to view it full size.') }}</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3 pt-4 border-t">
                    <a href="{{ route('units.index') }}" class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-50 text-center">{{ __('Cancel') }}</a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">{{ __('Create Unit') }}</button>
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
            if (window._unitAttributes && Array.isArray(window._unitAttributes)) {
                const attr = window._unitAttributes.find(a => a.attribute_field_id == field.id);
                if (attr) {
                    if (attr.int_value !== null) existingValue = attr.int_value;
                    else if (attr.decimal_value !== null) existingValue = attr.decimal_value;
                    else if (attr.string_value !== null) existingValue = attr.string_value;
                    else if (attr.bool_value !== null) existingValue = attr.bool_value ? 'checked' : '';
                    else if (attr.json_value !== null) {
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
    
    // Photo preview functionality
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
                            <img src="${e.target.result}" alt="Preview ${index + 1}" 
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
