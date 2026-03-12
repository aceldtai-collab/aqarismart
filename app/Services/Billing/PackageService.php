<?php

namespace App\Services\Billing;

use App\Models\Addon;
use App\Models\Package;
use App\Models\Tenant;
use App\Models\TenantAddon;
use App\Models\TenantSubscription;

class PackageService
{
    /**
     * Get the effective limit for a feature, combining package entitlement + active add-ons.
     * Returns: PHP_INT_MAX (unlimited), int (hard limit), or 0 (not entitled).
     */
    public function effectiveLimit(Tenant $tenant, string $feature): int
    {
        $sub = $tenant->activeSubscription;
        if (! $sub) {
            return 0;
        }

        $package = $sub->package;
        $ent = $package->entitlementFor($feature);

        if ($ent === false) {
            return 0; // feature not included in package
        }

        if ($ent === true) {
            return PHP_INT_MAX; // boolean feature, always allowed
        }

        // $ent is an int limit — add add-on grants
        $base = $ent ?? PHP_INT_MAX; // null limit_value = unlimited
        if ($base === PHP_INT_MAX) {
            return PHP_INT_MAX;
        }

        $addonExtra = $tenant->activeAddons()
            ->with('addon')
            ->get()
            ->filter(fn (TenantAddon $ta) => $ta->addon->feature === $feature)
            ->sum(fn (TenantAddon $ta) => $ta->grantedUnits());

        return $base + $addonExtra;
    }

    /**
     * Check if a boolean feature is allowed.
     */
    public function allows(Tenant $tenant, string $feature): bool
    {
        return $this->effectiveLimit($tenant, $feature) > 0;
    }

    /**
     * Check if a metered feature still has capacity.
     */
    public function hasCapacity(Tenant $tenant, string $feature, int $currentUsage): bool
    {
        $limit = $this->effectiveLimit($tenant, $feature);
        return $currentUsage < $limit;
    }

    /**
     * Get usage summary for a tenant: feature → [limit, used, remaining].
     */
    public function usageSummary(Tenant $tenant): array
    {
        $sub = $tenant->activeSubscription;
        if (! $sub) {
            return [];
        }

        $entitlements = $sub->package->entitlements;
        $summary = [];

        foreach ($entitlements as $ent) {
            $feature = $ent->feature;
            $limit = $this->effectiveLimit($tenant, $feature);
            $used = $this->currentUsage($tenant, $feature);

            $summary[$feature] = [
                'type' => $ent->type,
                'limit' => $limit === PHP_INT_MAX ? null : $limit,
                'used' => $used,
                'remaining' => $limit === PHP_INT_MAX ? null : max(0, $limit - $used),
                'unlimited' => $limit === PHP_INT_MAX,
            ];
        }

        return $summary;
    }

    /**
     * Resolve current usage count for a metered feature.
     */
    public function currentUsage(Tenant $tenant, string $feature): int
    {
        $tenantId = $tenant->getKey();

        return match ($feature) {
            'users' => $tenant->users()->count(),
            'units' => \App\Models\Unit::where('tenant_id', $tenantId)->count(),
            'properties' => \App\Models\Property::where('tenant_id', $tenantId)->count(),
            'agents' => \App\Models\Agent::where('tenant_id', $tenantId)->count(),
            'residents' => \App\Models\Resident::where('tenant_id', $tenantId)->count(),
            'leases' => \App\Models\Lease::where('tenant_id', $tenantId)->count(),
            default => 0,
        };
    }

    /* ── Subscription lifecycle (admin-driven) ────────── */

    public function subscribe(Tenant $tenant, Package $package, string $cycle = 'monthly', ?int $trialDays = null): TenantSubscription
    {
        // Cancel any existing active subscription
        $tenant->subscriptions()
            ->whereIn('status', ['active', 'trialing'])
            ->update(['status' => 'canceled', 'canceled_at' => now()]);

        $data = [
            'package_id' => $package->id,
            'billing_cycle' => $cycle,
            'status' => $trialDays ? 'trialing' : 'active',
            'starts_at' => now(),
            'trial_ends_at' => $trialDays ? now()->addDays($trialDays) : null,
        ];

        // Keep legacy plan column in sync
        $tenant->update(['plan' => $package->slug]);

        return $tenant->subscriptions()->create($data);
    }

    public function changePlan(Tenant $tenant, Package $newPackage, ?string $cycle = null): ?TenantSubscription
    {
        $current = $tenant->activeSubscription;

        if ($current) {
            $current->update([
                'status' => 'canceled',
                'canceled_at' => now(),
            ]);
        }

        return $this->subscribe($tenant, $newPackage, $cycle ?? ($current->billing_cycle ?? 'monthly'));
    }

    public function cancel(Tenant $tenant): void
    {
        $current = $tenant->activeSubscription;
        if ($current) {
            $current->update([
                'status' => 'canceled',
                'canceled_at' => now(),
            ]);
        }
    }

    /* ── Add-on lifecycle ─────────────────────────────── */

    public function attachAddon(Tenant $tenant, Addon $addon, int $qty = 1, string $cycle = 'monthly'): TenantAddon
    {
        return $tenant->tenantAddons()->create([
            'addon_id' => $addon->id,
            'qty' => $qty,
            'billing_cycle' => $cycle,
            'status' => 'active',
            'starts_at' => now(),
        ]);
    }

    public function removeAddon(TenantAddon $tenantAddon): void
    {
        $tenantAddon->update([
            'status' => 'canceled',
            'canceled_at' => now(),
        ]);
    }
}
