<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class AdDuration extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_en',
        'name_ar',
        'days',
        'price',
        'currency',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'days' => 'integer',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function residentListings(): HasMany
    {
        return $this->hasMany(ResidentListing::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('days');
    }

    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? $this->name_ar : $this->name_en;
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 0) . ' ' . $this->currency;
    }
}
