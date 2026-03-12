<x-admin-layout>
    <x-slot name="header">{{ __('Super Admin Reports') }}</x-slot>
    <x-slot name="subtitle">{{ __('Platform-wide analytics and snapshots.') }}</x-slot>

    {{-- Filters --}}
    <div class="gz-widget mb-5">
        <div class="p-5">
            <form method="get" class="flex flex-wrap items-end gap-4 text-sm">
                <div class="w-48">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Tenant') }}</label>
                    <select id="tenant_id" name="tenant_id" class="gz-search w-full" onchange="this.form.submit()">
                        <option value="">{{ __('All tenants') }}</option>
                        @foreach($tenants as $tenant)
                            <option value="{{ $tenant->id }}" @selected($selectedTenant == $tenant->id)>{{ $tenant->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-28">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-[#7c8db5] mb-1">{{ __('Days') }}</label>
                    <select id="days" name="days" class="gz-search w-full" onchange="this.form.submit()">
                        @foreach([14,30,60,90] as $range)
                            <option value="{{ $range }}" @selected($days === $range)>{{ $range }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div class="space-y-5">
        {{-- Pipeline & Leasing --}}
        <div class="gz-widget">
            <div class="gz-widget-header">
                <h3 class="text-base font-bold text-[#1e1e2d]">{{ __('Pipeline & Leasing') }}</h3>
                <span class="text-xs text-[#7c8db5]">{{ __('Aggregated per tenant (agent totals).') }}</span>
            </div>
            @if($pipelineSnapshots->isEmpty())
                <div class="gz-widget-body text-center py-8 text-sm text-[#7c8db5]">{{ __('No snapshots available for this range.') }}</div>
            @else
                <table class="w-full gz-table">
                    <thead>
                    <tr>
                        <th class="text-left">{{ __('Date') }}</th>
                        <th class="text-left">{{ __('Tenant') }}</th>
                        <th class="text-left">{{ __('New Leads') }}</th>
                        <th class="text-left">{{ __('In Progress') }}</th>
                        <th class="text-left">{{ __('Viewings Completed') }}</th>
                        <th class="text-left">{{ __('Leases Started') }}</th>
                        <th class="text-left">{{ __('Lead→Lease %') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($pipelineSnapshots as $row)
                        <tr>
                            <td class="text-[#7c8db5]">{{ \Illuminate\Support\Carbon::parse($row->snapshot_date)->format('M j, Y') }}</td>
                            <td class="font-semibold text-[#1e1e2d]">{{ $row->name }}</td>
                            <td>{{ number_format($row->leads_new) }}</td>
                            <td>{{ number_format($row->leads_in_progress) }}</td>
                            <td>{{ number_format($row->viewings_completed) }}</td>
                            <td>{{ number_format($row->leases_started) }}</td>
                            <td><span class="gz-badge bg-[#2bc155]/10 text-[#2bc155]">{{ number_format($row->lead_to_lease_rate, 2) }}%</span></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- Occupancy & Rent Roll --}}
        <div class="gz-widget">
            <div class="gz-widget-header">
                <h3 class="text-base font-bold text-[#1e1e2d]">{{ __('Occupancy & Rent Roll') }}</h3>
            </div>
            @if($occupancySnapshots->isEmpty())
                <div class="gz-widget-body text-center py-8 text-sm text-[#7c8db5]">{{ __('No occupancy snapshots for this range.') }}</div>
            @else
                <table class="w-full gz-table">
                    <thead>
                    <tr>
                        <th class="text-left">{{ __('Date') }}</th>
                        <th class="text-left">{{ __('Tenant') }}</th>
                        <th class="text-left">{{ __('Units Total') }}</th>
                        <th class="text-left">{{ __('Units Occupied') }}</th>
                        <th class="text-left">{{ __('Occupancy Rate') }}</th>
                        <th class="text-left">{{ __('Rent Roll') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($occupancySnapshots as $row)
                        <tr>
                            <td class="text-[#7c8db5]">{{ \Illuminate\Support\Carbon::parse($row->snapshot_date)->format('M j, Y') }}</td>
                            <td class="font-semibold text-[#1e1e2d]">{{ $row->name }}</td>
                            <td>{{ number_format($row->units_total) }}</td>
                            <td>{{ number_format($row->units_occupied) }}</td>
                            <td><span class="gz-badge bg-[#5b73e8]/10 text-[#5b73e8]">{{ number_format($row->occupancy_rate, 2) }}%</span></td>
                            <td class="font-medium text-[#1e1e2d]">{{ number_format(($row->rent_roll_cents ?? 0)/100, 2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- Commissions --}}
        <div class="gz-widget">
            <div class="gz-widget-header">
                <h3 class="text-base font-bold text-[#1e1e2d]">{{ __('Commissions') }}</h3>
            </div>
            @if($commissionSnapshots->isEmpty())
                <div class="gz-widget-body text-center py-8 text-sm text-[#7c8db5]">{{ __('No commission snapshots for this range.') }}</div>
            @else
                <table class="w-full gz-table">
                    <thead>
                    <tr>
                        <th class="text-left">{{ __('Date') }}</th>
                        <th class="text-left">{{ __('Tenant') }}</th>
                        <th class="text-left">{{ __('Pending') }}</th>
                        <th class="text-left">{{ __('Approved') }}</th>
                        <th class="text-left">{{ __('Paid') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($commissionSnapshots as $row)
                        <tr>
                            <td class="text-[#7c8db5]">{{ \Illuminate\Support\Carbon::parse($row->snapshot_date)->format('M j, Y') }}</td>
                            <td class="font-semibold text-[#1e1e2d]">{{ $row->name }}</td>
                            <td><span class="gz-badge bg-[#ffab2d]/10 text-[#ffab2d]">{{ number_format($row->pending_amount, 2) }}</span></td>
                            <td><span class="gz-badge bg-[#5b73e8]/10 text-[#5b73e8]">{{ number_format($row->approved_amount, 2) }}</span></td>
                            <td><span class="gz-badge bg-[#2bc155]/10 text-[#2bc155]">{{ number_format($row->paid_amount, 2) }}</span></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- Maintenance --}}
        <div class="gz-widget">
            <div class="gz-widget-header">
                <h3 class="text-base font-bold text-[#1e1e2d]">{{ __('Maintenance') }}</h3>
            </div>
            @if($maintenanceSnapshots->isEmpty())
                <div class="gz-widget-body text-center py-8 text-sm text-[#7c8db5]">{{ __('No maintenance snapshots for this range.') }}</div>
            @else
                <table class="w-full gz-table">
                    <thead>
                    <tr>
                        <th class="text-left">{{ __('Date') }}</th>
                        <th class="text-left">{{ __('Tenant') }}</th>
                        <th class="text-left">{{ __('Open Tickets') }}</th>
                        <th class="text-left">{{ __('Resolved Today') }}</th>
                        <th class="text-left">{{ __('Avg Days Open') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($maintenanceSnapshots as $row)
                        <tr>
                            <td class="text-[#7c8db5]">{{ \Illuminate\Support\Carbon::parse($row->snapshot_date)->format('M j, Y') }}</td>
                            <td class="font-semibold text-[#1e1e2d]">{{ $row->name }}</td>
                            <td><span class="gz-badge bg-[#e8604c]/10 text-[#e8604c]">{{ number_format($row->open_total) }}</span></td>
                            <td><span class="gz-badge bg-[#2bc155]/10 text-[#2bc155]">{{ number_format($row->resolved_today) }}</span></td>
                            <td class="text-[#7c8db5]">{{ number_format($row->avg_open_days, 1) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</x-admin-layout>
