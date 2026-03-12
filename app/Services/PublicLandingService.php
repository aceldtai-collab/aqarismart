<?php

namespace App\Services;

use App\Models\SystemSetting;
use App\Models\Tenant;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Support\Arr;

class PublicLandingService
{
    public const CACHE_PUBLIC = 'landing:public';

    public function __construct(private readonly Cache $cache)
    {
    }

    /**
     * Resolve the landing page data for the base domain (no tenant).
     */
    public function forPublicDomain(): array
    {
        $locale = app()->getLocale();

        return $this->cache->remember(self::CACHE_PUBLIC . ':' . $locale, 60, function () {
            $defaults = config('public_site_landing', []);
            if (! is_array($defaults)) {
                $defaults = [];
            }
            $overrides = SystemSetting::getValue('landing', []);
            if (! is_array($overrides)) {
                $overrides = [];
            }

            return $this->prepare(array_replace_recursive($defaults, $overrides));
        });
    }

    /**
     * Resolve tenant-specific landing data. Currently tenants do not expose a landing page,
     * but this method keeps the contract ready should we enable it later.
     */
    public function forTenant(Tenant $tenant): array
    {
        $locale = app()->getLocale();
        $cacheKey = 'landing:tenant:' . $tenant->getKey() . ':' . $locale;

        return $this->cache->remember($cacheKey, 60, function () use ($tenant) {
            $defaults = config('public_site_landing', []);
            if (! is_array($defaults)) {
                $defaults = [];
            }
            $overrides = Arr::get($tenant->settings ?? [], 'landing', []);

            return $this->prepare(array_replace_recursive($defaults, $overrides));
        });
    }

    /**
     * Build a preview payload without touching cache (used by admin editor).
     */
    public function preview(array $overrides): array
    {
        $defaults = config('public_site_landing', []);
        if (! is_array($defaults)) {
            $defaults = [];
        }

        return $this->prepare(array_replace_recursive($defaults, $overrides));
    }

    protected function prepare(array $data): array
    {
        $data = $this->normalizeTranslations($data);

        // Resolve dynamic placeholders (e.g., current year, route placeholders).
        $data['footer']['copyright'] = str_replace(':year', now()->year, Arr::get($data, 'footer.copyright', ''));

        // Normalize CTA links that point to registration/book call.
        $data['cta']['primary']['href'] = $this->resolveHref(Arr::get($data, 'cta.primary.href', '#'));
        $data['cta']['secondary']['href'] = $this->resolveHref(Arr::get($data, 'cta.secondary.href', '#'));

        // SEO defaults
        $seo = Arr::get($data, 'seo', []);
        if (! is_array($seo)) {
            $seo = [];
        }
        $data['seo'] = [
            'title' => Arr::get($seo, 'title') ?: Arr::get($data, 'meta.title'),
            'description' => Arr::get($seo, 'description') ?: '',
            'robots' => Arr::get($seo, 'robots', 'index, follow'),
            'canonical' => url()->current(),
            'og_image' => Arr::get($seo, 'og_image'),
            'twitter_image' => Arr::get($seo, 'twitter_image'),
            'favicon' => Arr::get($seo, 'favicon'),
        ];

        return $data;
    }

    protected function resolveHref(?string $href): string
    {
        if (! $href) {
            return '#';
        }

        return match ($href) {
            '#register' => route('register'),
            '/book-call' => route('book-call'),
            default => $href,
        };
    }

    public function clearCache(?Tenant $tenant = null): void
    {
        foreach (array_keys($this->locales()) as $locale) {
            $this->cache->forget(self::CACHE_PUBLIC . ':' . $locale);

            if ($tenant) {
                $this->cache->forget('landing:tenant:' . $tenant->getKey() . ':' . $locale);
            }
        }
    }

    protected function normalizeTranslations(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        if ($this->isTranslationArray($value)) {
            return $this->resolveLocalizedString($value);
        }

        foreach ($value as $key => $item) {
            $value[$key] = $this->normalizeTranslations($item);
        }

        return $value;
    }

    protected function isTranslationArray(array $value): bool
    {
        if ($value === []) {
            return false;
        }

        $localeKeys = array_keys($this->locales());

        foreach ($value as $key => $item) {
            if (! in_array($key, $localeKeys, true)) {
                return false;
            }
            if (! is_string($item)) {
                return false;
            }
        }

        return true;
    }

    protected function resolveLocalizedString(array $translations): string
    {
        $locale = app()->getLocale();
        $fallback = array_key_first($this->locales()) ?? 'en';

        return $translations[$locale]
            ?? ($fallback && isset($translations[$fallback]) ? $translations[$fallback] : reset($translations));
    }

    protected function locales(): array
    {
        $labels = [
            'en' => 'English',
            'ar' => 'العربية',
        ];

        $supported = config('locales.supported');
        if (is_array($supported)) {
            $isAssoc = array_keys($supported) !== range(0, count($supported) - 1);
            if ($isAssoc) {
                return $supported;
            }

            $mapped = [];
            foreach ($supported as $code) {
                if (! is_string($code)) {
                    continue;
                }
                $mapped[$code] = $labels[$code] ?? strtoupper($code);
            }
            if ($mapped !== []) {
                return $mapped;
            }
        }

        $fallback = config('app.supported_locales');
        if (is_array($fallback)) {
            $isAssoc = array_keys($fallback) !== range(0, count($fallback) - 1);
            if ($isAssoc) {
                return $fallback;
            }

            $mapped = [];
            foreach ($fallback as $code) {
                if (! is_string($code)) {
                    continue;
                }
                $mapped[$code] = $labels[$code] ?? strtoupper($code);
            }
            if ($mapped !== []) {
                return $mapped;
            }
        }

        return [
            'en' => 'English',
            'ar' => 'العربية',
        ];
    }
}

