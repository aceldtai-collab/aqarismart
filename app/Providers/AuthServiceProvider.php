<?php

namespace App\Providers;

use App\Models\Agent;
use App\Models\AgentCommission;
use App\Models\AgentLead;
use App\Models\Contact;
use App\Models\PropertyViewing;
use App\Policies\AgentCommissionPolicy;
use App\Policies\AgentLeadPolicy;
use App\Policies\AgentPolicy;
use App\Policies\ContactPolicy;
use App\Policies\PropertyViewingPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Agent::class => AgentPolicy::class,
        AgentLead::class => AgentLeadPolicy::class,
        PropertyViewing::class => PropertyViewingPolicy::class,
        AgentCommission::class => AgentCommissionPolicy::class,
        Contact::class => ContactPolicy::class,
        \App\Models\Property::class => \App\Policies\PropertyPolicy::class,
        \App\Models\Unit::class => \App\Policies\UnitPolicy::class,
        \App\Models\Resident::class => \App\Policies\ResidentPolicy::class,
        \App\Models\Lease::class => \App\Policies\LeasePolicy::class,
        \App\Models\MaintenanceRequest::class => \App\Policies\MaintenanceRequestPolicy::class,
        \App\Models\Tenant::class => \App\Policies\TenantPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Super admin bypass via env list
        Gate::before(function ($user, $ability) {
            if (! $user) {
                return null;
            }

            $emails = config('auth.super_admin_emails', []);

            if ($emails === []) {
                return null;
            }

            if (in_array(strtolower((string) $user->email), $emails, true)) {
                return true;
            }
        });

        // Tenant permissions management (delegates to TenantPolicy)
        Gate::define('manage-permissions', [\App\Policies\TenantPolicy::class, 'managePermissions']);

        // Tenant roles management - owner only (delegates to TenantPolicy)
        Gate::define('manage-roles', [\App\Policies\TenantPolicy::class, 'manageRoles']);

        // Tenant feature access (delegates to TenantPolicy)
        Gate::define('view-dashboard', function ($user) {
            $tenant = app(\App\Services\Tenancy\TenantManager::class)->tenant();
            if (!$tenant) return false;
            $policy = new \App\Policies\TenantPolicy();
            return $policy->viewDashboard($user, $tenant);
        });
        Gate::define('view-reports', function ($user) {
            $tenant = app(\App\Services\Tenancy\TenantManager::class)->tenant();
            if (!$tenant) return false;
            $policy = new \App\Policies\TenantPolicy();
            return $policy->viewReports($user, $tenant);
        });
        Gate::define('view-members', function ($user) {
            $tenant = app(\App\Services\Tenancy\TenantManager::class)->tenant();
            if (!$tenant) return false;
            $policy = new \App\Policies\TenantPolicy();
            return $policy->viewMembers($user, $tenant);
        });
        Gate::define('invite-members', function ($user) {
            $tenant = app(\App\Services\Tenancy\TenantManager::class)->tenant();
            if (!$tenant) return false;
            $policy = new \App\Policies\TenantPolicy();
            return $policy->inviteMembers($user, $tenant);
        });
        Gate::define('update-member-roles', function ($user) {
            $tenant = app(\App\Services\Tenancy\TenantManager::class)->tenant();
            if (!$tenant) return false;
            $policy = new \App\Policies\TenantPolicy();
            return $policy->updateMemberRoles($user, $tenant);
        });
        Gate::define('remove-members', function ($user) {
            $tenant = app(\App\Services\Tenancy\TenantManager::class)->tenant();
            if (!$tenant) return false;
            $policy = new \App\Policies\TenantPolicy();
            return $policy->removeMembers($user, $tenant);
        });
        Gate::define('manage-attributes', function ($user) {
            $tenant = app(\App\Services\Tenancy\TenantManager::class)->tenant();
            if (!$tenant) return false;
            $policy = new \App\Policies\TenantPolicy();
            return $policy->manageAttributes($user, $tenant);
        });
        Gate::define('view-settings', function ($user) {
            $tenant = app(\App\Services\Tenancy\TenantManager::class)->tenant();
            if (!$tenant) return false;
            $policy = new \App\Policies\TenantPolicy();
            return $policy->viewSettings($user, $tenant);
        });
        Gate::define('update-settings', function ($user) {
            $tenant = app(\App\Services\Tenancy\TenantManager::class)->tenant();
            if (!$tenant) return false;
            $policy = new \App\Policies\TenantPolicy();
            return $policy->updateSettings($user, $tenant);
        });
    }
}
