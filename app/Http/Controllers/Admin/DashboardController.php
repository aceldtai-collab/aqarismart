<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $activeSubsCount = DB::table('tenant_subscriptions')
            ->where('status', 'active')
            ->count();

        $recentTenants = Tenant::latest()->limit(5)->get(['id','name','slug','created_at']);

        $packagesCount = DB::table('packages')->where('is_active', true)->count();

        return view('admin.index', [
            'tenantsCount' => Tenant::count(),
            'usersCount' => User::count(),
            'activeSubsCount' => $activeSubsCount,
            'packagesCount' => $packagesCount,
            'recentTenants' => $recentTenants,
            'occupancySnapshots' => $this->latestOccupancySnapshots(),
            'maintenanceSnapshots' => $this->latestMaintenanceSnapshots(),
            'commissionSnapshots' => $this->latestCommissionSnapshots(),
        ]);
    }

    protected function latestOccupancySnapshots()
    {
        return DB::table('report_occupancy_daily as ro')
            ->join('tenants', 'tenants.id', '=', 'ro.tenant_id')
            ->select('tenants.name', 'ro.snapshot_date', 'ro.units_total', 'ro.units_occupied', 'ro.occupancy_rate')
            ->whereNull('ro.property_id')
            ->orderByDesc('ro.snapshot_date')
            ->limit(5)
            ->get();
    }

    protected function latestMaintenanceSnapshots()
    {
        return DB::table('report_maintenance_daily as rm')
            ->join('tenants', 'tenants.id', '=', 'rm.tenant_id')
            ->select('tenants.name', 'rm.snapshot_date', 'rm.open_total', 'rm.resolved_today', 'rm.avg_open_days')
            ->orderByDesc('rm.snapshot_date')
            ->limit(5)
            ->get();
    }

    protected function latestCommissionSnapshots()
    {
        return DB::table('report_commissions_daily as rc')
            ->join('tenants', 'tenants.id', '=', 'rc.tenant_id')
            ->select('tenants.name', 'rc.snapshot_date', 'rc.pending_amount', 'rc.approved_amount', 'rc.paid_amount')
            ->whereNull('rc.agent_id')
            ->orderByDesc('rc.snapshot_date')
            ->limit(5)
            ->get();
    }
}
