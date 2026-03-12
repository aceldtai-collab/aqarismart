<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('property_viewings') || Schema::hasColumn('property_viewings', 'tenant_id')) {
            return;
        }

        Schema::table('property_viewings', function (Blueprint $table) {
            $table->foreignId('tenant_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->cascadeOnDelete();
        });

        DB::table('property_viewings')
            ->whereNull('tenant_id')
            ->orderBy('id')
            ->chunkById(200, function ($viewings) {
                $propertyIds = collect($viewings)->pluck('property_id')->filter()->unique()->all();
                $leadIds = collect($viewings)->pluck('lead_id')->filter()->unique()->all();

                $propertyTenants = empty($propertyIds)
                    ? collect()
                    : DB::table('properties')->whereIn('id', $propertyIds)->pluck('tenant_id', 'id');

                $leadTenants = empty($leadIds)
                    ? collect()
                    : DB::table('agent_leads')->whereIn('id', $leadIds)->pluck('tenant_id', 'id');

                foreach ($viewings as $viewing) {
                    $tenantId = $propertyTenants[$viewing->property_id] ?? $leadTenants[$viewing->lead_id] ?? null;
                    if (! $tenantId) {
                        continue;
                    }

                    DB::table('property_viewings')
                        ->where('id', $viewing->id)
                        ->update(['tenant_id' => $tenantId]);
                }
            });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `property_viewings` MODIFY `tenant_id` BIGINT UNSIGNED NOT NULL');
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('property_viewings', 'tenant_id')) {
            return;
        }

        Schema::table('property_viewings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tenant_id');
        });
    }
};
