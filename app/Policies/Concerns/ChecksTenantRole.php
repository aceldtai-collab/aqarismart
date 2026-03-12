<?php

namespace App\Policies\Concerns;

use App\Models\Property;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use App\Services\Tenancy\TenantManager;

trait ChecksTenantRole
{
    protected function currentTenant(): ?Tenant
    {
        return app(TenantManager::class)->tenant();
    }

    protected function userRole(?User $user): ?string
    {
        if (! $user) return null;
        $tenant = $this->currentTenant();
        if (! $tenant) return null;

        // Prefer Spatie roles if installed
        if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($tenant->getKey());

            // Return the first Spatie role scoped to this tenant (any role, not just system ones)
            if (method_exists($user, 'roles')) {
                $spatieRole = $user->roles()
                    ->where('roles.tenant_id', $tenant->getKey())
                    ->value('name');
                if ($spatieRole) {
                    return $spatieRole;
                }
            }
        }

        // Fallback to pivot role
        $relation = $user->tenants()->whereKey($tenant->getKey())->first();
        return $relation?->pivot?->role;
    }

    protected function isSuperAdmin(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        $emails = config('auth.super_admin_emails', []);

        if ($emails === []) {
            return false;
        }

        return in_array(strtolower((string) $user->email), $emails, true);
    }

    protected function isMember(?User $user): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        return (bool) $this->userRole($user);
    }

    protected function isAdmin(?User $user): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        return in_array($this->userRole($user), ['owner', 'admin'], true);
    }

    /**
     * Check if the user has a specific Spatie permission within the current tenant.
     * Owner and admin roles implicitly have ALL permissions (bypass).
     * For other roles (member, custom), the permission must be explicitly assigned.
     */
    protected function hasPermission(?User $user, string $permission): bool
    {
        if (! $user) {
            return false;
        }

        // Super admin bypass
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        $tenant = $this->currentTenant();
        if (! $tenant) {
            return false;
        }

        // Ensure Spatie team context is set
        if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($tenant->getKey());
        }

        // Owner and admin always have all permissions
        $role = $this->userRole($user);
        if (in_array($role, ['owner', 'admin'], true)) {
            return true;
        }

        // Must at least be a tenant member (any role)
        if (! $role) {
            return false;
        }

        // Check Spatie permission for custom roles
        if (method_exists($user, 'hasPermissionTo')) {
            try {
                return $user->hasPermissionTo($permission);
            } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
                return false;
            }
        }

        return false;
    }

    protected function userAgentId(?User $user): ?int
    {
        /** @var ?int $id */
        $id = $user?->agent_id ?? null;
        return $id ? (int) $id : null;
    }

    protected function propertyHasAgent(Property $property, int $agentId): bool
    {
        if ($property->agent_id === $agentId) {
            return true;
        }

        if ($property->relationLoaded('agents')) {
            return $property->agents->contains('id', $agentId);
        }

        return $property->agents()->where('agents.id', $agentId)->exists();
    }

    protected function unitHasAgent(Unit $unit, int $agentId): bool
    {
        if ($unit->agent_id === $agentId) {
            return true;
        }

        if ($unit->relationLoaded('agents') && $unit->agents->contains('id', $agentId)) {
            return true;
        }

        if ($unit->agents()->where('agents.id', $agentId)->exists()) {
            return true;
        }

        $property = $unit->relationLoaded('property')
            ? $unit->getRelation('property')
            : $unit->property;

        return $property ? $this->propertyHasAgent($property, $agentId) : false;
    }
}
