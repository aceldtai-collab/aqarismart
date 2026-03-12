<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('agent_commissions') || Schema::hasColumn('agent_commissions', 'tenant_id')) {
            return;
        }

        Schema::table('agent_commissions', function (Blueprint $table) {
            $table->foreignId('tenant_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->cascadeOnDelete();
        });

        DB::table('agent_commissions')
            ->whereNull('tenant_id')
            ->orderBy('id')
            ->chunkById(200, function ($commissions) {
                $agentIds = collect($commissions)->pluck('agent_id')->filter()->unique()->all();
                if (empty($agentIds)) {
                    return;
                }

                $tenantMap = DB::table('agents')
                    ->whereIn('id', $agentIds)
                    ->pluck('tenant_id', 'id');

                foreach ($commissions as $commission) {
                    $tenantId = $tenantMap[$commission->agent_id] ?? null;
                    if (! $tenantId) {
                        continue;
                    }

                    DB::table('agent_commissions')
                        ->where('id', $commission->id)
                        ->update(['tenant_id' => $tenantId]);
                }
            });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `agent_commissions` MODIFY `tenant_id` BIGINT UNSIGNED NOT NULL');
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('agent_commissions', 'tenant_id')) {
            return;
        }

        Schema::table('agent_commissions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tenant_id');
        });
    }
};
