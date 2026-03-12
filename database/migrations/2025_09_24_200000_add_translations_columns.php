<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (! Schema::hasColumn('categories', 'name_translations')) {
                $table->json('name_translations')->nullable()->after('name');
            }
            if (Schema::hasColumn('categories', 'description') && ! Schema::hasColumn('categories', 'description_translations')) {
                $table->json('description_translations')->nullable()->after('description');
            }
        });

        Schema::table('subcategories', function (Blueprint $table) {
            if (! Schema::hasColumn('subcategories', 'name_translations')) {
                $table->json('name_translations')->nullable()->after('name');
            }
            if (Schema::hasColumn('subcategories', 'description') && ! Schema::hasColumn('subcategories', 'description_translations')) {
                $table->json('description_translations')->nullable()->after('description');
            }
        });

        // Consolidated into properties update
        // Schema::table('properties', function (Blueprint $table) {
        //     if (!Schema::hasColumn('properties', 'name_translations')) {
        //         $table->json('name_translations')->nullable()->after('name');
        //     }
        // });

        Schema::table('companies', function (Blueprint $table) {
            if (! Schema::hasColumn('companies', 'name_translations')) {
                $table->json('name_translations')->nullable()->after('name');
            }
        });

        // Backfill existing values to English in *_translations
        try {
            // Categories
            DB::table('categories')->whereNotNull('name')->update([
                'name_translations' => DB::raw("JSON_OBJECT('en', name)")
            ]);
            if (Schema::hasColumn('categories', 'description')) {
                DB::table('categories')->whereNotNull('description')->update([
                    'description_translations' => DB::raw("JSON_OBJECT('en', description)")
                ]);
            }

            // Subcategories
            DB::table('subcategories')->whereNotNull('name')->update([
                'name_translations' => DB::raw("JSON_OBJECT('en', name)")
            ]);
            if (Schema::hasColumn('subcategories', 'description')) {
                DB::table('subcategories')->whereNotNull('description')->update([
                    'description_translations' => DB::raw("JSON_OBJECT('en', description)")
                ]);
            }

            // Properties consolidated
            // DB::table('properties')->whereNotNull('name')->update([
            //     'name_translations' => DB::raw("JSON_OBJECT('en', name)")
            // ]);

            // Companies
            DB::table('companies')->whereNotNull('name')->update([
                'name_translations' => DB::raw("JSON_OBJECT('en', name)")
            ]);
        } catch (\Throwable $e) {
            // noop: some DB engines may not support JSON_OBJECT; allow manual backfill later
        }
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'name_translations')) {
                $table->dropColumn('name_translations');
            }
            if (Schema::hasColumn('categories', 'description_translations')) {
                $table->dropColumn('description_translations');
            }
        });

        Schema::table('subcategories', function (Blueprint $table) {
            if (Schema::hasColumn('subcategories', 'name_translations')) {
                $table->dropColumn('name_translations');
            }
        });

        Schema::table('properties', function (Blueprint $table) {
            if (Schema::hasColumn('properties', 'name_translations')) {
                $table->dropColumn('name_translations');
            }
        });

        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'name_translations')) {
                $table->dropColumn('name_translations');
            }
        });
    }
};
