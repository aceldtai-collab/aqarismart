<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->unsignedInteger('rent_cents');
            $table->unsignedInteger('deposit_cents')->default(0);
            $table->string('frequency')->default('monthly');
            $table->string('status')->default('active'); // active, pending, ended
            $table->timestamps();
        });

        Schema::create('lease_resident', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_id')->constrained('leases')->cascadeOnDelete();
            $table->foreignId('resident_id')->constrained('residents')->cascadeOnDelete();
            $table->string('role')->default('primary'); // primary, occupant
            $table->timestamps();
            $table->unique(['lease_id', 'resident_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lease_resident');
        Schema::dropIfExists('leases');
    }
};

