<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyViewingRequest;
use App\Http\Controllers\Concerns\ResolvesAgentAssignments;
use App\Models\AgentLead;
use App\Models\Property;
use App\Models\PropertyViewing;
use App\Services\Tenancy\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class PropertyViewingController extends Controller
{
    use ResolvesAgentAssignments;

    public function __construct(protected TenantManager $tenants) {}

    public function index(): View
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('viewAny', PropertyViewing::class);

        $status = request('status');
        $query = PropertyViewing::with(['agent', 'property', 'lead'])
            ->where('tenant_id', $tenant->id)
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderByDesc('appointment_at');

        if ($agentId = auth()->user()?->agent_id) {
            $query->forAgent($agentId);
        }

        $viewings = $query->paginate(15)->withQueryString();

        return view('property-viewings.index', compact('viewings', 'status'));
    }

    public function create(): View
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('create', PropertyViewing::class);

        return view('property-viewings.create', [
            'agents' => $this->availableAgents(),
            'leads' => $this->availableLeads(),
            'properties' => $this->availableProperties(),
        ]);
    }

    public function store(PropertyViewingRequest $request): RedirectResponse
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('create', PropertyViewing::class);

        $data = $request->validated();
        $data['tenant_id'] = $tenant->id;

        PropertyViewing::create($data);

        return redirect()->route('property-viewings.index')->with('status', __('Viewing scheduled.'));
    }

    public function edit(string $tenant, PropertyViewing $propertyViewing): View
    {
        $tenantModel = $this->tenants->tenant();
        abort_if(! $tenantModel, 404);
        $this->authorize('update', $propertyViewing);

        return view('property-viewings.edit', [
            'viewing' => $propertyViewing,
            'agents' => $this->availableAgents(),
            'leads' => $this->availableLeads(),
            'properties' => $this->availableProperties(),
        ]);
    }

    public function update(PropertyViewingRequest $request, string $tenant, PropertyViewing $propertyViewing): RedirectResponse
    {
        $tenantModel = $this->tenants->tenant();
        abort_if(! $tenantModel, 404);
        $this->authorize('update', $propertyViewing);

        $data = $request->validated();
        $propertyViewing->update($data);

        return redirect()->route('property-viewings.index')->with('status', __('Viewing updated.'));
    }

    public function destroy(string $tenant, PropertyViewing $propertyViewing): RedirectResponse
    {
        $tenantModel = $this->tenants->tenant();
        abort_if(! $tenantModel, 404);
        $this->authorize('delete', $propertyViewing);

        $propertyViewing->delete();

        return redirect()->route('property-viewings.index')->with('status', __('Viewing deleted.'));
    }

    // availableAgents provided by ResolvesAgentAssignments.

    protected function availableLeads(): Collection
    {
        $tenant = $this->tenants->tenant();
        $agentId = auth()->user()?->agent_id;

        return AgentLead::query()
            ->where('tenant_id', $tenant?->id)
            ->when($agentId, fn ($q) => $q->forAgent($agentId))
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    protected function availableProperties(): Collection
    {
        $tenant = $this->tenants->tenant();
        $agentId = auth()->user()?->agent_id;

        return Property::query()
            ->where('tenant_id', $tenant?->id)
            ->orderBy('name')
            ->when($agentId, function ($q) use ($agentId) {
                $q->where(function ($inner) use ($agentId) {
                    $inner->where('agent_id', $agentId)
                        ->orWhereHas('agents', fn ($aq) => $aq->where('agents.id', $agentId));
                });
            })
            ->pluck('name', 'id');
    }
}
