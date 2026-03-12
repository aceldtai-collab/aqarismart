<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Permissions Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 rounded-lg bg-green-50 border border-green-200 p-4 text-green-800 text-sm">
                    <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4 text-red-800 text-sm">
                    <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('Manage Roles & Permissions') }}</h3>
                            <p class="text-sm text-gray-600">{{ __('Configure what each role can do in your organization') }}</p>
                        </div>
                        @can('manage-roles', $tenant)
                            <a href="{{ route('permissions.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fas fa-plus mr-2"></i>
                                {{ __('Create Role') }}
                            </a>
                        @endcan
                    </div>

                    <div class="space-y-6">
                        @foreach($roles as $role)
                            <div class="border border-gray-200 rounded-lg p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900">{{ __(ucfirst($role->name)) }}</h4>
                                        <p class="text-sm text-gray-600">
                                            {{ $roleUserCounts[$role->name] ?? 0 }} {{ __('users assigned') }}
                                            @if(in_array($role->name, ['owner', 'admin', 'member']))
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ __('System Role') }}</span>
                                            @endif
                                        </p>
                                    </div>
                                    @if(!in_array($role->name, ['owner', 'admin', 'member']))
                                        <div class="flex space-x-2">
                                            @can('manage-roles', $tenant)
                                                <a href="{{ route('permissions.edit', $role->id) }}" class="inline-flex items-center px-3 py-1 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700">
                                                    <i class="fas fa-edit mr-1"></i>
                                                    {{ __('Edit') }}
                                                </a>
                                                <button type="button" onclick="deleteRole({{ $role->id }}, '{{ $role->name }}')" class="inline-flex items-center px-3 py-1 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                                                    <i class="fas fa-trash mr-1"></i>
                                                    {{ __('Delete') }}
                                                </button>
                                            @endcan
                                        </div>
                                    @endif
                                </div>

                                @can('manage-permissions', $tenant)
                                    <form method="POST" action="{{ route('permissions.roles.update', $role->id) }}" class="space-y-4">
                                        @csrf
                                        @method('PATCH')

                                        @foreach($permissions as $group => $groupPermissions)
                                            <div>
                                                <h5 class="text-sm font-medium text-gray-700 mb-2 capitalize">{{ __($group) }}</h5>
                                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                                                    @foreach($groupPermissions as $permission)
                                                        <label class="inline-flex items-center">
                                                            <input type="checkbox"
                                                                   name="permissions[]"
                                                                   value="{{ $permission->name }}"
                                                                   {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}
                                                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                            <span class="ml-2 text-sm text-gray-700">{{ __(ucfirst(str_replace(['-', '_'], ' ', $permission->name))) }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach

                                        <div class="pt-4 border-t border-gray-200">
                                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                <i class="fas fa-save mr-2"></i>
                                                {{ __('Update Permissions') }}
                                            </button>
                                        </div>
                                    </form>
                                @else
                                    <div class="text-sm text-gray-600">
                                        <p>{{ __('Current permissions:') }}</p>
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            @foreach($role->permissions as $permission)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    {{ __(ucfirst(str_replace(['-', '_'], ' ', $permission->name))) }}
                                                </span>
                                            @endforeach
                                            @if($role->permissions->isEmpty())
                                                <span class="text-gray-500">{{ __('No permissions assigned') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endcan
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Role Modal -->
    <div id="createRoleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Create New Role') }}</h3>
                <form method="POST" action="{{ route('permissions.roles.create') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">{{ __('Role Name') }}</label>
                        <input type="text" name="name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Permissions') }}</label>
                        @foreach($permissions as $group => $groupPermissions)
                            <div class="mb-3">
                                <h5 class="text-sm font-medium text-gray-600 mb-1 capitalize">{{ __($group) }}</h5>
                                <div class="ml-2 space-y-1">
                                    @foreach($groupPermissions as $permission)
                                        <label class="inline-flex items-center text-sm">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <span class="ml-2">{{ __(ucfirst(str_replace(['-', '_'], ' ', $permission->name))) }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeCreateRoleModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">{{ __('Cancel') }}</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">{{ __('Create Role') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- User Roles Management -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-900">{{ __('User Roles') }}</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('User') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Current Role') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($tenant->users as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700">{{ substr($user->name, 0, 2) }}</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                @if($user->getTenantRole($tenant) === 'owner') bg-red-100 text-red-800
                                @elseif($user->getTenantRole($tenant) === 'admin') bg-blue-100 text-blue-800
                                @elseif($user->getTenantRole($tenant) === 'member') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $user->getTenantRole($tenant) ? __($user->getTenantRole($tenant)) : __('No Role') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @if($user->id !== auth()->id() || $user->getTenantRole($tenant) !== 'owner')
                            <button onclick="openAssignRoleModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->getTenantRole($tenant) }}')"
                                    class="text-indigo-600 hover:text-indigo-900">{{ __('Assign Role') }}</button>
                            @else
                            <span class="text-gray-400">{{ __('Owner') }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Assign Role Modal -->
    <div id="assignRoleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Assign Role to User') }}</h3>
                <form id="assignRoleForm" method="POST" action="{{ route('permissions.assign-role') }}">
                    @csrf
                    <input type="hidden" id="assignUserId" name="user_id">
                    <div class="mb-4">
                        <p class="text-sm text-gray-600">
                            {{ __('Assigning role to') }}: <span id="assignUserName" class="font-medium"></span>
                        </p>
                        <p class="text-sm text-gray-600">
                            {{ __('Current role') }}: <span id="assignCurrentRole" class="font-medium"></span>
                        </p>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">{{ __('Select Role') }}</label>
                        <select id="assignRoleSelect" name="role_name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">{{ __('Select a role') }}</option>
                            <option value="member">{{ __('Member') }}</option>
                            <option value="admin">{{ __('Admin') }}</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ __($role->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeAssignRoleModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">{{ __('Cancel') }}</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">{{ __('Assign Role') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Role Modal -->
    <div id="editRoleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Edit Role') }}</h3>
                <form id="editRoleForm" method="POST" action="">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">{{ __('Role Name') }}</label>
                        <input type="text" id="editRoleName" name="name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Permissions') }}</label>
                        <div id="editRolePermissions"></div>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeEditRoleModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">{{ __('Cancel') }}</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">{{ __('Update Role') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openCreateRoleModal() {
            document.getElementById('createRoleModal').classList.remove('hidden');
        }

        function closeCreateRoleModal() {
            document.getElementById('createRoleModal').classList.add('hidden');
        }

        function openAssignRoleModal(userId, userName, currentRole) {
            document.getElementById('assignUserId').value = userId;
            document.getElementById('assignUserName').textContent = userName;
            document.getElementById('assignCurrentRole').textContent = currentRole || '{{ __('No Role') }}';
            document.getElementById('assignRoleModal').classList.remove('hidden');
        }

        function closeAssignRoleModal() {
            document.getElementById('assignRoleModal').classList.add('hidden');
        }

        function openEditRoleModal(roleId, roleName) {
            document.getElementById('editRoleName').value = roleName;
            document.getElementById('editRoleForm').action = `/permissions/roles/${roleId}/edit`;

            // Fetch role permissions and populate the modal
            fetch(`/permissions/roles/${roleId}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(response => response.json())
                .then(data => {
                    let permissionsHtml = '';
                    @foreach($permissions as $group => $groupPermissions)
                        permissionsHtml += `<div class="mb-3"><h5 class="text-sm font-medium text-gray-600 mb-1 capitalize">${{ json_encode($group) }}</h5><div class="ml-2 space-y-1">`;
                        @foreach($groupPermissions as $permission)
                            const checked = data.permissions.includes({{ json_encode($permission->name) }}) ? 'checked' : '';
                            permissionsHtml += `<label class="inline-flex items-center text-sm"><input type="checkbox" name="permissions[]" value="${{ json_encode($permission->name) }}" ${checked} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"><span class="ml-2">${{ json_encode(ucfirst(str_replace(['-', '_'], ' ', $permission->name))) }}</span></label>`;
                        @endforeach
                        permissionsHtml += '</div></div>';
                    @endforeach
                    document.getElementById('editRolePermissions').innerHTML = permissionsHtml;
                });

            document.getElementById('editRoleModal').classList.remove('hidden');
        }

        function closeEditRoleModal() {
            document.getElementById('editRoleModal').classList.add('hidden');
        }

        function deleteRole(roleId, roleName) {
            if (confirm(`{{ __('Are you sure you want to delete the') }} "${roleName}" {{ __('role?') }}`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/permissions/roles/${roleId}`;

                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';

                const method = document.createElement('input');
                method.type = 'hidden';
                method.name = '_method';
                method.value = 'DELETE';

                form.appendChild(csrf);
                form.appendChild(method);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</x-app-layout>
