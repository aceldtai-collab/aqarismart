<?php

namespace App\Services\Search;

use App\Models\Tenant;
use App\Models\Unit;
use App\Services\Tenancy\TenantManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SearchExperienceBuilder
{
    /**
     * Approximate map fallbacks for locations frequently used in local and production-style data.
     *
     * @var array<string, array{lat: float, lng: float}>
     */
    private const PLACE_COORDINATES = [
        'amman' => ['lat' => 31.9539494, 'lng' => 35.9106350],
        'عمان' => ['lat' => 31.9539494, 'lng' => 35.9106350],
        'zarqa' => ['lat' => 32.0727530, 'lng' => 36.0889350],
        'الزرقاء' => ['lat' => 32.0727530, 'lng' => 36.0889350],
        'irbid' => ['lat' => 32.5569636, 'lng' => 35.8478960],
        'إربد' => ['lat' => 32.5569636, 'lng' => 35.8478960],
        'ajloun' => ['lat' => 32.3332576, 'lng' => 35.7518020],
        'عجلون' => ['lat' => 32.3332576, 'lng' => 35.7518020],
        'madaba' => ['lat' => 31.7165941, 'lng' => 35.7943856],
        'مادبا' => ['lat' => 31.7165941, 'lng' => 35.7943856],
        'aqaba' => ['lat' => 29.5266730, 'lng' => 35.0077800],
        'العقبة' => ['lat' => 29.5266730, 'lng' => 35.0077800],
        'salt' => ['lat' => 32.0391666, 'lng' => 35.7272222],
        'السلط' => ['lat' => 32.0391666, 'lng' => 35.7272222],
        'mafraq' => ['lat' => 32.3416924, 'lng' => 36.2029971],
        'المفرق' => ['lat' => 32.3416924, 'lng' => 36.2029971],
        'baghdad' => ['lat' => 33.3128057, 'lng' => 44.3614875],
        'بغداد' => ['lat' => 33.3128057, 'lng' => 44.3614875],
        'basra' => ['lat' => 30.5085230, 'lng' => 47.7804010],
        'البصرة' => ['lat' => 30.5085230, 'lng' => 47.7804010],
        'erbil' => ['lat' => 36.1911130, 'lng' => 44.0091670],
        'أربيل' => ['lat' => 36.1911130, 'lng' => 44.0091670],
        'najaf' => ['lat' => 31.9985400, 'lng' => 44.3398500],
        'النجف' => ['lat' => 31.9985400, 'lng' => 44.3398500],
        'mosul' => ['lat' => 36.3450000, 'lng' => 43.1575000],
        'الموصل' => ['lat' => 36.3450000, 'lng' => 43.1575000],
        'karbala' => ['lat' => 32.6160300, 'lng' => 44.0248800],
        'كربلاء' => ['lat' => 32.6160300, 'lng' => 44.0248800],
        'sulaymaniyah' => ['lat' => 35.5612800, 'lng' => 45.4300000],
        'السليمانية' => ['lat' => 35.5612800, 'lng' => 45.4300000],
        'dohuk' => ['lat' => 36.8617400, 'lng' => 42.9884500],
        'دهوك' => ['lat' => 36.8617400, 'lng' => 42.9884500],
        'denver' => ['lat' => 39.7392358, 'lng' => -104.9902510],
    ];

    public function __construct(private readonly TenantManager $tenantManager)
    {
    }

    /**
     * @param  Collection<int, Unit>  $units
     * @param  array{scope?: string, tenant?: Tenant|null, total?: int|null}  $options
     * @return array<string, mixed>
     */
    public function build(Collection $units, array $options = []): array
    {
        $scope = $options['scope'] ?? 'public';
        $tenant = $options['tenant'] ?? null;
        $total = (int) ($options['total'] ?? $units->count());

        $markers = $units
            ->map(fn (Unit $unit) => $this->buildMarker($unit, $scope, $tenant))
            ->filter()
            ->values();
        $markers = $this->spreadMarkers($markers);

        $prices = $units
            ->map(fn (Unit $unit) => $this->displayPrice($unit))
            ->filter(fn (?float $price) => $price !== null && $price > 0)
            ->values();

        $locationClusters = $this->buildLocationClusters($units);
        $typeClusters = $this->buildTypeClusters($units);

        return [
            'summary' => [
                'total_results' => $total,
                'visible_results' => $units->count(),
                'location_count' => $locationClusters->count(),
                'type_count' => $typeClusters->count(),
                'price_min' => $prices->min(),
                'price_max' => $prices->max(),
                'currency' => $units->pluck('currency')->filter()->map(fn ($currency) => strtoupper((string) $currency))->first() ?? 'JOD',
                'rent_count' => $units->where('listing_type', Unit::LISTING_RENT)->count(),
                'sale_count' => $units->where('listing_type', Unit::LISTING_SALE)->count(),
            ],
            'map' => [
                'center' => $this->mapCenter($markers),
                'markers' => $markers->all(),
            ],
            'locations' => $locationClusters->all(),
            'types' => $typeClusters->all(),
        ];
    }

    private function displayPrice(Unit $unit): ?float
    {
        $price = ($unit->listing_type ?? Unit::LISTING_RENT) === Unit::LISTING_SALE
            ? $unit->price
            : (($unit->market_rent && $unit->market_rent > 0) ? $unit->market_rent : $unit->price);

        return $price !== null ? (float) $price : null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function buildMarker(Unit $unit, string $scope, ?Tenant $tenant = null): ?array
    {
        $coordinates = $this->coordinatesFor($unit);
        if ($coordinates === null) {
            return null;
        }

        $title = $unit->translated_title ?: ($unit->property?->name ?? $unit->code);
        $type = $unit->subcategory?->name ?? __('Property');
        $location = $this->locationLabel($unit);
        $price = $this->displayPrice($unit);

        return [
            'title' => $title,
            'type' => $type,
            'location' => $location,
            'price' => $price,
            'currency' => strtoupper((string) ($unit->currency ?? 'JOD')),
            'listing_type' => $unit->listing_type,
            'href' => $this->listingUrl($unit, $scope, $tenant),
            'photo' => $unit->photos[0] ?? null,
            'code' => $unit->code,
            'lat' => $coordinates['lat'],
            'lng' => $coordinates['lng'],
            'approximate' => $coordinates['approximate'],
        ];
    }

    private function listingUrl(Unit $unit, string $scope, ?Tenant $tenant = null): string
    {
        if (in_array($scope, ['mobile_public', 'mobile_tenant'], true)) {
            return route('mobile.units.show', ['unit' => $unit]);
        }

        if ($scope === 'tenant' && $tenant instanceof Tenant) {
            return route('tenant.unit', ['tenant_slug' => $tenant->slug, 'unit' => $unit]);
        }

        if ($unit->tenant instanceof Tenant) {
            return $this->tenantManager->tenantUrl($unit->tenant, '/listings/' . $unit->code);
        }

        return '#';
    }

    /**
     * @return array{lat: float, lng: float, approximate: bool}|null
     */
    private function coordinatesFor(Unit $unit): ?array
    {
        if ($unit->lat !== null && $unit->lng !== null) {
            return [
                'lat' => (float) $unit->lat,
                'lng' => (float) $unit->lng,
                'approximate' => false,
            ];
        }

        $property = $unit->property;
        $propertyCityRelation = $property?->getRelation('city');
        $propertyStateRelation = $property?->getRelation('state');

        $candidates = [
            $unit->city?->name_en,
            $unit->city?->name_ar,
            $unit->area?->name_en,
            $unit->area?->name_ar,
            $propertyCityRelation?->name_en,
            $propertyCityRelation?->name_ar,
            $property?->getRawOriginal('city'),
            $propertyStateRelation?->name_en,
            $propertyStateRelation?->name_ar,
            $property?->getRawOriginal('state'),
            $property?->address,
            Str::before((string) $unit->location, '-'),
            Str::before((string) $unit->location, ','),
            $unit->location,
        ];

        foreach ($candidates as $candidate) {
            $pin = $this->matchCoordinate((string) $candidate);
            if ($pin !== null) {
                return [
                    'lat' => $pin['lat'],
                    'lng' => $pin['lng'],
                    'approximate' => true,
                ];
            }
        }

        return null;
    }

    /**
     * @return array{lat: float, lng: float}|null
     */
    private function matchCoordinate(string $candidate): ?array
    {
        $candidate = trim(Str::lower($candidate));
        if ($candidate === '') {
            return null;
        }

        if (isset(self::PLACE_COORDINATES[$candidate])) {
            return self::PLACE_COORDINATES[$candidate];
        }

        foreach (self::PLACE_COORDINATES as $needle => $pin) {
            if (Str::contains($candidate, $needle)) {
                return $pin;
            }
        }

        return null;
    }

    private function locationLabel(Unit $unit): string
    {
        $property = $unit->property;
        $propertyCityRelation = $property?->getRelation('city');
        $propertyStateRelation = $property?->getRelation('state');

        $labels = [
            $unit->location,
            $unit->city?->{app()->getLocale() === 'ar' ? 'name_ar' : 'name_en'},
            $unit->city?->name_en,
            $unit->area?->{app()->getLocale() === 'ar' ? 'name_ar' : 'name_en'},
            $unit->area?->name_en,
            $propertyCityRelation?->{app()->getLocale() === 'ar' ? 'name_ar' : 'name_en'},
            $propertyCityRelation?->name_en,
            $property?->getRawOriginal('city'),
            $propertyStateRelation?->{app()->getLocale() === 'ar' ? 'name_ar' : 'name_en'},
            $propertyStateRelation?->name_en,
            $property?->address,
            __('Location details coming soon'),
        ];

        foreach ($labels as $label) {
            $label = trim((string) $label);
            if ($label !== '') {
                return $label;
            }
        }

        return __('Location details coming soon');
    }

    /**
     * @param  Collection<int, Unit>  $units
     * @return Collection<int, array{name: string, count: int}>
     */
    private function buildLocationClusters(Collection $units): Collection
    {
        return $units
            ->map(function (Unit $unit): ?string {
                $location = trim((string) $unit->location);
                if ($location !== '') {
                    return trim((string) Str::before($location, '-')) ?: $location;
                }

                $property = $unit->property;
                $propertyCityRelation = $property?->getRelation('city');

                return $unit->city?->{app()->getLocale() === 'ar' ? 'name_ar' : 'name_en'}
                    ?: $unit->city?->name_en
                    ?: $propertyCityRelation?->{app()->getLocale() === 'ar' ? 'name_ar' : 'name_en'}
                    ?: $propertyCityRelation?->name_en
                    ?: $property?->getRawOriginal('city')
                    ?: null;
            })
            ->filter()
            ->countBy()
            ->sortDesc()
            ->take(6)
            ->map(fn (int $count, string $name) => ['name' => $name, 'count' => $count])
            ->values();
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $markers
     * @return Collection<int, array<string, mixed>>
     */
    private function spreadMarkers(Collection $markers): Collection
    {
        return $markers
            ->groupBy(fn (array $marker) => round((float) $marker['lat'], 5) . ':' . round((float) $marker['lng'], 5))
            ->flatMap(function (Collection $group): array {
                $count = $group->count();
                if ($count <= 1) {
                    return $group->all();
                }

                $distance = 0.0105;

                return $group->values()->map(function (array $marker, int $index) use ($count, $distance): array {
                    $angle = (2 * pi() * $index) / $count;

                    $marker['lat'] = round((float) $marker['lat'] + (sin($angle) * $distance), 6);
                    $marker['lng'] = round((float) $marker['lng'] + (cos($angle) * $distance), 6);

                    return $marker;
                })->all();
            })
            ->values();
    }

    /**
     * @param  Collection<int, Unit>  $units
     * @return Collection<int, array{name: string, count: int}>
     */
    private function buildTypeClusters(Collection $units): Collection
    {
        return $units
            ->map(fn (Unit $unit): ?string => $unit->subcategory?->name)
            ->filter()
            ->countBy()
            ->sortDesc()
            ->take(6)
            ->map(fn (int $count, string $name) => ['name' => $name, 'count' => $count])
            ->values();
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $markers
     * @return array{lat: float, lng: float, zoom: int}
     */
    private function mapCenter(Collection $markers): array
    {
        if ($markers->isEmpty()) {
            return ['lat' => 31.9539494, 'lng' => 35.9106350, 'zoom' => 7];
        }

        return [
            'lat' => round((float) $markers->avg('lat'), 6),
            'lng' => round((float) $markers->avg('lng'), 6),
            'zoom' => $markers->contains(fn (array $marker) => $marker['approximate'] === false) ? 11 : 9,
        ];
    }
}
