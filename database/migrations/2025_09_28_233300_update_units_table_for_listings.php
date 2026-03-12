<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Consolidated into update_units_table.php
        // Schema::table('units', function (Blueprint $table) {
        //     // Change subcategory_id foreign key to cascadeOnDelete
        //     $table->dropForeign(['subcategory_id']);
        //     $table->foreign('subcategory_id')->references('id')->on('subcategories')->cascadeOnDelete();
        // 
        //     // Add new fields from provided schema
        //     if (! Schema::hasColumn('units', 'title')) {
        //         $table->string('title')->nullable()->after('subcategory_id');
        //     }
        //     if (! Schema::hasColumn('units', 'description')) {
        //         $table->text('description')->nullable()->after('title');
        //     }
        //     if (! Schema::hasColumn('units', 'city_id')) {
        //         $table->unsignedBigInteger('city_id')->nullable()->after('description');
        //     }
        //     if (! Schema::hasColumn('units', 'area_id')) {
        //         $table->unsignedBigInteger('area_id')->nullable()->after('city_id');
        //     }
        //     if (! Schema::hasColumn('units', 'price')) {
        //         $table->decimal('price', 12, 2)->nullable()->after('area_id');
        //     }
        //     if (! Schema::hasColumn('units', 'currency')) {
        //         $table->string('currency', 3)->default('JOD')->after('price');
        //     }
        //     if (! Schema::hasColumn('units', 'lat')) {
        //         $table->decimal('lat', 10, 7)->nullable()->after('currency');
        //     }
        //     if (! Schema::hasColumn('units', 'lng')) {
        //         $table->decimal('lng', 10, 7)->nullable()->after('lat');
        //     }
        //     if (! Schema::hasColumn('units', 'bedrooms')) {
        //         $table->unsignedTinyInteger('bedrooms')->nullable()->after('lng');
        //     }
        //     if (! Schema::hasColumn('units', 'bathrooms')) {
        //         $table->unsignedTinyInteger('bathrooms')->nullable()->after('bedrooms');
        //     }
        //     if (! Schema::hasColumn('units', 'area_m2')) {
        //         $table->unsignedInteger('area_m2')->nullable()->after('bathrooms');
        //     }
        // 
        // 
        //     // Update status default to 'draft'
        //     $table->string('status', 20)->default('draft')->change();
        // 
        //     // Add indexes from provided schema
        //     $table->index(['tenant_id', 'subcategory_id', 'status']);
        //     $table->index(['city_id', 'area_id']);
        // });
    }

    public function down(): void
    {
        // Consolidated into update_units_table.php
        // Schema::table('units', function (Blueprint $table) {
        //     // Drop new indexes
        //     $table->dropIndex(['units_tenant_id_subcategory_id_status_index']);
        //     $table->dropIndex(['units_city_id_area_id_index']);
        // 
        //     // Drop new columns
        //     $table->dropColumn(['title', 'description', 'city_id', 'area_id', 'price', 'currency', 'lat', 'lng', 'bedrooms', 'bathrooms', 'area_m2']);
        // 
        //     // Rename media back to photos
        //     if (Schema::hasColumn('units', 'media')) {
        //         $table->renameColumn('media', 'photos');
        //     }
        // 
        //     // Revert status default
        //     $table->string('status', 20)->default('vacant')->change();
        // 
        //     // Revert subcategory_id foreign key to nullOnDelete
        //     $table->dropForeign(['subcategory_id']);
        //     $table->foreign('subcategory_id')->references('id')->on('subcategories')->nullOnDelete();
        // });
    }
};
