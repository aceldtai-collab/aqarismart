<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_occupancy_daily', function (Blueprint $table) {
            $table->id();
            $table->date('snapshot_date');
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->nullable()->constrained('properties')->nullOnDelete();
            $table->unsignedInteger('units_total')->default(0);
            $table->unsignedInteger('units_occupied')->default(0);
            $table->decimal('occupancy_rate', 5, 2)->default(0);
            $table->unsignedBigInteger('rent_roll_cents')->default(0);
            $table->timestamps();

            $table->unique(['snapshot_date', 'tenant_id', 'property_id'], 'report_occupancy_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_occupancy_daily');
    }
};
