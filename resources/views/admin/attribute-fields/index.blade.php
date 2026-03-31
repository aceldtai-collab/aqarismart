<x-admin-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">{{ __('Attribute Fields') }}</h2>
            <p class="mt-1 text-xs text-slate-500">{{ __('Define the dynamic fields available for each subcategory.') }}</p>
        </div>
    </x-slot>
    <x-slot name="headerActions">
        <a href="{{ route('admin.attribute-fields.create', request()->only('lang')) }}" class="inline-flex items-center gap-2 rounded-md bg-gray-50 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-800">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4"><path fill-rule="evenodd" d="M10 3a1 1 0 0 1 1 1v5h5a1 1 0 1 1 0 2h-5v5a1 1 0 1 1-2 0v-5H4a1 1 0 1 1 0-2h5V4a1 1 0 0 1 1-1Z" clip-rule="evenodd"/></svg>
            <span>{{ __('Add Field') }}</span>
        </a>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4">
            <x-flash-status />

            @forelse($fields as $subcategoryName => $subcategoryFields)
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="px-6 py-4 border-b border-gray-200 cursor-pointer" onclick="toggleSection('{{ Str::slug($subcategoryName) }}')">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $subcategoryName }}</h3>
                            <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200" id="icon-{{ Str::slug($subcategoryName) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>

                    <div id="section-{{ Str::slug($subcategoryName) }}" class="hidden">
                        @php
                            $groupedFields = $subcategoryFields->groupBy('group');
                        @endphp

                        @foreach($groupedFields as $groupName => $groupFields)
                            <div class="border-b border-gray-100 last:border-b-0">
                                <div class="px-6 py-3 bg-gray-50 border-b border-gray-100">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-sm font-semibold text-gray-800 uppercase tracking-wide">{{ __($groupName) }}</h4>
                                        <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded-full">{{ $groupFields->count() }}</span>
                                    </div>
                                </div>
                                <div class="px-6 py-4">
                                    <div class="grid gap-3">
                                        @foreach($groupFields->sortBy('sort') as $field)
                                            <div class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg hover:border-[#5b73e8]/40 hover:bg-[#5b73e8]/10 transition-all duration-200">
                                                <div class="flex items-center space-x-4">
                                                    <div class="w-10 h-10 bg-[#5b73e8]/15 rounded-lg flex items-center justify-center">
                                                        @switch($field->type)
                                                            @case('bool')
                                                                <svg class="w-5 h-5 text-[#5b73e8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                </svg>
                                                                @break
                                                            @case('enum')
                                                                <svg class="w-5 h-5 text-[#5b73e8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                                                </svg>
                                                                @break
                                                            @case('int')
                                                            @case('decimal')
                                                                <svg class="w-5 h-5 text-[#5b73e8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                                                </svg>
                                                                @break
                                                            @case('date')
                                                                <svg class="w-5 h-5 text-[#5b73e8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                                </svg>
                                                                @break
                                                            @default
                                                                <svg class="w-5 h-5 text-[#5b73e8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                                                                </svg>
                                                        @endswitch
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex items-center space-x-2">
                                                            <h5 class="font-medium text-gray-900 truncate">{{ $field->translated_label }}</h5>
                                                            @if($field->required)
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-[#e8604c]/15 text-[#e8604c]">
                                                                    {{ __('Required') }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="flex items-center space-x-3 mt-1">
                                                            <span class="text-sm text-gray-500">{{ $field->key }}</span>
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-[#5b73e8]/15 text-[#5b73e8]">
                                                                {{ $field->type }}
                                                            </span>
                                                            @if($field->unit)
                                                                <span class="text-xs text-gray-400">{{ $field->unit }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <a href="{{ route('admin.attribute-fields.edit', ['attributeField' => $field] + request()->only('lang')) }}" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-[#5b73e8] bg-[#5b73e8]/10 rounded-md hover:bg-[#5b73e8]/15 transition-colors duration-200">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                        {{ __('Edit') }}
                                                    </a>
                                                    <form method="POST" action="{{ route('admin.attribute-fields.destroy', ['attributeField' => $field] + request()->only('lang')) }}" class="inline" onsubmit="return confirm('{{ __('Delete this field?') }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-[#e8604c] bg-[#e8604c]/10 rounded-md hover:bg-[#e8604c]/20 transition-colors duration-200">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                            {{ __('Delete') }}
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow p-6 text-center">
                    <p class="text-gray-500">{{ __('No attribute fields found') }}</p>
                    <a href="{{ route('admin.attribute-fields.create', request()->only('lang')) }}" class="mt-4 inline-block px-4 py-2 bg-[#e8604c] text-white rounded hover:bg-[#d4503e]">
                        {{ __('Add First Field') }}
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</x-admin-layout>

<script>
function toggleSection(sectionId) {
    const section = document.getElementById('section-' + sectionId);
    const icon = document.getElementById('icon-' + sectionId);
    
    if (section.classList.contains('hidden')) {
        section.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
    } else {
        section.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
    }
}
</script>