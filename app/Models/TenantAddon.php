<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantAddon extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'addon_id',
        'qty',
        'billing_cycle',
        'status',
        'stripe_subscription_id',
        'starts_at',
        'ends_at',
        'canceled_at',
        'metadata',
    ];

    protected $casts = [
        'qty' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'canceled_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function addon(): BelongsTo
    {
        return $this->belongsTo(Addon::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Total extra units this row grants = addon.qty * purchased qty.
     */
    public function grantedUnits(): int
    {
        return ($this->addon->qty ?? 0) * $this->qty;
    }
}
