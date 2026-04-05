<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MobileTenantResource;
use App\Models\Agent;
use App\Http\Resources\MobileUnitResource;
use App\Models\Tenant;
use App\Models\Unit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileTenantDirectoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $baseQuery = Tenant::query()
            ->whereHas('activeSubscription')
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->string('q')->trim()->value();

                $query->where(function ($builder) use ($q) {
                    $builder->where('name', 'like', "%{$q}%")
                        ->orWhere('slug', 'like', "%{$q}%")
                        ->orWhere('settings', 'like', "%{$q}%");
                });
            });

        $query = (clone $baseQuery)
            ->with(['activeSubscription.package'])
            ->withCount([
                'units',
                'units as active_units_count' => fn ($units) => $units->where('status', Unit::STATUS_VACANT),
                'agents',
            ]);

        $tenants = $query
            ->orderByDesc('active_units_count')
            ->orderByDesc('units_count')
            ->orderBy('name')
            ->paginate((int) $request->input('per_page', 12));

        $tenantIds = (clone $baseQuery)->select('tenants.id');

        return response()->json([
            'data' => MobileTenantResource::collection($tenants->getCollection()),
            'summary' => [
                'agencies_count' => (clone $baseQuery)->count(),
                'active_units_count' => Unit::withoutGlobalScope('tenant')
                    ->whereIn('tenant_id', $tenantIds)
                    ->where('status', Unit::STATUS_VACANT)
                    ->count(),
                'agents_count' => Agent::withoutGlobalScope('tenant')
                    ->whereIn('tenant_id', $tenantIds)
                    ->count(),
            ],
            'meta' => [
                'current_page' => $tenants->currentPage(),
                'last_page' => $tenants->lastPage(),
                'per_page' => $tenants->perPage(),
                'total' => $tenants->total(),
            ],
        ]);
    }

    public function show(Tenant $tenant): JsonResponse
    {
        abort_unless($tenant->activeSubscription()->exists(), 404);

        $tenant->load('activeSubscription.package');
        $tenant->active_units_count = Unit::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenant->id)
            ->where('status', Unit::STATUS_VACANT)
            ->count();

        return response()->json([
            'data' => new MobileTenantResource($tenant),
        ]);
    }

    public function home(Tenant $tenant, Request $request): JsonResponse
    {
        abort_unless($tenant->activeSubscription()->exists(), 404);

        $listingType = $request->input('listing_type', Unit::LISTING_RENT);
        $search = $request->filled('q') ? trim($request->string('q')->value()) : '';
        $summaryQuery = Unit::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenant->id)
            ->whereIn('status', [Unit::STATUS_VACANT, Unit::STATUS_OCCUPIED]);

        $baseQuery = Unit::withoutGlobalScope('tenant')
            ->with(['property.city', 'property.state', 'subcategory.category', 'city', 'area', 'tenant', 'unitAttributes.attributeField'])
            ->where('tenant_id', $tenant->id)
            ->whereIn('status', [Unit::STATUS_VACANT, Unit::STATUS_OCCUPIED])
            ->when(in_array($listingType, Unit::LISTING_TYPES, true), fn ($q) => $q->where('listing_type', $listingType))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%")
                        ->orWhereHas('property', fn ($p) => $p
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('address', 'like', "%{$search}%")
                            ->orWhere('city', 'like', "%{$search}%"));
                });
            });

        $units = (clone $baseQuery)->latest()
            ->paginate((int) $request->input('per_page', 12));

        $featuredUnits = Unit::withoutGlobalScope('tenant')
            ->with(['property.city', 'property.state', 'subcategory.category', 'city', 'area', 'tenant', 'unitAttributes.attributeField'])
            ->where('tenant_id', $tenant->id)
            ->whereIn('status', [Unit::STATUS_VACANT, Unit::STATUS_OCCUPIED])
            ->latest()
            ->limit(6)
            ->get();

        return response()->json([
            'tenant' => new MobileTenantResource($tenant->load('activeSubscription.package')),
            'featured_units' => MobileUnitResource::collection($featuredUnits),
            'units' => MobileUnitResource::collection($units->getCollection()),
            'summary' => [
                'total_active' => (clone $summaryQuery)->count(),
                'rent_count' => (clone $summaryQuery)->where('listing_type', Unit::LISTING_RENT)->count(),
                'sale_count' => (clone $summaryQuery)->where('listing_type', Unit::LISTING_SALE)->count(),
            ],
            'meta' => [
                'current_page' => $units->currentPage(),
                'last_page' => $units->lastPage(),
                'per_page' => $units->perPage(),
                'total' => $units->total(),
            ],
        ]);
    }
}
