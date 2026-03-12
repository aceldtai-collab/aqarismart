<?php

namespace App\Policies;

use App\Models\Resident;
use App\Models\User;
use App\Policies\Concerns\ChecksTenantRole;

class ResidentPolicy
{
    use ChecksTenantRole;

    public function viewAny(?User $user): bool
    {
        return $this->hasPermission($user, 'view-residents');
    }

    public function view(?User $user, Resident $model): bool
    {
        if (! $this->hasPermission($user, 'view-residents')) return false;
        $agentId = $this->userAgentId($user);
        if ($agentId && $model->agent_id && $model->agent_id !== $agentId) {
            return false;
        }
        return true;
    }

    public function create(?User $user): bool
    {
        return $this->hasPermission($user, 'create-residents');
    }

    public function update(?User $user, Resident $model): bool
    {
        if (! $this->hasPermission($user, 'update-residents')) return false;
        $agentId = $this->userAgentId($user);
        if ($agentId && $model->agent_id && $model->agent_id !== $agentId) {
            return false;
        }
        return true;
    }

    public function delete(?User $user, Resident $model): bool
    {
        if (! $this->hasPermission($user, 'delete-residents')) return false;
        $agentId = $this->userAgentId($user);
        if ($agentId && $model->agent_id && $model->agent_id !== $agentId) {
            return false;
        }
        return true;
    }
}
