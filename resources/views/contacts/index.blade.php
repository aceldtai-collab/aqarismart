@php
    $tenantCtx = app(\App\Services\Tenancy\TenantManager::class)->tenant();
    $indexRoute = $tenantCtx ? route('contacts.index') : route('admin.contacts.index');
    $createRoute = $tenantCtx ? route('contacts.create') : route('admin.contacts.create');
    $importFormRoute = $tenantCtx ? route('contacts.import.form') : route('admin.contacts.import.form');
@endphp

<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">{{ __('Contacts') }}</h2>
            <p class="mt-1 text-xs text-slate-500">{{ __('Leads and inquiries captured across your portfolio.') }}</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto space-y-6 sm:px-6 lg:px-8">
            <x-flash-status />
            
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">{{ __('Contacts Management') }}</h1>
                    <p class="mt-1 text-sm text-slate-600">{{ __('Leads and inquiries captured across your portfolio') }}</p>
                </div>
                <div class="flex items-center gap-3">
                    @can('create', App\Models\Contact::class)
                        <a href="{{ $importFormRoute }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-300 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50 focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 transition-all shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                            </svg>
                            {{ __('Import CSV') }}
                        </a>
                        <a href="{{ $createRoute }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-white text-sm font-semibold rounded-lg focus:ring-2 focus:ring-offset-2 transition-all shadow-sm {{ $tenantCtx ? 'bg-slate-900 hover:bg-slate-800 focus:ring-slate-500' : 'bg-[#e8604c] hover:bg-[#d4503e] focus:ring-[#e8604c]' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            {{ __('Add New Contact') }}
                        </a>
                    @endcan
                </div>
            </div>
            
            <div class="bg-white rounded-xl border border-slate-200/60 shadow-sm overflow-hidden">
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200/70">
                    <thead class="bg-gradient-to-r from-slate-50 to-slate-100/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">{{ __('Name') }}</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">{{ __('Agent') }}</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">{{ __('Email') }}</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">{{ __('Phone') }}</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-slate-600">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200/60 bg-white">
                        @forelse($contacts as $contact)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-900">{{ $contact->name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($contact->agent)
                                        <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-1 text-xs font-medium text-blue-700">
                                            {{ $contact->agent->name }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-slate-600">{{ $contact->email }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-slate-600">{{ $contact->phone }}</div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center gap-2 justify-end">
                                        @can('update', $contact)
                                            <a href="{{ $tenantCtx ? route('contacts.edit', $contact) : route('admin.contacts.edit', $contact) }}" class="inline-flex items-center rounded-lg px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors">{{ __('Edit') }}</a>
                                        @endcan
                                        @can('delete', $contact)
                                            <form method="post" action="{{ $tenantCtx ? route('contacts.destroy', $contact) : route('admin.contacts.destroy', $contact) }}" class="inline" onsubmit="return confirm('{{ __('Delete contact?') }}')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="inline-flex items-center rounded-lg px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors">{{ __('Delete') }}</button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-16" colspan="5">
                                    <div class="text-center">
                                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-slate-100 to-slate-200 text-slate-500">
                                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"></path>
                                            </svg>
                                        </div>
                                        <h3 class="mt-4 text-lg font-semibold text-slate-900">{{ __('No contacts yet') }}</h3>
                                        <p class="mt-2 text-sm text-slate-600 max-w-sm mx-auto">{{ __('Start building your contact database by adding leads and prospects.') }}</p>
                                        @can('create', App\Models\Contact::class)
                                            <div class="mt-4 flex items-center gap-3 justify-center">
                                                <a href="{{ $importFormRoute }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-300 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                                    </svg>
                                                    {{ __('Import CSV') }}
                                                </a>
                                                <a href="{{ $createRoute }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m-6 0H6"></path>
                                                    </svg>
                                                    {{ __('Add Your First Contact') }}
                                                </a>
                                            </div>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
              </div>
            </div>
            <div class="mt-4">{{ $contacts->links() }}</div>
        </div>
    </div>
</x-app-layout>
