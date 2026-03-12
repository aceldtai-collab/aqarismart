<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attribute_fields', function (Blueprint $t) {
            // Drop the foreign key on subcategory_id first so the unique index can be dropped
            $t->dropForeign(['subcategory_id']);
            $t->dropUnique(['subcategory_id', 'key']);
        });

        Schema::table('attribute_fields', function (Blueprint $t) {
            $t->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->cascadeOnDelete();
            // Re-add the foreign key on subcategory_id
            $t->foreign('subcategory_id')->references('id')->on('subcategories')->cascadeOnDelete();
            $t->unique(['tenant_id', 'subcategory_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::table('attribute_fields', function (Blueprint $t) {
            $t->dropUnique(['tenant_id', 'subcategory_id', 'key']);
            $t->dropForeign(['tenant_id']);
            $t->dropColumn('tenant_id');
        });

        Schema::table('attribute_fields', function (Blueprint $t) {
            $t->unique(['subcategory_id', 'key']);
        });
    }
};
