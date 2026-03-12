<?php

namespace App\Support\Billing\Stubs;

trait BillableStub
{
    public function subscribed(string $name = 'default', $price = null): bool
    {
        return false;
    }
}

