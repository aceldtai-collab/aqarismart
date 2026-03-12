<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Consolidated into properties, units, residents updates
        // Schema::table('properties', function (Blueprint $table) {
        //     $table->index(['tenant_id', 'company_id'], 'properties_tenant_company_index');
        // });
        // Schema::table('units', function (Blueprint $table) {
        //     $table->index(['tenant_id', 'company_id'], 'units_tenant_company_index');
        // });
        // Schema::table('residents', function (Blueprint $table) {
        //     $table->index(['tenant_id', 'company_id'], 'residents_tenant_company_index');
        // });
        Schema::table('leases', function (Blueprint $table) {
            $table->index(['property_id', 'unit_id'], 'leases_property_unit_index');
        });
    }

    public function down(): void
    {
        Schema::table('leases', function (Blueprint $table) {
            $table->dropIndex('leases_property_unit_index');
        });
        // Consolidated
        // Schema::table('residents', function (Blueprint $table) {
        //     $table->dropIndex('residents_tenant_company_index');
        // });
        // Schema::table('units', function (Blueprint $table) {
        //     $table->dropIndex('units_tenant_company_index');
        // });
        // Schema::table('properties', function (Blueprint $table) {
        //     $table->dropIndex('properties_tenant_company_index');
        // });
    }
};
