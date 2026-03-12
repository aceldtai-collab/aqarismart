@if(isset($title) || isset($subtitle))
<div class=" mb-8">
    @if(isset($title))
    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">{{ $title }}</h2>
    @endif
    @if(isset($subtitle))
    <p class="mt-4 text-lg text-gray-600">{{ $subtitle }}</p>
    @endif
</div>
@endif

<div class="mt-12 grid grid-cols-1 gap-y-6 sm:grid-cols-2 lg:grid-cols-{{ $displayinrow ?? 4 }}">
    @forelse($units as $unit)
        <x-unit-card :unit="$unit" />
    @empty
        <p class="col-span-full text-center text-gray-500">{{ __('No units available at the moment.') }}</p>
    @endforelse
</div>
