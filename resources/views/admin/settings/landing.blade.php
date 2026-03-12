@extends('layouts.admin')

@section('title', __('Public Landing Settings'))

@section('content')
<div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="space-y-6">
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200/60">
            <h1 class="text-2xl font-semibold text-gray-900">{{ __('Public Landing Settings') }}</h1>
            <p class="mt-1 text-sm text-gray-600">{{ __('Update brand assets and key copy without JavaScript or Livewire.') }}</p>
        </div>

        @if (session('status'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
                {{ __('Please review the highlighted fields and try again.') }}
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">
            <div class="space-y-6">
                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200/60 space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">{{ __('Brand Assets') }}</h2>
                    </div>
                    <form method="POST" action="{{ route('admin.settings.landing.updateSection', 'assets') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('Logo') }}</label>
                                <input id="logoInput" type="file" name="logo" accept="image/*" class="mt-1 w-full text-sm" />
                                <div id="logoPreview" class="mt-2 hidden">
                                    <img src="" alt="logo preview" class="h-12 w-12 rounded border object-contain bg-white">
                                </div>
                                @if(!empty($current['assets']['logo_url']))
                                    <div class="mt-2 relative inline-block">
                                        <img src="{{ $current['assets']['logo_url'] }}" class="h-12 w-12 rounded border object-contain bg-white" alt="logo" />
                                        <button type="submit" name="remove_logo" value="1" class="absolute -right-2 -top-2 inline-flex h-6 w-6 items-center justify-center rounded-full bg-rose-600 text-white shadow hover:bg-rose-700" title="{{ __('Remove logo') }}" aria-label="{{ __('Remove logo') }}">&times;</button>
                                    </div>
                                @endif
                                @error('logo')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('Hero Image') }}</label>
                                <input id="heroInput" type="file" name="hero" accept="image/*" class="mt-1 w-full text-sm" />
                                <div id="heroPreview" class="mt-2 hidden">
                                    <img src="" alt="hero preview" class="h-20 rounded border object-cover bg-white">
                                </div>
                                @if(!empty($current['assets']['hero_image']))
                                    <div class="mt-2 relative inline-block">
                                        <img src="{{ $current['assets']['hero_image'] }}" class="h-20 rounded border object-cover bg-white" alt="hero" />
                                        <button type="submit" name="remove_hero" value="1" class="absolute -right-2 -top-2 inline-flex h-6 w-6 items-center justify-center rounded-full bg-rose-600 text-white shadow hover:bg-rose-700" title="{{ __('Remove hero') }}" aria-label="{{ __('Remove hero') }}">&times;</button>
                                    </div>
                                @endif
                                @error('hero')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('Auth Screenshot') }}</label>
                                <p class="text-xs text-gray-400 mt-0.5">{{ __('Shown on login & register pages as a product preview.') }}</p>
                                <input id="authScreenshotInput" type="file" name="auth_screenshot" accept="image/*" class="mt-1 w-full text-sm" />
                                <div id="authScreenshotPreview" class="mt-2 hidden">
                                    <img src="" alt="auth screenshot preview" class="h-20 rounded border object-cover bg-white">
                                </div>
                                @if(!empty($current['assets']['auth_screenshot']))
                                    <div class="mt-2 relative inline-block">
                                        <img src="{{ $current['assets']['auth_screenshot'] }}" class="h-20 rounded border object-cover bg-white" alt="auth screenshot" />
                                        <button type="submit" name="remove_auth_screenshot" value="1" class="absolute -right-2 -top-2 inline-flex h-6 w-6 items-center justify-center rounded-full bg-rose-600 text-white shadow hover:bg-rose-700" title="{{ __('Remove') }}" aria-label="{{ __('Remove auth screenshot') }}">&times;</button>
                                    </div>
                                @endif
                                @error('auth_screenshot')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">{{ __('Gallery Images') }}</label>
                                <input id="galleryInput" type="file" name="gallery[]" accept="image/*" multiple class="mt-1 w-full text-sm" />
                                <div id="galleryPreview" class="mt-2 grid gap-3 md:grid-cols-3 hidden"></div>
                                @error('gallery')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                                @error('gallery.*')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                                <div class="mt-2 grid gap-3 md:grid-cols-3">
                                    @foreach (Arr::get($current, 'assets.feature_images.gallery', []) as $idx => $image)
                                        <div class="relative overflow-hidden rounded border bg-white">
                                            <img src="{{ $image }}" class="h-28 w-full object-cover" alt="gallery image">
                                            <button type="submit" name="remove_gallery" value="{{ $idx }}" class="absolute right-2 top-2 inline-flex h-7 w-7 items-center justify-center rounded-full bg-rose-600 text-white shadow hover:bg-rose-700" title="{{ __('Remove') }}" aria-label="{{ __('Remove image') }}">&times;</button>
                                        </div>
                                    @endforeach
                                    @if (empty(Arr::get($current, 'assets.feature_images.gallery', [])))
                                        <div class="col-span-full text-xs text-gray-400">{{ __('No gallery images uploaded yet.') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <button class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700" type="submit">{{ __('Save Assets') }}</button>
                        </div>
                    </form>
                </section>

                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200/60 space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">{{ __('Video Section') }}</h2>
                    </div>
                    <form method="POST" action="{{ route('admin.settings.landing.updateSection', 'video') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('Headline (EN)') }}</label>
                                <input type="text" name="video[headline][en]" value="{{ Arr::get($current, 'video.headline.en', '') }}" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('Headline (AR)') }}</label>
                                <input type="text" name="video[headline][ar]" value="{{ Arr::get($current, 'video.headline.ar', '') }}" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" dir="rtl" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('Description (EN)') }}</label>
                                <input type="text" name="video[description][en]" value="{{ Arr::get($current, 'video.description.en', '') }}" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('Description (AR)') }}</label>
                                <input type="text" name="video[description][ar]" value="{{ Arr::get($current, 'video.description.ar', '') }}" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" dir="rtl" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('YouTube URL') }}</label>
                                <input type="url" name="video[youtube_url]" value="{{ Arr::get($current, 'video.youtube_url', '') }}" placeholder="https://www.youtube.com/watch?v=..." class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                                <p class="text-xs text-gray-400 mt-1">{{ __('Paste a full YouTube URL. The video section only appears when a URL is set.') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('Video Poster Image') }}</label>
                                <input type="file" name="video_poster" accept="image/*" class="mt-1 w-full text-sm" />
                                @if(!empty($current['video']['poster_image']))
                                    <div class="mt-2 relative inline-block">
                                        <img src="{{ $current['video']['poster_image'] }}" class="h-20 rounded border object-cover bg-white" alt="poster" />
                                        <button type="submit" name="remove_video_poster" value="1" class="absolute -right-2 -top-2 inline-flex h-6 w-6 items-center justify-center rounded-full bg-rose-600 text-white shadow hover:bg-rose-700" title="{{ __('Remove') }}">&times;</button>
                                    </div>
                                @endif
                                @error('video_poster')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div class="text-right">
                            <button class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700" type="submit">{{ __('Save Video') }}</button>
                        </div>
                    </form>
                </section>

                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200/60 space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">{{ __('Meta') }}</h2>
                    </div>
                    <form method="POST" action="{{ route('admin.settings.landing.updateSection', 'meta') }}" class="space-y-4">
                        @csrf
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('Page Title (EN)') }}</label>
                                <input type="text" name="meta[title][en]" value="{{ Arr::get($current, 'meta.title.en', '') }}" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                                @error('meta.title.en')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('Page Title (AR)') }}</label>
                                <input type="text" name="meta[title][ar]" value="{{ Arr::get($current, 'meta.title.ar', '') }}" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                                @error('meta.title.ar')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div class="text-right">
                            <button class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700" type="submit">{{ __('Save Meta') }}</button>
                        </div>
                    </form>
                </section>

                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200/60 space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">{{ __('Hero') }}</h2>
                    </div>
                    <form method="POST" action="{{ route('admin.settings.landing.updateSection', 'hero') }}" class="space-y-4">
                        @csrf
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('Headline (EN)') }}</label>
                                <input type="text" name="hero[headline][en]" value="{{ Arr::get($current, 'hero.headline.en', '') }}" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                                @error('hero.headline.en')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('Headline (AR)') }}</label>
                                <input type="text" name="hero[headline][ar]" value="{{ Arr::get($current, 'hero.headline.ar', '') }}" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">{{ __('Subheadline (EN)') }}</label>
                                <input type="text" name="hero[subheadline][en]" value="{{ Arr::get($current, 'hero.subheadline.en', '') }}" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                                @error('hero.subheadline.en')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">{{ __('Subheadline (AR)') }}</label>
                                <input type="text" name="hero[subheadline][ar]" value="{{ Arr::get($current, 'hero.subheadline.ar', '') }}" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                            </div>
                        </div>
                        <div class="text-right">
                            <button class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700" type="submit">{{ __('Save Hero') }}</button>
                        </div>
                    </form>
                </section>

                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200/60 space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">{{ __('CTA') }}</h2>
                    </div>
                    <form method="POST" action="{{ route('admin.settings.landing.updateSection', 'cta') }}" class="space-y-4">
                        @csrf
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('Primary Label (EN)') }}</label>
                                <input type="text" name="cta[primary][label][en]" value="{{ Arr::get($current, 'cta.primary.label.en', '') }}" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                                @error('cta.primary.label.en')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('Primary Link') }}</label>
                                <input type="text" name="cta[primary][href]" value="{{ Arr::get($current, 'cta.primary.href', '') }}" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                                @error('cta.primary.href')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('Secondary Label (EN)') }}</label>
                                <input type="text" name="cta[secondary][label][en]" value="{{ Arr::get($current, 'cta.secondary.label.en', '') }}" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('Secondary Link') }}</label>
                                <input type="text" name="cta[secondary][href]" value="{{ Arr::get($current, 'cta.secondary.href', '') }}" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                            </div>
                        </div>
                        <div class="text-right">
                            <button class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700" type="submit">{{ __('Save CTA') }}</button>
                        </div>
                    </form>
                </section>

                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200/60 space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">{{ __('SEO') }}</h2>
                    </div>
                    <form method="POST" action="{{ route('admin.settings.landing.updateSection', 'seo') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('SEO Title (EN)') }}</label>
                                <input type="text" name="seo[title][en]" value="{{ Arr::get($current, 'seo.title.en', '') }}" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('SEO Title (AR)') }}</label>
                                <input type="text" name="seo[title][ar]" value="{{ Arr::get($current, 'seo.title.ar', '') }}" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">{{ __('Meta Description (EN)') }}</label>
                                <textarea name="seo[description][en]" rows="3" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">{{ Arr::get($current, 'seo.description.en', '') }}</textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">{{ __('Meta Description (AR)') }}</label>
                                <textarea name="seo[description][ar]" rows="3" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">{{ Arr::get($current, 'seo.description.ar', '') }}</textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">{{ __('Robots') }}</label>
                                <input type="text" name="seo[robots]" value="{{ Arr::get($current, 'seo.robots', 'index, follow') }}" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" />
                            </div>
                        </div>
                        <div class="grid gap-4 md:grid-cols-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('OpenGraph Image') }}</label>
                                <input type="file" name="seo_og" accept="image/*" class="mt-1 block w-full text-sm" />
                                @if(Arr::get($current, 'seo.og_image'))
                                    <div class="mt-2 relative inline-block">
                                        <img src="{{ Arr::get($current, 'seo.og_image') }}" class="h-16 rounded border object-cover bg-white" alt="">
                                        <button type="submit" name="remove_seo_og" value="1" class="absolute -right-2 -top-2 inline-flex h-6 w-6 items-center justify-center rounded-full bg-rose-600 text-white shadow hover:bg-rose-700" title="{{ __('Remove') }}">&times;</button>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('Twitter Image') }}</label>
                                <input type="file" name="seo_twitter" accept="image/*" class="mt-1 block w-full text-sm" />
                                @if(Arr::get($current, 'seo.twitter_image'))
                                    <div class="mt-2 relative inline-block">
                                        <img src="{{ Arr::get($current, 'seo.twitter_image') }}" class="h-16 rounded border object-cover bg-white" alt="">
                                        <button type="submit" name="remove_seo_twitter" value="1" class="absolute -right-2 -top-2 inline-flex h-6 w-6 items-center justify-center rounded-full bg-rose-600 text-white shadow hover:bg-rose-700" title="{{ __('Remove') }}">&times;</button>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('Favicon') }}</label>
                                <input type="file" name="seo_favicon" accept="image/*" class="mt-1 block w-full text-sm" />
                                @if(Arr::get($current, 'seo.favicon'))
                                    <div class="mt-2 relative inline-block">
                                        <img src="{{ Arr::get($current, 'seo.favicon') }}" class="h-10 w-10 rounded border object-contain bg-white" alt="">
                                        <button type="submit" name="remove_seo_favicon" value="1" class="absolute -right-2 -top-2 inline-flex h-6 w-6 items-center justify-center rounded-full bg-rose-600 text-white shadow hover:bg-rose-700" title="{{ __('Remove') }}">&times;</button>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <button class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700" type="submit">{{ __('Save SEO') }}</button>
                        </div>
                    </form>
                </section>

                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200/60 space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">{{ __('Advanced JSON Editor') }}</h2>
                    </div>
                    <form method="POST" action="{{ route('admin.settings.landing.update') }}" class="space-y-3">
                        @csrf
                        <textarea class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm" name="payload" rows="16" spellcheck="false">{{ old('payload', $currentJson) }}</textarea>
                        <div class="text-right">
                            <button class="inline-flex items-center rounded-lg bg-gray-700 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800" type="submit">{{ __('Save JSON') }}</button>
                        </div>
                    </form>
                </section>
            </div>

            <aside class="space-y-5">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200/60">
                    <h2 class="text-base font-semibold text-gray-900">{{ __('Structure Overview (defaults)') }}</h2>
                    <div class="mt-4 rounded-xl border border-gray-200 bg-slate-950/5 p-4">
                        <pre class="max-h-[520px] overflow-auto text-xs leading-relaxed text-slate-800">{{ $defaultsJson }}</pre>
                    </div>
                </div>
            </aside>
        </div>

        <div class="mt-8 grid gap-6">
            <!-- Navigation form -->
            <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200/60 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">{{ __('Navigation') }}</h2>
                    <button type="button" class="rounded-md border px-3 py-1 text-sm" onclick="addNavItem()">+ {{ __('Add Link') }}</button>
                </div>
                <form method="POST" action="{{ route('admin.settings.landing.updateSection', 'navigation') }}" class="space-y-3">
                    @csrf
                    <div id="nav-items" class="space-y-3">
                        @foreach(($current['navigation'] ?? []) as $i => $item)
                        <div class="grid gap-3 md:grid-cols-4 border rounded-md p-3">
                            <div>
                                <label class="block text-xs text-gray-600">{{ __('Label (EN)') }}</label>
                                <input class="w-full rounded border px-2 py-1" name="navigation[{{ $i }}][label][en]" value="{{ $item['label']['en'] ?? '' }}" />
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600">{{ __('Label (AR)') }}</label>
                                <input class="w-full rounded border px-2 py-1" name="navigation[{{ $i }}][label][ar]" value="{{ $item['label']['ar'] ?? '' }}" />
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600">{{ __('Href') }}</label>
                                <input class="w-full rounded border px-2 py-1" name="navigation[{{ $i }}][href]" value="{{ $item['href'] ?? '' }}" />
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600">{{ __('Variant') }}</label>
                                <input class="w-full rounded border px-2 py-1" name="navigation[{{ $i }}][variant]" value="{{ $item['variant'] ?? '' }}" placeholder="button-primary" />
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="text-right">
                        <button class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700" type="submit">{{ __('Save Navigation') }}</button>
                    </div>
                </form>
            </section>

            <!-- Features intro form (simple) -->
            <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200/60 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">{{ __('Features (Intro)') }}</h2>
                    <button type="button" class="rounded-md border px-3 py-1 text-sm" onclick="addFeatureItem()">+ {{ __('Add Intro Card') }}</button>
                </div>
                <form method="POST" action="{{ route('admin.settings.landing.updateSection', 'features') }}" class="space-y-3">
                    @csrf
                    <div class="grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="block text-xs text-gray-600">{{ __('Intro Headline (EN)') }}</label>
                            <input class="w-full rounded border px-2 py-1" name="features[intro][headline][en]" value="{{ Arr::get($current,'features.intro.headline.en','') }}" />
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600">{{ __('Intro Headline (AR)') }}</label>
                            <input class="w-full rounded border px-2 py-1" name="features[intro][headline][ar]" value="{{ Arr::get($current,'features.intro.headline.ar','') }}" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs text-gray-600">{{ __('Intro Description (EN)') }}</label>
                            <input class="w-full rounded border px-2 py-1" name="features[intro][description][en]" value="{{ Arr::get($current,'features.intro.description.en','') }}" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs text-gray-600">{{ __('Intro Description (AR)') }}</label>
                            <input class="w-full rounded border px-2 py-1" name="features[intro][description][ar]" value="{{ Arr::get($current,'features.intro.description.ar','') }}" />
                        </div>
                    </div>
                    <div id="feature-items" class="space-y-3">
                        @foreach(Arr::get($current,'features.intro.items',[]) as $i => $it)
                        <div class="grid gap-3 md:grid-cols-4 border rounded-md p-3">
                            <div>
                                <label class="block text-xs text-gray-600">{{ __('Icon') }}</label>
                                <input class="w-full rounded border px-2 py-1" name="features[intro][items][{{ $i }}][icon]" value="{{ $it['icon'] ?? '' }}" />
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600">{{ __('Title (EN)') }}</label>
                                <input class="w-full rounded border px-2 py-1" name="features[intro][items][{{ $i }}][title][en]" value="{{ $it['title']['en'] ?? '' }}" />
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600">{{ __('Title (AR)') }}</label>
                                <input class="w-full rounded border px-2 py-1" name="features[intro][items][{{ $i }}][title][ar]" value="{{ $it['title']['ar'] ?? '' }}" />
                            </div>
                            <div class="md:col-span-1">
                                <label class="block text-xs text-gray-600">{{ __('Body (EN)') }}</label>
                                <input class="w-full rounded border px-2 py-1" name="features[intro][items][{{ $i }}][body][en]" value="{{ $it['body']['en'] ?? '' }}" />
                            </div>
                            <div class="md:col-span-1">
                                <label class="block text-xs text-gray-600">{{ __('Body (AR)') }}</label>
                                <input class="w-full rounded border px-2 py-1" name="features[intro][items][{{ $i }}][body][ar]" value="{{ $it['body']['ar'] ?? '' }}" />
                            </div>
                            <div class="md:col-span-4 text-right">
                                <button class="text-xs text-rose-600" type="button" onclick="removeBlock(this)">Remove</button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="text-right">
                        <button class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700" type="submit">{{ __('Save Features') }}</button>
                    </div>
                </form>
            </section>

            <!-- Features columns form -->
            <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200/60 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">{{ __('Features (Columns)') }}</h2>
                    <button type="button" class="rounded-md border px-3 py-1 text-sm" onclick="addFeatureColumn()">+ {{ __('Add Column') }}</button>
                </div>
                <form method="POST" action="{{ route('admin.settings.landing.updateSection', 'features') }}" class="space-y-3">
                    @csrf
                    <div id="feature-columns" class="space-y-4">
                        @foreach(Arr::get($current,'features.columns',[]) as $ci => $col)
                        <div class="rounded border p-3 space-y-3">
                            <div class="grid gap-3 md:grid-cols-2 items-end">
                                <div>
                                    <label class="block text-xs text-gray-600">{{ __('Column Title (EN)') }}</label>
                                    <input class="w-full rounded border px-2 py-1" name="features[columns][{{ $ci }}][title][en]" value="{{ $col['title']['en'] ?? '' }}" />
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600">{{ __('Column Title (AR)') }}</label>
                                    <input class="w-full rounded border px-2 py-1" name="features[columns][{{ $ci }}][title][ar]" value="{{ $col['title']['ar'] ?? '' }}" />
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-gray-700">{{ __('Items') }}</h3>
                                <button class="rounded-md border px-2 py-1 text-xs" type="button" onclick="addFeatureColumnItem(this, {{ $ci }})">+ {{ __('Add Item') }}</button>
                            </div>
                            <div class="space-y-3">
                                @foreach(($col['items'] ?? []) as $ii => $citem)
                                <div class="grid gap-3 md:grid-cols-2 border rounded-md p-3">
                                    <div>
                                        <label class="block text-xs text-gray-600">{{ __('Title (EN)') }}</label>
                                        <input class="w-full rounded border px-2 py-1" name="features[columns][{{ $ci }}][items][{{ $ii }}][title][en]" value="{{ $citem['title']['en'] ?? '' }}" />
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600">{{ __('Title (AR)') }}</label>
                                        <input class="w-full rounded border px-2 py-1" name="features[columns][{{ $ci }}][items][{{ $ii }}][title][ar]" value="{{ $citem['title']['ar'] ?? '' }}" />
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600">{{ __('Body (EN)') }}</label>
                                        <textarea class="w-full rounded border px-2 py-1 text-sm" rows="2" name="features[columns][{{ $ci }}][items][{{ $ii }}][body][en]">{{ $citem['body']['en'] ?? '' }}</textarea>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600">{{ __('Body (AR)') }}</label>
                                        <textarea class="w-full rounded border px-2 py-1 text-sm" rows="2" name="features[columns][{{ $ci }}][items][{{ $ii }}][body][ar]">{{ $citem['body']['ar'] ?? '' }}</textarea>
                                    </div>
                                    <div class="md:col-span-2 text-right">
                                        <button class="text-xs text-rose-600" type="button" onclick="removeBlock(this)">Remove</button>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="text-right">
                                <button class="text-xs text-rose-600" type="button" onclick="removeBlock(this)">Remove Column</button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="text-right">
                        <button class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700" type="submit">{{ __('Save Columns') }}</button>
                    </div>
                </form>
            </section>

            <!-- Pricing simple form -->
            <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200/60 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">{{ __('Pricing') }}</h2>
                    <button type="button" class="rounded-md border px-3 py-1 text-sm" onclick="addPlan()">+ {{ __('Add Plan') }}</button>
                </div>
                <form method="POST" action="{{ route('admin.settings.landing.updateSection', 'pricing') }}" class="space-y-3">
                    @csrf
                    <div id="pricing-plans" class="space-y-4">
                        @foreach(Arr::get($current,'pricing.plans',[]) as $i => $plan)
                        <div class="rounded border p-3 space-y-3">
                            <div class="grid gap-3 md:grid-cols-4 items-end">
                                <div>
                                    <label class="block text-xs text-gray-600">{{ __('Name (EN)') }}</label>
                                    <input class="w-full rounded border px-2 py-1" name="pricing[plans][{{ $i }}][name][en]" value="{{ $plan['name']['en'] ?? '' }}" />
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600">{{ __('Name (AR)') }}</label>
                                    <input class="w-full rounded border px-2 py-1" name="pricing[plans][{{ $i }}][name][ar]" value="{{ $plan['name']['ar'] ?? '' }}" />
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600">{{ __('Price') }}</label>
                                    <input class="w-full rounded border px-2 py-1" name="pricing[plans][{{ $i }}][price]" value="{{ $plan['price'] ?? '' }}" />
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600">{{ __('Unit') }}</label>
                                    <input class="w-full rounded border px-2 py-1" name="pricing[plans][{{ $i }}][unit]" value="{{ $plan['unit'] ?? '' }}" />
                                </div>
                            </div>
                            <div class="grid gap-3 md:grid-cols-3 items-end">
                                <div>
                                    <label class="block text-xs text-gray-600">{{ __('CTA Label (EN)') }}</label>
                                    <input class="w-full rounded border px-2 py-1" name="pricing[plans][{{ $i }}][cta][label][en]" value="{{ $plan['cta']['label']['en'] ?? '' }}" />
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600">{{ __('CTA Label (AR)') }}</label>
                                    <input class="w-full rounded border px-2 py-1" name="pricing[plans][{{ $i }}][cta][label][ar]" value="{{ $plan['cta']['label']['ar'] ?? '' }}" />
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600">{{ __('CTA Link') }}</label>
                                    <input class="w-full rounded border px-2 py-1" name="pricing[plans][{{ $i }}][cta][href]" value="{{ $plan['cta']['href'] ?? '' }}" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600">{{ __('Features (one per line)') }}</label>
                                @php
                                    $featuresArr = $plan['features'] ?? [];
                                    $featuresText = '';
                                    if (is_array($featuresArr)) {
                                        $lines = [];
                                        foreach ($featuresArr as $f) {
                                            if (is_array($f)) {
                                                $loc = app()->getLocale();
                                                $lines[] = $f[$loc] ?? ($f['en'] ?? reset($f));
                                            } else {
                                                $lines[] = (string) $f;
                                            }
                                        }
                                        $featuresText = implode("\n", $lines);
                                    }
                                @endphp
                                <textarea class="w-full rounded border px-2 py-1 text-sm" rows="4" name="pricing[plans][{{ $i }}][features_text]">{{ $featuresText }}</textarea>
                            </div>
                            <label class="inline-flex items-center gap-2 text-xs">
                                <input type="checkbox" name="pricing[plans][{{ $i }}][highlighted]" value="1" {{ !empty($plan['highlighted']) ? 'checked' : '' }} /> {{ __('Featured') }}
                            </label>
                            <div class="text-right">
                                <button class="text-xs text-rose-600" type="button" onclick="removeBlock(this)">Remove Plan</button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="text-right">
                        <button class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700" type="submit">{{ __('Save Pricing') }}</button>
                    </div>
                </form>
            </section>

            <!-- Testimonials form -->
            <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200/60 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">{{ __('Testimonials') }}</h2>
                    <button type="button" class="rounded-md border px-3 py-1 text-sm" onclick="addTestimonial()">+ {{ __('Add') }}</button>
                </div>
                <form method="POST" action="{{ route('admin.settings.landing.updateSection', 'testimonials') }}" class="space-y-3">
                    @csrf
                    <div class="grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="block text-xs text-gray-600">{{ __('Headline (EN)') }}</label>
                            <input class="w-full rounded border px-2 py-1" name="testimonials[headline][en]" value="{{ Arr::get($current,'testimonials.headline.en','') }}" />
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600">{{ __('Headline (AR)') }}</label>
                            <input class="w-full rounded border px-2 py-1" name="testimonials[headline][ar]" value="{{ Arr::get($current,'testimonials.headline.ar','') }}" />
                        </div>
                    </div>
                    <div id="t-items" class="space-y-3">
                        @foreach(Arr::get($current,'testimonials.items',[]) as $i => $t)
                        <div class="grid gap-3 md:grid-cols-2 border rounded-md p-3">
                            <div>
                                <label class="block text-xs text-gray-600">{{ __('Quote (EN)') }}</label>
                                <textarea class="w-full rounded border px-2 py-1 text-sm" rows="2" name="testimonials[items][{{ $i }}][quote][en]">{{ $t['quote']['en'] ?? '' }}</textarea>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600">{{ __('Quote (AR)') }}</label>
                                <textarea class="w-full rounded border px-2 py-1 text-sm" rows="2" name="testimonials[items][{{ $i }}][quote][ar]">{{ $t['quote']['ar'] ?? '' }}</textarea>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600">{{ __('Author (EN)') }}</label>
                                <input class="w-full rounded border px-2 py-1" name="testimonials[items][{{ $i }}][author][en]" value="{{ $t['author']['en'] ?? '' }}" />
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600">{{ __('Author (AR)') }}</label>
                                <input class="w-full rounded border px-2 py-1" name="testimonials[items][{{ $i }}][author][ar]" value="{{ $t['author']['ar'] ?? '' }}" />
                            </div>
                            <div class="md:col-span-2 text-right">
                                <button class="text-xs text-rose-600" type="button" onclick="removeBlock(this)">Remove</button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="text-right">
                        <button class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700" type="submit">{{ __('Save Testimonials') }}</button>
                    </div>
                </form>
            </section>
        </div>

        <script>
        function addNavItem() {
            const wrap = document.getElementById('nav-items');
            const idx = wrap.children.length;
            const html = `
                <div class=\"grid gap-3 md:grid-cols-4 border rounded-md p-3\">
                    <div>
                        <label class=\"block text-xs text-gray-600\">Label (EN)</label>
                        <input class=\"w-full rounded border px-2 py-1\" name=\"navigation[${idx}][label][en]\" />
                    </div>
                    <div>
                        <label class=\"block text-xs text-gray-600\">Label (AR)</label>
                        <input class=\"w-full rounded border px-2 py-1\" name=\"navigation[${idx}][label][ar]\" />
                    </div>
                    <div>
                        <label class=\"block text-xs text-gray-600\">Href</label>
                        <input class=\"w-full rounded border px-2 py-1\" name=\"navigation[${idx}][href]\" />
                    </div>
                    <div>
                        <label class=\"block text-xs text-gray-600\">Variant</label>
                        <input class=\"w-full rounded border px-2 py-1\" name=\"navigation[${idx}][variant]\" placeholder=\"button-primary\" />
                    </div>
                </div>`;
            wrap.insertAdjacentHTML('beforeend', html);
        }
        function addFeatureItem() {
            const wrap = document.getElementById('feature-items');
            const idx = wrap.children.length;
            const html = `
                <div class=\"grid gap-3 md:grid-cols-4 border rounded-md p-3\">
                    <div>
                        <label class=\"block text-xs text-gray-600\">Icon</label>
                        <input class=\"w-full rounded border px-2 py-1\" name=\"features[intro][items][${idx}][icon]\" />
                    </div>
                    <div>
                        <label class=\"block text-xs text-gray-600\">Title (EN)</label>
                        <input class=\"w-full rounded border px-2 py-1\" name=\"features[intro][items][${idx}][title][en]\" />
                    </div>
                    <div>
                        <label class=\"block text-xs text-gray-600\">Title (AR)</label>
                        <input class=\"w-full rounded border px-2 py-1\" name=\"features[intro][items][${idx}][title][ar]\" />
                    </div>
                    <div>
                        <label class=\"block text-xs text-gray-600\">Body (EN)</label>
                        <input class=\"w-full rounded border px-2 py-1\" name=\"features[intro][items][${idx}][body][en]\" />
                    </div>
                    <div>
                        <label class=\"block text-xs text-gray-600\">Body (AR)</label>
                        <input class=\"w-full rounded border px-2 py-1\" name=\"features[intro][items][${idx}][body][ar]\" />
                    </div>
                    <div class=\"md:col-span-4 text-right\">
                        <button class=\"text-xs text-rose-600\" type=\"button\" onclick=\"removeBlock(this)\">Remove</button>
                    </div>
                </div>`;
            wrap.insertAdjacentHTML('beforeend', html);
        }
        function addPlan() {
            const wrap = document.getElementById('pricing-plans');
            const i = wrap.children.length;
            const html = `
            <div class=\"rounded border p-3 space-y-3\">
                <div class=\"grid gap-3 md:grid-cols-4 items-end\">
                    <div>
                        <label class=\"block text-xs text-gray-600\">Name (EN)</label>
                        <input class=\"w-full rounded border px-2 py-1\" name=\"pricing[plans][${i}][name][en]\" />
                    </div>
                    <div>
                        <label class=\"block text-xs text-gray-600\">Name (AR)</label>
                        <input class=\"w-full rounded border px-2 py-1\" name=\"pricing[plans][${i}][name][ar]\" />
                    </div>
                    <div>
                        <label class=\"block text-xs text-gray-600\">Price</label>
                        <input class=\"w-full rounded border px-2 py-1\" name=\"pricing[plans][${i}][price]\" />
                    </div>
                    <div>
                        <label class=\"block text-xs text-gray-600\">Unit</label>
                        <input class=\"w-full rounded border px-2 py-1\" name=\"pricing[plans][${i}][unit]\" />
                    </div>
                </div>
                <div class=\"grid gap-3 md:grid-cols-3 items-end\">
                    <div>
                        <label class=\"block text-xs text-gray-600\">CTA Label (EN)</label>
                        <input class=\"w-full rounded border px-2 py-1\" name=\"pricing[plans][${i}][cta][label][en]\" />
                    </div>
                    <div>
                        <label class=\"block text-xs text-gray-600\">CTA Label (AR)</label>
                        <input class=\"w-full rounded border px-2 py-1\" name=\"pricing[plans][${i}][cta][label][ar]\" />
                    </div>
                    <div>
                        <label class=\"block text-xs text-gray-600\">CTA Link</label>
                        <input class=\"w-full rounded border px-2 py-1\" name=\"pricing[plans][${i}][cta][href]\" />
                    </div>
                </div>
                <div>
                    <label class=\"block text-xs text-gray-600\">Features (one per line)</label>
                    <textarea class=\"w-full rounded border px-2 py-1 text-sm\" rows=\"4\" name=\"pricing[plans][${i}][features_text]\"></textarea>
                </div>
                <label class=\"inline-flex items-center gap-2 text-xs\"><input type=\"checkbox\" name=\"pricing[plans][${i}][highlighted]\" value=\"1\" /> Featured</label>
                <div class=\"text-right\"><button class=\"text-xs text-rose-600\" type=\"button\" onclick=\"removeBlock(this)\">Remove Plan</button></div>
            </div>`;
            wrap.insertAdjacentHTML('beforeend', html);
        }
        function addTestimonial() {
            const wrap = document.getElementById('t-items');
            const i = wrap.children.length;
            const html = `
                <div class=\"grid gap-3 md:grid-cols-2 border rounded-md p-3\">
                    <div>
                        <label class=\"block text-xs text-gray-600\">Quote (EN)</label>
                        <textarea class=\"w-full rounded border px-2 py-1 text-sm\" rows=\"2\" name=\"testimonials[items][${i}][quote][en]\"></textarea>
                    </div>
                    <div>
                        <label class=\"block text-xs text-gray-600\">Quote (AR)</label>
                        <textarea class=\"w-full rounded border px-2 py-1 text-sm\" rows=\"2\" name=\"testimonials[items][${i}][quote][ar]\"></textarea>
                    </div>
                    <div>
                        <label class=\"block text-xs text-gray-600\">Author (EN)</label>
                        <input class=\"w-full rounded border px-2 py-1\" name=\"testimonials[items][${i}][author][en]\" />
                    </div>
                    <div>
                        <label class=\"block text-xs text-gray-600\">Author (AR)</label>
                        <input class=\"w-full rounded border px-2 py-1\" name=\"testimonials[items][${i}][author][ar]\" />
                    </div>
                    <div class=\"md:col-span-2 text-right\"><button class=\"text-xs text-rose-600\" type=\"button\" onclick=\"removeBlock(this)\">Remove</button></div>
                </div>`;
            wrap.insertAdjacentHTML('beforeend', html);
        }
        function addFeatureColumn() {
            const wrap = document.getElementById('feature-columns');
            const ci = wrap.children.length;
            const html = `
            <div class=\"rounded border p-3 space-y-3\">
                <div class=\"grid gap-3 md:grid-cols-2 items-end\">
                    <div>
                        <label class=\"block text-xs text-gray-600\">Column Title (EN)</label>
                        <input class=\"w-full rounded border px-2 py-1\" name=\"features[columns][${ci}][title][en]\" />
                    </div>
                    <div>
                        <label class=\"block text-xs text-gray-600\">Column Title (AR)</label>
                        <input class=\"w-full rounded border px-2 py-1\" name=\"features[columns][${ci}][title][ar]\" />
                    </div>
                </div>
                <div class=\"flex items-center justify-between\">
                    <h3 class=\"text-sm font-semibold text-gray-700\">Items</h3>
                    <button class=\"rounded-md border px-2 py-1 text-xs\" type=\"button\" onclick=\"addFeatureColumnItem(this, ${ci})\">+ Add Item</button>
                </div>
                <div class=\"space-y-3\"></div>
                <div class=\"text-right\"><button class=\"text-xs text-rose-600\" type=\"button\" onclick=\"removeBlock(this)\">Remove Column</button></div>
            </div>`;
            wrap.insertAdjacentHTML('beforeend', html);
        }
        function addFeatureColumnItem(btn, ci) {
            // find sibling items container (previous element)
            const container = btn.closest('div').parentElement.querySelector('.space-y-3');
            const ii = container.children.length;
            const html = `
            <div class=\"grid gap-3 md:grid-cols-2 border rounded-md p-3\">
                <div>
                    <label class=\"block text-xs text-gray-600\">Title (EN)</label>
                    <input class=\"w-full rounded border px-2 py-1\" name=\"features[columns][${ci}][items][${ii}][title][en]\" />
                </div>
                <div>
                    <label class=\"block text-xs text-gray-600\">Title (AR)</label>
                    <input class=\"w-full rounded border px-2 py-1\" name=\"features[columns][${ci}][items][${ii}][title][ar]\" />
                </div>
                <div>
                    <label class=\"block text-xs text-gray-600\">Body (EN)</label>
                    <textarea class=\"w-full rounded border px-2 py-1 text-sm\" rows=\"2\" name=\"features[columns][${ci}][items][${ii}][body][en]\"></textarea>
                </div>
                <div>
                    <label class=\"block text-xs text-gray-600\">Body (AR)</label>
                    <textarea class=\"w-full rounded border px-2 py-1 text-sm\" rows=\"2\" name=\"features[columns][${ci}][items][${ii}][body][ar]\"></textarea>
                </div>
                <div class=\"md:col-span-2 text-right\"><button class=\"text-xs text-rose-600\" type=\"button\" onclick=\"removeBlock(this)\">Remove</button></div>
            </div>`;
            container.insertAdjacentHTML('beforeend', html);
        }
        function removeBlock(el){
            const block = el.closest('.rounded.border.p-3, .grid.gap-3.md\:grid-cols-4.border.rounded-md.p-3, .grid.gap-3.md\:grid-cols-2.border.rounded-md.p-3');
            if(block){ block.remove(); }
        }
        // Client-side previews for assets
        (function(){
            const logoInput = document.getElementById('logoInput');
            const logoPreview = document.getElementById('logoPreview');
            const heroInput = document.getElementById('heroInput');
            const heroPreview = document.getElementById('heroPreview');
            const authScreenshotInput = document.getElementById('authScreenshotInput');
            const authScreenshotPreview = document.getElementById('authScreenshotPreview');
            const galleryInput = document.getElementById('galleryInput');
            const galleryPreview = document.getElementById('galleryPreview');

            if (logoInput && logoPreview){
                logoInput.addEventListener('change', ()=>{
                    const f = logoInput.files && logoInput.files[0];
                    if (!f) { logoPreview.classList.add('hidden'); return; }
                    logoPreview.querySelector('img').src = URL.createObjectURL(f);
                    logoPreview.classList.remove('hidden');
                });
            }
            if (heroInput && heroPreview){
                heroInput.addEventListener('change', ()=>{
                    const f = heroInput.files && heroInput.files[0];
                    if (!f) { heroPreview.classList.add('hidden'); return; }
                    heroPreview.querySelector('img').src = URL.createObjectURL(f);
                    heroPreview.classList.remove('hidden');
                });
            }
            if (authScreenshotInput && authScreenshotPreview){
                authScreenshotInput.addEventListener('change', ()=>{
                    const f = authScreenshotInput.files && authScreenshotInput.files[0];
                    if (!f) { authScreenshotPreview.classList.add('hidden'); return; }
                    authScreenshotPreview.querySelector('img').src = URL.createObjectURL(f);
                    authScreenshotPreview.classList.remove('hidden');
                });
            }
            if (galleryInput && galleryPreview){
                galleryInput.addEventListener('change', ()=>{
                    const files = Array.from(galleryInput.files || []);
                    galleryPreview.innerHTML='';
                    if (!files.length){ galleryPreview.classList.add('hidden'); return; }
                    files.forEach((file, idx)=>{
                        const card = document.createElement('div');
                        card.className = 'relative overflow-hidden rounded border bg-white';
                        const url = URL.createObjectURL(file);
                        card.innerHTML = `
                            <img src="${url}" class="h-28 w-full object-cover" alt="preview">
                            <button type="button" class="absolute right-2 top-2 inline-flex h-7 w-7 items-center justify-center rounded-full bg-rose-600 text-white shadow hover:bg-rose-700" aria-label="Remove">&times;</button>
                        `;
                        card.querySelector('button').addEventListener('click', ()=>{
                            const dt = new DataTransfer();
                            Array.from(galleryInput.files).forEach((f, i)=>{ if (i!==idx) dt.items.add(f); });
                            galleryInput.files = dt.files;
                            card.remove();
                            if (!galleryPreview.children.length){ galleryPreview.classList.add('hidden'); }
                        });
                        galleryPreview.appendChild(card);
                    });
                    galleryPreview.classList.remove('hidden');
                });
            }
        })();
        </script>
    </div>
</div>
@endsection
