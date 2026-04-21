<?php

namespace App\Http\Middleware;

use App\Services\Tenancy\TenantManager;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Spatie\Permission\PermissionRegistrar;

class IdentifyTenant
{
    public function __construct(protected TenantManager $manager)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        $base = config('tenancy.base_domain');
        $tenant = $this->manager->resolveFromHost($host, $base);

        if (! $tenant) {
            // Allow base domain / non-tenant hosts to continue without forcing a tenant.
            return $next($request);
        }

        $user = $request->user();
        $superAdmins = array_filter(array_map('trim', explode(',', (string) env('SUPER_ADMIN_EMAILS', ''))));
        $isSuperAdmin = $user && $superAdmins && in_array($user->email, $superAdmins, true);

        if ($user && ! $isSuperAdmin) {
            $belongs = $user->tenants()->whereKey($tenant->getKey())->exists();
            if (! $belongs) {
                if ($request->isMethod('get')) {
                    // By default the app logged-out users when they navigated to a tenant
                    // they did not belong to. To allow preserving logged-in state
                    // across tenant subdomains (read-only browsing), set
                    // TENANCY_PRESERVE_AUTH_ON_SWITCH=true in the environment.
                    $preserve = (bool) env('TENANCY_PRESERVE_AUTH_ON_SWITCH', false);
                    if (! $preserve) {
                        Auth::logout();
                        $request->session()->invalidate();
                        $request->session()->regenerateToken();
                        $user = null;
                    }
                } else {
                    abort(403, 'You do not have access to this tenant.');
                }
            }
        }

        $this->manager->setTenant($tenant);

        // Update APP_URL for current subdomain
        $scheme = $request->getScheme();
        $port = $request->getPort();
        $defaultPort = $scheme === 'https' ? 443 : 80;
        $portPart = $port && $port !== $defaultPort ? ':'.$port : '';
        $currentUrl = sprintf('%s://%s%s', $scheme, $host, $portPart);
        config(['app.url' => $currentUrl]);
        URL::forceRootUrl($currentUrl);

        // Ensure domain route parameter {tenant} is auto-filled for URL generation
        if (method_exists($tenant, 'getAttribute')) {
            $slug = $tenant->slug ?? null;
            if (is_string($slug) && $slug !== '') {
                URL::defaults(['tenant_slug' => $slug]);
            }
        }

        // If Spatie Permission is installed with teams, set current team id to tenant
        if (class_exists(PermissionRegistrar::class)) {
            app(PermissionRegistrar::class)->setPermissionsTeamId($tenant->getKey());
        }

        return $next($request);
    }
}
