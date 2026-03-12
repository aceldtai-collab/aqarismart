<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_commissions_daily', function (Blueprint $table) {
            $table->id();
            $table->date('snapshot_date');
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('agent_id')->nullable()->constrained('agents')->nullOnDelete();
            $table->unsignedInteger('pending_count')->default(0);
            $table->decimal('pending_amount', 12, 2)->default(0);
            $table->unsignedInteger('approved_count')->default(0);
            $table->decimal('approved_amount', 12, 2)->default(0);
            $table->unsignedInteger('paid_count')->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->unsignedInteger('cancelled_count')->default(0);
            $table->decimal('cancelled_amount', 12, 2)->default(0);
            $table->timestamps();

            $table->unique(['snapshot_date', 'tenant_id', 'agent_id'], 'report_commissions_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_commissions_daily');
    }
};
