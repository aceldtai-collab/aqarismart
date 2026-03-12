<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('units', function (Blueprint $table) {
            if (! Schema::hasColumn('units', 'listing_type')) {
                $table->string('listing_type', 20)->default('rent')->after('status');
                $table->index('listing_type');
            }
        });

        $connection = Schema::getConnection()->getDriverName();
        if ($connection === 'sqlite') {
            return;
        }

        Schema::table('units', function (Blueprint $table) {
            $table->dropForeign(['property_id']);
        });

        Schema::table('units', function (Blueprint $table) {
            $table->unsignedBigInteger('property_id')->nullable()->change();
            $table->foreign('property_id')->references('id')->on('properties')->nullOnDelete();
        });
    }

    public function down(): void
    {
        $connection = Schema::getConnection()->getDriverName();
        if ($connection !== 'sqlite') {
            Schema::table('units', function (Blueprint $table) {
                $table->dropForeign(['property_id']);
            });

            Schema::table('units', function (Blueprint $table) {
                $table->unsignedBigInteger('property_id')->nullable(false)->change();
                $table->foreign('property_id')->references('id')->on('properties')->cascadeOnDelete();
            });
        }

        Schema::table('units', function (Blueprint $table) {
            if (Schema::hasColumn('units', 'listing_type')) {
                $table->dropIndex(['listing_type']);
                $table->dropColumn('listing_type');
            }
        });
    }
};
