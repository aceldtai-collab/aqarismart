<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MobileTenantResource;
use App\Http\Resources\MobileUserResource;
use App\Models\Resident;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Tenancy\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\PermissionRegistrar;

class MobileAuthController extends Controller
{
    public function __construct(protected TenantManager $tenants)
    {
    }

    public function registerBusiness(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'agent' => ['nullable', 'string', 'max:255'],
            'subdomain' => ['nullable', 'string', 'max:30', 'alpha_dash', 'unique:tenants,slug'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => Str::lower($data['email']),
            'password' => Hash::make($data['password']),
        ]);

        $tenantName = $data['agent'] ?: ($data['name'] . ' Agent');
        $slug = $data['subdomain'] ?: Str::of($tenantName)->slug('-')->limit(20, '');
        if (Tenant::where('slug', $slug)->exists()) {
            $slug = Str::limit($slug . '-' . Str::lower(Str::random(4)), 30, '');
        }

        $tenant = Tenant::create([
            'name' => $tenantName,
            'slug' => (string) $slug,
            'plan' => 'starter',
            'settings' => ['timezone' => config('app.timezone', 'UTC')],
            'trial_ends_at' => now()->addDays(14),
        ]);

        $tenant->users()->syncWithoutDetaching([$user->id => ['role' => 'owner']]);

        Artisan::call('permissions:sync', ['--tenant' => $tenant->id]);
        Artisan::call('attributes:sync-tenants', ['--tenant' => $tenant->id]);

        if (class_exists(PermissionRegistrar::class)) {
            app(PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);
            $user->assignRole('owner');
        }

