<div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
    <!-- Toasts -->
    <div x-data="{toasts: []}"
         x-on:notify.window="toasts.push({id: Date.now()+Math.random(), message: $event.detail.message || 'Saved', type: $event.detail.type || 'success'}); setTimeout(()=>toasts.shift(), 1800)"
         class="fixed right-4 top-4 z-50 space-y-2">
        <template x-for="t in toasts" :key="t.id">
            <div class="rounded-lg px-3 py-2 text-sm shadow-lg"
                 :class="t.type==='success' ? 'bg-emerald-600 text-white' : (t.type==='error' ? 'bg-rose-600 text-white' : 'bg-gray-800 text-white')"
                 x-text="t.message"></div>
        </template>
    </div>
    <div class="space-y-6">
        <div class="bg-white shadow-sm ring-1 ring-gray-200/60 rounded-2xl p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">{{ __('Public Landing Page Content') }}</h1>
                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('Adjust hero copy, navigation, CTAs, and translations. Use the locale toggle to switch languages.') }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('docs.public-landing') }}" target="_blank" rel="noopener"
                        class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        <svg class="h-4 w-4 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ __('Documentation') }}
                    </a>
                    <a href="{{ route('home') }}" target="_blank" rel="noopener"
                        class="inline-flex items-center gap-2 rounded-lg bg-indigo-50 px-3 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-100">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75V21h15V9.75" />
                        </svg>
                        {{ __('View Landing') }}
                    </a>
                </div>
            </div>
        </div>

        <div x-data="{ hidden: false }" x-init="hidden = localStorage.getItem('landingTipsHidden') === '1'" x-cloak
            class="rounded-2xl border border-indigo-100 bg-indigo-50/70 p-4 sm:p-5">
            <div x-show="!hidden" class="flex items-start justify-between gap-3">
                <div>
                    <div class="text-sm font-semibold text-indigo-900">{{ __('Quick Tips') }}</div>
                    <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-indigo-900/80">
                        <li>{{ __('Craft a clear hero headline + subheadline for both EN and AR locales.') }}</li>
                        <li>{{ __('Use navigation anchors such as #pricing and #testimonials for smooth scrolling.') }}</li>
                        <li>{{ __('Plan CTAs accept shortcuts like #register and /book-call for internal links.') }}</li>
                        <li>{{ __('Uploaded assets live in storage/app/public/landing and are served via public/storage.') }}</li>
                        <li>{{ __('Preview the page in desktop, tablet, and mobile modes to verify spacing.') }}</li>
                    </ul>
                </div>
                <button type="button"
                    class="text-xs font-medium text-indigo-700 hover:underline"
                    @click="hidden = true; localStorage.setItem('landingTipsHidden', '1')">
                    {{ __('Hide tips') }}
                </button>
            </div>
            <div x-show="hidden" class="flex items-center justify-between">
                <span class="text-xs text-indigo-800">{{ __('Tips hidden') }}</span>
                <button type="button"
                    class="text-xs font-medium text-indigo-700 hover:underline"
                    @click="hidden = false; localStorage.removeItem('landingTipsHidden')">
                    {{ __('Show tips') }}
                </button>
            </div>
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

        <div class="flex flex-wrap gap-2">
            @foreach ($locales as $locale => $label)
                <button type="button" wire:click="switchLocale('{{ $locale }}')"
                    class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-sm font-medium transition
                    {{ $activeLocale === $locale ? 'border-indigo-500 bg-indigo-600 text-white' : 'border-gray-200 bg-white text-gray-700 hover:border-indigo-300 hover:text-indigo-600' }}">
                    <span>{{ $label }}</span>
                    <span class="text-xs uppercase tracking-wide">{{ strtoupper($locale) }}</span>
                </button>
            @endforeach
        </div>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">
            <div class="space-y-6">
                <section x-data="{ open: (localStorage.getItem('landing:open:assets') ?? '1') === '1' }" x-cloak class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200/60 space-y-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">{{ __('Brand Assets') }}</h2>
                            <p class="text-sm text-gray-600">{{ __('Upload or update hero image, logo, and gallery screenshots.') }}</p>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-gray-500" x-data="{saved:false}" x-on:section-saved.window="if($event.detail.section==='assets'){ saved=true; setTimeout(()=>saved=false, 1500) }">
                            <button type="button" wire:click="saveSection('assets')"
                                class="rounded-full bg-indigo-600 px-3 py-1 text-white hover:bg-indigo-700"
                                wire:loading.attr="disabled" wire:target="saveSection,logoUpload,heroUpload,galleryUploads">{{ __('Save') }}</button>
                            <span x-show="saved" class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-medium text-emerald-700">
                                ✓ {{ __('Saved') }}
                            </span>
                            <span>{{ __('JPG, PNG, SVG up to 4MB') }}</span>
                            <button type="button"
                                class="rounded-full border px-2 py-1 text-[11px] font-medium text-gray-700 hover:bg-gray-50"
                                @click="open = !open; localStorage.setItem('landing:open:assets', open ? '1' : '0')">
                                <span x-show="open">{{ __('Collapse') }}</span>
                                <span x-show="!open">{{ __('Expand') }}</span>
                            </button>
                        </div>
                    </div>
                    <div x-show="open" x-transition class="grid gap-6 md:grid-cols-2">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="text-sm font-medium text-gray-700">{{ __('Logo') }}</label>
                                @if(!empty($form['assets']['logo_url']))
                                    <button type="button" wire:click="removeLogo" class="text-xs font-medium text-rose-600 hover:underline">{{ __('Remove') }}</button>
                                @endif
                            </div>
                            <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 p-4 space-y-3">
                                <input type="file" wire:model="logoUpload" accept="image/*"
                                    class="block w-full text-sm text-gray-700 file:mr-3 file:rounded-full file:border-0 file:bg-indigo-600 file:px-3 file:py-1 file:text-sm file:font-semibold file:text-white hover:file:bg-indigo-700">
                                @error('logoUpload')
                                    <p class="text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                                <div class="flex items-center gap-3">
                                    @if ($logoUpload)
                                        <img src="{{ $logoUpload->temporaryUrl() }}" class="h-12 w-12 rounded border object-contain bg-white" alt="logo preview" />
                                    @elseif (!empty($form['assets']['logo_url']))
                                        <img src="{{ $form['assets']['logo_url'] }}" class="h-12 w-12 rounded border object-contain bg-white" alt="logo" />
                                    @else
                                        <span class="text-xs text-gray-400">{{ __('No logo uploaded yet.') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="text-sm font-medium text-gray-700">{{ __('Hero Image') }}</label>
                                @if(!empty($form['assets']['hero_image']))
                                    <button type="button" wire:click="removeHeroImage" class="text-xs font-medium text-rose-600 hover:underline">{{ __('Remove') }}</button>
                                @endif
                            </div>
                            <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 p-4 space-y-3">
                                <input type="file" wire:model="heroUpload" accept="image/*"
                                    class="block w-full text-sm text-gray-700 file:mr-3 file:rounded-full file:border-0 file:bg-indigo-600 file:px-3 file:py-1 file:text-sm file:font-semibold file:text-white hover:file:bg-indigo-700">
                                @error('heroUpload')
                                    <p class="text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                                <div class="overflow-hidden rounded-lg border bg-white">
                                    @if ($heroUpload)
                                        <img src="{{ $heroUpload->temporaryUrl() }}" class="h-32 w-full object-cover" alt="hero preview" />
                                    @elseif (!empty($form['assets']['hero_image']))
                                        <img src="{{ $form['assets']['hero_image'] }}" class="h-32 w-full object-cover" alt="hero image" />
                                    @else
                                        <div class="flex h-32 w-full items-center justify-center text-xs text-gray-400">{{ __('No hero image uploaded yet.') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-medium text-gray-700">{{ __('Gallery Images') }}</label>
                            <span class="text-xs text-gray-400">{{__('Upload multiple screenshots at once.')}}</span>
                        </div>
                        <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 p-4 space-y-3">
                            <input type="file" wire:model="galleryUploads" accept="image/*" multiple
                                class="block w-full text-sm text-gray-700 file:mr-3 file:rounded-full file:border-0 file:bg-indigo-600 file:px-3 file:py-1 file:text-sm file:font-semibold file:text-white hover:file:bg-indigo-700">
                            @error('galleryUploads.*')
                                <p class="text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                            <div class="grid gap-3 md:grid-cols-3">
                                @foreach ($galleryUploads as $tempIndex => $upload)
                                    <div wire:key="gallery-temp-{{ $tempIndex }}" class="relative overflow-hidden rounded-md border bg-white">
                                        <img src="{{ $upload->temporaryUrl() }}" class="h-28 w-full object-cover" alt="temporary upload">
                                        <span class="absolute inset-x-0 bottom-0 bg-black/50 px-2 py-1 text-[11px] text-white">{{ __('Pending upload') }}</span>
                                    </div>
                                @endforeach
                                @foreach (($form['assets']['feature_images']['gallery'] ?? []) as $galleryIndex => $image)
                                    <div wire:key="gallery-existing-{{ $galleryIndex }}" class="relative overflow-hidden rounded-md border bg-white">
                                        <img src="{{ $image }}" class="h-28 w-full object-cover" alt="gallery image">
                                        <button type="button" wire:click="removeGalleryImage({{ $galleryIndex }})"
                                            class="absolute right-2 top-2 inline-flex h-6 w-6 items-center justify-center rounded-full bg-rose-600 text-white shadow hover:bg-rose-700"
                                            title="{{ __('Remove image') }}">
                                            ×
                                        </button>
                                    </div>
                                @endforeach
                                @if (empty($form['assets']['feature_images']['gallery']) && empty($galleryUploads))
                                    <div class="col-span-full text-xs text-gray-400">{{ __('No gallery images uploaded yet.') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </section>

                <section x-data="{ open: (localStorage.getItem('landing:open:meta') ?? '1') === '1' }" x-cloak class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200/60 space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">{{ __('Meta') }}</h2>
                        <div class="flex items-center gap-2" x-data="{saved:false}" x-on:section-saved.window="if($event.detail.section==='meta'){ saved=true; setTimeout(()=>saved=false, 1500) }">
                            <button type="button" wire:click="saveSection('meta')"
                                class="rounded-full bg-indigo-600 px-3 py-1 text-white hover:bg-indigo-700"
                                wire:loading.attr="disabled" wire:target="saveSection">{{ __('Save') }}</button>
                            <span x-show="saved" class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-medium text-emerald-700">✓ {{ __('Saved') }}</span>
                            <button type="button"
                                class="rounded-full border px-2 py-1 text-[11px] font-medium text-gray-700 hover:bg-gray-50"
                                @click="open = !open; localStorage.setItem('landing:open:meta', open ? '1' : '0')">
                                <span x-show="open">{{ __('Collapse') }}</span>
                                <span x-show="!open">{{ __('Expand') }}</span>
                            </button>
                        </div>
                    </div>
                    <div x-show="open" x-transition>
                        <label class="block text-sm font-medium text-gray-700">
                            {{ __('Page Title') }} ({{ strtoupper($activeLocale) }})
                        </label>
                        <input type="text" wire:model.defer="form.meta.title.{{ $activeLocale }}"
                            class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        @error('form.meta.title.' . $activeLocale)
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </section>

                <section x-data="{ open: (localStorage.getItem('landing:open:hero') ?? '0') === '1' }" x-cloak class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200/60 space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">{{ __('Hero Section') }}</h2>
                        <div class="flex items-center gap-2" x-data="{saved:false}" x-on:section-saved.window="if($event.detail.section==='hero'){ saved=true; setTimeout(()=>saved=false, 1500) }">
                            <button type="button" wire:click="saveSection('hero')"
                                class="rounded-full bg-indigo-600 px-3 py-1 text-white hover:bg-indigo-700"
                                wire:loading.attr="disabled" wire:target="saveSection">{{ __('Save') }}</button>
                            <span x-show="saved" class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-medium text-emerald-700">✓ {{ __('Saved') }}</span>
                            <button type="button"
                                class="rounded-full border px-2 py-1 text-[11px] font-medium text-gray-700 hover:bg-gray-50"
                                @click="open = !open; localStorage.setItem('landing:open:hero', open ? '1' : '0')">
                                <span x-show="open">{{ __('Collapse') }}</span>
                                <span x-show="!open">{{ __('Expand') }}</span>
                            </button>
                        </div>
                    </div>
                    <div x-show="open" x-transition>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Headline') }} ({{ strtoupper($activeLocale) }})</label>
                        <input type="text" wire:model.defer="form.hero.headline.{{ $activeLocale }}"
                            class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        @error('form.hero.headline.' . $activeLocale)
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Subheadline') }} ({{ strtoupper($activeLocale) }})</label>
                        <textarea rows="3" wire:model.defer="form.hero.subheadline.{{ $activeLocale }}"
                            class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        @error('form.hero.subheadline.' . $activeLocale)
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-900">{{ __('Hero Calls-to-Action') }}</h3>
                            <button type="button" wire:click="addHeroCta"
                                class="inline-flex items-center gap-1 rounded-full border border-indigo-200 px-3 py-1 text-xs font-medium text-indigo-600 hover:bg-indigo-50">
                                <span>+</span>{{ __('Add CTA') }}
                            </button>
                        </div>
                        @foreach ($form['hero']['ctas'] as $index => $cta)
                            <div wire:key="hero-cta-{{ $index }}" class="rounded-xl border border-gray-200 p-4 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold uppercase text-gray-500">{{ __('CTA') }} #{{ $index + 1 }}</span>
                                    <button type="button" wire:click="removeHeroCta({{ $index }})"
                                        class="text-xs font-medium text-rose-600 hover:underline">{{ __('Remove') }}</button>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600">{{ __('Label') }} ({{ strtoupper($activeLocale) }})</label>
                                    <input type="text" wire:model.defer="form.hero.ctas.{{ $index }}.label.{{ $activeLocale }}"
                                        class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    @error('form.hero.ctas.' . $index . '.label.' . $activeLocale)
                                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600">{{ __('Destination URL') }}</label>
                                    <input type="text" wire:model.defer="form.hero.ctas.{{ $index }}.href"
                                        class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    @error('form.hero.ctas.' . $index . '.href')
                                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600">{{ __('Visual Style') }}</label>
                                    <select wire:model.defer="form.hero.ctas.{{ $index }}.style"
                                        class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="primary">{{ __('Primary Button') }}</option>
                                        <option value="outline">{{ __('Outline Button') }}</option>
                                        <option value="text">{{ __('Link Style') }}</option>
                                    </select>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section x-data="{ open: (localStorage.getItem('landing:open:navigation') ?? '0') === '1' }" x-cloak class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200/60 space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">{{ __('Navigation Links') }}</h2>
                        <div class="flex items-center gap-2" x-data="{saved:false}" x-on:section-saved.window="if($event.detail.section==='navigation'){ saved=true; setTimeout(()=>saved=false, 1500) }">
                            <button type="button" wire:click="saveSection('navigation')"
                                class="rounded-full bg-indigo-600 px-3 py-1 text-white hover:bg-indigo-700"
                                wire:loading.attr="disabled" wire:target="saveSection">{{ __('Save') }}</button>
                            <span x-show="saved" class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-medium text-emerald-700">✓ {{ __('Saved') }}</span>
                            <button type="button" wire:click="addNavigationLink"
                                class="inline-flex items-center gap-1 rounded-full border border-indigo-200 px-3 py-1 text-xs font-medium text-indigo-600 hover:bg-indigo-50">
                                <span>+</span>{{ __('Add Link') }}
                            </button>
                            <button type="button"
                                class="rounded-full border px-2 py-1 text-[11px] font-medium text-gray-700 hover:bg-gray-50"
                                @click="open = !open; localStorage.setItem('landing:open:navigation', open ? '1' : '0')">
                                <span x-show="open">{{ __('Collapse') }}</span>
                                <span x-show="!open">{{ __('Expand') }}</span>
                            </button>
                        </div>
                    </div>
                    <div x-show="open" x-transition class="space-y-3">
                        @foreach ($form['navigation'] as $index => $link)
                            <div wire:key="nav-link-{{ $index }}" class="rounded-xl border border-gray-200 p-4 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold uppercase text-gray-500">{{ __('Link') }} #{{ $index + 1 }}</span>
                                    <button type="button" wire:click="removeNavigationLink({{ $index }})"
                                        class="text-xs font-medium text-rose-600 hover:underline">{{ __('Remove') }}</button>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600">{{ __('Label') }} ({{ strtoupper($activeLocale) }})</label>
                                    <input type="text" wire:model.defer="form.navigation.{{ $index }}.label.{{ $activeLocale }}"
                                        class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    @error('form.navigation.' . $index . '.label.' . $activeLocale)
                                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600">{{ __('Destination URL or Anchor') }}</label>
                                    <input type="text" wire:model.defer="form.navigation.{{ $index }}.href"
                                        class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    @error('form.navigation.' . $index . '.href')
                                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600">{{ __('Variant (optional)') }}</label>
                                    <input type="text" wire:model.defer="form.navigation.{{ $index }}.variant"
                                        class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="button-primary" />
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section x-data="{ open: (localStorage.getItem('landing:open:features') ?? '0') === '1' }" x-cloak class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200/60 space-y-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">{{ __('Key Features') }}</h2>
                            <p class="text-sm text-gray-600">{{ __('Intro cards and detailed columns shown beneath the hero.') }}</p>
                        </div>
                        <div class="flex items-center gap-2" x-data="{saved:false}" x-on:section-saved.window="if($event.detail.section==='features'){ saved=true; setTimeout(()=>saved=false, 1500) }">
                            <button type="button" wire:click="saveSection('features')"
                                class="rounded-full bg-indigo-600 px-3 py-1 text-white hover:bg-indigo-700"
                                wire:loading.attr="disabled" wire:target="saveSection">{{ __('Save') }}</button>
                            <span x-show="saved" class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-medium text-emerald-700">✓ {{ __('Saved') }}</span>
                            <button type="button" wire:click="addFeatureIntroItem"
                                class="inline-flex items-center gap-1 rounded-full border border-indigo-200 px-3 py-1 text-xs font-medium text-indigo-600 hover:bg-indigo-50">
                                <span>+</span>{{ __('Add Intro Card') }}
                            </button>
                            <button type="button"
                                class="rounded-full border px-2 py-1 text-[11px] font-medium text-gray-700 hover:bg-gray-50"
                                @click="open = !open; localStorage.setItem('landing:open:features', open ? '1' : '0')">
                                <span x-show="open">{{ __('Collapse') }}</span>
                                <span x-show="!open">{{ __('Expand') }}</span>
                            </button>
                        </div>
                    </div>
                    <div x-show="open" x-transition class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Intro Headline') }} ({{ strtoupper($activeLocale) }})</label>
                            <input type="text" wire:model.defer="form.features.intro.headline.{{ $activeLocale }}"
                                class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            @error('form.features.intro.headline.' . $activeLocale)
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Intro Description') }} ({{ strtoupper($activeLocale) }})</label>
                            <textarea rows="3" wire:model.defer="form.features.intro.description.{{ $activeLocale }}"
                                class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                            @error('form.features.intro.description.' . $activeLocale)
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            @foreach($form['features']['intro']['items'] as $index => $feature)
                                <div wire:key="feature-card-{{ $index }}" class="rounded-xl border border-gray-200 p-4 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-semibold uppercase text-gray-500">{{ __('Card') }} #{{ $index + 1 }}</span>
                                        <button type="button" wire:click="removeFeatureIntroItem({{ $index }})" class="text-xs font-medium text-rose-600 hover:underline">{{ __('Remove') }}</button>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600">{{ __('Icon keyword') }}</label>
                                        <input type="text" wire:model.defer="form.features.intro.items.{{ $index }}.icon"
                                            class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                        @error('form.features.intro.items.' . $index . '.icon')
                                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600">{{ __('Title') }} ({{ strtoupper($activeLocale) }})</label>
                                        <input type="text" wire:model.defer="form.features.intro.items.{{ $index }}.title.{{ $activeLocale }}"
                                            class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                        @error('form.features.intro.items.' . $index . '.title.' . $activeLocale)
                                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600">{{ __('Description') }} ({{ strtoupper($activeLocale) }})</label>
                                        <textarea rows="2" wire:model.defer="form.features.intro.items.{{ $index }}.body.{{ $activeLocale }}"
                                            class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                        @error('form.features.intro.items.' . $index . '.body.' . $activeLocale)
                                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h3 class="text-sm font-semibold text-gray-900">{{ __('Columns') }}</h3>
                        @foreach($form['features']['columns'] as $columnIndex => $column)
                            <div wire:key="feature-column-{{ $columnIndex }}" class="rounded-2xl border border-gray-200 p-4 space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold uppercase text-gray-500">{{ __('Column') }} #{{ $columnIndex + 1 }}</span>
                                    <button type="button" wire:click="addFeatureColumnItem({{ $columnIndex }})"
                                        class="text-xs font-medium text-indigo-600 hover:underline">{{ __('Add Item') }}</button>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600">{{ __('Column Title') }} ({{ strtoupper($activeLocale) }})</label>
                                    <input type="text" wire:model.defer="form.features.columns.{{ $columnIndex }}.title.{{ $activeLocale }}"
                                        class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    @error('form.features.columns.' . $columnIndex . '.title.' . $activeLocale)
                                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-3">
                                    @foreach(($column['items'] ?? []) as $itemIndex => $item)
                                        <div wire:key="column-{{ $columnIndex }}-item-{{ $itemIndex }}" class="rounded-xl border border-gray-100 bg-gray-50 p-3 space-y-2">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-semibold uppercase text-gray-500">{{ __('Item') }} #{{ $itemIndex + 1 }}</span>
                                                <button type="button" wire:click="removeFeatureColumnItem({{ $columnIndex }}, {{ $itemIndex }})"
                                                    class="text-[11px] font-medium text-rose-600 hover:underline">{{ __('Remove') }}</button>
                                            </div>
                                            <div>
                                                <label class="block text-[11px] font-medium text-gray-600">{{ __('Title') }} ({{ strtoupper($activeLocale) }})</label>
                                                <input type="text" wire:model.defer="form.features.columns.{{ $columnIndex }}.items.{{ $itemIndex }}.title.{{ $activeLocale }}"
                                                    class="mt-1 w-full rounded-lg border border-gray-200 px-2 py-1.5 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                                @error('form.features.columns.' . $columnIndex . '.items.' . $itemIndex . '.title.' . $activeLocale)
                                                    <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            <div>
                                                <label class="block text-[11px] font-medium text-gray-600">{{ __('Description') }} ({{ strtoupper($activeLocale) }})</label>
                                                <textarea rows="2" wire:model.defer="form.features.columns.{{ $columnIndex }}.items.{{ $itemIndex }}.body.{{ $activeLocale }}"
                                                    class="mt-1 w-full rounded-lg border border-gray-200 px-2 py-1.5 text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                                @error('form.features.columns.' . $columnIndex . '.items.' . $itemIndex . '.body.' . $activeLocale)
                                                    <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section x-data="{ open: (localStorage.getItem('landing:open:pricing') ?? '0') === '1' }" x-cloak class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200/60 space-y-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">{{ __('Pricing Plans') }}</h2>
                            <p class="text-sm text-gray-600">{{ __('Manage subscription tiers, pricing copy, and feature bullets.') }}</p>
                        </div>
                        <div class="flex items-center gap-2" x-data="{saved:false}" x-on:section-saved.window="if($event.detail.section==='pricing'){ saved=true; setTimeout(()=>saved=false, 1500) }">
                            <button type="button" wire:click="saveSection('pricing')"
                                class="rounded-full bg-indigo-600 px-3 py-1 text-white hover:bg-indigo-700"
                                wire:loading.attr="disabled" wire:target="saveSection">{{ __('Save') }}</button>
                            <span x-show="saved" class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-medium text-emerald-700">✓ {{ __('Saved') }}</span>
                            <button type="button" wire:click="addPricingPlan"
                                class="inline-flex items-center gap-1 rounded-full border border-indigo-200 px-3 py-1 text-xs font-medium text-indigo-600 hover:bg-indigo-50">
                                <span>+</span>{{ __('Add Plan') }}
                            </button>
                            <button type="button"
                                class="rounded-full border px-2 py-1 text-[11px] font-medium text-gray-700 hover:bg-gray-50"
                                @click="open = !open; localStorage.setItem('landing:open:pricing', open ? '1' : '0')">
                                <span x-show="open">{{ __('Collapse') }}</span>
                                <span x-show="!open">{{ __('Expand') }}</span>
                            </button>
                        </div>
                    </div>
                    <div x-show="open" x-transition class="space-y-4">
                        @foreach($form['pricing']['plans'] as $planIndex => $plan)
                            <div wire:key="pricing-plan-{{ $planIndex }}" class="rounded-2xl border border-gray-200 p-5 space-y-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <span class="text-xs font-semibold uppercase text-gray-500">{{ __('Plan') }} #{{ $planIndex + 1 }}</span>
                                        <label class="inline-flex items-center gap-2 text-xs font-medium text-gray-600">
                                            <input type="checkbox" wire:model.defer="form.pricing.plans.{{ $planIndex }}.highlighted"
                                                class="h-3.5 w-3.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            {{ __('Featured') }}
                                        </label>
                                    </div>
                                    <button type="button" wire:click="removePricingPlan({{ $planIndex }})"
                                        class="text-xs font-medium text-rose-600 hover:underline">{{ __('Remove') }}</button>
                                </div>
                                <div class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600">{{ __('Plan Name') }} ({{ strtoupper($activeLocale) }})</label>
                                        <input type="text" wire:model.defer="form.pricing.plans.{{ $planIndex }}.name.{{ $activeLocale }}"
                                            class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                        @error('form.pricing.plans.' . $planIndex . '.name.' . $activeLocale)
                                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600">{{ __('Price') }}</label>
                                        <input type="text" wire:model.defer="form.pricing.plans.{{ $planIndex }}.price"
                                            class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                        @error('form.pricing.plans.' . $planIndex . '.price')
                                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600">{{ __('Price Unit') }}</label>
                                        <input type="text" wire:model.defer="form.pricing.plans.{{ $planIndex }}.unit"
                                            class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                        @error('form.pricing.plans.' . $planIndex . '.unit')
                                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600">{{ __('CTA Label') }} ({{ strtoupper($activeLocale) }})</label>
                                        <input type="text" wire:model.defer="form.pricing.plans.{{ $planIndex }}.cta.label.{{ $activeLocale }}"
                                            class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                        @error('form.pricing.plans.' . $planIndex . '.cta.label.' . $activeLocale)
                                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600">{{ __('CTA Link') }}</label>
                                        <input type="text" wire:model.defer="form.pricing.plans.{{ $planIndex }}.cta.href"
                                            class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                        @error('form.pricing.plans.' . $planIndex . '.cta.href')
                                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-xs font-semibold uppercase text-gray-500">{{ __('Plan Features') }}</h4>
                                        <button type="button" wire:click="addPlanFeature({{ $planIndex }})"
                                            class="text-xs font-medium text-indigo-600 hover:underline">{{ __('Add Feature') }}</button>
                                    </div>
                                    @foreach(($plan['features'] ?? []) as $featureIndex => $feature)
                                        <div wire:key="plan-{{ $planIndex }}-feature-{{ $featureIndex }}" class="flex items-center gap-2">
                                            <input type="text" wire:model.defer="form.pricing.plans.{{ $planIndex }}.features.{{ $featureIndex }}.{{ $activeLocale }}"
                                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                            <button type="button" wire:click="removePlanFeature({{ $planIndex }}, {{ $featureIndex }})"
                                                class="text-xs font-medium text-rose-600 hover:underline">{{ __('Remove') }}</button>
                                        </div>
                                        @error('form.pricing.plans.' . $planIndex . '.features.' . $featureIndex . '.' . $baseLocale)
                                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                        @enderror
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section x-data="{ open: (localStorage.getItem('landing:open:testimonials') ?? '0') === '1' }" x-cloak class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200/60 space-y-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">{{ __('Testimonials') }}</h2>
                            <p class="text-sm text-gray-600">{{ __('Highlight customer quotes to build trust.') }}</p>
                        </div>
                        <div class="flex items-center gap-2" x-data="{saved:false}" x-on:section-saved.window="if($event.detail.section==='testimonials'){ saved=true; setTimeout(()=>saved=false, 1500) }">
                            <button type="button" wire:click="saveSection('testimonials')"
                                class="rounded-full bg-indigo-600 px-3 py-1 text-white hover:bg-indigo-700"
                                wire:loading.attr="disabled" wire:target="saveSection">{{ __('Save') }}</button>
                            <span x-show="saved" class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-medium text-emerald-700">✓ {{ __('Saved') }}</span>
                            <button type="button" wire:click="addTestimonial"
                                class="inline-flex items-center gap-1 rounded-full border border-indigo-200 px-3 py-1 text-xs font-medium text-indigo-600 hover:bg-indigo-50">
                                <span>+</span>{{ __('Add Testimonial') }}
                            </button>
                            <button type="button"
                                class="rounded-full border px-2 py-1 text-[11px] font-medium text-gray-700 hover:bg-gray-50"
                                @click="open = !open; localStorage.setItem('landing:open:testimonials', open ? '1' : '0')">
                                <span x-show="open">{{ __('Collapse') }}</span>
                                <span x-show="!open">{{ __('Expand') }}</span>
                            </button>
                        </div>
                    </div>

                    <div x-show="open" x-transition>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Testimonials Headline') }} ({{ strtoupper($activeLocale) }})</label>
                        <input type="text" wire:model.defer="form.testimonials.headline.{{ $activeLocale }}"
                            class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        @error('form.testimonials.headline.' . $activeLocale)
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-4">
                        @foreach($form['testimonials']['items'] as $testimonialIndex => $testimonial)
                            <div wire:key="testimonial-{{ $testimonialIndex }}" class="rounded-2xl border border-gray-200 p-4 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold uppercase text-gray-500">{{ __('Testimonial') }} #{{ $testimonialIndex + 1 }}</span>
                                    <button type="button" wire:click="removeTestimonial({{ $testimonialIndex }})"
                                        class="text-xs font-medium text-rose-600 hover:underline">{{ __('Remove') }}</button>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600">{{ __('Quote') }} ({{ strtoupper($activeLocale) }})</label>
                                    <textarea rows="3" wire:model.defer="form.testimonials.items.{{ $testimonialIndex }}.quote.{{ $activeLocale }}"
                                        class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                    @error('form.testimonials.items.' . $testimonialIndex . '.quote.' . $activeLocale)
                                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600">{{ __('Author') }} ({{ strtoupper($activeLocale) }})</label>
                                    <input type="text" wire:model.defer="form.testimonials.items.{{ $testimonialIndex }}.author.{{ $activeLocale }}"
                                        class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    @error('form.testimonials.items.' . $testimonialIndex . '.author.' . $activeLocale)
                                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section x-data="{ open: (localStorage.getItem('landing:open:cta') ?? '0') === '1' }" x-cloak class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200/60 space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">{{ __('Footer CTA') }}</h2>
                        <div class="flex items-center gap-2" x-data="{saved:false}" x-on:section-saved.window="if($event.detail.section==='cta'){ saved=true; setTimeout(()=>saved=false, 1500) }">
                            <button type="button" wire:click="saveSection('cta')"
                                class="rounded-full bg-indigo-600 px-3 py-1 text-white hover:bg-indigo-700"
                                wire:loading.attr="disabled" wire:target="saveSection">{{ __('Save') }}</button>
                            <span x-show="saved" class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-medium text-emerald-700">✓ {{ __('Saved') }}</span>
                            <button type="button"
                                class="rounded-full border px-2 py-1 text-[11px] font-medium text-gray-700 hover:bg-gray-50"
                                @click="open = !open; localStorage.setItem('landing:open:cta', open ? '1' : '0')">
                                <span x-show="open">{{ __('Collapse') }}</span>
                                <span x-show="!open">{{ __('Expand') }}</span>
                            </button>
                        </div>
                    </div>
                    <div x-show="open" x-transition class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-600">{{ __('Primary CTA Label') }} ({{ strtoupper($activeLocale) }})</label>
                            <input type="text" wire:model.defer="form.cta.primary.label.{{ $activeLocale }}"
                                class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            @error('form.cta.primary.label.' . $activeLocale)
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600">{{ __('Primary CTA Link') }}</label>
                            <input type="text" wire:model.defer="form.cta.primary.href"
                                class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            @error('form.cta.primary.href')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600">{{ __('Secondary CTA Label') }} ({{ strtoupper($activeLocale) }})</label>
                            <input type="text" wire:model.defer="form.cta.secondary.label.{{ $activeLocale }}"
                                class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            @error('form.cta.secondary.label.' . $activeLocale)
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600">{{ __('Secondary CTA Link') }}</label>
                            <input type="text" wire:model.defer="form.cta.secondary.href"
                                class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            @error('form.cta.secondary.href')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>

                <div class="flex items-center justify-end gap-3 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-200/60">
                    <button type="button" wire:click="save"
                        class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-1"
                        wire:loading.attr="disabled" wire:target="save,saveSection,logoUpload,heroUpload,galleryUploads">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m-12 4.5H18m-9 4.5h9M6 7.5h12M9 3h6" />
                        </svg>
                        <span>{{ __('Save changes') }}</span>
                    </button>
                    <span wire:loading class="text-xs text-gray-500">{{ __('Saving…') }}</span>
                </div>
            </div>

            <aside class="space-y-5">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200/60">
                    <h2 class="text-base font-semibold text-gray-900">{{ __('Structure Overview (defaults)') }}</h2>
                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('Reference payload for all sections. Unset keys fall back to this structure.') }}
                    </p>
                    <div class="mt-4 rounded-xl border border-gray-200 bg-slate-950/5 p-4">
                        <pre class="max-h-[520px] overflow-auto text-xs leading-relaxed text-slate-800">{{ $defaultsJson }}</pre>
                    </div>
                </div>

                <div class="rounded-2xl bg-white shadow-sm ring-1 ring-gray-200/60">
                    <div class="flex flex-col gap-3 border-b border-gray-100 px-4 py-3">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-900">{{ __('Live Preview') }}</h3>
                            <button type="button" wire:click="refreshPreview"
                                class="inline-flex items-center gap-1 rounded-full border border-indigo-200 px-3 py-1 text-[11px] font-medium text-indigo-600 hover:bg-indigo-50">
                                &#10227; {{ __('Refresh') }}
                            </button>
                        </div>
                        <div class="flex items-center gap-2">
                            @php $modes = ['desktop' => 'Desktop', 'tablet' => 'Tablet', 'mobile' => 'Mobile']; @endphp
                            @foreach ($modes as $key => $label)
                                <button type="button" wire:click="setPreviewMode('{{ $key }}')"
                                    class="inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-xs font-medium transition
                                    {{ $previewMode === $key ? 'border-indigo-500 bg-indigo-600 text-white' : 'border-gray-200 bg-white text-gray-700 hover:border-indigo-300 hover:text-indigo-600' }}">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                    <div class="preview-frame relative flex justify-center bg-gray-100 px-4 py-5">
                        @if($previewError)
                            <div class="w-full rounded-xl border border-dashed border-rose-300 bg-rose-50 px-4 py-10 text-center text-sm text-rose-600">
                                {{ $previewError }}
                            </div>
                        @elseif($previewHtml)
                            @php
                                $frameClasses = [
                                    'desktop' => 'w-full',
                                    'tablet' => 'w-[768px] max-w-full',
                                    'mobile' => 'w-[390px] max-w-full',
                                ];
                            @endphp
                            <iframe title="Landing preview"
                                class="h-[520px] border border-gray-300 bg-white shadow-inner transition-all duration-300 {{ $frameClasses[$previewMode] ?? 'w-full' }}"
                                srcdoc="{{ e($previewHtml) }}"></iframe>
                        @else
                            <div class="w-full rounded-xl border border-dashed border-gray-200 bg-white px-4 py-10 text-center text-sm text-gray-500">
                                {{ __('Preview loading…') }}
                            </div>
                        @endif
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>
