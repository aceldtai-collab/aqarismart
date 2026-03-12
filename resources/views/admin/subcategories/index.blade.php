<x-admin-layout>
    <x-slot name="header">{{ __('Subcategories') }}</x-slot>
    <x-slot name="subtitle">{{ __('Fine-grained listing types within each category.') }}</x-slot>
    <x-slot name="headerActions">
        <a href="{{ route('admin.subcategories.create', request()->only('lang')) }}" class="gz-btn gz-btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            {{ __('New Subcategory') }}
        </a>
    </x-slot>

    <div class="gz-widget mb-5">
        <div class="p-5">
            <form method="get" class="flex items-end gap-3">
                <div class="w-52">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Category') }}</label>
                    <select name="category_id" class="gz-search w-full">
                        <option value="">{{ __('All') }}</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected($categoryId == $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="gz-btn gz-btn-outline">{{ __('Filter') }}</button>
            </form>
        </div>
    </div>

    <div class="gz-widget">
      <div class="gz-table-wrap">
        <table class="w-full gz-table">
            <thead>
                <tr>
                    <th class="text-left">{{ __('Name') }}</th>
                    <th class="text-left">{{ __('Slug') }}</th>
                    <th class="text-left">{{ __('Category') }}</th>
                    <th class="text-left">{{ __('Active') }}</th>
                    <th class="text-left">{{ __('Sort') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($subcategories as $s)
                    <tr>
                        <td class="font-semibold text-[#1e1e2d]">{{ $s->name }}</td>
                        <td class="text-[#7c8db5]">{{ $s->slug }}</td>
                        <td><span class="gz-badge bg-[#5b73e8]/10 text-[#5b73e8]">{{ $s->category?->name }}</span></td>
                        <td>
                            <span class="gz-badge {{ $s->is_active ? 'bg-[#2bc155]/10 text-[#2bc155]' : 'bg-gray-100 text-[#7c8db5]' }}">{{ $s->is_active ? __('Yes') : __('No') }}</span>
                        </td>
                        <td class="text-[#7c8db5]">{{ $s->sort_order }}</td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.subcategories.edit', $s) }}" class="gz-btn gz-btn-outline text-xs py-1.5 px-3">{{ __('Edit') }}</a>
                                <form method="post" action="{{ route('admin.subcategories.destroy', $s) }}" class="inline" onsubmit="return confirm('{{ __('Delete this subcategory?') }}')">
                                    @csrf
                                    @method('delete')
                                    <button class="gz-btn text-xs py-1.5 px-3 text-[#e8604c] border border-[#e8604c]/20 hover:bg-[#e8604c]/5">{{ __('Delete') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-10 text-sm text-[#7c8db5]">{{ __('No subcategories yet.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
      </div>
        <div class="px-6 py-4 border-t border-[#e8ecf3]">{{ $subcategories->links() }}</div>
    </div>
</x-admin-layout>
