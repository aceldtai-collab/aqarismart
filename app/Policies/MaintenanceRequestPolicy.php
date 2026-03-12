<?php

namespace App\Policies;

use App\Models\MaintenanceRequest;
use App\Models\User;
use App\Policies\Concerns\ChecksTenantRole;

class MaintenanceRequestPolicy
{
    use ChecksTenantRole;

    public function viewAny(?User $user): bool
    {
        return $this->hasPermission($user, 'view-maintenance');
    }

    public function view(?User $user, MaintenanceRequest $model): bool
    {
        if (! $this->hasPermission($user, 'view-maintenance')) return false;
        $agentId = $this->userAgentId($user);
        if ($agentId && ! $this->maintenanceRequestBelongsToAgent($model, $agentId)) {
            return false;
        }
        return true;
    }

    public function create(?User $user): bool
    {
        return $this->hasPermission($user, 'create-maintenance');
    }

    public function update(?User $user, MaintenanceRequest $model): bool
    {
        if (! $this->hasPermission($user, 'update-maintenance')) return false;
        $agentId = $this->userAgentId($user);
        if ($agentId && ! $this->maintenanceRequestBelongsToAgent($model, $agentId)) {
            return false;
        }
        return true;
    }

    public function delete(?User $user, MaintenanceRequest $model): bool
    {
        if (! $this->hasPermission($user, 'delete-maintenance')) return false;
        $agentId = $this->userAgentId($user);
        if ($agentId && ! $this->maintenanceRequestBelongsToAgent($model, $agentId)) {
            return false;
        }
        return true;
    }

    protected function maintenanceRequestBelongsToAgent(MaintenanceRequest $model, int $agentId): bool
    {
        if ($model->relationLoaded('property') && $model->property && $this->propertyHasAgent($model->property, $agentId)) {
            return true;
        }

        if ($model->relationLoaded('unit') && $model->unit && $this->unitHasAgent($model->unit, $agentId)) {
            return true;
        }

        $property = $model->property;
        if ($property && $this->propertyHasAgent($property, $agentId)) {
            return true;
        }

        $unit = $model->unit;
        if ($unit && $this->unitHasAgent($unit, $agentId)) {
            return true;
        }

        return false;
    }
}
