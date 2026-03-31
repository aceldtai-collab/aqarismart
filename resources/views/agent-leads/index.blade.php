<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">{{ __('Agent Leads') }}</h2>
            <p class="mt-1 text-xs text-slate-500">{{ __('Track prospects and pipeline progress.') }}</p>
        </div>
    </x-slot>
    <x-slot name="headerActions">
        @can('create', App\Models\AgentLead::class)
            <a href="{{ route('agent-leads.create') }}" class="inline-flex items-center gap-2 rounded-md bg-gray-50 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-800">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4"><path fill-rule="evenodd" d="M10 3a1 1 0 0 1 1 1v5h5a1 1 0 1 1 0 2h-5v5a1 1 0 1 1-2 0v-5H4a1 1 0 1 1 0-2h5V4a1 1 0 0 1 1-1Z" clip-rule="evenodd"/></svg>
                <span>{{ __('New Lead') }}</span>
            </a>
        @endcan
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <x-flash-status />
            <div class="mb-4 rounded-md bg-white p-4 shadow">
                <form method="get" class="flex flex-wrap items-end gap-3">
                    <div>
                        <x-input-label for="status" value="{{ __('Status') }}" />
                        <select id="status" name="status" class="mt-1 block w-48 rounded-md border-gray-300">
                            <option value="">{{ __('All') }}</option>
                            @foreach(\App\Models\AgentLead::statusLabels() as $value => $label)
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
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Name') }}</th>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Agent') }}</th>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Status') }}</th>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Source') }}</th>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Contact') }}</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($leads as $lead)
                            <tr>
                                <td class="px-4 py-2">
                                    <div class="font-medium text-gray-900">{{ $lead->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $lead->email ?? '—' }}</div>
                                </td>
                                <td class="px-4 py-2">{{ $lead->agent?->name ?? '—' }}</td>
                                <td class="px-4 py-2">
                                    <span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-700">{{ \Illuminate\Support\Str::headline($lead->status) }}</span>
                                </td>
                                <td class="px-4 py-2">{{ $lead->source ?? '—' }}</td>
                                <td class="px-4 py-2">{{ $lead->phone ?? '—' }}</td>
                                <td class="px-4 py-2 text-right">
                                    @can('update', $lead)
                                        <a href="{{ route('agent-leads.edit', $lead) }}" class="text-indigo-600 hover:underline">{{ __('Edit') }}</a>
                                    @endcan
                                    @can('delete', $lead)
                                        <form method="post" action="{{ route('agent-leads.destroy', $lead) }}" class="inline-block" onsubmit="return confirm('{{ __('Delete lead?') }}')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="ml-3 text-red-600 hover:underline">{{ __('Delete') }}</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-4 py-6 text-center text-gray-500" colspan="6">{{ __('No leads yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $leads->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
