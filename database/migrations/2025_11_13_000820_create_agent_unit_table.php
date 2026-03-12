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
        Schema::create('agent_unit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('agent_id')->constrained()->cascadeOnDelete();
            $table->string('role')->default('primary');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();

            $table->unique(['unit_id', 'agent_id', 'role']);
        });

        $now = Carbon::now();
        DB::table('units')
            ->whereNotNull('agent_id')
            ->orderBy('id')
            ->chunkById(500, function ($units) use ($now) {
                $payload = [];
                foreach ($units as $unit) {
                    $payload[] = [
                        'unit_id' => $unit->id,
                        'agent_id' => $unit->agent_id,
                        'role' => 'primary',
                        'assigned_at' => $now,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                if ($payload) {
                    DB::table('agent_unit')->insert($payload);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_unit');
    }
};
