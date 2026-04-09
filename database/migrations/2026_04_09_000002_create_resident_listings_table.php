<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resident_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->json('title');
            $table->json('description');
            $table->foreignId('subcategory_id')->nullable()->constrained('subcategories')->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->foreignId('area_id')->nullable()->constrained('states')->nullOnDelete();
            $table->integer('bedrooms')->default(0);
            $table->decimal('bathrooms', 3, 1)->default(1.0);
            $table->decimal('area_m2', 10, 2)->nullable();
            $table->decimal('price', 12, 2);
            $table->string('currency', 3)->default('IQD');
            $table->string('location')->nullable();
            $table->text('location_url')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->json('photos')->nullable();
            $table->enum('listing_type', ['rent', 'sale']);
            $table->string('source')->default('direct_owner');
            
            // Ad duration tracking
            $table->foreignId('ad_duration_id')->nullable()->constrained('ad_durations')->nullOnDelete();
            $table->timestamp('ad_started_at')->nullable();
            $table->timestamp('ad_expires_at')->nullable();
            $table->enum('ad_status', ['pending', 'active', 'expired'])->default('pending');
            
            // Moderation fields
            $table->enum('status', ['active', 'pending', 'rejected'])->default('active');
            $table->text('moderation_notes')->nullable();
            $table->foreignId('moderated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('moderated_at')->nullable();
            
            // Payment tracking
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->decimal('amount_paid', 10, 2)->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            // Indexes for common queries
            $table->index('user_id');
            $table->index('listing_type');
            $table->index('ad_status');
            $table->index('status');
            $table->index('ad_expires_at');
            $table->index(['ad_status', 'status', 'deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resident_listings');
    }
};
