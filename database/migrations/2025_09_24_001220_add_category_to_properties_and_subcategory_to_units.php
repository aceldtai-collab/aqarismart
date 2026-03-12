<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('company_id')->constrained('categories')->nullOnDelete();
            $table->index(['tenant_id', 'company_id', 'category_id']);
        });

        Schema::table('units', function (Blueprint $table) {
            $table->foreignId('subcategory_id')->nullable()->after('property_id')->constrained('subcategories')->nullOnDelete();
            $table->index(['tenant_id', 'company_id', 'subcategory_id']);
        });
    }

    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropIndex(['units_tenant_id_company_id_subcategory_id_index']);
            $table->dropConstrainedForeignId('subcategory_id');
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->dropIndex(['properties_tenant_id_company_id_category_id_index']);
            $table->dropConstrainedForeignId('category_id');
        });
    }
};
