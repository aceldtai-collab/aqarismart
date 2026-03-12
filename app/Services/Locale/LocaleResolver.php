<?php

namespace App\Services\Locale;

use App\Services\Tenancy\TenantManager;
use Illuminate\Http\Request;

class LocaleResolver
{
    public function __construct(private TenantManager $tenants) {}

    public function resolve(Request $request): string
    {
        $supported = config('locales.supported', ['en', 'ar']);
        $default = config('locales.default', config('app.locale', 'en'));

        $queryKey = (string) config('locales.cookie_name', 'lang');
        $query = strtolower((string) $request->query($queryKey, ''));
        if ($query && in_array($query, $supported, true)) {
            $this->persist($request, $query);
            return $query;
        }

        $cookie = strtolower((string) $request->cookie($queryKey, ''));
        if ($cookie && in_array($cookie, $supported, true)) {
            return $cookie;
        }

        if ($request->hasSession()) {
            $session = strtolower((string) $request->session()->get($queryKey, ''));
            if ($session && in_array($session, $supported, true)) {
                return $session;
            }
        }

        $tenant = $this->tenants->tenant();
        if ($tenant) {
            $tenantLocale = strtolower((string) data_get($tenant->settings ?? [], 'locale', ''));
            if ($tenantLocale && in_array($tenantLocale, $supported, true)) {
                return $tenantLocale;
            }
        }

        $preferred = (string) $request->getPreferredLanguage();
        $preferredShort = strtolower(substr($preferred, 0, 2));
        if ($preferredShort && in_array($preferredShort, $supported, true)) {
            return $preferredShort;
        }

        return $default;
    }

    private function persist(Request $request, string $locale): void
    {
        if ($request->hasSession()) {
            $request->session()->put(config('locales.cookie_name', 'lang'), $locale);
        }
    }
}
