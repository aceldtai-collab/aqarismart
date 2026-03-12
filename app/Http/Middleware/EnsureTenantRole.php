<?php

namespace App\Http\Middleware;

use App\Services\Tenancy\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantRole
{
    public function __construct(protected TenantManager $tenants)
    {
    }

    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        if (! $user) {
            abort(403);
        }

        $tenant = $this->tenants->tenant();
        if (! $tenant) {
            abort(403);
        }

        $candidates = [];
        foreach ($roles as $role) {
            foreach (explode('|', $role) as $segment) {
                $segment = strtolower(trim($segment));
                if ($segment !== '' && ! in_array($segment, $candidates, true)) {
                    $candidates[] = $segment;
                }
            }
        }

        if (empty($candidates)) {
            return $next($request);
        }

        $superAdmins = array_filter(array_map('trim', explode(',', (string) env('SUPER_ADMIN_EMAILS', ''))));
        if ($superAdmins && in_array($user->email, $superAdmins, true)) {
            return $next($request);
        }

        if (class_exists(PermissionRegistrar::class)) {
            app(PermissionRegistrar::class)->setPermissionsTeamId($tenant->getKey());
            if (method_exists($user, 'hasAnyRole') && $user->hasAnyRole($candidates)) {
                return $next($request);
            }
        }

        $pivotRole = $user->tenants()->whereKey($tenant->getKey())->first()?->pivot?->role;
        if ($pivotRole && in_array(strtolower($pivotRole), $candidates, true)) {
            return $next($request);
        }

        abort(403);
    }
}
