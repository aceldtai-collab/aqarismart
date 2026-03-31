<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">{{ __('Leases') }}</h2>
            <p class="mt-1 text-xs text-slate-500">{{ __('Track active contracts, renewals, and rent roll.') }}</p>
        </div>
    </x-slot>

    @php
        $statusPalette = config('status.palette', [
            'success' => ['text' => 'text-emerald-700', 'bg' => 'bg-emerald-50'],
            'warning' => ['text' => 'text-amber-700', 'bg' => 'bg-amber-50'],
            'danger' => ['text' => 'text-rose-700', 'bg' => 'bg-rose-50'],
            'info' => ['text' => 'text-sky-700', 'bg' => 'bg-sky-50'],
        ]);
        $statusTone = [
            'active' => 'success',
            'pending' => 'warning',
            'ended' => 'info',
            'cancelled' => 'danger',
        ];
    @endphp

    <div class="py-8">
        <div class="max-w-7xl mx-auto space-y-6 sm:px-6 lg:px-8">
            <x-flash-status />
            
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">{{ __('Leases Management') }}</h1>
                    <p class="mt-2 text-slate-600">{{ __('Track active contracts, renewals, and rent roll') }}</p>
                </div>
                @can('create', App\Models\Lease::class)
                    <a href="{{ route('leases.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-50 text-white text-sm font-semibold rounded-lg hover:bg-slate-800 focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        {{ __('Add New Lease') }}
                    </a>
                @endcan
            </div>
            
            <div class="bg-white rounded-xl border border-slate-200/60 shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-slate-200/70">
                    <thead class="bg-gradient-to-r from-slate-50 to-slate-100/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">{{ __('Property / Unit') }}</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">{{ __('Start') }}</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">{{ __('End') }}</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">{{ __('Rent') }}</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">{{ __('Status') }}</th>
                            @if(! auth()->user()?->agent_id)
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">{{ __('Agent') }}</th>
                            @endif
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-slate-600">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200/60 bg-white">
                        @forelse($leases as $l)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-900">{{ $l->property?->name ?? __('Standalone') }}</div>
                                    <div class="text-sm text-slate-500">{{ $l->unit->code }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $l->start_date->toDateString() }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $l->end_date?->toDateString() ?: '—' }}</td>
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-slate-900">${{ number_format($l->rent_cents / 100, 2) }}</span>
                                    <div class="text-xs text-slate-500">{{ __('per year') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $tone = $statusTone[$l->status] ?? 'info';
                                        $toneStyles = $statusPalette[$tone];
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $toneStyles['bg'] }} {{ $toneStyles['text'] }}">
                                        {{ ucfirst($l->status) }}
                                    </span>
                                </td>
                                @if(! auth()->user()?->agent_id)
                                    <td class="px-6 py-4">
                                        @php
                                            $ag = $l->unit?->agent ?? $l->property?->agent;
                                        @endphp
                                        @if($ag)
                                            <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-1 text-xs font-medium text-blue-700">{{ $ag->name }}</span>
                                        @else
                                            <span class="text-xs text-slate-400">—</span>
                                        @endif
                                    </td>
                                @endif
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center gap-2 justify-end">
                                        @can('update', $l)
                                            <a href="{{ route('leases.edit', $l) }}" class="inline-flex items-center rounded-lg px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors">{{ __('Edit') }}</a>
                                        @endcan
                                        @can('delete', $l)
                                            <form method="post" action="{{ route('leases.destroy', $l) }}" class="inline" onsubmit="return confirm('{{ __('Delete this lease?') }}')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="inline-flex items-center rounded-lg px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors">{{ __('Delete') }}</button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-16" colspan="{{ auth()->user()?->agent_id ? 5 : 6 }}">
                                    <div class="text-center">
                                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-slate-100 to-slate-200 text-slate-500">
                                            <svg viewBox="0 0 24 24" class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h7.5m-7.5 4.5h7.5m-7.5 4.5h4.5M6 3.75h8.25L18 7.5V20.25a.75.75 0 01-.75.75H6.75a.75.75 0 01-.75-.75z" />
                                            </svg>
                                        </div>
                                        <h3 class="mt-4 text-lg font-semibold text-slate-900">{{ __('No leases yet') }}</h3>
                                        <p class="mt-2 text-sm text-slate-600 max-w-sm mx-auto">{{ __('Create a lease to track occupancy and rent.') }}</p>
                                        @can('create', App\Models\Lease::class)
                                            <a href="{{ route('leases.create') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-gray-50 text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m-6 0H6"></path>
                                                </svg>
                                                {{ __('Add Your First Lease') }}
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $leases->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
