<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('companies') && ! Schema::hasTable('agents')) {
            Schema::rename('companies', 'agents');
        }

        $this->renameCompanyIdColumn('properties', 'cascade');
        $this->renameCompanyIdColumn('units', 'cascade');
        $this->renameCompanyIdColumn('residents', 'cascade');
        $this->renameCompanyIdColumn('contacts', 'set null');
        $this->renameCompanyIdColumn('users', 'set null');
    }

    public function down(): void
    {
        $this->renameAgentIdColumn('users', 'set null');
        $this->renameAgentIdColumn('contacts', 'set null');
        $this->renameAgentIdColumn('residents', 'cascade');
        $this->renameAgentIdColumn('units', 'cascade');
        $this->renameAgentIdColumn('properties', 'cascade');

        if (Schema::hasTable('agents') && ! Schema::hasTable('companies')) {
            Schema::rename('agents', 'companies');
        }
    }

    private function renameCompanyIdColumn(string $table, string $onDelete): void
    {
        if (! Schema::hasTable($table) || Schema::hasColumn($table, 'agent_id') || ! Schema::hasColumn($table, 'company_id')) {
            return;
        }

        Schema::table($table, function (Blueprint $table) {
            $table->dropForeign(['company_id']);
        });

        DB::statement("ALTER TABLE `{$table}` CHANGE `company_id` `agent_id` BIGINT UNSIGNED NULL");

        Schema::table($table, function (Blueprint $table) use ($onDelete) {
            $column = $table->foreign('agent_id')->references('id')->on('agents');
            if ($onDelete === 'cascade') {
                $column->cascadeOnDelete();
            } else {
                $column->nullOnDelete();
            }
        });
    }

    private function renameAgentIdColumn(string $table, string $onDelete): void
    {
        if (! Schema::hasTable($table) || Schema::hasColumn($table, 'company_id') || ! Schema::hasColumn($table, 'agent_id')) {
            return;
        }

        Schema::table($table, function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
        });

        DB::statement("ALTER TABLE `{$table}` CHANGE `agent_id` `company_id` BIGINT UNSIGNED NULL");

        Schema::table($table, function (Blueprint $table) use ($onDelete) {
            $column = $table->foreign('company_id')->references('id')->on('companies');
            if ($onDelete === 'cascade') {
                $column->cascadeOnDelete();
            } else {
                $column->nullOnDelete();
            }
        });
    }
};
