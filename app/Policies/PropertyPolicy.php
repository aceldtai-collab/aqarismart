<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;
use App\Policies\Concerns\ChecksTenantRole;

class PropertyPolicy
{
    use ChecksTenantRole;

    public function viewAny(?User $user): bool
    {
        return $this->hasPermission($user, 'view-properties');
    }

    public function view(?User $user, Property $model): bool
    {
        if (! $this->hasPermission($user, 'view-properties')) return false;
        $agentId = $this->userAgentId($user);
        if ($agentId && ! $this->propertyHasAgent($model, $agentId)) {
            return false;
        }
        return true;
    }

    public function create(?User $user): bool
    {
        return $this->hasPermission($user, 'create-properties');
    }

    public function update(?User $user, Property $model): bool
    {
        if (! $this->hasPermission($user, 'update-properties')) return false;
        $agentId = $this->userAgentId($user);
        if ($agentId && ! $this->propertyHasAgent($model, $agentId)) {
            return false;
        }
        return true;
    }

    public function delete(?User $user, Property $model): bool
    {
        if (! $this->hasPermission($user, 'delete-properties')) return false;
        $agentId = $this->userAgentId($user);
        if ($agentId && ! $this->propertyHasAgent($model, $agentId)) {
            return false;
        }
        return true;
    }
}
