<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('leases') || Schema::hasColumn('leases', 'agent_id')) {
            return;
        }

        Schema::table('leases', function (Blueprint $table) {
            $table->foreignId('agent_id')
                ->nullable()
                ->after('unit_id')
                ->constrained('agents')
                ->nullOnDelete();
        });

        DB::table('leases')->orderBy('id')->chunkById(200, function ($leases) {
            $unitIds = collect($leases)->pluck('unit_id')->filter()->unique()->all();
            $propertyIds = collect($leases)->pluck('property_id')->filter()->unique()->all();

            $unitAgents = empty($unitIds)
                ? collect()
                : DB::table('units')->whereIn('id', $unitIds)->pluck('agent_id', 'id');

            $propertyAgents = empty($propertyIds)
                ? collect()
                : DB::table('properties')->whereIn('id', $propertyIds)->pluck('agent_id', 'id');

            foreach ($leases as $lease) {
                $agentId = $unitAgents[$lease->unit_id] ?? $propertyAgents[$lease->property_id] ?? null;
                if (! $agentId) {
                    continue;
                }

                DB::table('leases')->where('id', $lease->id)->update(['agent_id' => $agentId]);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('leases', 'agent_id')) {
            return;
        }

        Schema::table('leases', function (Blueprint $table) {
            $table->dropConstrainedForeignId('agent_id');
        });
    }
};
