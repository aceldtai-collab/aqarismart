<?php

namespace App\Models\Concerns;

use App\Models\Agent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToAgent
{
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }
}
