<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Services\Tenancy\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\HttpFoundation\Response;

class SetMobileTenantContext
{
    public function __construct(protected TenantManager $tenants)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $this->resolveTenant($request);

        if ($tenant) {
            $this->tenants->setTenant($tenant);
            $request->attributes->set('mobile_tenant', $tenant);
            URL::defaults(['tenant_slug' => $tenant->slug]);

            if (class_exists(PermissionRegistrar::class)) {
                app(PermissionRegistrar::class)->setPermissionsTeamId($tenant->getKey());
            }
        }

        return $next($request);
    }

    protected function resolveTenant(Request $request): ?Tenant
    {
        $tenantId = $request->route('tenant')
            ?? $request->route('tenant_id')
            ?? $request->input('tenant_id')
            ?? $request->header('X-Tenant-Id');

        if ($tenantId) {
            $tenant = Tenant::query()->find($tenantId);
            if ($tenant) {
                return $tenant;
            }
        }

        $tenantSlug = $request->route('tenant_slug')
            ?? $request->input('tenant_slug')
            ?? $request->header('X-Tenant-Slug');

        if (is_string($tenantSlug) && $tenantSlug !== '') {
            $tenant = Tenant::query()->where('slug', $tenantSlug)->first();
            if ($tenant) {
                return $tenant;
            }
        }

        $user = $request->user();
        if (! $user) {
            return null;
        }

        return $user->tenants()->orderBy('tenant_user.created_at')->first();
    }
}
