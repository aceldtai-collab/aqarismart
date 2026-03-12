<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Addon;
use App\Models\Package;
use App\Models\Tenant;
use App\Models\TenantAddon;
use App\Services\Billing\PackageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenantSubscriptionController extends Controller
{
    public function __construct(protected PackageService $packages) {}

    public function show(Tenant $tenant): View
    {
        $tenant->load(['activeSubscription.package', 'activeAddons.addon', 'subscriptions.package']);

        $allPackages = Package::where('is_active', true)->orderBy('sort_order')->get();
        $allAddons = Addon::where('is_active', true)->orderBy('sort_order')->get();
        $usage = $this->packages->usageSummary($tenant);

        return view('admin.tenants.subscription', compact('tenant', 'allPackages', 'allAddons', 'usage'));
    }

    public function subscribe(Request $request, Tenant $tenant): RedirectResponse
    {
        $request->validate([
            'package_id'    => 'required|exists:packages,id',
            'billing_cycle' => 'required|in:monthly,yearly',
            'trial_days'    => 'nullable|integer|min:0',
        ]);

        $package = Package::findOrFail($request->package_id);
        $trialDays = $request->integer('trial_days') ?: null;

        $this->packages->subscribe($tenant, $package, $request->billing_cycle, $trialDays);

        return back()->with('status', __('Subscription updated successfully'));
    }

    public function cancel(Tenant $tenant): RedirectResponse
    {
        $this->packages->cancel($tenant);
        return back()->with('status', __('Subscription canceled'));
    }

    public function attachAddon(Request $request, Tenant $tenant): RedirectResponse
    {
        $request->validate([
            'addon_id'      => 'required|exists:addons,id',
            'qty'           => 'required|integer|min:1',
            'billing_cycle' => 'required|in:monthly,yearly',
        ]);

        $addon = Addon::findOrFail($request->addon_id);
        $this->packages->attachAddon($tenant, $addon, $request->integer('qty'), $request->billing_cycle);

        return back()->with('status', __('Add-on attached successfully'));
    }

    public function removeAddon(Tenant $tenant, TenantAddon $tenantAddon): RedirectResponse
    {
        abort_if($tenantAddon->tenant_id !== $tenant->id, 403);

        $this->packages->removeAddon($tenantAddon);

        return back()->with('status', __('Add-on removed'));
    }
}
