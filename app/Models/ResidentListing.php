<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class ResidentListing extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'code',
        'title',
        'description',
        'subcategory_id',
        'city_id',
        'area_id',
        'bedrooms',
        'bathrooms',
        'area_m2',
        'price',
        'currency',
        'location',
        'location_url',
        'lat',
        'lng',
        'photos',
        'listing_type',
        'source',
        'ad_duration_id',
        'ad_started_at',
        'ad_expires_at',
        'ad_status',
        'status',
        'moderation_notes',
        'moderated_by',
        'moderated_at',
        'payment_status',
        'payment_method',
        'payment_reference',
        'paid_at',
        'amount_paid',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'photos' => 'array',
        'price' => 'decimal:2',
        'area_m2' => 'decimal:2',
        'bathrooms' => 'decimal:1',
        'bedrooms' => 'integer',
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
        'amount_paid' => 'decimal:2',
        'ad_started_at' => 'datetime',
        'ad_expires_at' => 'datetime',
        'paid_at' => 'datetime',
        'moderated_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(State::class, 'area_id');
    }

    public function adDuration(): BelongsTo
    {
        return $this->belongsTo(AdDuration::class);
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active')
            ->where('ad_status', 'active')
            ->where(function ($q) {
                $q->whereNull('ad_expires_at')
                    ->orWhere('ad_expires_at', '>', now());
            });
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('ad_status', 'expired')
            ->orWhere(function ($q) {
                $q->where('ad_expires_at', '<=', now())
                    ->whereNotNull('ad_expires_at');
            });
    }

    public function scopeExpiringSoon(Builder $query, int $days = 2): Builder
    {
        return $query->where('ad_status', 'active')
            ->where('ad_expires_at', '<=', now()->addDays($days))
            ->where('ad_expires_at', '>', now());
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByListingType(Builder $query, string $type): Builder
    {
        return $query->where('listing_type', $type);
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('payment_status', 'paid');
    }

    // Accessors
    public function getTranslatedTitleAttribute(): ?string
    {
        $locale = app()->getLocale();
        $titles = $this->title ?? [];

        if (isset($titles[$locale])) {
            return $titles[$locale];
        }

        if (isset($titles['en'])) {
            return $titles['en'];
        }

        return is_array($titles) ? (reset($titles) ?: null) : null;
    }

    public function getTranslatedDescriptionAttribute(): ?string
    {
        $locale = app()->getLocale();
        $descriptions = $this->description ?? [];

        if (isset($descriptions[$locale])) {
            return $descriptions[$locale];
        }

        if (isset($descriptions['en'])) {
            return $descriptions['en'];
        }

        return is_array($descriptions) ? (reset($descriptions) ?: null) : null;
    }

    public function getDaysUntilExpirationAttribute(): ?int
    {
        if (!$this->ad_expires_at) {
            return null;
        }

        return max(0, now()->diffInDays($this->ad_expires_at, false));
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->ad_status === 'expired' || 
               ($this->ad_expires_at && $this->ad_expires_at->isPast());
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        if (!$this->ad_expires_at || $this->is_expired) {
            return false;
        }

        return $this->ad_expires_at->diffInDays(now()) <= 2;
    }

    public function getFirstPhotoAttribute(): ?string
    {
        $photos = $this->photos ?? [];
        return is_array($photos) && count($photos) > 0 ? $photos[0] : null;
    }

    // Helper methods
    public function startAd(): void
    {
        if ($this->ad_duration_id && $this->adDuration) {
            $this->ad_started_at = now();
            $this->ad_expires_at = now()->addDays($this->adDuration->days);
            $this->ad_status = 'active';
            $this->save();
        }
    }

    public function renewAd(int $adDurationId): void
    {
        $this->ad_duration_id = $adDurationId;
        $this->payment_status = 'pending';
        $this->ad_status = 'pending';
        $this->save();
    }

    public function markAsExpired(): void
    {
        $this->ad_status = 'expired';
        $this->save();
    }

    public function markAsPaid(float $amount, string $method = null, string $reference = null): void
    {
        $this->payment_status = 'paid';
        $this->amount_paid = $amount;
        $this->payment_method = $method;
        $this->payment_reference = $reference;
        $this->paid_at = now();
        $this->save();

        // Auto-start ad after payment
        if ($this->ad_status === 'pending') {
            $this->startAd();
        }
    }

    public function moderate(string $status, ?string $notes = null, ?int $moderatorId = null): void
    {
        $this->status = $status;
        $this->moderation_notes = $notes;
        $this->moderated_by = $moderatorId;
        $this->moderated_at = now();
        $this->save();
    }

    public static function generateCode(): string
    {
        do {
            $code = 'RL' . strtoupper(substr(uniqid(), -8));
        } while (self::where('code', $code)->exists());

        return $code;
    }
}
