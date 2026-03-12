<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __invoke(Request $request): View
    {
        $days = (int) $request->integer('days', 30);
        $days = $days > 0 ? min($days, 90) : 30;
        $tenantId = $request->integer('tenant_id');

        $tenants = Tenant::orderBy('name')->get(['id', 'name']);
        $startDate = now()->subDays($days - 1)->startOfDay()->toDateString();

        $pipelineSnapshots = DB::table('report_agent_pipeline_daily as rapd')
            ->join('tenants', 'tenants.id', '=', 'rapd.tenant_id')
            ->select('tenants.id as tenant_id', 'tenants.name', 'rapd.snapshot_date', 'rapd.leads_new', 'rapd.leads_in_progress', 'rapd.viewings_completed', 'rapd.leases_started', 'rapd.lead_to_lease_rate')
            ->whereNull('rapd.agent_id')
            ->where('rapd.snapshot_date', '>=', $startDate)
            ->when($tenantId, fn ($q) => $q->where('rapd.tenant_id', $tenantId))
            ->orderByDesc('rapd.snapshot_date')
            ->limit(100)
            ->get();

        $occupancySnapshots = DB::table('report_occupancy_daily as rod')
            ->join('tenants', 'tenants.id', '=', 'rod.tenant_id')
            ->select('tenants.id as tenant_id', 'tenants.name', 'rod.snapshot_date', 'rod.units_total', 'rod.units_occupied', 'rod.occupancy_rate', 'rod.rent_roll_cents')
            ->whereNull('rod.property_id')
            ->where('rod.snapshot_date', '>=', $startDate)
            ->when($tenantId, fn ($q) => $q->where('rod.tenant_id', $tenantId))
            ->orderByDesc('rod.snapshot_date')
            ->limit(100)
            ->get();

        $commissionSnapshots = DB::table('report_commissions_daily as rcd')
            ->join('tenants', 'tenants.id', '=', 'rcd.tenant_id')
            ->select('tenants.id as tenant_id', 'tenants.name', 'rcd.snapshot_date', 'rcd.pending_amount', 'rcd.approved_amount', 'rcd.paid_amount')
            ->whereNull('rcd.agent_id')
            ->where('rcd.snapshot_date', '>=', $startDate)
            ->when($tenantId, fn ($q) => $q->where('rcd.tenant_id', $tenantId))
            ->orderByDesc('rcd.snapshot_date')
            ->limit(100)
            ->get();

        $maintenanceSnapshots = DB::table('report_maintenance_daily as rmd')
            ->join('tenants', 'tenants.id', '=', 'rmd.tenant_id')
            ->select('tenants.id as tenant_id', 'tenants.name', 'rmd.snapshot_date', 'rmd.open_total', 'rmd.resolved_today', 'rmd.avg_open_days')
            ->where('rmd.snapshot_date', '>=', $startDate)
            ->when($tenantId, fn ($q) => $q->where('rmd.tenant_id', $tenantId))
            ->orderByDesc('rmd.snapshot_date')
            ->limit(100)
            ->get();

        return view('admin.reports.index', [
            'tenants' => $tenants,
            'selectedTenant' => $tenantId,
            'days' => $days,
            'pipelineSnapshots' => $pipelineSnapshots,
            'occupancySnapshots' => $occupancySnapshots,
            'commissionSnapshots' => $commissionSnapshots,
            'maintenanceSnapshots' => $maintenanceSnapshots,
        ]);
    }
}
