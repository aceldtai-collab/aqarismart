<?php

namespace App\Support\Permissions\Stubs;

trait HasRolesStub
{
    public function hasRole($roles, $guard = null): bool { return false; }
    public function hasAnyRole($roles, $guard = null): bool { return false; }
    public function hasAllRoles($roles, $guard = null): bool { return false; }
    public function assignRole(...$roles): static { return $this; }
    public function syncRoles(...$roles): static { return $this; }
}
