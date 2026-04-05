<?php

namespace Database\Seeders;

use Database\Seeders\Support\IraqSeedVisualFactory;
use App\Models\Agent;
use App\Models\AttributeField;
use App\Models\City;
use App\Models\Country;
use App\Models\Package;
use App\Models\Property;
use App\Models\State;
use App\Models\Subcategory;
use App\Models\Tenant;
use App\Models\TenantSubscription;
use App\Models\Unit;
use App\Models\UnitAttribute;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\PermissionRegistrar;

class IraqProductionSeeder extends Seeder
{
    protected array $subcategories = [];

    protected array $cities = [];

    protected array $states = [];

    protected ?IraqSeedVisualFactory $visualFactory = null;

    public function run(): void
    {
        $this->call([
            PackageSeeder::class,
            CategorySeeder::class,
            LocationsSeeder::class,
            AttributeFieldsSeeder::class,
        ]);

        $iraq = Country::query()->where('iso2', 'IQ')->firstOrFail();

        $this->subcategories = Subcategory::query()
            ->with('category')
            ->whereIn('slug', [
                'apartment',
                'furnished_apartment',
                'duplex',
                'villa',
                'office',
                'showroom',
                'retail_shop',
                'warehouse',
                'guest_house',
                'hotel',
                'residential_land',
                'commercial_land',
            ])
            ->get()
            ->keyBy('slug')
            ->all();

        $this->cities = City::query()
            ->where('country_id', $iraq->id)
            ->get()
            ->keyBy(fn (City $city) => strtolower($city->name_en))
            ->all();

        $this->states = State::query()
            ->where('country_id', $iraq->id)
            ->get()
            ->keyBy('code')
            ->all();

        $packages = Package::query()
            ->whereIn('slug', ['starter', 'pro', 'business', 'enterprise'])
            ->get()
            ->keyBy('slug');

        $this->seedSuperAdmins();

        foreach ($this->tenantBlueprints() as $tenantPayload) {
            $package = $packages->get($tenantPayload['plan']);

            if (! $package) {
                continue;
            }

            $tenantVisual = $this->tenantVisualContext($tenantPayload);
            $this->purgeLegacySeedAssets($tenantPayload['slug']);
            $tenant = $this->upsertTenant($tenantPayload, $tenantVisual);
            $this->upsertSubscription($tenant, $package->id);
            $this->pruneTenantPortfolio($tenant, $tenantPayload);

            $owner = $this->upsertOwner($tenant, $tenantPayload['owner']);
            $this->syncPermissionsForTenant($tenant);
            $this->assignTenantRole($tenant, $owner, 'owner');

            $agents = $this->upsertAgents($tenant, $tenantPayload['agents'], $tenantVisual);
            $attributeMap = $this->seedTenantCustomAttributes($tenant);

            foreach ($tenantPayload['units'] as $unitPayload) {
                $this->upsertPortfolioRecord($tenant, $tenantVisual, $agents, $attributeMap, $unitPayload);
            }
        }
    }

    protected function seedSuperAdmins(): void
    {
        $password = (string) env('SEED_SUPER_ADMIN_PASSWORD', 'Admin@123456');
        $emails = config('auth.super_admin_emails', []);

        if ($emails === []) {
            $emails = ['admin@aqarismart.com'];
        }

        foreach ($emails as $index => $email) {
            User::query()->updateOrCreate(
                ['email' => strtolower((string) $email)],
                [
                    'name' => $index === 0 ? 'Aqari Smart Super Admin' : 'Aqari Smart Admin',
                    'phone_country_code' => '+964',
                    'phone' => '+9647700001' . str_pad((string) $index, 3, '0', STR_PAD_LEFT),
                    'email_verified_at' => now(),
                    'password' => $password,
                ]
            );
        }
    }

    protected function upsertTenant(array $payload, array $tenantVisual): Tenant
    {
        $logoPath = $this->writeSeedVisual(
            "seed/tenants/{$payload['slug']}/logo.svg",
            $this->visualFactory()->tenantLogo($tenantVisual)
        );
        $headerPath = $this->writeSeedVisual(
            "seed/tenants/{$payload['slug']}/header.svg",
            $this->visualFactory()->tenantHeader($tenantVisual)
        );

        return Tenant::query()->updateOrCreate(
            ['slug' => $payload['slug']],
            [
                'name' => $payload['name'],
                'plan' => $payload['plan'],
                'settings' => [
                    'timezone' => 'Asia/Baghdad',
                    'currency' => 'IQD',
                    'country' => 'IQ',
                    'primary_color' => $payload['settings']['primary_color'],
                    'accent_color' => $payload['settings']['accent_color'],
                    'font_color' => '#1f241d',
                    'typography' => 'system',
                    'tagline' => $payload['settings']['tagline'],
                    'contact_email' => $payload['owner']['email'],
                    'contact_phone' => $payload['owner']['phone'],
                    'logo_url' => 'storage/' . $logoPath,
                    'favicon_url' => 'storage/' . $logoPath,
                    'header_bg_url' => 'storage/' . $headerPath,
                    'home_show_types' => true,
                    'home_show_cities' => true,
                    'home_show_latest' => true,
                    'home_show_search' => true,
                    'home_show_map' => true,
                    'footer' => [
                        'quote' => $payload['settings']['footer_quote'],
                        'links' => [
                            ['label' => 'Marketplace', 'href' => '/marketplace'],
                            ['label' => 'Browse Properties', 'href' => '/search'],
                            ['label' => 'Contact', 'href' => '/login'],
                        ],
                        'social' => [
                            ['label' => 'Instagram', 'url' => 'https://instagram.com/aqarismart'],
                            ['label' => 'Facebook', 'url' => 'https://facebook.com/aqarismart'],
                        ],
                        'show_newsletter' => false,
                    ],
                ],
                'trial_ends_at' => null,
            ]
        );
    }

    protected function upsertSubscription(Tenant $tenant, int $packageId): void
    {
        TenantSubscription::query()->updateOrCreate(
            ['tenant_id' => $tenant->id, 'package_id' => $packageId],
            [
                'billing_cycle' => 'yearly',
                'status' => 'active',
                'starts_at' => now()->subDays(30),
                'ends_at' => now()->addYear(),
                'trial_ends_at' => null,
            ]
        );
    }

    protected function upsertOwner(Tenant $tenant, array $payload): User
    {
        $password = (string) env('SEED_TENANT_OWNER_PASSWORD', 'Owner@123456');

        $user = User::query()->updateOrCreate(
            ['email' => $payload['email']],
            [
                'name' => $payload['name_en'],
                'phone_country_code' => '+964',
                'phone' => $payload['phone'],
                'email_verified_at' => now(),
                'password' => $password,
            ]
        );

        $tenant->users()->syncWithoutDetaching([$user->id => ['role' => 'owner']]);

        return $user;
    }

