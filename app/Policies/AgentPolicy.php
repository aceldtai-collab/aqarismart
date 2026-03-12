<?php

namespace App\Policies;

use App\Models\Agent;
use App\Models\User;
use App\Policies\Concerns\ChecksTenantRole;

class AgentPolicy
{
    use ChecksTenantRole;

    public function viewAny(?User $user): bool
    {
        return $this->hasPermission($user, 'view-agents');
    }

    public function view(?User $user, Agent $agent): bool
    {
        return $this->hasPermission($user, 'view-agents');
    }

    public function create(?User $user): bool
    {
        return $this->hasPermission($user, 'create-agents');
    }

    public function update(?User $user, Agent $agent): bool
    {
        return $this->hasPermission($user, 'update-agents');
    }

    public function delete(?User $user, Agent $agent): bool
    {
        return $this->hasPermission($user, 'delete-agents');
    }
}
