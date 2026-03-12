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

class Property extends Model
{
    use HasFactory;
    use BelongsToAgent;
    use BelongsToTenant;
    use BindsToTenant;
    use HasAgentAssignments;
    use HasPhotoAttributes;

    protected $fillable = [
        'tenant_id',
        'agent_id',
        'category_id',
        'name',
        'description',
        'address',
        'city',
        'state',
        'postal',
        'country',
        'country_id',
        'state_id',
        'city_id',
        'settings',
        'photos',
    ];

    protected $casts = [
        'settings' => 'array',
        'photos' => 'array',
        'name_translations' => 'array',
        'description_translations' => 'array',
        'city_translations' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Category::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Country::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(\App\Models\State::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(\App\Models\City::class);
    }

    public function getNameAttribute($value)
    {
        $t = $this->attributes['name_translations'] ?? null;
        if ($t) {
            $arr = is_string($t) ? json_decode($t, true) : $t;
            $loc = app()->getLocale();
            if (is_array($arr) && isset($arr[$loc]) && $arr[$loc] !== '') {
                return $arr[$loc];
            }
            if (is_array($arr) && isset($arr['en'])) {
                return $arr['en'];
            }
        }
        return $value;
    }

    public function setNameAttribute($value): void
    {
        $locale = app()->getLocale() ?: 'en';
        $current = $this->attributes['name_translations'] ?? [];
        $arr = is_string($current) ? (json_decode($current, true) ?: []) : (is_array($current) ? $current : []);
        if (is_array($value)) {
            $arr = array_filter($value, fn($v) => $v !== null && $v !== '');
            $this->attributes['name'] = $arr['en'] ?? reset($arr) ?: null;
        } else {
            $arr[$locale] = $value;
            if (!isset($arr['en'])) $arr['en'] = $value;
            $this->attributes['name'] = $arr['en'];
        }
        $this->attributes['name_translations'] = json_encode($arr, JSON_UNESCAPED_UNICODE);
    }

    public function getDescriptionAttribute($value)
    {
        $t = $this->attributes['description_translations'] ?? null;
        if ($t) {
            $arr = is_string($t) ? json_decode($t, true) : $t;
            $loc = app()->getLocale();
            if (is_array($arr) && ($arr[$loc] ?? '') !== '') {
                return $arr[$loc];
            }
            if (is_array($arr) && isset($arr['en'])) {
                return $arr['en'];
            }
        }
        return $value;
    }

    public function setDescriptionAttribute($value): void
    {
        $locale = app()->getLocale() ?: 'en';
        $current = $this->attributes['description_translations'] ?? [];
        $arr = is_string($current) ? (json_decode($current, true) ?: []) : (is_array($current) ? $current : []);
        if (is_array($value)) {
            $arr = array_filter($value, fn($v) => $v !== null && $v !== '');
            $this->attributes['description'] = $arr['en'] ?? reset($arr) ?: null;
        } else {
            if ($value !== null && $value !== '') {
                $arr[$locale] = $value;
                if (!isset($arr['en'])) $arr['en'] = $value;
                $this->attributes['description'] = $arr['en'];
            }
        }
        $this->attributes['description_translations'] = json_encode($arr, JSON_UNESCAPED_UNICODE);
    }

    public function getCityAttribute($value)
    {
        $t = $this->attributes['city_translations'] ?? null;
        if ($t) {
            $arr = is_string($t) ? json_decode($t, true) : $t;
            $loc = app()->getLocale();
            if (is_array($arr) && ($arr[$loc] ?? '') !== '') {
                return $arr[$loc];
            }
            if (is_array($arr) && isset($arr['en'])) {
                return $arr['en'];
            }
        }
        return $value;
    }

    public function setCityAttribute($value): void
    {
        $locale = app()->getLocale() ?: 'en';
        $current = $this->attributes['city_translations'] ?? [];
        $arr = is_string($current) ? (json_decode($current, true) ?: []) : (is_array($current) ? $current : []);
        if (is_array($value)) {
            $arr = array_filter($value, fn($v) => $v !== null && $v !== '');
            $this->attributes['city'] = $arr['en'] ?? reset($arr) ?: null;
        } else {
            if ($value !== null && $value !== '') {
                $arr[$locale] = $value;
                if (!isset($arr['en'])) $arr['en'] = $value;
                $this->attributes['city'] = $arr['en'];
            }
        }
        $this->attributes['city_translations'] = json_encode($arr, JSON_UNESCAPED_UNICODE);
    }

    protected function agentPivotTable(): string
    {
        return 'property_agent';
    }

    public function scopeForAgent(Builder $query, int $agentId): Builder
    {
        return $query->where(function (Builder $inner) use ($agentId) {
            $inner->where('agent_id', $agentId)
                ->orWhereHas('agents', fn (Builder $agents) => $agents->where('agents.id', $agentId));
        });
    }
}
