<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Services\Tenancy\TenantManager;
use App\Models\Tenant;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request)
    {
        $user = $request->user();
        $tenant = app(TenantManager::class)->tenant();
        $pivotRole = null;
        if ($tenant) {
            $pivotRole = strtolower((string) ($user->tenants()->whereKey($tenant->getKey())->first()?->pivot?->role));
        }

        $isResident = ($pivotRole === 'resident') || (method_exists($user, 'hasRole') && $user->hasRole('resident'));
        if ($isResident || ! $tenant) {
            return $this->publicProfile($request, $tenant);
        }

        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    public function resident(Request $request): View
    {
        return $this->publicProfile($request, app(TenantManager::class)->tenant());
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    protected function publicProfile(Request $request, ?Tenant $tenant = null): View
    {
        $user = $request->user()->loadMissing('tenants.activeSubscription.package');
        $tenant ??= $user->tenants()->with('activeSubscription.package')->first();

        $role = 'marketplace';
        if ($tenant) {
            $role = strtolower((string) ($user->tenants()->whereKey($tenant->getKey())->first()?->pivot?->role ?: 'resident'));
        } elseif (method_exists($user, 'hasRole') && $user->hasRole('resident')) {
            $role = 'resident';
        }

        return view('profile.public', [
            'user' => $user,
            'profileTenant' => $tenant,
            'profileRole' => $role,
        ]);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
