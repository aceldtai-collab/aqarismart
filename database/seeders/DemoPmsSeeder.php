<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\AgentCommission;
use App\Models\AgentLead;
use App\Models\Lease;
use App\Models\MaintenanceRequest;
use App\Models\Property;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\PropertyViewing;
use App\Models\Resident;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Services\Reports\TenantDailySnapshotService;
use Illuminate\Support\Str;

class DemoPmsSeeder extends Seeder
{
    public function run(): void
    {
        // Users
        $owner = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $admin = User::firstOrCreate(
            ['email' => 'admin2@example.com'],
            [
                'name' => 'Admin Two',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $member = User::firstOrCreate(
            ['email' => 'member@example.com'],
            [
                'name' => 'Member User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        // Tenants (workspaces)
        $tenants = [
            ['name' => 'Acme Inc', 'slug' => 'acme', 'plan' => 'pro'],
            ['name' => 'Globex LLC', 'slug' => 'globex', 'plan' => 'starter'],
            ['name' => 'Initech', 'slug' => 'initech', 'plan' => 'pro'],
            ['name' => 'Umbrella Corp', 'slug' => 'umbrella', 'plan' => 'business'],
            ['name' => 'Wayne Enterprises', 'slug' => 'wayne', 'plan' => 'enterprise'],
        ];

        $snapshotService = app(TenantDailySnapshotService::class);

        foreach ($tenants as $t) {
            $tenant = Tenant::firstOrCreate(
                ['slug' => $t['slug']],
                [
                    'name' => $t['name'],
                    'plan' => $t['plan'],
                    'settings' => ['timezone' => 'UTC'],
                    'trial_ends_at' => now()->addDays(14),
                ]
            );

            // Attach users with roles via pivot
            $tenant->users()->syncWithoutDetaching([
                $owner->id => ['role' => 'owner'],
                $admin->id => ['role' => 'admin'],
                $member->id => ['role' => 'member'],
            ]);

            // If Spatie is installed, set team and sync roles to match pivot
            if (class_exists(\Spatie\Permission\Models\Role::class)) {
                // Ensure roles exist
                foreach (['owner','admin','member'] as $r) {
                    \Spatie\Permission\Models\Role::findOrCreate($r, 'web');
                }
                if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
                    app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);
                }
                $assignments = [
                    ['user' => $owner, 'role' => 'owner'],
                    ['user' => $admin, 'role' => 'admin'],
                    ['user' => $member, 'role' => 'member'],
                ];
                foreach ($assignments as $pair) {
                    $user = $pair['user'];
                    $role = $pair['role'];
                    if (method_exists($user, 'syncRoles')) {
                        $user->syncRoles([$role]);
                    }
                }
            }

            // Tenant-scoped CRM: Agents and Contacts
            $agentsSeed = [
                ['name' => $tenant->name.' Holdings', 'email' => 'info+'.Str::random(3).'@'.$tenant->slug.'.test', 'phone' => '+1 555-2001'],
                ['name' => $tenant->name.' Services', 'email' => 'hello+'.Str::random(3).'@'.$tenant->slug.'.test', 'phone' => '+1 555-2002'],
                ['name' => $tenant->name.' Partners', 'email' => 'contact+'.Str::random(3).'@'.$tenant->slug.'.test', 'phone' => '+1 555-2003'],
            ];

            $agents = collect();
            foreach ($agentsSeed as $c) {
                $payload = $c;
                $payload['name'] = ['en' => $c['name'], 'ar' => $c['name']];
                $payload['license_id'] = 'LIC-'.strtoupper(Str::random(6));
                $payload['commission_rate'] = random_int(5, 15);
                $payload['active'] = true;
                $payload['photo'] = 'https://api.dicebear.com/7.x/initials/svg?seed='.urlencode($c['name']);
                $agent = \App\Models\Agent::updateOrCreate(
                    ['tenant_id' => $tenant->id, 'email' => $c['email']],
                    $payload
                );
                $agents->push($agent);
                // Create a default user for the agent with password "password"
                $agentUser = User::firstOrCreate(
                    ['email' => $c['email']],
                    [
                        'name' => ($agent->name).' Admin',
                        'password' => bcrypt('password'),
                        'email_verified_at' => now(),
                        'agent_id' => $agent->id,
                    ]
                );
                if ($agentUser->agent_id !== $agent->id) {
                    $agentUser->agent_id = $agent->id;
                    $agentUser->save();
                }
                // Attach to tenant as member
                $tenant->users()->syncWithoutDetaching([$agentUser->id => ['role' => 'member']]);
                if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
                    app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);
                    if (method_exists($agentUser, 'syncRoles')) {
                        $agentUser->syncRoles(['member']);
                    }
                }
                for ($i = 1; $i <= random_int(2, 4); $i++) {
                    \App\Models\Contact::firstOrCreate(
                        ['tenant_id' => $tenant->id, 'email' => 'contact'.Str::random(4).'@'.$tenant->slug.'.test'],
                        [
                            'agent_id' => $agent->id,
                            'name' => 'Contact '.$i.' of '.$agent->name,
                            'phone' => '+1 555-'.random_int(1000,9999),
                        ]
                    );
                }
            }

            // Seed a richer PMS dataset per tenant
            $locations = [
                ['city' => 'Springfield', 'state' => 'CA', 'postal' => '90210'],
                ['city' => 'Portland', 'state' => 'OR', 'postal' => '97201'],
                ['city' => 'Austin', 'state' => 'TX', 'postal' => '73301'],
                ['city' => 'Denver', 'state' => 'CO', 'postal' => '80014'],
                ['city' => 'Seattle', 'state' => 'WA', 'postal' => '98101'],
                ['city' => 'Miami', 'state' => 'FL', 'postal' => '33101'],
            ];
            $loc = $locations[array_rand($locations)];

            $propertiesData = [
                [
                    'name' => 'Main Street Apartments', 'address' => '123 Main St', 'city' => $loc['city'], 'state' => $loc['state'], 'postal' => $loc['postal'], 'country' => 'US',
                    'units' => [
                        ['code' => 'A-101', 'beds' => 2, 'baths' => 1.5, 'sqft' => 900, 'market_rent' => 165000 + random_int(-5000, 5000), 'status' => (random_int(0,1)?'vacant':'occupied')],
                        ['code' => 'A-102', 'beds' => 1, 'baths' => 1.0, 'sqft' => 700, 'market_rent' => 145000 + random_int(-5000, 5000), 'status' => (random_int(0,1)?'vacant':'occupied')],
                        ['code' => 'A-103', 'beds' => 3, 'baths' => 2.0, 'sqft' => 1200, 'market_rent' => 210000 + random_int(-10000, 10000), 'status' => (random_int(0,1)?'vacant':'occupied')],
                    ],
                ],
                [
                    'name' => 'Riverside Place', 'address' => '55 River Rd', 'city' => $loc['city'], 'state' => $loc['state'], 'postal' => $loc['postal'], 'country' => 'US',
                    'units' => [
                        ['code' => 'B-201', 'beds' => 2, 'baths' => 2.0, 'sqft' => 950, 'market_rent' => 175000 + random_int(-5000, 5000), 'status' => (random_int(0,1)?'vacant':'occupied')],
                        ['code' => 'B-202', 'beds' => 1, 'baths' => 1.0, 'sqft' => 650, 'market_rent' => 135000 + random_int(-5000, 5000), 'status' => (random_int(0,1)?'vacant':'occupied')],
                        ['code' => 'B-203', 'beds' => 2, 'baths' => 1.0, 'sqft' => 800, 'market_rent' => 155000 + random_int(-5000, 5000), 'status' => (random_int(0,1)?'vacant':'occupied')],
                    ],
                ],
                [
                    'name' => 'Hilltop Residences', 'address' => '789 Hilltop Ave', 'city' => $loc['city'], 'state' => $loc['state'], 'postal' => $loc['postal'], 'country' => 'US',
                    'units' => [
                        ['code' => 'C-301', 'beds' => 3, 'baths' => 2.0, 'sqft' => 1300, 'market_rent' => 230000 + random_int(-10000, 10000), 'status' => (random_int(0,1)?'vacant':'occupied')],
                        ['code' => 'C-302', 'beds' => 2, 'baths' => 1.5, 'sqft' => 980, 'market_rent' => 185000 + random_int(-5000, 5000), 'status' => (random_int(0,1)?'vacant':'occupied')],
                    ],
                ],
            ];

            $allUnits = [];
            $allProperties = [];
            $i = 0;
            // Load categories with subcategories for property/unit assignment
            $allCategories = Category::with('subcategories')->get();

            foreach ($propertiesData as $pd) {
                $attrs = $pd;
                unset($attrs['units']);
                $ownerAgent = $agents[$i % max(1, $agents->count())] ?? null;

                // Pick a category for this property (variety across dataset)
                $propCategory = $allCategories->isNotEmpty() ? $allCategories->random() : null;

                $property = Property::create(array_merge($attrs, [
                    'tenant_id' => $tenant->id,
                    'agent_id' => $ownerAgent?->id,
                    'category_id' => $propCategory?->id,
                ]));
                $allProperties[] = $property;
                foreach ($pd['units'] as $ud) {
                    // Pick a subcategory that belongs to the property category
                    $subId = null;
                    if ($propCategory && $propCategory->subcategories->isNotEmpty()) {
                        $subsBySlug = $propCategory->subcategories->keyBy('slug');
                        if ($propCategory->slug === 'residential') {
                            $subId = ($ud['beds'] ?? 1) <= 1
                                ? ($subsBySlug['studio']->id ?? $propCategory->subcategories->first()->id)
                                : ($subsBySlug['apartment']->id ?? $propCategory->subcategories->first()->id);
                        } elseif ($propCategory->slug === 'commercial_office') {
                            $subId = ($subsBySlug['office']->id ?? $subsBySlug['retail_shop']->id ?? $propCategory->subcategories->first()->id);
                        } elseif ($propCategory->slug === 'industrial_logistics') {
                            $subId = ($subsBySlug['warehouse']->id ?? $subsBySlug['factory']->id ?? $propCategory->subcategories->first()->id);
                        } elseif ($propCategory->slug === 'land') {
                            $subId = ($subsBySlug['residential_land']->id ?? $propCategory->subcategories->first()->id);
                        } elseif ($propCategory->slug === 'hospitality_education') {
                            $subId = ($subsBySlug['hotel']->id ?? $propCategory->subcategories->first()->id);
                        } elseif ($propCategory->slug === 'parking_facilities') {
                            $subId = ($subsBySlug['parking_spot']->id ?? $propCategory->subcategories->first()->id);
                        } else {
                            $subId = $propCategory->subcategories->first()->id;
                        }
                    }
                    $allUnits[] = Unit::create(array_merge($ud, [
                        'tenant_id' => $tenant->id,
                        'property_id' => $property->id,
                        'agent_id' => $ownerAgent?->id,
                        'subcategory_id' => $subId,
                    ]));
                }
                $i++;
            }

            // Pick a leased unit for demo associations
            $leasedUnit = collect($allUnits)->firstWhere('status', 'occupied') ?: $allUnits[0];

            // Residents and a sample active lease
            $r1 = Resident::create([
                'tenant_id' => $tenant->id,
                'agent_id' => $leasedUnit->agent_id,
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe+'.Str::random(4).'@example.com',
                'phone' => '+1 555-'.random_int(1000,9999),
            ]);

            $r2 = Resident::create([
                'tenant_id' => $tenant->id,
                'agent_id' => $leasedUnit->agent_id,
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane.smith+'.Str::random(4).'@example.com',
                'phone' => '+1 555-'.random_int(1000,9999),
            ]);

            $lease = Lease::create([
                'tenant_id' => $tenant->id,
                'property_id' => $leasedUnit->property_id,
                'unit_id' => $leasedUnit->id,
                'start_date' => now()->subMonths(2)->toDateString(),
                'end_date' => null,
                'rent_cents' => $leasedUnit->market_rent,
                'deposit_cents' => 50000,
                'frequency' => 'monthly',
                'status' => 'active',
            ]);
            $lease->residents()->sync([
                $r1->id => ['role' => 'primary'],
                $r2->id => ['role' => 'occupant'],
            ]);

            MaintenanceRequest::create([
                'tenant_id' => $tenant->id,
                'property_id' => $leasedUnit->property_id,
                'unit_id' => $leasedUnit->id,
                'resident_id' => $r1->id,
                'title' => 'Leaky faucet in kitchen',
                'details' => 'Dripping continuously; please replace cartridge.',
                'priority' => 'normal',
                'status' => 'new',
            ]);

            // Agent Leads, Viewings, Commissions
            $leadStatuses = ['new', 'in_progress', 'visited', 'closed', 'lost'];
            $viewingStatuses = ['scheduled', 'completed', 'cancelled'];
            foreach ($agents as $agent) {
                $lead = AgentLead::create([
                    'tenant_id' => $tenant->id,
                    'agent_id' => $agent->id,
                    'name' => $agent->name.' Lead',
                    'email' => 'lead+'.Str::random(4).'@'.$tenant->slug.'.test',
                    'phone' => '+1 555-'.random_int(2000, 9999),
                    'source' => 'Online',
                    'status' => $leadStatuses[array_rand($leadStatuses)],
                    'notes' => 'Demo lead for '.$agent->name,
                ]);

                $propertyForViewing = collect($allProperties)->firstWhere('agent_id', $agent->id) ?? ($allProperties[0] ?? null);
                if ($propertyForViewing) {
                    PropertyViewing::create([
                        'tenant_id' => $tenant->id,
                        'lead_id' => $lead->id,
                        'property_id' => $propertyForViewing->id,
                        'agent_id' => $agent->id,
                        'appointment_at' => now()->addDays(random_int(1, 10)),
                        'status' => $viewingStatuses[array_rand($viewingStatuses)],
                        'notes' => 'Demo viewing for '.$propertyForViewing->name,
                    ]);
                }
            }

            AgentCommission::create([
                'tenant_id' => $tenant->id,
                'agent_id' => $leasedUnit->agent_id ?? ($agents->first()?->id),
                'lease_id' => $lease->id,
                'amount' => round(($lease->rent_cents ?? 0) / 100 * 0.1, 2),
                'rate' => 10.00,
                'status' => ['pending', 'approved', 'paid'][array_rand(['pending', 'approved', 'paid'])],
                'notes' => 'Demo commission for active lease',
            ]);

            $snapshotService->capture($tenant, now());
        }
    }
}
