<?php

namespace App\Policies;

use App\Models\PropertyViewing;
use App\Models\User;
use App\Policies\Concerns\ChecksTenantRole;

class PropertyViewingPolicy
{
    use ChecksTenantRole;

    public function viewAny(?User $user): bool
    {
        return $this->hasPermission($user, 'view-viewings');
    }

    public function view(?User $user, PropertyViewing $viewing): bool
    {
        if (! $this->hasPermission($user, 'view-viewings')) return false;
        $agentId = $this->userAgentId($user);
        if ($agentId && $viewing->agent_id !== $agentId) {
            return false;
        }
        return true;
    }

    public function create(?User $user): bool
    {
        return $this->hasPermission($user, 'create-viewings');
    }

    public function update(?User $user, PropertyViewing $viewing): bool
    {
        if (! $this->hasPermission($user, 'update-viewings')) return false;
        $agentId = $this->userAgentId($user);
        if ($agentId && $viewing->agent_id !== $agentId) {
            return false;
        }
        return true;
    }

    public function delete(?User $user, PropertyViewing $viewing): bool
    {
        if (! $this->hasPermission($user, 'delete-viewings')) return false;
        $agentId = $this->userAgentId($user);
        if ($agentId && $viewing->agent_id !== $agentId) {
            return false;
        }
        return true;
    }
}
