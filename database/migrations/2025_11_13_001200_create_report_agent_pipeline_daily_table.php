<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_agent_pipeline_daily', function (Blueprint $table) {
            $table->id();
            $table->date('snapshot_date');
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('agent_id')->nullable()->constrained('agents')->nullOnDelete();
            $table->unsignedInteger('leads_new')->default(0);
            $table->unsignedInteger('leads_in_progress')->default(0);
            $table->unsignedInteger('leads_visited')->default(0);
            $table->unsignedInteger('leads_closed')->default(0);
            $table->unsignedInteger('leads_lost')->default(0);
            $table->unsignedInteger('viewings_scheduled')->default(0);
            $table->unsignedInteger('viewings_completed')->default(0);
            $table->unsignedInteger('leases_started')->default(0);
            $table->unsignedInteger('leases_active')->default(0);
            $table->decimal('lead_to_viewing_rate', 5, 2)->default(0);
            $table->decimal('lead_to_lease_rate', 5, 2)->default(0);
            $table->timestamps();

            $table->unique(['snapshot_date', 'tenant_id', 'agent_id'], 'report_agent_pipeline_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_agent_pipeline_daily');
    }
};
