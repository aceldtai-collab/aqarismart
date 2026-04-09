<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdDuration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdDurationController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', AdDuration::class);

        $durations = AdDuration::ordered()->get();

        return view('admin.ad-durations.index', compact('durations'));
    }

    public function create(): View
    {
        $this->authorize('create', AdDuration::class);

        return view('admin.ad-durations.create');
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
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $data['currency'] = $data['currency'] ?? 'IQD';
        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        AdDuration::create($data);

        return redirect()->route('admin.ad-durations.index')
            ->with('status', 'Ad duration created successfully.');
    }

    public function edit(AdDuration $adDuration): View
    {
        $this->authorize('update', $adDuration);

        return view('admin.ad-durations.edit', compact('adDuration'));
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
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $data['currency'] = $data['currency'] ?? 'IQD';
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
}
