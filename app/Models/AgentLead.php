<?php

namespace App\Models;

use App\Models\Concerns\BelongsToAgent;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgentLead extends Model
{
    public const STATUS_NEW = 'new';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_VISITED = 'visited';
    public const STATUS_CLOSED = 'closed';
    public const STATUS_LOST = 'lost';

    public const STATUSES = [
        self::STATUS_NEW,
        self::STATUS_IN_PROGRESS,
        self::STATUS_VISITED,
        self::STATUS_CLOSED,
        self::STATUS_LOST,
    ];

    use HasFactory;
    use BelongsToAgent;
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'agent_id',
        'name',
        'email',
        'phone',
        'source',
        'status',
        'notes',
    ];

    public static function statusLabels(): array
    {
        return [
            self::STATUS_NEW => __('New'),
            self::STATUS_IN_PROGRESS => __('In Progress'),
            self::STATUS_VISITED => __('Visited'),
            self::STATUS_CLOSED => __('Closed'),
            self::STATUS_LOST => __('Lost'),
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function viewings(): HasMany
    {
        return $this->hasMany(PropertyViewing::class, 'lead_id');
    }

    public function scopeForAgent(Builder $query, int $agentId): Builder
    {
        return $query->where('agent_id', $agentId);
    }
}
