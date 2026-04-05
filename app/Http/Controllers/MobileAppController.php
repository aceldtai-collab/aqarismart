<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\City;
use App\Models\Tenant;
use App\Models\Unit;
use App\Services\Search\SearchExperienceBuilder;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class MobileAppController extends Controller
{
    public function home(): View
    {
        return view('mobile.home');
    }

    public function login(): View
    {
        return view('mobile.auth.login');
    }

    public function register(): View
    {
        return view('mobile.auth.register');
    }

    public function marketplace(Request $request): View
    {
        $this->abortIfNotCentralDomain($request);

        return view('mobile.marketplace');
    }

    public function search(Request $request, SearchExperienceBuilder $searchExperienceBuilder): View
    {
        $this->abortIfNotCentralDomain($request);

        $query = Unit::withoutGlobalScope('tenant')
            ->with(['tenant', 'property.city', 'property.state', 'subcategory.category', 'city', 'area', 'unitAttributes.attributeField'])
            ->whereHas('tenant', fn ($tenantQuery) => $tenantQuery->whereHas('activeSubscription'))
            ->whereIn('status', [Unit::STATUS_VACANT, Unit::STATUS_OCCUPIED]);

        $this->applySearchFilters($query, $request, true);
        $query = $this->applySearchSort($query, (string) $request->input('sort', 'latest'));

        $units = (clone $query)->paginate(10)->withQueryString();
        $searchUnits = (clone $query)->limit(72)->get();

        return view('mobile.search.experience', [
            'context' => 'public',
            'units' => $units,
            'categories' => Category::where('is_active', true)->orderBy('sort_order')->with('subcategories')->get(),
            'cities' => City::where('is_active', true)->orderBy('name_en')->get(),
            'tenants' => Tenant::whereHas('activeSubscription')->orderBy('name')->get(),
            'filters' => $request->only(['q', 'listing_type', 'category_id', 'subcategory_id', 'city_id', 'tenant_id', 'bedrooms', 'price_min', 'price_max', 'sort']),
            'searchExperience' => $searchExperienceBuilder->build($searchUnits, [
                'scope' => 'mobile_public',
                'total' => $units->total(),
            ]),
            'searchAction' => route('mobile.search'),
            'clearUrl' => route('mobile.search'),
            'backUrl' => route('mobile.marketplace'),
        ]);
    }

    public function dashboard(): View
    {
        return view('mobile.dashboard');
    }

    public function units(): View
    {
        return view('mobile.units.index');
    }

    public function createUnit(): View
    {
        return view('mobile.units.create');
    }

    public function showUnit(Unit $unit): View
    {
        $unit->load([
            'property',
            'tenant',
            'subcategory.category',
            'city',
            'area',
            'subcategory',
            'agent',
            'officialInfo',
            'owner',
            'unitAttributes.attributeField',
        ]);

        return view('mobile.units.show', compact('unit'));
    }

    public function editUnit(Unit $unit): View
    {
        return view('mobile.units.edit', compact('unit'));
    }

    public function tenants(): View
    {
        return view('mobile.tenants.index');
    }

    public function showTenant(Tenant $tenant): View
    {
        return view('mobile.tenants.show', compact('tenant'));
    }

    public function tenantSearch(Tenant $tenant, Request $request, SearchExperienceBuilder $searchExperienceBuilder): View
    {
        abort_unless($tenant->activeSubscription()->exists(), 404);

        $query = Unit::withoutGlobalScope('tenant')
            ->with(['tenant', 'property.city', 'property.state', 'subcategory.category', 'city', 'area', 'unitAttributes.attributeField'])
            ->where('tenant_id', $tenant->id)
            ->whereIn('status', [Unit::STATUS_VACANT, Unit::STATUS_OCCUPIED]);

        $this->applySearchFilters($query, $request, false);
        $query = $this->applySearchSort($query, (string) $request->input('sort', 'latest'));

        $units = (clone $query)->paginate(10)->withQueryString();
        $searchUnits = (clone $query)->limit(72)->get();

        return view('mobile.search.experience', [
            'context' => 'tenant',
            'tenant' => $tenant,
            'units' => $units,
            'categories' => Category::where('is_active', true)->orderBy('sort_order')->with('subcategories')->get(),
            'cities' => City::where('is_active', true)->orderBy('name_en')->get(),
            'tenants' => collect(),
            'filters' => $request->only(['q', 'listing_type', 'category_id', 'subcategory_id', 'city_id', 'bedrooms', 'price_min', 'price_max', 'sort']),
            'searchExperience' => $searchExperienceBuilder->build($searchUnits, [
                'scope' => 'mobile_tenant',
                'tenant' => $tenant,
                'total' => $units->total(),
            ]),
            'searchAction' => route('mobile.tenants.search', $tenant),
            'clearUrl' => route('mobile.tenants.search', $tenant),
            'backUrl' => route('mobile.tenants.show', $tenant),
        ]);
    }

    public function profile(): View
    {
        return view('mobile.profile');
    }

    public function about(): View
    {
        return view('mobile.about');
    }

    private function abortIfNotCentralDomain(Request $request): void
    {
        abort_unless($this->isAllowedCentralHost($request), 404);
    }

    private function applySearchFilters($query, Request $request, bool $allowTenantFilter): void
    {
        if ($request->filled('q')) {
            $term = trim((string) $request->input('q'));
            $query->where(function ($builder) use ($term) {
                $builder->where('title', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%")
                    ->orWhere('code', 'like', "%{$term}%")
                    ->orWhere('location', 'like', "%{$term}%")
                    ->orWhereHas('property', fn ($propertyQuery) => $propertyQuery
                        ->where('name', 'like', "%{$term}%")
                        ->orWhere('address', 'like', "%{$term}%")
                        ->orWhere('city', 'like', "%{$term}%"));
            });
        }

        if ($request->filled('listing_type')) {
            $query->where('listing_type', $request->input('listing_type'));
        }

        if ($request->filled('subcategory_id')) {
            $query->where('subcategory_id', $request->input('subcategory_id'));
        }

        if ($request->filled('category_id')) {
            $query->whereHas('subcategory', fn ($subcategoryQuery) => $subcategoryQuery->where('category_id', $request->input('category_id')));
        }

        if ($request->filled('city_id')) {
            $query->where('city_id', $request->input('city_id'));
        }

        if ($allowTenantFilter && $request->filled('tenant_id')) {
            $query->where('tenant_id', $request->input('tenant_id'));
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
    }

    private function applySearchSort($query, string $sort)
    {
        return match ($sort) {
            'price_asc' => $query->orderByRaw("CASE WHEN listing_type = 'rent' THEN COALESCE(NULLIF(market_rent, 0), price) ELSE price END asc"),
            'price_desc' => $query->orderByRaw("CASE WHEN listing_type = 'rent' THEN COALESCE(NULLIF(market_rent, 0), price) ELSE price END desc"),
            'oldest' => $query->oldest(),
            default => $query->latest(),
        };
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
}
