<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Services\Reports\TenantSnapshotQuery;
use App\Services\Tenancy\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExportController extends Controller
{
    public function __construct(
        protected TenantManager $tenants,
        protected TenantSnapshotQuery $snapshots,
    ) {}

    protected function resolveContext(Request $request): array
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);

        $user = $request->user();
        $agentScoped = (bool) $user?->agent_id;
        $agentId = $agentScoped ? $user->agent_id : $request->integer('agent_id');
        if (! $agentScoped && $agentId) {
            $exists = Agent::where('tenant_id', $tenant->id)->where('id', $agentId)->exists();
            if (! $exists) {
                $agentId = null;
            }
        }
        $days = $request->integer('days', 30);
        $days = $days > 0 ? min(max($days, 7), 90) : 30;

        return [$tenant, $agentId, $days];
    }

    public function pipelineCsv(Request $request): StreamedResponse
    {
        [$tenant, $agentId, $days] = $this->resolveContext($request);
        $data = collect($this->snapshots->agentPipelineTrend($tenant, $agentId, $days));

        return Response::streamDownload(function () use ($data) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'New Leads', 'In Progress', 'Visited', 'Viewings Scheduled', 'Viewings Completed', 'Leases Started', 'Leases Active', 'Lead->Viewing %', 'Lead->Lease %']);
            foreach ($data as $row) {
                fputcsv($handle, [
                    $row['date'],
                    $row['leads_new'],
                    $row['leads_in_progress'],
                    $row['leads_visited'],
                    $row['viewings_scheduled'],
                    $row['viewings_completed'],
                    $row['leases_started'],
                    $row['leases_active'],
                    $row['lead_to_viewing_rate'],
                    $row['lead_to_lease_rate'],
                ]);
            }
            fclose($handle);
        }, 'pipeline-report.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function occupancyCsv(Request $request): StreamedResponse
    {
        [$tenant, $agentId, $days] = $this->resolveContext($request);
        $data = collect($this->snapshots->occupancyTrend($tenant, null, $days));

        return Response::streamDownload(function () use ($data) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Units Total', 'Units Occupied', 'Occupancy Rate', 'Rent Roll']);
            foreach ($data as $row) {
                fputcsv($handle, [
                    $row['date'],
                    $row['units_total'],
                    $row['units_occupied'],
                    $row['occupancy_rate'],
                    number_format(($row['rent_roll_cents'] ?? 0)/100, 2, '.', ''),
                ]);
            }
            fclose($handle);
        }, 'occupancy-report.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function pipelinePdf(Request $request)
    {
        [$tenant, $agentId, $days] = $this->resolveContext($request);
        $data = collect($this->snapshots->agentPipelineTrend($tenant, $agentId, $days));

        $html = view('reports.exports.pipeline-pdf', [
            'tenant' => $tenant,
            'rows' => $data,
            'days' => $days,
        ])->render();

        $pdf = new \Mpdf\Mpdf();
        $pdf->WriteHTML($html);

        return response($pdf->Output('', 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="pipeline-report.pdf"',
        ]);
    }

    public function occupancyPdf(Request $request)
    {
        [$tenant, , $days] = $this->resolveContext($request);
        $data = collect($this->snapshots->occupancyTrend($tenant, null, $days));

        $html = view('reports.exports.occupancy-pdf', [
            'tenant' => $tenant,
            'rows' => $data,
            'days' => $days,
        ])->render();

        $pdf = new \Mpdf\Mpdf();
        $pdf->WriteHTML($html);

        return response($pdf->Output('', 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="occupancy-report.pdf"',
        ]);
    }
}
