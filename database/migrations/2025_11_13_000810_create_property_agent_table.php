<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_agent', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('agent_id')->constrained()->cascadeOnDelete();
            $table->string('role')->default('primary');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();

            $table->unique(['property_id', 'agent_id', 'role']);
        });

        $now = Carbon::now();
        DB::table('properties')
            ->whereNotNull('agent_id')
            ->orderBy('id')
            ->chunkById(500, function ($properties) use ($now) {
                $payload = [];
                foreach ($properties as $property) {
                    $payload[] = [
                        'property_id' => $property->id,
                        'agent_id' => $property->agent_id,
                        'role' => 'primary',
                        'assigned_at' => $now,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                if ($payload) {
                    DB::table('property_agent')->insert($payload);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_agent');
    }
};
