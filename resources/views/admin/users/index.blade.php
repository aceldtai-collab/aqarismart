<x-admin-layout>
    <x-slot name="header">{{ __('Users') }}</x-slot>
    <x-slot name="subtitle">{{ __('All registered platform users.') }}</x-slot>

    <div class="gz-widget">
      <div class="gz-table-wrap">
        <table class="w-full gz-table">
            <thead>
                <tr>
                    <th class="text-left">{{ __('Name') }}</th>
                    <th class="text-left">{{ __('Email') }}</th>
                    <th class="text-left">{{ __('Created') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td class="font-semibold text-[#1e1e2d]">{{ $user->name }}</td>
                        <td class="text-[#7c8db5]">{{ $user->email }}</td>
                        <td class="text-[#7c8db5]">{{ $user->created_at->toDateString() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
      </div>
        <div class="px-6 py-4 border-t border-[#e8ecf3]">{{ $users->links() }}</div>
    </div>
</x-admin-layout>