    protected function upsertAgents(Tenant $tenant, array $blueprints, array $tenantVisual): array
    {
        $password = (string) env('SEED_AGENT_PASSWORD', 'Agent@123456');
        $agents = [];

        foreach ($blueprints as $payload) {
            $photoPath = $this->writeSeedVisual(
                "seed/tenants/{$tenant->slug}/agents/{$payload['slug']}.svg",
                $this->visualFactory()->agentPortrait($tenantVisual, $payload)
            );

            $agent = Agent::query()->updateOrCreate(
                ['tenant_id' => $tenant->id, 'email' => $payload['email']],
                [
                    'name' => $payload['name'],
                    'phone' => $payload['phone'],
                    'license_id' => $payload['license_id'],
                    'commission_rate' => $payload['commission_rate'],
                    'active' => true,
                    'photo' => $photoPath,
                ]
            );

            $user = User::query()->updateOrCreate(
                ['email' => $payload['email']],
                [
                    'name' => $payload['name']['en'],
                    'phone_country_code' => '+964',
                    'phone' => $payload['phone'],
                    'agent_id' => $agent->id,
                    'email_verified_at' => now(),
                    'password' => $password,
                ]
            );

            if ($user->agent_id !== $agent->id) {
                $user->forceFill(['agent_id' => $agent->id])->save();
            }

            $tenant->users()->syncWithoutDetaching([$user->id => ['role' => 'member']]);
            $this->assignTenantRole($tenant, $user, 'member');

            $agents[$payload['slug']] = $agent;
        }

        return $agents;
    }

    protected function seedTenantCustomAttributes(Tenant $tenant): array
    {
        $tenantFields = [];
        $subcategories = collect($this->subcategories);
        $globalFields = AttributeField::query()
            ->whereNull('tenant_id')
            ->whereIn('subcategory_id', $subcategories->pluck('id'))
            ->orderBy('sort')
            ->get();

        foreach ($globalFields as $field) {
            $copy = AttributeField::query()->updateOrCreate(
                ['tenant_id' => $tenant->id, 'subcategory_id' => $field->subcategory_id, 'key' => $field->key],
                [
                    'label' => $field->label,
                    'label_translations' => $field->label_translations,
                    'type' => $field->type,
                    'required' => $field->required,
                    'searchable' => $field->searchable,
                    'facetable' => $field->facetable,
                    'promoted' => $field->promoted,
                    'options' => $field->options,
                    'unit' => $field->unit,
                    'min' => $field->min,
                    'max' => $field->max,
                    'group' => $field->group,
                    'sort' => $field->sort,
                ]
            );

            $subcategorySlug = $subcategories->firstWhere('id', $field->subcategory_id)?->slug;
            if ($subcategorySlug) {
                $tenantFields[$subcategorySlug][$copy->key] = $copy;
            }
        }

        return $tenantFields;
    }

