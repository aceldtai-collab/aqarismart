<?php

namespace App\Console\Commands;

use App\Models\AttributeField;
use App\Models\Tenant;
use Illuminate\Console\Command;

class SyncTenantAttributes extends Command
{
    protected $signature = 'attributes:sync-tenants
                            {--tenant= : Sync only a specific tenant ID}
                            {--dry-run : Show what would be synced without making changes}';

    protected $description = 'Sync global attribute fields to all tenants (or a specific tenant)';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $tenantId = $this->option('tenant');

        $globalFields = AttributeField::query()
            ->whereNull('tenant_id')
            ->orderBy('subcategory_id')
            ->orderBy('sort')
            ->get();

        if ($globalFields->isEmpty()) {
            $this->error('No global attribute fields found. Run AttributeFieldsSeeder first.');

            return self::FAILURE;
        }

        $this->info("Found {$globalFields->count()} global attribute fields.");

        $tenants = $tenantId
            ? Tenant::where('id', $tenantId)->get()
            : Tenant::all();

        if ($tenants->isEmpty()) {
            $this->error('No tenants found.');

            return self::FAILURE;
        }

        $this->info("Syncing to {$tenants->count()} tenant(s)...");

        if ($dryRun) {
            $this->warn('DRY RUN — no changes will be made.');
        }

        $totalCreated = 0;
        $totalUpdated = 0;

        foreach ($tenants as $tenant) {
            $created = 0;
            $updated = 0;

            foreach ($globalFields as $field) {
                $existing = AttributeField::query()
                    ->where('tenant_id', $tenant->id)
                    ->where('subcategory_id', $field->subcategory_id)
                    ->where('key', $field->key)
                    ->first();

                if ($dryRun) {
                    if ($existing) {
                        $updated++;
                    } else {
                        $created++;
                    }
                    continue;
                }

                AttributeField::query()->updateOrCreate(
                    [
                        'tenant_id' => $tenant->id,
                        'subcategory_id' => $field->subcategory_id,
                        'key' => $field->key,
                    ],
                    [
                        'label' => $field->label,
                        'label_translations' => $field->label_translations,
                        'type' => $field->type,
                        'required' => $field->required,
                        'searchable' => $field->searchable,
                        'facetable' => $field->facetable,
                        'promoted' => $field->promoted,
                        'options' => $field->options,
                        'unit' => $field->unit,
                        'min' => $field->min,
                        'max' => $field->max,
                        'group' => $field->group,
                        'sort' => $field->sort,
                    ]
                );

                if ($existing) {
                    $updated++;
                } else {
                    $created++;
                }
            }

            $this->line("  Tenant #{$tenant->id} ({$tenant->name}): {$created} created, {$updated} updated");
            $totalCreated += $created;
            $totalUpdated += $updated;
        }

        $verb = $dryRun ? 'Would create' : 'Created';
        $this->info("{$verb} {$totalCreated}, updated {$totalUpdated} total attribute fields.");

        return self::SUCCESS;
    }
}
