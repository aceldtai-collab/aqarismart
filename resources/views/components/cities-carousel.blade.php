@if(isset($title) || isset($subtitle))
    <div class="mx-auto px-4 sm:px-6 lg:px-8 my-8">
        @isset($title)
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">{{ $title }}</h2>
        @endisset
        @isset($subtitle)
            <p class="mt-4 text-lg text-gray-600">{{ $subtitle }}</p>
        @endisset
    </div>
@endif

<div class="w-full overflow-x-auto">
    <div class="flex flex-nowrap gap-4 px-4 sm:px-6 lg:px-8">
        @forelse($cities ?? [] as $city)
        <a href="{{ $city['link'] ?? '#' }}" class="flex-shrink-0 w-56 sm:w-64 rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow">
            <img src="{{ $city['image'] ?? 'https://via.placeholder.com/300x200' }}" alt="{{ $city['name'] ?? 'City' }}" class="w-full h-32 sm:h-48 object-cover">
            <div class="p-3 sm:p-4 bg-white">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900">{{ $city['name'] ?? 'City' }}</h3>
                <p class="text-xs sm:text-sm text-gray-600 mt-1">{{ __('explore_rentals') }}</p>
            </div>
        </a>
        @empty
        <div class="w-full text-center py-8">
            <p class="text-gray-500">{{ __('no_popular_cities_available') }}</p>
        </div>
        @endforelse
    </div>
</div>
