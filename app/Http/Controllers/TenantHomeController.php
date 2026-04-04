<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Subcategory;
use App\Services\Tenancy\TenantManager;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class TenantHomeController extends Controller
{
    public function __construct(protected TenantManager $tenants) {}

    public function index(Request $request): View|RedirectResponse
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);

        $beds = (int) $request->query('beds', 0);
        $baths = (float) $request->query('baths', 0);
        $max = (int) $request->query('max', 0); // in USD per month
        $category = (int) $request->query('category', 0);
        $subcategory = (int) $request->query('subcategory', 0);
        $q = trim((string) $request->query('q', ''));
        $rawListingType = trim((string) $request->query('listing_type', ''));
        $listing_type = in_array($rawListingType, Unit::LISTING_TYPES, true) ? $rawListingType : Unit::LISTING_RENT;
        $hasListingTypeFilter = $rawListingType !== '';

        // If any filters/search are applied on home, redirect to the dedicated search page
        $hasFilters = ($beds > 0) || ($baths > 0) || ($max > 0) || ($category > 0) || ($subcategory > 0) || ($q !== '') || $hasListingTypeFilter;
        if ($hasFilters) {
            return redirect()->route('tenant.search', $request->query());
        }

        $publicUnitsBase = Unit::with(['property','subcategory.category','agents','agent'])
            ->whereIn('status', ['vacant', 'occupied']);

        $unitsQuery = (clone $publicUnitsBase)
            ->where('listing_type', $listing_type)
            ->when($beds > 0, fn ($q2) => $q2->where('beds', '>=', $beds))
            ->when($baths > 0, fn ($q2) => $q2->where('baths', '>=', $baths))
            ->when($max > 0, fn ($q2) => $q2->where('market_rent', '<=', $max * 100))
            ->when($subcategory > 0, fn ($q2) => $q2->where('subcategory_id', $subcategory))
            ->when($category > 0, function ($q2) use ($category) {
                $q2->where(function ($inner) use ($category) {
                    $inner->whereHas('property', fn($qq) => $qq->where('category_id', $category))
                        ->orWhereHas('subcategory.category', fn($qq) => $qq->where('id', $category));
                });
            })
            ->when($q !== '', function ($q2) use ($q) {
                $q2->where(function ($inner) use ($q) {
                    $inner->whereHas('property', function ($qq) use ($q) {
                        $qq->where('name', 'like', "%$q%");
                    })->orWhere('code', 'like', "%$q%")
                      ->orWhere('title', 'like', "%$q%");
                });
            });

        $units = (clone $publicUnitsBase)
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        $spotlightUnits = (clone $publicUnitsBase)
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        $typeCounts = (clone $publicUnitsBase)
            ->whereNotNull('subcategory_id')
            ->selectRaw('subcategory_id, COUNT(*) as units_count')
            ->groupBy('subcategory_id')
            ->pluck('units_count', 'subcategory_id');

        $stats = [
            'total' => (clone $publicUnitsBase)->count(),
            'sale' => (clone $publicUnitsBase)->where('listing_type', Unit::LISTING_SALE)->count(),
            'rent' => (clone $publicUnitsBase)->where('listing_type', Unit::LISTING_RENT)->count(),
            'types' => (clone $publicUnitsBase)->whereNotNull('subcategory_id')->distinct('subcategory_id')->count('subcategory_id'),
        ];

        // Build list of subcategories that have available units under current filters (excluding subcategory filter)
        $availableSubcategoryIds = (clone $unitsQuery)
            ->when($subcategory > 0, fn($q3) => $q3) // ignore current subcategory filter to show options
            ->whereNotNull('subcategory_id')
            ->distinct()
            ->pluck('subcategory_id');

        $categories = \App\Models\Category::with('subcategories')->orderBy('name')->get();
        $types = Subcategory::whereIn('id', $availableSubcategoryIds)
            ->orderBy('name')
            ->get()
            ->map(function (Subcategory $subcategory) use ($typeCounts) {
                $subcategory->setAttribute('units_count', (int) ($typeCounts[$subcategory->id] ?? 0));
                return $subcategory;
            });

        $popularCities = (clone $publicUnitsBase)
            ->orderByDesc('id')
            ->get()
            ->groupBy(function (Unit $unit) {
                $location = trim((string) $unit->location);
                $prefix = trim(Str::before($location, '-'));
                return $prefix !== '' ? $prefix : __('Various areas');
            })
            ->map(function ($cityUnits, $cityName) use ($tenant) {
                $firstUnit = $cityUnits->first();
                $photo = null;

                if (is_array($firstUnit?->photos) && filled($firstUnit->photos[0] ?? null)) {
                    $rawPhoto = $firstUnit->photos[0];
                    $photo = Str::startsWith($rawPhoto, ['http://', 'https://'])
                        ? $rawPhoto
                        : url('/') . '/' . (Str::startsWith($rawPhoto, 'storage/') ? $rawPhoto : 'storage/' . ltrim($rawPhoto, '/'));
                }

                return [
                    'name' => $cityName,
                    'count' => $cityUnits->count(),
                    'image' => $photo,
                    'link' => route('tenant.search', ['tenant_slug' => $tenant->slug, 'q' => $cityName]),
                ];
            })
            ->take(6)
            ->values();

        $mapUnits = (clone $publicUnitsBase)
            ->orderByDesc('id')
            ->limit(12)
            ->get();
        
        return view('tenant.home', compact(
            'tenant',
            'units',
            'spotlightUnits',
            'stats',
            'mapUnits',
            'beds',
            'baths',
            'max',
            'category',
            'subcategory',
            'q',
            'categories',
            'types',
            'popularCities',
            'listing_type'
        ));
    }

    public function search(Request $request): View
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);

        $beds = (int) $request->query('beds', 0);
        $baths = (float) $request->query('baths', 0);
        $max = (int) $request->query('max', 0); // in USD per month
        $category = (int) $request->query('category', 0);
        $subcategory = (int) $request->query('subcategory', 0);
        $q = trim((string) $request->query('q', ''));
        $rawListingType = trim((string) $request->query('listing_type', ''));
        $listing_type = in_array($rawListingType, Unit::LISTING_TYPES, true) ? $rawListingType : Unit::LISTING_RENT;

        $unitsQuery = Unit::with(['property','subcategory.category','agents','agent'])
            ->whereIn('status', ['vacant', 'occupied'])
            ->where('listing_type', $listing_type)
            ->when($beds > 0, fn ($q2) => $q2->where('beds', '>=', $beds))
            ->when($baths > 0, fn ($q2) => $q2->where('baths', '>=', $baths))
            ->when($max > 0, fn ($q2) => $q2->where('market_rent', '<=', $max * 100))
            ->when($subcategory > 0, fn ($q2) => $q2->where('subcategory_id', $subcategory))
            ->when($category > 0, function ($q2) use ($category) {
                $q2->where(function ($inner) use ($category) {
                    $inner->whereHas('property', fn($qq) => $qq->where('category_id', $category))
                        ->orWhereHas('subcategory.category', fn($qq) => $qq->where('id', $category));
                });
            })
            ->when($q !== '', function ($q2) use ($q) {
                $q2->where(function ($inner) use ($q) {
                    $inner->whereHas('property', function ($qq) use ($q) {
                        $qq->where('name', 'like', "%$q%");
                    })->orWhere('code', 'like', "%$q%")
                      ->orWhere('title', 'like', "%$q%");
                });
            });

        $units = (clone $unitsQuery)
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        $categories = \App\Models\Category::orderBy('name')->get();
        $subcategories = \App\Models\Subcategory::when($category > 0, fn($q3) => $q3->where('category_id', $category))->orderBy('name')->get();
        return view('tenant.search', compact('tenant', 'units', 'beds', 'baths', 'max', 'category', 'subcategory', 'q', 'listing_type') + [
            'subcategories' => $subcategories,
        ]);
    }

    public function mobileDashboard(Request $request): View
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);

        $user = auth()->user();
        $agentScoped = (bool) $user?->agent_id;
        $requestedAgent = $agentScoped ? $user->agent_id : $request->integer('agent_id');

        if (! $agentScoped && $requestedAgent) {
            $exists = \App\Models\Agent::where('tenant_id', $tenant->id)->where('id', $requestedAgent)->exists();
            if (! $exists) {
                $requestedAgent = null;
            }
        }

        $agentId = $requestedAgent ?? $user?->agent_id;
        $days = $request->integer('days', 14);
        $days = $days > 0 ? min(max($days, 7), 90) : 14;

        $metrics = app(\App\Services\Dashboard\TenantDashboardMetrics::class)->metrics($tenant, $agentId);
        $snapshots = app(\App\Services\Reports\TenantSnapshotQuery::class);
        $availableAgents = $agentScoped
            ? collect()
            : \App\Models\Agent::where('tenant_id', $tenant->id)->orderBy('name')->get(['id', 'name']);
        $recentAlerts = \App\Models\ReportAlert::where('tenant_id', $tenant->id)
            ->orderByDesc('snapshot_date')
            ->latest()
            ->limit(5)
            ->get();

        return view('mobile.tenant-home', $metrics + [
            'tenant' => $tenant,
            'agentScoped' => $agentScoped,
            'filterAgent' => $agentId,
            'filterDays' => $days,
            'availableAgents' => $availableAgents,
            'alerts' => $recentAlerts,
            'pipelineTrend' => $snapshots->agentPipelineTrend($tenant, $agentId, $days),
            'occupancyTrend' => $snapshots->occupancyTrend($tenant, null, $days),
            'commissionTrend' => $snapshots->commissionTrend($tenant, $agentId, $days),
            'maintenanceTrend' => $snapshots->maintenanceTrend($tenant, $days),
        ]);
    }

    public function tenantHome(Request $request): View
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);

        return view('mobile.tenant-home', compact('tenant'));
    }
}
