<?php

namespace App\Http\Middleware;

use App\Services\Tenancy\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStaff
{
    public function __construct(protected TenantManager $tenants) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $tenant = $this->tenants->tenant();
        if (! $user || ! $tenant) {
            abort(403);
        }

        if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($tenant->getKey());
        }

        $pivotRole = strtolower((string) ($user->tenants()->whereKey($tenant->getKey())->first()?->pivot?->role));
        $hasSpatie = method_exists($user, 'hasAnyRole');
        $hasSpatieRole = $hasSpatie && method_exists($user, 'roles')
            ? $user->roles()->where('roles.tenant_id', $tenant->getKey())->exists()
            : false;

        $isResident = ($pivotRole === 'resident') || ($hasSpatie && $user->hasAnyRole(['resident']));
        if ($isResident) {
            return redirect()->route('profile.edit');
        }

        $isStaff = $hasSpatieRole || ($pivotRole !== '' && $pivotRole !== 'resident');

        if (! $isStaff) {
            abort(403);
        }

        return $next($request);
    }
}

