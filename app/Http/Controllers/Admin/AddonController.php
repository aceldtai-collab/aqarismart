<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Addon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AddonController extends Controller
{
    protected array $featureOptions = [
        'users',
        'units',
        'properties',
        'agents',
        'residents',
        'leases',
        'custom_attributes',
    ];

    public function index(): View
    {
        $addons = Addon::orderBy('sort_order')->paginate(20);
        return view('admin.addons.index', compact('addons'));
    }

    public function create(): View
    {
        $featureOptions = $this->featureOptions;
        return view('admin.addons.create', compact('featureOptions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'slug'                 => 'required|string|max:255|unique:addons,slug',
            'description'          => 'nullable|string|max:1000',
            'feature'              => 'required|string|in:' . implode(',', $this->featureOptions),
            'qty'                  => 'required|integer|min:1',
            'price_monthly'        => 'required|numeric|min:0',
            'price_yearly'         => 'required|numeric|min:0',
            'stripe_price_monthly' => 'nullable|string|max:255',
            'stripe_price_yearly'  => 'nullable|string|max:255',
            'sort_order'           => 'nullable|integer|min:0',
            'is_active'            => 'nullable|boolean',
        ]);

        $validated['price_monthly'] = (int) round($validated['price_monthly'] * 100);
        $validated['price_yearly'] = (int) round($validated['price_yearly'] * 100);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        Addon::create($validated);

        return redirect()->route('admin.addons.index')
            ->with('status', __('Add-on created successfully'));
    }

    public function edit(Addon $addon): View
    {
        $featureOptions = $this->featureOptions;
        return view('admin.addons.edit', compact('addon', 'featureOptions'));
    }

    public function update(Request $request, Addon $addon): RedirectResponse
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'slug'                 => 'required|string|max:255|unique:addons,slug,' . $addon->id,
            'description'          => 'nullable|string|max:1000',
            'feature'              => 'required|string|in:' . implode(',', $this->featureOptions),
            'qty'                  => 'required|integer|min:1',
            'price_monthly'        => 'required|numeric|min:0',
            'price_yearly'         => 'required|numeric|min:0',
            'stripe_price_monthly' => 'nullable|string|max:255',
            'stripe_price_yearly'  => 'nullable|string|max:255',
            'sort_order'           => 'nullable|integer|min:0',
            'is_active'            => 'nullable|boolean',
        ]);

        $validated['price_monthly'] = (int) round($validated['price_monthly'] * 100);
        $validated['price_yearly'] = (int) round($validated['price_yearly'] * 100);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $addon->update($validated);

        return redirect()->route('admin.addons.index')
            ->with('status', __('Add-on updated successfully'));
    }

    public function destroy(Addon $addon): RedirectResponse
    {
        if ($addon->tenantAddons()->where('status', 'active')->exists()) {
            return back()->with('error', __('Cannot delete an add-on with active subscriptions'));
        }

        $addon->delete();

        return redirect()->route('admin.addons.index')
            ->with('status', __('Add-on deleted successfully'));
    }
}
