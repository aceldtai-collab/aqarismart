<x-tenant-layout>
    @php
        $metrics = $metrics ?? [];
        $leadStats = $leadStats ?? [];
        $maintenanceBreakdown = $maintenanceBreakdown ?? [];
        $leadTrendCollection = collect($leadStats['trend'] ?? []);
        $pipelineTrend = collect($pipelineTrend ?? [])->sortByDesc('date');
        $occupancyTrend = collect($occupancyTrend ?? [])->sortByDesc('date');
        $commissionTrend = collect($commissionTrend ?? [])->sortByDesc('date');
        $maintenanceTrend = collect($maintenanceTrend ?? [])->sortByDesc('date');
        $occupancyRate = min(max($metrics['occupancy_rate'] ?? 0, 0), 100);
        $maintenanceTotal = collect($maintenanceBreakdown)->sum();
        $statusLabels = [
            'new' => __('New'),
            'open' => __('Open'),
            'in_progress' => __('In Progress'),
            'resolved' => __('Resolved'),
            'completed' => __('Completed'),
        ];
        $filterDays = $filterDays ?? 14;
        $filterAgent = $filterAgent ?? null;
        $availableAgents = $availableAgents ?? collect();
        $alerts = collect($alerts ?? []);
        $latestPipeline = $pipelineTrend->first();
        $latestOccupancy = $occupancyTrend->first();
        $latestCommission = $commissionTrend->first();
        $latestMaintenance = $maintenanceTrend->first();
        $exportParams = array_filter([
            'agent_id' => $filterAgent,
            'days' => $filterDays,
        ], fn ($v) => !is_null($v) && $v !== '');
        $statusPalette = config('status.palette', [
            'success' => ['text' => 'text-emerald-700', 'bg' => 'bg-emerald-50', 'dot' => 'bg-emerald-500', 'bar' => 'bg-emerald-500'],
            'warning' => ['text' => 'text-amber-700', 'bg' => 'bg-amber-50', 'dot' => 'bg-amber-500', 'bar' => 'bg-amber-500'],
            'danger' => ['text' => 'text-rose-700', 'bg' => 'bg-rose-50', 'dot' => 'bg-rose-500', 'bar' => 'bg-rose-500'],
            'info' => ['text' => 'text-sky-700', 'bg' => 'bg-sky-50', 'dot' => 'bg-sky-500', 'bar' => 'bg-sky-500'],
        ]);
        $maintenanceOpen = (int) ($metrics['open_maintenance'] ?? 0);
        $maintenanceTone = $maintenanceOpen >= 12 ? 'danger' : ($maintenanceOpen >= 6 ? 'warning' : 'success');
        $leadChangePct = (float) ($leadStats['change_pct'] ?? 0);
        $leadTone = $leadChangePct >= 0 ? 'success' : 'danger';
        $vacantUnits = (int) ($metrics['vacant_units'] ?? 0);
        $vacancyTone = $vacantUnits >= 10 ? 'danger' : ($vacantUnits >= 4 ? 'warning' : 'success');
        $avgOpenDays = (float) ($metrics['avg_open_days'] ?? 0);
        $avgOpenTone = $avgOpenDays >= 7 ? 'danger' : ($avgOpenDays >= 4 ? 'warning' : 'success');
        $latestOccupancyRate = (float) data_get($latestOccupancy, 'occupancy_rate', $occupancyRate);
        $occupancyTone = $latestOccupancyRate >= 90 ? 'success' : ($latestOccupancyRate >= 75 ? 'warning' : 'danger');
        $previousOccupancy = $occupancyTrend->skip(1)->first();
        $occupancyChange = $previousOccupancy
            ? ($latestOccupancyRate - (float) data_get($previousOccupancy, 'occupancy_rate', 0))
            : null;
        $occupancyChangeTone = !is_null($occupancyChange) && $occupancyChange >= 0 ? 'success' : 'danger';
    @endphp
    <x-slot name="header">
        {{ __('Dashboard') }}
    </x-slot>

    <div class="py-4 sm:py-8">
        <div class="max-w-7xl mx-auto space-y-6 sm:px-6 lg:px-8">

            <!-- Filters & Exports -->
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                <form method="get" class="flex flex-wrap items-end gap-3 text-sm">
                    @if($availableAgents->isNotEmpty())
                        <div>
                            <label for="agent_id" class="block text-[11px] font-semibold uppercase tracking-wide text-slate-500 mb-1">{{ __('Agent') }}</label>
                            <select id="agent_id" name="agent_id" class="w-full min-w-[140px] rounded-lg border-slate-300 text-sm" onchange="this.form.submit()">
                                <option value="">{{ __('All Agents') }}</option>
                                @foreach($availableAgents as $agent)
                                    <option value="{{ $agent->id }}" @selected($filterAgent == $agent->id)>{{ $agent->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div>
                        <label for="days_filter" class="block text-[11px] font-semibold uppercase tracking-wide text-slate-500 mb-1">{{ __('Days') }}</label>
                        <select id="days_filter" name="days" class="w-full min-w-[96px] rounded-lg border-slate-300 text-sm" onchange="this.form.submit()">
                            @foreach([14,30,60,90] as $range)
                                <option value="{{ $range }}" @selected($filterDays === $range)>{{ $range }} {{ __('Days') }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
                <div x-data="{ showExports: false }" class="relative">
                    <button @click="showExports = !showExports" class="inline-flex items-center gap-2 rounded-lg bg-slate-100 px-3 py-2 text-xs font-medium text-slate-600 hover:bg-slate-200 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                        {{ __('Exports') }}
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                    </button>
                    <div x-show="showExports" x-cloak @click.outside="showExports = false" x-transition
                         class="absolute ltr:right-0 rtl:left-0 mt-1 w-48 bg-white border border-slate-200 rounded-lg shadow-lg py-1 z-20">
                        <a href="{{ route('reports.export.pipeline.csv', $exportParams) }}" class="block px-3 py-2 text-sm text-slate-600 hover:bg-slate-50">{{ __('Pipeline CSV') }}</a>
                        <a href="{{ route('reports.export.pipeline.pdf', $exportParams) }}" class="block px-3 py-2 text-sm text-slate-600 hover:bg-slate-50">{{ __('Pipeline PDF') }}</a>
                        <a href="{{ route('reports.export.occupancy.csv', $exportParams) }}" class="block px-3 py-2 text-sm text-slate-600 hover:bg-slate-50">{{ __('Occupancy CSV') }}</a>
                        <a href="{{ route('reports.export.occupancy.pdf', $exportParams) }}" class="block px-3 py-2 text-sm text-slate-600 hover:bg-slate-50">{{ __('Occupancy PDF') }}</a>
                    </div>
                </div>
            </div>
            <!-- Enhanced KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-emerald-100 text-sm font-medium">{{ __('Units') }}</p>
                            <p class="text-3xl font-bold">{{ number_format($metrics['units'] ?? 0) }}</p>
                        </div>
                        <div class="p-3 bg-emerald-400/30 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v0"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="mt-3 text-emerald-100 text-sm">{{ __('Occupied: :occupied / Vacant: :vacant', ['occupied' => number_format($metrics['occupied_units'] ?? 0), 'vacant' => number_format($metrics['vacant_units'] ?? 0)]) }}</p>
                </div>
                
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm font-medium">{{ __('Occupancy Rate') }}</p>
                            <p class="text-3xl font-bold">{{ number_format($latestOccupancyRate, 1) }}%</p>
                        </div>
                        <div class="p-3 bg-purple-400/30 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 w-full bg-purple-400/30 rounded-full h-2">
                        <div class="bg-white rounded-full h-2 transition-all duration-500" style="width: {{ min($latestOccupancyRate, 100) }}%"></div>
                    </div>
                </div>
                
                <div class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-xl p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-amber-100 text-sm font-medium">{{ __('Annual Rent Roll') }}</p>
                            <p class="text-2xl font-bold">{{ number_format($metrics['monthly_rent'] ?? 0, 0) }} <span class="text-lg">{{ $metrics['rent_currency'] ?? 'JOD' }}</span></p>
                        </div>
                        <div class="p-3 bg-amber-400/30 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="mt-3 text-amber-100 text-sm">{{ __('Based on annual rent values for active leases.') }}</p>
                </div>
            </div>

            <!-- Enhanced Status Summary -->
            <div class="bg-white rounded-xl border border-slate-200/60 shadow-sm p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 bg-slate-100 rounded-lg">
                        <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">{{ __('Status Summary') }}</h3>
                        <p class="text-sm text-slate-500">{{ __('Operational health signals across leasing, vacancy, and maintenance.') }}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-lg p-4 border border-slate-200/60">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-medium text-slate-700">{{ __('Active Leases') }}</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                {{ __('Stable') }}
                            </span>
                        </div>
                        <div class="text-2xl font-bold text-slate-900">{{ number_format($metrics['active_leases'] ?? 0) }}</div>
                        <p class="text-xs text-slate-500 mt-2">{{ __('Maintain occupancy above 90% to protect revenue.') }}</p>
                    </div>
                    
                    <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-lg p-4 border border-slate-200/60">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-medium text-slate-700">{{ __('Vacant Units') }}</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusPalette[$vacancyTone]['bg'] }} {{ $statusPalette[$vacancyTone]['text'] }}">
                                {{ $vacancyTone === 'danger' ? __('High') : ($vacancyTone === 'warning' ? __('Watch') : __('Healthy')) }}
                            </span>
                        </div>
                        <div class="text-2xl font-bold text-slate-900">{{ number_format($metrics['vacant_units'] ?? 0) }}</div>
                        <p class="text-xs text-slate-500 mt-2">{{ __('Aim to turn units within 14 days of vacancy.') }}</p>
                    </div>
                    
                    <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-lg p-4 border border-slate-200/60">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-medium text-slate-700">{{ __('Open Maintenance') }}</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusPalette[$maintenanceTone]['bg'] }} {{ $statusPalette[$maintenanceTone]['text'] }}">
                                {{ $maintenanceTone === 'danger' ? __('Elevated') : ($maintenanceTone === 'warning' ? __('Watch') : __('Stable')) }}
                            </span>
                        </div>
                        <div class="text-2xl font-bold text-slate-900">{{ number_format($metrics['open_maintenance'] ?? 0) }}</div>
                        <p class="text-xs text-slate-500 mt-2">{{ __('Prioritize critical tickets to protect resident experience.') }}</p>
                    </div>
                    
                    <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-lg p-4 border border-slate-200/60">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-medium text-slate-700">{{ __('Average Days Open') }}</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusPalette[$avgOpenTone]['bg'] }} {{ $statusPalette[$avgOpenTone]['text'] }}">
                                {{ $avgOpenTone === 'danger' ? __('Overdue') : ($avgOpenTone === 'warning' ? __('At Risk') : __('On Track')) }}
                            </span>
                        </div>
                        <div class="text-2xl font-bold text-slate-900">{{ number_format($metrics['avg_open_days'] ?? 0, 1) }}</div>
                        <p class="text-xs text-slate-500 mt-2">{{ __('Average age of active maintenance tickets.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Enhanced Activity Section -->
            <div class="bg-white rounded-xl border border-slate-200/60 shadow-sm p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 bg-orange-100 rounded-lg">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5-5-5h5v-5a7.5 7.5 0 00-15 0v5h5l-5 5-5-5h5V7.5a7.5 7.5 0 0115 0V17z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">{{ __('Activity') }}</h3>
                        <p class="text-sm text-slate-500">{{ __('Latest alerts and activity signals across your portfolio.') }}</p>
                    </div>
                </div>
                
                <div class="space-y-3">
                    @forelse($alerts as $alert)
                        @php
                            $tone = match($alert->severity) {
                                'critical' => 'danger',
                                'warning' => 'warning',
                                default => 'info',
                            };
                            $toneStyles = $statusPalette[$tone];
                        @endphp
                        <div class="bg-gradient-to-r from-slate-50 to-slate-100/50 rounded-lg p-4 border border-slate-200/60">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 {{ $toneStyles['bg'] }} rounded-lg">
                                        <div class="w-2 h-2 {{ $toneStyles['dot'] }} rounded-full"></div>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-slate-900">{{ $alert->title }}</h4>
                                        <p class="text-sm text-slate-600 mt-1">{{ $alert->message }}</p>
                                    </div>
                                </div>
                                <span class="text-xs text-slate-500 whitespace-nowrap">{{ $alert->snapshot_date->format('M j, Y') }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="bg-gradient-to-r from-slate-50 to-slate-100/50 rounded-lg p-6 text-center">
                            <div class="w-16 h-16 bg-slate-200 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-slate-900 mb-2">{{ __('No recent alerts yet.') }}</h4>
                            <p class="text-slate-500 mb-4">{{ __('Run daily snapshots to surface portfolio signals.') }}</p>
                            <a href="{{ route('reports.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 text-white text-sm font-medium rounded-lg hover:bg-slate-800 transition-all">
                                {{ __('Open Reports') }}
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            <section class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="rounded-md bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Pipeline Snapshot') }}</h3>
                        <span class="text-xs text-slate-500">{{ $latestPipeline ? \Illuminate\Support\Carbon::parse($latestPipeline['date'])->format('M j, Y') : __('N/A') }}</span>
                    </div>
                    @if(!$latestPipeline)
                        <div class="mt-3 rounded-md bg-slate-50 p-4">
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-full bg-white text-slate-500 shadow-sm ring-1 ring-slate-200/60">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5V4.5h15v15m-9-4.5h3m-3-3h6m-6-3h6" />
                                    </svg>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-slate-700">{{ __('No pipeline snapshots yet.') }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ __('Run daily reports to start tracking lead flow.') }}</p>
                                    <a href="{{ route('reports.index') }}" class="mt-2 inline-flex text-xs font-semibold text-slate-700 hover:text-slate-900">{{ __('Open Reports') }}</a>
                                </div>
                            </div>
                        </div>
                    @else
                        <dl class="mt-4 grid grid-cols-2 gap-4 text-sm text-slate-600">
                            <div>
                                <dt class="font-semibold text-slate-800">{{ __('New Leads') }}</dt>
                                <dd>{{ number_format($latestPipeline['leads_new']) }}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-slate-800">{{ __('In Progress') }}</dt>
                                <dd>{{ number_format($latestPipeline['leads_in_progress']) }}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-slate-800">{{ __('Viewings Completed') }}</dt>
                                <dd>{{ number_format($latestPipeline['viewings_completed']) }}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-slate-800">{{ __('Leases Started') }}</dt>
                                <dd>{{ number_format($latestPipeline['leases_started']) }}</dd>
                            </div>
                        </dl>
                    @endif
                </div>

                <div class="rounded-md bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Occupancy Snapshot') }}</h3>
                        <span class="text-xs text-slate-500">{{ $latestOccupancy ? \Illuminate\Support\Carbon::parse($latestOccupancy['date'])->format('M j, Y') : __('N/A') }}</span>
                    </div>
                    @if(!$latestOccupancy)
                        <div class="mt-3 rounded-md bg-slate-50 p-4">
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-full bg-white text-slate-500 shadow-sm ring-1 ring-slate-200/60">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75h15M6 8.25h12M6 17.25h12" />
                                    </svg>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-slate-700">{{ __('No occupancy snapshots yet.') }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ __('Capture daily occupancy to monitor leasing health.') }}</p>
                                    <a href="{{ route('reports.index') }}" class="mt-2 inline-flex text-xs font-semibold text-slate-700 hover:text-slate-900">{{ __('Open Reports') }}</a>
                                </div>
                            </div>
                        </div>
                    @else
                        <dl class="mt-4 grid grid-cols-2 gap-4 text-sm text-slate-600">
                            <div>
                                <dt class="font-semibold text-slate-800">{{ __('Units Total') }}</dt>
                                <dd>{{ number_format($latestOccupancy['units_total']) }}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-slate-800">{{ __('Units Occupied') }}</dt>
                                <dd>{{ number_format($latestOccupancy['units_occupied']) }}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-slate-800">{{ __('Occupancy Rate') }}</dt>
                                <dd>{{ number_format($latestOccupancy['occupancy_rate'], 2) }}%</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-slate-800">{{ __('Rent Roll') }}</dt>
                                <dd>{{ number_format(($latestOccupancy['rent_roll_cents'] ?? 0)/100, 2) }} {{ $metrics['rent_currency'] ?? 'JOD' }}</dd>
                            </div>
                        </dl>
                    @endif
                </div>
            </section>

            <section class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="rounded-md bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Commission Snapshot') }}</h3>
                        <span class="text-xs text-slate-500">{{ $latestCommission ? \Illuminate\Support\Carbon::parse($latestCommission['date'])->format('M j, Y') : __('N/A') }}</span>
                    </div>
                    @if(!$latestCommission)
                        <div class="mt-3 rounded-md bg-slate-50 p-4">
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-full bg-white text-slate-500 shadow-sm ring-1 ring-slate-200/60">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m3-9H9m11.25 3a8.25 8.25 0 11-16.5 0 8.25 8.25 0 0116.5 0z" />
                                    </svg>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-slate-700">{{ __('No commission snapshots yet.') }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ __('Daily reports will track pending and paid commissions.') }}</p>
                                    <a href="{{ route('reports.index') }}" class="mt-2 inline-flex text-xs font-semibold text-slate-700 hover:text-slate-900">{{ __('Open Reports') }}</a>
                                </div>
                            </div>
                        </div>
                    @else
                        <dl class="mt-4 grid grid-cols-3 gap-4 text-sm text-slate-600">
                            <div>
                                <dt class="font-semibold text-slate-800">{{ __('Pending') }}</dt>
                                <dd>{{ number_format($latestCommission['pending']['amount'], 2) }}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-slate-800">{{ __('Approved') }}</dt>
                                <dd>{{ number_format($latestCommission['approved']['amount'], 2) }}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-slate-800">{{ __('Paid') }}</dt>
                                <dd>{{ number_format($latestCommission['paid']['amount'], 2) }}</dd>
                            </div>
                        </dl>
                    @endif
                </div>
                <div class="rounded-md bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Maintenance Snapshot') }}</h3>
                        <span class="text-xs text-slate-500">{{ $latestMaintenance ? \Illuminate\Support\Carbon::parse($latestMaintenance['date'])->format('M j, Y') : __('N/A') }}</span>
                    </div>
                    @if(!$latestMaintenance)
                        <div class="mt-3 rounded-md bg-slate-50 p-4">
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-full bg-white text-slate-500 shadow-sm ring-1 ring-slate-200/60">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6.75a4.5 4.5 0 016.75 5.75l-6 6-3.5.75.75-3.5 6-6A4.5 4.5 0 0110 6.75z" />
                                    </svg>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-slate-700">{{ __('No maintenance snapshots yet.') }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ __('Capture daily maintenance signals to spot backlogs.') }}</p>
                                    <a href="{{ route('reports.index') }}" class="mt-2 inline-flex text-xs font-semibold text-slate-700 hover:text-slate-900">{{ __('Open Reports') }}</a>
                                </div>
                            </div>
                        </div>
                    @else
                        <dl class="mt-4 grid grid-cols-3 gap-4 text-sm text-slate-600">
                            <div>
                                <dt class="font-semibold text-slate-800">{{ __('Open New') }}</dt>
                                <dd>{{ number_format($latestMaintenance['open_new']) }}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-slate-800">{{ __('Open Total') }}</dt>
                                <dd>{{ number_format($latestMaintenance['open_total']) }}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-slate-800">{{ __('Avg Days Open') }}</dt>
                                <dd>{{ number_format($latestMaintenance['avg_open_days'], 1) }}</dd>
                            </div>
                        </dl>
                    @endif
                </div>
            </section>

            <section class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="rounded-md bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Upcoming Lease Expirations') }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ __('Stay ahead of renewals and marketing for outgoing residents.') }}</p>
                        </div>
                        <span class="text-sm font-medium text-slate-500">{{ __('Next 60 days') }}</span>
                    </div>
                    <div class="mt-4 space-y-3">
                        @forelse($upcomingLeases as $lease)
                            @php
                                $unit = $lease->unit;
                                $rawTitle = $unit?->translated_title ?? $unit?->title ?? null;
                                if (is_array($rawTitle)) {
                                    $rawTitle = $rawTitle[app()->getLocale()] ?? ($rawTitle['en'] ?? reset($rawTitle));
                                }
                                $unitLabel = $rawTitle ?: __('Unit #:code', ['code' => $unit->code ?? '—']);
                            @endphp
                            <div class="rounded-md bg-slate-50 p-4 hover:bg-slate-100">
                                <div class="flex items-center justify-between text-sm font-medium text-slate-900">
                                    <span>{{ $unitLabel }}</span>
                                    <span class="text-slate-500">{{ optional($lease->end_date)->format('M j, Y') }}</span>
                                </div>
                                <div class="mt-1 flex items-center justify-between text-xs text-slate-500">
                                    <span>{{ optional($lease->property)->name ?? __('N/A') }}</span>
                                    <span>{{ __('Rent: :amount', ['amount' => number_format(($lease->rent_cents ?? 0) / 100, 2)]) }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-md bg-slate-50 p-4">
                                <div class="flex items-start gap-3">
                                    <span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-full bg-white text-slate-500 shadow-sm ring-1 ring-slate-200/60">
                                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h7.5m-7.5 4.5h7.5m-7.5 4.5h4.5M6 3.75h8.25L18 7.5V20.25a.75.75 0 01-.75.75H6.75a.75.75 0 01-.75-.75z" />
                                        </svg>
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-700">{{ __('No upcoming lease expirations.') }}</p>
                                        <p class="mt-1 text-sm text-slate-500">{{ __('Add leases to track renewals and revenue timelines.') }}</p>
                                        <a href="{{ route('leases.create') }}" class="mt-2 inline-flex text-xs font-semibold text-slate-700 hover:text-slate-900">{{ __('Add Lease') }}</a>
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-md bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Maintenance Workload') }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ __('Breakdown of outstanding and completed requests by status.') }}</p>
                        </div>
                        <span class="text-sm font-medium text-slate-500">{{ __('Total: :count', ['count' => number_format($maintenanceTotal) ]) }}</span>
                    </div>
                    <div class="mt-5 space-y-3">
                        @foreach($statusLabels as $statusKey => $label)
                            @php
                                $count = $maintenanceBreakdown[$statusKey] ?? 0;
                                $percentage = $maintenanceTotal > 0 ? round(($count / $maintenanceTotal) * 100) : 0;
                                $barColor = match($statusKey) {
                                    'new', 'open', 'in_progress' => $statusPalette['warning']['bar'],
                                    'resolved', 'completed' => $statusPalette['success']['bar'],
                                    default => $statusPalette['info']['bar'],
                                };
                            @endphp
                            <div>
                                <div class="flex items-center justify-between text-xs font-medium text-slate-600">
                                    <span>{{ $label }}</span>
                                    <span>{{ $count }} <span class="text-slate-400">({{ $percentage }}%)</span></span>
                                </div>
                                <div class="mt-1 h-1.5 rounded-full bg-slate-100">
                                    <div class="h-full rounded-full {{ $barColor }}" style="width: {{ $percentage }}%;"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-5 rounded-md bg-slate-50 p-4 text-sm text-slate-600">
                        {{ __('On average, active tickets have been open for :days days. Prioritize anything older than 7 days to keep service levels high.', ['days' => number_format($metrics['avg_open_days'] ?? 0, 1)]) }}
                    </div>
                </div>
            </section>

            <section class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="rounded-md bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Lead Performance') }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ __('Monitor leasing pipeline health by tracking new inquiries.') }}</p>
                        </div>
                        <span class="text-sm font-medium text-slate-500">{{ now()->format('M Y') }}</span>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-4 text-center">
                        <div class="rounded-md bg-slate-50 p-4">
                            <div class="text-xs uppercase tracking-wide text-slate-500">{{ __('Leads This Month') }}</div>
                            <div class="mt-1 text-2xl font-semibold text-slate-800">{{ number_format($leadStats['this_month'] ?? 0) }}</div>
                        </div>
                        <div class="rounded-md bg-slate-50 p-4">
                            <div class="text-xs uppercase tracking-wide text-slate-500">{{ __('Change vs. Last Month') }}</div>
                            <div class="mt-1 text-2xl font-semibold {{ $statusPalette[$leadTone]['text'] }}">
                                {{ number_format($leadStats['change_pct'] ?? 0, 1) }}%
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 space-y-2">
                        <h4 class="text-xs font-semibold uppercase text-slate-500">{{ __('Last 30 days trend') }}</h4>
                        <div class="grid grid-cols-1 gap-2">
                        @forelse($leadTrendCollection->sortByDesc('raw_date')->take(7) as $day)
                            <div class="flex items-center justify-between rounded-md bg-slate-50 px-3 py-2 text-sm">
                                <span class="text-slate-600">{{ $day['day'] }}</span>
                                <span class="font-semibold text-slate-900">{{ $day['total'] }}</span>
                            </div>
                        @empty
                            <div class="rounded-md bg-slate-50 p-4">
                                <div class="flex items-start gap-3">
                                    <span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-full bg-white text-slate-500 shadow-sm ring-1 ring-slate-200/60">
                                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 9a3 3 0 11-6 0 3 3 0 016 0zM2.25 20.25a8.25 8.25 0 0116.5 0" />
                                        </svg>
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-700">{{ __('No recent lead activity.') }}</p>
                                        <p class="mt-1 text-sm text-slate-500">{{ __('Capture inquiries to see pipeline momentum.') }}</p>
                                        <a href="{{ route('agent-leads.index') }}" class="mt-2 inline-flex text-xs font-semibold text-slate-700 hover:text-slate-900">{{ __('View Leads') }}</a>
                                    </div>
                                </div>
                            </div>
                        @endforelse
                        </div>
                    </div>
                </div>

                <div class="rounded-md bg-white p-5 shadow-sm space-y-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Historical Snapshots') }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ __('Trends captured daily from reports pipeline.') }}</p>
                        </div>
                        <span class="text-sm font-medium text-slate-500">{{ __('Last 14 days') }}</span>
                    </div>
                    <div>
                        <h4 class="text-xs font-semibold uppercase text-slate-500">{{ __('Pipeline Snapshot') }}</h4>
                        @if($pipelineTrend->isEmpty())
                            <div class="mt-3 rounded-md bg-slate-50 p-4">
                                <div class="flex items-start gap-3">
                                    <span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-full bg-white text-slate-500 shadow-sm ring-1 ring-slate-200/60">
                                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5V4.5h15v15m-9-4.5h3m-3-3h6m-6-3h6" />
                                        </svg>
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-700">{{ __('No pipeline history yet.') }}</p>
                                        <p class="mt-1 text-sm text-slate-500">{{ __('Run daily reports to capture the last 14 days.') }}</p>
                                        <a href="{{ route('reports.index') }}" class="mt-2 inline-flex text-xs font-semibold text-slate-700 hover:text-slate-900">{{ __('Open Reports') }}</a>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="mt-3 space-y-2">
                                @foreach($pipelineTrend->take(7) as $row)
                                    <div class="rounded-md bg-slate-50 px-3 py-2 text-xs text-slate-600">
                                        <div class="flex items-center justify-between font-semibold text-slate-800">
                                            <span>{{ \Illuminate\Support\Carbon::parse($row['date'])->format('M j') }}</span>
                                            <span>{{ __('Leads: :count', ['count' => $row['leads_new'] + $row['leads_in_progress']]) }}</span>
                                        </div>
                                        <div class="mt-1 grid grid-cols-2 gap-1">
                                            <div>{{ __('Viewings: :count', ['count' => $row['viewings_completed']]) }}</div>
                                            <div>{{ __('Leases: :count', ['count' => $row['leases_started']]) }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div>
                        <h4 class="text-xs font-semibold uppercase text-slate-500">{{ __('Occupancy Trend') }}</h4>
                        @if($occupancyTrend->isEmpty())
                            <div class="mt-3 rounded-md bg-slate-50 p-4">
                                <div class="flex items-start gap-3">
                                    <span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-full bg-white text-slate-500 shadow-sm ring-1 ring-slate-200/60">
                                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75h15M6 8.25h12M6 17.25h12" />
                                        </svg>
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-700">{{ __('No occupancy history yet.') }}</p>
                                        <p class="mt-1 text-sm text-slate-500">{{ __('Daily snapshots keep occupancy trend lines accurate.') }}</p>
                                        <a href="{{ route('reports.index') }}" class="mt-2 inline-flex text-xs font-semibold text-slate-700 hover:text-slate-900">{{ __('Open Reports') }}</a>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="mt-3 space-y-2">
                                @foreach($occupancyTrend->take(5) as $row)
                                    <div class="flex items-center justify-between rounded-md bg-slate-50 px-3 py-2 text-xs">
                                        <span class="text-slate-500">{{ \Illuminate\Support\Carbon::parse($row['date'])->format('M j') }}</span>
                                        <span class="font-semibold text-slate-800">{{ $row['occupancy_rate'] }}%</span>
                                        <span class="text-slate-500">{{ __('Rent: :amount', ['amount' => number_format(($row['rent_roll_cents'] ?? 0)/100, 2)]) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div>
                        <h4 class="text-xs font-semibold uppercase text-slate-500">{{ __('Commissions Snapshot') }}</h4>
                        @if($commissionTrend->isEmpty())
                            <div class="mt-3 rounded-md bg-slate-50 p-4">
                                <div class="flex items-start gap-3">
                                    <span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-full bg-white text-slate-500 shadow-sm ring-1 ring-slate-200/60">
                                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m3-9H9m11.25 3a8.25 8.25 0 11-16.5 0 8.25 8.25 0 0116.5 0z" />
                                        </svg>
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-700">{{ __('No commission history yet.') }}</p>
                                        <p class="mt-1 text-sm text-slate-500">{{ __('Track approval and payout trends with reports.') }}</p>
                                        <a href="{{ route('reports.index') }}" class="mt-2 inline-flex text-xs font-semibold text-slate-700 hover:text-slate-900">{{ __('Open Reports') }}</a>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="mt-3 space-y-2">
                                @foreach($commissionTrend->take(5) as $row)
                                    <div class="rounded-md bg-slate-50 px-3 py-2 text-xs text-slate-600">
                                        <div class="flex items-center justify-between font-semibold text-slate-800">
                                            <span>{{ \Illuminate\Support\Carbon::parse($row['date'])->format('M j') }}</span>
                                            <span>{{ __('Pending: :amount', ['amount' => number_format($row['pending']['amount'], 2)]) }}</span>
                                        </div>
                                        <div class="mt-1 grid grid-cols-2 gap-1">
                                            <div>{{ __('Approved: :amount', ['amount' => number_format($row['approved']['amount'], 2)]) }}</div>
                                            <div>{{ __('Paid: :amount', ['amount' => number_format($row['paid']['amount'], 2)]) }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div>
                        <h4 class="text-xs font-semibold uppercase text-slate-500">{{ __('Maintenance Snapshot') }}</h4>
                        @if($maintenanceTrend->isEmpty())
                            <div class="mt-3 rounded-md bg-slate-50 p-4">
                                <div class="flex items-start gap-3">
                                    <span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-full bg-white text-slate-500 shadow-sm ring-1 ring-slate-200/60">
                                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6.75a4.5 4.5 0 016.75 5.75l-6 6-3.5.75.75-3.5 6-6A4.5 4.5 0 0110 6.75z" />
                                        </svg>
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-700">{{ __('No maintenance history yet.') }}</p>
                                        <p class="mt-1 text-sm text-slate-500">{{ __('Capture daily maintenance signals for trend lines.') }}</p>
                                        <a href="{{ route('reports.index') }}" class="mt-2 inline-flex text-xs font-semibold text-slate-700 hover:text-slate-900">{{ __('Open Reports') }}</a>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="mt-3 space-y-2">
                                @foreach($maintenanceTrend->take(5) as $row)
                                    <div class="flex items-center justify-between rounded-md bg-slate-50 px-3 py-2 text-xs">
                                        <span class="text-slate-500">{{ \Illuminate\Support\Carbon::parse($row['date'])->format('M j') }}</span>
                                        <span class="font-semibold text-slate-800">{{ __('Open: :count', ['count' => $row['open_total']]) }}</span>
                                        <span class="text-slate-500">{{ __('Avg Days: :days', ['days' => number_format($row['avg_open_days'], 1)]) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </section>
            <section class="rounded-md bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Unit Occupancy Leaders') }}</h3>
                        <p class="mt-1 text-sm text-slate-500">{{ __('Top performing units ranked by occupancy rate.') }}</p>
                    </div>
                    <span class="text-sm font-medium text-slate-500">{{ __('Top :count', ['count' => $propertyOccupancy->count()]) }}</span>
                </div>
                <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2">
                    @forelse($propertyOccupancy as $property)
                        @php
                            $rateTone = ($property['rate'] ?? 0) >= 90 ? 'success' : (($property['rate'] ?? 0) >= 75 ? 'warning' : 'danger');
                        @endphp
                        <div class="rounded-md bg-slate-50 p-4 hover:bg-slate-100">
                            <div class="flex items-center justify-between text-sm font-medium text-slate-900">
                                <span>{{ $property['name'] }}</span>
                                <span>{{ $property['rate'] }}%</span>
                            </div>
                            <div class="mt-1 flex items-center justify-between text-xs text-slate-500">
                                <span>{{ __('Occupied :occupied / :total units', ['occupied' => $property['occupied'], 'total' => $property['total']]) }}</span>
                            </div>
                            <div class="mt-2 h-1.5 rounded-full bg-slate-100">
                                <div class="h-full rounded-full {{ $statusPalette[$rateTone]['bar'] }} transition-all duration-500" style="width: {{ min($property['rate'], 100) }}%;"></div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-md bg-slate-50 p-4">
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-full bg-white text-slate-500 shadow-sm ring-1 ring-slate-200/60">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 21V9l9-6 9 6v12M9 21V9h6v12" />
                                    </svg>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-slate-700">{{ __('No occupancy leaders yet.') }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ __('Add units to rank occupancy performance.') }}</p>
                                    <a href="{{ route('units.create') }}" class="mt-2 inline-flex text-xs font-semibold text-slate-700 hover:text-slate-900">{{ __('Add Unit') }}</a>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-tenant-layout>
