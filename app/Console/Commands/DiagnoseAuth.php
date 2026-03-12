<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\User;
use App\Services\Tenancy\TenantManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Gate;

class DiagnoseAuth extends Command
{
    protected $signature = 'diagnose:auth {user_id=0} {tenant_slug=reva}';
    protected $description = 'Trace the full authorization chain for a user+tenant';

    public function handle(): int
    {
        $tenant = Tenant::where('slug', $this->argument('tenant_slug'))->first();
        if (!$tenant) {
            $this->error('Tenant not found.');
            return 1;
        }

        $userId = (int) $this->argument('user_id');
        if ($userId === 0) {
            $this->info("=== TENANT USERS for {$tenant->slug} ===");
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);
            foreach ($tenant->users()->get() as $u) {
                $spatieRole = $u->roles()->where('roles.tenant_id', $tenant->id)->value('name') ?: '(none)';
                $this->line("  #{$u->id} {$u->email} pivot={$u->pivot->role} spatie={$spatieRole}");
            }
            $this->line("\nRun: php artisan diagnose:auth {user_id} {$tenant->slug}");
            return 0;
        }

        $user = User::find($userId);
        if (!$user) {
            $this->error('User not found.');
            return 1;
        }

        $this->info("=== AUTH DIAGNOSTIC ===");
        $this->info("User: #{$user->id} {$user->email}");
        $this->info("Tenant: #{$tenant->id} {$tenant->slug} (plan: {$tenant->plan})");

        // 1. Pivot role
        $pivotRole = $user->tenants()->whereKey($tenant->id)->first()?->pivot?->role;
        $this->line("\n--- 1. PIVOT ROLE ---");
        $this->line("Pivot role: " . ($pivotRole ?: '(none)'));

        // 2. Spatie team context
        $this->line("\n--- 2. SPATIE ROLES ---");
        if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);
            $spatieRoles = $user->roles()->where('roles.tenant_id', $tenant->id)->pluck('name');
            $this->line("Spatie roles: " . ($spatieRoles->isEmpty() ? '(none)' : $spatieRoles->implode(', ')));

            // 3. Permissions via role
            $this->line("\n--- 3. SPATIE PERMISSIONS ---");
            $perms = $user->getAllPermissions()->pluck('name');
            $this->line("Total permissions: " . $perms->count());
            if ($perms->count() <= 20) {
                foreach ($perms as $p) $this->line("  - $p");
            } else {
                foreach ($perms->take(10) as $p) $this->line("  - $p");
                $this->line("  ... and " . ($perms->count() - 10) . " more");
            }

            // 4. Key permission checks
            $this->line("\n--- 4. hasPermissionTo() CHECKS ---");
            $keyPerms = ['view-dashboard', 'view-reports', 'view-agents', 'view-properties', 'view-members'];
            foreach ($keyPerms as $perm) {
                try {
                    $has = $user->hasPermissionTo($perm) ? 'YES' : 'NO';
                } catch (\Exception $e) {
                    $has = 'ERROR: ' . $e->getMessage();
                }
                $this->line("  hasPermissionTo('$perm'): $has");
            }
        } else {
            $this->warn("Spatie Permission not installed");
        }

        // 5. EnsureStaff logic
        $this->line("\n--- 5. STAFF CHECK ---");
        $hasSpatieRole = method_exists($user, 'roles')
            ? $user->roles()->where('roles.tenant_id', $tenant->id)->exists()
            : false;
        $isResident = strtolower((string) $pivotRole) === 'resident';
        $isStaff = $hasSpatieRole || ($pivotRole !== '' && $pivotRole !== null && strtolower((string) $pivotRole) !== 'resident');
        $this->line("Has Spatie role for tenant: " . ($hasSpatieRole ? 'YES' : 'NO'));
        $this->line("Is resident: " . ($isResident ? 'YES' : 'NO'));
        $this->line("Would pass staff check: " . ($isStaff ? 'YES' : 'NO'));

        // 6. Gate checks (simulate)
        $this->line("\n--- 6. GATE CHECKS ---");
        app(TenantManager::class)->setTenant($tenant);
        auth()->login($user);
        $gates = ['view-dashboard', 'view-reports', 'view-members'];
        foreach ($gates as $gate) {
            try {
                $allowed = Gate::allows($gate) ? 'ALLOWED' : 'DENIED';
            } catch (\Exception $e) {
                $allowed = 'ERROR: ' . $e->getMessage();
            }
            $this->line("  Gate::allows('$gate'): $allowed");
        }

        // 7. Policy checks
        $this->line("\n--- 7. POLICY CHECKS ---");
        $policyChecks = [
            ['viewAny', \App\Models\Agent::class, 'Agent viewAny'],
            ['viewAny', \App\Models\Property::class, 'Property viewAny'],
            ['viewAny', \App\Models\Unit::class, 'Unit viewAny'],
        ];
        foreach ($policyChecks as [$ability, $model, $label]) {
            try {
                $allowed = Gate::allows($ability, $model) ? 'ALLOWED' : 'DENIED';
            } catch (\Exception $e) {
                $allowed = 'ERROR: ' . $e->getMessage();
            }
            $this->line("  $label: $allowed");
        }

        // 8. Feature gate
        $this->line("\n--- 8. FEATURE GATE ---");
        $features = ['agents', 'contacts', 'properties', 'leases', 'maintenance', 'custom_attributes', 'files'];
        foreach ($features as $f) {
            $allowed = $tenant->canUse($f) ? 'YES' : 'NO';
            $this->line("  canUse('$f'): $allowed");
        }

        // 9. Subscription info
        $this->line("\n--- 9. SUBSCRIPTION ---");
        $sub = $tenant->activeSubscription;
        if ($sub) {
            $this->line("Active subscription: #{$sub->id} package_id={$sub->package_id} status={$sub->status}");
        } else {
            $this->line("Active subscription: (none)");
        }
        $this->line("Trial ends at: " . ($tenant->trial_ends_at ?: '(none)'));

        // 10. Gate::before behavior
        $this->line("\n--- 10. SUPER ADMIN CHECK ---");
        $emails = config('auth.super_admin_emails', []);
        $this->line("Configured super admin emails: " . (empty($emails) ? '(empty list)' : implode(', ', $emails)));
        $isSuperAdmin = !empty($emails) && in_array(strtolower($user->email), $emails, true);
        $this->line("User is super admin: " . ($isSuperAdmin ? 'YES' : 'NO'));

        $this->info("\n=== DIAGNOSTIC COMPLETE ===");
        return 0;
    }
}
