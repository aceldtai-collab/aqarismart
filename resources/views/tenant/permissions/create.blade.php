<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('permissions.index') }}" class="text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800">{{ __('Create Role') }}</h2>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-flash-status />

            <div class="bg-white shadow rounded-lg">
                <form method="POST" action="{{ route('permissions.roles.create') }}">
                    @csrf
                    <div class="p-6 space-y-6">
                        <!-- Role Name -->
                        <div>
                            <x-input-label for="name" :value="__('Role Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" 
                                          value="{{ old('name') }}" required autocomplete="off" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            <p class="mt-1 text-sm text-gray-500">
                                {{ __('Use lowercase with hyphens, e.g., property-manager, maintenance-staff') }}
                            </p>
                        </div>

                        <!-- Permissions -->
                        <div>
                            <x-input-label :value="__('Permissions')" />
                            <p class="mt-1 text-sm text-gray-500">
                                {{ __('Select the permissions this role should have') }}
                            </p>
                            
                            <div class="mt-4 space-y-4">
                                @foreach($permissions as $group => $groupPermissions)
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <h3 class="text-sm font-medium text-gray-900 mb-3 capitalize">
                                            {{ $group === 'general' ? __('General') : $group }}
                                        </h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                            @foreach($groupPermissions as $permission)
                                                <label class="flex items-center">
                                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" 
                                                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                           @if(old('permissions') && in_array($permission->name, old('permissions'))) checked @endif>
                                                    <span class="ml-2 text-sm text-gray-700">
                                                        {{ __(ucfirst(str_replace(['-', '_'], ' ', $permission->name))) }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('permissions')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-3">
                        <a href="{{ route('permissions.index') }}" 
                           class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Create Role') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
