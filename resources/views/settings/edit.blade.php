<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ __('Tenant Settings') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <x-flash-status />
            @php
                $settings = $tenant->settings ?? [];
                $footer = $settings['footer'] ?? [];
                $existingLinks = old('footer_links', data_get($footer, 'links', []));
                $existingSocial = old('footer_social', data_get($footer, 'social', []));
                $footerLinks = collect($existingLinks)->map(function ($link) {
                    return [
                        'label' => $link['label'] ?? '',
                        'href' => $link['href'] ?? '',
                    ];
                })->pad(4, ['label' => '', 'href' => ''])->take(4)->values();
                $footerSocial = collect($existingSocial)->map(function ($item) {
                    return [
                        'label' => $item['label'] ?? '',
                        'url' => $item['url'] ?? '',
                    ];
                })->pad(4, ['label' => '', 'url' => ''])->take(4)->values();
            @endphp

            <div class="rounded-md bg-white p-6 shadow">
                <form method="post" action="{{ route('settings.update') }}" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" name="name" class="mt-1 block w-full" required :value="$tenant->name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div class="mb-4">
                        <x-input-label for="slug" :value="__('Subdomain')" />
                        <div class="flex items-center gap-2">
                            <x-text-input id="slug" name="slug" class="mt-1 block w-full" required :value="$tenant->slug" />
                            <span class="text-sm text-gray-500">.{{ config('tenancy.base_domain') }}</span>
                        </div>
                        <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                    </div>
                    <div class="mb-6">
                        <x-input-label for="timezone" :value="__('Timezone')" />
                        <select id="timezone" name="timezone" class="mt-1 block w-full rounded-md border-gray-300">
                            @php
                                $tz = $tenant->settings['timezone'] ?? config('app.timezone');
                            @endphp
                            @foreach(DateTimeZone::listIdentifiers() as $z)
                                <option value="{{ $z }}" @selected($tz === $z)>{{ $z }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('timezone')" class="mt-2" />
                    </div>

                    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <x-input-label for="primary_color" :value="__('Primary Color')" />
                            @php
                                $primary = $tenant->settings['primary_color'] ?? '#4f46e5';
                            @endphp
                            <div class="mt-1 flex items-center gap-2">
                                <input id="primary_color" name="primary_color" type="color" value="{{ $primary }}" class="h-10 w-14 rounded border-gray-300 p-0" />
                                <x-text-input value="{{ $primary }}" class="w-32" />
                            </div>
                            <x-input-error :messages="$errors->get('primary_color')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="accent_color" :value="__('Accent Color')" />
                            @php
                                $accent = $tenant->settings['accent_color'] ?? '#0ea5e9';
                            @endphp
                            <div class="mt-1 flex items-center gap-2">
                                <input id="accent_color" name="accent_color" type="color" value="{{ $accent }}" class="h-10 w-14 rounded border-gray-300 p-0" />
                                <x-text-input value="{{ $accent }}" class="w-32" />
                            </div>
                            <x-input-error :messages="$errors->get('accent_color')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <x-input-label for="font_color" :value="__('Font Color')" />
                            @php
                                $fontColor = $tenant->settings['font_color'] ?? '#0b3849';
                            @endphp
                            <div class="mt-1 flex items-center gap-2">
                                <input id="font_color" name="font_color" type="color" value="{{ $fontColor }}" class="h-10 w-14 rounded border-gray-300 p-0" />
                                <x-text-input value="{{ $fontColor }}" class="w-32" />
                            </div>
                            <x-input-error :messages="$errors->get('font_color')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <x-input-label for="currency" :value="__('Currency')" />
                            @php
                                $currency = $tenant->settings['currency'] ?? 'IQD';
                            @endphp
                            <select id="currency" name="currency" class="mt-1 block w-full rounded-md border-gray-300">
                                <option value="IQD" @selected($currency==='IQD')>{{ __('IQD (Iraqi Dinar)') }}</option>
                                <option value="USD" @selected($currency==='USD')>{{ __('USD (US Dollar)') }}</option>
                                <option value="JOD" @selected(in_array($currency, ['JOD', 'JD'], true))>{{ __('JOD (Jordanian Dinar)') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('currency')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="country" :value="__('Country')" />
                            @php
                                $country = $tenant->settings['country'] ?? 'IQ';
                            @endphp
                            <select id="country" name="country" class="mt-1 block w-full rounded-md border-gray-300">
                                <option value="IQ" @selected($country==='IQ')>{{ __('Iraq') }}</option>
                                <option value="US" @selected($country==='US')>{{ __('USA') }}</option>
                                <option value="JO" @selected($country==='JO')>{{ __('Jordan') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('country')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <x-input-label for="logo" value="Logo (PNG/JPG/SVG)" />
                            <input id="logo" name="logo" type="file" accept="image/png,image/jpeg,image/svg+xml" class="mt-1 block w-full rounded-md border-gray-300" />
                            @if(!empty($tenant->settings['logo_url']))
                                <div class="mt-2 flex items-center gap-3">
                                    <img src="{{ Str::startsWith($tenant->settings['logo_url'], ['http://','https://']) ? $tenant->settings['logo_url'] : asset($tenant->settings['logo_url']) }}" alt="logo" class="h-10 w-auto" />
                                    <span class="text-xs text-gray-500 truncate">{{ $tenant->settings['logo_url'] }}</span>
                                </div>
                            @endif
                            <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="favicon" value="Favicon (ICO/PNG)" />
                            <input id="favicon" name="favicon" type="file" accept="image/x-icon,image/png" class="mt-1 block w-full rounded-md border-gray-300" />
                            @if(!empty($tenant->settings['favicon_url']))
                                <div class="mt-2 flex items-center gap-3">
                                    <img src="{{ Str::startsWith($tenant->settings['favicon_url'], ['http://','https://']) ? $tenant->settings['favicon_url'] : asset($tenant->settings['favicon_url']) }}" alt="favicon" class="h-6 w-6" />
                                    <span class="text-xs text-gray-500 truncate">{{ $tenant->settings['favicon_url'] }}</span>
                                </div>
                            @endif
                            <x-input-error :messages="$errors->get('favicon')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <x-input-label for="typography" value="Typography" />
                            @php
                                $typ = $tenant->settings['typography'] ?? 'system';
                            @endphp
                            <select id="typography" name="typography" class="mt-1 block w-full rounded-md border-gray-300">
                                <option value="system" @selected($typ==='system')>System Sans</option>
                                <option value="serif" @selected($typ==='serif')>Serif</option>
                                <option value="mono" @selected($typ==='mono')>Monospace</option>
                            </select>
                            <x-input-error :messages="$errors->get('typography')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="header_bg" value="Header Background (JPG/PNG/WebP/SVG)" />
                            <input id="header_bg" name="header_bg" type="file" accept="image/*" class="mt-1 block w-full rounded-md border-gray-300" />
                            @if(!empty($tenant->settings['header_bg_url']))
                                <div class="mt-2">
                                    <img src="{{ Str::startsWith($tenant->settings['header_bg_url'], ['http://','https://']) ? $tenant->settings['header_bg_url'] : asset($tenant->settings['header_bg_url']) }}" alt="header background" class="h-20 w-full rounded object-cover" />
                                </div>
                            @endif
                            <x-input-error :messages="$errors->get('header_bg')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <x-input-label for="tagline" :value="__('Public tagline')" />
                            <x-text-input id="tagline" name="tagline" class="mt-1 block w-full" :value="old('tagline', $settings['tagline'] ?? '')" placeholder="{{ __('Inspiring residences for modern living') }}" />
                            <x-input-error :messages="$errors->get('tagline')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="footer_quote" :value="__('Footer quote (optional)')" />
                            <x-text-input id="footer_quote" name="footer_quote" class="mt-1 block w-full" :value="old('footer_quote', data_get($footer, 'quote', ''))" placeholder="{{ __('A house is made of walls and beams; a home is built with love and dreams.') }}" />
                            <x-input-error :messages="$errors->get('footer_quote')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <x-input-label for="contact_email" :value="__('Contact email')" />
                            <x-text-input id="contact_email" name="contact_email" class="mt-1 block w-full" :value="old('contact_email', $settings['contact_email'] ?? '')" placeholder="contact@example.com" />
                            <x-input-error :messages="$errors->get('contact_email')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="contact_phone" :value="__('Contact phone')" />
                            <x-text-input id="contact_phone" name="contact_phone" class="mt-1 block w-full" :value="old('contact_phone', $settings['contact_phone'] ?? '')" placeholder="+964 7XX XXX XXXX" />
                            <x-input-error :messages="$errors->get('contact_phone')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-gray-700">{{ __('Footer quick links') }}</h3>
                        <p class="text-xs text-gray-500 mb-3">{{ __('Add up to 4 helpful links that appear in the footer.') }}</p>
                        <div class="space-y-4">
                            @foreach($footerLinks as $index => $link)
                                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                    <div>
                                        <x-input-label :for="'footer_links_label_'.$index" :value="__('Link label')" />
                                        <x-text-input :id="'footer_links_label_'.$index" :name="'footer_links['.$index.'][label]'" class="mt-1 block w-full" :value="$link['label']" />
                                        <x-input-error :messages="$errors->get('footer_links.'.$index.'.label')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label :for="'footer_links_href_'.$index" :value="__('Link URL')" />
                                        <x-text-input :id="'footer_links_href_'.$index" :name="'footer_links['.$index.'][href]'" class="mt-1 block w-full" :value="$link['href']" placeholder="https://..." />
                                        <x-input-error :messages="$errors->get('footer_links.'.$index.'.href')" class="mt-2" />
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-gray-700">{{ __('Social links') }}</h3>
                        <p class="text-xs text-gray-500 mb-3">{{ __('Add links to your social media profiles.') }}</p>
                        <div class="space-y-4">
                            @foreach($footerSocial as $index => $item)
                                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                    <div>
                                        <x-input-label :for="'footer_social_label_'.$index" :value="__('Platform')" />
                                        <x-text-input :id="'footer_social_label_'.$index" :name="'footer_social['.$index.'][label]'" class="mt-1 block w-full" :value="$item['label']" placeholder="Facebook, Instagram..." />
                                        <x-input-error :messages="$errors->get('footer_social.'.$index.'.label')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label :for="'footer_social_url_'.$index" :value="__('Profile URL')" />
                                        <x-text-input :id="'footer_social_url_'.$index" :name="'footer_social['.$index.'][url]'" class="mt-1 block w-full" :value="$item['url']" placeholder="https://..." />
                                        <x-input-error :messages="$errors->get('footer_social.'.$index.'.url')" class="mt-2" />
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-6">
                        @php
                            $newsletterEnabled = (bool) old('footer_show_newsletter', data_get($footer, 'show_newsletter', false));
                        @endphp
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="footer_show_newsletter" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" {{ $newsletterEnabled ? 'checked' : '' }} />
                            <span class="text-sm text-gray-700">{{ __('Enable newsletter signup in footer') }}</span>
                        </label>
                        <div class="mt-3">
                            <x-input-label for="footer_newsletter_text" :value="__('Newsletter copy')" />
                            <x-text-input id="footer_newsletter_text" name="footer_newsletter_text" class="mt-1 block w-full" :value="old('footer_newsletter_text', data_get($footer, 'newsletter_text', ''))" placeholder="{{ __('Be the first to hear about new listings.') }}" />
                            <x-input-error :messages="$errors->get('footer_newsletter_text')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mb-6">
                        @php
                            $showTypes = (bool) ($tenant->settings['home_show_types'] ?? false);
                        @endphp
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="home_show_types" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" {{ $showTypes ? 'checked' : '' }} />
                            <span class="text-sm text-gray-700">{{ __('Show Property Types scroller on public home') }}</span>
                        </label>
                    </div>

                    <div class="mb-6">
                        @php
                            $showCities = (bool) ($tenant->settings['home_show_cities'] ?? false);
                        @endphp
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="home_show_cities" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" {{ $showCities ? 'checked' : '' }} />
                            <span class="text-sm text-gray-700">{{ __('Show Popular Cities carousel on public home') }}</span>
                        </label>
                    </div>
                    <div class="mb-6">
                        @php
                            $showLatest = (bool) ($tenant->settings['home_show_latest'] ?? true);
                        @endphp
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="home_show_latest" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" {{ $showLatest ? 'checked' : '' }} />
                            <span class="text-sm text-gray-700">{{ __('Show Latest rental listings on public home') }}</span>
                        </label>
                    </div>
                    <div class="mb-6">
                        @php
                            $showSearch = (bool) ($tenant->settings['home_show_search'] ?? true);
                        @endphp
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="home_show_search" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" {{ $showSearch ? 'checked' : '' }} />
                            <span class="text-sm text-gray-700">{{ __('settings.show_search_promotion') }}</span>
                        </label>
                    </div>
                    <div class="mb-6">
                        @php
                            $showMap = (bool) ($tenant->settings['home_show_map'] ?? true);
                        @endphp
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="home_show_map" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" {{ $showMap ? 'checked' : '' }} />
                            <span class="text-sm text-gray-700">{{ __('settings.show_map') }}</span>
                        </label>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ $tenant ? route('dashboard') : route('admin.index') }}" class="rounded-md border px-3 py-2 text-gray-700 hover:bg-gray-50">{{ __('Cancel') }}</a>
                        <button class="btn-brand inline-flex items-center rounded-md px-3 py-2 text-white" type="submit">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
