<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Addon extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'feature',
        'qty',
        'price_monthly',
        'price_yearly',
        'stripe_price_monthly',
        'stripe_price_yearly',
        'is_active',
        'sort_order',
        'metadata',
    ];

    protected $casts = [
        'qty' => 'integer',
        'price_monthly' => 'integer',
        'price_yearly' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'metadata' => 'array',
    ];

    public function tenantAddons(): HasMany
    {
        return $this->hasMany(TenantAddon::class);
    }

    public function formattedMonthlyPrice(): string
    {
        return number_format($this->price_monthly / 100, 2);
    }

    public function formattedYearlyPrice(): string
    {
        return number_format($this->price_yearly / 100, 2);
    }
}
