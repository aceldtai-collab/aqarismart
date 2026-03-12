<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Concerns\BelongsToAgent;
use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\BindsToTenant;

class Lease extends Model
{
    use HasFactory;
    use BelongsToAgent;
    use BelongsToTenant;
    use BindsToTenant;

    protected $fillable = [
        'tenant_id',
        'property_id',
        'unit_id',
        'agent_id',
        'start_date',
        'end_date',
        'rent_cents',
        'deposit_cents',
        'frequency',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function residents(): BelongsToMany
    {
        return $this->belongsToMany(Resident::class, 'lease_resident')->withPivot(['role'])->withTimestamps();
    }
}
