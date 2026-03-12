<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ __('Edit Commission') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-md bg-white p-6 shadow">
                @php
                    $commissionId = $commission?->getKey();
                @endphp
                <form method="post" action="{{ route('agent-commissions.update', ['agent_commission' => $commissionId]) }}">
                    @csrf
                    @method('PUT')
                    @if(($agents ?? collect())->isNotEmpty())
                        <div class="mb-4">
                            <x-input-label for="agent_id" :value="__('Agent')" />
                            <select id="agent_id" name="agent_id" class="mt-1 block w-full rounded-md border-gray-300" required>
                                @foreach($agents as $id => $name)
                                    <option value="{{ $id }}" @selected(old('agent_id', $commission->agent_id) == $id)>{{ $name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('agent_id')" class="mt-2" />
                        </div>
                    @endif
                    <div class="mb-4">
                        <x-input-label for="lease_id" :value="__('Lease (optional)')" />
                        <select id="lease_id" name="lease_id" class="mt-1 block w-full rounded-md border-gray-300">
                            <option value="">{{ __('No lease') }}</option>
                            @foreach(($leases ?? collect()) as $id => $label)
                                <option value="{{ $id }}" @selected(old('lease_id', $commission->lease_id) == $id)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('lease_id')" class="mt-2" />
                    </div>
                    <div class="mb-4 grid gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label for="amount" :value="__('Amount')" />
                            <x-text-input id="amount" name="amount" type="number" step="0.01" class="mt-1 block w-full" value="{{ old('amount', $commission->amount) }}" required />
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="rate" :value="__('Rate (%)')" />
                            <x-text-input id="rate" name="rate" type="number" step="0.01" class="mt-1 block w-full" value="{{ old('rate', $commission->rate) }}" />
                            <x-input-error :messages="$errors->get('rate')" class="mt-2" />
                        </div>
                    </div>
                    <div class="mb-4">
                        <x-input-label for="status" :value="__('Status')" />
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300" required>
                            @foreach(\App\Models\AgentCommission::statusLabels() as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', $commission->status ?? \App\Models\AgentCommission::STATUS_PENDING) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>
                    <div class="mb-6">
                        <x-input-label for="notes" :value="__('Notes')" />
                        <textarea id="notes" name="notes" rows="4" class="mt-1 block w-full rounded-md border-gray-300">{{ old('notes', $commission->notes) }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('agent-commissions.index') }}" class="rounded-md border px-3 py-2 text-gray-700">{{ __('Cancel') }}</a>
                        <button class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-white hover:bg-indigo-700" type="submit">{{ __('Update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
