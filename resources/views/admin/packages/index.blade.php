<x-admin-layout>
    <x-slot name="header">{{ __('Packages') }}</x-slot>
    <x-slot name="subtitle">{{ __('Manage subscription packages and their entitlements.') }}</x-slot>
    <x-slot name="headerActions">
        <a href="{{ route('admin.packages.create') }}" class="gz-btn gz-btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            {{ __('New Package') }}
        </a>
    </x-slot>

    <div class="gz-widget">
      <div class="gz-table-wrap">
        <table class="w-full gz-table">
            <thead>
                <tr>
                    <th class="text-left">{{ __('Package') }}</th>
                    <th class="text-left">{{ __('Price (Monthly)') }}</th>
                    <th class="text-left">{{ __('Price (Yearly)') }}</th>
                    <th class="text-left">{{ __('Subscribers') }}</th>
                    <th class="text-left">{{ __('Status') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($packages as $package)
                    <tr>
                        <td>
                            <div class="font-semibold text-[#1e1e2d]">{{ $package->name }}</div>
                            <div class="text-xs text-[#7c8db5]">{{ $package->slug }}</div>
                            @if($package->is_default)
                                <span class="gz-badge bg-[#e8604c]/10 text-[#e8604c] mt-1">{{ __('Default') }}</span>
                            @endif
                        </td>
                        <td class="font-medium text-[#1e1e2d]">${{ $package->formattedMonthlyPrice() }}</td>
                        <td class="font-medium text-[#1e1e2d]">${{ $package->formattedYearlyPrice() }}</td>
                        <td>
                            <span class="gz-badge bg-[#5b73e8]/10 text-[#5b73e8]">{{ $package->active_subscriptions_count }}</span>
                        </td>
                        <td>
                            <span class="gz-badge {{ $package->is_active ? 'bg-[#2bc155]/10 text-[#2bc155]' : 'bg-gray-100 text-[#7c8db5]' }}">
                                {{ $package->is_active ? __('Active') : __('Inactive') }}
                            </span>
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.packages.edit', $package) }}" class="gz-btn gz-btn-outline text-xs py-1.5 px-3">{{ __('Edit') }}</a>
                                @if($package->active_subscriptions_count === 0)
                                    <form method="post" action="{{ route('admin.packages.destroy', $package) }}" class="inline" onsubmit="return confirm('{{ __('Delete this package?') }}')">
                                        @csrf
                                        @method('delete')
                                        <button class="gz-btn text-xs py-1.5 px-3 text-[#e8604c] border border-[#e8604c]/20 hover:bg-[#e8604c]/5">{{ __('Delete') }}</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-10">
                            <div class="w-14 h-14 rounded-full bg-[#e8604c]/10 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-7 h-7 text-[#e8604c]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/></svg>
                            </div>
                            <p class="text-sm text-[#7c8db5]">{{ __('No packages yet. Create your first package.') }}</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
      </div>
        <div class="px-6 py-4 border-t border-[#e8ecf3]">{{ $packages->links() }}</div>
    </div>
</x-admin-layout>
