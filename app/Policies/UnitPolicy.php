<?php

namespace App\Policies;

use App\Models\Unit;
use App\Models\User;
use App\Policies\Concerns\ChecksTenantRole;

class UnitPolicy
{
    use ChecksTenantRole;

    public function viewAny(?User $user): bool
    {
        return $this->hasPermission($user, 'view-units');
    }

    public function view(?User $user, Unit $model): bool
    {
        if (! $this->hasPermission($user, 'view-units')) return false;
        $agentId = $this->userAgentId($user);
        if ($agentId && ! $this->unitHasAgent($model, $agentId)) {
            return false;
        }
        return true;
    }

    public function create(?User $user): bool
    {
        return $this->hasPermission($user, 'create-units');
    }

    public function update(?User $user, Unit $model): bool
    {
        if (! $this->hasPermission($user, 'update-units')) return false;
        $agentId = $this->userAgentId($user);
        if ($agentId && ! $this->unitHasAgent($model, $agentId)) {
            return false;
        }
        return true;
    }

    public function delete(?User $user, Unit $model): bool
    {
        if (! $this->hasPermission($user, 'delete-units')) return false;
        $agentId = $this->userAgentId($user);
        if ($agentId && ! $this->unitHasAgent($model, $agentId)) {
            return false;
        }
        return true;
    }
}
