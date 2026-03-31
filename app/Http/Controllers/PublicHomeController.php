<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Category;
use App\Models\City;
use App\Models\Subcategory;
use App\Models\Tenant;
use App\Models\Unit;
use App\Services\PublicLandingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicHomeController extends Controller
{
    public function index(PublicLandingService $landing): View
    {
        $categories = Category::query()->orderBy('name')->get();

        // Tenants with active subscriptions (showcase on landing)
        $subscribedTenants = Tenant::whereHas('activeSubscription')
            ->latest()
            ->limit(8)
            ->get();

        // Featured units from different tenants (bypass tenant scope)
        $featuredUnits = Unit::withoutGlobalScope('tenant')
            ->with(['tenant', 'property', 'subcategory', 'city'])
            ->whereHas('tenant', fn($q) => $q->whereHas('activeSubscription'))
            ->where('status', Unit::STATUS_VACANT)
            ->latest()
            ->limit(8)
            ->get();

        $tenantsCount = Tenant::whereHas('activeSubscription')->count();
        $unitsCount = Unit::withoutGlobalScope('tenant')
            ->whereHas('tenant', fn($q) => $q->whereHas('activeSubscription'))
            ->count();

        return view('home', [
            'landing' => $landing->forPublicDomain(),
            'categories' => $categories,
            'subscribedTenants' => $subscribedTenants,
            'featuredUnits' => $featuredUnits,
            'tenantsCount' => $tenantsCount,
            'unitsCount' => $unitsCount,
        ]);
    }

    public function marketplace(Request $request, PublicLandingService $landing): View
    {
        $baseQuery = Unit::withoutGlobalScope('tenant')
            ->with(['tenant', 'property', 'subcategory', 'city'])
            ->whereHas('tenant', fn($q) => $q->whereHas('activeSubscription'))
            ->where('status', Unit::STATUS_VACANT);

        $query = clone $baseQuery;

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($builder) use ($q) {
                $builder->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%")
                    ->orWhereHas('property', fn($p) => $p->where('name', 'like', "%{$q}%"));
            });
        }

        if ($request->filled('listing_type')) {
            $query->where('listing_type', $request->input('listing_type'));
        }
        if ($request->filled('subcategory_id')) {
            $query->where('subcategory_id', $request->input('subcategory_id'));
        }
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->input('city_id'));
        }
        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', $request->input('tenant_id'));
        }
        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', '>=', (int) $request->input('bedrooms'));
        }
        if ($request->filled('price_min')) {
            $query->where('price', '>=', (float) $request->input('price_min'));
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', (float) $request->input('price_max'));
        }

        $sort = $request->input('sort', 'latest');
        $query = match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'oldest' => $query->oldest(),
            default => $query->latest(),
        };

        $units = $query->paginate(12)->withQueryString();

        $categories = Category::where('is_active', true)->orderBy('sort_order')->with('subcategories')->get();
        $cities = City::where('is_active', true)->orderBy('name_en')->get();
        $tenants = Tenant::whereHas('activeSubscription')->orderBy('name')->get();
        $activeTenantIds = $tenants->modelKeys();

        $featuredUnits = (clone $baseQuery)
            ->latest()
            ->limit(6)
            ->get();

        $saleUnits = (clone $baseQuery)
            ->where('listing_type', Unit::LISTING_SALE)
            ->latest()
            ->limit(5)
            ->get();

        $rentUnits = (clone $baseQuery)
            ->where('listing_type', Unit::LISTING_RENT)
            ->latest()
            ->limit(5)
            ->get();

        $agents = Agent::withoutGlobalScope('tenant')
            ->where('active', true)
            ->whereIn('tenant_id', $activeTenantIds)
            ->latest()
            ->limit(6)
            ->get();

        $cityCounts = Unit::withoutGlobalScope('tenant')
            ->selectRaw('city_id, COUNT(*) as units_count')
            ->whereNotNull('city_id')
            ->where('status', Unit::STATUS_VACANT)
            ->whereHas('tenant', fn($q) => $q->whereHas('activeSubscription'))
            ->groupBy('city_id')
            ->orderByDesc('units_count')
            ->limit(4)
            ->get();

        $popularCities = City::whereIn('id', $cityCounts->pluck('city_id'))
            ->get()
            ->map(function (City $city) use ($cityCounts) {
                $city->units_count = (int) optional($cityCounts->firstWhere('city_id', $city->id))->units_count;
                return $city;
            })
            ->sortByDesc('units_count')
            ->values();

        $stats = [
            'properties' => (clone $baseQuery)->count(),
            'sale' => (clone $baseQuery)->where('listing_type', Unit::LISTING_SALE)->count(),
            'rent' => (clone $baseQuery)->where('listing_type', Unit::LISTING_RENT)->count(),
            'managers' => $tenants->count(),
        ];

        return view('public.marketplace', [
            'landing' => $landing->forPublicDomain(),
            'units' => $units,
            'featuredUnits' => $featuredUnits,
            'saleUnits' => $saleUnits,
            'rentUnits' => $rentUnits,
            'agents' => $agents,
            'popularCities' => $popularCities,
            'stats' => $stats,
            'categories' => $categories,
            'cities' => $cities,
            'tenants' => $tenants,
            'filters' => $request->only(['q', 'listing_type', 'subcategory_id', 'city_id', 'tenant_id', 'bedrooms', 'price_min', 'price_max', 'sort']),
        ]);
    }

    public function search(Request $request, PublicLandingService $landing): View
    {
        $query = Unit::withoutGlobalScope('tenant')
            ->with(['tenant', 'property', 'subcategory', 'city'])
            ->whereHas('tenant', fn($q) => $q->whereHas('activeSubscription'));

        // Search keyword
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($builder) use ($q) {
                $builder->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%")
                    ->orWhereHas('property', fn($p) => $p->where('name', 'like', "%{$q}%"));
            });
        }

        // Filters
        if ($request->filled('listing_type')) {
            $query->where('listing_type', $request->input('listing_type'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('subcategory_id')) {
            $query->where('subcategory_id', $request->input('subcategory_id'));
        }
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->input('city_id'));
        }
        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', $request->input('tenant_id'));
        }
        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', '>=', (int) $request->input('bedrooms'));
        }
        if ($request->filled('price_min')) {
            $query->where('price', '>=', (float) $request->input('price_min'));
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', (float) $request->input('price_max'));
        }

        // Sort
        $sort = $request->input('sort', 'latest');
        $query = match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'oldest' => $query->oldest(),
            default => $query->latest(),
        };

        $units = $query->paginate(12)->withQueryString();

        // Filter options
        $categories = Category::where('is_active', true)->orderBy('sort_order')->with('subcategories')->get();
        $cities = City::where('is_active', true)->orderBy('name_en')->get();
        $tenants = Tenant::whereHas('activeSubscription')->orderBy('name')->get();

        return view('public.search', [
            'landing' => $landing->forPublicDomain(),
            'units' => $units,
            'categories' => $categories,
            'cities' => $cities,
            'tenants' => $tenants,
            'filters' => $request->only(['q', 'listing_type', 'status', 'subcategory_id', 'city_id', 'tenant_id', 'bedrooms', 'price_min', 'price_max', 'sort']),
        ]);
    }
}
