<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (!Schema::hasColumn('tenants', 'stripe_id')) {
                $table->string('stripe_id')->nullable()->after('trial_ends_at');
            }
            if (!Schema::hasColumn('tenants', 'pm_type')) {
                $table->string('pm_type')->nullable()->after('stripe_id');
            }
            if (!Schema::hasColumn('tenants', 'pm_last_four')) {
                $table->string('pm_last_four', 4)->nullable()->after('pm_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (Schema::hasColumn('tenants', 'pm_last_four')) {
                $table->dropColumn('pm_last_four');
            }
            if (Schema::hasColumn('tenants', 'pm_type')) {
                $table->dropColumn('pm_type');
            }
            if (Schema::hasColumn('tenants', 'stripe_id')) {
                $table->dropColumn('stripe_id');
            }
        });
    }
};

