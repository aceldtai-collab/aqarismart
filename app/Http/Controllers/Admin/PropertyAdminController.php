<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\View\View;

class PropertyAdminController extends Controller
{
    public function index(): View
    {
        $this->middleware(['auth','verified','superadmin']);

        $q = trim((string) request()->query('q', ''));
        $query = Property::query()
            ->with(['tenant','agent','category'])
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%$q%")
                      ->orWhere('address', 'like', "%$q%")
                      ->orWhere('city', 'like', "%$q%")
                      ->orWhere('state', 'like', "%$q%");
                });
            })
            ->orderByDesc('id');

        $properties = $query->paginate(15)->withQueryString();

        return view('admin.properties.index', [
            'properties' => $properties,
            'q' => $q,
        ]);
    }
}
