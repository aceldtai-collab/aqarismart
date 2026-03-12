<?php

namespace App\Models\Concerns;

use Illuminate\Support\Carbon;

trait SyncsAgents
{
    public function syncAgents(array $agentIds): void
    {
        $agentIds = collect($agentIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        if ($agentIds->isEmpty()) {
            $this->agents()->detach();
            return;
        }

        $now = Carbon::now();
        $payload = [];
        foreach ($agentIds as $index => $id) {
            $payload[$id] = [
                'role' => $index === 0 ? 'primary' : 'secondary',
                'assigned_at' => $now,
            ];
        }

        $this->agents()->sync($payload);
    }
}
