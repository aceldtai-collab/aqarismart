<?php

namespace App\Policies;

use App\Models\AgentCommission;
use App\Models\User;
use App\Policies\Concerns\ChecksTenantRole;

class AgentCommissionPolicy
{
    use ChecksTenantRole;

    public function viewAny(?User $user): bool
    {
        return $this->hasPermission($user, 'view-commissions');
    }

    public function view(?User $user, AgentCommission $commission): bool
    {
        if (! $this->hasPermission($user, 'view-commissions')) return false;
        $agentId = $this->userAgentId($user);
        if ($agentId && $commission->agent_id !== $agentId) {
            return false;
        }
        return true;
    }

    public function create(?User $user): bool
    {
        return $this->hasPermission($user, 'create-commissions');
    }

    public function update(?User $user, AgentCommission $commission): bool
    {
        if (! $this->hasPermission($user, 'update-commissions')) return false;
        $agentId = $this->userAgentId($user);
        if ($agentId && $commission->agent_id !== $agentId) {
            return false;
        }
        return true;
    }

    public function delete(?User $user, AgentCommission $commission): bool
    {
        if (! $this->hasPermission($user, 'delete-commissions')) return false;
        $agentId = $this->userAgentId($user);
        if ($agentId && $commission->agent_id !== $agentId) {
            return false;
        }
        return true;
    }
}
