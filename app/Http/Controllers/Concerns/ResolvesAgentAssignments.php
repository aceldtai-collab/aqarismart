<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Agent;
use Illuminate\Support\Collection;

trait ResolvesAgentAssignments
{
    protected function prepareAgentAssignments(array $agentIds): Collection
    {
        return collect($agentIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();
    }

    protected function resolveAssignedAgents(array $agentIds): Collection
    {
        $assignedAgents = $this->prepareAgentAssignments($agentIds);
        if ($cid = auth()->user()?->agent_id) {
            $assignedAgents = collect([$cid]);
        }

        return $assignedAgents;
    }

    protected function availableAgents(): Collection
    {
        $agentId = auth()->user()?->agent_id;
        return Agent::query()
            ->orderBy('name')
            ->when($agentId, fn ($q) => $q->where('id', $agentId))
            ->pluck('name', 'id');
    }
}
