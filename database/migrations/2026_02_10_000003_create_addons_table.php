<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addons', function (Blueprint $table) {
            $table->id();
            $table->string('name');                    // e.g. "Extra 10 Agent Seats"
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('feature');                 // which feature it extends: agents, units, users …
            $table->unsignedInteger('qty');             // how many units this add-on grants
            $table->unsignedInteger('price_monthly')->default(0);  // cents
            $table->unsignedInteger('price_yearly')->default(0);   // cents
            $table->string('stripe_price_monthly')->nullable();
            $table->string('stripe_price_yearly')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addons');
    }
};
