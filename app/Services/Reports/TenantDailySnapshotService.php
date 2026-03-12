<?php

namespace App\Services\Reports;

use App\Models\Agent;
use App\Models\AgentCommission;
use App\Models\AgentLead;
use App\Models\Lease;
use App\Models\MaintenanceRequest;
use App\Models\Property;
use App\Models\PropertyViewing;
use App\Models\Tenant;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TenantDailySnapshotService
{
    public function capture(Tenant $tenant, Carbon|string $date): void
    {
        $date = $date instanceof Carbon ? $date->copy()->startOfDay() : Carbon::parse($date)->startOfDay();

        $this->snapshotAgentPipeline($tenant, $date);
        $this->snapshotOccupancy($tenant, $date);
        $this->snapshotCommissions($tenant, $date);
        $this->snapshotMaintenance($tenant, $date);
    }

    protected function snapshotAgentPipeline(Tenant $tenant, Carbon $date): void
    {
        $agentIds = Agent::where('tenant_id', $tenant->id)->pluck('id')->all();
        $agentIds[] = null; // aggregate row

        foreach ($agentIds as $agentId) {
            $leadQuery = AgentLead::query()->where('tenant_id', $tenant->id);
            $viewingQuery = PropertyViewing::query()->where('tenant_id', $tenant->id);
            $leaseQuery = Lease::query()->where('tenant_id', $tenant->id);

            if ($agentId) {
                $leadQuery->where('agent_id', $agentId);
                $viewingQuery->where('agent_id', $agentId);
                $leaseQuery->where(function ($q) use ($agentId) {
                    $q->where('agent_id', $agentId)
                        ->orWhereHas('property', fn ($p) => $p->where('agent_id', $agentId))
                        ->orWhereHas('unit', fn ($u) => $u->where('agent_id', $agentId));
                });
            }

            $leadStatus = $leadQuery->select('status', DB::raw('COUNT(*) as aggregate'))
                ->groupBy('status')
                ->pluck('aggregate', 'status');

            $viewingsScheduled = (clone $viewingQuery)->where('status', 'scheduled')->count();
            $viewingsCompleted = (clone $viewingQuery)->where('status', 'completed')->count();

            $leasesStarted = (clone $leaseQuery)->whereDate('start_date', '<=', $date)->count();
            $leasesActive = (clone $leaseQuery)->where('status', 'active')->count();

            $leadTotal = array_sum($leadStatus->toArray());
            $leadToViewingRate = $leadTotal > 0 ? round(($viewingsCompleted / $leadTotal) * 100, 2) : 0.0;
            $leadToLeaseRate = $leadTotal > 0 ? round(($leasesStarted / $leadTotal) * 100, 2) : 0.0;

            DB::table('report_agent_pipeline_daily')->upsert([
                'snapshot_date' => $date->toDateString(),
                'tenant_id' => $tenant->id,
                'agent_id' => $agentId,
                'leads_new' => (int) ($leadStatus['new'] ?? 0),
                'leads_in_progress' => (int) ($leadStatus['in_progress'] ?? 0),
                'leads_visited' => (int) ($leadStatus['visited'] ?? 0),
                'leads_closed' => (int) ($leadStatus['closed'] ?? 0),
                'leads_lost' => (int) ($leadStatus['lost'] ?? 0),
                'viewings_scheduled' => $viewingsScheduled,
                'viewings_completed' => $viewingsCompleted,
                'leases_started' => $leasesStarted,
                'leases_active' => $leasesActive,
                'lead_to_viewing_rate' => $leadToViewingRate,
                'lead_to_lease_rate' => $leadToLeaseRate,
                'created_at' => now(),
                'updated_at' => now(),
            ], ['snapshot_date', 'tenant_id', 'agent_id'], [
                'leads_new',
                'leads_in_progress',
                'leads_visited',
                'leads_closed',
                'leads_lost',
                'viewings_scheduled',
                'viewings_completed',
                'leases_started',
                'leases_active',
                'lead_to_viewing_rate',
                'lead_to_lease_rate',
                'updated_at',
            ]);
        }
    }

    protected function snapshotOccupancy(Tenant $tenant, Carbon $date): void
    {
        $properties = Property::where('tenant_id', $tenant->id)->pluck('id');

        $unitTotals = Unit::select('property_id', DB::raw('COUNT(*) as total'))
            ->where('tenant_id', $tenant->id)
            ->groupBy('property_id')
            ->pluck('total', 'property_id');

        $occupiedTotals = Lease::select('property_id', DB::raw('COUNT(DISTINCT unit_id) as occupied'))
            ->where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->groupBy('property_id')
            ->pluck('occupied', 'property_id');

        $rentRoll = Lease::select('property_id', DB::raw('SUM(rent_cents) as rent'))
            ->where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->groupBy('property_id')
            ->pluck('rent', 'property_id');

        $rows = [];
        $totalUnits = 0;
        $totalOccupied = 0;
        $totalRent = 0;

        foreach ($properties as $propertyId) {
            $units = (int) ($unitTotals[$propertyId] ?? 0);
            $occupied = (int) ($occupiedTotals[$propertyId] ?? 0);
            $rent = (int) ($rentRoll[$propertyId] ?? 0);
            $totalUnits += $units;
            $totalOccupied += $occupied;
            $totalRent += $rent;

            $rows[] = [
                'snapshot_date' => $date->toDateString(),
                'tenant_id' => $tenant->id,
                'property_id' => $propertyId,
                'units_total' => $units,
                'units_occupied' => $occupied,
                'occupancy_rate' => $units > 0 ? round(($occupied / $units) * 100, 2) : 0,
                'rent_roll_cents' => $rent,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Aggregate row (property_id = null)
        $rows[] = [
            'snapshot_date' => $date->toDateString(),
            'tenant_id' => $tenant->id,
            'property_id' => null,
            'units_total' => $totalUnits,
            'units_occupied' => $totalOccupied,
            'occupancy_rate' => $totalUnits > 0 ? round(($totalOccupied / $totalUnits) * 100, 2) : 0,
            'rent_roll_cents' => $totalRent,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('report_occupancy_daily')->upsert(
            $rows,
            ['snapshot_date', 'tenant_id', 'property_id'],
            ['units_total', 'units_occupied', 'occupancy_rate', 'rent_roll_cents', 'updated_at']
        );
    }

    protected function snapshotCommissions(Tenant $tenant, Carbon $date): void
    {
        $baseQuery = AgentCommission::query()
            ->where('tenant_id', $tenant->id)
            ->select('agent_id', 'status', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as amount'))
            ->groupBy('agent_id', 'status')
            ->get();

        $byAgent = [];
        foreach ($baseQuery as $row) {
            $agentId = $row->agent_id;
            $byAgent[$agentId][$row->status] = [
                'count' => (int) $row->count,
                'amount' => (float) $row->amount,
            ];
        }

        // Aggregate row (null agent)
        $aggregate = [];
        foreach ($byAgent as $agentData) {
            foreach ($agentData as $status => $values) {
                if (! isset($aggregate[$status])) {
                    $aggregate[$status] = ['count' => 0, 'amount' => 0];
                }
                $aggregate[$status]['count'] += $values['count'];
                $aggregate[$status]['amount'] += $values['amount'];
            }
        }
        $byAgent[null] = $aggregate;

        $rows = [];
        foreach ($byAgent as $agentId => $data) {
            $rows[] = [
                'snapshot_date' => $date->toDateString(),
                'tenant_id' => $tenant->id,
                'agent_id' => $agentId ?? null,
                'pending_count' => $data['pending']['count'] ?? 0,
                'pending_amount' => $data['pending']['amount'] ?? 0,
                'approved_count' => $data['approved']['count'] ?? 0,
                'approved_amount' => $data['approved']['amount'] ?? 0,
                'paid_count' => $data['paid']['count'] ?? 0,
                'paid_amount' => $data['paid']['amount'] ?? 0,
                'cancelled_count' => $data['cancelled']['count'] ?? 0,
                'cancelled_amount' => $data['cancelled']['amount'] ?? 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if ($rows) {
            foreach ($rows as &$row) {
                if ($row['agent_id'] === '') {
                    $row['agent_id'] = null;
                }
            }
            unset($row);

            DB::table('report_commissions_daily')->upsert(
                $rows,
                ['snapshot_date', 'tenant_id', 'agent_id'],
                [
                    'pending_count',
                    'pending_amount',
                    'approved_count',
                    'approved_amount',
                    'paid_count',
                    'paid_amount',
                    'cancelled_count',
                    'cancelled_amount',
                    'updated_at',
                ]
            );
        }
    }

    protected function snapshotMaintenance(Tenant $tenant, Carbon $date): void
    {
        $maintenanceQuery = MaintenanceRequest::query()->where('tenant_id', $tenant->id);

        $openNew = (clone $maintenanceQuery)->where('status', 'new')->count();
        $openInProgress = (clone $maintenanceQuery)->whereIn('status', ['open', 'in_progress'])->count();
        $openTotal = (clone $maintenanceQuery)->whereIn('status', ['new', 'open', 'in_progress'])->count();
        $resolvedToday = MaintenanceRequest::where('tenant_id', $tenant->id)
            ->whereIn('status', ['resolved', 'completed'])
            ->whereDate('updated_at', $date->toDateString())
            ->count();

        $aging = (clone $maintenanceQuery)
            ->whereIn('status', ['new', 'open', 'in_progress'])
            ->get(['created_at']);
        $avgAge = $aging->isNotEmpty()
            ? round($aging->avg(fn ($req) => $req->created_at ? $req->created_at->diffInDays(now()) : 0), 2)
            : 0.0;

        DB::table('report_maintenance_daily')->upsert([
            'snapshot_date' => $date->toDateString(),
            'tenant_id' => $tenant->id,
            'open_new' => $openNew,
            'open_in_progress' => $openInProgress,
            'open_total' => $openTotal,
            'resolved_today' => $resolvedToday,
            'avg_open_days' => $avgAge,
            'created_at' => now(),
            'updated_at' => now(),
        ], ['snapshot_date', 'tenant_id'], [
            'open_new',
            'open_in_progress',
            'open_total',
            'resolved_today',
            'avg_open_days',
            'updated_at',
        ]);
    }
}
