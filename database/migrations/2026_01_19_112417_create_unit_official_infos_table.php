<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('unit_official_infos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('unit_id')
                ->constrained('units')
                ->cascadeOnDelete()
                ->unique(); // 1:1

            // Official property info
            $table->string('directorate')->nullable();        // المديرية
            $table->string('village')->nullable();            // القرية

            $table->string('basin_number')->nullable();       // رقم الحوض
            $table->string('basin_name')->nullable();         // اسم الحوض

            $table->string('plot_number')->nullable();        // رقم القطعة
            $table->string('apartment_number')->nullable();   // رقم الشقة (إذا ينطبق)

            $table->json('areas')->nullable();                // المساحات
           
            // areas 
            
            // {
            //   "land_sqm": 500,
            //   "built_sqm": 160,
            //   "total_sqm": 660,
            //   "notes": "حسب سند التسجيل"
            // }

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_official_infos');
    }
};
