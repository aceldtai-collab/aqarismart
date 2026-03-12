<?php

namespace App\Http\Controllers;

use App\Http\Requests\AgentLeadRequest;
use App\Http\Controllers\Concerns\ResolvesAgentAssignments;
use App\Models\AgentLead;
use App\Services\Tenancy\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AgentLeadController extends Controller
{
    use ResolvesAgentAssignments;

    public function __construct(protected TenantManager $tenants) {}

    public function index(): View
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('viewAny', AgentLead::class);

        $status = request('status');
        $query = AgentLead::with('agent')
            ->where('tenant_id', $tenant->id)
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderByDesc('created_at');

        if ($agentId = auth()->user()?->agent_id) {
            $query->forAgent($agentId);
        }

        $leads = $query->paginate(15)->withQueryString();

        return view('agent-leads.index', compact('leads', 'status'));
    }

    public function create(): View
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('create', AgentLead::class);
        $agents = $this->availableAgents();

        return view('agent-leads.create', compact('agents'));
    }

    public function store(AgentLeadRequest $request): RedirectResponse
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('create', AgentLead::class);

        $data = $request->validated();
        $data['tenant_id'] = $tenant->id;

        AgentLead::create($data);

        return redirect()->route('agent-leads.index')->with('status', __('Lead created.'));
    }

    public function edit(string $tenant, AgentLead $agentLead): View
    {
        $tenantModel = $this->tenants->tenant();
        abort_if(! $tenantModel, 404);
        $this->authorize('update', $agentLead);
        $agents = $this->availableAgents();

        return view('agent-leads.edit', ['lead' => $agentLead, 'agents' => $agents]);
    }

    public function update(AgentLeadRequest $request, string $tenant, AgentLead $agentLead): RedirectResponse
    {
        $tenantModel = $this->tenants->tenant();
        abort_if(! $tenantModel, 404);
        $this->authorize('update', $agentLead);

        $data = $request->validated();
        $agentLead->update($data);

        return redirect()->route('agent-leads.index')->with('status', __('Lead updated.'));
    }

    public function destroy(string $tenant, AgentLead $agentLead): RedirectResponse
    {
        $tenantModel = $this->tenants->tenant();
        abort_if(! $tenantModel, 404);
        $this->authorize('delete', $agentLead);

        $agentLead->delete();

        return redirect()->route('agent-leads.index')->with('status', __('Lead deleted.'));
    }

    // availableAgents provided by ResolvesAgentAssignments.
}
