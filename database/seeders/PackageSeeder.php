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
                'description' => 'Basic plan for small property managers.',
                'price_monthly' => 2900,   // $29/mo
                'price_yearly' => 29000,    // $290/yr
                'sort_order' => 1,
                'is_active' => true,
                'is_default' => true,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'description' => 'Professional plan with advanced features.',
                'price_monthly' => 7900,   // $79/mo
                'price_yearly' => 79000,   // $790/yr
                'sort_order' => 2,
                'is_active' => true,
                'is_default' => false,
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'description' => 'Business plan for growing teams.',
                'price_monthly' => 14900,  // $149/mo
                'price_yearly' => 149000,  // $1490/yr
                'sort_order' => 3,
                'is_active' => true,
                'is_default' => false,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'Enterprise plan with unlimited access.',
                'price_monthly' => 29900,  // $299/mo
                'price_yearly' => 299000,  // $2990/yr
                'sort_order' => 4,
                'is_active' => true,
                'is_default' => false,
            ],
        ];

        foreach ($packages as $pkg) {
            Package::updateOrCreate(
                ['slug' => $pkg['slug']],
                $pkg
            );
        }
    }
}
