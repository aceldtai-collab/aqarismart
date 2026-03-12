<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_maintenance_daily', function (Blueprint $table) {
            $table->id();
            $table->date('snapshot_date');
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('open_new')->default(0);
            $table->unsignedInteger('open_in_progress')->default(0);
            $table->unsignedInteger('open_total')->default(0);
            $table->unsignedInteger('resolved_today')->default(0);
            $table->decimal('avg_open_days', 5, 2)->default(0);
            $table->timestamps();

            $table->unique(['snapshot_date', 'tenant_id'], 'report_maintenance_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_maintenance_daily');
    }
};
