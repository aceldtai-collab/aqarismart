<x-admin-layout>
    <x-slot name="header">{{ $tenant->name }}</x-slot>
    <x-slot name="subtitle">{{ __('Tenant overview and metrics') }}</x-slot>
    <x-slot name="headerActions">
        <a href="{{ $tenant->url ?? 'http://initech.localtest.me:8000/dashboard' }}" target="_blank" class="gz-btn gz-btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
            {{ __('Open Dashboard') }}
        </a>
        <a href="{{ route('admin.tenants.subscription', $tenant) }}" class="gz-btn gz-btn-outline">
            {{ __('Manage Subscription') }}
        </a>
    </x-slot>

    {{-- Tenant Info Card --}}
    <div class="gz-widget mb-6">
        <div class="p-6">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-[#7c8db5]">{{ __('Tenant') }}</div>
                    <div class="mt-1 font-bold text-[#1e1e2d] text-lg">{{ $tenant->slug ?? 'initech' }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-[#7c8db5]">{{ __('Plan') }}</div>
                    <div class="mt-1 inline-flex items-center gap-2">
                        <span class="inline-block h-2.5 w-2.5 rounded-full {{ $tenant->plan === 'pro' ? 'bg-[#2bc155]' : 'bg-[#7c8db5]' }}"></span>
                        <span class="font-bold text-[#1e1e2d] text-lg">{{ ucfirst($tenant->plan) ?? 'pro' }}</span>
                    </div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-[#7c8db5]">{{ __('Trial Ends') }}</div>
                    <div class="mt-1 font-bold text-[#1e1e2d] text-lg">
                        {{ \Carbon\Carbon::parse($tenant->trial_ends_at)->format('M d, Y') ?? '—' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stat Cards --}}
    @php
        $cards = [
            ['label' => __('Properties'), 'value' => $metrics['properties'] ?? 0, 'url' => $urls['properties'] ?? '#', 'color' => '#e8604c'],
            ['label' => __('Units'), 'value' => $metrics['units'] ?? 0, 'url' => $urls['units'] ?? '#', 'color' => '#5b73e8'],
            ['label' => __('Residents'), 'value' => $metrics['residents'] ?? 0, 'url' => $urls['residents'] ?? '#', 'color' => '#2bc155'],
            ['label' => __('Active Leases'), 'value' => $metrics['leasesActive'] ?? 0, 'url' => $urls['leases'] ?? '#', 'color' => '#ffab2d'],
            ['label' => __('Leads'), 'value' => $metrics['leads'] ?? 0, 'url' => $urls['contacts'] ?? '#', 'color' => '#5b73e8'],
            ['label' => __('Open Maintenance Requests'), 'value' => $metrics['openRequests'] ?? ($openRequests ?? 0), 'url' => $urls['maintenance'] ?? '#', 'color' => '#e8604c'],
            ['label' => __('Vacant Units'), 'value' => $metrics['vacantUnits'] ?? 0, 'url' => $urls['unitsVacant'] ?? ($urls['units'] ?? '#'), 'color' => '#ffab2d'],
            ['label' => __('Occupancy Rate'), 'value' => ($metrics['occupancyRate'] ?? 0) . '%', 'url' => null, 'color' => '#2bc155'],
            ['label' => __('New Leads (7d)'), 'value' => $metrics['leads7d'] ?? 0, 'url' => $urls['contacts'] ?? '#', 'color' => '#5b73e8'],
            ['label' => __('New Units (7d)'), 'value' => $metrics['newUnits7d'] ?? 0, 'url' => $urls['units'] ?? '#', 'color' => '#e8604c'],
            ['label' => __('New Open Maint. (7d)'), 'value' => $metrics['openMaint7d'] ?? 0, 'url' => $urls['maintenance'] ?? '#', 'color' => '#ffab2d'],
        ];
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        @foreach($cards as $card)
            <div class="gz-stat-card">
                <div class="flex items-center gap-3">
                    <div class="gz-icon-box" style="background: {{ $card['color'] }}15;">
                        <svg class="w-5 h-5" style="color: {{ $card['color'] }};" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                    </div>
                    <div>
                        @if($card['url'])
                            <a href="{{ $card['url'] }}" target="_blank" class="text-xl font-bold text-[#1e1e2d] hover:underline">{{ $card['value'] }}</a>
                        @else
                            <div class="text-xl font-bold text-[#1e1e2d]">{{ $card['value'] }}</div>
                        @endif
                        <div class="text-xs text-[#7c8db5]">{{ $card['label'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Recent Activity --}}
    <div class="gz-widget">
        <div class="gz-widget-header">
            <h3 class="text-base font-bold text-[#1e1e2d]">{{ __('Recent Activity') }}</h3>
        </div>
        <div class="gz-widget-body">
            <div class="divide-y divide-[#e8ecf3]">
                @forelse(($recentActivities ?? []) as $activity)
                    @php
                        $ts = $activity->created_at instanceof \Illuminate\Support\Carbon
                            ? $activity->created_at
                            : \Illuminate\Support\Carbon::parse($activity->created_at);
                    @endphp
                    <div class="flex items-start justify-between gap-3 py-3">
                        <div>
                            <div class="font-semibold text-sm text-[#1e1e2d]">{{ $activity->title }}</div>
                            <div class="text-xs text-[#7c8db5]">{{ $activity->description }}</div>
                        </div>
                        <div class="whitespace-nowrap text-xs text-[#7c8db5]">{{ $ts->diffForHumans() }}</div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-sm text-[#7c8db5]">{{ __('No recent activity.') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-admin-layout>
