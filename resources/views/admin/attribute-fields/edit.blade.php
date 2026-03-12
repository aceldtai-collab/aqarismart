<x-admin-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-900">{{ __('Edit Attribute Field') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4">
            <form id="attribute-field-form" method="POST" action="{{ route('admin.attribute-fields.update', ['attributeField' => $attributeField] + request()->only('lang')) }}" class="bg-white rounded-lg shadow p-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Subcategory') }}</label>
                        <select name="subcategory_id" class="mt-1 w-full border rounded py-2" required>
                            @foreach($subcategories as $subcategory)
                                <option value="{{ $subcategory->id }}" @selected(old('subcategory_id', $attributeField->subcategory_id) == $subcategory->id)>
                                    {{ $subcategory->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('subcategory_id')" class="mt-2" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Key') }}</label>
                        <input name="key" class="mt-1 w-full border rounded py-2" value="{{ old('key', $attributeField->key) }}" required>
                        <x-input-error :messages="$errors->get('key')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Label (English)') }}</label>
                        <input name="label_en" class="mt-1 w-full border rounded py-2" value="{{ old('label_en', $attributeField->label_translations['en'] ?? $attributeField->label) }}" required>
                        <x-input-error :messages="$errors->get('label_en')" class="mt-2" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Label (Arabic)') }}</label>
                        <input name="label_ar" class="mt-1 w-full border rounded py-2" value="{{ old('label_ar', $attributeField->label_translations['ar'] ?? '') }}" dir="rtl">
                        <x-input-error :messages="$errors->get('label_ar')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Type') }}</label>
                        <select name="type" class="mt-1 w-full border rounded py-2" required onchange="toggleOptions(this.value)">
                            @foreach($types as $type)
                                <option value="{{ $type }}" @selected(old('type', $attributeField->type) == $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Group') }}</label>
                        <select name="group" class="mt-1 w-full border rounded py-2" required>
                            @foreach($groups as $group)
                                <option value="{{ $group }}" @selected(old('group', $attributeField->group) == $group)>{{ __($group) }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('group')" class="mt-2" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Sort Order') }}</label>
                        <input type="number" name="sort" class="mt-1 w-full border rounded py-2" value="{{ old('sort', $attributeField->sort) }}" required>
                        <x-input-error :messages="$errors->get('sort')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Min Value') }}</label>
                        <input type="number" step="0.01" name="min" class="mt-1 w-full border rounded py-2" value="{{ old('min', $attributeField->min) }}">
                        <x-input-error :messages="$errors->get('min')" class="mt-2" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Max Value') }}</label>
                        <input type="number" step="0.01" name="max" class="mt-1 w-full border rounded py-2" value="{{ old('max', $attributeField->max) }}">
                        <x-input-error :messages="$errors->get('max')" class="mt-2" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Unit') }}</label>
                        <input name="unit" class="mt-1 w-full border rounded py-2" value="{{ old('unit', $attributeField->unit) }}">
                        <x-input-error :messages="$errors->get('unit')" class="mt-2" />
                    </div>
                </div>

                <div id="options-section" class="{{ in_array($attributeField->type, ['enum', 'multi_enum'], true) ? '' : 'hidden' }}">
                    <label class="block text-sm font-medium text-gray-700">{{ __('Options') }}</label>
                    <div id="options-container" class="mt-2 space-y-2">
                        @if($attributeField->options)
                            @foreach($attributeField->options as $key => $value)
                                <div class="flex gap-2">
                                    <input type="text" placeholder="Key" class="flex-1 border rounded py-2 option-key" value="{{ $key }}">
                                    <input type="text" placeholder="Value" class="flex-1 border rounded py-2 option-value" value="{{ $value }}">
                                    <button type="button" onclick="removeOption(this)" class="px-3 py-2 bg-[#e8604c] text-white rounded">-</button>
                                </div>
                            @endforeach
                        @else
                            <div class="flex gap-2">
                                <input type="text" placeholder="Key" class="flex-1 border rounded py-2 option-key">
                                <input type="text" placeholder="Value" class="flex-1 border rounded py-2 option-value">
                                <button type="button" onclick="removeOption(this)" class="px-3 py-2 bg-[#e8604c] text-white rounded">-</button>
                            </div>
                        @endif
                    </div>
                    <button type="button" onclick="addOption()" class="mt-2 px-3 py-2 bg-[#2bc155] text-white rounded">{{ __('Add Option') }}</button>
                </div>

                <div class="flex items-center space-x-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="required" class="rounded" @checked(old('required', $attributeField->required))>
                        <span class="ml-2 text-sm text-gray-700">{{ __('Required') }}</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="searchable" class="rounded" @checked(old('searchable', $attributeField->searchable))>
                        <span class="ml-2 text-sm text-gray-700">{{ __('Searchable') }}</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="facetable" class="rounded" @checked(old('facetable', $attributeField->facetable))>
                        <span class="ml-2 text-sm text-gray-700">{{ __('Facetable') }}</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="promoted" class="rounded" @checked(old('promoted', $attributeField->promoted))>
                        <span class="ml-2 text-sm text-gray-700">{{ __('Promoted') }}</span>
                    </label>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <a href="{{ route('admin.attribute-fields.index', request()->only('lang')) }}" class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-50">{{ __('Cancel') }}</a>
                    <button type="submit" class="px-4 py-2 bg-[#e8604c] text-white rounded hover:bg-[#d4503e]">{{ __('Update Field') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>

<script>
function toggleOptions(type) {
    const optionsSection = document.getElementById('options-section');
    if (type === 'enum' || type === 'multi_enum') {
        optionsSection.classList.remove('hidden');
    } else {
        optionsSection.classList.add('hidden');
    }
}

function addOption() {
    const container = document.getElementById('options-container');
    const div = document.createElement('div');
    div.className = 'flex gap-2';
    div.innerHTML = `
        <input type="text" placeholder="Key" class="flex-1 border rounded py-2 option-key">
        <input type="text" placeholder="Value" class="flex-1 border rounded py-2 option-value">
        <button type="button" onclick="removeOption(this)" class="px-3 py-2 bg-[#e8604c] text-white rounded">-</button>
    `;
    container.appendChild(div);
}

function removeOption(button) {
    button.parentElement.remove();
}

document.getElementById('attribute-field-form')?.addEventListener('submit', function() {
    const options = {};
    document.querySelectorAll('#options-container > div').forEach(div => {
        const key = div.querySelector('.option-key')?.value;
        const value = div.querySelector('.option-value')?.value;
        if (key && value) {
            options[key] = value;
        }
    });

    Object.entries(options).forEach(([key, value]) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = `options[${key}]`;
        input.value = value;
        this.appendChild(input);
    });
});
</script>
