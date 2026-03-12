<?php

namespace App\Http\Middleware;

use App\Services\Tenancy\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFeatures
{
    public function __construct(protected TenantManager $tenants) {}

    public function handle(Request $request, Closure $next, string $features): Response
    {
        $tenant = $this->tenants->tenant();
        if (!$tenant) {
            abort(404);
        }

        foreach (explode(',', $features) as $feature) {
            $feature = trim($feature);
            if ($feature !== '' && ! $tenant->canUse($feature)) {
                abort(403, 'Feature not available for current plan.');
            }
        }

        return $next($request);
    }
}

