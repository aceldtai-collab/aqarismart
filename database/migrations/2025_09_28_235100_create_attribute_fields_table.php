<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attribute_fields', function (Blueprint $t) {
            $t->id();
            $t->foreignId('subcategory_id')->constrained('subcategories')->cascadeOnDelete();
            $t->string('key');                 // e.g., "bedrooms", "zone"
            $t->string('label');               // UI label
            $t->json('label_translations')->nullable(); // e.g., {"en": "Bedrooms", "ar": "عدد الغرف"}
            $t->enum('type', ['int','decimal','string','bool','enum','multi_enum','date','json']);
            $t->boolean('required')->default(false);
            $t->boolean('searchable')->default(true); // can be filtered
            $t->boolean('facetable')->default(true);  // show as facet
            $t->boolean('promoted')->default(false);  // mirror to properties.* if exists
            $t->json('options')->nullable();          // for enum/multi_enum
            $t->string('unit')->nullable();           // e.g., "m2","year"
            $t->integer('min')->nullable();
            $t->integer('max')->nullable();
            $t->string('group')->nullable();          // UI grouping
            $t->unsignedSmallInteger('sort')->default(0);
            $t->unique(['subcategory_id','key']);
      $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attribute_fields');
    }
};
