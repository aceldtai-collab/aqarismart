@php
    $labelTranslations = is_array($customAttribute->label_translations ?? null)
        ? $customAttribute->label_translations
        : [];
    $enumOptions = old('options');
    if (! is_array($enumOptions)) {
        $enumOptions = is_array($customAttribute->options ?? null)
            ? $customAttribute->options
            : [];
    }
    $isEnum = in_array(old('type', $customAttribute->type), ['enum', 'multi_enum'], true);
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-slate-900">{{ __('Edit Custom Attribute') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <form id="attribute-field-form" method="POST" action="{{ route('custom-attributes.update', ['customAttribute' => $customAttribute] + request()->only('lang')) }}" class="bg-white rounded-xl border border-slate-200/60 shadow-sm p-6 space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Unit Category') }} <span class="text-red-500">*</span></label>
                    <select name="subcategory_id" class="w-full border border-slate-300 rounded-lg py-2.5 px-3 text-sm focus:ring-2 focus:ring-slate-900 focus:border-slate-900" required>
                        <option value="">{{ __('Select Category') }}</option>
                        @foreach($subcategories as $subcategory)
                            <option value="{{ $subcategory->id }}" @selected(old('subcategory_id', $customAttribute->subcategory_id) == $subcategory->id)>
                                {{ $subcategory->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-slate-400">{{ __('Which type of unit will this attribute apply to?') }}</p>
                    <x-input-error :messages="$errors->get('subcategory_id')" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Name (English)') }} <span class="text-red-500">*</span></label>
                        <input name="label_en" class="w-full border border-slate-300 rounded-lg py-2.5 px-3 text-sm focus:ring-2 focus:ring-slate-900 focus:border-slate-900" value="{{ old('label_en', data_get($labelTranslations, 'en', $customAttribute->label)) }}" required>
                        <x-input-error :messages="$errors->get('label_en')" class="mt-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Name (Arabic)') }}</label>
                        <input name="label_ar" class="w-full border border-slate-300 rounded-lg py-2.5 px-3 text-sm focus:ring-2 focus:ring-slate-900 focus:border-slate-900" value="{{ old('label_ar', data_get($labelTranslations, 'ar', '')) }}" dir="rtl">
                        <x-input-error :messages="$errors->get('label_ar')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Field Type') }} <span class="text-red-500">*</span></label>
                        <select name="type" id="type-select" class="w-full border border-slate-300 rounded-lg py-2.5 px-3 text-sm focus:ring-2 focus:ring-slate-900 focus:border-slate-900" required onchange="toggleOptions(this.value)">
                            <option value="">{{ __('Select Type') }}</option>
                            <option value="bool" @selected(old('type', $customAttribute->type) == 'bool')>{{ __('Yes / No') }}</option>
                            <option value="int" @selected(old('type', $customAttribute->type) == 'int')>{{ __('Number') }}</option>
                            <option value="decimal" @selected(old('type', $customAttribute->type) == 'decimal')>{{ __('Decimal Number') }}</option>
                            <option value="string" @selected(old('type', $customAttribute->type) == 'string')>{{ __('Text') }}</option>
                            <option value="enum" @selected(old('type', $customAttribute->type) == 'enum')>{{ __('Dropdown List') }}</option>
                            <option value="multi_enum" @selected(old('type', $customAttribute->type) == 'multi_enum')>{{ __('Multiple Choice') }}</option>
                            <option value="date" @selected(old('type', $customAttribute->type) == 'date')>{{ __('Date') }}</option>
                            <option value="json" @selected(old('type', $customAttribute->type) == 'json')>{{ __('Flexible Data') }}</option>
                        </select>
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Group') }} <span class="text-red-500">*</span></label>
                        <select name="group" class="w-full border border-slate-300 rounded-lg py-2.5 px-3 text-sm focus:ring-2 focus:ring-slate-900 focus:border-slate-900" required>
                            @foreach($groups as $group)
                                <option value="{{ $group }}" @selected(old('group', $customAttribute->group) == $group)>{{ __($group) }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-slate-400">{{ __('How to group this attribute in the unit form.') }}</p>
                        <x-input-error :messages="$errors->get('group')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Unit of Measurement') }}</label>
                    <input name="unit" class="w-full border border-slate-300 rounded-lg py-2.5 px-3 text-sm focus:ring-2 focus:ring-slate-900 focus:border-slate-900" value="{{ old('unit', $customAttribute->unit) }}" placeholder="{{ __('e.g. m2, kg, years') }}">
                    <p class="mt-1 text-xs text-slate-400">{{ __('Optional. Shown next to the field value.') }}</p>
                    <x-input-error :messages="$errors->get('unit')" class="mt-2" />
                </div>

                <div id="options-section" class="{{ $isEnum ? '' : 'hidden' }}">
                    <label class="block text-sm font-medium text-slate-700 mb-2">{{ __('Dropdown Options') }} <span class="text-red-500">*</span></label>
                    <div id="options-container" class="space-y-2">
                        @if($isEnum && $enumOptions !== [])
                            @foreach($enumOptions as $optKey => $optVal)
                                <div class="flex gap-2">
                                    <input type="text" placeholder="{{ __('Option value') }}" class="flex-1 border border-slate-300 rounded-lg py-2 px-3 text-sm option-key" value="{{ $optKey }}">
                                    <input type="text" placeholder="{{ __('Display label') }}" class="flex-1 border border-slate-300 rounded-lg py-2 px-3 text-sm option-value" value="{{ $optVal }}">
                                    <button type="button" onclick="removeOption(this)" class="px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors text-sm font-medium">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            @endforeach
                        @else
                            <div class="flex gap-2">
                                <input type="text" placeholder="{{ __('Option value') }}" class="flex-1 border border-slate-300 rounded-lg py-2 px-3 text-sm option-key">
                                <input type="text" placeholder="{{ __('Display label') }}" class="flex-1 border border-slate-300 rounded-lg py-2 px-3 text-sm option-value">
                                <button type="button" onclick="removeOption(this)" class="px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors text-sm font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        @endif
                    </div>
                    <button type="button" onclick="addOption()" class="mt-3 inline-flex items-center gap-1 px-3 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 transition-colors text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        {{ __('Add Option') }}
                    </button>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                    <a href="{{ route('custom-attributes.index', request()->only('lang')) }}" class="px-4 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">{{ __('Cancel') }}</a>
                    <button type="submit" class="px-4 py-2.5 text-sm font-semibold bg-gray-50 rounded-lg hover:bg-slate-800 transition-colors shadow-sm">{{ __('Save Changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

<script>
function toggleOptions(type) {
    document.getElementById('options-section').classList.toggle('hidden', !['enum', 'multi_enum'].includes(type));
}

function addOption() {
    const container = document.getElementById('options-container');
    const div = document.createElement('div');
    div.className = 'flex gap-2';
    div.innerHTML = `
        <input type="text" placeholder="{{ __('Option value') }}" class="flex-1 border border-slate-300 rounded-lg py-2 px-3 text-sm option-key">
        <input type="text" placeholder="{{ __('Display label') }}" class="flex-1 border border-slate-300 rounded-lg py-2 px-3 text-sm option-value">
        <button type="button" onclick="removeOption(this)" class="px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors text-sm font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
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
