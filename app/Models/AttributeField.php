<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttributeField extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'subcategory_id',
        'key',
        'label',
        'label_translations',
        'type',
        'required',
        'searchable',
        'facetable',
        'promoted',
        'options',
        'unit',
        'min',
        'max',
        'group',
        'sort',
    ];

    protected $casts = [
        'options' => 'array',
        'label_translations' => 'array',
        'required' => 'boolean',
        'searchable' => 'boolean',
        'facetable' => 'boolean',
        'promoted' => 'boolean',
    ];

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function scopeGlobal(Builder $query): Builder
    {
        return $query->whereNull('tenant_id');
    }

    public function scopeForTenant(Builder $query, int $tenantId): Builder
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeAvailableTo(Builder $query, int $tenantId): Builder
    {
        return $query->where(function ($q) use ($tenantId) {
            $q->whereNull('tenant_id')->orWhere('tenant_id', $tenantId);
        });
    }

    public function validationRule(): array
    {
        $base = $this->required ? ['required'] : ['nullable'];
        return match($this->type) {
            'int'     => array_merge($base, ['integer', $this->min ? 'min:' . $this->min : null, $this->max ? 'max:' . $this->max : null]),
            'decimal' => array_merge($base, ['numeric']),
            'string'  => array_merge($base, ['string', 'max:512']),
            'bool'    => array_merge($base, ['boolean']),
            'enum'    => array_merge($base, ['in:' . implode(',', $this->options ?? [])]),
            'multi_enum' => array_merge($base, ['array']),
            'date'    => array_merge($base, ['date']),
            'json'    => $base,
            default   => $base,
        };
    }

    public function getTranslatedLabelAttribute(): string
    {
        $locale = app()->getLocale();
        $translations = $this->label_translations ?? [];
        
        if (isset($translations[$locale])) {
            return $translations[$locale];
        }
        
        if (isset($translations['en'])) {
            return $translations['en'];
        }
        
        return $this->label ?? '';
    }
}
