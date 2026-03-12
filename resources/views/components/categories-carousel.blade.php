@php
    $tenant = app(\App\Services\Tenancy\TenantManager::class)->tenant();
    $scheme = request()->getScheme();
    $port = request()->getPort();
    $baseDomain = config('tenancy.base_domain');
    $demoSlug = config('tenancy.demo_slug', 'acme');
    $demoBase = $scheme.'://'.$demoSlug.'.'.$baseDomain.($port && ! in_array($port, [80, 443], true) ? ':' . $port : '');
@endphp

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
        @forelse($categories ?? [] as $category)
            @php
                $categoryName = $category->name ?? 'Category';
            @endphp
            <a
                href="{{ $tenant ? route('tenant.search', ['category' => $category->slug]) : $demoBase.'/search?category='.$category->slug }}"
                class="flex-shrink-0 w-56 sm:w-64 rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow"
            >
                <img
                    src="{{ $category->image ?? 'https://via.placeholder.com/300x200?text=' . urlencode($categoryName) }}"
                    alt="{{ $categoryName }}"
                    class="w-full h-32 sm:h-48 object-cover"
                >
                <div class="p-3 sm:p-4 bg-white">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900">{{ $categoryName }}</h3>
                <p class="text-xs sm:text-sm text-gray-600 mt-1">{{ __('Explore') }} {{ strtolower($categoryName) }}</p>
                </div>
            </a>
        @empty
            <div class="w-full text-center py-8">
                <p class="text-gray-500">{{ __('No property types available at the moment.') }}</p>
            </div>
        @endforelse
    </div>
</div>
