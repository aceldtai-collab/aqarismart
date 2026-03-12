<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_monthly',
        'price_yearly',
        'stripe_price_monthly',
        'stripe_price_yearly',
        'sort_order',
        'is_active',
        'is_default',
        'metadata',
    ];

    protected $casts = [
        'price_monthly' => 'integer',
        'price_yearly' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'metadata' => 'array',
    ];

    public function entitlements(): HasMany
    {
        return $this->hasMany(PackageEntitlement::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(TenantSubscription::class);
    }

    public function activeSubscriptions(): HasMany
    {
        return $this->subscriptions()->whereIn('status', ['active', 'trialing']);
    }

    /**
     * Get the effective limit for a feature from this package's entitlements.
     * Returns: true (boolean enabled), int (limit), or false (not entitled).
     */
    public function entitlementFor(string $feature): bool|int
    {
        $ent = $this->entitlements->firstWhere('feature', $feature);

        if (! $ent) {
            return false;
        }

        if ($ent->type === 'boolean') {
            return true;
        }

        // type === 'limit' — null limit_value means unlimited
        return $ent->limit_value ?? PHP_INT_MAX;
    }

    public function formattedMonthlyPrice(): string
    {
        return number_format($this->price_monthly / 100, 2);
    }

    public function formattedYearlyPrice(): string
    {
        return number_format($this->price_yearly / 100, 2);
    }
}
