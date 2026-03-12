<?php

namespace App\Models;

use App\Models\Concerns\BelongsToAgent;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;
    use BelongsToAgent;
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'agent_id', 'name', 'email', 'phone'
    ];

    // agent relationship provided by BelongsToAgent.
}
