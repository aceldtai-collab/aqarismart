<?php

namespace App\Http\Controllers;

use App\Services\Reports\TenantSnapshotQuery;
use App\Services\Tenancy\TenantManager;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        protected TenantManager $tenants,
        protected TenantSnapshotQuery $snapshots,
    ) {}

    public function index(Request $request): View
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);

        $agentId = auth()->user()?->agent_id;
        $range = (int) $request->integer('days', 30);
        $range = $range > 0 ? min($range, 90) : 30;

        return view('reports.index', [
            'tenant' => $tenant,
            'agentScoped' => (bool) $agentId,
            'days' => $range,
            'pipelineTrend' => $this->snapshots->agentPipelineTrend($tenant, $agentId, $range),
            'occupancyTrend' => $this->snapshots->occupancyTrend($tenant, null, $range),
            'commissionTrend' => $this->snapshots->commissionTrend($tenant, $agentId, $range),
            'maintenanceTrend' => $this->snapshots->maintenanceTrend($tenant, $range),
        ]);
    }
}
