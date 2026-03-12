<?php

use App\Http\Controllers\AgentCommissionController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\AgentLeadController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PropertyViewingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportExportController;
use App\Http\Controllers\TenantSettingsController;
use App\Http\Controllers\TenantPublicInquiryController;
use App\Models\Agent;
use App\Models\ReportAlert;
use App\Services\Dashboard\TenantDashboardMetrics;
use App\Services\Reports\TenantSnapshotQuery;
use App\Services\Tenancy\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;

// Tenant Subdomain: Public + Staff (no locale prefix)
Route::domain('{tenant_slug}.' . config('tenancy.base_domain'))
    ->middleware(['web','tenant', SubstituteBindings::class])
    ->group(function () {
        // --- Tenant Public (no auth)
        Route::get('/', [\App\Http\Controllers\TenantHomeController::class, 'index'])->name('tenant.home');
        Route::get('/search', [\App\Http\Controllers\TenantHomeController::class, 'search'])->name('tenant.search');
        Route::get('/sales-flow', function () {
            $tenant = app(TenantManager::class)->tenant();
            abort_if(! $tenant, 404);
            return view('tenant.sales-flow', ['tenant' => $tenant]);
        })->name('tenant.sales-flow');
        Route::get('/sales-flow/print', function () {
            $tenant = app(TenantManager::class)->tenant();
            abort_if(! $tenant, 404);
            return view('tenant.sales-flow-print', ['tenant' => $tenant]);
        })->name('tenant.sales-flow.print');
        Route::get('/sales-story', function () {
            $tenant = app(TenantManager::class)->tenant();
            abort_if(! $tenant, 404);
            return view('tenant.sales-story', ['tenant' => $tenant]);
        })->name('tenant.sales-story');
        Route::middleware(['guest'])->post('/resident/register', [\App\Http\Controllers\ResidentAuthController::class, 'register'])->name('resident.register');
        Route::middleware(['throttle:10,1'])->post('/inquire', [TenantPublicInquiryController::class, 'store'])->name('tenant.inquire');
        Route::get('/listings/{unit}', [\App\Http\Controllers\UnitController::class, 'tenantShow'])->name('tenant.unit');

        // Public Locations API (tenant-scoped, no auth)
        Route::prefix('locations')->group(function () {
            Route::get('/countries', [\App\Http\Controllers\LocationsController::class, 'countries'])->name('locations.countries');
            Route::get('/states', [\App\Http\Controllers\LocationsController::class, 'states'])->name('locations.states');
            Route::get('/cities', [\App\Http\Controllers\LocationsController::class, 'cities'])->name('locations.cities');
        });
        // --- Tenant Staff (auth)
        Route::get('/dashboard', function (Request $request) {
            $tenant = app(TenantManager::class)->tenant();
            abort_if(! $tenant, 404);

            $user = auth()->user();
            $agentScoped = (bool) $user?->agent_id;
            $requestedAgent = $agentScoped ? $user->agent_id : $request->integer('agent_id');

            if (! $agentScoped && $requestedAgent) {
                $exists = Agent::where('tenant_id', $tenant->id)->where('id', $requestedAgent)->exists();
                if (! $exists) {
                    $requestedAgent = null;
                }
            }

            $agentId = $requestedAgent ?? $user?->agent_id;
            $days = $request->integer('days', 14);
            $days = $days > 0 ? min(max($days, 7), 90) : 14;

            $metrics = app(TenantDashboardMetrics::class)->metrics($tenant, $agentId);
            $snapshots = app(TenantSnapshotQuery::class);
            $availableAgents = $agentScoped
                ? collect()
                : Agent::where('tenant_id', $tenant->id)->orderBy('name')->get(['id', 'name']);
            $recentAlerts = ReportAlert::where('tenant_id', $tenant->id)
                ->orderByDesc('snapshot_date')
                ->latest()
                ->limit(5)
                ->get();

            return view('dashboard', $metrics + [
                'tenant' => $tenant,
                'agentScoped' => $agentScoped,
                'filterAgent' => $agentId,
                'filterDays' => $days,
                'availableAgents' => $availableAgents,
                'alerts' => $recentAlerts,
                'pipelineTrend' => $snapshots->agentPipelineTrend($tenant, $agentId, $days),
                'occupancyTrend' => $snapshots->occupancyTrend($tenant, null, $days),
                'commissionTrend' => $snapshots->commissionTrend($tenant, $agentId, $days),
                'maintenanceTrend' => $snapshots->maintenanceTrend($tenant, $days),
            ]);
        })->middleware(['auth', 'verified', 'staff', 'can:view-dashboard'])->name('dashboard');

        Route::middleware(['auth', 'verified', 'staff'])->group(function () {
            Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
            Route::get('/billing/setup-intent', [BillingController::class, 'setupIntent'])->name('billing.setup_intent');
            Route::post('/billing/subscribe', [BillingController::class, 'subscribe'])->middleware('role:owner|admin')->name('billing.subscribe');
            Route::post('/billing/cancel', [BillingController::class, 'cancel'])->middleware('role:owner|admin')->name('billing.cancel');
            Route::post('/billing/resume', [BillingController::class, 'resume'])->middleware('role:owner|admin')->name('billing.resume');

            // Roles & Permissions (Owner/Admin only)
            Route::middleware('role:owner|admin')->group(function () {
                Route::get('/permissions', [\App\Http\Controllers\TenantPermissionController::class, 'index'])->name('permissions.index');
                Route::get('/permissions/create', [\App\Http\Controllers\TenantPermissionController::class, 'create'])->name('permissions.create');
                Route::get('/permissions/{roleId}/edit', [\App\Http\Controllers\TenantPermissionController::class, 'edit'])->name('permissions.edit');
                Route::patch('/permissions/roles/{roleId}', [\App\Http\Controllers\TenantPermissionController::class, 'update'])->name('permissions.roles.update');
                Route::get('/permissions/roles/{roleId}', [\App\Http\Controllers\TenantPermissionController::class, 'showRole'])->name('permissions.roles.show');
                Route::patch('/permissions/roles/{roleId}/edit', [\App\Http\Controllers\TenantPermissionController::class, 'updateRole'])->name('permissions.roles.edit');
                Route::post('/permissions/roles', [\App\Http\Controllers\TenantPermissionController::class, 'createRole'])->name('permissions.roles.create');
                Route::post('/permissions/assign-role', [\App\Http\Controllers\TenantPermissionController::class, 'assignRoleToUser'])->name('permissions.assign-role');
                Route::delete('/permissions/roles/{roleId}', [\App\Http\Controllers\TenantPermissionController::class, 'deleteRole'])->name('permissions.roles.delete');
            });

            // Tenant-scoped Agents and Contacts
            Route::resource('agents', AgentController::class)->except(['show']);

            Route::resource('agent-leads', AgentLeadController::class)->except(['show']);
            Route::resource('property-viewings', PropertyViewingController::class)->except(['show']);
            Route::resource('agent-commissions', AgentCommissionController::class)->except(['show']);

            Route::resource('contacts', ContactController::class)->except(['show']);
            Route::get('contacts-import', [ContactController::class, 'importForm'])->name('contacts.import.form');
            Route::post('contacts-import', [ContactController::class, 'importStore'])->name('contacts.import.store');

            // Members management
            Route::get('/members', [\App\Http\Controllers\MemberController::class, 'index'])->name('members.index');
            Route::post('/members/invite', [\App\Http\Controllers\MemberController::class, 'invite'])->name('members.invite');
            Route::patch('/members/{user}/role', [\App\Http\Controllers\MemberController::class, 'updateRole'])->name('members.updateRole');
            Route::delete('/members/{user}', [\App\Http\Controllers\MemberController::class, 'destroy'])->name('members.destroy');

            // PMS: Properties
            Route::middleware('features:properties')->group(function () {
                Route::resource('properties', \App\Http\Controllers\PropertyController::class)->except(['show']);

                // Residents
                Route::resource('residents', \App\Http\Controllers\ResidentController::class)->except(['show']);

                // Leases
                Route::resource('leases', \App\Http\Controllers\LeaseController::class)->except(['show']);
            });

            // PMS: Units (independent from properties)
            Route::middleware('features:units')->group(function () {
                Route::resource('units', \App\Http\Controllers\UnitController::class)->except(['show']);
            });

            // Maintenance
            Route::middleware('features:maintenance')->group(function () {
                Route::resource('maintenance', \App\Http\Controllers\MaintenanceController::class)->except(['show'])->parameters([
                    'maintenance' => 'maintenanceRequest'
                ]);
            });

            // Custom Attributes (paid feature)
            Route::middleware(['features:custom_attributes', 'can:manage-attributes'])->group(function () {
                Route::resource('custom-attributes', \App\Http\Controllers\TenantAttributeFieldController::class)
                    ->parameters(['custom-attributes' => 'customAttribute'])
                    ->except(['show'])
                    ->names('custom-attributes');
            });

            // Tenant settings
            Route::get('/settings', [TenantSettingsController::class, 'edit'])->middleware('can:view-settings')->name('settings.edit');
            Route::put('/settings', [TenantSettingsController::class, 'update'])->middleware('can:update-settings')->name('settings.update');

            Route::middleware(['auth', 'verified', 'staff', 'can:view-reports'])->group(function () {
                Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
                Route::get('/reports/export/pipeline.csv', [ReportExportController::class, 'pipelineCsv'])->name('reports.export.pipeline.csv');
                Route::get('/reports/export/occupancy.csv', [ReportExportController::class, 'occupancyCsv'])->name('reports.export.occupancy.csv');
                Route::get('/reports/export/pipeline.pdf', [ReportExportController::class, 'pipelinePdf'])->name('reports.export.pipeline.pdf');
                Route::get('/reports/export/occupancy.pdf', [ReportExportController::class, 'occupancyPdf'])->name('reports.export.occupancy.pdf');
            });
        });
    });
