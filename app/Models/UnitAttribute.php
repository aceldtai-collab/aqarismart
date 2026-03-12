<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'attribute_field_id',
        'int_value',
        'decimal_value',
        'string_value',
        'bool_value',
        'json_value',
    ];

    protected $casts = [
        'bool_value' => 'boolean',
        'json_value' => 'array',
        'decimal_value' => 'decimal:3',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function attributeField(): BelongsTo
    {
        return $this->belongsTo(AttributeField::class);
    }

    public function getFormattedValueAttribute(): ?string
    {
        if ($this->int_value !== null) {
            return (string) $this->int_value;
        }

        if ($this->decimal_value !== null) {
            return number_format($this->decimal_value, 1);
        }

        if ($this->string_value !== null) {
            return $this->string_value;
        }

        if ($this->bool_value !== null) {
            return $this->bool_value ? __('Yes') : __('No');
        }

        if ($this->json_value !== null) {
            return json_encode($this->json_value);
        }

        return null;
    }
}
