@extends('layouts.app')

@section('title', ($q ? ($q.' - '.($tenant->name ?? config('app.name'))) : ($tenant->name ?? config('app.name'))))

@section('content')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script defer src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    @php
        $visibleCount = $units->total() ?: 10000;
        $listingType = $listing_type ?? \App\Models\Unit::LISTING_RENT;
        $listingLabel = $listingType === \App\Models\Unit::LISTING_SALE ? __('for sale') : __('for rent');
        $maxLabel = $listingType === \App\Models\Unit::LISTING_SALE ? __('Max Price (USD)') : __('Max Rent (USD)');
    @endphp

    {{-- <header class="px-4 py-10 sm:py-12 bg-gradient-to-r from-indigo-600 via-indigo-500 to-indigo-600 text-white">
        <div class="mx-auto w-full max-w-4xl text-center space-y-3">
            <p class="text-sm sm:text-base text-white/90">{{ __('Fine-tune your search by price, layout, and amenities to discover spaces that match your lifestyle within seconds.') }}</p>
        </div>
    </header> --}}

    <main class="px-4 py-6 lg:px-8">
        <div class="flex flex-col-reverse gap-8 lg:flex-row lg:items-start">
            <aside class="hidden w-[370px] shrink-0 overflow-hidden rounded-xl bg-white lg:sticky lg:top-4 lg:block lg:max-h-[calc(100vh-2rem)]">
                <div id="filter-map" class="mb-4 h-48 w-full rounded shadow"></div>
                <form method="get" action="{{ route('tenant.search') }}" class="flex h-[calc(100%-theme(space.48)-theme(space.4))] flex-col rounded-lg bg-white p-4 shadow">
                    <div class="space-y-3 flex-1 overflow-y-auto pr-1">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Search') }}</label>
                            <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="{{ __('Property or unit code') }}" class="mt-1 block w-full rounded-md border-gray-300 focus:brand-border focus:brand-border" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Category') }}</label>
                            <select name="category" class="mt-1 block w-full rounded-md border-gray-300 focus:brand-border focus:brand-border" onchange="this.form.submit()">
                                <option value="0">{{ __('All') }}</option>
                                @foreach(($categories ?? []) as $c)
                                    <option value="{{ $c->id }}" @selected(($category ?? 0) == $c->id)>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Listing Type') }}</label>
                            <select name="listing_type" class="mt-1 block w-full rounded-md border-gray-300 focus:brand-border focus:brand-border">
                                @foreach(\App\Models\Unit::listingTypeLabels() as $value => $label)
                                    <option value="{{ $value }}" @selected(($listingType ?? \App\Models\Unit::LISTING_RENT) == $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Subcategory') }}</label>
                            <select name="subcategory" class="mt-1 block w-full rounded-md border-gray-300 focus:brand-border focus:brand-border">
                                <option value="0">{{ __('All') }}</option>
                                @foreach(($subcategories ?? []) as $s)
                                    <option value="{{ $s->id }}" @selected(($subcategory ?? 0) == $s->id)>{{ $s->name }} @if($s->category) ({{ $s->category->name }}) @endif</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Min Beds') }}</label>
                            <select name="beds" class="mt-1 block w-full rounded-md border-gray-300 focus:brand-border focus:brand-border">
                                @foreach([0=>__('Any'),1=>'1+',2=>'2+',3=>'3+',4=>'4+'] as $val => $label)
                                    <option value="{{ $val }}" @selected(($beds ?? 0) == $val)>@num($label)</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Min Baths') }}</label>
                            <select name="baths" class="mt-1 block w-full rounded-md border-gray-300 focus:brand-border focus:brand-border">
                                @foreach([0=>__('Any'),1=>'1+',1.5=>'1.5+',2=>'2+',2.5=>'2.5+',3=>'3+'] as $val => $label)
                                    <option value="{{ $val }}" @selected(($baths ?? 0) == $val)>@num($label)</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ $maxLabel }}</label>
                            <select name="max" class="mt-1 block w-full rounded-md border-gray-300 focus:brand-border focus:brand-border">
                                @foreach([0=>__('No limit'),1000=>'$1,000',1500=>'$1,500',2000=>'$2,000',2500=>'$2,500',3000=>'$3,000'] as $val => $label)
                                    <option value="{{ $val }}" @selected(($max ?? 0) == $val)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="pt-3 flex items-center gap-2">
                        <button class="inline-flex flex-1 items-center justify-center rounded-md brand-bg px-4 py-2 text-sm font-medium text-white shadow hover:opacity-90">{{ __('Search') }}</button>
                        <a href="{{ route('tenant.search') }}" class="inline-flex items-center justify-center rounded-md border px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ __('Clear') }}</a>
                    </div>
                </form>
            </aside>

            <div class="flex-1 w-full">
                <!-- Mobile Filters -->
                <section class="mb-6 w-full bg-white/95 p-4 shadow lg:hidden">
                    <form method="get" action="{{ route('tenant.search') }}" class="mx-auto grid w-full max-w-xl grid-cols-1 gap-3 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Search') }}</label>
                            <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="{{ __('Property or unit code') }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Category') }}</label>
                            <select name="category" class="mt-1 block w-full border-gray-300 focus:brand-border focus:brand-border">
                                <option value="0">{{ __('All') }}</option>
                                @foreach(($categories ?? []) as $c)
                                    <option value="{{ $c->id }}" @selected(($category ?? 0) == $c->id)>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Listing Type') }}</label>
                            <select name="listing_type" class="mt-1 block w-full border-gray-300 focus:brand-border focus:brand-border">
                                @foreach(\App\Models\Unit::listingTypeLabels() as $value => $label)
                                    <option value="{{ $value }}" @selected(($listingType ?? \App\Models\Unit::LISTING_RENT) == $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Subcategory') }}</label>
                            <select name="subcategory" class="mt-1 block w-full border-gray-300 focus:brand-border focus:brand-border">
                                <option value="0">{{ __('All') }}</option>
                                @foreach(($subcategories ?? []) as $s)
                                    <option value="{{ $s->id }}" @selected(($subcategory ?? 0) == $s->id)>{{ $s->name }} @if($s->category) ({{ $s->category->name }}) @endif</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Min Beds') }}</label>
                            <select name="beds" class="mt-1 block w-full border-gray-300 focus:brand-border focus:brand-border">
                                @foreach([0=>__('Any'),1=>'1+',2=>'2+',3=>'3+',4=>'4+'] as $val => $label)
                                    <option value="{{ $val }}" @selected(($beds ?? 0) == $val)>@num($label)</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Min Baths') }}</label>
                            <select name="baths" class="mt-1 block w-full border-gray-300 focus:brand-border focus:brand-border">
                                @foreach([0=>__('Any'),1=>'1+',1.5=>'1.5+',2=>'2+',2.5=>'2.5+',3=>'3+'] as $val => $label)
                                    <option value="{{ $val }}" @selected(($baths ?? 0) == $val)>@num($label)</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $maxLabel }}</label>
                            <select name="max" class="mt-1 block w-full border-gray-300 focus:brand-border focus:brand-border">
                                @foreach([0=>__('No limit'),1000=>'$1,000',1500=>'$1,500',2000=>'$2,000',2500=>'$2,500',3000=>'$3,000'] as $val => $label)
                                    <option value="{{ $val }}" @selected(($max ?? 0) == $val)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <button class="btn-accent inline-flex w-full items-center justify-center px-4 py-2 text-sm font-semibold">{{ __('Apply Filters') }}</button>
                        </div>
                    </form>
                </section>

                <div id="filter-map-mobile" class="mb-6 h-56 w-full shadow lg:hidden"></div>

                <header class="mb-1 flex flex-col gap-2 sm:flex-row sm:items-baseline sm:justify-between px-4 sm:px-6 lg:px-8">
                    <div>
                        {{-- <h1 class="text-2xl font-semibold text-gray-900 sm:text-3xl">{{ __('Search Results') }}</h1> --}}
            <h1 class="text-3xl  xl:text-4xl mb-2 font-bold text-gray-500">{{ number_format($visibleCount) }} {{ __('available listings') }} {{ $listingLabel }} {{ __('worldwide') }}</h1>

                        <p class="text-sm text-gray-500">{{ __('Showing :count results', ['count' => $units->total()]) }} @if($q) {{ __('for') }} "{{ $q }}" @endif</p>
                    </div>
                </header>

                @if($units->count() === 0)
                    <div class="bg-white p-6 text-center shadow">
                        <p class="text-gray-700">{{ __('No available units match your filters. Try adjusting your search or check back later.') }}</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($units as $u)
                            <x-unit-card :unit="$u" />
                        @endforeach
                    </div>
                    <div class="mt-6">
                        {{ $units->links() }}
                    </div>
                @endif
            </div>
        </div>
    </main>
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        if (window.L) {
          const desktopMapEl = document.getElementById('filter-map');
          if (desktopMapEl && window.getComputedStyle(desktopMapEl).display !== 'none') {
            const map = L.map(desktopMapEl).setView([31.9539, 35.9106], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
              maxZoom: 19,
              attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);
          }

          const mobileMapEl = document.getElementById('filter-map-mobile');
          if (mobileMapEl && window.getComputedStyle(mobileMapEl).display !== 'none') {
            const mobileMap = L.map(mobileMapEl).setView([31.9539, 35.9106], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
              maxZoom: 19,
              attribution: '&copy; OpenStreetMap contributors'
            }).addTo(mobileMap);
          }
        }
      });
    </script>
@endsection
