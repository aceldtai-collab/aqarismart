<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add column if missing
        if (! Schema::hasColumn('units', 'tenant_id')) {
            Schema::table('units', function (Blueprint $table) {
                $table->unsignedBigInteger('tenant_id')->nullable()->after('id');
                $table->index('tenant_id');
            });
        }

        // Backfill tenant_id from related property
        $connection = Schema::getConnection()->getDriverName();
        if ($connection === 'mysql') {
            DB::statement('UPDATE `units` u JOIN `properties` p ON p.id = u.property_id SET u.tenant_id = p.tenant_id WHERE u.tenant_id IS NULL');
        } elseif ($connection === 'sqlite') {
            DB::statement('UPDATE units SET tenant_id = (SELECT tenant_id FROM properties WHERE properties.id = units.property_id) WHERE tenant_id IS NULL');
        } else {
            DB::table('units')->whereNull('tenant_id')->update([
                'tenant_id' => DB::raw('(SELECT tenant_id FROM properties WHERE properties.id = units.property_id)')
            ]);
        }

        // Try to add FK if not already present (MySQL)
        if ($connection === 'mysql') {
            $exists = DB::selectOne(
                "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'units' AND COLUMN_NAME = 'tenant_id' AND REFERENCED_TABLE_NAME = 'tenants' LIMIT 1"
            );
            if (! $exists) {
                try {
                    Schema::table('units', function (Blueprint $table) {
                        $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
                    });
                } catch (\Throwable $e) {
                    // Ignore if FK cannot be created (e.g., pre-existing)
                }
            }
        }

        // Leave column nullable if doctrine/dbal is not installed; app logic enforces tenant context
    }

    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropIndex(['tenant_id']);
            $table->dropColumn('tenant_id');
        });
    }
};
