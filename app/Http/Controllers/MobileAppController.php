<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Unit;
use Illuminate\Contracts\View\View;

class MobileAppController extends Controller
{
    public function home(): View
    {
        return view('mobile.home');
    }

    public function login(): View
    {
        return view('mobile.auth.login');
    }

    public function register(): View
    {
        return view('mobile.auth.register');
    }

    public function marketplace(): View
    {
        return view('mobile.marketplace');
    }

    public function dashboard(): View
    {
        return view('mobile.dashboard');
    }

    public function units(): View
    {
        return view('mobile.units.index');
    }

    public function createUnit(): View
    {
        return view('mobile.units.create');
    }

    public function showUnit(Unit $unit): View
    {
        $unit->load([
            'property',
            'city',
            'subcategory',
            'agent',
            'unitAttributes.attributeField',
        ]);

        return view('mobile.units.show', compact('unit'));
    }

    public function editUnit(Unit $unit): View
    {
        return view('mobile.units.edit', compact('unit'));
    }

    public function tenants(): View
    {
        return view('mobile.tenants.index');
    }

    public function showTenant(Tenant $tenant): View
    {
        return view('mobile.tenants.show', compact('tenant'));
    }

    public function profile(): View
    {
        return view('mobile.profile');
    }

    public function about(): View
    {
        return view('mobile.about');
    }
}
