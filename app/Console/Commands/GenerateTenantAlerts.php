<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Services\Reports\TenantInsightService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateTenantAlerts extends Command
{
    protected $signature = 'reports:tenant-alerts {date? : Snapshot date (YYYY-MM-DD)}';

    protected $description = 'Analyze snapshot tables and log tenant insights/alerts.';

    public function __construct(protected TenantInsightService $insights)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $date = Carbon::parse($this->argument('date') ?? now())->startOfDay();
        $this->info("Scanning snapshots for {$date->toDateString()}...");

        Tenant::query()->orderBy('id')->chunk(50, function ($tenants) use ($date) {
            foreach ($tenants as $tenant) {
                $alerts = $this->insights->analyze($tenant, $date);
                foreach ($alerts as $alert) {
                    $this->line(" - {$tenant->name}: {$alert->title}");
                }
            }
        });

        $this->info('Alert scan complete.');

        return static::SUCCESS;
    }
}
