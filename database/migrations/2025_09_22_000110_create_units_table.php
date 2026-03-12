<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->string('code');
            $table->unsignedInteger('beds')->default(0);
            $table->decimal('baths', 3, 1)->default(1.0);
            $table->unsignedInteger('sqft')->nullable();
            $table->unsignedInteger('market_rent')->default(0); // cents

            $table->string('status')->default('vacant'); // vacant, occupied, notice
            $table->json('photos')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};

