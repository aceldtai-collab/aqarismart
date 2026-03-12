<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeaseRequest;
use App\Models\Lease;
use App\Models\Property;
use App\Models\Resident;
use App\Models\Unit;
use App\Services\Tenancy\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LeaseController extends Controller
{
    public function __construct(protected TenantManager $tenants) {}

    public function index(): View
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('viewAny', Lease::class);

        $query = Lease::with(['property.agent','unit.agent'])->orderByDesc('start_date');
        if ($cid = auth()->user()?->agent_id) {
            $query->where(function($q) use ($cid) {
                $q->whereHas('property', fn($p) => $p->where('agent_id', $cid))
                  ->orWhereHas('unit', fn($u) => $u->where('agent_id', $cid));
            });
        }
        $leases = $query->paginate(15);
        return view('leases.index', compact('leases'));
    }

    public function create(): View
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('create', Lease::class);

        $properties = Property::orderBy('name');
        $units = Unit::orderBy('code');
        if ($cid = auth()->user()?->agent_id) {
            $properties->where('agent_id', $cid);
            $units->where('agent_id', $cid);
        }
        $properties = $properties->get();
        $units = $units->get();
        $residents = Resident::orderBy('last_name')->orderBy('first_name')->get();
        return view('leases.create', compact('properties','units','residents'));
    }

    public function store(StoreLeaseRequest $request): RedirectResponse
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('create', Lease::class);

        $data = $request->validated();

        $unit = Unit::findOrFail($data['unit_id']);
        $property = null;
        if (! empty($data['property_id'])) {
            $property = Property::findOrFail($data['property_id']);
        } elseif ($unit->property_id) {
            $property = $unit->property;
        }
        if ($cid = auth()->user()?->agent_id) {
            if ($property && $property->agent_id !== $cid) {
                abort(403);
            }
            abort_if($unit->agent_id !== $cid, 403);
        }

        $lease = Lease::create([
            'tenant_id' => $tenant->id,
            'property_id' => $property?->id,
            'unit_id' => $unit->id,
            'agent_id' => $unit->agent_id ?? $property?->agent_id,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? null,
            'rent_cents' => $data['rent_cents'],
            'deposit_cents' => $data['deposit_cents'] ?? 0,
            'frequency' => 'monthly',
            'status' => 'active',
        ]);

        $residentIds = Resident::whereIn('id', $data['resident_ids'])->pluck('id')->all();
        if ($residentIds) {
            $attach = [];
            foreach ($residentIds as $rid) { $attach[$rid] = ['role' => 'occupant']; }
            $attach[$residentIds[0]] = ['role' => 'primary'];
            $lease->residents()->sync($attach);
        }

        return redirect()->route('leases.index')->with('status', 'Lease created');
    }

    public function edit(String $tenant,Lease $lease): View
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('update', $lease);
        $properties = Property::orderBy('name');
        $units = Unit::orderBy('code');
        if ($cid = auth()->user()?->agent_id) {
            $properties->where('agent_id', $cid);
            $units->where('agent_id', $cid);
        }
        $properties = $properties->get();
        $units = $units->get();
        $residents = Resident::orderBy('last_name')->orderBy('first_name')->get();
        $selectedResidents = $lease->residents()->pluck('residents.id')->all();
        return view('leases.edit', compact('lease','properties','units','residents','selectedResidents'));
    }

    public function update(StoreLeaseRequest $request,String $tenant, Lease $lease): RedirectResponse
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('update', $lease);
        $data = $request->validated();
        $unit = Unit::findOrFail($data['unit_id']);
        $property = null;
        if (! empty($data['property_id'])) {
            $property = Property::findOrFail($data['property_id']);
        } elseif ($unit->property_id) {
            $property = $unit->property;
        }
        if ($cid = auth()->user()?->agent_id) {
            if ($property && $property->agent_id !== $cid) {
                abort(403);
            }
            abort_if($unit->agent_id !== $cid, 403);
        }
        $lease->update([
            'property_id' => $property?->id,
            'unit_id' => $unit->id,
            'agent_id' => $unit->agent_id ?? $property?->agent_id,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? null,
            'rent_cents' => $data['rent_cents'],
            'deposit_cents' => $data['deposit_cents'] ?? 0,
        ]);
        $residentIds = Resident::whereIn('id', $data['resident_ids'])->pluck('id')->all();
        $attach = [];
        foreach ($residentIds as $rid) { $attach[$rid] = ['role' => 'occupant']; }
        if ($residentIds) { $attach[$residentIds[0]] = ['role' => 'primary']; }
        $lease->residents()->sync($attach);
        return redirect()->route('leases.index')->with('status', 'Lease updated');
    }

    public function destroy(Lease $lease): RedirectResponse
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('delete', $lease);
        $lease->delete();
        return redirect()->route('leases.index')->with('status', 'Lease removed');
    }
}
