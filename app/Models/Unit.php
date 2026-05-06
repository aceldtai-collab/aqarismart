<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\BelongsToAgent;
use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\BindsToTenant;
use App\Models\Concerns\HasAgentAssignments;
use App\Models\Concerns\HasPhotoAttributes;

class Unit extends Model
{
    public const STATUS_SOLD = 'sold';
    public const STATUS_VACANT = 'vacant';
    public const STATUS_OCCUPIED = 'occupied';

    public const LISTING_RENT = 'rent';
    public const LISTING_SALE = 'sale';
    public const LISTING_BOTH = 'both';

    public const STATUSES = [
        self::STATUS_SOLD,
        self::STATUS_VACANT,
        self::STATUS_OCCUPIED,
    ];

    public const LISTING_TYPES = [
        self::LISTING_RENT,
        self::LISTING_SALE,
        self::LISTING_BOTH,
    ];

    public static function statusLabels(): array
    {
        return [
            self::STATUS_SOLD => __('Sold'),
            self::STATUS_VACANT => __('Vacant'),
            self::STATUS_OCCUPIED => __('Occupied'),
        ];
    }

    public static function listingTypeLabels(): array
    {
        return [
            self::LISTING_RENT => __('For Rent'),
            self::LISTING_SALE => __('For Sale'),
            self::LISTING_BOTH => __('For Rent & Sale'),
        ];
    }

    use HasFactory;
    use BelongsToAgent;
    use BelongsToTenant;
    use BindsToTenant;
    use HasAgentAssignments;
    use HasPhotoAttributes;

    protected $fillable = [
        'tenant_id',
        'agent_id',
        'property_id',
        'subcategory_id',
        'title',
        'description',
        'city_id',
        'area_id',
        'price',
        'currency',
        'lat',
        'lng',
        'bedrooms',
        'bathrooms',
        'area_m2',
        'photos',
        'code',
        'beds',
        'baths',
        'sqft',
        'market_rent',
        'status',
        'listing_type',
        'location_url',
        'location',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'price' => 'decimal:2',
        'lat' => 'decimal:7',
        'lng' => 'decimal:7'
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Subcategory::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(\App\Models\City::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(\App\Models\State::class, 'area_id');
    }

    public function unitAttributes(): HasMany
    {
        return $this->hasMany(\App\Models\UnitAttribute::class);
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }



    /**
     * Accessor for dynamic unit attributes, keyed by field key.
     * Returns array of value types based on field type.
     */
    public function getAttributesAttribute(): array
    {
        return $this->unitAttributes->mapWithKeys(function ($attr) {
            $value = null;
            if ($attr->int_value !== null) $value = $attr->int_value;
            elseif ($attr->decimal_value !== null) $value = $attr->decimal_value;
            elseif ($attr->string_value !== null) $value = $attr->string_value;
            elseif ($attr->bool_value !== null) $value = $attr->bool_value;
            elseif ($attr->json_value !== null) $value = $attr->json_value;
            return [$attr->attributeField->key => $value];
        })->toArray();
    }

    /**
     * Set title attribute: json_encode if array (e.g., multilingual), otherwise string.
     */
    public function setTitleAttribute($value): void
    {
        if (is_array($value)) {
            $this->attributes['title'] = json_encode($value);
        } else {
            $this->attributes['title'] = $value;
        }
    }

    /**
     * Set description attribute: json_encode if array (e.g., multilingual), otherwise string.
     */
    public function setDescriptionAttribute($value): void
    {
        if (is_array($value)) {
            $this->attributes['description'] = json_encode($value);
        } else {
            $this->attributes['description'] = $value;
        }
    }

    /**
     * Accessor for translated description based on current locale.
     */
    public function getTranslatedDescriptionAttribute(): ?string
    {
        $locale = app()->getLocale();
        $descriptions = $this->description ?? [];

        if (is_string($descriptions) && json_last_error() === JSON_ERROR_NONE) {
            $descriptions = json_decode($descriptions, true) ?? [];
        }

        if (isset($descriptions[$locale])) {
            return $descriptions[$locale];
        }

        if (isset($descriptions['en'])) {
            return $descriptions['en'];
        }

        return $descriptions ? $descriptions[0] ?? '' : null;
    }

    /**
     * Accessor for translated title based on current locale.
     */
    public function getTranslatedTitleAttribute(): ?string
    {
        $locale = app()->getLocale();
        $titles = $this->title ?? [];

        if (is_string($titles)) {
            $decoded = json_decode($titles, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $titles = $decoded;
            }
        }

        if (is_array($titles)) {
            return $titles[$locale]
                ?? ($titles['en'] ?? (reset($titles) ?: null));
        }

        return is_string($titles) ? $titles : null;
    }

    protected function agentPivotTable(): string
    {
        return 'agent_unit';
    }

    public function scopeForAgent(Builder $query, int $agentId): Builder
    {
        return $query->where(function (Builder $inner) use ($agentId) {
            $inner->where('agent_id', $agentId)
                ->orWhereHas('agents', fn(Builder $agents) => $agents->where('agents.id', $agentId));
        });
    }
    
    public function officialInfo()
    {
        return $this->hasOne(\App\Models\UnitOfficialInfo::class);
    }

    public function owner()
    {
        return $this->hasOne(\App\Models\UnitOwner::class);
    }
}
