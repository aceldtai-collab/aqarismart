<x-admin-layout>
    <x-slot name="header">{{ __('Subscription Management') }}: {{ $tenant->name }}</x-slot>
    <x-slot name="subtitle">{{ __('Manage package, add-ons and usage for this tenant.') }}</x-slot>
    <x-slot name="headerActions">
        <a href="{{ route('admin.tenants.show', $tenant) }}" class="gz-btn gz-btn-outline text-xs">
            &larr; {{ __('Back to Tenant') }}
        </a>
    </x-slot>

    <div class="space-y-5">
        {{-- Current Subscription --}}
        <div class="gz-widget">
            <div class="gz-widget-header"><h3 class="text-sm font-bold text-[#1e1e2d]">{{ __('Current Subscription') }}</h3></div>
            <div class="gz-widget-body">
                @if($tenant->activeSubscription)
                    @php $sub = $tenant->activeSubscription; @endphp
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <span class="text-xs text-[#7c8db5] uppercase tracking-wider">{{ __('Package') }}</span>
                            <div class="font-bold text-[#1e1e2d] mt-0.5">{{ $sub->package->name }}</div>
                        </div>
                        <div>
                            <span class="text-xs text-[#7c8db5] uppercase tracking-wider">{{ __('Billing Cycle') }}</span>
                            <div class="font-bold text-[#1e1e2d] mt-0.5 capitalize">{{ __($sub->billing_cycle) }}</div>
                        </div>
                        <div>
                            <span class="text-xs text-[#7c8db5] uppercase tracking-wider">{{ __('Status') }}</span>
                            <div class="mt-0.5">
                                <span class="gz-badge
                                    {{ $sub->status === 'active' ? 'bg-[#2bc155]/10 text-[#2bc155]' : '' }}
                                    {{ $sub->status === 'trialing' ? 'bg-[#5b73e8]/10 text-[#5b73e8]' : '' }}
                                    {{ $sub->status === 'canceled' ? 'bg-[#e8604c]/10 text-[#e8604c]' : '' }}
                                    {{ $sub->status === 'past_due' ? 'bg-[#ffab2d]/10 text-[#ffab2d]' : '' }}
                                ">{{ __(ucfirst($sub->status)) }}</span>
                            </div>
                        </div>
                        <div>
                            <span class="text-xs text-[#7c8db5] uppercase tracking-wider">{{ __('Since') }}</span>
                            <div class="font-bold text-[#1e1e2d] mt-0.5">{{ $sub->starts_at?->format('M d, Y') ?? '—' }}</div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <form method="post" action="{{ route('admin.tenants.subscription.cancel', $tenant) }}" class="inline" onsubmit="return confirm('{{ __('Cancel this subscription?') }}')">
                            @csrf @method('delete')
                            <button class="gz-btn text-xs py-1.5 px-3 text-[#e8604c] border border-[#e8604c]/20 hover:bg-[#e8604c]/5">{{ __('Cancel Subscription') }}</button>
                        </form>
                    </div>
                @else
                    <p class="text-sm text-[#7c8db5]">{{ __('No active subscription.') }}</p>
                    @if($tenant->plan)
                        <p class="text-xs text-[#7c8db5] mt-1">{{ __('Legacy plan column') }}: <span class="font-mono">{{ $tenant->plan }}</span></p>
                    @endif
                @endif
            </div>
        </div>

        {{-- Assign / Change Package --}}
        <div class="gz-widget">
            <div class="gz-widget-header"><h3 class="text-sm font-bold text-[#1e1e2d]">{{ __('Assign / Change Package') }}</h3></div>
            <div class="gz-widget-body">
                <form method="post" action="{{ route('admin.tenants.subscription.subscribe', $tenant) }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Package') }}</label>
                            <select name="package_id" class="gz-search w-full" required>
                                <option value="">{{ __('Select package') }}</option>
                                @foreach($allPackages as $pkg)
                                    <option value="{{ $pkg->id }}" {{ $tenant->activeSubscription?->package_id == $pkg->id ? 'selected' : '' }}>
                                        {{ $pkg->name }} — ${{ $pkg->formattedMonthlyPrice() }}/mo
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Billing Cycle') }}</label>
                            <select name="billing_cycle" class="gz-search w-full" required>
                                <option value="monthly" {{ ($tenant->activeSubscription?->billing_cycle ?? 'monthly') === 'monthly' ? 'selected' : '' }}>{{ __('Monthly') }}</option>
                                <option value="yearly" {{ ($tenant->activeSubscription?->billing_cycle ?? '') === 'yearly' ? 'selected' : '' }}>{{ __('Yearly') }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Trial Days') }}</label>
                            <input type="number" name="trial_days" min="0" value="0" class="gz-search w-full" />
                            <p class="text-xs text-[#7c8db5] mt-1">{{ __('0 = no trial') }}</p>
                        </div>
                    </div>
                    <button class="gz-btn gz-btn-primary">
                        {{ $tenant->activeSubscription ? __('Change Package') : __('Assign Package') }}
                    </button>
                </form>
            </div>
        </div>

        {{-- Usage Summary --}}
        @if(!empty($usage))
        <div class="gz-widget">
            <div class="gz-widget-header"><h3 class="text-sm font-bold text-[#1e1e2d]">{{ __('Usage Summary') }}</h3></div>
            <div class="gz-widget-body">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    @foreach($usage as $feature => $info)
                        <div class="rounded-xl border border-[#e8ecf3] p-4">
                            <div class="text-xs text-[#7c8db5] uppercase tracking-wider mb-1">{{ __(ucfirst($feature)) }}</div>
                            @if($info['type'] === 'boolean')
                                <span class="gz-badge bg-[#2bc155]/10 text-[#2bc155]">{{ __('Enabled') }}</span>
                            @else
                                <div class="text-lg font-bold text-[#1e1e2d]">
                                    {{ $info['used'] }}
                                    <span class="text-sm font-normal text-[#7c8db5]">/ {{ $info['unlimited'] ? '∞' : $info['limit'] }}</span>
                                </div>
                                @if(!$info['unlimited'])
                                    @php $pct = $info['limit'] > 0 ? min(100, round(($info['used'] / $info['limit']) * 100)) : 0; @endphp
                                    <div class="mt-2 w-full bg-gray-200 rounded-full h-1.5">
                                        <div class="h-1.5 rounded-full {{ $pct >= 90 ? 'bg-[#e8604c]' : ($pct >= 70 ? 'bg-[#ffab2d]' : 'bg-[#2bc155]') }}" style="width: {{ $pct }}%"></div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Active Add-ons --}}
        <div class="gz-widget">
            <div class="gz-widget-header"><h3 class="text-sm font-bold text-[#1e1e2d]">{{ __('Active Add-ons') }}</h3></div>
            <div class="gz-widget-body">
                @php $activeAddons = $tenant->activeAddons; @endphp
                @if($activeAddons->count())
                    <div class="gz-table-wrap">
                        <table class="w-full gz-table">
                            <thead>
                                <tr>
                                    <th class="text-left">{{ __('Add-on') }}</th>
                                    <th class="text-left">{{ __('Feature') }}</th>
                                    <th class="text-left">{{ __('Qty') }}</th>
                                    <th class="text-left">{{ __('Grants') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activeAddons as $ta)
                                    <tr>
                                        <td class="font-semibold text-[#1e1e2d]">{{ $ta->addon->name }}</td>
                                        <td><span class="gz-badge bg-[#5b73e8]/10 text-[#5b73e8] capitalize">{{ $ta->addon->feature }}</span></td>
                                        <td class="text-[#7c8db5]">{{ $ta->qty }}</td>
                                        <td><span class="gz-badge bg-[#2bc155]/10 text-[#2bc155]">+{{ $ta->grantedUnits() }}</span></td>
                                        <td class="text-right">
                                            <form method="post" action="{{ route('admin.tenants.addons.remove', [$tenant, $ta]) }}" class="inline" onsubmit="return confirm('{{ __('Remove this add-on?') }}')">
                                                @csrf @method('delete')
                                                <button class="gz-btn text-xs py-1 px-2 text-[#e8604c] border border-[#e8604c]/20 hover:bg-[#e8604c]/5">{{ __('Remove') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-[#7c8db5]">{{ __('No active add-ons.') }}</p>
                @endif

                @if($allAddons->count())
                    <div class="mt-4 pt-4 border-t border-[#e8ecf3]">
                        <h4 class="text-xs font-bold text-[#1e1e2d] mb-2">{{ __('Attach Add-on') }}</h4>
                        <form method="post" action="{{ route('admin.tenants.addons.attach', $tenant) }}" class="flex flex-wrap items-end gap-3">
                            @csrf
                            <div>
                                <label class="block text-xs text-[#7c8db5] mb-1">{{ __('Add-on') }}</label>
                                <select name="addon_id" class="gz-search" required>
                                    <option value="">{{ __('Select') }}</option>
                                    @foreach($allAddons as $addon)
                                        <option value="{{ $addon->id }}">{{ $addon->name }} (+{{ $addon->qty }} {{ $addon->feature }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-[#7c8db5] mb-1">{{ __('Qty') }}</label>
                                <input type="number" name="qty" min="1" value="1" class="gz-search w-20" required />
                            </div>
                            <div>
                                <label class="block text-xs text-[#7c8db5] mb-1">{{ __('Cycle') }}</label>
                                <select name="billing_cycle" class="gz-search">
                                    <option value="monthly">{{ __('Monthly') }}</option>
                                    <option value="yearly">{{ __('Yearly') }}</option>
                                </select>
                            </div>
                            <button class="gz-btn gz-btn-primary text-xs">{{ __('Attach') }}</button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        {{-- Subscription History --}}
        <div class="gz-widget">
            <div class="gz-widget-header"><h3 class="text-sm font-bold text-[#1e1e2d]">{{ __('Subscription History') }}</h3></div>
            @php $history = $tenant->subscriptions()->with('package')->latest()->limit(10)->get(); @endphp
            @if($history->count())
                <div class="gz-table-wrap">
                    <table class="w-full gz-table">
                        <thead>
                            <tr>
                                <th class="text-left">{{ __('Package') }}</th>
                                <th class="text-left">{{ __('Cycle') }}</th>
                                <th class="text-left">{{ __('Status') }}</th>
                                <th class="text-left">{{ __('Started') }}</th>
                                <th class="text-left">{{ __('Canceled') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($history as $h)
                                <tr>
                                    <td class="font-semibold text-[#1e1e2d]">{{ $h->package->name ?? '—' }}</td>
                                    <td class="text-[#7c8db5] capitalize">{{ $h->billing_cycle }}</td>
                                    <td>
                                        <span class="gz-badge
                                            {{ $h->status === 'active' ? 'bg-[#2bc155]/10 text-[#2bc155]' : '' }}
                                            {{ $h->status === 'trialing' ? 'bg-[#5b73e8]/10 text-[#5b73e8]' : '' }}
                                            {{ $h->status === 'canceled' ? 'bg-gray-100 text-[#7c8db5]' : '' }}
                                        ">{{ ucfirst($h->status) }}</span>
                                    </td>
                                    <td class="text-[#7c8db5]">{{ $h->starts_at?->format('M d, Y') ?? '—' }}</td>
                                    <td class="text-[#7c8db5]">{{ $h->canceled_at?->format('M d, Y') ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="gz-widget-body"><p class="text-sm text-[#7c8db5]">{{ __('No subscription history.') }}</p></div>
            @endif
        </div>
    </div>
</x-admin-layout>
