<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MobileTenantResource;
use App\Http\Resources\MobileUserResource;
use App\Models\Lease;
use App\Models\Resident;
use App\Services\Dashboard\TenantDashboardMetrics;
use App\Services\Tenancy\TenantManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileDashboardController extends Controller
{
    public function __construct(
        protected TenantManager $tenants,
        protected TenantDashboardMetrics $metrics,
    ) {
    }

    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->loadMissing('tenants.activeSubscription.package');
        $tenant = $this->tenants->tenant() ?: $user->tenants()->first();
        abort_if(! $tenant, 404);

        $request->attributes->set('mobile_tenant', $tenant);
        $pivotRole = strtolower((string) ($user->tenants()->whereKey($tenant->getKey())->first()?->pivot?->role));
        $isResident = $pivotRole === 'resident' || (method_exists($user, 'hasRole') && $user->hasRole('resident'));

        if ($isResident) {
            $resident = Resident::query()
                ->where('tenant_id', $tenant->id)
                ->where(function ($query) use ($user) {
                    $query->where('email', $user->email)
                        ->orWhere('phone', $user->phone);
                })
                ->first();

            $leases = Lease::query()
                ->with(['unit.property'])
                ->whereHas('residents', fn ($query) => $query->where('residents.id', $resident?->id))
                ->latest()
                ->get();

            return response()->json([
                'role' => 'resident',
                'user' => new MobileUserResource($user),
                'tenant' => new MobileTenantResource($tenant),
                'resident' => $resident ? [
                    'id' => $resident->id,
                    'name' => $resident->name,
                    'email' => $resident->email,
                    'phone' => $resident->phone,
                ] : null,
                'leases' => $leases->map(fn ($lease) => [
                    'id' => $lease->id,
                    'status' => $lease->status,
                    'start_date' => optional($lease->start_date)?->toDateString(),
                    'end_date' => optional($lease->end_date)?->toDateString(),
                    'rent_cents' => $lease->rent_cents,
                    'unit' => [
                        'id' => $lease->unit?->id,
                        'code' => $lease->unit?->code,
                        'title' => $lease->unit?->translated_title,
                        'property_name' => $lease->unit?->property?->name,
                    ],
                ])->values(),
            ]);
        }

        $agentId = $user->agent_id ?: null;
        $metrics = $this->metrics->metrics($tenant, $agentId);

        return response()->json([
            'role' => 'staff',
            'user' => new MobileUserResource($user),
            'tenant' => new MobileTenantResource($tenant),
            'dashboard' => $metrics,
        ]);
    }
}
