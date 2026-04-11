@php($isAr = app()->getLocale() === 'ar')
<x-admin-layout>
    <x-slot name="header">{{ $isAr ? 'مدد الإعلان' : 'Ad Durations' }}</x-slot>
    <x-slot name="subtitle">{{ $isAr ? 'إدارة الأسعار ومدة إعلانات المالكين.' : 'Manage pricing and duration options for resident listings.' }}</x-slot>
    <x-slot name="headerActions">
        <a href="{{ route('admin.ad-durations.create') }}" class="gz-btn gz-btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            {{ $isAr ? 'مدة جديدة' : 'New Duration' }}
        </a>
    </x-slot>

    <div class="gz-widget">
      <div class="gz-table-wrap">
        <table class="w-full gz-table">
            <thead>
                <tr>
                    <th class="text-left">{{ $isAr ? 'الاسم بالإنجليزية' : 'Name (EN)' }}</th>
                    <th class="text-left">{{ $isAr ? 'الاسم بالعربية' : 'Name (AR)' }}</th>
                    <th class="text-left">{{ $isAr ? 'الأيام' : 'Days' }}</th>
                    <th class="text-left">{{ $isAr ? 'السعر' : 'Price' }}</th>
                    <th class="text-left">{{ $isAr ? 'العملة' : 'Currency' }}</th>
                    <th class="text-left">{{ $isAr ? 'نشط' : 'Active' }}</th>
                    <th class="text-left">{{ $isAr ? 'الترتيب' : 'Sort' }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($durations as $d)
                    <tr>
                        <td class="font-semibold text-[#1e1e2d]">{{ $d->name_en }}</td>
                        <td class="text-[#1e1e2d]">{{ $d->name_ar }}</td>
                        <td class="text-[#7c8db5]">{{ $d->days }}</td>
                        <td class="font-semibold text-[#1e1e2d]">{{ number_format($d->price, 0) }}</td>
                        <td class="text-[#7c8db5]">{{ $d->currency }}</td>
                        <td>
                            <span class="gz-badge {{ $d->is_active ? 'bg-[#2bc155]/10 text-[#2bc155]' : 'bg-gray-100 text-[#7c8db5]' }}">{{ $isAr ? ($d->is_active ? 'نعم' : 'لا') : ($d->is_active ? 'Yes' : 'No') }}</span>
                        </td>
                        <td class="text-[#7c8db5]">{{ $d->sort_order }}</td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.ad-durations.edit', $d) }}" class="gz-btn gz-btn-outline text-xs py-1.5 px-3">{{ $isAr ? 'تعديل' : 'Edit' }}</a>
                                <form method="post" action="{{ route('admin.ad-durations.destroy', $d) }}" class="inline" onsubmit="return confirm('{{ $isAr ? 'حذف هذه المدة؟' : 'Delete this duration?' }}')">
                                    @csrf
                                    @method('delete')
                                    <button class="gz-btn text-xs py-1.5 px-3 text-[#e8604c] border border-[#e8604c]/20 hover:bg-[#e8604c]/5">{{ $isAr ? 'حذف' : 'Delete' }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-10 text-sm text-[#7c8db5]">{{ $isAr ? 'لا توجد مدد إعلانية بعد.' : 'No ad durations yet.' }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
      </div>
    </div>
</x-admin-layout>
