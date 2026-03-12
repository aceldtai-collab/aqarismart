<?php

namespace App\Support\Billing;

if (class_exists(\Laravel\Cashier\Billable::class)) {
    trait UsesBillable
    {
        use \Laravel\Cashier\Billable;
    }
} else {
    trait UsesBillable
    {
        use \App\Support\Billing\Stubs\BillableStub;
    }
}

