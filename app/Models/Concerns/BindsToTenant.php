<?php

namespace App\Models\Concerns;

use App\Services\Tenancy\TenantManager;

trait BindsToTenant
{
    /**
     * Ensure route-bound models always belong to the current tenant.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $tenant = app(TenantManager::class)->tenant();

        $query = static::query();
        $key = $field ?: $this->getRouteKeyName();
        $query->where($key, $value);

        // Extra defense: add tenant_id condition when applicable
        if ($tenant && \Schema::hasColumn($this->getTable(), 'tenant_id')) {
            $query->where($this->getTable().'.tenant_id', $tenant->getKey());
        }

        return $query->first();
    }
}

