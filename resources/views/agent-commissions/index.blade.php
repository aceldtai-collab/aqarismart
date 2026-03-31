<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">{{ __('Agent Commissions') }}</h2>
            <p class="mt-1 text-xs text-slate-500">{{ __('Track commission statuses and payouts.') }}</p>
        </div>
    </x-slot>
    <x-slot name="headerActions">
        @can('create', App\Models\AgentCommission::class)
            <a href="{{ route('agent-commissions.create') }}" class="inline-flex items-center gap-2 rounded-md bg-gray-50 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-800">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4"><path fill-rule="evenodd" d="M10 3a1 1 0 0 1 1 1v5h5a1 1 0 1 1 0 2h-5v5a1 1 0 1 1-2 0v-5H4a1 1 0 1 1 0-2h5V4a1 1 0 0 1 1-1Z" clip-rule="evenodd"/></svg>
                <span>{{ __('Record Commission') }}</span>
            </a>
        @endcan
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <x-flash-status />

            <div class="mb-4 rounded-md bg-white p-4 shadow">
                <form method="get" class="flex flex-wrap items-end gap-3">
                    <div>
                        <x-input-label for="status" :value="__('Status')" />
                        <select id="status" name="status" class="mt-1 block w-48 rounded-md border-gray-300">
                            <option value="">{{ __('All') }}</option>
                            @foreach(\App\Models\AgentCommission::statusLabels() as $value => $label)
                                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-white hover:bg-indigo-700">{{ __('Filter') }}</button>
                    </div>
                </form>
            </div>

            <div class="overflow-hidden rounded-md bg-white shadow">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Agent') }}</th>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Lease') }}</th>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Amount') }}</th>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Rate') }}</th>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Status') }}</th>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Created') }}</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($commissions as $commission)
                            <tr>
                                <td class="px-4 py-2">{{ $commission->agent?->name ?? '—' }}</td>
                                <td class="px-4 py-2">
                                    @if($commission->lease)
                                        <div class="font-medium text-gray-900">{{ __('Lease #:id', ['id' => $commission->lease->id]) }}</div>
                                        <div class="text-sm text-gray-500">
                                            {{ $commission->lease->start_date?->format('Y-m-d') ?? '—' }}
                                        </div>
                                    @else
                                        <span class="text-gray-500">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2">
                                    {{ number_format((float) $commission->amount, 2) }}
                                </td>
                                <td class="px-4 py-2">
                                    {{ is_null($commission->rate) ? '—' : number_format((float) $commission->rate, 2).'%' }}
                                </td>
                                <td class="px-4 py-2">
                                    <span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-700">
                                        {{ \Illuminate\Support\Str::headline($commission->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2">
                                    {{ optional($commission->created_at)->format('M d, Y') ?? '—' }}
                                </td>
                                <td class="px-4 py-2 text-right">
                                    @can('update', $commission)
                                        <a href="{{ route('agent-commissions.edit', $commission) }}" class="text-indigo-600 hover:underline">{{ __('Edit') }}</a>
                                    @endcan
                                    @can('delete', $commission)
                                        <form method="post" action="{{ route('agent-commissions.destroy', $commission) }}" class="inline-block" onsubmit="return confirm('{{ __('Delete commission?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="ml-3 text-red-600 hover:underline">{{ __('Delete') }}</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-4 py-6 text-center text-gray-500" colspan="7">{{ __('No commissions recorded yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $commissions->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
