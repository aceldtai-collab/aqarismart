<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_attributes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $t->foreignId('attribute_field_id')->constrained('attribute_fields')->cascadeOnDelete();

            // store exactly one of the following based on attribute_fields.type
            $t->bigInteger('int_value')->nullable();
            $t->decimal('decimal_value', 13, 3)->nullable();
            $t->string('string_value', 512)->nullable();
            $t->boolean('bool_value')->nullable();
            $t->json('json_value')->nullable(); // enum/multi_enum/date/etc normalized to json

            $t->timestamps();
            $t->unique(['property_id','attribute_field_id']);
            $t->index(['attribute_field_id','int_value']);
            $t->index(['attribute_field_id','decimal_value']);
            $t->index(['attribute_field_id','string_value']);
            $t->index(['attribute_field_id','bool_value']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_attributes');
    }
};
