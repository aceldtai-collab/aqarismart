<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MobileTenantResource;
use App\Http\Resources\MobileUnitResource;
use App\Models\Tenant;
use App\Models\Unit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileTenantDirectoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Tenant::query()
            ->whereHas('activeSubscription')
            ->with(['activeSubscription.package'])
            ->withCount([
                'users',
                'subscriptions',
            ]);

        if ($request->filled('q')) {
            $q = $request->string('q')->trim()->value();
            $query->where(function ($builder) use ($q) {
                $builder->where('name', 'like', "%{$q}%")
                    ->orWhere('slug', 'like', "%{$q}%")
                    ->orWhere('settings', 'like', "%{$q}%");
            });
        }

        $tenants = $query->orderBy('name')->paginate((int) $request->input('per_page', 12));

        return response()->json([
            'data' => MobileTenantResource::collection($tenants->getCollection()),
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

        $units = Unit::withoutGlobalScope('tenant')
            ->with(['property', 'subcategory.category', 'city', 'tenant'])
            ->where('tenant_id', $tenant->id)
            ->whereIn('status', [Unit::STATUS_VACANT, Unit::STATUS_OCCUPIED])
            ->when(in_array($listingType, Unit::LISTING_TYPES, true), fn ($q) => $q->where('listing_type', $listingType))
            ->latest()
            ->paginate((int) $request->input('per_page', 12));

        $featuredUnits = Unit::withoutGlobalScope('tenant')
            ->with(['property', 'subcategory.category', 'city', 'tenant'])
            ->where('tenant_id', $tenant->id)
            ->whereIn('status', [Unit::STATUS_VACANT, Unit::STATUS_OCCUPIED])
            ->latest()
            ->limit(6)
            ->get();

        return response()->json([
            'tenant' => new MobileTenantResource($tenant->load('activeSubscription.package')),
            'featured_units' => MobileUnitResource::collection($featuredUnits),
            'units' => MobileUnitResource::collection($units->getCollection()),
            'meta' => [
                'current_page' => $units->currentPage(),
                'last_page' => $units->lastPage(),
                'per_page' => $units->perPage(),
                'total' => $units->total(),
            ],
        ]);
    }
}
