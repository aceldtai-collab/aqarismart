<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Tenant;
use App\Models\Unit;
use App\Models\Property;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\City;
use App\Models\TenantSubscription;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Creating new tenant...\n";

// Create tenant
$tenant = Tenant::create([
    'name' => 'Premium Properties',
    'slug' => 'premium-properties-' . strtolower(Str::random(4)),
    'plan' => 'pro',
    'trial_ends_at' => now()->addDays(30),
    'settings' => ['timezone' => 'UTC']
]);

echo "Tenant created: {$tenant->id} - {$tenant->slug}\n";

// Create a package if none exists
$package = \App\Models\Package::first();
if (!$package) {
    $package = \App\Models\Package::create([
        'name' => 'Pro Plan',
        'slug' => 'pro-plan',
        'price' => 99.99,
        'billing_cycle' => 'monthly',
        'features' => json_encode(['Unlimited Units', 'Advanced Analytics', 'Priority Support']),
        'is_active' => true,
    ]);
    echo "Package created: {$package->id}\n";
}

// Create active subscription
$tenant->subscriptions()->create([
    'package_id' => $package->id,
    'status' => 'active',
    'billing_cycle' => 'monthly',
    'starts_at' => now(),
    'ends_at' => now()->addDays(30),
]);

echo "Subscription created\n";

// Get a category and city for units
$category = Category::first();
$city = City::first();

if (!$category) {
    echo "No categories found, creating one...\n";
    $category = Category::create([
        'name_en' => 'Residential',
        'name_ar' => 'سكني',
        'is_active' => true,
        'sort_order' => 1
    ]);
}

if (!$city) {
    echo "No cities found, creating one...\n";
    $city = City::create([
        'name_en' => 'Amman',
        'name_ar' => 'عمّان',
        'is_active' => true
    ]);
}

// Create a property for the tenant
$property = Property::create([
    'tenant_id' => $tenant->id,
    'name' => 'Premium Tower',
    'address' => '123 Amman Street',
    'city' => 'Amman',
    'country' => 'JO'
]);

echo "Property created: {$property->id}\n";

// Create 3 apartment units with placeholder images
$placeholderImages = [
    'https://images.unsplash.com/photo-1560448214-623082f3bb5b?auto=format&fit=crop&w=800&q=80',
    'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=800&q=80',
    'https://images.unsplash.com/photo-1570129477492-45c003edd2be?auto=format&fit=crop&w=800&q=80'
];

for ($i = 1; $i <= 3; $i++) {
    $unit = Unit::create([
        'tenant_id' => $tenant->id,
        'property_id' => $property->id,
        'code' => 'APT-' . str_pad($i, 3, '0', STR_PAD_LEFT),
        'title' => [
            'en' => "Luxury Apartment {$i}",
            'ar' => "شقة فاخرة {$i}"
        ],
        'description' => [
            'en' => "Beautiful luxury apartment with modern amenities and stunning views. Perfect for those seeking comfort and style.",
            'ar' => "شقة فاخرة جميلة مع وسائل حديثة وإطلالات رائعة. مثالية لأولئك الذين يبحثون عن الراحة والأناقة."
        ],
        'price' => 150000 + ($i * 25000),
        'market_rent' => 1200 + ($i * 200),
        'currency' => 'JOD',
        'bedrooms' => $i + 1,
        'bathrooms' => $i + 0.5,
        'sqft' => 1200 + ($i * 300),
        'area_m2' => 110 + ($i * 30),
        'status' => 'vacant',
        'listing_type' => $i === 1 ? 'sale' : 'rent',
        'city_id' => $city->id,
        'photos' => [$placeholderImages[$i - 1]],
        'lat' => 31.9539 + ($i * 0.001),
        'lng' => 35.9106 + ($i * 0.001),
    ]);
    
    echo "Unit created: {$unit->id} - {$unit->code}\n";
}

echo "\nSetup complete!\n";
echo "Tenant slug: {$tenant->slug}\n";
echo "Access at: http://{$tenant->slug}.localtest.me:8000 (if configured)\n";
echo "Or check marketplace: http://localhost:8000/marketplace\n";
