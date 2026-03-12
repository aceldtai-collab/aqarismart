<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Services\Tenancy\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TenantAdminController extends Controller
{
    public function index(): View
    {
        $tenants = Tenant::with('activeSubscription.package')->latest()->paginate(20);
        return view('admin.tenants.index', compact('tenants'));
    }

    public function show(Tenant $tenant, TenantManager $tenants): View
    {
        $tenantId = $tenant->getKey();

        // Basic metrics per tenant (central admin context; no tenant global scope)
        $properties = \App\Models\Property::where('tenant_id', $tenantId)->count();
        $units = \App\Models\Unit::where('tenant_id', $tenantId)->count();
        $totalUnits = $units;
        $vacantUnits = \App\Models\Unit::where('tenant_id', $tenantId)->where('status', 'vacant')->count();
        $occupancyRate = $totalUnits > 0 ? (int) round((($totalUnits - $vacantUnits) / $totalUnits) * 100) : 0;
        $residents = \App\Models\Resident::where('tenant_id', $tenantId)->count();
        $leasesActive = \App\Models\Lease::where('tenant_id', $tenantId)->where('status', 'active')->count();
        $leads = \App\Models\Lead::where('tenant_id', $tenantId)->count();
        $openRequests = \App\Models\MaintenanceRequest::where('tenant_id', $tenantId)->whereIn('status', ['new', 'open', 'in_progress'])->count();
        $since = now()->subDays(7);
        $leads7d = \App\Models\Lead::where('tenant_id', $tenantId)->where('created_at', '>=', $since)->count();
        $newUnits7d = \App\Models\Unit::where('tenant_id', $tenantId)->where('created_at', '>=', $since)->count();
        $openMaint7d = \App\Models\MaintenanceRequest::where('tenant_id', $tenantId)->whereIn('status', ['new', 'open', 'in_progress'])->where('created_at', '>=', $since)->count();

        // Recent activity demo (replace with real activity log if present)
        $recentActivities = [
            (object) ['title' => 'Lease signed', 'description' => 'Unit 3B - John Doe', 'created_at' => now()->subMinutes(5)],
            (object) ['title' => 'Maintenance closed', 'description' => 'HVAC issue - Unit 2A', 'created_at' => now()->subHours(2)],
        ];

        // Quick URLs into the tenant app
        $urls = [
            'dashboard' => $tenants->tenantUrl($tenant, '/dashboard'),
            'properties' => $tenants->tenantUrl($tenant, '/properties'),
            'units' => $tenants->tenantUrl($tenant, '/units'),
            'unitsVacant' => $tenants->tenantUrl($tenant, '/units?status=vacant'),
            'residents' => $tenants->tenantUrl($tenant, '/residents'),
            'leases' => $tenants->tenantUrl($tenant, '/leases'),
            'maintenance' => $tenants->tenantUrl($tenant, '/maintenance'),
            'contacts' => $tenants->tenantUrl($tenant, '/contacts'),
        ];

        return view('admin.tenants.show', [
            'tenant' => $tenant,
            'tenantUrl' => $tenants->tenantUrl($tenant, '/dashboard'),
            'metrics' => compact('properties', 'units', 'residents', 'leasesActive', 'leads', 'openRequests', 'vacantUnits', 'occupancyRate', 'leads7d', 'newUnits7d', 'openMaint7d'),
            'urls' => $urls,
            'openRequests' => $openRequests, // for backward widget
            'recentActivities' => $recentActivities,
        ]);
    }
}
