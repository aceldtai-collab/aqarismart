<?php

namespace App\Models\Concerns;

use App\Models\Agent;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasAgentAssignments
{
    use SyncsAgents;

    abstract protected function agentPivotTable(): string;

    public function agents(): BelongsToMany
    {
        return $this->belongsToMany(Agent::class, $this->agentPivotTable())
            ->withPivot(['role', 'assigned_at'])
            ->withTimestamps()
            ->orderByPivot('assigned_at', 'desc');
    }
}
