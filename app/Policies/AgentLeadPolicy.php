<?php

namespace App\Policies;

use App\Models\AgentLead;
use App\Models\User;
use App\Policies\Concerns\ChecksTenantRole;

class AgentLeadPolicy
{
    use ChecksTenantRole;

    public function viewAny(?User $user): bool
    {
        return $this->hasPermission($user, 'view-leads');
    }

    public function view(?User $user, AgentLead $lead): bool
    {
        if (! $this->hasPermission($user, 'view-leads')) return false;
        $agentId = $this->userAgentId($user);
        if ($agentId && $lead->agent_id !== $agentId) {
            return false;
        }
        return true;
    }

    public function create(?User $user): bool
    {
        return $this->hasPermission($user, 'create-leads');
    }

    public function update(?User $user, AgentLead $lead): bool
    {
        if (! $this->hasPermission($user, 'update-leads')) return false;
        $agentId = $this->userAgentId($user);
        if ($agentId && $lead->agent_id !== $agentId) {
            return false;
        }
        return true;
    }

    public function delete(?User $user, AgentLead $lead): bool
    {
        if (! $this->hasPermission($user, 'delete-leads')) return false;
        $agentId = $this->userAgentId($user);
        if ($agentId && $lead->agent_id !== $agentId) {
            return false;
        }
        return true;
    }
}
