<x-admin-layout>
    <x-slot name="header">{{ __('Categories') }}</x-slot>
    <x-slot name="subtitle">{{ __('Manage the top-level classification for listings.') }}</x-slot>
    <x-slot name="headerActions">
        <a href="{{ route('admin.categories.create', request()->only('lang')) }}" class="gz-btn gz-btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            {{ __('New Category') }}
        </a>
    </x-slot>

    <div class="gz-widget">
      <div class="gz-table-wrap">
        <table class="w-full gz-table">
            <thead>
                <tr>
                    <th class="text-left">{{ __('Name') }}</th>
                    <th class="text-left">{{ __('Slug') }}</th>
                    <th class="text-left">{{ __('Active') }}</th>
                    <th class="text-left">{{ __('Sort') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $c)
                    <tr>
                        <td class="font-semibold text-[#1e1e2d]">{{ $c->name }}</td>
                        <td class="text-[#7c8db5]">{{ $c->slug }}</td>
                        <td>
                            <span class="gz-badge {{ $c->is_active ? 'bg-[#2bc155]/10 text-[#2bc155]' : 'bg-gray-100 text-[#7c8db5]' }}">{{ $c->is_active ? __('Yes') : __('No') }}</span>
                        </td>
                        <td class="text-[#7c8db5]">{{ $c->sort_order }}</td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.categories.edit', $c) }}" class="gz-btn gz-btn-outline text-xs py-1.5 px-3">{{ __('Edit') }}</a>
                                <form method="post" action="{{ route('admin.categories.destroy', $c) }}" class="inline" onsubmit="return confirm('{{ __('Delete this category?') }}')">
                                    @csrf
                                    @method('delete')
                                    <button class="gz-btn text-xs py-1.5 px-3 text-[#e8604c] border border-[#e8604c]/20 hover:bg-[#e8604c]/5">{{ __('Delete') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-10 text-sm text-[#7c8db5]">{{ __('No categories yet.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
      </div>
        <div class="px-6 py-4 border-t border-[#e8ecf3]">{{ $categories->links() }}</div>
    </div>
</x-admin-layout>
