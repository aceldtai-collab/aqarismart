@extends('layouts.app')

@section('title', $tenant->name . ' - ' . __('Home'))

@section('content')
    @php
        $theme = $tenant->settings ?? [];
        $registerModalShouldOpen = old('register_intent') === 'resident'
            || $errors->hasAny(['name','country_code','phone','email','password','password_confirmation']);
        $loginModalShouldOpen = old('login_intent') === 'login'
            || (! $registerModalShouldOpen && $errors->hasAny(['login','password']));
        $countryCodes = config('phone.codes', []);
        $defaultCountry = config('phone.default', '+962');
        if (empty($countryCodes)) {
            $countryCodes = [$defaultCountry => $defaultCountry];
        }
        $headerBg = $theme['header_bg_url'] ?? null;
        $headerStyle = $headerBg
            ? "background-image: url('" . (\Illuminate\Support\Str::startsWith($headerBg, ['http://', 'https://']) ? $headerBg : asset($headerBg)) . "'); background-size: cover; background-position: center;"
            : 'background: linear-gradient(135deg, var(--brand), color-mix(in srgb, var(--brand) 80%, white 20%));';
        $availableCount = number_format($units->total());
    @endphp
    <style>[x-cloak]{ display:none !important; }</style>
    <div x-data>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script defer src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <header
        class="relative isolate flex w-full items-center justify-center overflow-hidden min-h-[60vh] sm:min-h-[70vh] lg:min-h-[80vh] px-4 sm:px-6"
        style="{{ $headerStyle }}">
        <div class="absolute inset-0 bg-black/30"></div>
        <div class="absolute inset-0 opacity-5"
            style="background-image: radial-gradient(white 1px, transparent 1px); background-size: 30px 30px;"></div>

        <div class="mx-auto w-full max-w-6xl text-white relative z-10">
            <div
                class="flex w-full flex-col items-center justify-center space-y-4 sm:space-y-6 lg:space-y-8 rounded-2xl sm:rounded-3xl bg-black/20 backdrop-blur-md px-4 py-8 sm:px-8 sm:py-12 lg:px-12 lg:py-16 border border-white/10">
                <h1
                    class="text-center text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-bold leading-tight text-white drop-shadow-2xl">
                    {{ __('Find Your Perfect Home') }}
                </h1>
                <p class="max-w-2xl text-center text-sm sm:text-base lg:text-lg text-white/90 leading-relaxed px-2">
                    {{ __('Find homes and units available in your community and connect with leasing instantly.') }}
                </p>
                <div class="w-full max-w-2xl px-2">
                    <form method="get" action="{{ route('tenant.search') }}" class="relative w-full">
                        <div class="pointer-events-none absolute inset-y-0 left-4 flex items-center">
                            <svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"
                                class="text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                        </div>
                        <input type="search" name="q" placeholder="{{ __('Search rentals...') }}"
                            class="block w-full rounded-xl sm:rounded-2xl border-0 bg-white/95 backdrop-blur-sm py-3 sm:py-4 pl-12 pr-16 text-gray-900 ring-1 ring-inset ring-white/20 placeholder:text-gray-500 focus:ring-2 focus:ring-inset focus:ring-white/50 text-sm sm:text-base shadow-xl transition-all duration-200 hover:bg-white focus:bg-white"
                            value="{{ request('q') }}">
                        <button type="submit"
                            class="absolute inset-y-0 right-2 flex items-center rounded-lg sm:rounded-xl px-3 sm:px-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-white/50 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                            <svg class="h-4 w-4 sm:h-5 sm:w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </form>
                </div>
                @guest
                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 w-full max-w-md">
                        <button @click="$store.auth.login = true" 
                            class="flex-1 bg-white/10 backdrop-blur-sm text-white border border-white/30 px-6 py-3 rounded-xl font-semibold hover:bg-white/20 transition-all duration-200 text-sm sm:text-base">
                            {{ __('Sign In') }}
                        </button>
                        <button @click="$store.auth.register = true" 
                            class="flex-1 bg-white text-gray-900 px-6 py-3 rounded-xl font-semibold hover:bg-white/90 transition-all duration-200 shadow-lg hover:shadow-xl text-sm sm:text-base">
                            {{ __('Get Started') }}
                        </button>
                    </div>
                @endguest
            </div>
        </div>
    </header>

    @if($tenant->settings['home_show_types'] ?? false)
    <x-subcategories-scroller :subcategories="$types" />
    @endif

    @if($tenant->settings['home_show_cities'] ?? false)
    <!-- Most Popular Cities Section -->
    <x-cities-carousel :cities="$popularCities ?? collect()" :title="__('most_popular_cities_title')" :subtitle="__('most_popular_cities_subtitle')" />
    @endif

    <main class="py-8 sm:py-12 lg:py-16">
        @if($tenant->settings['home_show_latest'] ?? true)
        <!-- Featured Units Section -->
        <section class="w-full">
            <div class="text-center mb-8 sm:mb-12 px-4 sm:px-6 lg:px-8">
                <div class="inline-flex items-center gap-3 px-4 sm:px-6 py-2 sm:py-3 bg-gradient-to-r from-emerald-50 to-blue-50 rounded-full border border-emerald-200/50 mb-6">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                        <span class="text-xs sm:text-sm font-semibold text-emerald-700">{{ __('Live Updates') }}</span>
                    </div>
                    <div class="w-px h-3 sm:h-4 bg-emerald-300"></div>
                    <span class="text-xs sm:text-sm font-bold text-gray-900">@num($availableCount) {{ __('Properties Available') }}</span>
                </div>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-3 sm:mb-4">
                    {{ __('Latest rental listings') }}
                </h2>
                <p class="text-base sm:text-lg text-gray-600 max-w-2xl mx-auto">
                    {{ __('Discover rentals updated list') }}
                </p>
            </div>
            <x-units-cards :units="$units" :title="''" :subtitle="''" :displayinrow="4" />
        </section>
        @endif
    </main>

    @if($tenant->settings['home_show_search'] ?? true)
    <section class="w-full py-8 sm:py-12 lg:py-16 px-4 sm:px-6 lg:px-8"
        style="background: linear-gradient(135deg, var(--brand), color-mix(in srgb, var(--brand) 80%, white 20%));">
        <div class="mx-auto max-w-7xl">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-6 lg:gap-12 text-center lg:text-left">
                <div class="flex-1 space-y-4">
                    <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold text-white leading-tight">
                        {{ __('tenant_home.why_wait_start_search_now') }}
                    </h2>
                    <p class="text-base sm:text-lg text-white/90 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                        {{__('Aqari Smart gathers the whole rental market in a single search. Never miss your dream rental home again.')}}
                    </p>
                </div>
                <div class="flex-shrink-0">
                    <a href="{{ route('tenant.search') }}"
                        class="inline-flex items-center justify-center bg-white text-gray-900 font-semibold hover:bg-white/95 px-6 sm:px-8 py-3 sm:py-4 rounded-xl sm:rounded-2xl shadow-xl hover:shadow-2xl text-sm sm:text-base transition-all duration-200 transform hover:scale-105 min-w-[140px] sm:min-w-[160px]">
                        {{__('search_now')}}
                        <svg class="ml-2 w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>
    @endif

    @if($tenant->settings['home_show_map'] ?? true)
    <!-- Map Section -->
    <section class="mx-auto mt-12 sm:mt-16 lg:mt-20 max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8 sm:mb-12">
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight text-gray-900 mb-3 sm:mb-4">
                {{ __('Our Locations') }}
            </h2>
            <p class="text-base sm:text-lg text-gray-600 max-w-2xl mx-auto">
                {{ __('Explore properties on the map') }}
            </p>
        </div>
        <div class="relative overflow-hidden rounded-xl sm:rounded-2xl lg:rounded-3xl shadow-2xl">
            <div id="map" class="h-64 sm:h-80 lg:h-96 w-full"></div>
            <div class="absolute inset-0 ring-1 ring-inset ring-black/10 rounded-xl sm:rounded-2xl lg:rounded-3xl pointer-events-none"></div>
        </div>
    </section>
    @endif

    @guest
    @endguest

    <x-tenant-footer :tenant="$tenant" />

    <script>
        let mapInstance = null;

        // Initialize Leaflet Map
        document.addEventListener('DOMContentLoaded', function() {
            mapInstance = L.map('map').setView([{{ $tenant->latitude ?? 31.9515 }},
                {{ $tenant->longitude ?? 35.9349 }}
            ], 12); // Default to Amman, Jordan or tenant coords

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(mapInstance);

            // Add central marker for tenant location
            L.marker([{{ $tenant->latitude ?? 31.9515 }}, {{ $tenant->longitude ?? 35.9349 }}]).addTo(mapInstance)
                .bindPopup('{{ $tenant->name ?? __('tenant_home.our_location') }}');

            // Add markers for properties if available
            @if (isset($properties) && $properties->count())
                @foreach ($properties as $property)
                    L.marker([{{ $property->latitude }}, {{ $property->longitude }}]).addTo(mapInstance)
                        .bindPopup('{{ $property->name }}');
                @endforeach
            @endif
        });

        function showUnitInMap(lat, lng) {
            if (mapInstance) {
                mapInstance.setView([lat, lng], 16);
            }
            document.querySelector('#map').scrollIntoView({
                behavior: 'smooth'
            });
        }
    </script>
</div>
@endsection