        return $this->authenticatedResponse($user->fresh('tenants.activeSubscription.package'), $tenant, 'owner');
    }

    public function registerResident(Request $request): JsonResponse
    {
        $tenant = $this->resolveTenantFromRequest($request, null, false);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'country_code' => ['required', 'string', 'max:8'],
            'phone' => ['required', 'string', 'max:32'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $countryCode = $this->normalizeCountryCode($data['country_code']);
        $sanitizedPhone = $this->normalizePhone($data['phone']);

        if ($sanitizedPhone === '') {
            throw ValidationException::withMessages([
                'phone' => __('Please enter a valid phone number.'),
            ]);
        }

        $fullPhone = $countryCode . $sanitizedPhone;
        $email = isset($data['email']) && $data['email'] !== '' ? Str::lower($data['email']) : null;

        if (User::where('phone', $fullPhone)->exists()) {
            throw ValidationException::withMessages([
                'phone' => __('This phone number is already registered.'),
            ]);
        }

        $user = $this->createResidentAccount($data, $tenant, $countryCode, $fullPhone, $email);

        return $this->authenticatedResponse(
            $user->fresh('tenants.activeSubscription.package'),
            $tenant,
            $tenant ? 'resident' : null
        );
    }

    public function registerResidentWeb(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'country_code' => ['required', 'string', 'max:8'],
            'phone' => ['required', 'string', 'max:32'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $countryCode = $this->normalizeCountryCode($data['country_code']);
        $sanitizedPhone = $this->normalizePhone($data['phone']);

        if ($sanitizedPhone === '') {
            throw ValidationException::withMessages([
                'phone' => __('Please enter a valid phone number.'),
            ]);
        }

        $fullPhone = $countryCode . $sanitizedPhone;
        $email = isset($data['email']) && $data['email'] !== '' ? Str::lower($data['email']) : null;

        if (User::where('phone', $fullPhone)->exists()) {
            throw ValidationException::withMessages([
                'phone' => __('This phone number is already registered.'),
            ]);
        }

        $user = $this->createResidentAccount($data, null, $countryCode, $fullPhone, $email);

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->regenerateToken();

        return response()->json([
            'redirect' => route('profile.edit'),
            'user' => new MobileUserResource($user->fresh('tenants.activeSubscription.package')),
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'login' => ['nullable', 'string', 'required_without:email'],
            'email' => ['nullable', 'string', 'email', 'required_without:login'],
            'login_country_code' => ['nullable', 'string', 'max:8'],
            'password' => ['required', 'string'],
            'tenant_id' => ['nullable', 'integer', 'exists:tenants,id'],
            'tenant_slug' => ['nullable', 'string', 'exists:tenants,slug'],
        ]);

        $identifier = trim((string) ($data['login'] ?? $data['email'] ?? ''));
        $user = filter_var($identifier, FILTER_VALIDATE_EMAIL)
            ? User::where('email', Str::lower($identifier))->first()
            : User::where('phone', $this->resolvePhone($identifier, $data['login_country_code'] ?? null))->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'login' => [trans('auth.failed')],
            ]);
        }

        $tenant = $this->resolveTenantFromRequest($request, null, false);

        if ($tenant && ! $user->tenants()->whereKey($tenant->getKey())->exists()) {
            throw ValidationException::withMessages([
                'tenant_id' => [__('You do not have access to the requested tenant.')],
            ]);
        }

        $pivotRole = $tenant
            ? strtolower((string) ($user->tenants()->whereKey($tenant->getKey())->first()?->pivot?->role))
            : null;

        return $this->authenticatedResponse($user->fresh('tenants.activeSubscription.package'), $tenant, $pivotRole ?: null);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->loadMissing('tenants.activeSubscription.package');
        $tenant = $this->resolveTenantFromRequest($request, null, false);

        if ($tenant) {
            $request->attributes->set('mobile_tenant', $tenant);
        }

        return response()->json([
            'user' => new MobileUserResource($user),
            'current_tenant' => $tenant ? new MobileTenantResource($tenant->loadMissing('activeSubscription.package')) : null,
        ]);
    }

    public function webDashboardLink(Request $request): JsonResponse
    {
        $user = $request->user()->loadMissing('tenants');
        $tenant = $this->resolveTenantFromRequest($request, $user, false);

        if (! $tenant) {
            return response()->json([
                'message' => __('No tenant context available.'),
            ], 404);
        }

        if (! $user->tenants()->whereKey($tenant->getKey())->exists()) {
            return response()->json([
                'message' => __('You do not have access to the requested tenant.'),
            ], 403);
        }

        $nonce = Str::random(64);

        Cache::put(
            $this->webDashboardBridgeCacheKey($nonce),
            [
                'user_id' => $user->getKey(),
                'tenant_id' => $tenant->getKey(),
            ],
            now()->addMinutes(2),
        );

        return response()->json([
            'url' => URL::temporarySignedRoute(
                'mobile.auth.web-dashboard',
                now()->addMinutes(2),
                ['nonce' => $nonce],
            ),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json([
            'message' => __('Logged out successfully.'),
        ]);
    }

    public function openWebDashboard(Request $request, string $nonce): RedirectResponse
    {
        abort_unless($request->hasValidSignature(), 403);

        $payload = Cache::pull($this->webDashboardBridgeCacheKey($nonce));
        abort_unless(is_array($payload), 403);

        $user = User::findOrFail((int) ($payload['user_id'] ?? 0));
        $tenant = Tenant::findOrFail((int) ($payload['tenant_id'] ?? 0));

        abort_unless($user->tenants()->whereKey($tenant->getKey())->exists(), 403);

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->regenerateToken();

        return redirect()->away($this->tenants->tenantUrl($tenant, '/dashboard'));
    }

    protected function authenticatedResponse(User $user, ?Tenant $tenant, ?string $role): JsonResponse
    {
        $token = $user->createToken('mobile-app')->plainTextToken;

        $user->loadMissing('tenants.activeSubscription.package');

        if ($tenant) {
            $this->tenants->setTenant($tenant);
            request()->attributes->set('mobile_tenant', $tenant);
        }

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => new MobileUserResource($user),
            'current_tenant' => $tenant ? new MobileTenantResource($tenant->loadMissing('activeSubscription.package')) : null,
            'tenant_role' => $role,
        ], 201);
    }

    protected function createResidentAccount(array $data, ?Tenant $tenant, string $countryCode, string $fullPhone, ?string $email): User
    {
        return DB::transaction(function () use ($countryCode, $data, $email, $fullPhone, $tenant) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $email,
                'phone' => $fullPhone,
                'phone_country_code' => $countryCode,
                'password' => Hash::make($data['password']),
            ]);

            if (method_exists($user, 'assignRole')) {
                try {
                    $user->assignRole('resident');
                } catch (\Throwable $e) {
                }
            }

            if ($tenant) {
                $user->tenants()->syncWithoutDetaching([$tenant->id => ['role' => 'resident']]);
                $user->tenants()->updateExistingPivot($tenant->id, ['role' => 'resident']);

                $parts = preg_split('/\s+/u', trim($data['name']), 2, PREG_SPLIT_NO_EMPTY);
                Resident::updateOrCreate(
                    ['tenant_id' => $tenant->id, 'phone' => $fullPhone],
                    [
                        'first_name' => $parts[0] ?? $data['name'],
                        'last_name' => $parts[1] ?? '',
                        'phone' => $fullPhone,
                        'phone_country_code' => $countryCode,
                        'email' => $email,
                        'tenant_id' => $tenant->id,
                    ]
                );
            }

            return $user;
        });
    }

    protected function webDashboardBridgeCacheKey(string $nonce): string
    {
        return "mobile:web-dashboard:{$nonce}";
    }

    protected function resolveTenantFromRequest(Request $request, ?User $user = null, bool $fallbackToUserTenant = true): ?Tenant
    {
        $tenantId = $request->input('tenant_id') ?? $request->header('X-Tenant-Id');
        if ($tenantId) {
            return Tenant::find($tenantId);
        }

        $tenantSlug = $request->input('tenant_slug') ?? $request->header('X-Tenant-Slug');
        if (is_string($tenantSlug) && $tenantSlug !== '') {
            return Tenant::where('slug', $tenantSlug)->first();
        }

        return $fallbackToUserTenant ? $user?->tenants()->first() : null;
    }

    protected function resolvePhone(string $value, ?string $countryCode): string
    {
        if (Str::startsWith($value, '+')) {
            return '+' . preg_replace('/\D+/', '', $value);
        }

        if (Str::startsWith($value, '00')) {
            return '+' . preg_replace('/\D+/', '', substr($value, 2));
        }

        return $this->normalizeCountryCode($countryCode ?: config('phone.default', '+962')) . $this->normalizePhone($value);
    }

    protected function normalizeCountryCode(string $code): string
    {
        $trimmed = trim($code);
        return Str::startsWith($trimmed, '+') ? $trimmed : '+' . ltrim($trimmed, '+');
    }

    protected function normalizePhone(string $value): string
    {
        return preg_replace('/\D+/', '', $value) ?? '';
    }
}
