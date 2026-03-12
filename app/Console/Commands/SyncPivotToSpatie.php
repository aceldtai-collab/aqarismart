<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SyncPivotToSpatie extends Command
{
    protected $signature = 'tenants:sync-roles {tenant_slug?}';
    protected $description = 'Sync pivot roles to Spatie roles for all (or one) tenant';

    public function handle(): int
    {
        $slug = $this->argument('tenant_slug');
        $tenants = $slug
            ? Tenant::where('slug', $slug)->get()
            : Tenant::all();

        if ($tenants->isEmpty()) {
            $this->error('No tenants found.');
            return 1;
        }

        foreach ($tenants as $tenant) {
            $this->info("Tenant: {$tenant->slug} (#{$tenant->id})");
            app(PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);

            foreach ($tenant->users()->get() as $user) {
                $pivotRole = $user->pivot->role;
                if (!$pivotRole) {
                    $this->line("  #{$user->id} {$user->email}: no pivot role, skipping");
                    continue;
                }

                // Check if user already has a Spatie role for this tenant
                $existing = $user->roles()->where('roles.tenant_id', $tenant->id)->value('name');
                if ($existing) {
                    $this->line("  #{$user->id} {$user->email}: already has Spatie role '{$existing}', skipping");
                    continue;
                }

                // Create the Spatie role if it doesn't exist
                $role = Role::firstOrCreate([
                    'name' => $pivotRole,
                    'tenant_id' => $tenant->id,
                    'guard_name' => 'web',
                ]);

                $user->assignRole($role);
                $this->line("  #{$user->id} {$user->email}: assigned Spatie role '{$pivotRole}'");
            }
        }

        // Clear permission cache
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->info('Permission cache cleared.');

        return 0;
    }
}
