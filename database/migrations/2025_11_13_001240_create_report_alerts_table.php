<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->date('snapshot_date');
            $table->string('type');
            $table->string('severity')->default('warning');
            $table->string('title');
            $table->text('message');
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'snapshot_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_alerts');
    }
};
