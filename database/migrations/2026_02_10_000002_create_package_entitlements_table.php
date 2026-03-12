<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_entitlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            $table->string('feature');          // e.g. units, agents, properties, leases, maintenance, contacts, files
            $table->string('type');             // 'boolean' | 'limit'
            $table->unsignedInteger('limit_value')->nullable();  // null = unlimited when type=limit
            $table->timestamps();

            $table->unique(['package_id', 'feature']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_entitlements');
    }
};
