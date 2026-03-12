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
            if (! Schema::hasColumn('properties', 'city_translations')) {
                $table->json('city_translations')->nullable()->after('city');
            }
        });

        try {
            DB::table('properties')->whereNotNull('city')->update([
                'city_translations' => DB::raw("JSON_OBJECT('en', city)")
            ]);
        } catch (\Throwable $e) {
            // ignore if JSON_OBJECT unsupported
        }
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            if (Schema::hasColumn('properties', 'city_translations')) {
                $table->dropColumn('city_translations');
            }
        });
    }
};

