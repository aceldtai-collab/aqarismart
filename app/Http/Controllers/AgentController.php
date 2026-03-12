<?php

namespace App\Http\Controllers;

use App\Http\Requests\AgentRequest;
use App\Models\Agent;
use App\Models\User;
use App\Services\Tenancy\MemberService;
use App\Services\Tenancy\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AgentController extends Controller
{
    public function __construct(
        protected TenantManager $tenants,
        protected MemberService $members
    ) {}
    public function index(): View
    {
        $this->authorize('viewAny', Agent::class);
        $agents = Agent::latest()->paginate(10);
        return view('agents.index', compact('agents'));
    }

    public function create(): View
    {
        $this->authorize('create', Agent::class);
        $tenants = [];
        if (! $this->tenants->tenant()) {
            $tenants = \App\Models\Tenant::orderBy('name')->pluck('name','id');
        }
        return view('agents.create', compact('tenants'));
    }

    public function store(AgentRequest $request): RedirectResponse
    {
        $this->authorize('create', Agent::class);
        $data = $request->validated();
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('agents/photos', 'public');
        }
        $data['commission_rate'] = round((float) ($data['commission_rate'] ?? 0), 2);
        $data['active'] = $request->boolean('active');
        // Ensure tenant_id is set
        if ($tenant = $this->tenants->tenant()) {
            $data['tenant_id'] = $tenant->getKey();
        } else {
            $validated = $request->validate([
                'tenant_id' => ['required','exists:tenants,id']
            ]);
            $data['tenant_id'] = (int) $validated['tenant_id'];
        }
        $agent = Agent::create($data);

        // Auto-invite login for this tenant using the agent contact email (if provided)
        $tenant = $this->tenants->tenant();
        if ($tenant && ! empty($data['email'])) {
            $plainName = is_array($data['name']) ? ($data['name']['en'] ?? (array_values($data['name'])[0] ?? '')) : $data['name'];
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                ['name' => $plainName, 'password' => bcrypt(str()->random(24)), 'email_verified_at' => now()]
            );

            // Attach as member by default. Agent-scoped users are limited by policies to their own data
            $this->members->attach($user, $tenant, 'member');
            // Scope this user to the created agent
            if (! isset($user->agent_id) || $user->agent_id !== $agent->id) {
                $user->agent_id = $agent->id;
                $user->save();
            }

            try {
                Password::sendResetLink(['email' => $user->email]);
                $msg = 'Agent created and login invite sent.';
            } catch (\Throwable $e) {
                $msg = 'Agent created. (Invite email failed in this environment)';
            }
        } else {
            $msg = 'Agent created';
        }

        $route = $this->tenants->tenant() ? 'agents.index' : 'admin.agents.index';

        return redirect()->route($route)->with('status', $msg);
    }

    public function edit(String $tenant, Agent $agent): View
    {
        $this->authorize('update', $agent);
        return view('agents.edit', compact('agent'));
    }

    public function update(AgentRequest $request, String $tenant, Agent $agent): RedirectResponse
    {
        $this->authorize('update', $agent);
        $data = $request->validated();
        if ($request->hasFile('photo')) {
            if ($agent->photo && ! Str::startsWith($agent->photo, ['http://', 'https://'])) {
                Storage::disk('public')->delete($agent->photo);
            }
            $data['photo'] = $request->file('photo')->store('agents/photos', 'public');
        }
        $data['commission_rate'] = round((float) ($data['commission_rate'] ?? 0), 2);
        $data['active'] = $request->boolean('active');
        if (! $this->tenants->tenant()) {
            $validated = $request->validate([
                'tenant_id' => ['required','exists:tenants,id']
            ]);
            $data['tenant_id'] = (int) $validated['tenant_id'];
        }
        $agent->update($data);
        $route = $this->tenants->tenant() ? 'agents.index' : 'admin.agents.index';

        return redirect()->route($route)->with('status', 'Agent updated');
    }

    public function destroy(String $tenant,Agent $agent): RedirectResponse
    {
        $this->authorize('delete', $agent);
        $agent->delete();
        return back()->with('status', 'Agent deleted');
    }
}
