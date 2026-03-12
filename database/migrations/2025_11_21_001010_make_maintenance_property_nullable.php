<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $connection = Schema::getConnection()->getDriverName();
        if ($connection === 'sqlite') {
            return;
        }

        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->dropForeign(['property_id']);
        });

        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('property_id')->nullable()->change();
            $table->foreign('property_id')->references('id')->on('properties')->nullOnDelete();
        });
    }

    public function down(): void
    {
        $connection = Schema::getConnection()->getDriverName();
        if ($connection === 'sqlite') {
            return;
        }

        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->dropForeign(['property_id']);
        });

        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('property_id')->nullable(false)->change();
            $table->foreign('property_id')->references('id')->on('properties')->cascadeOnDelete();
        });
    }
};
