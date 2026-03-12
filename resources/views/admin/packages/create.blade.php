<x-admin-layout>
    <x-slot name="header">{{ __('New Package') }}</x-slot>

    <div class="max-w-3xl">
        <div class="gz-widget">
            <div class="p-4 sm:p-6">
                <form method="post" action="{{ route('admin.packages.store') }}" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Name') }}</label>
                            <input name="name" value="{{ old('name') }}" class="gz-search w-full" required />
                            @error('name')<div class="text-sm text-[#e8604c] mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Slug') }}</label>
                            <input name="slug" value="{{ old('slug') }}" class="gz-search w-full" required />
                            @error('slug')<div class="text-sm text-[#e8604c] mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Description') }}</label>
                        <textarea name="description" rows="2" class="gz-search w-full">{{ old('description') }}</textarea>
                        @error('description')<div class="text-sm text-[#e8604c] mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Price Monthly ($)') }}</label>
                            <input type="number" step="0.01" min="0" name="price_monthly" value="{{ old('price_monthly', '0') }}" class="gz-search w-full" required />
                            @error('price_monthly')<div class="text-sm text-[#e8604c] mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Price Yearly ($)') }}</label>
                            <input type="number" step="0.01" min="0" name="price_yearly" value="{{ old('price_yearly', '0') }}" class="gz-search w-full" required />
                            @error('price_yearly')<div class="text-sm text-[#e8604c] mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Stripe Price ID (Monthly)') }}</label>
                            <input name="stripe_price_monthly" value="{{ old('stripe_price_monthly') }}" placeholder="price_..." class="gz-search w-full" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Stripe Price ID (Yearly)') }}</label>
                            <input name="stripe_price_yearly" value="{{ old('stripe_price_yearly') }}" placeholder="price_..." class="gz-search w-full" />
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-4 sm:gap-6">
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-[#e8604c] focus:ring-[#e8604c]" {{ old('is_active', true) ? 'checked' : '' }} />
                            <span class="text-sm text-[#1e1e2d]">{{ __('Active') }}</span>
                        </label>
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="is_default" value="1" class="rounded border-gray-300 text-[#e8604c] focus:ring-[#e8604c]" {{ old('is_default') ? 'checked' : '' }} />
                            <span class="text-sm text-[#1e1e2d]">{{ __('Default package (auto-assigned on signup)') }}</span>
                        </label>
                        <div class="ml-auto">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Sort Order') }}</label>
                            <input type="number" name="sort_order" min="0" value="{{ old('sort_order', 0) }}" class="gz-search w-24" />
                        </div>
                    </div>

                    <div class="border-t border-[#e8ecf3] pt-5">
                        <h3 class="text-sm font-bold text-[#1e1e2d] mb-3">{{ __('Entitlements') }}</h3>
                        <div class="space-y-3">
                            @foreach($featureOptions as $feature => $type)
                                <div class="flex flex-wrap items-center gap-4 p-3 rounded-xl border border-[#e8ecf3] bg-[#f5f6fa]">
                                    <label class="inline-flex items-center gap-2 min-w-[140px]">
                                        <input type="checkbox" name="entitlements[{{ $feature }}][enabled]" value="1"
                                               class="rounded border-gray-300 text-[#e8604c] focus:ring-[#e8604c]"
                                               {{ old("entitlements.{$feature}.enabled") ? 'checked' : '' }} />
                                        <span class="text-sm font-semibold text-[#1e1e2d] capitalize">{{ __(ucfirst($feature)) }}</span>
                                    </label>
                                    @if($type === 'limit')
                                        <div class="flex items-center gap-2">
                                            <label class="text-xs text-[#7c8db5]">{{ __('Limit') }}:</label>
                                            <input type="number" name="entitlements[{{ $feature }}][limit]" min="1"
                                                   value="{{ old("entitlements.{$feature}.limit") }}"
                                                   placeholder="{{ __('unlimited') }}"
                                                   class="gz-search w-28 text-sm" />
                                            <span class="text-xs text-[#7c8db5]">{{ __('(empty = unlimited)') }}</span>
                                        </div>
                                    @else
                                        <span class="text-xs text-[#7c8db5]">{{ __('On / Off') }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-3 flex gap-2 border-t border-[#e8ecf3]">
                        <a href="{{ route('admin.packages.index') }}" class="gz-btn gz-btn-outline">{{ __('Cancel') }}</a>
                        <button class="gz-btn gz-btn-primary">{{ __('Create Package') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
