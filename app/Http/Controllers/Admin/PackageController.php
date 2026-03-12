<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PackageEntitlement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PackageController extends Controller
{
    protected array $featureOptions = [
        'users'       => 'limit',
        'units'       => 'limit',
        'properties'  => 'limit',
        'agents'      => 'limit',
        'residents'   => 'limit',
        'leases'      => 'limit',
        'contacts'    => 'boolean',
        'files'       => 'boolean',
        'maintenance'        => 'boolean',
        'custom_attributes'  => 'boolean',
    ];

    public function index(): View
    {
        $packages = Package::withCount('activeSubscriptions')
            ->orderBy('sort_order')
            ->paginate(20);

        return view('admin.packages.index', compact('packages'));
    }

    public function create(): View
    {
        $featureOptions = $this->featureOptions;
        return view('admin.packages.create', compact('featureOptions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'slug'                 => 'required|string|max:255|unique:packages,slug',
            'description'          => 'nullable|string|max:1000',
            'price_monthly'        => 'required|numeric|min:0',
            'price_yearly'         => 'required|numeric|min:0',
            'stripe_price_monthly' => 'nullable|string|max:255',
            'stripe_price_yearly'  => 'nullable|string|max:255',
            'sort_order'           => 'nullable|integer|min:0',
            'is_active'            => 'nullable|boolean',
            'is_default'           => 'nullable|boolean',
            'entitlements'         => 'nullable|array',
        ]);

        // Convert dollars to cents
        $validated['price_monthly'] = (int) round($validated['price_monthly'] * 100);
        $validated['price_yearly'] = (int) round($validated['price_yearly'] * 100);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_default'] = $request->boolean('is_default');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        // If marking as default, unset other defaults
        if ($validated['is_default']) {
            Package::where('is_default', true)->update(['is_default' => false]);
        }

        $package = Package::create($validated);

        // Save entitlements
        $this->syncEntitlements($package, $request->input('entitlements', []));

        return redirect()->route('admin.packages.index')
            ->with('status', __('Package created successfully'));
    }

    public function edit(Package $package): View
    {
        $package->load('entitlements');
        $featureOptions = $this->featureOptions;

        // Build a lookup: feature => entitlement row
        $currentEntitlements = $package->entitlements->keyBy('feature');

        return view('admin.packages.edit', compact('package', 'featureOptions', 'currentEntitlements'));
    }

    public function update(Request $request, Package $package): RedirectResponse
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'slug'                 => 'required|string|max:255|unique:packages,slug,' . $package->id,
            'description'          => 'nullable|string|max:1000',
            'price_monthly'        => 'required|numeric|min:0',
            'price_yearly'         => 'required|numeric|min:0',
            'stripe_price_monthly' => 'nullable|string|max:255',
            'stripe_price_yearly'  => 'nullable|string|max:255',
            'sort_order'           => 'nullable|integer|min:0',
            'is_active'            => 'nullable|boolean',
            'is_default'           => 'nullable|boolean',
            'entitlements'         => 'nullable|array',
        ]);

        $validated['price_monthly'] = (int) round($validated['price_monthly'] * 100);
        $validated['price_yearly'] = (int) round($validated['price_yearly'] * 100);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_default'] = $request->boolean('is_default');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        if ($validated['is_default']) {
            Package::where('is_default', true)->where('id', '!=', $package->id)->update(['is_default' => false]);
        }

        $package->update($validated);

        $this->syncEntitlements($package, $request->input('entitlements', []));

        return redirect()->route('admin.packages.index')
            ->with('status', __('Package updated successfully'));
    }

    public function destroy(Package $package): RedirectResponse
    {
        if ($package->activeSubscriptions()->exists()) {
            return back()->with('error', __('Cannot delete a package with active subscriptions'));
        }

        $package->entitlements()->delete();
        $package->delete();

        return redirect()->route('admin.packages.index')
            ->with('status', __('Package deleted successfully'));
    }

    protected function syncEntitlements(Package $package, array $entitlements): void
    {
        $package->entitlements()->delete();

        foreach ($entitlements as $feature => $data) {
            if (empty($data['enabled'])) {
                continue;
            }

            $type = $this->featureOptions[$feature] ?? 'boolean';

            $package->entitlements()->create([
                'feature' => $feature,
                'type' => $type,
                'limit_value' => $type === 'limit' ? ($data['limit'] ?? null) : null,
            ]);
        }
    }
}
