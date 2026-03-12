<?php

namespace App\Services\Reports;

use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TenantSnapshotQuery
{
    public function __construct(protected int $ttlSeconds = 300) {}

    public function agentPipelineTrend(Tenant $tenant, ?int $agentId = null, int $days = 30): array
    {
        $start = Carbon::now()->subDays($days - 1)->startOfDay();
        $cacheKey = sprintf('snapshot:pipeline:%d:%d:%d', $tenant->id, $agentId ?: 0, $days);

        return Cache::remember($cacheKey, $this->ttlSeconds, function () use ($tenant, $agentId, $start) {
            return DB::table('report_agent_pipeline_daily')
                ->where('tenant_id', $tenant->id)
                ->when($agentId, fn ($q) => $q->where('agent_id', $agentId))
                ->where('snapshot_date', '>=', $start->toDateString())
                ->orderBy('snapshot_date')
                ->get()
                ->map(fn ($row) => [
                    'date' => $row->snapshot_date,
                    'leads_new' => (int) $row->leads_new,
                    'leads_in_progress' => (int) $row->leads_in_progress,
                    'leads_visited' => (int) $row->leads_visited,
                    'leads_closed' => (int) $row->leads_closed,
                    'leads_lost' => (int) $row->leads_lost,
                    'viewings_scheduled' => (int) $row->viewings_scheduled,
                    'viewings_completed' => (int) $row->viewings_completed,
                    'leases_started' => (int) $row->leases_started,
                    'leases_active' => (int) $row->leases_active,
                    'lead_to_viewing_rate' => (float) $row->lead_to_viewing_rate,
                    'lead_to_lease_rate' => (float) $row->lead_to_lease_rate,
                ])
                ->toArray();
        });
    }

    public function occupancyTrend(Tenant $tenant, ?int $propertyId = null, int $days = 30): array
    {
        $start = Carbon::now()->subDays($days - 1)->startOfDay();
        $cacheKey = sprintf('snapshot:occupancy:%d:%d:%d', $tenant->id, $propertyId ?: 0, $days);

        return Cache::remember($cacheKey, $this->ttlSeconds, function () use ($tenant, $propertyId, $start) {
            return DB::table('report_occupancy_daily')
                ->where('tenant_id', $tenant->id)
                ->when($propertyId, fn ($q) => $q->where('property_id', $propertyId))
                ->when(! $propertyId, fn ($q) => $q->whereNull('property_id'))
                ->where('snapshot_date', '>=', $start->toDateString())
                ->orderBy('snapshot_date')
                ->get()
                ->map(fn ($row) => [
                    'date' => $row->snapshot_date,
                    'units_total' => (int) $row->units_total,
                    'units_occupied' => (int) $row->units_occupied,
                    'occupancy_rate' => (float) $row->occupancy_rate,
                    'rent_roll_cents' => (int) $row->rent_roll_cents,
                ])
                ->toArray();
        });
    }

    public function commissionTrend(Tenant $tenant, ?int $agentId = null, int $days = 30): array
    {
        $start = Carbon::now()->subDays($days - 1)->startOfDay();
        $cacheKey = sprintf('snapshot:commissions:%d:%d:%d', $tenant->id, $agentId ?: 0, $days);

        return Cache::remember($cacheKey, $this->ttlSeconds, function () use ($tenant, $agentId, $start) {
            return DB::table('report_commissions_daily')
                ->where('tenant_id', $tenant->id)
                ->when($agentId, fn ($q) => $q->where('agent_id', $agentId))
                ->when(! $agentId, fn ($q) => $q->whereNull('agent_id'))
                ->where('snapshot_date', '>=', $start->toDateString())
                ->orderBy('snapshot_date')
                ->get()
                ->map(fn ($row) => [
                    'date' => $row->snapshot_date,
                    'pending' => [
                        'count' => (int) $row->pending_count,
                        'amount' => (float) $row->pending_amount,
                    ],
                    'approved' => [
                        'count' => (int) $row->approved_count,
                        'amount' => (float) $row->approved_amount,
                    ],
                    'paid' => [
                        'count' => (int) $row->paid_count,
                        'amount' => (float) $row->paid_amount,
                    ],
                    'cancelled' => [
                        'count' => (int) $row->cancelled_count,
                        'amount' => (float) $row->cancelled_amount,
                    ],
                ])
                ->toArray();
        });
    }

    public function maintenanceTrend(Tenant $tenant, int $days = 30): array
    {
        $start = Carbon::now()->subDays($days - 1)->startOfDay();
        $cacheKey = sprintf('snapshot:maintenance:%d:%d', $tenant->id, $days);

        return Cache::remember($cacheKey, $this->ttlSeconds, function () use ($tenant, $start) {
            return DB::table('report_maintenance_daily')
                ->where('tenant_id', $tenant->id)
                ->where('snapshot_date', '>=', $start->toDateString())
                ->orderBy('snapshot_date')
                ->get()
                ->map(fn ($row) => [
                    'date' => $row->snapshot_date,
                    'open_new' => (int) $row->open_new,
                    'open_in_progress' => (int) $row->open_in_progress,
                    'open_total' => (int) $row->open_total,
                    'resolved_today' => (int) $row->resolved_today,
                    'avg_open_days' => (float) $row->avg_open_days,
                ])
                ->toArray();
        });
    }
}
