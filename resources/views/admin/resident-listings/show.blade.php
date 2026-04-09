<x-admin-layout>
    <x-slot name="header">{{ __('Listing') }}: {{ $residentListing->code }}</x-slot>
    <x-slot name="headerActions">
        <a href="{{ route('admin.resident-listings.edit', $residentListing) }}" class="gz-btn gz-btn-outline">{{ __('Edit') }}</a>
        <a href="{{ route('admin.resident-listings.index') }}" class="gz-btn gz-btn-outline">{{ __('Back to List') }}</a>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Info --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Title & Description --}}
            <div class="gz-widget">
                <div class="p-6 space-y-4">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Title (EN)') }}</div>
                        <div class="text-[#1e1e2d] font-medium">{{ $residentListing->title['en'] ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Title (AR)') }}</div>
                        <div class="text-[#1e1e2d] font-medium" dir="rtl">{{ $residentListing->title['ar'] ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Description (EN)') }}</div>
                        <div class="text-sm text-[#1e1e2d] whitespace-pre-line">{{ $residentListing->description['en'] ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Description (AR)') }}</div>
                        <div class="text-sm text-[#1e1e2d] whitespace-pre-line" dir="rtl">{{ $residentListing->description['ar'] ?? '—' }}</div>
                    </div>
                </div>
            </div>

            {{-- Property Details --}}
            <div class="gz-widget">
                <div class="p-6">
                    <h3 class="text-sm font-semibold text-[#1e1e2d] mb-4">{{ __('Property Details') }}</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        <div>
                            <div class="text-xs text-[#7c8db5] uppercase tracking-wider">{{ __('Type') }}</div>
                            <div class="text-sm font-medium text-[#1e1e2d] mt-1">{{ $residentListing->subcategory->name ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-[#7c8db5] uppercase tracking-wider">{{ __('Listing Type') }}</div>
                            <div class="text-sm font-medium text-[#1e1e2d] mt-1">{{ ucfirst($residentListing->listing_type ?? '—') }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-[#7c8db5] uppercase tracking-wider">{{ __('Price') }}</div>
                            <div class="text-sm font-semibold text-[#1e1e2d] mt-1">{{ number_format($residentListing->price, 0) }} {{ $residentListing->currency }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-[#7c8db5] uppercase tracking-wider">{{ __('Bedrooms') }}</div>
                            <div class="text-sm font-medium text-[#1e1e2d] mt-1">{{ $residentListing->bedrooms ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-[#7c8db5] uppercase tracking-wider">{{ __('Bathrooms') }}</div>
                            <div class="text-sm font-medium text-[#1e1e2d] mt-1">{{ $residentListing->bathrooms ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-[#7c8db5] uppercase tracking-wider">{{ __('Area') }}</div>
                            <div class="text-sm font-medium text-[#1e1e2d] mt-1">{{ $residentListing->area_m2 ? number_format($residentListing->area_m2, 0) . ' m²' : '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-[#7c8db5] uppercase tracking-wider">{{ __('City') }}</div>
                            <div class="text-sm font-medium text-[#1e1e2d] mt-1">{{ $residentListing->city->name_en ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-[#7c8db5] uppercase tracking-wider">{{ __('Location') }}</div>
                            <div class="text-sm font-medium text-[#1e1e2d] mt-1">{{ $residentListing->location ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Photos --}}
            @if($residentListing->photos && count($residentListing->photos) > 0)
                <div class="gz-widget">
                    <div class="p-6">
                        <h3 class="text-sm font-semibold text-[#1e1e2d] mb-4">{{ __('Photos') }} ({{ count($residentListing->photos) }})</h3>
                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                            @foreach($residentListing->photos as $photo)
                                <div class="aspect-square rounded-lg overflow-hidden bg-gray-100">
                                    <img src="{{ $photo }}" alt="" class="w-full h-full object-cover">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Status & Actions --}}
            <div class="gz-widget">
                <div class="p-6 space-y-4">
                    <h3 class="text-sm font-semibold text-[#1e1e2d]">{{ __('Status & Moderation') }}</h3>

                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-[#7c8db5] uppercase tracking-wider">{{ __('Status') }}</span>
                            @php
                                $statusColors = [
                                    'active' => 'bg-[#2bc155]/10 text-[#2bc155]',
                                    'pending' => 'bg-amber-50 text-amber-700',
                                    'rejected' => 'bg-red-50 text-red-600',
                                    'suspended' => 'bg-gray-100 text-gray-600',
                                ];
                            @endphp
                            <span class="gz-badge {{ $statusColors[$residentListing->status] ?? 'bg-gray-100 text-gray-500' }}">{{ ucfirst($residentListing->status ?? 'unknown') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-[#7c8db5] uppercase tracking-wider">{{ __('Ad Status') }}</span>
                            @php
                                $adColors = [
                                    'active' => 'bg-[#2bc155]/10 text-[#2bc155]',
                                    'pending' => 'bg-amber-50 text-amber-700',
                                    'expired' => 'bg-red-50 text-red-600',
                                ];
                            @endphp
                            <span class="gz-badge {{ $adColors[$residentListing->ad_status] ?? 'bg-gray-100 text-gray-500' }}">{{ ucfirst($residentListing->ad_status ?? '—') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-[#7c8db5] uppercase tracking-wider">{{ __('Payment') }}</span>
                            <span class="gz-badge {{ $residentListing->payment_status === 'paid' ? 'bg-[#2bc155]/10 text-[#2bc155]' : 'bg-amber-50 text-amber-700' }}">
                                {{ ucfirst($residentListing->payment_status ?? 'pending') }}
                            </span>
                        </div>
                    </div>

                    @if($residentListing->status === 'pending' || $residentListing->status === 'rejected')
                        <div class="pt-3 border-t border-[#e8ecf3] space-y-2">
                            <form method="post" action="{{ route('admin.resident-listings.approve', $residentListing) }}">
                                @csrf
                                <button class="gz-btn gz-btn-primary w-full justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                    {{ __('Approve') }}
                                </button>
                            </form>
                            <form method="post" action="{{ route('admin.resident-listings.reject', $residentListing) }}" x-data="{ showNotes: false }">
                                @csrf
                                <button type="button" @click="showNotes = !showNotes" class="gz-btn w-full justify-center text-[#e8604c] border border-[#e8604c]/20 hover:bg-[#e8604c]/5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    {{ __('Reject') }}
                                </button>
                                <div x-show="showNotes" x-cloak class="mt-2 space-y-2">
                                    <textarea name="moderation_notes" rows="3" class="gz-search w-full text-sm" placeholder="{{ __('Reason for rejection (optional)...') }}"></textarea>
                                    <button type="submit" class="gz-btn text-xs py-1.5 px-3 text-[#e8604c] border border-[#e8604c]/20 hover:bg-[#e8604c]/5 w-full justify-center">{{ __('Confirm Rejection') }}</button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Owner Info --}}
            <div class="gz-widget">
                <div class="p-6 space-y-3">
                    <h3 class="text-sm font-semibold text-[#1e1e2d]">{{ __('Owner') }}</h3>
                    @if($residentListing->user)
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-sm font-semibold text-slate-600">
                                {{ strtoupper(substr($residentListing->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="text-sm font-medium text-[#1e1e2d]">{{ $residentListing->user->name }}</div>
                                <div class="text-xs text-[#7c8db5]">{{ $residentListing->user->email }}</div>
                                @if($residentListing->user->phone)
                                    <div class="text-xs text-[#7c8db5]">{{ $residentListing->user->phone_country_code }}{{ $residentListing->user->phone }}</div>
                                @endif
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-[#7c8db5]">{{ __('No owner info') }}</p>
                    @endif
                </div>
            </div>

            {{-- Ad Duration & Payment --}}
            <div class="gz-widget">
                <div class="p-6 space-y-3">
                    <h3 class="text-sm font-semibold text-[#1e1e2d]">{{ __('Ad & Payment') }}</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-[#7c8db5]">{{ __('Duration') }}</span>
                            <span class="text-[#1e1e2d]">{{ $residentListing->adDuration->name_en ?? '—' }} ({{ $residentListing->adDuration->days ?? 0 }} {{ __('days') }})</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#7c8db5]">{{ __('Duration Price') }}</span>
                            <span class="text-[#1e1e2d]">{{ $residentListing->adDuration ? $residentListing->adDuration->formatted_price : '—' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#7c8db5]">{{ __('Amount Paid') }}</span>
                            <span class="text-[#1e1e2d]">{{ $residentListing->amount_paid ? number_format($residentListing->amount_paid, 0) . ' ' . $residentListing->currency : '—' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#7c8db5]">{{ __('Paid At') }}</span>
                            <span class="text-[#1e1e2d]">{{ $residentListing->paid_at ? $residentListing->paid_at->format('M d, Y H:i') : '—' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#7c8db5]">{{ __('Ad Started') }}</span>
                            <span class="text-[#1e1e2d]">{{ $residentListing->ad_started_at ? $residentListing->ad_started_at->format('M d, Y') : '—' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#7c8db5]">{{ __('Ad Expires') }}</span>
                            <span class="text-[#1e1e2d]">{{ $residentListing->ad_expires_at ? $residentListing->ad_expires_at->format('M d, Y') : '—' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Moderation History --}}
            @if($residentListing->moderated_at)
                <div class="gz-widget">
                    <div class="p-6 space-y-3">
                        <h3 class="text-sm font-semibold text-[#1e1e2d]">{{ __('Moderation') }}</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-[#7c8db5]">{{ __('Moderated By') }}</span>
                                <span class="text-[#1e1e2d]">{{ $residentListing->moderator->name ?? '—' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-[#7c8db5]">{{ __('Moderated At') }}</span>
                                <span class="text-[#1e1e2d]">{{ $residentListing->moderated_at->format('M d, Y H:i') }}</span>
                            </div>
                            @if($residentListing->moderation_notes)
                                <div>
                                    <div class="text-[#7c8db5] mb-1">{{ __('Notes') }}</div>
                                    <div class="text-[#1e1e2d] bg-slate-50 p-3 rounded-lg text-sm">{{ $residentListing->moderation_notes }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- Danger Zone --}}
            <div class="gz-widget border-red-200">
                <div class="p-6">
                    <h3 class="text-sm font-semibold text-red-600 mb-3">{{ __('Danger Zone') }}</h3>
                    <form method="post" action="{{ route('admin.resident-listings.destroy', $residentListing) }}" onsubmit="return confirm('{{ __('Are you sure? This will permanently delete this listing.') }}')">
                        @csrf
                        @method('delete')
                        <button class="gz-btn text-xs py-1.5 px-3 text-[#e8604c] border border-[#e8604c]/20 hover:bg-[#e8604c]/5 w-full justify-center">{{ __('Delete Listing') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
