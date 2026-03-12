<x-admin-layout>
    <x-slot name="header">{{ __('Tenants') }}</x-slot>
    <x-slot name="subtitle">{{ __('All registered tenants and their subscription status.') }}</x-slot>

    <div class="gz-widget">
      <div class="gz-table-wrap">
        <table class="w-full gz-table">
            <thead>
                <tr>
                    <th class="text-left">{{ __('Name') }}</th>
                    <th class="text-left">{{ __('Slug') }}</th>
                    <th class="text-left">{{ __('Package') }}</th>
                    <th class="text-left">{{ __('Status') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($tenants as $tenant)
                    @php $sub = $tenant->activeSubscription; @endphp
                    <tr>
                        <td>
                            <span class="font-semibold text-[#1e1e2d]">{{ $tenant->name }}</span>
                            <span class="text-xs text-[#7c8db5] ml-1">#{{ $tenant->id }}</span>
                        </td>
                        <td class="text-[#7c8db5]">{{ $tenant->slug }}</td>
                        <td>
                            @if($sub)
                                <span class="font-medium text-[#1e1e2d]">{{ $sub->package->name ?? '—' }}</span>
                                <span class="text-xs text-[#7c8db5] capitalize ml-1">{{ $sub->billing_cycle }}</span>
                            @else
                                <span class="text-[#7c8db5]">{{ $tenant->plan ?? __('None') }}</span>
                            @endif
                        </td>
                        <td>
                            @if($sub)
                                <span class="gz-badge
                                    {{ $sub->status === 'active' ? 'bg-[#2bc155]/10 text-[#2bc155]' : '' }}
                                    {{ $sub->status === 'trialing' ? 'bg-[#5b73e8]/10 text-[#5b73e8]' : '' }}
                                    {{ $sub->status === 'canceled' ? 'bg-gray-100 text-[#7c8db5]' : '' }}
                                    {{ $sub->status === 'past_due' ? 'bg-[#ffab2d]/10 text-[#ffab2d]' : '' }}
                                ">{{ __(ucfirst($sub->status)) }}</span>
                            @else
                                <span class="text-xs text-[#7c8db5]">—</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.tenants.show', $tenant) }}" class="gz-btn gz-btn-outline text-xs py-1.5 px-3">{{ __('View') }}</a>
                                <a href="{{ route('admin.tenants.subscription', $tenant) }}" class="gz-btn gz-btn-primary text-xs py-1.5 px-3">{{ __('Subscription') }}</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
      </div>
        <div class="px-6 py-4 border-t border-[#e8ecf3]">{{ $tenants->links() }}</div>
    </div>
</x-admin-layout>
