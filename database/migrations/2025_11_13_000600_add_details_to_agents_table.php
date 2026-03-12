<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = $this->targetTable();
        if (! $table) {
            return;
        }

        Schema::table($table, function (Blueprint $table) {
            if (! Schema::hasColumn($table->getTable(), 'license_id')) {
                $table->string('license_id')->nullable()->after('email');
            }

            if (! Schema::hasColumn($table->getTable(), 'commission_rate')) {
                $table->decimal('commission_rate', 5, 2)->default(0.00)->after('license_id');
            }

            if (! Schema::hasColumn($table->getTable(), 'active')) {
                $table->boolean('active')->default(true)->after('commission_rate');
            }

            if (! Schema::hasColumn($table->getTable(), 'photo')) {
                $table->string('photo')->nullable()->after('phone');
            }
        });
    }

    public function down(): void
    {
        $table = $this->targetTable();
        if (! $table) {
            return;
        }

        Schema::table($table, function (Blueprint $table) {
            if (Schema::hasColumn($table->getTable(), 'photo')) {
                $table->dropColumn('photo');
            }

            if (Schema::hasColumn($table->getTable(), 'active')) {
                $table->dropColumn('active');
            }

            if (Schema::hasColumn($table->getTable(), 'commission_rate')) {
                $table->dropColumn('commission_rate');
            }

            if (Schema::hasColumn($table->getTable(), 'license_id')) {
                $table->dropColumn('license_id');
            }
        });
    }

    private function targetTable(): ?string
    {
        if (Schema::hasTable('agents')) {
            return 'agents';
        }

        if (Schema::hasTable('companies')) {
            return 'companies';
        }

        return null;
    }
};
