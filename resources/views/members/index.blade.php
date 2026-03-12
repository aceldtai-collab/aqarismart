<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">{{ __('Members') }}</h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <x-flash-status />

            <div class="mb-4 rounded-md bg-white p-4 shadow">
                <div class="flex items-center justify-between">
                    <div class="text-gray-700">{{ __('Seats used:') }} <strong>{{ $used }}</strong> / <strong>{{ $limit === (int) PHP_INT_MAX ? __('Unlimited') : $limit }}</strong></div>
                    @if($remaining <= 0)
                        <a href="{{ route('billing.index') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-white hover:bg-indigo-700">{{ __('Upgrade Plan') }}</a>
                    @endif
                </div>
            </div>

            <div class="mb-6 rounded-md bg-white p-6 shadow">
                <form method="post" action="{{ route('members.invite') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    @csrf
                    <div>
                        <x-input-label for="email" :value="__('Invite by Email')" />
                        <x-text-input id="email" name="email" type="email" class="block w-full mt-1" value="{{ old('email') }}" autocomplete="email" required  />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="name" :value="__('Name (optional)')" />
                        <x-text-input id="name" name="name" class="block w-full mt-1" value="{{ old('name') }}" autocomplete="name"  />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="role" :value="__('Role')" />
                        <select id="role" name="role" class="mt-1 block w-full rounded-md border-gray-300" >
                            @foreach(['member' => __('Member'), 'admin' => __('Admin'), 'owner' => __('Owner')] as $value => $label)
                                <option value="{{ $value }}" @selected(old('role', 'member') === $value)>{{ $label }}</option>
                            @endforeach
                            @foreach($tenantRoles as $customRole)
                                @if(!in_array($customRole, ['owner', 'admin', 'member']))
                                    <option value="{{ $customRole }}" @selected(old('role') === $customRole)>{{ ucfirst($customRole) }}</option>
                                @endif
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-2" />
                    </div>
                    <div class="flex items-end">
                        <button class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-white hover:bg-indigo-700 disabled:opacity-50" type="submit" @disabled($remaining <= 0)>{{ __('Invite') }}</button>
                        @if($remaining <= 0)
                            <p class="ms-3 text-sm text-amber-700">{{ __('User limit reached. Upgrade to add more members.') }}</p>
                        @endif
                    </div>
                </form>
            </div>

            <div class="overflow-hidden rounded-md bg-white shadow">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Name') }}</th>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Email') }}</th>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Role') }}</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($users as $u)
                            <tr>
                                <td class="px-4 py-2">{{ $u->name }}</td>
                                <td class="px-4 py-2">{{ $u->email }}</td>
                                <td class="px-4 py-2">
                                    <form method="post" action="{{ route('members.updateRole', $u) }}" class="flex items-center gap-2">
                                        @csrf @method('PATCH')
                                        <select name="role" class="rounded-md border-gray-300">
                                            @foreach(['owner','admin','member'] as $r)
                                                <option value="{{ $r }}" @selected(($roles[$u->id] ?? 'member') === $r)>{{ ucfirst($r) }}</option>
                                            @endforeach
                                            @foreach($tenantRoles as $customRole)
                                                @if(!in_array($customRole, ['owner', 'admin', 'member']))
                                                    <option value="{{ $customRole }}" @selected(($roles[$u->id] ?? '') === $customRole)>{{ ucfirst($customRole) }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <button class="rounded-md border px-2 py-1 text-sm" type="submit">{{ __('Save') }}</button>
                                    </form>
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <form method="post" action="{{ route('members.destroy', $u) }}" onsubmit="return confirm('{{ __('Remove member?') }}')" class="inline-block">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">{{ __('Remove') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td class="px-4 py-6 text-center text-gray-500" colspan="4">{{ __('No members yet.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>


