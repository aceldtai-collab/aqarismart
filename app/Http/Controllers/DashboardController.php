<?php

namespace App\Http\Controllers;

use App\Services\Tenancy\TenantManager;

class DashboardController extends Controller
{
    public function __invoke(TenantManager $manager)
    {
        return view('dashboard', [
            'tenant' => $manager->tenant(),
        ]);
    }
}

