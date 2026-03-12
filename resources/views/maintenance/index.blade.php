<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">{{ __('Maintenance') }}</h2>
            <p class="mt-1 text-xs text-slate-500">{{ __('Track requests, assignments, and resolution status.') }}</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto space-y-6 sm:px-6 lg:px-8">
            <x-flash-status />
            
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">{{ __('Maintenance Management') }}</h1>
                    <p class="mt-2 text-slate-600">{{ __('Track requests, assignments, and resolution status') }}</p>
                </div>
                @can('create', App\Models\MaintenanceRequest::class)
                    <a href="{{ route('maintenance.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800 focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        {{ __('Add New Request') }}
                    </a>
                @endcan
            </div>
            
            <!-- Collapsible Filters Section -->
            <div class="bg-white rounded-xl border border-slate-200/60 shadow-sm overflow-hidden">
                <button 
                    onclick="toggleFilters()" 
                    class="w-full flex items-center justify-between p-4 text-left hover:bg-slate-50 transition-colors"
                >
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-slate-100 rounded-lg">
                            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900">{{ __('Advanced Filters') }}</h3>
                            <p class="text-xs text-slate-500">{{ __('Filter maintenance requests by various criteria') }}</p>
                        </div>
                    </div>
                    <svg id="filter-chevron" class="w-5 h-5 text-slate-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                
                <div id="filters-content" class="hidden border-t border-slate-200/60 p-6">
                    <form method="get" class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">{{ __('Status') }}</label>
                            <select name="status" class="block w-full py-2.5 px-3 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-slate-500 focus:border-transparent transition-all">
                                <option value="">{{ __('All Status') }}</option>
                                @foreach(['new' => 'New', 'open' => 'Open', 'in_progress' => 'In progress', 'closed' => 'Closed'] as $key => $label)
                                    <option value="{{ $key }}" {{ ($status ?? request('status')) === $key ? 'selected' : '' }}>{{ __($label) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">{{ __('Time Period') }}</label>
                            <select name="since" class="block w-full py-2.5 px-3 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-slate-500 focus:border-transparent transition-all">
                                <option value="0">{{ __('Any time') }}</option>
                                <option value="7" {{ (int)($since ?? request('since')) === 7 ? 'selected' : '' }}>{{ __('Last 7 days') }}</option>
                                <option value="14" {{ (int)($since ?? request('since')) === 14 ? 'selected' : '' }}>{{ __('Last 14 days') }}</option>
                                <option value="30" {{ (int)($since ?? request('since')) === 30 ? 'selected' : '' }}>{{ __('Last 30 days') }}</option>
                            </select>
                        </div>
                        <div class="md:col-span-2 flex items-end gap-2">
                            <button class="inline-flex flex-1 items-center justify-center rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 transition-all">{{ __('Apply Filters') }}</button>
                            <a href="{{ route('maintenance.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 transition-all">{{ __('Clear') }}</a>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="bg-white rounded-xl border border-slate-200/60 shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-slate-200/70">
                    <thead class="bg-gradient-to-r from-slate-50 to-slate-100/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">{{ __('Title') }}</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">{{ __('Property / Unit') }}</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">{{ __('Priority') }}</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">{{ __('Status') }}</th>
                            @if(! auth()->user()?->agent_id)
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">{{ __('Agent') }}</th>
                            @endif
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">{{ __('Created') }}</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-slate-600">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200/60 bg-white">
                        @forelse($requests as $r)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-900">{{ $r->title }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-slate-600">
                                        {{ $r->property?->name ?? __('Standalone') }}
                                        @if($r->unit) — {{ $r->unit->code }} @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $priorityColors = [
                                            'low' => 'bg-blue-100 text-blue-700',
                                            'medium' => 'bg-amber-100 text-amber-700',
                                            'high' => 'bg-red-100 text-red-700',
                                            'urgent' => 'bg-red-200 text-red-800'
                                        ];
                                        $priorityColor = $priorityColors[$r->priority] ?? 'bg-slate-100 text-slate-700';
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $priorityColor }}">
                                        {{ ucfirst($r->priority) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusColors = [
                                            'new' => 'bg-blue-100 text-blue-700',
                                            'open' => 'bg-amber-100 text-amber-700',
                                            'in_progress' => 'bg-purple-100 text-purple-700',
                                            'closed' => 'bg-emerald-100 text-emerald-700'
                                        ];
                                        $statusColor = $statusColors[$r->status] ?? 'bg-slate-100 text-slate-700';
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $statusColor }}">
                                        {{ ucfirst(str_replace('_', ' ', $r->status)) }}
                                    </span>
                                </td>
                                @if(! auth()->user()?->agent_id)
                                    <td class="px-6 py-4">
                                        @php
                                            $ag = $r->unit?->agent ?? $r->property?->agent;
                                        @endphp
                                        @if($ag)
                                            <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-1 text-xs font-medium text-blue-700">{{ $ag->name }}</span>
                                        @else
                                            <span class="text-xs text-slate-400">—</span>
                                        @endif
                                    </td>
                                @endif
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $r->created_at->diffForHumans() }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center gap-2 justify-end">
                                        @can('update', $r)
                                            <a href="{{ route('maintenance.edit', $r) }}" class="inline-flex items-center rounded-lg px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors">{{ __('Edit') }}</a>
                                        @endcan
                                        @can('delete', $r)
                                            <form method="post" action="{{ route('maintenance.destroy', $r) }}" class="inline" onsubmit="return confirm('{{ __('Delete this request?') }}')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="inline-flex items-center rounded-lg px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors">{{ __('Delete') }}</button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-16" colspan="{{ auth()->user()?->agent_id ? 6 : 7 }}">
                                    <div class="text-center">
                                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-slate-100 to-slate-200 text-slate-500">
                                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z"></path>
                                            </svg>
                                        </div>
                                        <h3 class="mt-4 text-lg font-semibold text-slate-900">{{ __('No maintenance requests yet') }}</h3>
                                        <p class="mt-2 text-sm text-slate-600 max-w-sm mx-auto">{{ __('Track and manage maintenance requests for your properties.') }}</p>
                                        @can('create', App\Models\MaintenanceRequest::class)
                                            <a href="{{ route('maintenance.create') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m-6 0H6"></path>
                                                </svg>
                                                {{ __('Add Your First Request') }}
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
                {{ $requests->links() }}
            </div>
        </div>
    </div>
    
    <script>
        function toggleFilters() {
            const content = document.getElementById('filters-content');
            const chevron = document.getElementById('filter-chevron');
            
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                chevron.style.transform = 'rotate(180deg)';
            } else {
                content.classList.add('hidden');
                chevron.style.transform = 'rotate(0deg)';
            }
        }
    </script>
</x-app-layout>
