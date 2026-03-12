<?php

namespace App\Policies;

use App\Models\Lease;
use App\Models\User;
use App\Policies\Concerns\ChecksTenantRole;

class LeasePolicy
{
    use ChecksTenantRole;

    public function viewAny(?User $user): bool
    {
        return $this->hasPermission($user, 'view-leases');
    }

    public function view(?User $user, Lease $model): bool
    {
        if (! $this->hasPermission($user, 'view-leases')) return false;
        $agentId = $this->userAgentId($user);
        if ($agentId && ! $this->leaseBelongsToAgent($model, $agentId)) {
            return false;
        }
        return true;
    }

    public function create(?User $user): bool
    {
        return $this->hasPermission($user, 'create-leases');
    }

    public function update(?User $user, Lease $model): bool
    {
        if (! $this->hasPermission($user, 'update-leases')) return false;
        $agentId = $this->userAgentId($user);
        if ($agentId && ! $this->leaseBelongsToAgent($model, $agentId)) {
            return false;
        }
        return true;
    }

    public function delete(?User $user, Lease $model): bool
    {
        if (! $this->hasPermission($user, 'delete-leases')) return false;
        $agentId = $this->userAgentId($user);
        if ($agentId && ! $this->leaseBelongsToAgent($model, $agentId)) {
            return false;
        }
        return true;
    }

    protected function leaseBelongsToAgent(Lease $lease, int $agentId): bool
    {
        if ($lease->agent_id && $lease->agent_id === $agentId) {
            return true;
        }

        if ($lease->relationLoaded('property') && $lease->property && $this->propertyHasAgent($lease->property, $agentId)) {
            return true;
        }

        if ($lease->relationLoaded('unit') && $lease->unit && $this->unitHasAgent($lease->unit, $agentId)) {
            return true;
        }

        $property = $lease->property;
        if ($property && $this->propertyHasAgent($property, $agentId)) {
            return true;
        }

        $unit = $lease->unit;
        if ($unit && $this->unitHasAgent($unit, $agentId)) {
            return true;
        }

        return false;
    }
}
