@props(['fields', 'unitAttributes' => collect(), 'prefix' => ''])

<div x-data="dynamicFields({{ $prefix }})" class="space-y-6">
    <h3 class="text-lg font-medium text-gray-900">{{ __('Custom Attributes') }}</h3>
    <template x-for="field in fields" :key="field.id">
        <div class="border p-4 rounded">
            <div class="flex gap-4">
                <div class="flex-1">
                    <x-input-label :for="'attr_' + field.id" x-text="field.label" />
                </div>
                <div class="flex-2">
                    <template x-if="field.type === 'enum'">
                        <select :id="'attr_' + field.id" :name="getName(field)" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" :required="field.required">
                            <option value="">-- {{ __('Select') }} --</option>
                                <template x-for="option in field.options" :key="option">
                                    <option :value="option" x-selected="option == currentValue(field.id)">{{ option }}</option>
                                </template>
                        </select>
                    </template>
                    <template x-if="field.type === 'multi_enum'">
                        <select :id="'attr_' + field.id" :name="getName(field) + '[]'" multiple class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" :required="field.required">
                            <template x-for="option in field.options" :key="option">
                                <option :value="option" x-selected="selectedMulti(field.id).includes(option)">{{ option }}</option>
                            </template>
                        </select>
                    </template>
                    <template x-if="field.type === 'bool'">
                        <input type="checkbox" :id="'attr_' + field.id" :name="getName(field)" class="rounded border-gray-300 text-indigo-600 shadow-sm" :required="field.required" :value="currentValue(field.id) ? 1 : 0">
                    </template>
                    <template x-if="field.type === 'int'">
                        <input type="number" :id="'attr_' + field.id" :name="getName(field)" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" :required="field.required" :min="field.min" :max="field.max" x-model.number="currentValue(field.id)">
                    </template>
                    <template x-if="field.type === 'decimal'">
                        <input type="number" step="0.01" :id="'attr_' + field.id" :name="getName(field)" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" :required="field.required" :min="field.min" :max="field.max" x-model.number="currentValue(field.id)">
                    </template>
                    <template x-if="field.type === 'string'">
                        <input type="text" :id="'attr_' + field.id" :name="getName(field)" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" :required="field.required" x-model="currentValue(field.id)">
                    </template>
                    <template x-if="field.type === 'date'">
                        <input type="date" :id="'attr_' + field.id" :name="getName(field)" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" :required="field.required" x-model="currentValue(field.id)">
                    </template>
                    <template x-if="field.type === 'json'">
                        <textarea :id="'attr_' + field.id" :name="getName(field)" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" :required="field.required" x-model="currentValue(field.id)"></textarea>
                    </template>
                </div>
                <div x-show="field.unit" class="self-end text-sm text-gray-500" x-text="field.unit"></div>
            </div>
            <x-input-error :messages="$errors->get('attributes.' + field.id)" class="mt-2" />
        </div>
    </template>
</div>

<script>
    Alpine.data('dynamicFields', (prefix) => ({
        prefix: prefix,
        getName(field) {
            const type = field.type;
            let column;
            if (type === 'int') column = 'int_value';
            else if (type === 'decimal') column = 'decimal_value';
            else if (type === 'string') column = 'string_value';
            else if (type === 'bool') column = 'bool_value';
            else column = 'json_value';

            return `${prefix}[${field.id}][${column}]`;
        },
        currentValue(fieldId) {
            // Initial values if needed
            return '';
        },
        selectedMulti(fieldId) {
            // For multi_enum, return array of selected values
            return [];
        }
    }));
</script>
