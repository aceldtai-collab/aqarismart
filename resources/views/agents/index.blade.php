<x-app-layout>
    @php
        $tenantCtx = app(\App\Services\Tenancy\TenantManager::class)->tenant();
    @endphp
    <x-slot name="header">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">{{ __('Agents') }}</h2>
            <p class="mt-1 text-xs text-slate-500">{{ __('Manage agents and their assignment details.') }}</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto space-y-6 sm:px-6 lg:px-8">
            <x-flash-status />
            
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">{{ __('Agents Management') }}</h1>
                    <p class="mt-1 text-sm text-slate-600">{{ __('Manage agents and their assignment details') }}</p>
                </div>
                @can('create', App\Models\Agent::class)
                    <a href="{{ $tenantCtx ? route('agents.create', request()->only('lang')) : route('admin.agents.create', request()->only('lang')) }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-lg focus:ring-2 focus:ring-offset-2 transition-all shadow-sm {{ $tenantCtx ? 'bg-gray-50 hover:bg-slate-800 focus:ring-slate-500' : 'bg-[#e8604c] hover:bg-[#d4503e] focus:ring-[#e8604c]' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        {{ __('Add New Agent') }}
                    </a>
                @endcan
            </div>
            
            <div class="bg-white rounded-xl border border-slate-200/60 shadow-sm overflow-hidden">
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200/70">
                    <thead class="bg-gradient-to-r from-slate-50 to-slate-100/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">{{ __('Name') }}</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">{{ __('Contact') }}</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">{{ __('License') }}</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">{{ __('Commission %') }}</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">{{ __('Status') }}</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-slate-600">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200/60 bg-white">
                        @forelse($agents as $agent)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-12 w-12 overflow-hidden rounded-full bg-gradient-to-br from-slate-100 to-slate-200">
                                            @if($agent->photo_url)
                                                <img src="{{ $agent->photo_url }}" alt="{{ $agent->name }}" class="h-full w-full object-cover" />
                                            @else
                                                <div class="flex h-full w-full items-center justify-center text-xs text-slate-400">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-semibold text-slate-900">{{ $agent->name }}</div>
                                            <div class="text-sm text-slate-500">{{ $agent->phone }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-slate-900">{{ $agent->email ?? '—' }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $agent->license_id ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                        {{ number_format((float) $agent->commission_rate, 2) }}%
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($agent->active)
                                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                            <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-1.5"></div>
                                            {{ __('Active') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                            <div class="w-1.5 h-1.5 bg-slate-500 rounded-full mr-1.5"></div>
                                            {{ __('Inactive') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center gap-2 justify-end">
                                        @can('update', $agent)
                                            <a href="{{ $tenantCtx ? route('agents.edit', $agent) : route('admin.agents.edit', $agent) }}" class="inline-flex items-center rounded-lg px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors">{{ __('Edit') }}</a>
                                        @endcan
                                        @can('delete', $agent)
                                            <form method="post" action="{{ $tenantCtx ? route('agents.destroy', $agent) : route('admin.agents.destroy', $agent) }}" class="inline" onsubmit="return confirm('{{ __('Delete agent?') }}')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="inline-flex items-center rounded-lg px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors">{{ __('Delete') }}</button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-16" colspan="6">
                                    <div class="text-center">
                                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-slate-100 to-slate-200 text-slate-500">
                                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"></path>
                                            </svg>
                                        </div>
                                        <h3 class="mt-4 text-lg font-semibold text-slate-900">{{ __('No agents yet') }}</h3>
                                        <p class="mt-2 text-sm text-slate-600 max-w-sm mx-auto">{{ __('Add agents to manage properties and handle client relationships.') }}</p>
                                        @can('create', App\Models\Agent::class)
                                            <a href="{{ $tenantCtx ? route('agents.create', request()->only('lang')) : route('admin.agents.create', request()->only('lang')) }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-gray-50 text-sm font-semibold rounded-lg hover:bg-slate-800 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                                {{ __('Add Your First Agent') }}
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
              </div>
            </div>
            <div class="mt-4">{{ $agents->links() }}</div>
        </div>
    </div>
</x-app-layout>
