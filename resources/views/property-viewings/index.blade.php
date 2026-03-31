<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">{{ __('Property Viewings') }}</h2>
            <p class="mt-1 text-xs text-slate-500">{{ __('Schedule and track upcoming appointments.') }}</p>
        </div>
    </x-slot>
    <x-slot name="headerActions">
        @can('create', App\Models\PropertyViewing::class)
            <a href="{{ route('property-viewings.create') }}" class="inline-flex items-center gap-2 rounded-md bg-gray-50 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-800">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4"><path fill-rule="evenodd" d="M10 3a1 1 0 0 1 1 1v5h5a1 1 0 1 1 0 2h-5v5a1 1 0 1 1-2 0v-5H4a1 1 0 1 1 0-2h5V4a1 1 0 0 1 1-1Z" clip-rule="evenodd"/></svg>
                <span>{{ __('Schedule Viewing') }}</span>
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
                            @foreach(\App\Models\PropertyViewing::statusLabels() as $value => $label)
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
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Lead') }}</th>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Property') }}</th>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Agent') }}</th>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Appointment') }}</th>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Status') }}</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($viewings as $viewing)
                            <tr>
                                <td class="px-4 py-2">
                                    <div class="font-medium text-gray-900">{{ $viewing->lead?->name ?? '—' }}</div>
                                    <div class="text-sm text-gray-500">
                                        {{ $viewing->lead?->phone ?? $viewing->lead?->email ?? '—' }}
                                    </div>
                                </td>
                                <td class="px-4 py-2">
                                    <div class="font-medium text-gray-900">{{ $viewing->property?->name ?? '—' }}</div>
                                </td>
                                <td class="px-4 py-2">{{ $viewing->agent?->name ?? '—' }}</td>
                                <td class="px-4 py-2">
                                    {{ optional($viewing->appointment_at)->format('M d, Y H:i') ?? '—' }}
                                </td>
                                <td class="px-4 py-2">
                                    <span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-700">
                                        {{ \Illuminate\Support\Str::headline($viewing->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-right">
                                    @can('update', $viewing)
                                        <a href="{{ route('property-viewings.edit', ['tenant_slug' => request()->route('tenant_slug'), 'property_viewing' => $viewing->getKey()]) }}" class="text-indigo-600 hover:underline">{{ __('Edit') }}</a>
                                    @endcan
                                    @can('delete', $viewing)
                                        <form method="post" action="{{ route('property-viewings.destroy', ['tenant_slug' => request()->route('tenant_slug'), 'property_viewing' => $viewing->getKey()]) }}" class="inline-block" onsubmit="return confirm('{{ __('Delete viewing?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="ml-3 text-red-600 hover:underline">{{ __('Delete') }}</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-4 py-6 text-center text-gray-500" colspan="6">{{ __('No viewings scheduled.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $viewings->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
