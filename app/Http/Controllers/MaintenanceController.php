<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Models\Property;
use App\Models\Unit;
use App\Models\Resident;
use App\Services\Tenancy\TenantManager;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreMaintenanceRequest;
use Illuminate\View\View;

class MaintenanceController extends Controller
{
    public function __construct(protected TenantManager $tenants) {}

    public function index(): View
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('viewAny', MaintenanceRequest::class);

        $status = trim((string) request()->query('status', ''));
        $since = (int) request()->query('since', 0);
        $sinceDate = $since > 0 ? now()->subDays($since) : null;

        $query = MaintenanceRequest::with(['property.agent','unit.agent','resident'])
            ->when($status !== '', function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($sinceDate, function ($q) use ($sinceDate) {
                $q->where('created_at', '>=', $sinceDate);
            })
            ->orderBy('created_at','desc');
        if ($cid = auth()->user()?->agent_id) {
            $query->where(function($q) use ($cid) {
                $q->whereHas('property', fn($p) => $p->where('agent_id', $cid))
                  ->orWhereHas('unit', fn($u) => $u->where('agent_id', $cid));
            });
        }
        $requests = $query->paginate(15)->withQueryString();
        return view('maintenance.index', compact('requests') + [
            'status' => $status,
            'since' => $since,
        ]);
    }

    public function create(): View
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('create', MaintenanceRequest::class);

        $properties = Property::orderBy('name');
        $units = Unit::orderBy('code');
        if ($cid = auth()->user()?->agent_id) {
            $properties->where('agent_id', $cid);
            $units->where('agent_id', $cid);
        }
        $properties = $properties->get();
        $units = $units->get();
        $residents = Resident::orderBy('last_name')->orderBy('first_name')->get();
        return view('maintenance.create', compact('properties','units','residents'));
    }

    public function store(StoreMaintenanceRequest $request): RedirectResponse
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('create', MaintenanceRequest::class);

        $data = $request->validated();

        $unit = null;
        if (! empty($data['unit_id'])) {
            $unit = Unit::findOrFail($data['unit_id']);
        }
        $property = null;
        if (! empty($data['property_id'])) {
            $property = Property::findOrFail($data['property_id']);
        } elseif ($unit && $unit->property_id) {
            $property = $unit->property;
        }
        if ($cid = auth()->user()?->agent_id) {
            if ($property && $property->agent_id !== $cid) {
                abort(403);
            }
            if (! $property && $unit && $unit->agent_id !== $cid) {
                abort(403);
            }
        }
        if (! empty($data['resident_id'])) { Resident::findOrFail($data['resident_id']); }

        MaintenanceRequest::create([
            'tenant_id' => $tenant->id,
            'property_id' => $property?->id,
            'unit_id' => $unit?->id,
            'resident_id' => $data['resident_id'] ?? null,
            'title' => $data['title'],
            'details' => $data['details'] ?? null,
            'priority' => $data['priority'] ?? 'normal',
            'status' => 'new',
        ]);

        return redirect()->route('maintenance.index')->with('status', 'Request created');
    }

    public function edit(String $tenant, MaintenanceRequest $maintenanceRequest): View
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('update', $maintenanceRequest);
        $properties = Property::orderBy('name');
        $units = Unit::orderBy('code');
        if ($cid = auth()->user()?->agent_id) {
            $properties->where('agent_id', $cid);
            $units->where('agent_id', $cid);
        }
        $properties = $properties->get();
        $units = $units->get();
        $residents = Resident::orderBy('last_name')->orderBy('first_name')->get();
        return view('maintenance.edit', [
            'request' => $maintenanceRequest,
            'properties' => $properties,
            'units' => $units,
            'residents' => $residents,
        ]);
    }

    public function update(StoreMaintenanceRequest $request,String $tenant, MaintenanceRequest $maintenanceRequest): RedirectResponse
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('update', $maintenanceRequest);
        $data = $request->validated();
        $unit = null;
        if (! empty($data['unit_id'])) {
            $unit = Unit::findOrFail($data['unit_id']);
        }
        $property = null;
        if (! empty($data['property_id'])) {
            $property = Property::findOrFail($data['property_id']);
        } elseif ($unit && $unit->property_id) {
            $property = $unit->property;
        }
        if ($cid = auth()->user()?->agent_id) {
            if ($property && $property->agent_id !== $cid) {
                abort(403);
            }
            if (! $property && $unit && $unit->agent_id !== $cid) {
                abort(403);
            }
        }
        if (! empty($data['resident_id'])) { Resident::findOrFail($data['resident_id']); }
        $maintenanceRequest->update([
            'property_id' => $property?->id,
            'unit_id' => $unit?->id,
            'resident_id' => $data['resident_id'] ?? null,
            'title' => $data['title'],
            'details' => $data['details'] ?? null,
            'priority' => $data['priority'] ?? 'normal',
        ]);
        return redirect()->route('maintenance.index')->with('status', 'Request updated');
    }

    public function destroy(MaintenanceRequest $maintenanceRequest): RedirectResponse
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('delete', $maintenanceRequest);
        $maintenanceRequest->delete();
        return redirect()->route('maintenance.index')->with('status', 'Request removed');
    }
}
