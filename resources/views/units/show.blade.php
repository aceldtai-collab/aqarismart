<x-app-layout>
    @php
        $statusPalette = config('status.palette', [
            'success' => ['text' => 'text-emerald-700', 'bg' => 'bg-emerald-50'],
            'warning' => ['text' => 'text-amber-700', 'bg' => 'bg-amber-50'],
            'danger' => ['text' => 'text-rose-700', 'bg' => 'bg-rose-50'],
            'info' => ['text' => 'text-sky-700', 'bg' => 'bg-sky-50'],
        ]);
        $statusTone = [
            'vacant' => 'warning',
            'occupied' => 'success',
            'draft' => 'info',
        ];
        $unitTitle = $unit->translated_title ?? $unit->title;
        if (is_array($unitTitle)) {
            $unitTitle = $unitTitle[app()->getLocale()] ?? ($unitTitle['en'] ?? reset($unitTitle));
        }
        $unitTitle = $unitTitle ?: __('Unit #:code', ['code' => $unit->code ?? '—']);
    @endphp
    <x-slot name="header">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">{{ __('Unit Details') }}</h2>
            <p class="mt-1 text-xs text-slate-500">{{ $unit->property?->name ?? __('Standalone unit') }}</p>
        </div>
    </x-slot>
    <x-slot name="headerActions">
        <div class="flex items-center gap-2">
            @can('update', $unit)
                <a href="{{ route('units.edit', $unit) }}" class="inline-flex items-center rounded-md bg-gray-50 px-3 py-2 text-xs font-semibold hover:bg-slate-800">
                    {{ __('Edit Unit') }}
                </a>
            @endcan
            @can('delete', $unit)
                <form method="POST" action="{{ route('units.destroy', $unit) }}" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                    @csrf @method('DELETE')
                    <button type="submit" class="inline-flex items-center rounded-md bg-red-50 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-100">
                        {{ __('Delete') }}
                    </button>
                </form>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto space-y-4 sm:px-6 lg:px-8">
            <div class="rounded-md bg-white shadow-sm divide-y divide-slate-200/60">
                <div class="px-6 py-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-semibold text-slate-900">{{ $unitTitle }}</h3>
                        @php
                            $tone = $statusTone[$unit->status] ?? 'info';
                            $toneStyles = $statusPalette[$tone];
                        @endphp
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold {{ $toneStyles['bg'] }} {{ $toneStyles['text'] }}">
                            {{ ucfirst($unit->status) }}
                        </span>
                    </div>
                    <dl class="mt-6 grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Property') }}</dt>
                            <dd class="mt-1 text-sm text-slate-700">{{ $unit->property?->name ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Subcategory') }}</dt>
                            <dd class="mt-1 text-sm text-slate-700">{{ $unit->subcategory?->name ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('City') }}</dt>
                            <dd class="mt-1 text-sm text-slate-700">{{ $unit->city?->name ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Area') }}</dt>
                            <dd class="mt-1 text-sm text-slate-700">{{ $unit->area?->name ?? 'N/A' }}</dd>
                        </div>
                        @if ($unit->price)
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Price') }}</dt>
                                <dd class="mt-1 text-sm text-slate-700">{{ number_format($unit->price, 2) }} {{ $unit->currency ?? 'JOD' }}</dd>
                            </div>
                        @endif
                        <div class="sm:col-span-2">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Location') }}</dt>
                            <dd class="mt-1 text-sm text-slate-700">
                                @if ($unit->lat && $unit->lng)
                                    <a href="https://maps.google.com/?q={{ $unit->lat }},{{ $unit->lng }}" target="_blank" class="text-slate-700 underline decoration-slate-300 underline-offset-4 hover:text-slate-900">
                                        {{ $unit->lat }}, {{ $unit->lng }}
                                    </a>
                                @else
                                    {{ __('N/A') }}
                                @endif
                            </dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Description') }}</dt>
                            <dd class="mt-1 text-sm text-slate-700">{{ $unit->description ?? __('No description provided.') }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="px-6 py-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Media') }}</h3>
                        <span class="text-xs text-slate-400">{{ __('Photos') }}</span>
                    </div>
                    @if ($unit->media && count($unit->media) > 0)
                        <div class="mt-4 grid grid-cols-2 gap-4 md:grid-cols-4">
                            @foreach ($unit->media as $photo)
                                @php ($path = is_string($photo) ? $photo : ($photo['path'] ?? null))
                                @if ($path)
                                    @php ($norm = (!filter_var($path, FILTER_VALIDATE_URL) && !Str::startsWith($path, 'storage/')) ? 'storage/'.$path : $path)
                                    @php ($src = filter_var($norm, FILTER_VALIDATE_URL) ? $norm : asset($norm))
                                    <img src="{{ $src }}" alt="Unit photo" class="h-36 w-full rounded-md object-cover" />
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="mt-4 rounded-md bg-slate-50 p-4">
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-full bg-white text-slate-500 shadow-sm ring-1 ring-slate-200/60">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 6.75h15M7.5 9.75h9M7.5 12.75h9M4.5 15.75h15" />
                                    </svg>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-slate-700">{{ __('No media uploaded yet.') }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ __('Add photos to improve listing quality.') }}</p>
                                    @can('update', $unit)
                                        <a href="{{ route('units.edit', $unit) }}" class="mt-2 inline-flex text-xs font-semibold text-slate-700 hover:text-slate-900">{{ __('Upload Photos') }}</a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="px-6 py-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Attributes') }}</h3>
                        <span class="text-xs text-slate-400">{{ __('Details') }}</span>
                    </div>
                    <div class="mt-4 space-y-4">
                        @foreach ($attributeFields as $field)
                            @php ($attr = $unitAttributes->get($field->id))
                            @if ($attr)
                                <div class="flex items-center justify-between rounded-md bg-slate-50 px-3 py-2">
                                    <span class="text-sm text-slate-700">{{ $field->label }}:</span>
                                    <span class="text-sm font-medium text-slate-900">
                                        @if ($attr->int_value !== null)
                                            {{ $attr->int_value }} {{ $field->unit ?? '' }}
                                        @elseif ($attr->decimal_value !== null)
                                            {{ number_format($attr->decimal_value, 2) }} {{ $field->unit ?? '' }}
                                        @elseif ($attr->string_value !== null)
                                            {{ $attr->string_value }}
                                        @elseif ($attr->bool_value !== null)
                                            {{ $attr->bool_value ? 'Yes' : 'No' }}
                                        @elseif ($attr->json_value !== null)
                                            @if (is_array($attr->json_value))
                                                {{ implode(', ', $attr->json_value) }}
                                            @else
                                                {{ $attr->json_value }}
                                            @endif
                                        @endif
                                    </span>
                                </div>
                            @endif
                        @endforeach
                        @if ($attributeFields->isEmpty())
                            <div class="rounded-md bg-slate-50 p-4">
                                <div class="flex items-start gap-3">
                                    <span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-full bg-white text-slate-500 shadow-sm ring-1 ring-slate-200/60">
                                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75h15M6 8.25h12M6 17.25h12" />
                                        </svg>
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-700">{{ __('No attributes defined for this subcategory.') }}</p>
                                        <p class="mt-1 text-sm text-slate-500">{{ __('Adjust the subcategory to load additional fields.') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
