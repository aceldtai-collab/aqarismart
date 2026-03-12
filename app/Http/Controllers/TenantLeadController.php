<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Unit;
use App\Services\Tenancy\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TenantLeadSubmitted;

class TenantLeadController extends Controller
{
    public function __construct(protected TenantManager $tenants) {}

    public function store(Request $request): RedirectResponse
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);

        $data = $request->validate([
            'unit_id' => ['nullable','integer','exists:units,id'],
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255'],
            'phone' => ['nullable','string','max:50'],
            'message' => ['nullable','string','max:2000'],
        ]);

        if (! empty($data['unit_id'])) {
            Unit::findOrFail($data['unit_id']); // tenant scope ensures ownership
        }

        $lead = Lead::create([
            'tenant_id' => $tenant->id,
            'unit_id' => $data['unit_id'] ?? null,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'message' => $data['message'] ?? null,
        ]);

        // Notify the first owner (fallback to any tenant user if not found)
        try {
            $owner = $tenant->users()->wherePivot('role','owner')->orderBy('users.id')->first() ?: $tenant->users()->orderBy('users.id')->first();
            if ($owner && config('mail.default')) {
                Mail::to($owner->email)->send(new TenantLeadSubmitted($lead));
            }
        } catch (\Throwable $e) {
            // swallow mail errors in local/dev
        }

        return back()->with('status', 'Thanks! We will contact you shortly.');
    }
}
