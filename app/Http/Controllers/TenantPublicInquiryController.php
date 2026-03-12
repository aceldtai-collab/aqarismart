<?php

namespace App\Http\Controllers;

use App\Models\AgentLead;
use App\Models\Unit;
use App\Services\Tenancy\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TenantPublicInquiryController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $tenant = app(TenantManager::class)->tenant();
        abort_if(! $tenant, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'message' => ['nullable', 'string', 'max:2000'],
            'unit_id' => ['nullable', 'integer', 'exists:units,id'],
        ]);

        $unit = null;
        if (! empty($data['unit_id'])) {
            $unit = Unit::where('tenant_id', $tenant->id)->find($data['unit_id']);
            if (! $unit) {
                $data['unit_id'] = null;
            }
        }

        $lead = AgentLead::create([
            'tenant_id' => $tenant->id,
            'agent_id' => $unit?->agent_id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? '',
            'source' => 'listing_inquiry',
            'status' => 'new',
            'notes' => $data['message'] ?? null,
        ]);

        return back()->with('status', __('Thanks! Your inquiry has been sent to our team.'));
    }
}
