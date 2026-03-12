<?php

namespace App\View\Components;

use App\Services\Tenancy\TenantManager;
use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        $tenant = app(TenantManager::class)->tenant();

        if ($tenant && !request()->is('admin*')) {
            return view('layouts.tenant');
        }

        return view('layouts.app');
    }
}
