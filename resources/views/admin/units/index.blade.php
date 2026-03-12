<x-admin-layout>
    <x-slot name="header">{{ __('Units') }}</x-slot>
    <x-slot name="subtitle">{{ __('Admin view') }}</x-slot>

    <div class="gz-widget mb-5">
        <div class="p-5">
            <form method="get" class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[180px]">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Search') }}</label>
                    <input type="text" name="q" value="{{ $q ?? '' }}" class="gz-search w-full" placeholder="{{ __('Code, title or property') }}" />
                </div>
                <div class="w-40">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Listing Type') }}</label>
                    <select name="listing_type" class="gz-search w-full">
                        <option value="">-- {{ __('All') }} --</option>
                        @foreach(\App\Models\Unit::listingTypeLabels() as $value => $label)
                            <option value="{{ $value }}" {{ ($listing_type ?? request('listing_type')) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-36">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Status') }}</label>
                    <select name="status" class="gz-search w-full">
                        <option value="">-- {{ __('All') }} --</option>
                        <option value="vacant" {{ ($status ?? request('status')) === 'vacant' ? 'selected' : '' }}>{{ __('Vacant') }}</option>
                        <option value="occupied" {{ ($status ?? request('status')) === 'occupied' ? 'selected' : '' }}>{{ __('Occupied') }}</option>
                    </select>
                </div>
                <button class="gz-btn gz-btn-primary">{{ __('Filter') }}</button>
                <a href="{{ route('admin.units.index') }}" class="gz-btn gz-btn-outline">{{ __('Clear') }}</a>
            </form>
        </div>
    </div>

    <div class="gz-widget">
      <div class="gz-table-wrap">
        <table class="w-full gz-table">
            <thead>
                <tr>
                    <th class="text-left">{{ __('Code') }}</th>
                    <th class="text-left">{{ __('Title') }}</th>
                    <th class="text-left">{{ __('Type') }}</th>
                    <th class="text-left">{{ __('Status') }}</th>
                    <th class="text-left">{{ __('Tenant') }}</th>
                    <th class="text-left">{{ __('Property') }}</th>
                    <th class="text-left">{{ __('Agent') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($units as $u)
                    <tr>
                        <td class="font-semibold text-[#1e1e2d]">{{ $u->code }}</td>
                        <td class="text-[#1e1e2d]">{{ $u->translated_title ?? (is_array($u->title) ? ($u->title[app()->getLocale()] ?? ($u->title['en'] ?? '')) : $u->title) }}</td>
                        <td><span class="gz-badge bg-[#5b73e8]/10 text-[#5b73e8]">{{ \App\Models\Unit::listingTypeLabels()[$u->listing_type] ?? $u->listing_type }}</span></td>
                        <td>
                            <span class="gz-badge {{ $u->status === 'vacant' ? 'bg-[#ffab2d]/10 text-[#ffab2d]' : 'bg-[#2bc155]/10 text-[#2bc155]' }}">{{ ucfirst($u->status) }}</span>
                        </td>
                        <td class="text-[#7c8db5]">{{ optional($u->tenant)->name }}</td>
                        <td class="text-[#7c8db5]">{{ optional($u->property)->name }}</td>
                        <td class="text-[#7c8db5]">{{ optional($u->agent)->name }}</td>
                    </tr>
                @empty
                    <tr><td class="text-center py-10 text-sm text-[#7c8db5]" colspan="7">{{ __('No units found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
      </div>
        <div class="px-6 py-4 border-t border-[#e8ecf3]">{{ $units->links() }}</div>
    </div>
</x-admin-layout>
