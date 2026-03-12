<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Resident;
use App\Services\Tenancy\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ResidentAuthController extends Controller
{
    public function __construct(protected TenantManager $tenants) {}

    public function register(Request $request): RedirectResponse
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);

        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'country_code' => ['required','string','max:8'],
            'phone' => ['required','string','max:32'],
            'email' => ['nullable','string','email','max:255','unique:users,email'],
            'password' => ['required','string','min:8','confirmed'],
        ]);

        $countryCode = $this->normalizeCountryCode($data['country_code']);
        $sanitizedPhone = $this->normalizePhone($data['phone']);

        if ($sanitizedPhone === '') {
            throw ValidationException::withMessages([
                'phone' => __('Please enter a valid phone number.'),
            ]);
        }

        $fullPhone = $countryCode.$sanitizedPhone;
        $email = isset($data['email']) && $data['email'] !== '' ? Str::lower($data['email']) : null;

        if (User::where('phone', $fullPhone)->exists()) {
            throw ValidationException::withMessages([
                'phone' => __('This phone number is already registered.'),
            ]);
        }

        $user = DB::transaction(function () use ($data, $tenant, $email, $countryCode, $fullPhone, $sanitizedPhone) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $email,
                'phone' => $fullPhone,
                'phone_country_code' => $countryCode,
                'password' => Hash::make($data['password']),
            ]);

            // Assign resident role if available
            if (method_exists($user, 'assignRole')) {
                try { $user->assignRole('resident'); } catch (\Throwable $e) { /* role may not exist yet */ }
            }

            // Attach to current tenant with resident role
            $user->tenants()->syncWithoutDetaching([$tenant->id => ['role' => 'resident']]);
            $user->tenants()->updateExistingPivot($tenant->id, ['role' => 'resident']);

            // Ensure resident profile exists for this tenant
            $parts = preg_split('/\s+/u', trim($data['name']), 2, PREG_SPLIT_NO_EMPTY);
            $firstName = $parts[0] ?? $data['name'];
            $lastName = $parts[1] ?? '';

            $resident = Resident::firstOrNew([
                'tenant_id' => $tenant->id,
                'phone' => $fullPhone,
            ]);

            $resident->first_name = $firstName;
            $resident->last_name = $lastName;
            $resident->phone = $fullPhone;
            $resident->phone_country_code = $countryCode;
            $resident->email = $email;
            $resident->tenant_id = $tenant->id;
            $resident->save();

            return $user;
        });

        Auth::login($user);

        return redirect()->route('resident.profile')->with('status', 'Welcome aboard! Update your profile to get started.');
    }

    protected function normalizeCountryCode(string $code): string
    {
        $trimmed = trim($code);
        if (! Str::startsWith($trimmed, '+')) {
            $trimmed = '+'.ltrim($trimmed, '+');
        }

        return $trimmed;
    }

    protected function normalizePhone(string $value): string
    {
        return preg_replace('/\D+/', '', $value) ?? '';
    }
}
