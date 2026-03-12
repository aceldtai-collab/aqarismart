<?php

namespace App\Services\Billing;

use App\Models\Tenant;

class FeatureGate
{
    protected ?Tenant $tenant = null;

    public function forTenant(Tenant $tenant): self
    {
        $clone = clone $this;
        $clone->tenant = $tenant;
        return $clone;
    }

    public function allows(string $feature): bool
    {
        if (! $this->tenant) {
            return false;
        }

        // Trial access (legacy column)
        if ($this->tenant->trial_ends_at && now()->lt($this->tenant->trial_ends_at)) {
            return true;
        }

        // New package system — check via PackageService
        $sub = $this->tenant->activeSubscription;
        if ($sub) {
            return app(PackageService::class)->allows($this->tenant, $feature);
        }

        // If Cashier is installed and there is an active Stripe subscription, allow
        if (method_exists($this->tenant, 'subscribed') && $this->tenant->subscribed()) {
            return true;
        }

        // Fallback to legacy plan-based features (config/features.php)
        $plan = $this->tenant->plan ?: 'starter';
        $plans = config('features.plans', []);
        return ($plans[$plan][$feature] ?? false) === true;
    }

    /**
     * Get the effective limit for a metered feature.
     */
    public function limit(string $feature): int
    {
        if (! $this->tenant) {
            return 0;
        }

        $sub = $this->tenant->activeSubscription;
        if ($sub) {
            return app(PackageService::class)->effectiveLimit($this->tenant, $feature);
        }

        // Legacy fallback
        $plan = $this->tenant->plan ?: 'starter';
        $plans = config('features.plans', []);
        $val = $plans[$plan][$feature] ?? false;

        if ($val === true) {
            return PHP_INT_MAX;
        }
        if (is_int($val)) {
            return $val;
        }
        return 0;
    }
}

