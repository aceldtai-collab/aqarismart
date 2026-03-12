<?php

namespace App\Policies;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TenantPolicy
{
    use HandlesAuthorization;

    protected function hasTenantPermission(User $user, Tenant $tenant, string $permission): bool
    {
        if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);

            // Owner and admin bypass — they have all permissions
            if (method_exists($user, 'hasRole') && ($user->hasRole('owner') || $user->hasRole('admin'))) {
                return true;
            }

            // Check if user has ANY Spatie role for this tenant (custom roles like "محاسب")
            $hasTenantRole = method_exists($user, 'roles')
                && $user->roles()->where('roles.tenant_id', $tenant->id)->exists();

            if ($hasTenantRole && method_exists($user, 'hasPermissionTo')) {
                try {
                    return $user->hasPermissionTo($permission);
                } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
                    return false;
                }
            }
        }

        // Fallback to pivot roles
        $pivotRole = $user->tenants()->whereKey($tenant->id)->first()?->pivot?->role;
        return in_array($pivotRole, ['owner', 'admin'], true);
    }

    public function managePermissions(User $user, Tenant $tenant): bool
    {
        // Check Spatie roles first
        if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);
            if (method_exists($user, 'hasRole') && ($user->hasRole('owner') || $user->hasRole('admin'))) {
                return true;
            }
        }

        // Fallback to pivot role
        $pivotRole = $user->tenants()->whereKey($tenant->id)->first()?->pivot?->role;
        return in_array($pivotRole, ['owner', 'admin'], true);
    }

    public function manageRoles(User $user, Tenant $tenant): bool
    {
        // Check Spatie roles first
        if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);
            if (method_exists($user, 'hasRole') && $user->hasRole('owner')) {
                return true;
            }
        }

        // Fallback to pivot role
        $pivotRole = $user->tenants()->whereKey($tenant->id)->first()?->pivot?->role;
        return $pivotRole === 'owner';
    }

    public function viewDashboard(User $user, Tenant $tenant): bool
    {
        return $this->hasTenantPermission($user, $tenant, 'view-dashboard');
    }

    public function viewReports(User $user, Tenant $tenant): bool
    {
        return $this->hasTenantPermission($user, $tenant, 'view-reports');
    }

    public function viewMembers(User $user, Tenant $tenant): bool
    {
        return $this->hasTenantPermission($user, $tenant, 'view-members');
    }

    public function inviteMembers(User $user, Tenant $tenant): bool
    {
        return $this->hasTenantPermission($user, $tenant, 'invite-members');
    }

    public function updateMemberRoles(User $user, Tenant $tenant): bool
    {
        return $this->hasTenantPermission($user, $tenant, 'update-member-roles');
    }

    public function removeMembers(User $user, Tenant $tenant): bool
    {
        return $this->hasTenantPermission($user, $tenant, 'remove-members');
    }

    public function manageAttributes(User $user, Tenant $tenant): bool
    {
        return $this->hasTenantPermission($user, $tenant, 'manage-attributes');
    }

    public function viewSettings(User $user, Tenant $tenant): bool
    {
        return $this->hasTenantPermission($user, $tenant, 'view-settings');
    }

    public function updateSettings(User $user, Tenant $tenant): bool
    {
        return $this->hasTenantPermission($user, $tenant, 'update-settings');
    }
}
