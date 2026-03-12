<?php

namespace App\Services\Reports;

use App\Models\ReportAlert;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TenantInsightService
{
    public function analyze(Tenant $tenant, Carbon|string $date): array
    {
        $date = $date instanceof Carbon ? $date->copy()->startOfDay() : Carbon::parse($date)->startOfDay();
        $alerts = [];

        if ($alert = $this->detectOccupancyDrop($tenant, $date)) {
            $alerts[] = $alert;
        }
        if ($alert = $this->detectMaintenanceBacklog($tenant, $date)) {
            $alerts[] = $alert;
        }
        if ($alert = $this->detectPipelineSlump($tenant, $date)) {
            $alerts[] = $alert;
        }

        return $alerts;
    }

    protected function detectOccupancyDrop(Tenant $tenant, Carbon $date): ?ReportAlert
    {
        $current = DB::table('report_occupancy_daily')
            ->where('tenant_id', $tenant->id)
            ->whereNull('property_id')
            ->whereDate('snapshot_date', $date->toDateString())
            ->first();

        $previous = DB::table('report_occupancy_daily')
            ->where('tenant_id', $tenant->id)
            ->whereNull('property_id')
            ->whereDate('snapshot_date', $date->copy()->subDay()->toDateString())
            ->first();

        if (! $current || ! $previous) {
            return null;
        }

        $drop = $previous->occupancy_rate - $current->occupancy_rate;
        if ($drop < 10) {
            return null;
        }

        return $this->storeAlert($tenant, $date, 'occupancy_drop', 'Occupancy Decline', sprintf(
            'Occupancy dropped %.1f points day-over-day (from %.1f%% to %.1f%%). Investigate vacancies.',
            $drop,
            $previous->occupancy_rate,
            $current->occupancy_rate
        ), [
            'previous_rate' => $previous->occupancy_rate,
            'current_rate' => $current->occupancy_rate,
        ], 'critical');
    }

    protected function detectMaintenanceBacklog(Tenant $tenant, Carbon $date): ?ReportAlert
    {
        $row = DB::table('report_maintenance_daily')
            ->where('tenant_id', $tenant->id)
            ->whereDate('snapshot_date', $date->toDateString())
            ->first();

        if (! $row) {
            return null;
        }

        if ($row->open_total < 10 || $row->avg_open_days < 7) {
            return null;
        }

        return $this->storeAlert($tenant, $date, 'maintenance_backlog', 'Maintenance Backlog Rising', sprintf(
            '%d open tickets with an average age of %.1f days. Consider reallocating technicians.',
            $row->open_total,
            $row->avg_open_days
        ), [
            'open_total' => $row->open_total,
            'avg_open_days' => $row->avg_open_days,
        ], 'warning');
    }

    protected function detectPipelineSlump(Tenant $tenant, Carbon $date): ?ReportAlert
    {
        $start = $date->copy()->subDays(6)->toDateString();

        $totalLeads = DB::table('report_agent_pipeline_daily')
            ->where('tenant_id', $tenant->id)
            ->whereNull('agent_id')
            ->whereBetween('snapshot_date', [$start, $date->toDateString()])
            ->sum('leads_new');

        if ($totalLeads > 0) {
            return null;
        }

        return $this->storeAlert($tenant, $date, 'pipeline_slump', 'Lead Pipeline Stalled', 'No new leads have been captured in the last 7 days.', [], 'warning');
    }

    protected function storeAlert(Tenant $tenant, Carbon $date, string $type, string $title, string $message, array $meta = [], string $severity = 'warning'): ReportAlert
    {
        return ReportAlert::updateOrCreate([
            'tenant_id' => $tenant->id,
            'snapshot_date' => $date->toDateString(),
            'type' => $type,
        ], [
            'severity' => $severity,
            'title' => $title,
            'message' => $message,
            'meta' => $meta,
        ]);
    }
}
