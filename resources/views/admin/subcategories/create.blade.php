<x-admin-layout>
    <x-slot name="header">{{ __('New Subcategory') }}</x-slot>

    <div class="max-w-2xl">
        <div class="gz-widget">
            <div class="p-6">
                <form method="post" action="{{ route('admin.subcategories.store') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Category') }}</label>
                        <select name="category_id" class="gz-search w-full" required>
                            <option value="">{{ __('Select…') }}</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('category_id')==$cat->id)>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')<div class="text-sm text-[#e8604c] mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Name (EN)') }}</label>
                            <input name="name[en]" value="{{ old('name.en') }}" class="gz-search w-full" required />
                            @error('name.en')<div class="text-sm text-[#e8604c] mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Name (AR)') }}</label>
                            <input name="name[ar]" value="{{ old('name.ar') }}" class="gz-search w-full" />
                            @error('name.ar')<div class="text-sm text-[#e8604c] mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Slug (optional)') }}</label>
                        <input name="slug" value="{{ old('slug') }}" class="gz-search w-full" />
                        @error('slug')<div class="text-sm text-[#e8604c] mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Description (EN)') }}</label>
                            <textarea name="description[en]" rows="4" class="gz-search w-full">{{ old('description.en') }}</textarea>
                            @error('description.en')<div class="text-sm text-[#e8604c] mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Description (AR)') }}</label>
                            <textarea name="description[ar]" rows="4" class="gz-search w-full">{{ old('description.ar') }}</textarea>
                            @error('description.ar')<div class="text-sm text-[#e8604c] mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-[#e8604c] focus:ring-[#e8604c]" {{ old('is_active', true) ? 'checked' : '' }} />
                            <span class="text-sm text-[#1e1e2d]">{{ __('Active') }}</span>
                        </label>
                        <div class="ml-auto">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Sort Order') }}</label>
                            <input type="number" name="sort_order" min="0" value="{{ old('sort_order', 0) }}" class="gz-search w-28" />
                        </div>
                    </div>
                    <div class="pt-3 flex gap-2 border-t border-[#e8ecf3]">
                        <a href="{{ route('admin.subcategories.index') }}" class="gz-btn gz-btn-outline">{{ __('Cancel') }}</a>
                        <button class="gz-btn gz-btn-primary">{{ __('Create') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
