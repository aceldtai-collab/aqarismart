<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\BelongsToAgent;
use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\BindsToTenant;

class Resident extends Model
{
    use HasFactory;
    use BelongsToAgent;
    use BelongsToTenant;
    use BindsToTenant;

    protected $fillable = [
        'tenant_id',
        'agent_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'phone_country_code',
        'notes',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function leases(): BelongsToMany
    {
        return $this->belongsToMany(Lease::class, 'lease_resident')->withPivot(['role'])->withTimestamps();
    }

    public function getNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }
}
