<x-admin-layout>
    <x-slot name="header">{{ __('Dashboard') }}</x-slot>
    <x-slot name="subtitle">{{ __('Welcome back! Here is your platform overview.') }}</x-slot>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <a href="{{ route('admin.tenants.index') }}" class="gz-stat-card flex items-center gap-3 group">
            <div class="gz-icon-box bg-[#e8604c]/10">
                <svg class="w-6 h-6 text-[#e8604c]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-xl sm:text-2xl font-bold text-[#1e1e2d]">{{ number_format($tenantsCount) }}</div>
                <div class="text-xs font-medium text-[#7c8db5]">{{ __('Tenants') }}</div>
            </div>
        </a>

        <a href="{{ route('admin.users.index') }}" class="gz-stat-card flex items-center gap-3 group">
            <div class="gz-icon-box bg-[#5b73e8]/10">
                <svg class="w-6 h-6 text-[#5b73e8]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-xl sm:text-2xl font-bold text-[#1e1e2d]">{{ number_format($usersCount) }}</div>
                <div class="text-xs font-medium text-[#7c8db5]">{{ __('Users') }}</div>
            </div>
        </a>

        <a href="{{ route('admin.packages.index') }}" class="gz-stat-card flex items-center gap-3 group">
            <div class="gz-icon-box bg-[#2bc155]/10">
                <svg class="w-6 h-6 text-[#2bc155]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-xl sm:text-2xl font-bold text-[#1e1e2d]">{{ number_format($activeSubsCount) }}</div>
                <div class="text-xs font-medium text-[#7c8db5]">{{ __('Active Subscriptions') }}</div>
            </div>
        </a>

        <a href="{{ route('admin.packages.index') }}" class="gz-stat-card flex items-center gap-3 group">
            <div class="gz-icon-box bg-[#ffab2d]/10">
                <svg class="w-6 h-6 text-[#ffab2d]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-xl sm:text-2xl font-bold text-[#1e1e2d]">{{ number_format($packagesCount) }}</div>
                <div class="text-xs font-medium text-[#7c8db5]">{{ __('Active Packages') }}</div>
            </div>
        </a>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3 mb-6">
        <a href="{{ route('admin.tenants.index') }}" class="flex items-center gap-2.5 p-3 rounded-xl bg-white border border-[#e8ecf3] hover:border-[#e8604c]/30 hover:shadow-md transition-all group">
            <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-[#e8604c] to-[#ff7b6b] flex items-center justify-center shadow-sm flex-shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            </div>
            <div class="text-xs font-semibold text-[#1e1e2d] group-hover:text-[#e8604c] transition truncate">{{ __('New Tenant') }}</div>
        </a>
        <a href="{{ route('admin.packages.create') }}" class="flex items-center gap-2.5 p-3 rounded-xl bg-white border border-[#e8ecf3] hover:border-[#5b73e8]/30 hover:shadow-md transition-all group">
            <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-[#5b73e8] to-[#7b8ff0] flex items-center justify-center shadow-sm flex-shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/></svg>
            </div>
            <div class="text-xs font-semibold text-[#1e1e2d] group-hover:text-[#5b73e8] transition truncate">{{ __('New Package') }}</div>
        </a>
        <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-2.5 p-3 rounded-xl bg-white border border-[#e8ecf3] hover:border-[#2bc155]/30 hover:shadow-md transition-all group">
            <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-[#2bc155] to-[#4dd97a] flex items-center justify-center shadow-sm flex-shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/></svg>
            </div>
            <div class="text-xs font-semibold text-[#1e1e2d] group-hover:text-[#2bc155] transition truncate">{{ __('Categories') }}</div>
        </a>
        <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-2.5 p-3 rounded-xl bg-white border border-[#e8ecf3] hover:border-[#ffab2d]/30 hover:shadow-md transition-all group">
            <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-[#ffab2d] to-[#ffc966] flex items-center justify-center shadow-sm flex-shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75z"/></svg>
            </div>
            <div class="text-xs font-semibold text-[#1e1e2d] group-hover:text-[#ffab2d] transition truncate">{{ __('Reports') }}</div>
        </a>
        <a href="{{ route('admin.addons.index') }}" class="flex items-center gap-2.5 p-3 rounded-xl bg-white border border-[#e8ecf3] hover:border-[#5b73e8]/30 hover:shadow-md transition-all group">
            <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-[#5b73e8] to-[#7b8ff0] flex items-center justify-center shadow-sm flex-shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 16.875h3.375m0 0h3.375m-3.375 0V13.5m0 3.375v3.375M6 10.5h2.25a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H6A2.25 2.25 0 003.75 6v2.25A2.25 2.25 0 006 10.5z"/></svg>
            </div>
            <div class="text-xs font-semibold text-[#1e1e2d] group-hover:text-[#5b73e8] transition truncate">{{ __('Add-ons') }}</div>
        </a>
        <a href="/readyz" class="flex items-center gap-2.5 p-3 rounded-xl bg-white border border-[#e8ecf3] hover:border-[#2bc155]/30 hover:shadow-md transition-all group">
            <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-[#2bc155] to-[#4dd97a] flex items-center justify-center shadow-sm flex-shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
            </div>
            <div class="text-xs font-semibold text-[#1e1e2d] group-hover:text-[#2bc155] transition truncate">{{ __('Health Check') }}</div>
        </a>
    </div>

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
        {{-- Recent Tenants (spans 2 cols on lg) --}}
        <div class="gz-widget lg:col-span-2">
            <div class="gz-widget-header">
                <div>
                    <h3 class="text-sm font-bold text-[#1e1e2d]">{{ __('Recent Tenants') }}</h3>
                    <p class="text-xs text-[#7c8db5] mt-0.5">{{ __('Latest tenants added to the platform.') }}</p>
                </div>
                <a href="{{ route('admin.tenants.index') }}" class="gz-btn gz-btn-outline text-xs py-1.5 px-3">{{ __('View All') }}</a>
            </div>
            <div class="gz-widget-body">
                @if($recentTenants->count())
                    <div class="gz-table-wrap">
                        <table class="w-full gz-table">
                            <thead>
                                <tr>
                                    <th class="text-left">{{ __('Name') }}</th>
                                    <th class="text-left">{{ __('Subdomain') }}</th>
                                    <th class="text-left">{{ __('Created') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTenants as $t)
                                    <tr>
                                        <td>
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-[#e8604c] to-[#ff7b6b] flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                                    {{ strtoupper(substr($t->name, 0, 1)) }}
                                                </div>
                                                <span class="font-semibold text-[#1e1e2d]">{{ $t->name }}</span>
                                            </div>
                                        </td>
                                        <td class="text-[#7c8db5]">{{ $t->slug ?? '—' }}</td>
                                        <td class="text-[#7c8db5]">{{ $t->created_at->diffForHumans() }}</td>
                                        <td class="text-right">
                                            <a href="{{ route('admin.tenants.show', $t) }}" class="gz-btn text-xs py-1 px-2.5 text-[#5b73e8] border border-[#5b73e8]/20 hover:bg-[#5b73e8]/5">{{ __('View') }}</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21"/></svg>
                        </div>
                        <p class="text-sm text-[#7c8db5]">{{ __('No tenants yet.') }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Occupancy --}}
        <div class="gz-widget">
            <div class="gz-widget-header">
                <div>
                    <h3 class="text-sm font-bold text-[#1e1e2d]">{{ __('Occupancy') }}</h3>
                    <p class="text-xs text-[#7c8db5] mt-0.5">{{ __('Latest snapshots') }}</p>
                </div>
                <div class="gz-icon-box bg-[#2bc155]/10 w-9 h-9">
                    <svg class="w-4 h-4 text-[#2bc155]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21"/></svg>
                </div>
            </div>
            <div class="gz-widget-body space-y-2.5">
                @forelse($occupancySnapshots as $row)
                    <div class="rounded-xl bg-[#f5f6fa] px-3 py-2.5">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-xs text-[#1e1e2d] truncate">{{ $row->name }}</span>
                            <span class="gz-badge bg-[#2bc155]/10 text-[#2bc155] text-[10px]">{{ $row->occupancy_rate }}%</span>
                        </div>
                        <div class="mt-1.5 flex items-center justify-between text-[10px] text-[#7c8db5]">
                            <span>{{ \Illuminate\Support\Carbon::parse($row->snapshot_date)->format('M j') }}</span>
                            <span>{{ $row->units_occupied }}/{{ $row->units_total }}</span>
                        </div>
                        <div class="mt-1.5 w-full bg-gray-200 rounded-full h-1">
                            <div class="h-1 rounded-full bg-[#2bc155]" style="width: {{ min(100, $row->occupancy_rate) }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6">
                        <p class="text-xs text-[#7c8db5]">{{ __('No occupancy snapshots yet.') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Bottom Widgets --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {{-- Maintenance --}}
        <div class="gz-widget">
            <div class="gz-widget-header">
                <div>
                    <h3 class="text-sm font-bold text-[#1e1e2d]">{{ __('Maintenance Snapshots') }}</h3>
                    <p class="text-xs text-[#7c8db5] mt-0.5">{{ __('Open workload and recent resolutions.') }}</p>
                </div>
                <div class="gz-icon-box bg-[#ffab2d]/10 w-9 h-9">
                    <svg class="w-4 h-4 text-[#ffab2d]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17l-5.384 3.18.64-5.99L2.294 8.004l6.005-.873L11.42 2l2.693 5.131 6.005.873-4.382 4.356.64 5.99z"/></svg>
                </div>
            </div>
            <div class="gz-widget-body space-y-2.5">
                @forelse($maintenanceSnapshots as $row)
                    <div class="rounded-xl bg-[#f5f6fa] px-3 py-2.5">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-xs text-[#1e1e2d]">{{ $row->name }}</span>
                            <span class="gz-badge bg-[#ffab2d]/10 text-[#ffab2d] text-[10px]">{{ __('Open: :count', ['count' => $row->open_total]) }}</span>
                        </div>
                        <div class="mt-1.5 text-[10px] text-[#7c8db5] flex items-center justify-between">
                            <span>{{ \Illuminate\Support\Carbon::parse($row->snapshot_date)->format('M j, Y') }}</span>
                            <span>{{ __('Resolved: :count · Avg: :days d', ['count' => $row->resolved_today, 'days' => number_format($row->avg_open_days, 1)]) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6">
                        <p class="text-xs text-[#7c8db5]">{{ __('No maintenance snapshots yet.') }}</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Commissions --}}
        <div class="gz-widget">
            <div class="gz-widget-header">
                <div>
                    <h3 class="text-sm font-bold text-[#1e1e2d]">{{ __('Commission Snapshots') }}</h3>
                    <p class="text-xs text-[#7c8db5] mt-0.5">{{ __('Pending, approved, and paid totals.') }}</p>
                </div>
                <div class="gz-icon-box bg-[#5b73e8]/10 w-9 h-9">
                    <svg class="w-4 h-4 text-[#5b73e8]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="gz-widget-body space-y-2.5">
                @forelse($commissionSnapshots as $row)
                    <div class="rounded-xl bg-[#f5f6fa] px-3 py-2.5">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-xs text-[#1e1e2d]">{{ $row->name }}</span>
                            <span class="gz-badge bg-[#5b73e8]/10 text-[#5b73e8] text-[10px]">{{ __('Pending: :amount', ['amount' => number_format($row->pending_amount, 2)]) }}</span>
                        </div>
                        <div class="mt-1.5 text-[10px] text-[#7c8db5]">
                            {{ \Illuminate\Support\Carbon::parse($row->snapshot_date)->format('M j, Y') }} ·
                            {{ __('Approved: :approved · Paid: :paid', ['approved' => number_format($row->approved_amount, 2), 'paid' => number_format($row->paid_amount, 2)]) }}
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6">
                        <p class="text-xs text-[#7c8db5]">{{ __('No commission snapshots yet.') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-admin-layout>
