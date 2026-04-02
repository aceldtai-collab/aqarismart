<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $tenantManager = app(\App\Services\Tenancy\TenantManager::class);
        $tenant = $tenantManager->tenant();
        if (! $tenant) {
            // Resolve tenant from current host for routes without explicit middleware
            $tenant = $tenantManager->resolveFromHost($request->getHost(), config('tenancy.base_domain'));
            if ($tenant) {
                $tenantManager->setTenant($tenant);
                if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
                    app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($tenant->getKey());
                }
            }
        }

        // Resolve user's role on this tenant
        $user = $request->user();

        if ($tenant) {
            $pivotRole = strtolower((string) ($user->tenants()->whereKey($tenant->getKey())->first()?->pivot?->role));

            if (! $pivotRole) {
                // User doesn't belong to this tenant
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect('/')->withErrors(['login' => __('You do not have access to this tenant.')]);
            }

            if ($pivotRole === 'resident') {
                return redirect()->intended('/');
            }

            // Staff / owner → dashboard
            return redirect()->intended('/dashboard');
        }

        // No tenant context (central domain) → admin panel
        return redirect()->intended('/admin');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