    protected function upsertPortfolioRecord(Tenant $tenant, array $tenantVisual, array $agents, array $attributeMap, array $payload): void
    {
        $record = $this->materializeUnitRecord($payload);
        $subcategory = $this->subcategories[$record['subcategory']];
        $city = $this->cities[strtolower($record['city'])];
        $state = $this->states[$record['state']];
        $agent = $agents[$record['agent']];
        $areaM2 = (int) round($record['sqft'] / 10.7639);

        $property = Property::query()->updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => $record['property_name']['en']],
            [
                'agent_id' => $agent->id,
                'category_id' => $subcategory->category_id,
                'name' => $record['property_name'],
                'description' => $record['property_description'],
                'address' => $record['address'],
                'city' => ['en' => $city->name_en, 'ar' => $city->name_ar],
                'city_id' => $city->id,
                'state' => $state->name_en,
                'state_id' => $state->id,
                'postal' => $record['postal'],
                'country' => 'IQ',
                'country_id' => $city->country_id,
                'photos' => $this->seedPhotoSet(
                    "seed/tenants/{$tenant->slug}/properties/{$record['code']}",
                    $tenantVisual,
                    $record,
                    'property'
                ),
            ]
        );

        $property->syncAgents([$agent->id]);

        $unit = Unit::query()->updateOrCreate(
            ['tenant_id' => $tenant->id, 'code' => $record['code']],
            [
                'agent_id' => $agent->id,
                'property_id' => $property->id,
                'subcategory_id' => $subcategory->id,
                'title' => $record['title'],
                'description' => $record['description'],
                'city_id' => $city->id,
                'area_id' => $state->id,
                'price' => $record['price'],
                'currency' => 'IQD',
                'lat' => $record['lat'],
                'lng' => $record['lng'],
                'bedrooms' => $record['beds'],
                'bathrooms' => (int) floor($record['baths']),
                'area_m2' => $areaM2,
                'beds' => $record['beds'],
                'baths' => $record['baths'],
                'sqft' => $record['sqft'],
                'market_rent' => $record['market_rent'],
                'status' => Unit::STATUS_VACANT,
                'listing_type' => $record['listing_type'],
                'location_url' => $record['location_url'],
                'location' => $record['location'],
                'photos' => $this->seedPhotoSet(
                    "seed/tenants/{$tenant->slug}/units/{$record['code']}",
                    $tenantVisual,
                    $record,
                    'unit'
                ),
            ]
        );

        $unit->syncAgents([$agent->id]);
        $unit->officialInfo()->updateOrCreate(['unit_id' => $unit->id], $record['official']);
        $unit->owner()->updateOrCreate(['unit_id' => $unit->id], $record['owner']);
        $this->syncUnitAttributes($unit, $attributeMap[$record['subcategory']] ?? [], $record['attributes']);
    }

    protected function pruneTenantPortfolio(Tenant $tenant, array $payload): void
    {
        $expectedUnitCodes = collect($payload['units'])->pluck('code')->all();
        $expectedAgentEmails = collect($payload['agents'])->pluck('email')->all();

        Unit::query()
            ->where('tenant_id', $tenant->id)
            ->when($expectedUnitCodes !== [], fn ($query) => $query->whereNotIn('code', $expectedUnitCodes))
            ->get()
            ->each(function (Unit $unit): void {
                $unit->unitAttributes()->delete();
                $unit->officialInfo()->delete();
                $unit->owner()->delete();
                $unit->delete();
            });

        $activePropertyIds = Unit::query()
            ->where('tenant_id', $tenant->id)
            ->pluck('property_id')
            ->filter()
            ->all();

        Property::query()
            ->where('tenant_id', $tenant->id)
            ->when($activePropertyIds !== [], fn ($query) => $query->whereNotIn('id', $activePropertyIds))
            ->delete();

        $staleAgentIds = Agent::query()
            ->where('tenant_id', $tenant->id)
            ->when($expectedAgentEmails !== [], fn ($query) => $query->whereNotIn('email', $expectedAgentEmails))
            ->pluck('id')
            ->all();

        if ($staleAgentIds !== []) {
            User::query()->whereIn('agent_id', $staleAgentIds)->update(['agent_id' => null]);
            Agent::query()->whereIn('id', $staleAgentIds)->delete();
        }
    }

    protected function syncUnitAttributes(Unit $unit, array $fields, array $values): void
    {
        $unit->unitAttributes()->delete();

        foreach ($values as $key => $value) {
            $field = $fields[$key] ?? null;

            if (! $field) {
                continue;
            }

            $payload = ['unit_id' => $unit->id, 'attribute_field_id' => $field->id];

            switch ($field->type) {
                case 'bool':
                    $payload['bool_value'] = (bool) $value;
                    break;
                case 'int':
                    $payload['int_value'] = (int) $value;
                    break;
                case 'decimal':
                    $payload['decimal_value'] = (float) $value;
                    break;
                case 'multi_enum':
                    $payload['json_value'] = (array) $value;
                    break;
                default:
                    $payload['string_value'] = (string) $value;
                    break;
            }

            UnitAttribute::query()->create($payload);
        }
    }

    protected function syncPermissionsForTenant(Tenant $tenant): void
    {
        Artisan::call('permissions:sync', ['--tenant' => $tenant->id]);
    }

    protected function assignTenantRole(Tenant $tenant, User $user, string $role): void
    {
        if (! class_exists(PermissionRegistrar::class)) {
            return;
        }

        app(PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);

        if (method_exists($user, 'syncRoles')) {
            $user->syncRoles([$role]);
        }
    }

    protected function tenantVisualContext(array $payload): array
    {
        $stateCode = $payload['units'][0]['state'];
        $state = $this->states[$stateCode] ?? null;

        return [
            'name' => $payload['name'],
            'slug' => $payload['slug'],
            'state_code' => $stateCode,
            'region_en' => $state?->name_en ?? $stateCode,
            'region_ar' => $state?->name_ar ?? $stateCode,
            'primary_color' => $payload['settings']['primary_color'] ?? null,
            'accent_color' => $payload['settings']['accent_color'] ?? null,
            'tagline' => $payload['settings']['tagline'] ?? $payload['name'],
        ];
    }

    protected function writeSeedVisual(string $relativePath, string $contents): string
    {
        $disk = Storage::disk('public');
        $directory = dirname($relativePath);

        if ($directory !== '.' && ! $disk->exists($directory)) {
            $disk->makeDirectory($directory);
        }

        $disk->put($relativePath, $contents);

        return $relativePath;
    }

    protected function purgeLegacySeedAssets(string $tenantSlug): void
    {
        $disk = Storage::disk('public');
        $directory = "seed/tenants/{$tenantSlug}";

        if (! $disk->exists($directory)) {
            return;
        }

        foreach ($disk->allFiles($directory) as $path) {
            if (str_ends_with($path, '.png')) {
                $disk->delete($path);
            }
        }
    }

    protected function seedPhotoSet(string $directory, array $tenantVisual, array $record, string $surface): array
    {
        return collect(range(1, 3))
            ->map(function (int $index) use ($directory, $record, $surface, $tenantVisual): string {
                $contents = $surface === 'property'
                    ? $this->visualFactory()->propertyPhoto($tenantVisual, $record, $index)
                    : $this->visualFactory()->unitPhoto($tenantVisual, $record, $index);

                return $this->writeSeedVisual(sprintf('%s/%02d.svg', $directory, $index), $contents);
            })
            ->all();
    }

    protected function visualFactory(): IraqSeedVisualFactory
    {
        return $this->visualFactory ??= new IraqSeedVisualFactory();
    }

    protected function materializeUnitRecord(array $payload): array
    {
        $subcategoryLabels = $this->subcategoryLabels($payload['subcategory']);
        $purpose = $payload['listing_type'] === Unit::LISTING_SALE
            ? ['en' => 'ownership and long-term investment', 'ar' => 'التملك والاستثمار طويل الأمد']
            : ['en' => 'comfortable day-to-day use', 'ar' => 'الاستخدام اليومي المريح'];
        $city = $this->cities[strtolower($payload['city'])];
        $areaM2 = (int) round($payload['sqft'] / 10.7639);
        $isLand = $this->isLandSubcategory($payload['subcategory']);

        return array_merge($payload, [
            'property_name' => [
                'en' => $payload['district_en'] . ' ' . $this->propertySuffix($payload['subcategory'])['en'],
                'ar' => $this->propertySuffix($payload['subcategory'])['ar'] . ' ' . $payload['district_ar'],
            ],
            'property_description' => [
                'en' => "A professionally presented {$subcategoryLabels['en']} in {$payload['district_en']}, {$city->name_en}.",
                'ar' => "{$subcategoryLabels['ar']} معروضة باحتراف في {$payload['district_ar']}، {$city->name_ar}.",
            ],
            'title' => ['en' => $payload['headline_en'], 'ar' => $payload['headline_ar']],
            'description' => [
                'en' => "A carefully prepared {$subcategoryLabels['en']} in {$payload['district_en']}, {$city->name_en}, with clear Iraqi records and a practical layout for {$purpose['en']}.",
                'ar' => "{$subcategoryLabels['ar']} مجهزة بعناية في {$payload['district_ar']}، {$city->name_ar}، مع مستندات عراقية واضحة ومساحة عملية تناسب {$purpose['ar']}.",
            ],
            'address' => $payload['district_en'] . ', ' . $city->name_en,
            'location' => $payload['district_en'] . ', ' . $city->name_en,
            'location_url' => 'https://maps.google.com/?q=' . $payload['lat'] . ',' . $payload['lng'],
            'official' => [
                'directorate' => $city->name_en . ' Municipality',
                'village' => $payload['district_en'],
                'basin_number' => $payload['basin_number'],
                'basin_name' => $payload['district_en'] . ' Basin',
                'plot_number' => $payload['plot_number'],
                'apartment_number' => $this->apartmentNumber($payload['code'], $payload['subcategory']),
                'areas' => [
                    'land_sqm' => max($areaM2 + 30, $payload['land_sqm'] ?? $areaM2),
                    'built_sqm' => $isLand ? 0 : $areaM2,
                    'total_sqm' => $isLand ? ($payload['land_sqm'] ?? $areaM2) : $areaM2,
                    'notes' => 'Iraqi title and site measurements reviewed for agency listing.',
                ],
            ],
            'owner' => [
                'name' => $payload['owner_name'],
                'phone' => $payload['owner_phone'],
                'email' => strtolower($payload['code']) . '@seed.aqarismart.test',
                'notes' => 'Managed under an exclusive agency instruction.',
            ],
        ]);
    }

    protected function subcategoryLabels(string $slug): array
    {
        return match ($slug) {
            'apartment' => ['en' => 'apartment', 'ar' => 'شقة'],
            'furnished_apartment' => ['en' => 'furnished apartment', 'ar' => 'شقة مفروشة'],
            'duplex' => ['en' => 'duplex', 'ar' => 'دوبلكس'],
            'villa' => ['en' => 'villa', 'ar' => 'فيلا'],
            'office' => ['en' => 'office', 'ar' => 'مكتب'],
            'showroom' => ['en' => 'showroom', 'ar' => 'معرض'],
            'retail_shop' => ['en' => 'retail shop', 'ar' => 'محل تجاري'],
            'warehouse' => ['en' => 'warehouse', 'ar' => 'مستودع'],
            'guest_house' => ['en' => 'guest house', 'ar' => 'بيت ضيافة'],
            'hotel' => ['en' => 'hotel', 'ar' => 'فندق'],
            'residential_land' => ['en' => 'residential land plot', 'ar' => 'قطعة أرض سكنية'],
            'commercial_land' => ['en' => 'commercial land plot', 'ar' => 'قطعة أرض تجارية'],
            default => ['en' => 'property', 'ar' => 'عقار'],
        };
    }

    protected function propertySuffix(string $slug): array
    {
        return match ($slug) {
            'apartment' => ['en' => 'Residences', 'ar' => 'إقامات'],
            'furnished_apartment' => ['en' => 'Furnished Stays', 'ar' => 'إقامات مفروشة'],
            'duplex' => ['en' => 'Duplex Homes', 'ar' => 'منازل دوبلكس'],
            'villa' => ['en' => 'Private Villas', 'ar' => 'فلل خاصة'],
            'office' => ['en' => 'Business Offices', 'ar' => 'مكاتب أعمال'],
            'showroom' => ['en' => 'Showroom Strip', 'ar' => 'معارض'],
            'retail_shop' => ['en' => 'Retail Walk', 'ar' => 'محال تجارية'],
            'warehouse' => ['en' => 'Logistics Yard', 'ar' => 'ساحات لوجستية'],
            'guest_house' => ['en' => 'Guest Courtyard', 'ar' => 'دور ضيافة'],
            'hotel' => ['en' => 'Hospitality Suites', 'ar' => 'أجنحة فندقية'],
            'residential_land' => ['en' => 'Expansion Plots', 'ar' => 'قطع توسعة'],
            'commercial_land' => ['en' => 'Commercial Plots', 'ar' => 'قطع تجارية'],
            default => ['en' => 'Properties', 'ar' => 'عقارات'],
        };
    }

    protected function apartmentNumber(string $code, string $subcategory): string
    {
        return match ($subcategory) {
            'villa' => 'Villa ' . substr($code, -1),
            'warehouse' => 'Warehouse ' . substr($code, -1),
            'hotel' => 'Suite ' . substr($code, -3),
            'residential_land', 'commercial_land' => 'Plot ' . substr($code, -4),
            default => substr($code, -4),
        };
    }

    protected function isLandSubcategory(string $slug): bool
    {
        return in_array($slug, ['residential_land', 'commercial_land'], true);
    }

    protected function tenantBlueprints(): array
    {
        return [
            $this->dijlahRealtyBlueprint(),
            $this->shattAlArabEstatesBlueprint(),
            $this->erbilSkylinePropertiesBlueprint(),
            $this->mosulHeritageRealtyBlueprint(),
            $this->najafCourtyardHomesBlueprint(),
            $this->sulaymaniyahSummitEstatesBlueprint(),
        ];
    }

    protected function dijlahRealtyBlueprint(): array
    {
        return $this->tenantBlueprint(
            name: 'Dijlah Realty',
            slug: 'dijlah-realty',
            plan: 'business',
            primaryColor: '#0f5d46',
            accentColor: '#b48a3a',
            tagline: 'Baghdad homes and business addresses shaped for modern living.',
            footerQuote: 'Trusted Baghdad listings with clear records and practical neighborhood knowledge.',
            owner: [
                'name_en' => 'Haidar Al-Dulaimi',
                'name_ar' => 'حيدر الدليمي',
                'email' => 'owner@dijlah-realty.test',
                'phone' => '+9647701102200',
            ],
            agents: [
                $this->agentBlueprint('omar-khazraji', 'Omar Al-Khazraji', 'عمر الخزرجي', 'omar@dijlah-realty.test', '+9647710011201', 'BGD-AG-101', 2.50),
                $this->agentBlueprint('mariam-obaidi', 'Mariam Al-Obaidi', 'مريم العبيدي', 'mariam@dijlah-realty.test', '+9647710011202', 'BGD-AG-102', 2.75),
            ],
            units: [
                $this->unitBlueprint('DJAP0001', 'apartment', 'omar-khazraji', 'Baghdad', 'BGD', 'Karrada', 'الكرادة', 'Karrada Riverside Apartment', 'شقة مطلة على دجلة في الكرادة', Unit::LISTING_SALE, 185000000, 0, 3, 2.5, 1850, 33.305701, 44.445736, '10011', '12', '144', 'Ahmed Abdulrahman', '+9647802203301', ['floor_number' => 9, 'parking_spaces' => 2, 'generator_amps' => 10, 'furnished' => true, 'elevator' => true, 'balconies' => 2, 'central_ac' => true, 'master_bedroom' => true, 'building_age_years' => 6, 'service_charge_iqd' => 180000]),
                $this->unitBlueprint('DJFA0001', 'furnished_apartment', 'mariam-obaidi', 'Baghdad', 'BGD', 'Adhamiya', 'الأعظمية', 'Turnkey Furnished Apartment in Adhamiya', 'شقة مفروشة جاهزة في الأعظمية', Unit::LISTING_RENT, 1950000, 1950000, 2, 2.0, 1520, 33.377248, 44.379809, '10052', '19', '82', 'Rafidain Kareem', '+9647802203305', ['floor_number' => 4, 'parking_spaces' => 1, 'generator_amps' => 12, 'elevator' => true, 'balconies' => 1, 'central_ac' => true, 'kitchen_equipped' => true, 'linen_ready' => true, 'housekeeping_ready' => false, 'furniture_package_notes' => 'Modern Baghdad package with appliances included.']),
                $this->unitBlueprint('DJDP0001', 'duplex', 'omar-khazraji', 'Baghdad', 'BGD', 'Kadhimiya', 'الكاظمية', 'Family Duplex Near Kadhimiya District', 'دوبلكس عائلي قرب الكاظمية', Unit::LISTING_SALE, 268000000, 0, 4, 3.5, 2860, 33.377004, 44.339295, '10061', '5', '212', 'Mustafa Jabbar', '+9647802203306', ['parking_spaces' => 2, 'private_entrance' => true, 'family_lounge' => true, 'split_levels' => true, 'terrace_area_m2' => 44, 'maid_room' => false, 'storage_room' => true, 'rooftop_access' => true, 'generator_amps' => 14, 'central_ac' => true]),
                $this->unitBlueprint('DJOF0001', 'office', 'mariam-obaidi', 'Baghdad', 'BGD', 'Karrada', 'الكرادة', 'Fully Fitted Office in Karrada', 'مكتب مجهز بالكامل في الكرادة', Unit::LISTING_RENT, 2400000, 2400000, 0, 2.0, 2150, 33.309977, 44.442587, '10069', '18', '77', 'Rasha Kareem', '+9647802203302', ['meeting_rooms' => 2, 'parking_spaces' => 3, 'reception_area' => true, 'generator_amps' => 25, 'fiber_internet' => true, 'fit_out_ready' => true, 'pantry' => true, 'server_room' => true, 'floor_number' => 7, 'security_access' => true]),
                $this->unitBlueprint('DJVI0001', 'villa', 'omar-khazraji', 'Baghdad', 'BGD', 'Al Mansour', 'المنصور', 'Private Villa in Mansour', 'فيلا خاصة في المنصور', Unit::LISTING_SALE, 620000000, 0, 5, 4.0, 5200, 33.308326, 44.328284, '10013', '7', '31', 'Laith Hassan', '+9647802203303', ['garden_area_m2' => 240, 'parking_spaces' => 3, 'maid_room' => true, 'driver_room' => true, 'private_pool' => false, 'generator_amps' => 20, 'central_ac' => true, 'basement_room' => true, 'security_room' => true, 'solar_water_heater' => true], 610),
            ],
        );
    }

    protected function shattAlArabEstatesBlueprint(): array
    {
        return $this->tenantBlueprint(
            name: 'Shatt Al-Arab Estates',
            slug: 'shatt-alarab-estates',
            plan: 'pro',
            primaryColor: '#13624c',
            accentColor: '#c08c36',
            tagline: 'Basra homes, logistics yards, and family villas with verified paperwork.',
            footerQuote: 'Basra supply moves on clean records, port access, and fast local agency follow-through.',
            owner: [
                'name_en' => 'Zainab Al-Basri',
                'name_ar' => 'زينب البصري',
                'email' => 'owner@shattalarab-estates.test',
                'phone' => '+9647803304400',
            ],
            agents: [
                $this->agentBlueprint('ali-jabbar', 'Ali Jabbar', 'علي جبار', 'ali@shattalarab-estates.test', '+9647810022201', 'BSR-AG-201', 2.25),
                $this->agentBlueprint('sara-abdulameer', 'Sara Abdulameer', 'سارة عبدالأمير', 'sara@shattalarab-estates.test', '+9647810022202', 'BSR-AG-202', 2.50),
            ],
            units: [
                $this->unitBlueprint('SHAP0001', 'apartment', 'ali-jabbar', 'Basra', 'BSR', 'Basra Corniche', 'كورنيش البصرة', 'Corniche Apartment with Water View', 'شقة بإطلالة مائية على الكورنيش', Unit::LISTING_RENT, 1450000, 1450000, 2, 2.0, 1485, 30.500314, 47.818413, '61001', '21', '52', 'Mahdi Salman', '+9647823304401', ['floor_number' => 11, 'parking_spaces' => 1, 'generator_amps' => 8, 'furnished' => false, 'elevator' => true, 'balconies' => 1, 'central_ac' => false, 'master_bedroom' => true, 'building_age_years' => 9, 'service_charge_iqd' => 95000]),
                $this->unitBlueprint('SHSR0001', 'showroom', 'ali-jabbar', 'Basra', 'BSR', 'Al Ashar', 'العشار', 'Vehicle Showroom in Al Ashar', 'معرض مركبات في العشار', Unit::LISTING_SALE, 368000000, 0, 0, 2.0, 3540, 30.508910, 47.809100, '61007', '11', '205', 'Jassim Kareem', '+9647823304402', ['display_frontage_m' => 18.5, 'parking_spaces' => 12, 'storage_room' => true, 'ceiling_height_m' => 6.2, 'signage_ready' => true, 'delivery_access' => true, 'corner_unit' => true, 'generator_amps' => 35, 'glass_facade' => true, 'service_lane' => true]),
                $this->unitBlueprint('SHWA0001', 'warehouse', 'sara-abdulameer', 'Umm Qasr', 'BSR', 'Umm Qasr', 'أم قصر', 'Port-Linked Warehouse in Umm Qasr', 'مستودع مرتبط بالميناء في أم قصر', Unit::LISTING_SALE, 455000000, 0, 0, 2.0, 9150, 30.036211, 47.919886, '61122', '3', '16', 'Jassem Kareem', '+9647823304402', ['ceiling_height_m' => 8.5, 'loading_doors' => 3, 'yard_area_m2' => 1050, 'three_phase_power' => true, 'sprinkler_system' => true, 'truck_access' => true, 'dock_levelers' => 2, 'office_built_in' => true, 'cold_storage_ready' => false, 'floor_load_ton' => 12.0], 1900),
                $this->unitBlueprint('SHVI0001', 'villa', 'ali-jabbar', 'Basra', 'BSR', 'Al-Baradiyah', 'البراضية', 'Garden Villa in Al-Baradiyah', 'فيلا بحديقة في البراضية', Unit::LISTING_RENT, 2900000, 2900000, 4, 4.0, 4300, 30.497311, 47.790225, '61014', '9', '41', 'Bashar Nasser', '+9647823304403', ['garden_area_m2' => 190, 'parking_spaces' => 2, 'maid_room' => true, 'driver_room' => false, 'private_pool' => false, 'generator_amps' => 15, 'central_ac' => false, 'basement_room' => false, 'security_room' => true, 'solar_water_heater' => true], 540),
                $this->unitBlueprint('SHLA0001', 'residential_land', 'sara-abdulameer', 'Zubair', 'BSR', 'Al-Zubair', 'الزبير', 'Residential Land Plot in Zubair', 'قطعة أرض سكنية في الزبير', Unit::LISTING_SALE, 128000000, 0, 0, 0.0, 4036, 30.392950, 47.701554, '61140', '28', '203', 'Abeer Abdulhadi', '+9647823304404', ['plot_frontage_m' => 15.0, 'street_width_m' => 20.0, 'corner_plot' => true, 'serviced_land' => true, 'permit_ready' => true, 'sewer_connection' => true, 'electricity_connection' => true, 'water_connection' => true, 'land_levelled' => true, 'nearby_services' => true], 375),
            ],
        );
    }

    protected function erbilSkylinePropertiesBlueprint(): array
    {
        return $this->tenantBlueprint(
            name: 'Erbil Skyline Properties',
            slug: 'erbil-skyline-properties',
            plan: 'business',
            primaryColor: '#155f49',
            accentColor: '#c59a46',
            tagline: 'Curated homes and business addresses across Ankawa, Empire, and Salahaddin.',
            footerQuote: 'Erbil demand rewards strong presentation, clean records, and quick agency execution.',
            owner: [
                'name_en' => 'Baran Karim',
                'name_ar' => 'باران كريم',
                'email' => 'owner@erbil-skyline-properties.test',
                'phone' => '+9647504405500',
            ],
            agents: [
                $this->agentBlueprint('dilan-ahmed', 'Dilan Ahmed', 'ديلان أحمد', 'dilan@erbil-skyline-properties.test', '+9647510033301', 'ARB-AG-301', 2.50),
                $this->agentBlueprint('karwan-abdullah', 'Karwan Abdullah', 'كاروان عبدالله', 'karwan@erbil-skyline-properties.test', '+9647510033302', 'ARB-AG-302', 2.75),
            ],
            units: [
                $this->unitBlueprint('ERAP0001', 'apartment', 'dilan-ahmed', 'Ankawa', 'ARB', 'Ankawa', 'عنكاوا', 'Modern Apartment in Ankawa', 'شقة حديثة في عنكاوا', Unit::LISTING_SALE, 210000000, 0, 2, 2.0, 1635, 36.234240, 43.995350, '44001', '6', '67', 'Dana Yousif', '+9647524405501', ['floor_number' => 6, 'parking_spaces' => 1, 'generator_amps' => 12, 'furnished' => false, 'elevator' => true, 'balconies' => 1, 'central_ac' => true, 'master_bedroom' => true, 'building_age_years' => 4, 'service_charge_iqd' => 125000]),
                $this->unitBlueprint('EROF0001', 'office', 'karwan-abdullah', 'Erbil', 'ARB', 'Empire District', 'إمباير', 'Corporate Office in Empire District', 'مكتب شركات في منطقة إمباير', Unit::LISTING_SALE, 295000000, 0, 0, 2.0, 2410, 36.180438, 44.032024, '44003', '15', '118', 'Hemin Rashid', '+9647524405502', ['meeting_rooms' => 3, 'parking_spaces' => 4, 'reception_area' => true, 'generator_amps' => 32, 'fiber_internet' => true, 'fit_out_ready' => true, 'pantry' => true, 'server_room' => true, 'floor_number' => 9, 'security_access' => true]),
                $this->unitBlueprint('ERVI0001', 'villa', 'dilan-ahmed', 'Erbil', 'ARB', 'Salahaddin', 'صلاح الدين', 'Executive Villa in Salahaddin', 'فيلا تنفيذية في صلاح الدين', Unit::LISTING_RENT, 4200000, 4200000, 5, 5.0, 5600, 36.123726, 44.039898, '44007', '11', '24', 'Sherzad Hoshyar', '+9647524405503', ['garden_area_m2' => 260, 'parking_spaces' => 3, 'maid_room' => true, 'driver_room' => true, 'private_pool' => true, 'generator_amps' => 25, 'central_ac' => true, 'basement_room' => false, 'security_room' => true, 'solar_water_heater' => true], 720),
                $this->unitBlueprint('ERHO0001', 'hotel', 'karwan-abdullah', 'Erbil', 'ARB', 'Shaqlawa Hills', 'تلال شقلاوة', 'Boutique Hotel in Shaqlawa Hills', 'فندق بوتيك في تلال شقلاوة', Unit::LISTING_SALE, 980000000, 0, 18, 18.0, 13670, 36.403258, 44.325028, '44015', '30', '16', 'Sarkawt Aziz', '+9647524405505', ['keys_count' => 18, 'parking_spaces' => 26, 'backup_power' => true, 'restaurant_space' => true, 'conference_room' => true, 'laundry_facility' => true, 'generator_amps' => 180, 'staff_housing' => true, 'elevators_count' => 2, 'service_kitchens' => true], 2100),
                $this->unitBlueprint('ERRS0001', 'retail_shop', 'karwan-abdullah', 'Ankawa', 'ARB', 'Ankawa Main Street', 'الشارع الرئيسي في عنكاوا', 'Retail Shop on Ankawa Main Street', 'محل تجاري على الشارع الرئيسي في عنكاوا', Unit::LISTING_SALE, 338000000, 0, 0, 1.0, 1240, 36.235884, 43.998767, '44009', '22', '81', 'Haval Saeed', '+9647524405504', ['frontage_m' => 8.0, 'storage_room' => true, 'corner_unit' => true, 'parking_spaces' => 6, 'license_ready' => true, 'mezzanine_area_m2' => 120, 'outdoor_signage' => true, 'delivery_access' => true, 'service_lane' => true, 'footfall_notes' => 'Strong street trade and walk-in demand.']),
            ],
        );
    }

    protected function mosulHeritageRealtyBlueprint(): array
    {
        return $this->tenantBlueprint(
            name: 'Mosul Heritage Realty',
            slug: 'mosul-heritage-realty',
            plan: 'pro',
            primaryColor: '#1b5a45',
            accentColor: '#b78d3f',
            tagline: 'Restored homes, guest stays, and trade frontage across Mosul and Nineveh.',
            footerQuote: 'Mosul recovery is driven by trusted paperwork, practical pricing, and visible frontage.',
            owner: [
                'name_en' => 'Ayman Al-Hadidi',
                'name_ar' => 'أيمن الحديدي',
                'email' => 'owner@mosul-heritage-realty.test',
                'phone' => '+9647705506600',
            ],
            agents: [
                $this->agentBlueprint('safa-yunus', 'Safa Yunus', 'صفاء يونس', 'safa@mosul-heritage-realty.test', '+9647710044401', 'NIN-AG-401', 2.40),
                $this->agentBlueprint('haider-najim', 'Haider Najim', 'حيدر ناجم', 'haider@mosul-heritage-realty.test', '+9647710044402', 'NIN-AG-402', 2.65),
            ],
            units: [
                $this->unitBlueprint('MODP0001', 'duplex', 'safa-yunus', 'Mosul', 'NIN', 'Al Majmoa', 'المجموعة', 'Duplex Family Home in Al Majmoa', 'منزل دوبلكس عائلي في المجموعة', Unit::LISTING_SALE, 246000000, 0, 4, 3.0, 2740, 36.353950, 43.142280, '41004', '9', '58', 'Nabil Fadel', '+9647805506601', ['parking_spaces' => 2, 'private_entrance' => true, 'family_lounge' => true, 'split_levels' => true, 'terrace_area_m2' => 36, 'maid_room' => false, 'storage_room' => true, 'rooftop_access' => true, 'generator_amps' => 10, 'central_ac' => false]),
                $this->unitBlueprint('MOGH0001', 'guest_house', 'haider-najim', 'Mosul', 'NIN', 'Old City Edge', 'حافة المدينة القديمة', 'Guest House Near the Old City', 'بيت ضيافة قرب المدينة القديمة', Unit::LISTING_RENT, 2600000, 2600000, 6, 6.0, 3920, 36.341820, 43.132950, '41008', '14', '121', 'Yasmeen Tariq', '+9647805506602', ['suite_count' => 6, 'parking_spaces' => 4, 'courtyard' => true, 'furnished' => true, 'generator_amps' => 18, 'staff_room' => true, 'reception_area' => true, 'laundry_room' => true, 'family_hall' => true, 'rooftop_seating' => true], 460),
                $this->unitBlueprint('MOSR0001', 'showroom', 'safa-yunus', 'Mosul', 'NIN', 'Al Danadan', 'الدندان', 'Showroom on Danadan Corridor', 'معرض على محور الدندان', Unit::LISTING_RENT, 3400000, 3400000, 0, 2.0, 3280, 36.360340, 43.126710, '41012', '18', '64', 'Khalid Salem', '+9647805506603', ['display_frontage_m' => 16.8, 'parking_spaces' => 10, 'storage_room' => true, 'ceiling_height_m' => 5.8, 'signage_ready' => true, 'delivery_access' => true, 'corner_unit' => false, 'generator_amps' => 28, 'glass_facade' => true, 'service_lane' => false]),
                $this->unitBlueprint('MOWA0001', 'warehouse', 'haider-najim', 'Bartella', 'NIN', 'Bartella Logistics Strip', 'ممر برطلة اللوجستي', 'Warehouse in Bartella Logistics Strip', 'مستودع في الممر اللوجستي ببرطلة', Unit::LISTING_SALE, 382000000, 0, 0, 2.0, 8240, 36.357904, 43.377235, '41016', '4', '88', 'Rafaa Fawzi', '+9647805506604', ['ceiling_height_m' => 7.4, 'loading_doors' => 4, 'yard_area_m2' => 960, 'three_phase_power' => true, 'sprinkler_system' => false, 'truck_access' => true, 'dock_levelers' => 2, 'office_built_in' => true, 'cold_storage_ready' => false, 'floor_load_ton' => 10.0], 1700),
                $this->unitBlueprint('MOCL0001', 'commercial_land', 'safa-yunus', 'Mosul', 'NIN', 'Airport Road', 'طريق المطار', 'Commercial Plot on Airport Road', 'قطعة تجارية على طريق المطار', Unit::LISTING_SALE, 214000000, 0, 0, 0.0, 5920, 36.326215, 43.155050, '41021', '22', '304', 'Adnan Saeed', '+9647805506605', ['plot_frontage_m' => 22.0, 'street_width_m' => 30.0, 'corner_plot' => true, 'serviced_land' => true, 'permit_ready' => true, 'heavy_traffic_access' => true, 'electricity_connection' => true, 'water_connection' => true, 'warehouse_ready' => true, 'billboard_exposure' => true], 550),
            ],
        );
    }

    protected function najafCourtyardHomesBlueprint(): array
    {
        return $this->tenantBlueprint(
            name: 'Najaf Courtyard Homes',
            slug: 'najaf-courtyard-homes',
            plan: 'starter',
            primaryColor: '#155641',
            accentColor: '#c29641',
            tagline: 'Pilgrim stays, family apartments, and commercial plots around Najaf and Kufa.',
            footerQuote: 'Najaf demand rewards clean hospitality layouts and strong tenant preparation.',
            owner: [
                'name_en' => 'Rida Al-Kaabi',
                'name_ar' => 'رضا الكعبي',
                'email' => 'owner@najaf-courtyard-homes.test',
                'phone' => '+9647806607700',
            ],
            agents: [
                $this->agentBlueprint('muntazar-hadi', 'Muntazar Hadi', 'منتظر هادي', 'muntazar@najaf-courtyard-homes.test', '+9647810055501', 'NJF-AG-501', 2.30),
                $this->agentBlueprint('zahraa-hamid', 'Zahraa Hamid', 'زهراء حميد', 'zahraa@najaf-courtyard-homes.test', '+9647810055502', 'NJF-AG-502', 2.55),
            ],
            units: [
                $this->unitBlueprint('NJAP0001', 'apartment', 'muntazar-hadi', 'Najaf', 'NJF', 'Al Salam District', 'حي السلام', 'Apartment in Al Salam District', 'شقة في حي السلام', Unit::LISTING_RENT, 1350000, 1350000, 2, 2.0, 1410, 31.996185, 44.339650, '54003', '12', '44', 'Hussein Fadhel', '+9647806607701', ['floor_number' => 3, 'parking_spaces' => 1, 'generator_amps' => 10, 'furnished' => false, 'elevator' => true, 'balconies' => 1, 'central_ac' => false, 'master_bedroom' => true, 'building_age_years' => 8, 'service_charge_iqd' => 85000]),
                $this->unitBlueprint('NJFA0001', 'furnished_apartment', 'zahraa-hamid', 'Kufa', 'NJF', 'Kufa Riverside', 'ضفاف الكوفة', 'Serviced Furnished Apartment in Kufa', 'شقة مفروشة مخدومة في الكوفة', Unit::LISTING_RENT, 1750000, 1750000, 2, 2.0, 1490, 32.041320, 44.397150, '54011', '7', '163', 'Maysam Hashim', '+9647806607702', ['floor_number' => 5, 'parking_spaces' => 1, 'generator_amps' => 12, 'elevator' => true, 'balconies' => 1, 'central_ac' => true, 'kitchen_equipped' => true, 'linen_ready' => true, 'housekeeping_ready' => true, 'furniture_package_notes' => 'Pilgrim-ready furnished package with daily-use appliances.']),
                $this->unitBlueprint('NJGH0001', 'guest_house', 'muntazar-hadi', 'Najaf', 'NJF', 'Old Najaf Access Road', 'طريق النجف القديم', 'Pilgrim Guest House in Najaf', 'بيت ضيافة للحجاج في النجف', Unit::LISTING_SALE, 412000000, 0, 10, 10.0, 4980, 31.992780, 44.327250, '54007', '18', '95', 'Jawad Karim', '+9647806607703', ['suite_count' => 10, 'parking_spaces' => 8, 'courtyard' => true, 'furnished' => true, 'generator_amps' => 28, 'staff_room' => true, 'reception_area' => true, 'laundry_room' => true, 'family_hall' => true, 'rooftop_seating' => false], 620),
                $this->unitBlueprint('NJSR0001', 'showroom', 'zahraa-hamid', 'Kufa', 'NJF', 'Kufa Trade Strip', 'شريط تجارة الكوفة', 'Ceramics Showroom in Kufa', 'معرض سيراميك في الكوفة', Unit::LISTING_RENT, 2850000, 2850000, 0, 2.0, 2870, 32.040102, 44.404233, '54012', '10', '129', 'Ali Nadhim', '+9647806607704', ['display_frontage_m' => 14.2, 'parking_spaces' => 9, 'storage_room' => true, 'ceiling_height_m' => 5.4, 'signage_ready' => true, 'delivery_access' => true, 'corner_unit' => true, 'generator_amps' => 24, 'glass_facade' => true, 'service_lane' => true]),
                $this->unitBlueprint('NJCL0001', 'commercial_land', 'muntazar-hadi', 'Najaf', 'NJF', 'Airport Expansion Road', 'طريق توسعة المطار', 'Commercial Plot Near Najaf Airport Road', 'قطعة تجارية قرب طريق مطار النجف', Unit::LISTING_SALE, 268000000, 0, 0, 0.0, 6640, 31.991420, 44.391100, '54019', '26', '240', 'Rasid Abdulhameed', '+9647806607705', ['plot_frontage_m' => 24.0, 'street_width_m' => 36.0, 'corner_plot' => false, 'serviced_land' => true, 'permit_ready' => true, 'heavy_traffic_access' => true, 'electricity_connection' => true, 'water_connection' => true, 'warehouse_ready' => false, 'billboard_exposure' => true], 617),
            ],
        );
    }

    protected function sulaymaniyahSummitEstatesBlueprint(): array
    {
        return $this->tenantBlueprint(
            name: 'Sulaymaniyah Summit Estates',
            slug: 'sulaymaniyah-summit-estates',
            plan: 'enterprise',
            primaryColor: '#145b47',
            accentColor: '#c79a46',
            tagline: 'Summit homes, boutique hospitality, and premium workspaces across Sulaymaniyah.',
            footerQuote: 'Sulaymaniyah premium buyers respond to strong presentation, hillside views, and exact records.',
            owner: [
                'name_en' => 'Shler Mahmood',
                'name_ar' => 'شلير محمود',
                'email' => 'owner@sulaymaniyah-summit-estates.test',
                'phone' => '+9647507708800',
            ],
            agents: [
                $this->agentBlueprint('ava-salih', 'Ava Salih', 'آفا صالح', 'ava@sulaymaniyah-summit-estates.test', '+9647510066601', 'SUL-AG-601', 2.60),
                $this->agentBlueprint('shwan-tariq', 'Shwan Tariq', 'شوان طارق', 'shwan@sulaymaniyah-summit-estates.test', '+9647510066602', 'SUL-AG-602', 2.85),
            ],
            units: [
                $this->unitBlueprint('SUVI0001', 'villa', 'ava-salih', 'Sulaymaniyah', 'SUL', 'Goizha', 'كويزة', 'Hillside Villa in Goizha', 'فيلا على التلال في كويزة', Unit::LISTING_SALE, 710000000, 0, 5, 5.0, 5840, 35.563370, 45.445770, '46004', '13', '74', 'Bakhtyar Omer', '+9647527708801', ['garden_area_m2' => 310, 'parking_spaces' => 4, 'maid_room' => true, 'driver_room' => true, 'private_pool' => true, 'generator_amps' => 30, 'central_ac' => true, 'basement_room' => true, 'security_room' => true, 'solar_water_heater' => true], 780),
                $this->unitBlueprint('SUDP0001', 'duplex', 'ava-salih', 'Sulaymaniyah', 'SUL', 'German Village', 'القرية الألمانية', 'Duplex Residence in German Village', 'مسكن دوبلكس في القرية الألمانية', Unit::LISTING_SALE, 324000000, 0, 4, 3.0, 3010, 35.583920, 45.424880, '46008', '9', '210', 'Lana Farhad', '+9647527708802', ['parking_spaces' => 2, 'private_entrance' => true, 'family_lounge' => true, 'split_levels' => true, 'terrace_area_m2' => 42, 'maid_room' => true, 'storage_room' => true, 'rooftop_access' => true, 'generator_amps' => 16, 'central_ac' => true]),
                $this->unitBlueprint('SUOF0001', 'office', 'shwan-tariq', 'Sulaymaniyah', 'SUL', 'Salim Street', 'شارع سالم', 'Prime Office on Salim Street', 'مكتب مميز على شارع سالم', Unit::LISTING_RENT, 3200000, 3200000, 0, 2.0, 2660, 35.564830, 45.432730, '46011', '16', '93', 'Karoan Ismail', '+9647527708803', ['meeting_rooms' => 4, 'parking_spaces' => 5, 'reception_area' => true, 'generator_amps' => 40, 'fiber_internet' => true, 'fit_out_ready' => true, 'pantry' => true, 'server_room' => true, 'floor_number' => 8, 'security_access' => true]),
                $this->unitBlueprint('SUHO0001', 'hotel', 'shwan-tariq', 'Sulaymaniyah', 'SUL', 'Azmar Ridge', 'مرتفعات أزمر', 'Boutique Hills Hotel in Azmar', 'فندق بوتيك في مرتفعات أزمر', Unit::LISTING_SALE, 1180000000, 0, 22, 22.0, 14800, 35.579920, 45.468120, '46014', '31', '18', 'Niyan Abdullah', '+9647527708804', ['keys_count' => 22, 'parking_spaces' => 30, 'backup_power' => true, 'restaurant_space' => true, 'conference_room' => true, 'laundry_facility' => true, 'generator_amps' => 220, 'staff_housing' => true, 'elevators_count' => 2, 'service_kitchens' => true], 2400),
                $this->unitBlueprint('SUCL0001', 'commercial_land', 'ava-salih', 'Chamchamal', 'SUL', 'Industrial Gateway', 'البوابة الصناعية', 'Commercial Plot in Chamchamal Gateway', 'قطعة تجارية في بوابة جمجمال', Unit::LISTING_SALE, 205000000, 0, 0, 0.0, 7060, 35.536666, 44.831944, '46022', '24', '306', 'Hoshyar Rasool', '+9647527708805', ['plot_frontage_m' => 26.0, 'street_width_m' => 32.0, 'corner_plot' => true, 'serviced_land' => true, 'permit_ready' => true, 'heavy_traffic_access' => true, 'electricity_connection' => true, 'water_connection' => true, 'warehouse_ready' => true, 'billboard_exposure' => true], 656),
            ],
        );
    }

    protected function tenantBlueprint(
        string $name,
        string $slug,
        string $plan,
        string $primaryColor,
        string $accentColor,
        string $tagline,
        string $footerQuote,
        array $owner,
        array $agents,
        array $units,
    ): array {
        return [
            'name' => $name,
            'slug' => $slug,
            'plan' => $plan,
            'settings' => [
                'primary_color' => $primaryColor,
                'accent_color' => $accentColor,
                'tagline' => $tagline,
                'footer_quote' => $footerQuote,
            ],
            'owner' => $owner,
            'agents' => $agents,
            'units' => $units,
        ];
    }

    protected function agentBlueprint(
        string $slug,
        string $nameEn,
        string $nameAr,
        string $email,
        string $phone,
        string $licenseId,
        float $commissionRate,
    ): array {
        return [
            'slug' => $slug,
            'name' => ['en' => $nameEn, 'ar' => $nameAr],
            'email' => $email,
            'phone' => $phone,
            'license_id' => $licenseId,
            'commission_rate' => $commissionRate,
        ];
    }

    protected function unitBlueprint(
        string $code,
        string $subcategory,
        string $agent,
        string $city,
        string $state,
        string $districtEn,
        string $districtAr,
        string $headlineEn,
        string $headlineAr,
        string $listingType,
        int $price,
        int $marketRent,
        int $beds,
        float $baths,
        int $sqft,
        float $lat,
        float $lng,
        string $postal,
        string $basinNumber,
        string $plotNumber,
        string $ownerName,
        string $ownerPhone,
        array $attributes,
        ?int $landSqm = null,
    ): array {
        return array_filter([
            'code' => $code,
            'subcategory' => $subcategory,
            'agent' => $agent,
            'city' => $city,
            'state' => $state,
            'district_en' => $districtEn,
            'district_ar' => $districtAr,
            'headline_en' => $headlineEn,
            'headline_ar' => $headlineAr,
            'listing_type' => $listingType,
            'price' => $price,
            'market_rent' => $marketRent,
            'beds' => $beds,
            'baths' => $baths,
            'sqft' => $sqft,
            'lat' => $lat,
            'lng' => $lng,
            'postal' => $postal,
            'basin_number' => $basinNumber,
            'plot_number' => $plotNumber,
            'owner_name' => $ownerName,
            'owner_phone' => $ownerPhone,
            'attributes' => $attributes,
            'land_sqm' => $landSqm,
        ], static fn ($value) => $value !== null);
    }
}
