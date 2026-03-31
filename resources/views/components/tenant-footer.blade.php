@props(['tenant'])

@php
    $settings = $tenant->settings ?? [];
    $footer = data_get($settings, 'footer', []);
    $links = collect(data_get($footer, 'links', []))
        ->filter(fn ($link) => filled(data_get($link, 'label')) && filled(data_get($link, 'href')));
    $social = collect(data_get($footer, 'social', []))
        ->filter(fn ($entry) => filled(data_get($entry, 'url')));
    $quote = data_get($footer, 'quote');
    $showNewsletter = (bool) data_get($footer, 'show_newsletter', false);
    $newsletterText = data_get($footer, 'newsletter_text', __('Be the first to hear about new listings.'));
    $defaultSocialIcons = [
        'facebook' => 'fab fa-facebook-f',
        'instagram' => 'fab fa-instagram',
        'linkedin' => 'fab fa-linkedin-in',
        'twitter' => 'fab fa-twitter',
        'x' => 'fab fa-x-twitter',
        'youtube' => 'fab fa-youtube',
        'tiktok' => 'fab fa-tiktok',
    ];
@endphp

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<footer class="pt-12 pb-8 text-slate-100" style="background: linear-gradient(135deg, #0f172a, #1e293b);">
    <div class="mx-auto max-w-6xl px-6">
        <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
            <div class="space-y-4">
                <h5 class="text-xl font-semibold text-white">{{ $tenant->name }}</h5>
                <p class="text-sm text-slate-300">
                    {{ data_get($settings, 'tagline', __('Quality homes, trusted management.')) }}
                </p>
                @if($quote)
                    <blockquote class="border-l-4 border-indigo-400 pl-4 text-sm text-slate-400 italic">
                        “{{ $quote }}”
                    </blockquote>
                @endif
            </div>

            <div>
                <h6 class="mb-3 text-sm font-semibold uppercase tracking-wide text-indigo-200">
                    {{ __('Quick Links') }}
                </h6>
                <ul class="space-y-2 text-sm text-slate-300">
                    @forelse($links as $link)
                        <li>
                            <a href="{{ $link['href'] }}" class="hover:text-indigo-300 transition-colors">
                                {{ $link['label'] }}
                            </a>
                        </li>
                    @empty
                        <li class="text-slate-500">{{ __('No links configured yet.') }}</li>
                    @endforelse
                </ul>
            </div>

            <div>
                <h6 class="mb-3 text-sm font-semibold uppercase tracking-wide text-indigo-200">
                    {{ __('Contact') }}
                </h6>
                <ul class="space-y-2 text-sm text-slate-300">
                    @if($email = data_get($settings, 'contact_email'))
                        <li>
                            <i class="fa-solid fa-envelope mr-2 text-indigo-300"></i>
                            <a href="mailto:{{ $email }}" class="hover:text-indigo-300 transition-colors">
                                {{ $email }}
                            </a>
                        </li>
                    @endif
                    @if($phone = data_get($settings, 'contact_phone'))
                        <li>
                            <i class="fa-solid fa-phone mr-2 text-indigo-300"></i>
                            <a href="tel:{{ preg_replace('/\s+/', '', $phone) }}" class="hover:text-indigo-300 transition-colors">
                                {{ $phone }}
                            </a>
                        </li>
                    @endif
                </ul>

                @if($social->isNotEmpty())
                    <div class="mt-4 flex items-center gap-3 text-lg">
                        @foreach($social as $entry)
                            @php
                                $network = \Illuminate\Support\Str::slug(data_get($entry, 'label', ''));
                                $iconClass = $defaultSocialIcons[$network] ?? 'fa-solid fa-link';
                            @endphp
                            <a href="{{ $entry['url'] }}" class="text-slate-400 hover:text-indigo-300 transition-transform transform hover:scale-110" target="_blank" rel="noopener">
                                <i class="{{ $iconClass }}"></i>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            @if($showNewsletter)
                <div>
                    <h6 class="mb-3 text-sm font-semibold uppercase tracking-wide text-indigo-200">
                        {{ __('Stay Updated') }}
                    </h6>
                    <p class="mb-4 text-sm text-slate-300">{{ $newsletterText }}</p>
                    <form class="space-y-3">
                        <input type="email" class="w-full rounded-md border border-indigo-400/40 bg-gray-50/40 px-3 py-2 text-sm text-white placeholder:text-slate-400 focus:border-indigo-300 focus:outline-none focus:ring focus:ring-indigo-500/40" placeholder="{{ __('Your email address') }}">
                        <button type="button" class="w-full rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-400 transition-colors">
                            {{ __('Subscribe') }}
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <div class="mt-10 border-t border-slate-700/60 pt-6 text-center text-xs text-slate-500">
            &copy; {{ now()->year }} {{ $tenant->name }}. {{ __('All rights reserved.') }}
        </div>
    </div>
</footer>
