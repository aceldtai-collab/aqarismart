<?php

namespace App\Services\Dashboard;

use App\Models\AgentCommission;
use App\Models\AgentLead;
use App\Models\Lease;
use App\Models\MaintenanceRequest;
use App\Models\Property;
use App\Models\PropertyViewing;
use App\Models\Tenant;
use App\Models\Unit;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class TenantDashboardMetrics
{
    public function __construct(protected int $ttlSeconds = 120) {}

    /**
     * Build dashboard data for a tenant (optionally scoped to an agent).
     */
    public function metrics(Tenant $tenant, ?int $agentId = null): array
    {
        $cacheKey = sprintf('tenant-dashboard:%d:agent:%d', $tenant->id, $agentId ?: 0);

        return Cache::remember($cacheKey, $this->ttlSeconds, function () use ($tenant, $agentId) {
            // Properties & units
            $propertyQuery = Property::query();
            if ($agentId) {
                $propertyQuery->where('agent_id', $agentId);
            }
            $propertyCount = (clone $propertyQuery)->count();

            $unitQuery = Unit::query();
            if ($agentId) {
                $unitQuery->where('agent_id', $agentId);
            }
            $unitCount = (clone $unitQuery)->count();

            // Leases and rent roll
            $activeLeaseQuery = Lease::query()->where('status', 'active');
            if ($agentId) {
                $activeLeaseQuery->where(function ($q) use ($agentId) {
                    $q->where('agent_id', $agentId)
                        ->orWhereHas('property', fn ($p) => $p->where('agent_id', $agentId))
                        ->orWhereHas('unit', fn ($u) => $u->where('agent_id', $agentId));
                });
            }
            $activeLeaseCount = (clone $activeLeaseQuery)->count();
            $rentRollCents = (clone $activeLeaseQuery)->sum('rent_cents');
            $occupiedUnitCount = $unitCount > 0
                ? (clone $activeLeaseQuery)->distinct('unit_id')->count('unit_id')
                : 0;

            // Maintenance
            $maintenanceQuery = MaintenanceRequest::query();
            if ($agentId) {
                $maintenanceQuery->where(function ($q) use ($agentId) {
                    $q->whereHas('property', fn ($p) => $p->where('agent_id', $agentId))
                        ->orWhereHas('unit', fn ($u) => $u->where('agent_id', $agentId));
                });
            }
            $openMaintenanceCount = (clone $maintenanceQuery)
                ->whereIn('status', ['new', 'open', 'in_progress'])
                ->count();
            $maintenanceBreakdown = (clone $maintenanceQuery)
                ->select('status')
                ->selectRaw('COUNT(*) as aggregate')
                ->groupBy('status')
                ->pluck('aggregate', 'status')
                ->toArray();
            $maintenanceBreakdown = array_merge([
                'new' => 0,
                'open' => 0,
                'in_progress' => 0,
                'resolved' => 0,
                'completed' => 0,
            ], $maintenanceBreakdown);
            $maintenanceAging = (clone $maintenanceQuery)
                ->whereIn('status', ['new', 'open', 'in_progress'])
                ->get(['id', 'created_at']);
            $avgOpenDays = $maintenanceAging->isNotEmpty()
                ? round($maintenanceAging->avg(fn ($req) => $req->created_at ? $req->created_at->diffInDays(now()) : 0), 1)
                : 0.0;

            // Upcoming leases (next 60 days)
            $upcomingLeases = (clone $activeLeaseQuery)
                ->whereNotNull('end_date')
                ->whereBetween('end_date', [now(), now()->addDays(60)])
                ->orderBy('end_date')
                ->with(['property:id,name', 'unit:id,code,title,property_id'])
                ->limit(5)
                ->get(['id', 'property_id', 'unit_id', 'end_date', 'rent_cents', 'agent_id']);

            // Leads and viewings
            $leadQuery = AgentLead::query()
                ->when($agentId, fn ($q) => $q->where('agent_id', $agentId));
            $leadsThisMonth = (clone $leadQuery)->where('created_at', '>=', now()->startOfMonth())->count();
            $leadsPrevMonth = (clone $leadQuery)->whereBetween('created_at', [
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth(),
            ])->count();
            $leadChangePct = 0.0;
            if ($leadsPrevMonth > 0) {
                $leadChangePct = round((($leadsThisMonth - $leadsPrevMonth) / $leadsPrevMonth) * 100, 1);
            } elseif ($leadsThisMonth > 0) {
                $leadChangePct = 100.0;
            }
            $leadTrend = (clone $leadQuery)
                ->where('created_at', '>=', now()->subDays(30))
                ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
                ->groupBy('day')
                ->orderBy('day')
                ->get()
                ->map(fn ($row) => [
                    'day' => Carbon::parse($row->day)->format('M j'),
                    'raw_date' => $row->day,
                    'total' => (int) $row->total,
                ]);

            $viewingQuery = PropertyViewing::query()
                ->when($agentId, fn ($q) => $q->where('agent_id', $agentId));
            $viewingsScheduled = (clone $viewingQuery)
                ->where('status', 'scheduled')
                ->whereBetween('appointment_at', [now(), now()->addDays(30)])
                ->count();
            $viewingsCompletedThisMonth = (clone $viewingQuery)
                ->where('status', 'completed')
                ->where('appointment_at', '>=', now()->startOfMonth())
                ->count();

            // Agent performance: property occupancy list
            $topProperties = Property::query()
                ->when($agentId, fn ($q) => $q->where('agent_id', $agentId))
                ->withCount([
                    'units as total_units' => function ($q) use ($agentId) {
                        if ($agentId) {
                            $q->where('agent_id', $agentId);
                        }
                    },
                    'units as occupied_units' => function ($q) use ($agentId) {
                        if ($agentId) {
                            $q->where('agent_id', $agentId);
                        }
                        $q->whereHas('leases', fn ($l) => $l->where('status', 'active'));
                    },
                ])
                ->having('total_units', '>', 0)
                ->orderByDesc('occupied_units')
                ->limit(5)
                ->get(['id', 'name']);

            // Commissions
            $commissionQuery = AgentCommission::query();
            if ($agentId) {
                $commissionQuery->forAgent($agentId);
            }
            $commissionAmountsByStatus = $commissionQuery
                ->selectRaw('status, COUNT(*) as count, SUM(amount) as amount')
                ->groupBy('status')
                ->pluck('amount', 'status')
                ->toArray();
            $commissionCountsByStatus = $commissionQuery
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $commissionStats = [
                'pending' => ['count' => $commissionCountsByStatus['pending'] ?? 0, 'amount' => (float) ($commissionAmountsByStatus['pending'] ?? 0)],
                'approved' => ['count' => $commissionCountsByStatus['approved'] ?? 0, 'amount' => (float) ($commissionAmountsByStatus['approved'] ?? 0)],
                'paid' => ['count' => $commissionCountsByStatus['paid'] ?? 0, 'amount' => (float) ($commissionAmountsByStatus['paid'] ?? 0)],
                'cancelled' => ['count' => $commissionCountsByStatus['cancelled'] ?? 0, 'amount' => (float) ($commissionAmountsByStatus['cancelled'] ?? 0)],
            ];

            $occupancyRate = $unitCount > 0 ? round(($occupiedUnitCount / $unitCount) * 100, 1) : 0.0;
            $vacantUnits = $unitCount > $occupiedUnitCount ? $unitCount - $occupiedUnitCount : 0;
            $currency = $tenant->settings['currency'] ?? 'JOD';

            return [
                'metrics' => [
                    'properties' => $propertyCount,
                    'units' => $unitCount,
                    'active_leases' => $activeLeaseCount,
                    'open_maintenance' => $openMaintenanceCount,
                    'occupied_units' => $occupiedUnitCount,
                    'vacant_units' => $vacantUnits,
                    'occupancy_rate' => $occupancyRate,
                    'monthly_rent' => round($rentRollCents / 100, 2),
                    'rent_currency' => $currency,
                    'avg_open_days' => $avgOpenDays,
                    'viewings_scheduled' => $viewingsScheduled,
                    'viewings_completed_this_month' => $viewingsCompletedThisMonth,
                    'commissions' => $commissionStats,
                ],
                'maintenanceBreakdown' => $maintenanceBreakdown,
                'upcomingLeases' => $upcomingLeases,
                'leadStats' => [
                    'this_month' => $leadsThisMonth,
                    'prev_month' => $leadsPrevMonth,
                    'change_pct' => $leadChangePct,
                    'trend' => $leadTrend,
                ],
                'propertyOccupancy' => $topProperties->map(function ($property) {
                    $total = max((int) ($property->total_units ?? 0), 1);
                    $occupied = (int) ($property->occupied_units ?? 0);
                    $rate = round(($occupied / $total) * 100, 1);
                    return [
                        'id' => $property->id,
                        'name' => $property->name,
                        'occupied' => $occupied,
                        'total' => $total,
                        'rate' => $rate,
                    ];
                }),
            ];
        });
    }
}
