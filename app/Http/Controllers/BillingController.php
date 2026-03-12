<?php

namespace App\Http\Controllers;

use App\Services\Tenancy\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class BillingController extends Controller
{
    public function __construct(protected TenantManager $tenants) {}

    public function index(): View
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);

        $this->authorize('viewAny', \App\Models\Agent::class);

        $plans = config('features.plans');
        $prices = config('billing.prices');
        $stripeKey = config('services.stripe.key');

        return view('billing.index', compact('tenant', 'plans', 'prices', 'stripeKey'));
    }

    public function subscribe(Request $request): RedirectResponse
    {
        $request->validate([
            'plan' => ['required', 'string'],
            'price' => ['nullable', 'string'],
            'payment_method' => ['nullable', 'string'],
        ]);

        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);

        $this->authorize('create', \App\Models\Agent::class);

        $plan = (string) $request->input('plan');
        $price = (string) $request->input('price', '');
        $pm = (string) $request->input('payment_method', '');

        // If Cashier is available and a price is provided, try to create/update subscription
        if (! empty($price) && method_exists($tenant, 'newSubscription')) {
            if (! $tenant->subscribed()) {
                // Requires Stripe keys and a valid payment method ID
                if ($pm) {
                    $tenant->newSubscription('default', $price)->create($pm);
                } else {
                    // If no PM provided, fall back to plan flag only
                    $tenant->plan = $plan;
                    $tenant->save();
                }
            } else {
                if (method_exists($tenant, 'subscription')) {
                    $tenant->subscription('default')->swap($price);
                }
                $tenant->plan = $plan;
                $tenant->save();
            }
        } else {
            // Fallback: just set the plan (feature-gated by config)
            $tenant->plan = $plan;
            $tenant->save();
        }

        return back()->with('status', 'Plan updated');
    }

    public function cancel(): RedirectResponse
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);

        $this->authorize('create', \App\Models\Agent::class);

        if (method_exists($tenant, 'subscription') && $tenant->subscribed()) {
            $tenant->subscription('default')->cancel();
        }
        return back()->with('status', 'Subscription canceled');
    }

    public function resume(): RedirectResponse
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);

        $this->authorize('create', \App\Models\Agent::class);

        if (method_exists($tenant, 'subscription') && $tenant->subscription('default')?->onGracePeriod()) {
            $tenant->subscription('default')->resume();
        }
        return back()->with('status', 'Subscription resumed');
    }

    public function setupIntent(): JsonResponse
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);

        $this->authorize('create', \App\Models\Agent::class);

        if (! method_exists($tenant, 'createSetupIntent')) {
            abort(404);
        }
        $intent = $tenant->createSetupIntent();
        return response()->json(['client_secret' => $intent->client_secret]);
    }
}
