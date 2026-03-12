<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">{{ __('Properties') }}</h2>
            <p class="mt-1 text-xs text-slate-500">{{ __('Track portfolio assets, assignments, and availability') }}</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto space-y-6 sm:px-6 lg:px-8">
            <x-flash-status />
            
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">{{ __('Properties Management') }}</h1>
                    <p class="mt-2 text-slate-600">{{ __('Track portfolio assets, assignments, and availability') }}</p>
                </div>
                @can('create', App\Models\Property::class)
                    <a href="{{ route('properties.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800 focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        {{ __('Add New Property') }}
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
                            <p class="text-xs text-slate-500">{{ __('Filter properties by various criteria') }}</p>
                        </div>
                    </div>
                    <svg id="filter-chevron" class="w-5 h-5 text-slate-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                
                <div id="filters-content" class="hidden border-t border-slate-200/60 p-6">
                    <form method="get" class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-2">{{ __('Search') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input type="text" name="q" value="{{ $q ?? '' }}" class="block w-full pl-10 pr-3 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-slate-500 focus:border-transparent transition-all" placeholder="{{ __('Name or address...') }}" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">{{ __('Category') }}</label>
                            <select name="category_id" class="block w-full py-2.5 px-3 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-slate-500 focus:border-transparent transition-all">
                                <option value="0">{{ __('All Categories') }}</option>
                                @foreach(($categories ?? []) as $c)
                                    <option value="{{ $c->id }}" @selected(($category_id ?? 0) == $c->id)>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <button class="inline-flex flex-1 items-center justify-center rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 transition-all">{{ __('Apply Filters') }}</button>
                            <a href="{{ route('properties.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 transition-all">{{ __('Clear') }}</a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-slate-200/60 shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-slate-200/70">
                    <thead class="bg-gradient-to-r from-slate-50 to-slate-100/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 w-16">{{ __('Photo') }}</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">{{ __('Name') }}</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">{{ __('Category') }}</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">{{ __('Location') }}</th>
                            @if(! auth()->user()?->agent_id)
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">{{ __('Agent') }}</th>
                            @endif
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-slate-600">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200/60 bg-white">
                        @forelse($properties as $p)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4">
                                    @php
                                        $raw = (is_array($p->photos ?? null) && count($p->photos)) ? ($p->photos[0] ?? null) : null;
                                        $path = is_string($raw) ? $raw : ($raw['path'] ?? null);
                                    @endphp
                                    @if($path)
                                        @php
                                            $norm = (!filter_var($path, FILTER_VALIDATE_URL) && !\Illuminate\Support\Str::startsWith($path, 'storage/')) ? 'storage/'.$path : $path;
                                            $src = filter_var($norm, FILTER_VALIDATE_URL) ? $norm : asset($norm);
                                        @endphp
                                        <img src="{{ $src }}" class="h-12 w-12 rounded object-cover" />
                                    @else
                                        <div class="h-12 w-12 rounded bg-slate-100"></div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-900">{{ $p->name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-1 text-xs font-medium text-blue-700">
                                        {{ $p->category?->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    @php
                                        $relCity = $p->relationLoaded('city') ? $p->getRelation('city') : null;
                                        $relState = $p->relationLoaded('state') ? $p->getRelation('state') : null;
                                        $relCountry = $p->relationLoaded('country') ? $p->getRelation('country') : null;
                                    @endphp
                                    @if($relCity || $relState || $relCountry)
                                        {{ $relCity?->name_en ?? $p->city }}
                                        @if($relState || $p->state)
                                            {{ $relState?->name_en ? ' - '.$relState->name_en : ($p->state ? ' - '.$p->state : '') }}
                                        @endif
                                        @if($relCountry)
                                            {{ ' ('.$relCountry->name_en.')' }}
                                        @endif
                                    @else
                                        {{ $p->city }} {{ $p->state }}
                                    @endif
                                </td>
                                @if(! auth()->user()?->agent_id)
                                    <td class="px-6 py-4">
                                        @php
                                            $assignedAgents = $p->agents->isNotEmpty() ? $p->agents : collect($p->agent ? [$p->agent] : []);
                                        @endphp
                                        <div class="flex flex-wrap gap-1">
                                            @forelse($assignedAgents as $agent)
                                                @if(is_object($agent) && isset($agent->name))
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                                        {{ $agent->name }}
                                                    </span>
                                                @endif
                                            @empty
                                                <span class="text-xs text-slate-400">{{ __('—') }}</span>
                                            @endforelse
                                        </div>
                                    </td>
                                @endif
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center gap-2">
                                        @can('update', $p)
                                            <a href="{{ route('properties.edit', $p) }}" class="inline-flex items-center rounded-lg px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors">{{ __('Edit') }}</a>
                                        @endcan
                                        @can('delete', $p)
                                            <form method="post" action="{{ route('properties.destroy', $p) }}" class="inline" onsubmit="return confirm('{{ __('Delete this property?') }}')">
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
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 21V9l9-6 9 6v12M9 21V9h6v12" />
                                            </svg>
                                        </div>
                                        <h3 class="mt-4 text-lg font-semibold text-slate-900">{{ __('No properties yet') }}</h3>
                                        <p class="mt-2 text-sm text-slate-600 max-w-sm mx-auto">{{ __('Create a property record to group units and assignments.') }}</p>
                                        @can('create', App\Models\Property::class)
                                            <a href="{{ route('properties.create') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                                {{ __('Add Your First Property') }}
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
                {{ $properties->links() }}
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
