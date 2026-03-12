<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResidentRequest;
use App\Models\Resident;
use App\Services\Tenancy\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ResidentController extends Controller
{
    public function __construct(protected TenantManager $tenants) {}

    public function index(): View
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('viewAny', Resident::class);

        $query = Resident::orderBy('last_name')->orderBy('first_name');
        if ($cid = auth()->user()?->agent_id) {
            $query->where('agent_id', $cid);
        }
        $residents = $query->paginate(15);
        return view('residents.index', compact('residents'));
    }

    public function create(): View
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('create', Resident::class);
        return view('residents.create');
    }

    public function store(StoreResidentRequest $request): RedirectResponse
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('create', Resident::class);

        $data = $request->validated();
        if ($cid = auth()->user()?->agent_id) {
            $data['agent_id'] = $cid;
        }
        Resident::create($data);
        return redirect()->route('residents.index')->with('status', 'Resident created');
    }

    public function edit(String $tenant, Resident $resident): View
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('update', $resident);
        return view('residents.edit', compact('resident'));
    }

    public function update(StoreResidentRequest $request,String $tenant, Resident $resident): RedirectResponse
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('update', $resident);
        $data = $request->validated();
        if ($cid = auth()->user()?->agent_id) {
            $data['agent_id'] = $cid;
        }
        $resident->update($data);
        return redirect()->route('residents.index')->with('status', 'Resident updated');
    }

    public function destroy(Resident $resident): RedirectResponse
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('delete', $resident);
        $resident->delete();
        return redirect()->route('residents.index')->with('status', 'Resident removed');
    }
}
