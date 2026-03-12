<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('packages')) {
            return;
        }
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');                    // e.g. Starter, Pro, Enterprise
            $table->string('slug')->unique();          // starter, pro, enterprise
            $table->text('description')->nullable();
            $table->unsignedInteger('price_monthly')->default(0);   // cents
            $table->unsignedInteger('price_yearly')->default(0);    // cents
            $table->string('stripe_price_monthly')->nullable();
            $table->string('stripe_price_yearly')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);          // auto-assigned on signup
            $table->json('metadata')->nullable();                   // future-proof bag
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
