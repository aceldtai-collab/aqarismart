<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdDuration;
use App\Models\Country;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdDurationController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', AdDuration::class);

        $durations = AdDuration::with('country')->ordered()->get();

        return view('admin.ad-durations.index', compact('durations'));
    }

    public function create(): View
    {
        $this->authorize('create', AdDuration::class);

        return view('admin.ad-durations.create', [
            'countries' => $this->countries(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', AdDuration::class);

        $data = $request->validate([
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'days' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:3'],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $country = Country::query()->find($data['country_id']);
        $data['currency'] = $data['currency'] ?? ($country?->currency_code ?: 'IQD');
        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        AdDuration::create($data);

        return redirect()->route('admin.ad-durations.index')
            ->with('status', 'Ad duration created successfully.');
    }

    public function edit(AdDuration $adDuration): View
    {
        $this->authorize('update', $adDuration);

        return view('admin.ad-durations.edit', [
            'adDuration' => $adDuration,
            'countries' => $this->countries(),
        ]);
    }

    public function update(Request $request, AdDuration $adDuration): RedirectResponse
    {
        $this->authorize('update', $adDuration);

        $data = $request->validate([
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'days' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:3'],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $country = Country::query()->find($data['country_id']);
        $data['currency'] = $data['currency'] ?? ($country?->currency_code ?: 'IQD');
        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $adDuration->update($data);

        return redirect()->route('admin.ad-durations.index')
            ->with('status', 'Ad duration updated successfully.');
    }

    public function destroy(AdDuration $adDuration): RedirectResponse
    {
        $this->authorize('delete', $adDuration);

        $adDuration->delete();

        return redirect()->route('admin.ad-durations.index')
            ->with('status', 'Ad duration deleted successfully.');
    }

    protected function countries()
    {
        return Country::query()
            ->where('is_active', true)
            ->whereIn('iso2', ['IQ', 'JO'])
            ->orderByRaw("CASE iso2 WHEN 'IQ' THEN 0 WHEN 'JO' THEN 1 ELSE 2 END")
            ->orderBy('name_en')
            ->get(['id', 'iso2', 'name_en', 'name_ar', 'currency_code']);
    }
}
