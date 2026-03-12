<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Services\Reports\TenantDailySnapshotService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateTenantDailyReports extends Command
{
    protected $signature = 'reports:tenant-daily {date? : Snapshot date (YYYY-MM-DD)}';

    protected $description = 'Capture daily tenant snapshots for pipeline, occupancy, commissions, and maintenance.';

    public function __construct(protected TenantDailySnapshotService $snapshots)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $dateInput = $this->argument('date') ?? now()->toDateString();
        $date = Carbon::parse($dateInput)->startOfDay();

        $this->info("Generating snapshots for {$date->toDateString()}...");

        Tenant::query()->orderBy('id')->chunk(50, function ($tenants) use ($date) {
            foreach ($tenants as $tenant) {
                $this->line(" - {$tenant->name} ({$tenant->id})");
                $this->snapshots->capture($tenant, $date);
            }
        });

        $this->info('Snapshots generated.');

        return static::SUCCESS;
    }
}
