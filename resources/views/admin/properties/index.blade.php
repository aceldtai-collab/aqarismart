<x-admin-layout>
    <x-slot name="header">{{ __('Properties') }}</x-slot>
    <x-slot name="subtitle">{{ __('Admin view') }}</x-slot>

    <div class="gz-widget mb-5">
        <div class="p-5">
            <form method="get" class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Search') }}</label>
                    <input type="text" name="q" value="{{ $q ?? '' }}" class="gz-search w-full" placeholder="{{ __('Name or address') }}" />
                </div>
                <button class="gz-btn gz-btn-primary">{{ __('Filter') }}</button>
                <a href="{{ route('admin.properties.index') }}" class="gz-btn gz-btn-outline">{{ __('Clear') }}</a>
            </form>
        </div>
    </div>

    <div class="gz-widget">
      <div class="gz-table-wrap">
        <table class="w-full gz-table">
            <thead>
                <tr>
                    <th class="text-left">{{ __('Name') }}</th>
                    <th class="text-left">{{ __('Tenant') }}</th>
                    <th class="text-left">{{ __('Agent') }}</th>
                    <th class="text-left">{{ __('Category') }}</th>
                    <th class="text-left">{{ __('City') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($properties as $p)
                    <tr>
                        <td class="font-semibold text-[#1e1e2d]">{{ $p->name }}</td>
                        <td class="text-[#7c8db5]">{{ optional($p->tenant)->name }}</td>
                        <td class="text-[#7c8db5]">{{ optional($p->agent)->name }}</td>
                        <td><span class="gz-badge bg-[#5b73e8]/10 text-[#5b73e8]">{{ optional($p->category)->name }}</span></td>
                        <td class="text-[#7c8db5]">{{ $p->city }}</td>
                    </tr>
                @empty
                    <tr><td class="text-center py-10 text-sm text-[#7c8db5]" colspan="5">{{ __('No properties found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
      </div>
        <div class="px-6 py-4 border-t border-[#e8ecf3]">{{ $properties->links() }}</div>
    </div>
</x-admin-layout>
