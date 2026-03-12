<?php

namespace App\Policies;

use App\Models\Contact;
use App\Models\User;
use App\Policies\Concerns\ChecksTenantRole;

class ContactPolicy
{
    use ChecksTenantRole;

    public function viewAny(?User $user): bool
    {
        return $this->hasPermission($user, 'view-contacts');
    }

    public function view(?User $user, Contact $contact): bool
    {
        if (! $this->hasPermission($user, 'view-contacts')) return false;
        $agentId = $this->userAgentId($user);
        if ($agentId && $contact->agent_id && $contact->agent_id !== $agentId) {
            return false;
        }
        return true;
    }

    public function create(?User $user): bool
    {
        return $this->hasPermission($user, 'create-contacts');
    }

    public function update(?User $user, Contact $contact): bool
    {
        if (! $this->hasPermission($user, 'update-contacts')) return false;
        $agentId = $this->userAgentId($user);
        if ($agentId && $contact->agent_id && $contact->agent_id !== $agentId) {
            return false;
        }
        return true;
    }

    public function delete(?User $user, Contact $contact): bool
    {
        if (! $this->hasPermission($user, 'delete-contacts')) return false;
        $agentId = $this->userAgentId($user);
        if ($agentId && $contact->agent_id && $contact->agent_id !== $agentId) {
            return false;
        }
        return true;
    }
}
