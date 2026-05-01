<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tenant;
use App\Services\Tenancy\TenantManager;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, TenantManager $tenants): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'agent' => ['nullable', 'string', 'max:255'],
            'subdomain' => ['nullable', 'string', 'max:30', 'alpha_dash', 'unique:tenants,slug'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        // Create a tenant for this user and attach as owner
        $name = $request->input('agent') ?: ($request->input('name').' Agent');
        $slug = $request->input('subdomain') ?: Str::of($name)->slug('-')->limit(20, '');
        if (Tenant::where('slug', $slug)->exists()) {
            $slug = Str::limit($slug.'-'.Str::lower(Str::random(4)), 30, '');
        }

        $tenant = Tenant::create([
            'name' => $name,
            'slug' => (string) $slug,
            'plan' => 'starter',
            'settings' => ['timezone' => config('app.timezone', 'UTC')],
            'trial_ends_at' => now()->addDays(14),
        ]);

        $tenant->users()->syncWithoutDetaching([$user->id => ['role' => 'owner']]);

        // Sync default permissions to system roles for this new tenant
        \Illuminate\Support\Facades\Artisan::call('permissions:sync', ['--tenant' => $tenant->id]);

        // Provision global attribute fields for the new tenant
        \Illuminate\Support\Facades\Artisan::call('attributes:sync-tenants', ['--tenant' => $tenant->id]);

        // Assign Spatie owner role
        if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);
            $user->assignRole('owner');
        }

        Auth::login($user);

        // Redirect to the tenant dashboard on its subdomain
        $url = $tenants->tenantUrl($tenant, '/dashboard');
        return redirect()->away($url);
    }
}
