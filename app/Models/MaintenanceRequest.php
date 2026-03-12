<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\BindsToTenant;

class MaintenanceRequest extends Model
{
    use HasFactory;
    use BelongsToTenant;
    use BindsToTenant;

    protected $fillable = [
        'tenant_id',
        'property_id',
        'unit_id',
        'resident_id',
        'title',
        'details',
        'priority',
        'status',
        'attachments',
    ];

    protected $casts = [
        'attachments' => 'array',
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

    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class);
    }
}
