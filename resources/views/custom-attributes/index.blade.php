<x-app-layout>
    <x-slot name="header">
        <div class="text-right">
            <h2 class="text-lg font-semibold text-slate-900">{{ __('Custom Attributes') }}</h2>
            <p class="mt-1 text-xs text-slate-500">{{ __('Add your own custom fields to unit listings.') }}</p>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-5xl mx-auto space-y-6 sm:px-6 lg:px-8">
            <x-flash-status />

            <div class="flex justify-end">
                <a href="{{ route('custom-attributes.create', request()->only('lang')) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-50 text-sm font-semibold rounded-lg hover:bg-slate-800 transition-all shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4"><path fill-rule="evenodd" d="M10 3a1 1 0 0 1 1 1v5h5a1 1 0 1 1 0 2h-5v5a1 1 0 1 1-2 0v-5H4a1 1 0 1 1 0-2h5V4a1 1 0 0 1 1-1Z" clip-rule="evenodd"/></svg>
                    <span>{{ __('Add Attribute') }}</span>
                </a>
            </div>

            {{-- Info banner --}}
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                <div class="text-sm text-blue-800">
                    <p class="font-medium">{{ __('How it works') }}</p>
                    <p class="mt-1 text-blue-700">{{ __('Custom attributes appear as extra fields when you create or edit a unit. They are added alongside the :count standard attributes provided by the system.', ['count' => $globalCount]) }}</p>
                </div>
            </div>

            <form method="GET" action="{{ route('custom-attributes.index') }}" class="bg-white rounded-xl border border-slate-200/60 shadow-sm p-4">
                @if(request('lang'))
                    <input type="hidden" name="lang" value="{{ request('lang') }}">
                @endif
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-1">{{ __('Search') }}</label>
                        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="{{ __('Name, key, or unit') }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-slate-900 focus:ring-slate-900">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-1">{{ __('Category') }}</label>
                        <select name="subcategory_id" class="w-full rounded-lg border-slate-300 text-sm focus:border-slate-900 focus:ring-slate-900">
                            <option value="">{{ __('All Categories') }}</option>
                            @foreach($subcategories as $subcategory)
                                <option value="{{ $subcategory->id }}" @selected((int) ($filters['subcategory_id'] ?? 0) === (int) $subcategory->id)>{{ $subcategory->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-1">{{ __('Type') }}</label>
                        <select name="type" class="w-full rounded-lg border-slate-300 text-sm focus:border-slate-900 focus:ring-slate-900">
                            <option value="">{{ __('All Types') }}</option>
                            @foreach($types as $type)
                                <option value="{{ $type }}" @selected(($filters['type'] ?? '') === $type)>{{ __("attribute_type_{$type}") }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-1">{{ __('Group') }}</label>
                        <select name="group" class="w-full rounded-lg border-slate-300 text-sm focus:border-slate-900 focus:ring-slate-900">
                            <option value="">{{ __('All Groups') }}</option>
                            @foreach($groups as $group)
                                <option value="{{ $group }}" @selected(($filters['group'] ?? '') === $group)>{{ __('attribute_group_' . str($group)->snake()) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-2">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition-colors">{{ __('Apply Filters') }}</button>
                    <a href="{{ route('custom-attributes.index', request()->only('lang') + ['clear' => 1]) }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50 transition-colors">{{ __('Clear') }}</a>
                    @if(!empty($activeFilters))
                        <span class="text-xs text-slate-500">{{ __('Filters remembered') }}</span>
                    @endif
                </div>
            </form>

            @if($fields->isEmpty() && empty($activeFilters))
                <div class="bg-white rounded-xl border border-slate-200/60 shadow-sm p-12 text-center">
                    <div class="mx-auto w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900">{{ __('No custom attributes yet') }}</h3>
                    <p class="mt-2 text-sm text-slate-500 max-w-md mx-auto">{{ __('Create custom attributes to add extra information fields to your unit listings, like special amenities or custom measurements.') }}</p>
                    <a href="{{ route('custom-attributes.create', request()->only('lang')) }}" class="mt-6 inline-flex items-center gap-2 px-4 py-2.5 bg-gray-50 text-sm font-semibold rounded-lg hover:bg-slate-800 transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        {{ __('Create First Attribute') }}
                    </a>
                </div>
            @elseif($fields->isEmpty())
                <div class="bg-white rounded-xl border border-slate-200/60 shadow-sm p-10 text-center">
                    <h3 class="text-lg font-semibold text-slate-900">{{ __('No matching attributes') }}</h3>
                    <p class="mt-2 text-sm text-slate-500">{{ __('Adjust or clear the filters to see more custom attributes.') }}</p>
                </div>
            @else
                <form method="POST" action="{{ route('custom-attributes.update-sort', request()->only('lang')) }}" id="sort-form">
                    @csrf
                    @method('PATCH')
                    <div class="bg-white rounded-xl border border-slate-200/60 shadow-sm overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200/70">
                                <thead class="bg-gradient-to-r from-slate-50 to-slate-100/50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">{{ __('Name') }}</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">{{ __('Category') }}</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">{{ __('Type') }}</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">{{ __('Group') }}</th>
                                        <th class="px-4 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-600 w-20">{{ __('Order') }}</th>
                                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-slate-600">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($fields as $field)
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0
                                                        @switch($field->type)
                                                            @case('bool') bg-green-100 @break
                                                            @case('enum') bg-purple-100 @break
                                                            @case('int') @case('decimal') bg-blue-100 @break
                                                            @default bg-slate-100
                                                        @endswitch">
                                                        @switch($field->type)
                                                            @case('bool')
                                                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                                @break
                                                            @case('enum')
                                                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                                                                @break
                                                            @case('int') @case('decimal')
                                                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                                                                @break
                                                            @default
                                                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                                                        @endswitch
                                                    </div>
                                                    <div>
                                                        <p class="font-medium text-slate-900">{{ $field->translated_label }}</p>
                                                        @if($field->unit)
                                                            <p class="text-xs text-slate-400">{{ $field->unit }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-slate-600">{{ $field->subcategory?->name ?? '—' }}</td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @switch($field->type)
                                                        @case('bool') bg-green-100 text-green-700 @break
                                                        @case('enum') bg-purple-100 text-purple-700 @break
                                                        @case('int') @case('decimal') bg-blue-100 text-blue-700 @break
                                                        @default bg-slate-100 text-slate-700
                                                    @endswitch">
                                                    @switch($field->type)
                                                        @case('bool') {{ __('attribute_type_bool') }} @break
                                                        @case('int') {{ __('attribute_type_int') }} @break
                                                        @case('decimal') {{ __('attribute_type_decimal') }} @break
                                                        @case('string') {{ __('attribute_type_string') }} @break
                                                        @case('enum') {{ __('attribute_type_enum') }} @break
                                                        @case('multi_enum') {{ __('attribute_type_multi_enum') }} @break
                                                        @case('date') {{ __('attribute_type_date') }} @break
                                                        @case('json') {{ __('attribute_type_json') }} @break
                                                        @default {{ $field->type }}
                                                    @endswitch
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-slate-600">{{ __('attribute_group_' . str($field->group)->snake()) }}</td>
                                            <td class="px-4 py-4 text-center">
                                                <input type="number" name="sorts[{{ $field->id }}]" value="{{ $field->sort }}" min="0" max="9999" class="w-16 text-center border border-slate-300 rounded-lg py-1.5 px-1 text-sm focus:ring-2 focus:ring-slate-900 focus:border-slate-900" onchange="document.getElementById('save-sort-btn').classList.remove('hidden')">
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center justify-end gap-2">
                                                    <a href="{{ route('custom-attributes.edit', ['customAttribute' => $field] + request()->only('lang')) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                        {{ __('Edit') }}
                                                    </a>
                                                    <button type="button" onclick="deleteAttribute('{{ route('custom-attributes.destroy', ['customAttribute' => $field] + request()->only('lang')) }}')" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        {{ __('Delete') }}
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div id="save-sort-btn" class="hidden border-t border-slate-200 p-4 bg-slate-50 flex justify-end">
                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                {{ __('Save Order') }}
                            </button>
                        </div>
                    </div>
                </form>
                <form id="delete-attribute-form" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
                <script>
                function deleteAttribute(url) {
                    if (!confirm('{{ __('Are you sure you want to delete this attribute?') }}')) return;
                    const form = document.getElementById('delete-attribute-form');
                    form.action = url;
                    form.submit();
                }
                </script>
            @endif
        </div>
    </div>
</x-app-layout>
