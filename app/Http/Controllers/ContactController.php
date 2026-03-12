<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\Agent;
use App\Models\Contact;
use App\Services\Tenancy\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function __construct(protected TenantManager $tenants)
    {
        //
    }

    public function index(): View
    {
        $this->authorize('viewAny', Contact::class);
        $query = Contact::with('agent')->latest();
        if ($cid = auth()->user()?->agent_id) {
            $query->where('agent_id', $cid);
        }
        $contacts = $query->paginate(10);
        return view('contacts.index', compact('contacts'));
    }

    public function create(): View
    {
        $this->authorize('create', Contact::class);
        $agents = Agent::orderBy('name');
        if ($cid = auth()->user()?->agent_id) {
            $agents->where('id', $cid);
        }
        $agents = $agents->pluck('name', 'id');
        $tenants = [];
        if (! $this->tenants->tenant()) {
            $tenants = \App\Models\Tenant::orderBy('name')->pluck('name','id');
        }
        return view('contacts.create', compact('agents','tenants'));
    }

    public function store(ContactRequest $request): RedirectResponse
    {
        $this->authorize('create', Contact::class);
        $data = $request->validated();
        // Determine tenant_id based on context
        if ($tenant = $this->tenants->tenant()) {
            $data['tenant_id'] = $tenant->getKey();
        } else {
            $validated = $request->validate([
                'tenant_id' => ['required','exists:tenants,id']
            ]);
            $data['tenant_id'] = (int) $validated['tenant_id'];
        }
        if ($cid = auth()->user()?->agent_id) {
            $data['agent_id'] = $cid;
        }
        Contact::create($data);
        return $this->redirectToIndex('Contact created');
    }

    public function edit(String $tenant,Contact $contact): View
    {
        $this->authorize('update', $contact);
        $agents = Agent::orderBy('name');
        if ($cid = auth()->user()?->agent_id) {
            $agents->where('id', $cid);
        }
        $agents = $agents->pluck('name', 'id');
        $tenants = [];
        if (! $this->tenants->tenant()) {
            $tenants = \App\Models\Tenant::orderBy('name')->pluck('name','id');
        }
        return view('contacts.edit', compact('contact', 'agents','tenants'));
    }

    public function update(ContactRequest $request,String $tenant, Contact $contact): RedirectResponse
    {
        $this->authorize('update', $contact);
        $data = $request->validated();
        if (! $this->tenants->tenant()) {
            $validated = $request->validate([
                'tenant_id' => ['required','exists:tenants,id']
            ]);
            $data['tenant_id'] = (int) $validated['tenant_id'];
        }
        if ($cid = auth()->user()?->agent_id) {
            $data['agent_id'] = $cid;
        }
        $contact->update($data);
        return $this->redirectToIndex('Contact updated');
    }

    public function destroy(String $tenant,Contact $contact): RedirectResponse
    {
        $this->authorize('delete', $contact);
        $contact->delete();
        return back()->with('status', 'Contact deleted');
    }

    public function importForm(): View
    {
        $this->authorize('create', Contact::class);
        return view('contacts.import');
    }

    public function importStore(\Illuminate\Http\Request $request): RedirectResponse
    {
        $this->authorize('create', Contact::class);
        $rules = [
            'file' => ['required','file','mimes:csv,txt','max:2048'],
        ];
        $tenantId = null;
        if ($tenant = $this->tenants->tenant()) {
            $tenantId = $tenant->getKey();
        } else {
            $rules['tenant_id'] = ['required','exists:tenants,id'];
        }
        $validated = $request->validate($rules);
        if (! $tenantId) { $tenantId = (int) $validated['tenant_id']; }

        $file = $request->file('file')->getRealPath();
        $handle = fopen($file, 'r');
        if (!$handle) {
            return back()->with('status', 'Unable to read file');
        }

        $header = null; $created = 0; $updated = 0; $rows = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if ($header === null) { $header = array_map('trim', $row); continue; }
            $rows++;
            $data = array_combine($header, $row);
            if (!$data) { continue; }
            $name = trim($data['name'] ?? '');
            if ($name === '') { continue; }
            $email = trim($data['email'] ?? '') ?: null;
            $phone = trim($data['phone'] ?? '') ?: null;
            $agentName = trim($data['agent'] ?? $data['company'] ?? '') ?: null;

            $agentId = null;
            if (auth()->user()?->agent_id) {
                $agentId = auth()->user()->agent_id;
            } elseif ($agentName) {
                $agent = Agent::firstOrCreate(['name' => $agentName]);
                $agentId = $agent->id;
            }

            $contact = Contact::where('name', $name)->where('email', $email)->first();
            if ($contact) {
                $contact->update(['agent_id' => $agentId, 'phone' => $phone]);
                $updated++;
            } else {
                Contact::create(['tenant_id' => $tenantId, 'name' => $name, 'email' => $email, 'phone' => $phone, 'agent_id' => $agentId]);
                $created++;
            }
        }
        fclose($handle);

        return $this->redirectToIndex("Imported $rows rows: $created created, $updated updated");
    }

    protected function indexRoute(): string
    {
        return $this->tenants->tenant() ? 'contacts.index' : 'admin.contacts.index';
    }

    protected function redirectToIndex(string $status): RedirectResponse
    {
        return redirect()->route($this->indexRoute())->with('status', $status);
    }
}
