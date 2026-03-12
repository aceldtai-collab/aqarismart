<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ __('Reports') }}</h2>
    </x-slot>

    @php
        $pipelineTrend = collect($pipelineTrend ?? [])->sortByDesc('date');
        $occupancyTrend = collect($occupancyTrend ?? [])->sortByDesc('date');
        $commissionTrend = collect($commissionTrend ?? [])->sortByDesc('date');
        $maintenanceTrend = collect($maintenanceTrend ?? [])->sortByDesc('date');
    @endphp

    <div class="py-6">
        <div class="max-w-6xl mx-auto space-y-8 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-6 shadow">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Snapshot Reports') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('Daily aggregates captured from operational data.') }}</p>
                    </div>
                    <form method="get" class="flex items-center gap-2 text-sm">
                        <label for="days" class="text-gray-500">{{ __('Days') }}</label>
                        <select id="days" name="days" class="rounded-md border-gray-300 text-sm" onchange="this.form.submit()">
                            @foreach([14,30,60,90] as $range)
                                <option value="{{ $range }}" @selected($days === $range)>{{ $range }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>

            <section class="rounded-lg bg-white p-6 shadow">
                <h4 class="text-lg font-semibold text-gray-900">{{ __('Pipeline & Leasing') }}</h4>
                @if($pipelineTrend->isEmpty())
                    <p class="mt-2 text-sm text-gray-500">{{ __('No snapshots recorded for the selected range.') }}</p>
                @else
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">{{ __('Date') }}</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">{{ __('Leads') }}</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">{{ __('Viewings') }}</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">{{ __('Leases Started') }}</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">{{ __('Leases Active') }}</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">{{ __('Lead→Viewing %') }}</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">{{ __('Lead→Lease %') }}</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                            @foreach($pipelineTrend as $row)
                                <tr>
                                    <td class="px-3 py-2 text-gray-700">{{ \Illuminate\Support\Carbon::parse($row['date'])->format('M j, Y') }}</td>
                                    <td class="px-3 py-2 text-gray-700">
                                        {{ __('New: :new / In Progress: :progress / Visited: :visited', ['new' => $row['leads_new'], 'progress' => $row['leads_in_progress'], 'visited' => $row['leads_visited']]) }}
                                    </td>
                                    <td class="px-3 py-2 text-gray-700">
                                        {{ __('Scheduled: :scheduled / Completed: :completed', ['scheduled' => $row['viewings_scheduled'], 'completed' => $row['viewings_completed']]) }}
                                    </td>
                                    <td class="px-3 py-2 text-gray-700">{{ $row['leases_started'] }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ $row['leases_active'] }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ number_format($row['lead_to_viewing_rate'], 2) }}%</td>
                                    <td class="px-3 py-2 text-gray-700">{{ number_format($row['lead_to_lease_rate'], 2) }}%</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            <section class="rounded-lg bg-white p-6 shadow">
                <h4 class="text-lg font-semibold text-gray-900">{{ __('Occupancy & Rent Roll') }}</h4>
                @if($occupancyTrend->isEmpty())
                    <p class="mt-2 text-sm text-gray-500">{{ __('No occupancy snapshots for the selected range.') }}</p>
                @else
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">{{ __('Date') }}</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">{{ __('Units Total') }}</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">{{ __('Units Occupied') }}</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">{{ __('Occupancy Rate') }}</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">{{ __('Rent Roll') }}</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                            @foreach($occupancyTrend as $row)
                                <tr>
                                    <td class="px-3 py-2 text-gray-700">{{ \Illuminate\Support\Carbon::parse($row['date'])->format('M j, Y') }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ number_format($row['units_total']) }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ number_format($row['units_occupied']) }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ number_format($row['occupancy_rate'], 2) }}%</td>
                                    <td class="px-3 py-2 text-gray-700">{{ number_format(($row['rent_roll_cents'] ?? 0)/100, 2) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            <section class="rounded-lg bg-white p-6 shadow">
                <h4 class="text-lg font-semibold text-gray-900">{{ __('Commissions') }}</h4>
                @if($commissionTrend->isEmpty())
                    <p class="mt-2 text-sm text-gray-500">{{ __('No commission snapshots for the selected range.') }}</p>
                @else
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">{{ __('Date') }}</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">{{ __('Pending (count / amount)') }}</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">{{ __('Approved (count / amount)') }}</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">{{ __('Paid (count / amount)') }}</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                            @foreach($commissionTrend as $row)
                                <tr>
                                    <td class="px-3 py-2 text-gray-700">{{ \Illuminate\Support\Carbon::parse($row['date'])->format('M j, Y') }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ $row['pending']['count'] }} / {{ number_format($row['pending']['amount'], 2) }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ $row['approved']['count'] }} / {{ number_format($row['approved']['amount'], 2) }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ $row['paid']['count'] }} / {{ number_format($row['paid']['amount'], 2) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            <section class="rounded-lg bg-white p-6 shadow">
                <h4 class="text-lg font-semibold text-gray-900">{{ __('Maintenance') }}</h4>
                @if($maintenanceTrend->isEmpty())
                    <p class="mt-2 text-sm text-gray-500">{{ __('No maintenance snapshots for the selected range.') }}</p>
                @else
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">{{ __('Date') }}</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">{{ __('Open New') }}</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">{{ __('In Progress') }}</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">{{ __('Total Open') }}</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">{{ __('Resolved Today') }}</th>
                                <th class="px-3 py-2 text-left font-semibold text-gray-600">{{ __('Avg Days Open') }}</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                            @foreach($maintenanceTrend as $row)
                                <tr>
                                    <td class="px-3 py-2 text-gray-700">{{ \Illuminate\Support\Carbon::parse($row['date'])->format('M j, Y') }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ $row['open_new'] }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ $row['open_in_progress'] }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ $row['open_total'] }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ $row['resolved_today'] }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ number_format($row['avg_open_days'], 1) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
