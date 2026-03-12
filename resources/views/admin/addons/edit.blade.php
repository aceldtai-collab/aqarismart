<x-admin-layout>
    <x-slot name="header">{{ __('Edit Add-on') }}: {{ $addon->name }}</x-slot>

    <div class="max-w-2xl">
        <div class="gz-widget">
            <div class="p-4 sm:p-6">
                <form method="post" action="{{ route('admin.addons.update', $addon) }}" class="space-y-5">
                    @csrf @method('put')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Name') }}</label>
                            <input name="name" value="{{ old('name', $addon->name) }}" class="gz-search w-full" required />
                            @error('name')<div class="text-sm text-[#e8604c] mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Slug') }}</label>
                            <input name="slug" value="{{ old('slug', $addon->slug) }}" class="gz-search w-full" required />
                            @error('slug')<div class="text-sm text-[#e8604c] mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Description') }}</label>
                        <textarea name="description" rows="2" class="gz-search w-full">{{ old('description', $addon->description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Feature') }}</label>
                            <select name="feature" class="gz-search w-full" required>
                                @foreach($featureOptions as $f)
                                    <option value="{{ $f }}" {{ old('feature', $addon->feature) === $f ? 'selected' : '' }}>{{ __(ucfirst($f)) }}</option>
                                @endforeach
                            </select>
                            @error('feature')<div class="text-sm text-[#e8604c] mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Quantity granted') }}</label>
                            <input type="number" name="qty" min="1" value="{{ old('qty', $addon->qty) }}" class="gz-search w-full" required />
                            @error('qty')<div class="text-sm text-[#e8604c] mt-1">{{ $message }}</div>@enderror
                            <p class="text-xs text-[#7c8db5] mt-1">{{ __('How many extra seats/units this add-on grants') }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Price Monthly ($)') }}</label>
                            <input type="number" step="0.01" min="0" name="price_monthly" value="{{ old('price_monthly', $addon->formattedMonthlyPrice()) }}" class="gz-search w-full" required />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Price Yearly ($)') }}</label>
                            <input type="number" step="0.01" min="0" name="price_yearly" value="{{ old('price_yearly', $addon->formattedYearlyPrice()) }}" class="gz-search w-full" required />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Stripe Price ID (Monthly)') }}</label>
                            <input name="stripe_price_monthly" value="{{ old('stripe_price_monthly', $addon->stripe_price_monthly) }}" placeholder="price_..." class="gz-search w-full" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Stripe Price ID (Yearly)') }}</label>
                            <input name="stripe_price_yearly" value="{{ old('stripe_price_yearly', $addon->stripe_price_yearly) }}" placeholder="price_..." class="gz-search w-full" />
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-4 sm:gap-6">
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-[#e8604c] focus:ring-[#e8604c]" {{ old('is_active', $addon->is_active) ? 'checked' : '' }} />
                            <span class="text-sm text-[#1e1e2d]">{{ __('Active') }}</span>
                        </label>
                        <div class="ml-auto">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Sort Order') }}</label>
                            <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $addon->sort_order) }}" class="gz-search w-24" />
                        </div>
                    </div>

                    <div class="pt-3 flex gap-2 border-t border-[#e8ecf3]">
                        <a href="{{ route('admin.addons.index') }}" class="gz-btn gz-btn-outline">{{ __('Cancel') }}</a>
                        <button class="gz-btn gz-btn-primary">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
