<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\City;
use App\Models\Tenant;
use App\Models\Unit;
use App\Services\PublicLandingService;
use App\Services\Search\SearchExperienceBuilder;
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
        // Only pass server data needed for filter dropdowns and SEO shell.
        // Dynamic sections (featured, sale, rent, cities, agencies, stats)
        // are fetched client-side from /api/mobile/marketplace (single source of truth).
        try {
            $categories = Category::where('is_active', true)->orderBy('sort_order')->with('subcategories')->get();
            $cities = City::where('is_active', true)->orderBy('name_en')->get();
        } catch (\Illuminate\Database\QueryException $e) {
            $categories = collect();
            $cities = collect();
        }

        return view('public.marketplace', [
            'landing' => $landing->forPublicDomain(),
            'categories' => $categories,
            'cities' => $cities,
            'filters' => $request->only(['q', 'listing_type', 'subcategory_id', 'city_id', 'tenant_id', 'bedrooms', 'price_min', 'price_max', 'sort']),
        ]);
    }

    public function search(Request $request, PublicLandingService $landing, SearchExperienceBuilder $searchExperienceBuilder): View
    {
        $query = Unit::withoutGlobalScope('tenant')
            ->with(['tenant', 'property.city', 'property.state', 'subcategory.category', 'city', 'area', 'unitAttributes.attributeField'])
            ->whereHas('tenant', fn($q) => $q->whereHas('activeSubscription'))
            ->whereIn('status', [Unit::STATUS_VACANT, Unit::STATUS_OCCUPIED]);

        // Search keyword
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($builder) use ($q) {
                $builder->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%")
                    ->orWhere('location', 'like', "%{$q}%")
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

        // Sort
        $sort = $request->input('sort', 'latest');
        $query = match ($sort) {
            'price_asc' => $query->orderByRaw("CASE WHEN listing_type = 'rent' THEN COALESCE(NULLIF(market_rent, 0), price) ELSE price END asc"),
            'price_desc' => $query->orderByRaw("CASE WHEN listing_type = 'rent' THEN COALESCE(NULLIF(market_rent, 0), price) ELSE price END desc"),
            'oldest' => $query->oldest(),
            default => $query->latest(),
        };

        $units = $query->paginate(12)->withQueryString();
        $searchUnits = (clone $query)->limit(72)->get();

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
            'searchExperience' => $searchExperienceBuilder->build($searchUnits, [
                'scope' => 'public',
                'total' => $units->total(),
            ]),
        ]);
    }
}
