<script>
    window._unitProps = @json($propMeta);
    window._unitCats = @json($catMeta);
    window._attrFields = @json($attributeFields);
    @isset($unitAttributes)
        window._unitAttributes = @json($unitAttributes);
    @endisset
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const categorySelect = document.getElementById('category_id');
        const propertySelect = document.getElementById('property_id');
        const subcatSelect = document.getElementById('subcategory_id');
        const attributesGrid = document.querySelector('#attributes .grid');
        const noFieldsMessage = document.querySelector('#attributes .text-center');
        const locale = '{{ app()->getLocale() }}';
        const initialSubcategoryId = @json(old('subcategory_id'));
        const initialCategoryId = @json(old('category_id'));
        const initialPropertyId = @json(old('property_id'));
        @isset($unit)
            const persistedSubcategoryId = @json($unit->subcategory_id);
        @else
            const persistedSubcategoryId = null;
        @endisset
        
        // Handle category selection to populate subcategories
        if (categorySelect) {
            categorySelect.addEventListener('change', function() {
                const categoryId = this.value;
                
                // Clear and enable/disable subcategory
                subcatSelect.innerHTML = '<option value="">{{ __('Select Subcategory') }}</option>';
                
                if (categoryId) {
                    subcatSelect.disabled = false;
                    const category = window._unitCats.find(c => c.id == categoryId);
                    if (category && category.subs) {
                        category.subs.forEach(sub => {
                            const option = document.createElement('option');
                            option.value = sub.id;
                            option.textContent = sub.name;
                            subcatSelect.appendChild(option);
                        });
                    }
                } else {
                    subcatSelect.disabled = true;
                }
                
                // Clear attributes
                renderFields([]);
            });
        }
        
        // Handle property selection to populate subcategories
        function populateSubcategoriesFromProperty() {
            if (!propertySelect || !subcatSelect) return;

            const categoryId = propertySelect.value ? propertySelect.options[propertySelect.selectedIndex]?.dataset.categoryId : 'all';
            const selectedSubcategoryId = initialSubcategoryId ?? persistedSubcategoryId;

            // Clear subcategory
            subcatSelect.innerHTML = '<option value="">{{ __('Select Subcategory') }}</option>';

            if (categoryId === 'all' || !categoryId) {
                // Show all subcategories for standalone option
                window._unitCats.forEach(category => {
                    if (category.subs) {
                        category.subs.forEach(sub => {
                            const option = document.createElement('option');
                            option.value = sub.id;
                            option.textContent = sub.name;
                            subcatSelect.appendChild(option);
                        });
                    }
                });
            } else if (categoryId) {
                const category = window._unitCats.find(c => c.id == categoryId);
                if (category && category.subs) {
                    category.subs.forEach(sub => {
                        const option = document.createElement('option');
                        option.value = sub.id;
                        option.textContent = sub.name;
                        subcatSelect.appendChild(option);
                    });
                }
            }

            if (selectedSubcategoryId) {
                subcatSelect.value = String(selectedSubcategoryId);
                renderFieldsForSubcategory(selectedSubcategoryId);
            } else {
                renderFields([]);
            }
        }

        if (propertySelect) {
            propertySelect.addEventListener('change', function() {
                const categoryId = this.options[this.selectedIndex]?.dataset.categoryId;
                
                // Clear subcategory
                subcatSelect.innerHTML = '<option value="">{{ __('Select Subcategory') }}</option>';
                
                if (categoryId === 'all') {
                    // Show all subcategories for standalone option
                    window._unitCats.forEach(category => {
                        if (category.subs) {
                            category.subs.forEach(sub => {
                                const option = document.createElement('option');
                                option.value = sub.id;
                                option.textContent = sub.name;
                                subcatSelect.appendChild(option);
                            });
                        }
                    });
                } else if (categoryId) {
                    const category = window._unitCats.find(c => c.id == categoryId);
                    if (category && category.subs) {
                        category.subs.forEach(sub => {
                            const option = document.createElement('option');
                            option.value = sub.id;
                            option.textContent = sub.name;
                            subcatSelect.appendChild(option);
                        });
                    }
                }
                
                // Clear attributes
                renderFields([]);
            });
        }
        
        // Handle subcategory selection to load attributes
        if (subcatSelect) {
            subcatSelect.addEventListener('change', function() {
                const subcategoryId = this.value;
                
                if (subcategoryId) {
                    renderFieldsForSubcategory(subcategoryId);
                } else {
                    renderFields([]);
                }
            });
        }

        function renderFieldsForSubcategory(subcategoryId) {
            const fields = Array.isArray(window._attrFields)
                ? window._attrFields.filter(f => f.subcategory_id == subcategoryId).sort((a, b) => (a.sort ?? 999) - (b.sort ?? 999))
                : [];

            renderFields(fields);
        }
        
        function renderFields(fields) {
            if (!attributesGrid || !noFieldsMessage) return;
            
            if (fields.length === 0) {
                attributesGrid.style.display = 'none';
                noFieldsMessage.style.display = 'block';
                return;
            }
            
            attributesGrid.style.display = 'grid';
            noFieldsMessage.style.display = 'none';
            attributesGrid.innerHTML = '';
            
            fields.forEach(field => {
                const div = document.createElement('div');
                const label = field.label_translations?.[locale] || field.label || field.key;
                const requiredAttr = field.required ? 'required' : '';
                
                // Get existing value if editing
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
                } else if (window._unitAttributes && typeof window._unitAttributes === 'object') {
                    // Handle case where _unitAttributes is an object (keyed by field_id)
                    const attr = window._unitAttributes[field.id];
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
                        // Handle both array and object formats
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
                } else if (field.type === 'multi_enum') {
                    let options = '';
                    const selectedValues = Array.isArray(existingValue) ? existingValue.map(String) : [];
                    if (field.options) {
                        if (Array.isArray(field.options)) {
                            field.options.forEach(option => {
                                const selected = selectedValues.includes(String(option)) ? 'selected' : '';
                                options += `<option value="${option}" ${selected}>${option}</option>`;
                            });
                        } else if (typeof field.options === 'object') {
                            Object.entries(field.options).forEach(([key, value]) => {
                                const selected = selectedValues.includes(String(key)) ? 'selected' : '';
                                options += `<option value="${key}" ${selected}>${value}</option>`;
                            });
                        }
                    }
                    div.innerHTML = `
                        <label class="block text-sm font-medium text-gray-700 mb-1">${label}</label>
                        <select multiple name="attributes[${field.id}][json_value][]" class="w-full border rounded py-2" ${requiredAttr}>
                            ${options}
                        </select>
                    `;
                } else if (field.type === 'int') {
                    div.innerHTML = `<input type="number" step="1" placeholder="${label}" name="attributes[${field.id}][int_value]" class="w-full border rounded py-2" value="${existingValue}" ${requiredAttr}>`;
                } else if (field.type === 'decimal') {
                    div.innerHTML = `<input type="number" step="0.01" placeholder="${label}" name="attributes[${field.id}][decimal_value]" class="w-full border rounded py-2" value="${existingValue}" ${requiredAttr}>`;
                } else if (field.type === 'date') {
                    div.innerHTML = `<input type="date" name="attributes[${field.id}][string_value]" class="w-full border rounded py-2" value="${existingValue}" ${requiredAttr}>`;
                } else if (field.type === 'json') {
                    div.innerHTML = `
                        <label class="block text-sm font-medium text-gray-700 mb-1">${label}</label>
                        <textarea name="attributes[${field.id}][json_value]" rows="3" class="w-full border rounded py-2" ${requiredAttr}>${existingValue || ''}</textarea>
                    `;
                } else {
                    div.innerHTML = `<input type="text" placeholder="${label}" name="attributes[${field.id}][string_value]" class="w-full border rounded py-2" value="${existingValue}" ${requiredAttr}>`;
                }
                
                attributesGrid.appendChild(div);
            });
        }

        if (categorySelect && initialCategoryId) {
            categorySelect.value = String(initialCategoryId);
            categorySelect.dispatchEvent(new Event('change'));
        }
        
        if (propertySelect && initialPropertyId) {
            propertySelect.value = String(initialPropertyId);
        }
        
        if (initialSubcategoryId) {
            setTimeout(() => {
                subcatSelect.value = String(initialSubcategoryId);
                renderFieldsForSubcategory(initialSubcategoryId);
            }, 100);
        }
        
        populateSubcategoriesFromProperty();
    });
</script>
