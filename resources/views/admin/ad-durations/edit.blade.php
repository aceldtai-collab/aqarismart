@php
    $isAr = app()->getLocale() === 'ar';
@endphp
<x-admin-layout>
    <x-slot name="header">{{ $isAr ? 'تعديل مدة الإعلان' : 'Edit Ad Duration' }}</x-slot>

    <div class="max-w-2xl">
        <div class="gz-widget">
            <div class="p-6">
                <form method="post" action="{{ route('admin.ad-durations.update', $adDuration) }}" class="space-y-5">
                    @csrf
                    @method('put')
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ $isAr ? 'الاسم بالإنجليزية' : 'Name (EN)' }}</label>
                            <input name="name_en" value="{{ old('name_en', $adDuration->name_en) }}" class="gz-search w-full" required />
                            @error('name_en')<div class="text-sm text-[#e8604c] mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ $isAr ? 'الاسم بالعربية' : 'Name (AR)' }}</label>
                            <input name="name_ar" value="{{ old('name_ar', $adDuration->name_ar) }}" class="gz-search w-full" required />
                            @error('name_ar')<div class="text-sm text-[#e8604c] mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ $isAr ? 'الأيام' : 'Days' }}</label>
                            <input type="number" name="days" min="1" value="{{ old('days', $adDuration->days) }}" class="gz-search w-full" required />
                            @error('days')<div class="text-sm text-[#e8604c] mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ $isAr ? 'السعر' : 'Price' }}</label>
                            <input type="number" name="price" min="0" step="0.01" value="{{ old('price', $adDuration->price) }}" class="gz-search w-full" required />
                            @error('price')<div class="text-sm text-[#e8604c] mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ $isAr ? 'العملة' : 'Currency' }}</label>
                            <input name="currency" value="{{ old('currency', $adDuration->currency) }}" class="gz-search w-full" />
                            @error('currency')<div class="text-sm text-[#e8604c] mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-[#e8604c] focus:ring-[#e8604c]" {{ old('is_active', $adDuration->is_active) ? 'checked' : '' }} />
                            <span class="text-sm text-[#1e1e2d]">{{ $isAr ? 'نشط' : 'Active' }}</span>
                        </label>
                        <div class="ml-auto">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ $isAr ? 'الترتيب' : 'Sort Order' }}</label>
                            <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $adDuration->sort_order) }}" class="gz-search w-28" />
                        </div>
                    </div>
                    <div class="pt-3 flex gap-2 border-t border-[#e8ecf3]">
                        <a href="{{ route('admin.ad-durations.index') }}" class="gz-btn gz-btn-outline">{{ $isAr ? 'إلغاء' : 'Cancel' }}</a>
                        <button class="gz-btn gz-btn-primary">{{ $isAr ? 'حفظ التغييرات' : 'Save Changes' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
