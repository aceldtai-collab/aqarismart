<?php

namespace App\Http\Controllers;

use App\Http\Requests\AgentCommissionRequest;
use App\Http\Controllers\Concerns\ResolvesAgentAssignments;
use App\Models\AgentCommission;
use App\Models\Lease;
use App\Services\Tenancy\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class AgentCommissionController extends Controller
{
    use ResolvesAgentAssignments;

    public function __construct(protected TenantManager $tenants) {}

    public function index(): View
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('viewAny', AgentCommission::class);

        $status = request('status');
        $query = AgentCommission::with(['agent', 'lease'])
            ->where('tenant_id', $tenant->id)
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderByDesc('created_at');

        if ($agentId = auth()->user()?->agent_id) {
            $query->forAgent($agentId);
        }

        $commissions = $query->paginate(15)->withQueryString();

        return view('agent-commissions.index', compact('commissions', 'status'));
    }

    public function create(): View
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('create', AgentCommission::class);

        return view('agent-commissions.create', [
            'agents' => $this->availableAgents(),
            'leases' => $this->availableLeases(),
        ]);
    }

    public function store(AgentCommissionRequest $request): RedirectResponse
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('create', AgentCommission::class);

        $data = $request->validated();
        $data['tenant_id'] = $tenant->id;

        AgentCommission::create($data);

        return redirect()->route('agent-commissions.index')->with('status', __('Commission recorded.'));
    }

    public function edit(string $tenant, AgentCommission $commission): View
    {
        $tenantModel = $this->tenants->tenant();
        abort_if(! $tenantModel, 404);
        $this->authorize('update', $commission);

        return view('agent-commissions.edit', [
            'commission' => $commission,
            'agents' => $this->availableAgents(),
            'leases' => $this->availableLeases(),
        ]);
    }

    public function update(AgentCommissionRequest $request, string $tenant, AgentCommission $commission): RedirectResponse
    {
        $tenantModel = $this->tenants->tenant();
        abort_if(! $tenantModel, 404);
        $this->authorize('update', $commission);

        $data = $request->validated();
        $commission->update($data);

        return redirect()->route('agent-commissions.index')->with('status', __('Commission updated.'));
    }

    public function destroy(string $tenant, AgentCommission $commission): RedirectResponse
    {
        $tenantModel = $this->tenants->tenant();
        abort_if(! $tenantModel, 404);
        $this->authorize('delete', $commission);

        $commission->delete();

        return redirect()->route('agent-commissions.index')->with('status', __('Commission deleted.'));
    }

    // availableAgents provided by ResolvesAgentAssignments.

    protected function availableLeases(): Collection
    {
        $tenant = $this->tenants->tenant();
        $agentId = auth()->user()?->agent_id;

        $leases = Lease::query()
            ->where('tenant_id', $tenant?->id)
            ->when($agentId, function ($q) use ($agentId) {
                $q->where(function ($inner) use ($agentId) {
                    $inner->where('agent_id', $agentId)
                        ->orWhereHas('property', fn ($p) => $p->where('agent_id', $agentId))
                        ->orWhereHas('unit', fn ($u) => $u->where('agent_id', $agentId));
                });
            })
            ->orderByDesc('start_date')
            ->with(['property', 'unit'])
            ->get();

        return $leases->mapWithKeys(function ($lease) {
            $label = $lease->property?->name;
            if ($lease->unit) {
                $label .= ' / '.$lease->unit->code;
            }
            $label .= ' - '.$lease->start_date?->format('Y-m-d');
            return [$lease->id => $label];
        });
    }
}
