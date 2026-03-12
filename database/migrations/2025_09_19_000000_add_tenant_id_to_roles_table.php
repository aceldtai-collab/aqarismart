<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('roles')) {
            return;
        }

        Schema::table('roles', function (Blueprint $table) {
            if (!Schema::hasColumn('roles', 'tenant_id')) {
                $table->unsignedBigInteger('tenant_id')->nullable()->after('id');
                $table->index('tenant_id', 'roles_team_foreign_key_index');
            }
        });

        // Adjust unique indexes: remove unique(name, guard_name) if present, add unique(tenant_id, name, guard_name)
        try {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropUnique('roles_name_guard_name_unique');
            });
        } catch (Throwable $e) {
            // ignore if the index doesn't exist
        }

        // Ensure composite unique exists
        try {
            Schema::table('roles', function (Blueprint $table) {
                $table->unique(['tenant_id', 'name', 'guard_name'], 'roles_tenant_name_guard_unique');
            });
        } catch (Throwable $e) {
            // ignore if already added
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('roles')) {
            return;
        }

        // Drop composite unique
        try {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropUnique('roles_tenant_name_guard_unique');
            });
        } catch (Throwable $e) {
            // ignore
        }

        Schema::table('roles', function (Blueprint $table) {
            if (Schema::hasColumn('roles', 'tenant_id')) {
                $table->dropIndex('roles_team_foreign_key_index');
                $table->dropColumn('tenant_id');
            }
        });

        // Restore original unique(name, guard_name)
        try {
            Schema::table('roles', function (Blueprint $table) {
                $table->unique(['name', 'guard_name'], 'roles_name_guard_name_unique');
            });
        } catch (Throwable $e) {
            // ignore
        }
    }
};

