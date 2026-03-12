<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use App\Services\Tenancy\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TenantPermissionController extends Controller
{
    public function __construct(
        protected TenantManager $tenants
    ) {}

    public function index()
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);

        // Only owner and admin can manage permissions
        Gate::authorize('manage-permissions', $tenant);

        // Get roles scoped to this tenant
        $roles = Role::where('tenant_id', $tenant->id)->with('permissions')->get();

        // Compute role user counts (Spatie role or pivot role)
        if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);
        }
        $users = $tenant->users()->get();
        $roleUserCounts = $roles->mapWithKeys(fn ($role) => [$role->name => 0])->all();
        foreach ($users as $user) {
            $roleName = null;
            if (class_exists(\Spatie\Permission\PermissionRegistrar::class) && method_exists($user, 'getRoleNames')) {
                $roleName = $user->getRoleNames()->first();
            }
            $roleName = $roleName ?: ($user->pivot?->role ?? null);
            if ($roleName && array_key_exists($roleName, $roleUserCounts)) {
                $roleUserCounts[$roleName]++;
            }
        }

        // Get all available permissions for this tenant
        $permissions = Permission::all()->groupBy(function($permission) {
            return explode('-', $permission->name)[0] ?? 'general';
        });

        return view('tenant.permissions.index', compact('tenant', 'roles', 'permissions', 'roleUserCounts'));
    }

    public function create()
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);

        // Only owner can create roles
        Gate::authorize('manage-roles', $tenant);

        // Get all available permissions for this tenant
        $permissions = Permission::all()->groupBy(function($permission) {
            return explode('-', $permission->name)[0] ?? 'general';
        });

        return view('tenant.permissions.create', compact('tenant', 'permissions'));
    }

    public function edit(Request $request)
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);

        // Only owner can edit roles
        Gate::authorize('manage-roles', $tenant);

        // Resolve role with tenant scoping
        $role = Role::where('tenant_id', $tenant->id)->findOrFail($request->route('roleId'));

        // Ensure role belongs to this tenant
        abort_if($role->tenant_id !== $tenant->id, 403);

        // Don't allow editing system roles
        if (in_array($role->name, ['owner', 'admin', 'member'])) {
            abort(403, 'Cannot modify system roles');
        }

        // Get all available permissions for this tenant
        $permissions = Permission::all()->groupBy(function($permission) {
            return explode('-', $permission->name)[0] ?? 'general';
        });

        // Get role's current permissions
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('tenant.permissions.edit', compact('tenant', 'role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request)
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);

        // Only owner and admin can manage permissions
        Gate::authorize('manage-permissions', $tenant);

        // Resolve role with tenant scoping
        $role = Role::where('tenant_id', $tenant->id)->findOrFail($request->route('roleId'));

        // Ensure role belongs to this tenant
        abort_if($role->tenant_id !== $tenant->id, 403);

        $validated = $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        // Sync permissions for this role
        app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);
        $role->syncPermissions($validated['permissions'] ?? []);

        return back()->with('success', 'Permissions updated successfully');
    }

    public function createRole(Request $request)
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);

        // Only owner can create roles
        Gate::authorize('manage-roles', $tenant);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,NULL,id,tenant_id,' . $tenant->id,
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        // Create role scoped to this tenant
        app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);
        $role = Role::create([
            'name' => $validated['name'],
            'tenant_id' => $tenant->id,
            'guard_name' => 'web'
        ]);

        // Assign permissions
        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return back()->with('success', 'Role created successfully');
    }

    public function updateRole(Request $request)
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);

        // Only owner can update roles
        Gate::authorize('manage-roles', $tenant);

        // Resolve role with tenant scoping
        $role = Role::where('tenant_id', $tenant->id)->findOrFail($request->route('roleId'));

        // Ensure role belongs to this tenant
        abort_if($role->tenant_id !== $tenant->id, 403);

        // Don't allow updating system roles
        if (in_array($role->name, ['owner', 'admin', 'member'])) {
            return back()->with('error', 'Cannot modify system roles');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id . ',id,tenant_id,' . $tenant->id,
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        // Update role
        app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);
        $role->update(['name' => $validated['name']]);

        // Sync permissions
        $role->syncPermissions($validated['permissions'] ?? []);

        return back()->with('success', 'Role updated successfully');
    }

    public function deleteRole(Request $request)
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);

        // Only owner can delete roles
        Gate::authorize('manage-roles', $tenant);

        // Resolve role with tenant scoping
        $role = Role::where('tenant_id', $tenant->id)->findOrFail($request->route('roleId'));

        // Ensure role belongs to this tenant
        abort_if($role->tenant_id !== $tenant->id, 403);

        // Don't allow deleting system roles
        if (in_array($role->name, ['owner', 'admin', 'member'])) {
            return back()->with('error', 'Cannot delete system roles');
        }

        // Check if role is assigned to any users
        if ($role->users()->exists()) {
            return back()->with('error', 'Cannot delete role that is assigned to users');
        }

        $role->delete();

        return back()->with('success', 'Role deleted successfully');
    }

    public function showRole(Request $request)
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);

        Gate::authorize('manage-permissions', $tenant);

        // Resolve role with tenant scoping
        $role = Role::where('tenant_id', $tenant->id)->findOrFail($request->route('roleId'));

        // Ensure role belongs to this tenant
        abort_if($role->tenant_id !== $tenant->id, 403);

        return response()->json([
            'id' => $role->id,
            'name' => $role->name,
            'permissions' => $role->permissions->pluck('name')->toArray(),
        ]);
    }

    public function assignRoleToUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_name' => 'required|string',
        ]);

        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);

        Gate::authorize('manage-roles', $tenant);

        $user = User::findOrFail($request->user_id);
        $roleName = $request->role_name;

        // Ensure the user belongs to this tenant
        if (!$user->tenants()->whereKey($tenant->id)->exists()) {
            return back()->withErrors(['user' => 'User does not belong to this tenant.']);
        }

        // Set tenant context for Spatie
        app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);

        // Remove existing tenant roles
        $user->roles()->where('roles.tenant_id', $tenant->id)->detach();

        // Create or get role for this tenant
        $role = Role::firstOrCreate([
            'name' => $roleName,
            'tenant_id' => $tenant->id,
            'guard_name' => 'web'
        ]);

        $user->assignRole($role);

        return back()->with('success', 'Role assigned successfully.');
    }
}
