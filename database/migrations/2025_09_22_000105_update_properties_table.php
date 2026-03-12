<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            // Company after settings
            if (!Schema::hasColumn('properties', 'company_id')) {
                $table->unsignedBigInteger('company_id')->nullable()->after('settings');
                $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
            }

            // Photos after company
            if (!Schema::hasColumn('properties', 'photos')) {
                $table->json('photos')->nullable()->after('company_id');
            }

            // Translations and description after 'name'
            if (!Schema::hasColumn('properties', 'name_translations')) {
                $table->json('name_translations')->nullable()->after('name');
            }
            if (!Schema::hasColumn('properties', 'description')) {
                $table->text('description')->nullable()->after('name_translations');
            }
            if (!Schema::hasColumn('properties', 'description_translations')) {
                $table->json('description_translations')->nullable()->after('description');
            }

            // Indices
            $table->index(['tenant_id', 'company_id'], 'properties_tenant_company_index');
        });

        // Backfill for description translations
        try {
            DB::table('properties')->whereNotNull('description')->update([
                'description_translations' => DB::raw("JSON_OBJECT('en', description)")
            ]);
        } catch (\Throwable $e) {
            // Ignore if JSON_OBJECT unsupported
        }
        // Backfill name translations
        try {
            DB::table('properties')->whereNotNull('name')->update([
                'name_translations' => DB::raw("JSON_OBJECT('en', name)")
            ]);
        } catch (\Throwable $e) {
            // Ignore
        }
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropIndex(['properties_tenant_company_index']);
            if (Schema::hasColumn('properties', 'company_id')) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            }
            if (Schema::hasColumn('properties', 'photos')) {
                $table->dropColumn('photos');
            }
            if (Schema::hasColumn('properties', 'description_translations')) {
                $table->dropColumn('description_translations');
            }
            if (Schema::hasColumn('properties', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('properties', 'name_translations')) {
                $table->dropColumn('name_translations');
            }
        });
    }
};
