<?php

namespace App\Models;

use App\Models\Concerns\BelongsToAgent;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyViewing extends Model
{
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        self::STATUS_SCHEDULED,
        self::STATUS_COMPLETED,
        self::STATUS_CANCELLED,
    ];

    use HasFactory;
    use BelongsToAgent;
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'lead_id',
        'property_id',
        'agent_id',
        'appointment_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'appointment_at' => 'datetime',
    ];

    public static function statusLabels(): array
    {
        return [
            self::STATUS_SCHEDULED => __('Scheduled'),
            self::STATUS_COMPLETED => __('Completed'),
            self::STATUS_CANCELLED => __('Cancelled'),
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(AgentLead::class, 'lead_id');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function scopeForAgent(Builder $query, int $agentId): Builder
    {
        return $query->where('agent_id', $agentId);
    }

}
