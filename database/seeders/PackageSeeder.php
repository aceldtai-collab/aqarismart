<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'description' => 'For independent agencies starting with a focused portfolio.',
                'price_monthly' => 2900,
                'price_yearly' => 29000,
                'sort_order' => 1,
                'is_active' => true,
                'is_default' => true,
                'entitlements' => [
                    'users' => ['type' => 'limit', 'limit' => 5],
                    'units' => ['type' => 'limit', 'limit' => 30],
                    'properties' => ['type' => 'limit', 'limit' => 10],
                    'agents' => ['type' => 'limit', 'limit' => 3],
                    'residents' => ['type' => 'limit', 'limit' => 50],
                    'leases' => ['type' => 'limit', 'limit' => 20],
                    'contacts' => ['type' => 'boolean'],
                    'files' => ['type' => 'boolean'],
                ],
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'description' => 'For growing agencies that need tenant operations and public listings.',
                'price_monthly' => 7900,
                'price_yearly' => 79000,
                'sort_order' => 2,
                'is_active' => true,
                'is_default' => false,
                'entitlements' => [
                    'users' => ['type' => 'limit', 'limit' => 15],
                    'units' => ['type' => 'limit', 'limit' => 120],
                    'properties' => ['type' => 'limit', 'limit' => 40],
                    'agents' => ['type' => 'limit', 'limit' => 10],
                    'residents' => ['type' => 'limit', 'limit' => 200],
                    'leases' => ['type' => 'limit', 'limit' => 100],
                    'contacts' => ['type' => 'boolean'],
                    'files' => ['type' => 'boolean'],
                    'maintenance' => ['type' => 'boolean'],
                    'custom_attributes' => ['type' => 'boolean'],
                ],
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'description' => 'For multi-agent agencies with heavier inventory and reporting needs.',
                'price_monthly' => 14900,
                'price_yearly' => 149000,
                'sort_order' => 3,
                'is_active' => true,
                'is_default' => false,
                'entitlements' => [
                    'users' => ['type' => 'limit', 'limit' => 40],
                    'units' => ['type' => 'limit', 'limit' => 350],
                    'properties' => ['type' => 'limit', 'limit' => 120],
                    'agents' => ['type' => 'limit', 'limit' => 25],
                    'residents' => ['type' => 'limit', 'limit' => 500],
                    'leases' => ['type' => 'limit', 'limit' => 300],
                    'contacts' => ['type' => 'boolean'],
                    'files' => ['type' => 'boolean'],
                    'maintenance' => ['type' => 'boolean'],
                    'custom_attributes' => ['type' => 'boolean'],
                ],
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'For enterprise-scale portfolios with unlimited operational growth.',
                'price_monthly' => 29900,
                'price_yearly' => 299000,
                'sort_order' => 4,
                'is_active' => true,
                'is_default' => false,
                'entitlements' => [
                    'users' => ['type' => 'limit', 'limit' => null],
                    'units' => ['type' => 'limit', 'limit' => null],
                    'properties' => ['type' => 'limit', 'limit' => null],
                    'agents' => ['type' => 'limit', 'limit' => null],
                    'residents' => ['type' => 'limit', 'limit' => null],
                    'leases' => ['type' => 'limit', 'limit' => null],
                    'contacts' => ['type' => 'boolean'],
                    'files' => ['type' => 'boolean'],
                    'maintenance' => ['type' => 'boolean'],
                    'custom_attributes' => ['type' => 'boolean'],
                ],
            ],
        ];

        foreach ($packages as $index => $payload) {
            $entitlements = $payload['entitlements'];
            unset($payload['entitlements']);

            if ($payload['is_default']) {
                Package::where('is_default', true)
                    ->where('slug', '!=', $payload['slug'])
                    ->update(['is_default' => false]);
            }

            $package = Package::updateOrCreate(
                ['slug' => $payload['slug']],
                $payload
            );

            $package->entitlements()->delete();

            foreach ($entitlements as $feature => $definition) {
                $package->entitlements()->create([
                    'feature' => $feature,
                    'type' => $definition['type'],
                    'limit_value' => $definition['type'] === 'limit'
                        ? $definition['limit']
                        : null,
                ]);
            }
        }
    }
}
