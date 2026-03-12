<?php

namespace App\Services\Tenancy;

use App\Models\Tenant;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class TenantManager
{
    protected ?Tenant $tenant = null;

    public function setTenant(?Tenant $tenant): void
    {
        $this->tenant = $tenant;
    }

    public function tenant(): ?Tenant
    {
        return $this->tenant;
    }

    public function resolveFromHost(string $host, ?string $baseDomain = null): ?Tenant
    {
        $host = Str::lower($host);

        // Determine base domain
        $baseDomain = $baseDomain ?: parse_url(config('app.url'), PHP_URL_HOST) ?: $host;

        // If host ends with the base domain and has a subdomain, extract it
        if (Str::endsWith($host, $baseDomain)) {
            $left = Str::before($host, $baseDomain);
            $left = rtrim($left, '.');

            if ($left !== '' && $left !== 'www') {
                $segments = array_values(array_filter(explode('.', $left)));
                if ($segments && $segments[0] === 'www') {
                    $segments = array_slice($segments, 1);
                }

                $subdomain = $segments[0] ?? null;
                if ($subdomain) {
                    return Tenant::where('slug', $subdomain)->first();
                }
            }
        }

        // Fallback: when using direct subdomain without explicit base matching
        $parts = explode('.', $host);
        if (count($parts) >= 3) {
            $candidate = $parts[0];
            if ($candidate !== 'www') {
                return Tenant::where('slug', $candidate)->first();
            }
        }

        return null;
    }

    public function tenantUrl(Tenant $tenant, string $path = '/'): string
    {
        /** @var Request $request */
        $request = request();
        $scheme = $request->getScheme() ?: 'http';
        $base = config('tenancy.base_domain');
        $host = $tenant->slug.'.'.$base;
        $port = $request->getPort();
        $defaultPort = $scheme === 'https' ? 443 : 80;
        $portPart = $port && $port !== $defaultPort ? ':'.$port : '';
        $path = '/'.ltrim($path, '/');
        return sprintf('%s://%s%s%s', $scheme, $host, $portPart, $path);
    }
}
