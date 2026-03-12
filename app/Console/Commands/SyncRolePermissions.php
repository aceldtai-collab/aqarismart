<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SyncRolePermissions extends Command
{
    protected $signature = 'permissions:sync {--tenant= : Sync only a specific tenant by ID}';

    protected $description = 'Sync default permissions to system roles (owner, admin, member) for all tenants';

    /**
     * All 63 permissions in the system, grouped by module.
     */
    protected array $allPermissions = [
        // Properties & Units
        'view-properties', 'create-properties', 'update-properties', 'delete-properties', 'export-properties',
        'view-units', 'create-units', 'update-units', 'delete-units', 'export-units',
        // Residents
        'view-residents', 'create-residents', 'update-residents', 'delete-residents', 'export-residents',
        // Leases
        'view-leases', 'create-leases', 'update-leases', 'delete-leases', 'export-leases',
        // Maintenance
        'view-maintenance', 'create-maintenance', 'update-maintenance', 'delete-maintenance', 'assign-maintenance',
        // Agents
        'view-agents', 'create-agents', 'update-agents', 'delete-agents',
        // Contacts
        'view-contacts', 'create-contacts', 'update-contacts', 'delete-contacts', 'import-contacts', 'export-contacts',
        // Leads
        'view-leads', 'create-leads', 'update-leads', 'delete-leads', 'assign-leads',
        // Viewings
        'view-viewings', 'create-viewings', 'update-viewings', 'delete-viewings', 'schedule-viewings',
        // Commissions
        'view-commissions', 'create-commissions', 'update-commissions', 'delete-commissions', 'approve-commissions',
        // Reports
        'view-dashboard',
        'view-reports', 'view-financial-reports', 'view-occupancy-reports', 'view-pipeline-reports', 'export-reports',
        // Billing
        'view-billing', 'manage-billing', 'can-make-payment', 'can-refund-payment', 'manage-subscriptions',
        // Members
        'view-members', 'invite-members', 'update-member-roles', 'remove-members',
        // Settings & System
        'view-settings', 'update-settings', 'manage-categories', 'manage-attributes',
        'export-data', 'import-data', 'view-audit-logs',
    ];

    /**
     * Member role gets view-only + basic create for their own work.
     */
    protected array $memberPermissions = [
        'view-properties', 'view-units',
        'view-residents',
        'view-leases',
        'view-maintenance', 'create-maintenance',
        'view-contacts', 'create-contacts', 'update-contacts',
        'view-leads', 'create-leads', 'update-leads',
        'view-viewings', 'create-viewings', 'update-viewings',
        'view-commissions',
        'view-dashboard',
        'view-reports',
        'view-members',
    ];

    public function handle(): int
    {
        // Ensure all permissions exist in the DB
        $this->ensurePermissionsExist();

        $tenantId = $this->option('tenant');
        $query = Tenant::query();
        if ($tenantId) {
            $query->where('id', $tenantId);
        }

        $tenants = $query->get();
        $bar = $this->output->createProgressBar($tenants->count());
        $bar->start();

        foreach ($tenants as $tenant) {
            $this->syncForTenant($tenant);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Synced permissions for {$tenants->count()} tenant(s).");

        // Clear Spatie cache
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return self::SUCCESS;
    }

    protected function ensurePermissionsExist(): void
    {
        $existing = Permission::pluck('name')->toArray();
        $missing = array_diff($this->allPermissions, $existing);

        foreach ($missing as $name) {
            Permission::create(['name' => $name, 'guard_name' => 'web']);
        }

        if (count($missing) > 0) {
            $this->info('Created ' . count($missing) . ' missing permission(s).');
        }
    }

    protected function syncForTenant(Tenant $tenant): void
    {
        app(PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);

        // Owner gets ALL permissions
        $ownerRole = Role::firstOrCreate(
            ['name' => 'owner', 'tenant_id' => $tenant->id, 'guard_name' => 'web']
        );
        $ownerRole->syncPermissions($this->allPermissions);

        // Admin gets ALL permissions (same as owner for now — role-level restrictions are via route middleware)
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin', 'tenant_id' => $tenant->id, 'guard_name' => 'web']
        );
        $adminRole->syncPermissions($this->allPermissions);

        // Member gets view-only + basic create
        $memberRole = Role::firstOrCreate(
            ['name' => 'member', 'tenant_id' => $tenant->id, 'guard_name' => 'web']
        );
        $memberRole->syncPermissions($this->memberPermissions);
    }
}
