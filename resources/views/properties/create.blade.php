<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-900">{{ __('Create Property') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto">
            <x-flash-status />
            <x-form-errors />
            
            <form method="POST" action="{{ route('properties.store') }}" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-6 space-y-6">
                @csrf
                
                <!-- Basic Info (Collapsible) -->
                <div class="border-b pb-4">
                    <button type="button" onclick="toggleSection('basic-info')" class="flex items-center justify-between w-full text-left">
                        <h3 class="font-medium text-gray-900">{{ __('Basic Information') }}</h3>
                        <svg id="icon-basic-info" class="w-4 h-4 text-gray-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div id="basic-info" class="mt-4 space-y-4 overflow-hidden transition-all duration-300">
                        <div class="grid grid-cols-2 gap-4">
                            <input name="name[en]" placeholder="{{ __('Name (English)') }}" class="border rounded px-3 py-2" value="{{ old('name.en') }}" required>
                            <input name="name[ar]" placeholder="{{ __('الاسم (العربية)') }}" class="border rounded px-3 py-2" dir="rtl" value="{{ old('name.ar') }}">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <textarea name="description[en]" placeholder="{{ __('Description (English)') }}" rows="3" class="border rounded px-3 py-2">{{ old('description.en') }}</textarea>
                            <textarea name="description[ar]" placeholder="{{ __('الوصف (العربية)') }}" rows="3" class="border rounded px-3 py-2" dir="rtl">{{ old('description.ar') }}</textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <select name="category_id" class="border rounded py-2">
                                <option value="">{{ __('Select Category') }}</option>
                                @foreach(($categories ?? []) as $c)
                                    <option value="{{ $c->id }}" @selected(old('category_id') == $c->id)>{{ $c->name }}</option>
                                @endforeach
                            </select>
                            
                            @if(($agents ?? collect())->isNotEmpty())
                                <select name="agent_id" class="border rounded py-2">
                                    <option value="">{{ __('Select Agent') }}</option>
                                    @foreach($agents as $id => $name)
                                        <option value="{{ $id }}" @selected(old('agent_id', auth()->user()?->agent_id) == $id)>{{ $name }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>

                        <input name="address" placeholder="{{ __('Address') }}" class="w-full border rounded px-3 py-2" value="{{ old('address') }}">
                        <input name="postal" placeholder="{{ __('Postal Code') }}" class="w-full border rounded px-3 py-2" value="{{ old('postal') }}">
                    </div>
                </div>

                <!-- Media (Collapsible) -->
                <div class="border-b pb-4">
                    <button type="button" onclick="toggleSection('media')" class="flex items-center justify-between w-full text-left">
                        <h3 class="font-medium text-gray-900">{{ __('Photos') }}</h3>
                        <svg id="icon-media" class="w-4 h-4 text-gray-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div id="media" class="mt-4 overflow-hidden transition-all duration-300">
                        <input type="file" name="photos[]" multiple accept="image/*" class="w-full border rounded px-3 py-2">
                        <p class="mt-2 text-xs text-gray-500">{{ __('You can upload multiple images. Max 5MB each.') }}</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <a href="{{ route('properties.index') }}" class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-50">{{ __('Cancel') }}</a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">{{ __('Create Property') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

<script>
    function toggleSection(id) {
        const section = document.getElementById(id);
        const icon = document.getElementById(`icon-${id}`);
        
        if (section.style.maxHeight === '0px' || !section.style.maxHeight) {
            section.style.maxHeight = section.scrollHeight + 'px';
            icon.style.transform = 'rotate(180deg)';
        } else {
            section.style.maxHeight = '0px';
            icon.style.transform = 'rotate(0deg)';
        }
    }

    // Collapse all sections by default
    document.addEventListener('DOMContentLoaded', () => {
        const sections = ['basic-info', 'media'];
        sections.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.style.maxHeight = '0px';
            }
        });
    });
</script>
