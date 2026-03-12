<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\View\View;

class UnitAdminController extends Controller
{
    public function index(): View
    {
        $this->middleware(['auth','verified','superadmin']);

        $q = trim((string) request()->query('q', ''));
        $status = trim((string) request()->query('status', ''));
        $listing_type = trim((string) request()->query('listing_type', ''));

        $query = Unit::query()
            ->with(['tenant','property.agent','agent','subcategory'])
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('code', 'like', "%$q%")
                      ->orWhere('title', 'like', "%$q%")
                      ->orWhereHas('property', function ($p) use ($q) {
                          $p->where('name', 'like', "%$q%");
                      });
                });
            })
            ->when($status !== '', function ($qq) use ($status) {
                $qq->where('status', $status);
            })
            ->when(in_array($listing_type, Unit::LISTING_TYPES, true), function ($qq) use ($listing_type) {
                $qq->where('listing_type', $listing_type);
            })
            ->orderByDesc('id');

        $units = $query->paginate(15)->withQueryString();

        return view('admin.units.index', [
            'units' => $units,
            'q' => $q,
            'status' => $status,
            'listing_type' => $listing_type,
        ]);
    }
}
