<?php

namespace App\Http\Middleware;

use App\Services\Locale\LocaleResolver;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App as AppFacade;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function __construct(private LocaleResolver $resolver) {}

    public function handle(Request $request, Closure $next): Response
    {
        $selected = $this->resolver->resolve($request);
        AppFacade::setLocale($selected);

        $queryKey = (string) config('locales.cookie_name', 'lang');
        if ($request->filled($queryKey)) {
            $this->queueLocaleCookie($request, $selected);
        }
        
        return $next($request);
    }

    private function queueLocaleCookie(Request $request, string $locale): void
    {
        $minutes = (int) config('locales.cookie_minutes', 60 * 24 * 180);
        $domain = $this->cookieDomain($request);
        Cookie::queue(
            Cookie::make(
                config('locales.cookie_name', 'lang'),
                $locale,
                $minutes,
                '/',
                $domain
            )
        );
    }

    private function cookieDomain(Request $request): ?string
    {
        $host = $request->getHost();

        $sessionDomain = $this->normalizeDomain(config('session.domain'));
        if ($sessionDomain && $this->hostMatchesDomain($host, $sessionDomain)) {
            return $sessionDomain;
        }

        $base = $this->normalizeDomain((string) config('tenancy.base_domain'));
        if ($base && $this->hostMatchesDomain($host, $base)) {
            return $base;
        }

        return null;
    }

    private function normalizeDomain(?string $domain): ?string
    {
        $domain = trim((string) $domain);
        if ($domain === '') {
            return null;
        }

        return ltrim($domain, '.');
    }

    private function hostMatchesDomain(string $host, string $domain): bool
    {
        return $host === $domain || Str::endsWith($host, '.'.$domain);
    }
}
