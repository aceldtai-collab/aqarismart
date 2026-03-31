<?php

namespace App\Http\Resources;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MobileUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $tenant = $request->attributes->get('mobile_tenant');
        if (! $tenant instanceof Tenant && method_exists($request, 'user') && $request->user()) {
            $tenant = $request->user()->tenants()->first();
        }

        $pivotRole = $tenant ? strtolower((string) $this->tenants()->whereKey($tenant->getKey())->first()?->pivot?->role) : null;
        $roleNames = method_exists($this->resource, 'getRoleNames') ? $this->getRoleNames()->values()->all() : [];

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'phone_country_code' => $this->phone_country_code,
            'email_verified_at' => optional($this->email_verified_at)?->toISOString(),
            'agent_id' => $this->agent_id,
            'tenant_role' => $pivotRole,
            'roles' => $roleNames,
            'is_resident' => $pivotRole === 'resident' || in_array('resident', $roleNames, true),
            'is_staff' => $pivotRole !== null && $pivotRole !== 'resident',
            'tenants' => MobileTenantResource::collection($this->whenLoaded('tenants')),
        ];
    }
}
