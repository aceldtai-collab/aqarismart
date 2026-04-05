<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MobileTenantResource;
use App\Http\Resources\MobileUnitResource;
use App\Models\Agent;
use App\Models\Category;
use App\Models\City;
use App\Models\Tenant;
use App\Models\Unit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MobileMarketplaceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->abortIfNotCentralDomain($request);

        try {
            return $this->queryMarketplace($request);
        } catch (\Illuminate\Database\QueryException $e) {
            // Graceful fallback for Jump/SQLite where tables may not exist
            return response()->json([
                'data' => [],
                'meta' => ['current_page' => 1, 'last_page' => 1, 'per_page' => 12, 'total' => 0],
                'filters' => $request->only(['q', 'listing_type', 'subcategory_id', 'city_id', 'tenant_id', 'bedrooms', 'price_min', 'price_max', 'sort']),
                'stats' => ['properties' => 0, 'sale' => 0, 'rent' => 0, 'managers' => 0, 'agents' => 0],
                'categories' => [],
                'cities' => [],
                'tenants' => [],
            ]);
        }
    }

    private function abortIfNotCentralDomain(Request $request): void
    {
        abort_unless($this->isAllowedCentralHost($request), 404);
    }

    private function isAllowedCentralHost(Request $request): bool
    {
        $host = strtolower((string) $request->getHost());
        $baseDomain = strtolower(trim((string) config('tenancy.base_domain')));
        $appHost = strtolower((string) parse_url((string) config('app.url'), PHP_URL_HOST));
        $centralDomains = array_filter(array_map(
            static fn (string $domain): string => strtolower(trim($domain)),
            explode(',', (string) env('CENTRAL_DOMAINS', ''))
        ));

        $allowedHosts = array_filter(array_unique([
            $baseDomain,
            $baseDomain !== '' ? 'www.' . $baseDomain : '',
            $appHost,
            'localhost',
            '127.0.0.1',
            '::1',
            '[::1]',
            'nativephp-mobile',
            ...$centralDomains,
        ]));

        if (in_array($host, $allowedHosts, true)) {
            return true;
        }

        if (! app()->environment(['local', 'development', 'testing'])) {
            return false;
        }

        $normalizedHost = trim($host, '[]');
        if (filter_var($normalizedHost, FILTER_VALIDATE_IP)) {
            return true;
        }

        if ($baseDomain !== '' && Str::endsWith($host, '.' . $baseDomain)) {
            return false;
        }

        return true;
    }

    private function queryMarketplace(Request $request): JsonResponse
    {
        $baseQuery = Unit::withoutGlobalScope('tenant')
            ->with(['tenant.activeSubscription.package', 'property.city', 'property.state', 'subcategory.category', 'city', 'area', 'unitAttributes.attributeField'])
            ->whereHas('tenant', fn ($q) => $q->whereHas('activeSubscription'))
            ->where('status', Unit::STATUS_VACANT);

        $query = clone $baseQuery;

        if ($request->filled('q')) {
            $q = $request->string('q')->trim()->value();
            $query->where(function ($builder) use ($q) {
                $builder->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%")
                    ->orWhere('location', 'like', "%{$q}%")
                    ->orWhereHas('property', fn ($p) => $p
                        ->where('name', 'like', "%{$q}%")
                        ->orWhere('address', 'like', "%{$q}%")
                        ->orWhere('city', 'like', "%{$q}%"));
            });
        }

        foreach (['listing_type', 'subcategory_id', 'city_id', 'tenant_id'] as $field) {
            if ($request->filled($field)) {
                $query->where($field, $request->input($field));
            }
        }

        if ($request->filled('category_id')) {
            $query->whereHas('subcategory', function ($q) use ($request) {
                $q->where('category_id', $request->input('category_id'));
            });
        }

        if ($request->filled('bedrooms')) {
            $query->where(function ($builder) use ($request) {
                $bedrooms = (int) $request->input('bedrooms');
                $builder->where('bedrooms', '>=', $bedrooms)
                    ->orWhere('beds', '>=', $bedrooms);
            });
        }

        $priceMin = $request->filled('price_min') ? (float) $request->input('price_min') : null;
        $priceMax = $request->filled('price_max') ? (float) $request->input('price_max') : null;

        if ($priceMin !== null || $priceMax !== null) {
            $query->where(function ($priceQuery) use ($priceMin, $priceMax) {
                $priceQuery
                    ->where(function ($rentQuery) use ($priceMin, $priceMax) {
                        $rentQuery->where('listing_type', Unit::LISTING_RENT);
                        if ($priceMin !== null) {
                            $rentQuery->whereRaw('COALESCE(NULLIF(market_rent, 0), price) >= ?', [$priceMin]);
                        }
                        if ($priceMax !== null) {
                            $rentQuery->whereRaw('COALESCE(NULLIF(market_rent, 0), price) <= ?', [$priceMax]);
                        }
                    })
                    ->orWhere(function ($saleQuery) use ($priceMin, $priceMax) {
                        $saleQuery->where('listing_type', Unit::LISTING_SALE);
                        if ($priceMin !== null) {
                            $saleQuery->where('price', '>=', $priceMin);
                        }
                        if ($priceMax !== null) {
                            $saleQuery->where('price', '<=', $priceMax);
                        }
                    });
            });
        }

        $sort = $request->input('sort', 'latest');
        $query = match ($sort) {
            'price_asc' => $query->orderByRaw("CASE WHEN listing_type = 'rent' THEN COALESCE(NULLIF(market_rent, 0), price) ELSE price END asc"),
            'price_desc' => $query->orderByRaw("CASE WHEN listing_type = 'rent' THEN COALESCE(NULLIF(market_rent, 0), price) ELSE price END desc"),
            'oldest' => $query->oldest(),
            default => $query->latest(),
        };

        $units = $query->paginate((int) $request->input('per_page', 12))->withQueryString();

        $tenants = Tenant::whereHas('activeSubscription')
            ->withCount(['units' => fn($q) => $q->where('status', Unit::STATUS_VACANT)])
            ->with('users')
            ->orderByDesc('units_count')
            ->limit(6)
            ->get();

        $cityCounts = Unit::withoutGlobalScope('tenant')
            ->selectRaw('city_id, COUNT(*) as units_count')
            ->whereNotNull('city_id')
            ->where('status', Unit::STATUS_VACANT)
            ->whereHas('tenant', fn ($q) => $q->whereHas('activeSubscription'))
            ->groupBy('city_id')
            ->orderByDesc('units_count')
            ->limit(4)
            ->get();

        $popularCities = City::whereIn('id', $cityCounts->pluck('city_id'))
            ->get()
            ->map(function (City $city) use ($cityCounts) {
                $cityImages = [
                    'Amman' => 'https://images.unsplash.com/photo-1589803889073-61a7a0fb9c74?auto=format&fit=crop&q=80&w=400',
                    'Irbid' => 'https://images.unsplash.com/photo-1605810230434-7631ac76ec81?auto=format&fit=crop&q=80&w=400',
                    'Zarqa' => 'https://images.unsplash.com/photo-1542361345-89e58247f2d5?auto=format&fit=crop&q=80&w=400',
                    'Aqaba' => 'https://images.unsplash.com/photo-1621303837174-89787a7d4729?auto=format&fit=crop&q=80&w=400',
                ];
                return [
                    'id' => $city->id,
                    'name_en' => $city->name_en,
                    'name_ar' => $city->name_ar,
                    'image' => $cityImages[$city->name_en] ?? 'https://images.unsplash.com/photo-1517737281489-08a804ed8924?auto=format&fit=crop&q=80&w=400',
                    'units_count' => (int) optional($cityCounts->firstWhere('city_id', $city->id))->units_count,
                ];
            })->values();

        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->withCount('subcategories')
            ->get()
            ->map(function (Category $category) {
                $image = match(strtolower($category->name_en)) {
                    'residential' => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&q=80&w=400',
                    'commercial' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&q=80&w=400',
                    'land' => 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&q=80&w=400',
                    default => 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&q=80&w=400',
                };
                return [
                    'id' => $category->id,
                    'name' => [
                        'en' => $category->name_en ?? $category->name,
                        'ar' => $category->name_ar ?? $category->name,
                    ],
                    'image' => $image,
                    'count' => Unit::whereHas('subcategory', fn($q) => $q->where('category_id', $category->id))->count(),
                ];
            });
            
        $featuredSectionUnits = Unit::withoutGlobalScope('tenant')
            ->with(['tenant', 'property.city', 'property.state', 'subcategory.category', 'city', 'area', 'unitAttributes.attributeField'])
            ->where('status', Unit::STATUS_VACANT)
            ->where('listing_type', Unit::LISTING_SALE)
            ->whereHas('tenant', fn ($q) => $q->whereHas('activeSubscription'))
            ->latest()
            ->limit(5)
            ->get();

        $recommendedSectionUnits = Unit::withoutGlobalScope('tenant')
            ->with(['tenant', 'property.city', 'property.state', 'subcategory.category', 'city', 'area', 'unitAttributes.attributeField'])
            ->where('status', Unit::STATUS_VACANT)
            ->whereHas('tenant', fn ($q) => $q->whereHas('activeSubscription'))
            ->latest()
            ->limit(6)
            ->get();

        $stats = [
            'properties' => (clone $baseQuery)->count(),
            'sale' => (clone $baseQuery)->where('listing_type', Unit::LISTING_SALE)->count(),
            'rent' => (clone $baseQuery)->where('listing_type', Unit::LISTING_RENT)->count(),
            'managers' => $tenants->count(),
            'agents' => Agent::withoutGlobalScope('tenant')
                ->whereIn('tenant_id', $tenants->modelKeys())
                ->where('active', true)
                ->count(),
        ];

        return response()->json([
            'data' => MobileUnitResource::collection($units->getCollection()),
            'meta' => [
                'current_page' => $units->currentPage(),
                'last_page' => $units->lastPage(),
                'per_page' => $units->perPage(),
                'total' => $units->total(),
            ],
            'filters' => $request->only(['q', 'listing_type', 'subcategory_id', 'city_id', 'tenant_id', 'bedrooms', 'price_min', 'price_max', 'sort']),
            'stats' => $stats,
            'categories' => $categories,
            'cities' => $popularCities,
            'tenants' => MobileTenantResource::collection($tenants),
            'featured_units' => MobileUnitResource::collection($featuredSectionUnits),
            'recommended_units' => MobileUnitResource::collection($recommendedSectionUnits),
        ]);
    }
}
