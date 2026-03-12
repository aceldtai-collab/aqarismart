<x-admin-layout>
    <x-slot name="header">{{ __('Edit Package') }}: {{ $package->name }}</x-slot>

    <div class="max-w-3xl">
        <div class="gz-widget">
            <div class="p-4 sm:p-6">
                <form method="post" action="{{ route('admin.packages.update', $package) }}" class="space-y-5">
                    @csrf @method('put')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Name') }}</label>
                            <input name="name" value="{{ old('name', $package->name) }}" class="gz-search w-full" required />
                            @error('name')<div class="text-sm text-[#e8604c] mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Slug') }}</label>
                            <input name="slug" value="{{ old('slug', $package->slug) }}" class="gz-search w-full" required />
                            @error('slug')<div class="text-sm text-[#e8604c] mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Description') }}</label>
                        <textarea name="description" rows="2" class="gz-search w-full">{{ old('description', $package->description) }}</textarea>
                        @error('description')<div class="text-sm text-[#e8604c] mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Price Monthly ($)') }}</label>
                            <input type="number" step="0.01" min="0" name="price_monthly" value="{{ old('price_monthly', $package->formattedMonthlyPrice()) }}" class="gz-search w-full" required />
                            @error('price_monthly')<div class="text-sm text-[#e8604c] mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Price Yearly ($)') }}</label>
                            <input type="number" step="0.01" min="0" name="price_yearly" value="{{ old('price_yearly', $package->formattedYearlyPrice()) }}" class="gz-search w-full" required />
                            @error('price_yearly')<div class="text-sm text-[#e8604c] mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Stripe Price ID (Monthly)') }}</label>
                            <input name="stripe_price_monthly" value="{{ old('stripe_price_monthly', $package->stripe_price_monthly) }}" placeholder="price_..." class="gz-search w-full" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Stripe Price ID (Yearly)') }}</label>
                            <input name="stripe_price_yearly" value="{{ old('stripe_price_yearly', $package->stripe_price_yearly) }}" placeholder="price_..." class="gz-search w-full" />
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-4 sm:gap-6">
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-[#e8604c] focus:ring-[#e8604c]" {{ old('is_active', $package->is_active) ? 'checked' : '' }} />
                            <span class="text-sm text-[#1e1e2d]">{{ __('Active') }}</span>
                        </label>
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="is_default" value="1" class="rounded border-gray-300 text-[#e8604c] focus:ring-[#e8604c]" {{ old('is_default', $package->is_default) ? 'checked' : '' }} />
                            <span class="text-sm text-[#1e1e2d]">{{ __('Default package (auto-assigned on signup)') }}</span>
                        </label>
                        <div class="ml-auto">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Sort Order') }}</label>
                            <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $package->sort_order) }}" class="gz-search w-24" />
                        </div>
                    </div>

                    <div class="border-t border-[#e8ecf3] pt-5">
                        <h3 class="text-sm font-bold text-[#1e1e2d] mb-3">{{ __('Entitlements') }}</h3>
                        <div class="space-y-3">
                            @foreach($featureOptions as $feature => $type)
                                @php $ent = $currentEntitlements->get($feature); @endphp
                                <div class="flex flex-wrap items-center gap-4 p-3 rounded-xl border border-[#e8ecf3] bg-[#f5f6fa]">
                                    <label class="inline-flex items-center gap-2 min-w-[140px]">
                                        <input type="checkbox" name="entitlements[{{ $feature }}][enabled]" value="1"
                                               class="rounded border-gray-300 text-[#e8604c] focus:ring-[#e8604c]"
                                               {{ old("entitlements.{$feature}.enabled", $ent ? true : false) ? 'checked' : '' }} />
                                        <span class="text-sm font-semibold text-[#1e1e2d] capitalize">{{ __(ucfirst($feature)) }}</span>
                                    </label>
                                    @if($type === 'limit')
                                        <div class="flex items-center gap-2">
                                            <label class="text-xs text-[#7c8db5]">{{ __('Limit') }}:</label>
                                            <input type="number" name="entitlements[{{ $feature }}][limit]" min="1"
                                                   value="{{ old("entitlements.{$feature}.limit", $ent?->limit_value) }}"
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

                    <div class="rounded-xl bg-[#f5f6fa] border border-[#e8ecf3] p-4 text-sm">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <span class="text-xs text-[#7c8db5] uppercase tracking-wider">{{ __('Active Subscribers') }}</span>
                                <div class="font-bold text-[#1e1e2d]">{{ $package->activeSubscriptions()->count() }}</div>
                            </div>
                            <div>
                                <span class="text-xs text-[#7c8db5] uppercase tracking-wider">{{ __('Created') }}</span>
                                <div class="font-bold text-[#1e1e2d]">{{ $package->created_at->format('M d, Y') }}</div>
                            </div>
                            <div>
                                <span class="text-xs text-[#7c8db5] uppercase tracking-wider">{{ __('Last Updated') }}</span>
                                <div class="font-bold text-[#1e1e2d]">{{ $package->updated_at->format('M d, Y') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 flex gap-2 border-t border-[#e8ecf3]">
                        <a href="{{ route('admin.packages.index') }}" class="gz-btn gz-btn-outline">{{ __('Cancel') }}</a>
                        <button class="gz-btn gz-btn-primary">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
