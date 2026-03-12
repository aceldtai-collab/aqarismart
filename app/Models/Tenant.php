<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Support\Billing\UsesBillable;

class Tenant extends Model
{
    use HasFactory;
    use UsesBillable;

    protected $fillable = [
        'name',
        'slug',
        'plan',
        'settings',
        'trial_ends_at',
        'stripe_id',
        'pm_type',
        'pm_last_four',
    ];

    protected $casts = [
        'settings' => 'array',
        'trial_ends_at' => 'datetime',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tenant_user')
            ->withPivot(['role'])
            ->withTimestamps();
    }

    /* ── Package system ─────────────────────────────── */

    public function subscriptions(): HasMany
    {
        return $this->hasMany(TenantSubscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(TenantSubscription::class)
            ->whereIn('status', ['active', 'trialing'])
            ->latestOfMany();
    }

    public function tenantAddons(): HasMany
    {
        return $this->hasMany(TenantAddon::class);
    }

    public function activeAddons(): HasMany
    {
        return $this->tenantAddons()->where('status', 'active');
    }

    /**
     * Get the current Package model (or null).
     */
    public function currentPackage(): ?Package
    {
        return $this->activeSubscription?->package;
    }

    public function customAttributeFields(): HasMany
    {
        return $this->hasMany(AttributeField::class);
    }

    public function canUse(string $feature): bool
    {
        return app(\App\Services\Billing\FeatureGate::class)->forTenant($this)->allows($feature);
    }
}
