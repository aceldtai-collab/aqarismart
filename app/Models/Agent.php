<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Agent extends Model
{
    use HasFactory;
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'phone',
        'license_id',
        'commission_rate',
        'active',
        'photo',
    ];

    protected $casts = [
        'name_translations' => 'array',
        'commission_rate' => 'decimal:2',
        'active' => 'bool',
    ];

    protected $appends = [
        'photo_url',
    ];

    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class, 'property_agent')
            ->withPivot(['role', 'assigned_at'])
            ->withTimestamps();
    }

    public function units(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class, 'agent_unit')
            ->withPivot(['role', 'assigned_at'])
            ->withTimestamps();
    }

    public function leads(): HasMany
    {
        return $this->hasMany(AgentLead::class);
    }

    public function viewings(): HasMany
    {
        return $this->hasMany(PropertyViewing::class);
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(AgentCommission::class);
    }

    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class);
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

    public function getPhotoUrlAttribute(): ?string
    {
        $photo = $this->attributes['photo'] ?? null;
        if (! $photo) {
            return null;
        }

        if (Str::startsWith($photo, ['http://', 'https://'])) {
            return $photo;
        }

        return Storage::disk('public')->url($photo);
    }
}
