<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'subcategory_id']);
            $table->index(['tenant_id', 'listing_type']);
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->index(['tenant_id', 'category_id']);
            $table->index(['tenant_id', 'agent_id']);
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->index(['tenant_id', 'created_at']);
        });

        Schema::table('agent_leads', function (Blueprint $table) {
            $table->index(['tenant_id', 'agent_id']);
        });
    }

    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'status']);
            $table->dropIndex(['tenant_id', 'subcategory_id']);
            $table->dropIndex(['tenant_id', 'listing_type']);
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'category_id']);
            $table->dropIndex(['tenant_id', 'agent_id']);
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'created_at']);
        });

        Schema::table('agent_leads', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'agent_id']);
        });
    }
};