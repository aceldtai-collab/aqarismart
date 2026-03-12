<?php

namespace App\Http\Middleware;

use App\Services\Tenancy\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureResident
{
    public function __construct(protected TenantManager $tenants) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $tenant = $this->tenants->tenant();
        if (! $user || ! $tenant) {
            abort(403);
        }

        $pivotRole = strtolower((string) ($user->tenants()->whereKey($tenant->getKey())->first()?->pivot?->role));
        $hasSpatie = method_exists($user, 'hasAnyRole');
        $isResident = ($pivotRole === 'resident') || ($hasSpatie && $user->hasAnyRole(['resident']));

        if (! $isResident) {
            return redirect()->route('profile.edit');
        }

        return $next($request);
    }
}

