<?php

namespace App\Http\Middleware;

use App\Services\Tenancy\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function __construct(private TenantManager $tenants)
    {
    }

    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (! Auth::guard($guard)->check()) {
                continue;
            }

            if ($request->expectsJson()) {
                abort(403);
            }

            $tenant = $this->tenants->tenant();

            if (! $tenant) {
                $tenant = $this->tenants->resolveFromHost($request->getHost(), config('tenancy.base_domain'));

                if ($tenant) {
                    $this->tenants->setTenant($tenant);
                }
            }

            if ($tenant) {
                return redirect()->away($this->tenants->tenantUrl($tenant, '/dashboard'));
            }

            $user = Auth::guard($guard)->user();
            $superAdminEmails = collect(config('auth.super_admin_emails', []))
                ->map(fn ($email) => strtolower((string) $email))
                ->filter();
            $isSuperAdmin = $user
                ? $superAdminEmails->contains(strtolower((string) $user->email))
                : false;

            return redirect()->intended(
                $isSuperAdmin ? '/admin' : route('profile.edit')
            );
        }

        return $next($request);
    }
}
