<x-admin-layout>
    <x-slot name="header">{{ __('Add-ons') }}</x-slot>
    <x-slot name="subtitle">{{ __('Manage purchasable extensions that tenants can add to their base package.') }}</x-slot>
    <x-slot name="headerActions">
        <a href="{{ route('admin.addons.create') }}" class="gz-btn gz-btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            {{ __('New Add-on') }}
        </a>
    </x-slot>

    <div class="gz-widget">
      <div class="gz-table-wrap">
        <table class="w-full gz-table">
            <thead>
                <tr>
                    <th class="text-left">{{ __('Add-on') }}</th>
                    <th class="text-left">{{ __('Feature') }}</th>
                    <th class="text-left">{{ __('Grants') }}</th>
                    <th class="text-left">{{ __('Price (Monthly)') }}</th>
                    <th class="text-left">{{ __('Status') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($addons as $addon)
                    <tr>
                        <td>
                            <div class="font-semibold text-[#1e1e2d]">{{ $addon->name }}</div>
                            <div class="text-xs text-[#7c8db5]">{{ $addon->slug }}</div>
                        </td>
                        <td><span class="gz-badge bg-[#5b73e8]/10 text-[#5b73e8] capitalize">{{ $addon->feature }}</span></td>
                        <td><span class="font-semibold text-[#2bc155]">+{{ $addon->qty }}</span></td>
                        <td class="font-medium text-[#1e1e2d]">${{ $addon->formattedMonthlyPrice() }}</td>
                        <td>
                            <span class="gz-badge {{ $addon->is_active ? 'bg-[#2bc155]/10 text-[#2bc155]' : 'bg-gray-100 text-[#7c8db5]' }}">
                                {{ $addon->is_active ? __('Active') : __('Inactive') }}
                            </span>
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.addons.edit', $addon) }}" class="gz-btn gz-btn-outline text-xs py-1.5 px-3">{{ __('Edit') }}</a>
                                <form method="post" action="{{ route('admin.addons.destroy', $addon) }}" class="inline" onsubmit="return confirm('{{ __('Delete this add-on?') }}')">
                                    @csrf
                                    @method('delete')
                                    <button class="gz-btn text-xs py-1.5 px-3 text-[#e8604c] border border-[#e8604c]/20 hover:bg-[#e8604c]/5">{{ __('Delete') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-10">
                            <div class="w-14 h-14 rounded-full bg-[#5b73e8]/10 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-7 h-7 text-[#5b73e8]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 16.875h3.375m0 0h3.375m-3.375 0V13.5m0 3.375v3.375M6 10.5h2.25a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H6A2.25 2.25 0 003.75 6v2.25A2.25 2.25 0 006 10.5z"/></svg>
                            </div>
                            <p class="text-sm text-[#7c8db5]">{{ __('No add-ons yet. Create your first add-on.') }}</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
      </div>
        <div class="px-6 py-4 border-t border-[#e8ecf3]">{{ $addons->links() }}</div>
    </div>
</x-admin-layout>
