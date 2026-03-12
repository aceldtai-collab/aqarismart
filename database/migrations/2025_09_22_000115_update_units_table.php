<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('units', function (Blueprint $table) {
            // Tenant ID
            if (! Schema::hasColumn('units', 'tenant_id')) {
                $table->unsignedBigInteger('tenant_id')->nullable()->after('id');
                $table->index('tenant_id');
            }

            // Company ID
            if (! Schema::hasColumn('units', 'company_id')) {
                $table->unsignedBigInteger('company_id')->nullable()->after('tenant_id');
                $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
            }

            // Listings fields after property_id
            if (! Schema::hasColumn('units', 'title')) {
                $table->json('title')->nullable()->after('property_id');
            }
            if (! Schema::hasColumn('units', 'description')) {
                $table->json('description')->nullable()->after('title');
            }
            if (! Schema::hasColumn('units', 'city_id')) {
                $table->unsignedBigInteger('city_id')->nullable()->after('description');
            }
            if (! Schema::hasColumn('units', 'area_id')) {
                $table->unsignedBigInteger('area_id')->nullable()->after('city_id');
            }
            if (! Schema::hasColumn('units', 'price')) {
                $table->decimal('price', 12, 2)->nullable()->after('area_id');
            }
            if (! Schema::hasColumn('units', 'currency')) {
                $table->string('currency', 3)->default('JOD')->after('price');
            }
            if (! Schema::hasColumn('units', 'lat')) {
                $table->decimal('lat', 10, 7)->nullable()->after('currency');
            }
            if (! Schema::hasColumn('units', 'lng')) {
                $table->decimal('lng', 10, 7)->nullable()->after('lat');
            }
            if (! Schema::hasColumn('units', 'bedrooms')) {
                $table->unsignedTinyInteger('bedrooms')->nullable()->after('lng');
            }
            if (! Schema::hasColumn('units', 'bathrooms')) {
                $table->unsignedTinyInteger('bathrooms')->nullable()->after('bedrooms');
            }
            if (! Schema::hasColumn('units', 'area_m2')) {
                $table->unsignedInteger('area_m2')->nullable()->after('bathrooms');
            }

            // Update status default to 'draft'
            $table->string('status', 20)->default('draft')->change();

            // Consolidated non-FK changes, subcategory FK in later migration

            // Indices
            $table->index(['tenant_id', 'company_id'], 'units_tenant_company_index');
            $table->index(['city_id', 'area_id'], 'units_city_area_index');
        });

        // Backfill tenant_id from properties
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

        // Add FK for tenant_id if possible (MySQL)
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
                    // Ignore
                }
            }
        }
    }

    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            // Drop indices
            $table->dropIndex(['units_city_area_index']);
            $table->dropIndex(['units_tenant_company_index']);
            $table->dropIndex(['tenant_id']);

            // Revert status default
            $table->string('status', 20)->default('vacant')->change();

            // Drop new columns
            $table->dropColumn(['area_m2', 'bathrooms', 'bedrooms', 'lng', 'lat', 'currency', 'price', 'area_id', 'city_id', 'description', 'title']);

            if (Schema::hasColumn('units', 'company_id')) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            }

            // Drop tenant_id
            if (Schema::hasColumn('units', 'tenant_id')) {
                $table->dropForeign(['tenant_id']);
                $table->dropIndex(['tenant_id']);
                $table->dropColumn('tenant_id');
            }
        });
    }
};
