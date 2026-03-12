<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Billing & Plans') }}
        </h2>
    </x-slot>

    <div class="py-6" x-data="billingPage()">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if(session('status'))
                <div class="mb-4 rounded-md bg-green-50 p-3 text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="mb-4 rounded-md bg-white p-4 shadow">
                <p class="text-gray-700">{{ __('Current plan:') }} <strong>{{ __($tenant?->plan ?? 'starter') }}</strong></p>
                @if(method_exists($tenant, 'subscription'))
                    @php
                        $sub = $tenant->subscription('default');
                    @endphp
                    <div class="mt-2 text-sm text-gray-600">
                        <div>{{ __('Subscription:') }} <strong>{{ $tenant->subscribed() ? __('subscription_active') : ($sub?->onGracePeriod() ? __('canceled (grace)') : __('none')) }}</strong></div>
                        @if($sub)
                            <div>{{ __('Status:') }} {{ $sub->stripe_status }}</div>
                            <div>{{ __('Price:') }} {{ $sub->stripe_price ?? '-' }}</div>
                            @if($sub->ends_at)
                                <div>{{ __('Ends at:') }} {{ $sub->ends_at->toDateString() }}</div>
                            @endif
                            @if($sub->trial_ends_at)
                                <div>{{ __('Trial ends:') }} {{ $sub->trial_ends_at->toDateString() }}</div>
                            @endif
                        @endif
                    </div>
                    <div class="mt-3 flex gap-2">
                        @if($tenant->subscribed())
                            <form method="post" action="{{ route('billing.cancel') }}">@csrf
                                <button class="rounded-md border px-3 py-2 text-sm" type="submit">{{ __('Cancel') }}</button>
                            </form>
                        @elseif($sub?->onGracePeriod())
                            <form method="post" action="{{ route('billing.resume') }}">@csrf
                                <button class="rounded-md border px-3 py-2 text-sm" type="submit">{{ __('Resume') }}</button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>

            <div class="mb-6 rounded-md bg-white p-4 shadow" x-show="hasStripe">
                <h3 class="mb-2 text-lg font-semibold">{{ __('Payment Method') }}</h3>
                <div id="payment-element" class="mb-3"></div>
                <div class="flex items-center gap-2">
                    <button type="button" class="inline-flex items-center rounded-md bg-gray-200 px-3 py-2 hover:bg-gray-300" @click="loadElement" x-show="!elementLoaded">{{ __('Load Payment Form') }}</button>
                    <button type="button" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-white hover:bg-indigo-700" @click="confirmSetup" x-show="elementLoaded && !paymentMethodId">{{ __('Save Payment Method') }}</button>
                    <span class="text-sm text-green-700" x-show="paymentMethodId">{{ __('Payment method saved') }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                @foreach($plans as $key => $features)
                    <div class="rounded-md bg-white p-4 shadow">
                        <h3 class="mb-2 text-lg font-semibold capitalize">{{ $key }}</h3>
                        <ul class="mb-3 text-sm text-gray-600">
                            <li>{{ __('Contacts') }}: {{ $features['contacts'] ? __('Yes') : __('No') }}</li>
                            <li>{{ __('Agents') }}: {{ $features['agents'] ? __('Yes') : __('No') }}</li>
                            <li>{{ __('Files') }}: {{ $features['files'] ? __('Yes') : __('No') }}</li>
                            <li>{{ __('Users limit') }}: {{ $features['users_limit'] }}</li>
                        </ul>
                        @if($tenant && $tenant->plan === $key)
                            <p class="text-sm italic text-gray-500">{{ __('Current') }}</p>
                        @else
                            <form method="post" action="{{ url('/billing/subscribe') }}">
                                @csrf
                                <input type="hidden" name="plan" value="{{ $key }}">
                                <input type="hidden" name="price" value="{{ $prices[$key] ?? '' }}">
                                <input type="hidden" name="payment_method" x-model="paymentMethodId">
                                <button class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-white hover:bg-indigo-700" type="submit">
                                    {{ __('Choose') }} {{ ucfirst($key) }}
                                </button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
@if(!empty($stripeKey))
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        function billingPage() {
            return {
                hasStripe: true,
                stripe: null,
                elements: null,
                elementLoaded: false,
                paymentMethodId: '',
                async loadElement() {
                    if (this.elementLoaded) return;
                    this.stripe = Stripe(@js($stripeKey));
                    const resp = await fetch(@js(route('billing.setup_intent', [], false)), { credentials: 'same-origin' });
                    if (!resp.ok) {
                        throw new Error('Setup intent failed: ' + resp.status);
                    }
                    const data = await resp.json();
                    this.elements = this.stripe.elements({clientSecret: data.client_secret});
                    const paymentElement = this.elements.create('payment');
                    paymentElement.mount('#payment-element');
                    this.elementLoaded = true;
                },
                async confirmSetup() {
                    if (!this.stripe || !this.elements) return;
                    const {setupIntent, error} = await this.stripe.confirmSetup({
                        elements: this.elements,
                        confirmParams: {}
                    });
                    if (error) {
                        alert(error.message || 'Unable to save payment method');
                        return;
                    }
                    this.paymentMethodId = setupIntent.payment_method;
                }
            }
        }
    </script>
@endif
