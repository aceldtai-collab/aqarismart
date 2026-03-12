<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subcategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'name_translations' => 'array',
        'description_translations' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
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
            if (is_array($arr) && isset($arr[$loc]) && $arr[$loc] !== '') {
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
}
