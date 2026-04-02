<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\Tenant;
use App\Models\TenantSubscription;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class TestTenantSeeder extends Seeder
{
    public function run(): void
    {
        // Create user
        $user = User::firstOrCreate(
            ['email' => 'test@google.com'],
            [
                'name' => 'Test Test',
                'password' => Hash::make('123123123'),
            ]
        );

        // Create tenant
        $tenant = Tenant::firstOrCreate(
            ['slug' => 'testrealstate'],
            [
                'name' => 'Test Real State',
                'plan' => 'starter',
                'settings' => ['timezone' => 'Asia/Amman'],
                'trial_ends_at' => now()->addDays(14),
            ]
        );

        // Attach user as owner
        $tenant->users()->syncWithoutDetaching([$user->id => ['role' => 'owner']]);

        // Sync permissions and assign role
        Artisan::call('permissions:sync', ['--tenant' => $tenant->id]);
        if (class_exists(PermissionRegistrar::class)) {
            app(PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);
            $user->assignRole('owner');
        }

        // Create trial subscription
        $starterPackage = Package::where('slug', 'starter')->first();
        if ($starterPackage) {
            TenantSubscription::firstOrCreate(
                ['tenant_id' => $tenant->id],
                [
                    'package_id' => $starterPackage->id,
                    'billing_cycle' => 'monthly',
                    'status' => 'trialing',
                    'starts_at' => now(),
                    'trial_ends_at' => now()->addDays(14),
                ]
            );
        }

        $this->command->info("Created tenant '{$tenant->name}' (slug: {$tenant->slug}) with user '{$user->email}' as owner.");
    }
}
