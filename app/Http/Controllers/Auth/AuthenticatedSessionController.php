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

        // If the login came from the public popup, restrict it to resident accounts
        if ($request->input('_form') === 'login') {
            $user = $request->user();
            $pivotRole = null;
            if ($tenant) {
                $pivotRole = strtolower((string) ($user->tenants()->whereKey($tenant->getKey())->first()?->pivot?->role));
            }
            $hasSpatie = method_exists($user, 'hasAnyRole');
            $isResident = ($pivotRole === 'resident') || ($hasSpatie && $user->hasAnyRole(['resident']));

            if (! $isResident) {
                // Log out and route to team login page with a friendly banner
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->with('status', __('Please sign in via Team Login.'));
            }

            // Resident success: always send to tenant public home
            return redirect()->intended('/');
        }

        // Standard login flow
        if (! $tenant) {
            return redirect()->intended('/admin');
        }
        return redirect()->intended('/dashboard');
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
