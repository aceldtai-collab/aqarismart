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
            if (! Schema::hasColumn('properties', 'description')) {
                $table->text('description')->nullable()->after('name');
            }
            if (! Schema::hasColumn('properties', 'description_translations')) {
                $table->json('description_translations')->nullable()->after('description');
            }
        });

        try {
            DB::table('properties')->whereNotNull('description')->update([
                'description_translations' => DB::raw("JSON_OBJECT('en', description)")
            ]);
        } catch (\Throwable $e) {
            // ignore if JSON_OBJECT unsupported
        }
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            if (Schema::hasColumn('properties', 'description_translations')) {
                $table->dropColumn('description_translations');
            }
            if (Schema::hasColumn('properties', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};

