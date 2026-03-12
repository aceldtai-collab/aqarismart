<?php

namespace App\Models;

use App\Models\Concerns\BelongsToAgent;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentCommission extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_PAID = 'paid';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_APPROVED,
        self::STATUS_PAID,
        self::STATUS_CANCELLED,
    ];

    use HasFactory;
    use BelongsToAgent;
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'agent_id',
        'lease_id',
        'amount',
        'rate',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'rate' => 'decimal:2',
    ];

    public static function statusLabels(): array
    {
        return [
            self::STATUS_PENDING => __('Pending'),
            self::STATUS_APPROVED => __('Approved'),
            self::STATUS_PAID => __('Paid'),
            self::STATUS_CANCELLED => __('Cancelled'),
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    /**
        * Scope commissions by an agent, falling back to the lease's agent when set.
        */
    public function scopeForAgent($query, int $agentId)
    {
        return $query->where(function ($q) use ($agentId) {
            $q->where('agent_id', $agentId)
              ->orWhereHas('lease', fn ($l) => $l->where('agent_id', $agentId));
        });
    }
}
