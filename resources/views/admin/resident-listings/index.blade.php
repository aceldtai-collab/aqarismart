<x-admin-layout>
    <x-slot name="header">{{ __('Resident Listings') }}</x-slot>
    <x-slot name="subtitle">{{ __('Review and moderate resident-posted property listings.') }}</x-slot>

    <div class="mb-4 flex flex-wrap items-center gap-3">
        <form method="get" class="flex items-center gap-2 flex-1 min-w-[200px] max-w-md">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Search code or title...') }}" class="gz-search flex-1" />
            <button class="gz-btn gz-btn-primary py-2">{{ __('Search') }}</button>
        </form>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('admin.resident-listings.index') }}" class="gz-btn gz-btn-outline text-xs py-1.5 {{ !request('status') && !request('ad_status') ? 'bg-slate-100' : '' }}">{{ __('All') }}</a>
            <a href="{{ route('admin.resident-listings.index', ['status' => 'pending']) }}" class="gz-btn gz-btn-outline text-xs py-1.5 {{ request('status') === 'pending' ? 'bg-slate-100' : '' }}">{{ __('Pending') }}</a>
            <a href="{{ route('admin.resident-listings.index', ['status' => 'active']) }}" class="gz-btn gz-btn-outline text-xs py-1.5 {{ request('status') === 'active' ? 'bg-slate-100' : '' }}">{{ __('Active') }}</a>
            <a href="{{ route('admin.resident-listings.index', ['status' => 'rejected']) }}" class="gz-btn gz-btn-outline text-xs py-1.5 {{ request('status') === 'rejected' ? 'bg-slate-100' : '' }}">{{ __('Rejected') }}</a>
            <a href="{{ route('admin.resident-listings.index', ['ad_status' => 'expired']) }}" class="gz-btn gz-btn-outline text-xs py-1.5 {{ request('ad_status') === 'expired' ? 'bg-slate-100' : '' }}">{{ __('Expired') }}</a>
        </div>
    </div>

    <div class="gz-widget">
      <div class="gz-table-wrap">
        <table class="w-full gz-table">
            <thead>
                <tr>
                    <th class="text-left">{{ __('Code') }}</th>
                    <th class="text-left">{{ __('Title') }}</th>
                    <th class="text-left">{{ __('Owner') }}</th>
                    <th class="text-left">{{ __('Type') }}</th>
                    <th class="text-left">{{ __('Price') }}</th>
                    <th class="text-left">{{ __('Status') }}</th>
                    <th class="text-left">{{ __('Ad Status') }}</th>
                    <th class="text-left">{{ __('Payment') }}</th>
                    <th class="text-left">{{ __('Expires') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($listings as $listing)
                    <tr>
                        <td class="font-mono text-xs text-[#7c8db5]">{{ $listing->code }}</td>
                        <td class="font-semibold text-[#1e1e2d] max-w-[200px] truncate">{{ $listing->translated_title ?? '—' }}</td>
                        <td class="text-sm text-[#7c8db5]">{{ $listing->user->name ?? '—' }}</td>
                        <td>
                            <span class="gz-badge {{ $listing->listing_type === 'sale' ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700' }}">
                                {{ $listing->listing_type === 'sale' ? __('Sale') : __('Rent') }}
                            </span>
                        </td>
                        <td class="font-semibold text-[#1e1e2d]">{{ number_format($listing->price, 0) }} {{ $listing->currency }}</td>
                        <td>
                            @php
                                $statusColors = [
                                    'active' => 'bg-[#2bc155]/10 text-[#2bc155]',
                                    'pending' => 'bg-amber-50 text-amber-700',
                                    'rejected' => 'bg-red-50 text-red-600',
                                    'suspended' => 'bg-gray-100 text-gray-600',
                                ];
                            @endphp
                            <span class="gz-badge {{ $statusColors[$listing->status] ?? 'bg-gray-100 text-gray-500' }}">{{ ucfirst($listing->status ?? 'unknown') }}</span>
                        </td>
                        <td>
                            @php
                                $adColors = [
                                    'active' => 'bg-[#2bc155]/10 text-[#2bc155]',
                                    'pending' => 'bg-amber-50 text-amber-700',
                                    'expired' => 'bg-red-50 text-red-600',
                                ];
                            @endphp
                            <span class="gz-badge {{ $adColors[$listing->ad_status] ?? 'bg-gray-100 text-gray-500' }}">{{ ucfirst($listing->ad_status ?? '—') }}</span>
                        </td>
                        <td>
                            <span class="gz-badge {{ $listing->payment_status === 'paid' ? 'bg-[#2bc155]/10 text-[#2bc155]' : 'bg-amber-50 text-amber-700' }}">
                                {{ ucfirst($listing->payment_status ?? 'pending') }}
                            </span>
                        </td>
                        <td class="text-xs text-[#7c8db5]">{{ $listing->ad_expires_at ? $listing->ad_expires_at->format('M d, Y') : '—' }}</td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.resident-listings.show', $listing) }}" class="gz-btn gz-btn-outline text-xs py-1.5 px-3">{{ __('View') }}</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-10 text-sm text-[#7c8db5]">{{ __('No resident listings found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
      </div>
      <div class="px-6 py-4 border-t border-[#e8ecf3]">{{ $listings->links() }}</div>
    </div>
</x-admin-layout>
