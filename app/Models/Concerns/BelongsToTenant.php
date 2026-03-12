<?php

namespace App\Models\Concerns;

use App\Services\Tenancy\TenantManager;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        // Auto-fill tenant_id on create, if available in context
        static::creating(function ($model) {
            if (! isset($model->tenant_id) || empty($model->tenant_id)) {
                $tenant = app(TenantManager::class)->tenant();
                if ($tenant) {
                    $model->tenant_id = $tenant->getKey();
                }
            }
        });

        // Global scope to the current tenant when one is set
        static::addGlobalScope('tenant', function (Builder $builder) {
            // Skip in console to allow admin operations and seeding
            if (app()->runningInConsole()) {
                return;
            }
            $tenant = app(TenantManager::class)->tenant();
            if ($tenant) {
                $builder->where($builder->getModel()->getTable() . '.tenant_id', $tenant->getKey());
            }
        });
    }

    // Allow explicit tenant scoping (and bypassing the global scope when needed)
    public function scopeForTenant(Builder $query, $tenant): Builder
    {
        $tenantId = is_object($tenant) && method_exists($tenant, 'getKey') ? $tenant->getKey() : (int) $tenant;
        return $query->withoutGlobalScope('tenant')->where($this->getTable() . '.tenant_id', $tenantId);
    }
}
