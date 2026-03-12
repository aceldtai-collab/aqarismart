<?php

namespace App\Services\Tenancy;

use App\Models\Tenant;
use App\Models\User;

class MemberService
{
    public function attach(User $user, Tenant $tenant, string $role = 'member'): void
    {
        $tenant->users()->syncWithoutDetaching([$user->id => ['role' => $role]]);

        if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);
            if (method_exists($user, 'syncRoles')) {
                $user->syncRoles([$role]);
            }
        }
    }

    public function setRole(User $user, Tenant $tenant, string $role): void
    {
        $tenant->users()->updateExistingPivot($user->id, ['role' => $role]);

        if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);
            if (method_exists($user, 'syncRoles')) {
                $user->syncRoles([$role]);
            }
        }
    }

    public function detach(User $user, Tenant $tenant): void
    {
        $tenant->users()->detach($user->id);

        if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);
            if (method_exists($user, 'syncRoles')) {
                $user->syncRoles([]);
            }
        }
    }
}
