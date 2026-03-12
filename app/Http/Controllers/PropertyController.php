<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePropertyRequest;
use App\Http\Controllers\Concerns\ResolvesAgentAssignments;
use App\Http\Controllers\Concerns\StoresPhotos;
use App\Models\Property;
use App\Services\Tenancy\TenantManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PropertyController extends Controller
{
    use ResolvesAgentAssignments;
    use StoresPhotos;

    public function __construct(protected TenantManager $tenants) {}

    public function index(): View
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('viewAny', Property::class);

        $q = trim((string) request()->query('q', ''));
        $category_id = (int) request()->query('category_id', 0);

        $query = Property::with(['agent','agents','country','state','city','category']);
        $query = $this->applyPropertySearch($query, $q);
        $query = $this->applyPropertyCategoryFilter($query, $category_id);
        if ($agentId = auth()->user()?->agent_id) {
            $query->forAgent($agentId);
        }
        $query = $query->orderBy('name');
        $properties = $query->paginate(15);
        $categories = \App\Models\Category::orderBy('name')->get();
        return view('properties.index', compact('tenant', 'properties', 'categories') + [
            'q' => $q,
            'category_id' => $category_id,
        ]);
    }

    public function create(): View
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('create', Property::class);

        return view('properties.create', $this->propertyFormData());
    }

    public function store(StorePropertyRequest $request): RedirectResponse
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('create', Property::class);

        $data = $request->validated();
        unset($data['photos']);
        $assignedAgents = $this->resolveAssignedAgents($request->input('agent_ids', []));
        $data['agent_id'] = $assignedAgents->first();
        $photos = $this->storePhotos($request, 'properties');

        $property = Property::create($data + ['photos' => $photos ?: null]);
        $property->syncAgents($assignedAgents->all());

        return redirect()->route('properties.index')->with('status', 'Property created');
    }

    public function edit(String $tenant,Property $property): View
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('update', $property);

        return view('properties.edit', array_merge(
            $this->propertyFormData(),
            compact('property')
        ));
    }

    public function update(StorePropertyRequest $request,String $tenant, Property $property): RedirectResponse
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('update', $property);
        $data = $request->validated();
        unset($data['photos']);
        $photos = $this->storePhotos($request, 'properties', $property->photos ?? []);
        $assignedAgents = $this->resolveAssignedAgents($request->input('agent_ids', []));
        $data['agent_id'] = $assignedAgents->first();
        $property->update($data + ['photos' => $photos ?: null]);
        $property->syncAgents($assignedAgents->all());
        return redirect()->route('properties.index')->with('status', 'Property updated');
    }

    public function destroy(String $tenant,Property $property): RedirectResponse
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('delete', $property);
        $property->delete();
        return redirect()->route('properties.index')->with('status', 'Property removed');
    }

    protected function propertyFormData(): array
    {
        $categories = \App\Models\Category::orderBy('name')->get();
        $agents = $this->availableAgents();

        return compact('categories', 'agents');
    }

    protected function applyPropertySearch(Builder $query, string $term): Builder
    {
        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $inner) use ($term) {
            $inner->where('name', 'like', "%$term%")
                ->orWhere('address', 'like', "%$term%")
                ->orWhere('city', 'like', "%$term%")
                ->orWhere('state', 'like', "%$term%");
        });
    }

    protected function applyPropertyCategoryFilter(Builder $query, int $categoryId): Builder
    {
        if ($categoryId <= 0) {
            return $query;
        }

        return $query->where('category_id', $categoryId);
    }

    // storePhotos provided by StoresPhotos.
}
