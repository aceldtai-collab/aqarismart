@props(['entries' => ['resources/css/app.css', 'resources/js/app.js']])

@php
    $entries = is_array($entries) ? $entries : [$entries];
    $hotPath = public_path('hot');
    $manifestPath = public_path('build/manifest.json');
    $useVite = file_exists($hotPath) || file_exists($manifestPath);
@endphp

@if($useVite)
    @vite($entries)
@else
    <link rel="stylesheet" href="{{ asset('build/assets/app.css') }}">
    <script type="module" src="{{ asset('build/assets/app.js') }}"></script>
@endif
