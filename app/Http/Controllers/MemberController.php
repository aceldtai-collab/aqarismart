<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use App\Services\Tenancy\MemberService;
use App\Services\Tenancy\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Password;
use Spatie\Permission\Models\Role;

class MemberController extends Controller
{
    public function __construct(
        protected TenantManager $tenants,
        protected MemberService $members
    ) {}

    public function index(): View
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $isAgentUser = (bool) (auth()->user()?->agent_id);
        if (! $isAgentUser) {
            Gate::authorize('view-members', $tenant);
        }

        $users = $tenant->users()->when($isAgentUser, function($q) {
            $cid = auth()->user()?->agent_id;
            $q->where('users.agent_id', $cid);
        })->orderBy('name')->get();

        // Helper to get role via Spatie or pivot
        if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);
        }
        $roles = $users->mapWithKeys(function ($u) use ($tenant) {
            $role = null;
            if (class_exists(\Spatie\Permission\PermissionRegistrar::class) && method_exists($u, 'getRoleNames')) {
                $spatieRoles = $u->getRoleNames();
                $role = $spatieRoles->first();
            }
            // Fall back to checking hardcoded roles then pivot
            if (!$role && class_exists(\Spatie\Permission\PermissionRegistrar::class) && method_exists($u, 'hasRole')) {
                foreach (['owner','admin','member'] as $r) {
                    if ($u->hasRole($r)) { $role = $r; break; }
                }
            }
            $role = $role ?: ($u->pivot->role ?? 'member');
            return [$u->id => $role];
        });

        // All roles available for this tenant (system + custom)
        $tenantRoles = Role::where('tenant_id', $tenant->id)->pluck('name')->toArray();

        // Seats usage info (plan-based limit)
        $plan = $tenant->plan ?: 'starter';
        $limit = (int) data_get(config('features.plans'), "$plan.users_limit", PHP_INT_MAX);
        $used = $tenant->users()->count();
        $remaining = max(0, $limit - $used);

        return view('members.index', compact('tenant', 'users', 'roles', 'tenantRoles', 'used', 'limit', 'remaining'));
    }

    public function invite(Request $request): RedirectResponse
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);

        $isAgentUser = (bool) (auth()->user()?->agent_id);
        if (! $isAgentUser) {
            Gate::authorize('invite-members', $tenant);
        }

        // Build valid role names: system roles + custom tenant roles
        $validRoles = collect(['owner', 'admin', 'member'])
            ->merge(Role::where('tenant_id', $tenant->id)->pluck('name'))
            ->unique()
            ->implode(',');

        $data = $request->validate([
            'email' => ['required','email','max:255'],
            'name' => ['nullable','string','max:255'],
            'role' => ['required','in:' . $validRoles],
        ]);

        if ($isAgentUser) {
            // Agent-scoped users can only invite member role
            $data['role'] = 'member';
        }

        // Enforce users_limit for the current plan
        $plan = $tenant->plan ?: 'starter';
        $limit = (int) data_get(config('features.plans'), "$plan.users_limit", PHP_INT_MAX);
        $currentCount = $tenant->users()->count();
        if ($currentCount >= $limit) {
            return back()->with('status', 'User limit reached for current plan. Please upgrade to add more members.');
        }

        $user = User::firstOrCreate(
            ['email' => $data['email']],
            ['name' => $data['name'] ?: $data['email'], 'password' => bcrypt(str()->random(24))]
        );

        // Prevent non-owners from assigning owner
        if ($data['role'] === 'owner' && ! $this->currentUserIsOwner($tenant)) {
            $data['role'] = 'admin';
        }

        $this->members->attach($user, $tenant, $data['role']);
        // Ensure invited user is scoped to inviter's agent if inviter is agent-scoped
        if ($isAgentUser && ($cid = auth()->user()?->agent_id)) {
            $user->agent_id = $cid;
            $user->save();
        }

        // Send password reset link so the invited user can set a password
        try {
            Password::sendResetLink(['email' => $user->email]);
            $msg = 'Member added and invite email sent.';
        } catch (\Throwable $e) {
            $msg = 'Member added. (Invite email failed in this environment)';
        }

        return back()->with('status', $msg);
    }

    public function updateRole(Request $request,String $tenant, User $user): RedirectResponse
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);

        $isAgentUser = (bool) (auth()->user()?->agent_id);
        if ($isAgentUser) {
            return back()->with('status', 'Not allowed');
        }
        Gate::authorize('update-member-roles', $tenant);

        // Build valid role names: system roles + custom tenant roles
        $validRoles = collect(['owner', 'admin', 'member'])
            ->merge(Role::where('tenant_id', $tenant->id)->pluck('name'))
            ->unique()
            ->implode(',');

        $data = $request->validate([
            'role' => ['required','in:' . $validRoles],
        ]);

        // Only owner can assign owner
        if ($data['role'] === 'owner' && ! $this->currentUserIsOwner($tenant)) {
            return back()->with('status', 'Only owner can assign owner role');
        }

        // Prevent removing the last owner
        $targetIsOwner = $this->userHasRoleInTenant($user, $tenant, 'owner');
        if ($targetIsOwner && $data['role'] !== 'owner') {
            if ($this->ownersCount($tenant) <= 1) {
                return back()->with('status', 'Tenant must have at least one owner');
            }
        }

        // Allow self-demotion only if another owner exists
        if (auth()->id() === $user->id && $data['role'] !== 'owner' && $this->currentUserIsOwner($tenant)) {
            if ($this->ownersCount($tenant) <= 1) {
                return back()->with('status', 'You are the only owner. Assign another owner first.');
            }
        }

        $this->members->setRole($user, $tenant, $data['role']);
        return back()->with('status', 'Role updated');
    }

    public function destroy(String $tenant,User $user): RedirectResponse
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);

        $isAgentUser = (bool) (auth()->user()?->agent_id);
        if ($isAgentUser) {
            if (auth()->id() === $user->id) {
                return back()->with('status', 'You cannot remove yourself');
            }
            if (($cid = auth()->user()?->agent_id) && $user->agent_id === $cid) {
                $this->members->detach($user, $tenant);
                return back()->with('status', 'Member removed');
            }
            return back()->with('status', 'Not allowed');
        }
        Gate::authorize('remove-members', $tenant);

        if (auth()->id() === $user->id) {
            return back()->with('status', 'You cannot remove yourself');
        }

        $this->members->detach($user, $tenant);
        return back()->with('status', 'Member removed');
    }

    protected function currentUserIsOwner(Tenant $tenant): bool
    {
        $user = auth()->user();
        if (! $user) return false;
        if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);
            if (method_exists($user, 'hasRole') && $user->hasRole('owner')) {
                return true;
            }
            // Fall back to pivot if Spatie role not set
        }
        $rel = $user->tenants()->whereKey($tenant->id)->first();
        return ($rel?->pivot?->role) === 'owner';
    }

    protected function userHasRoleInTenant(User $user, Tenant $tenant, string $role): bool
    {
        if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);
            if (method_exists($user, 'hasRole') && $user->hasRole($role)) {
                return true;
            }
        }
        $rel = $user->tenants()->whereKey($tenant->id)->first();
        return ($rel?->pivot?->role) === $role;
    }

    protected function ownersCount(Tenant $tenant): int
    {
        // Prefer Spatie when available for quicker check
        if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);
            return $tenant->users->filter(function (User $u) use ($tenant) {
                return method_exists($u, 'hasRole') && $u->hasRole('owner');
            })->count() ?: $tenant->users()->wherePivot('role', 'owner')->count();
        }
        return $tenant->users()->wherePivot('role', 'owner')->count();
    }
}
