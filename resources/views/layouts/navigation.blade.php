@php
    $navBase = 'flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg transition-colors';
    $navActive = 'bg-indigo-50 text-indigo-700';
    $navInactive = 'text-slate-600 hover:bg-slate-50 hover:text-slate-900';
@endphp

@if($tenantCtx)
    {{-- Dashboard --}}
    <a href="{{ route('dashboard') }}" class="{{ $navBase }} {{ request()->routeIs('dashboard') ? $navActive : $navInactive }}">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
        {{ __('Dashboard') }}
    </a>
    
    {{-- Properties --}}
    @can('viewAny', App\Models\Property::class)
        @if($tenantCtx->canUse('properties'))
            <a href="{{ route('properties.index') }}" class="{{ $navBase }} {{ request()->routeIs('properties.*') ? $navActive : $navInactive }}">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm3 1h6v4H7V5zm6 6H7v2h6v-2z" clip-rule="evenodd"/></svg>
                {{ __('Properties') }}
            </a>
        @endif
    @endcan

    {{-- Units --}}
    @can('viewAny', App\Models\Unit::class)
        @if($tenantCtx->canUse('units'))
            <a href="{{ route('units.index') }}" class="{{ $navBase }} {{ request()->routeIs('units.*') ? $navActive : $navInactive }}">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/></svg>
                {{ __('Units') }}
            </a>
        @endif
    @endcan
    
    {{-- Custom Attributes (standalone) --}}
    @can('manage-attributes')
        @if($tenantCtx->canUse('custom_attributes'))
            <a href="{{ route('custom-attributes.index') }}" class="{{ $navBase }} {{ request()->routeIs('custom-attributes.*') ? $navActive : $navInactive }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                {{ __('Custom Attributes') }}
            </a>
        @endif
    @endcan
    
    {{-- Tenants Dropdown --}}
    <div x-data="{ open: false }" @click.outside="open = false" class="relative z-50">
        <button @click="open = !open" class="{{ $navBase }} {{ request()->routeIs(['residents.*', 'leases.*']) ? $navActive : $navInactive }}">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg>
            {{ __('Residents') }}
            <svg class="w-3 h-3 transition-transform" :class="open ? 'rotate-180' : ''" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
        </button>
        <div x-show="open" x-cloak x-transition class="absolute top-full ltr:left-0 rtl:right-0 mt-1 bg-white border border-slate-200 rounded-lg shadow-lg z-[100] min-w-[200px] py-1">
            <a href="{{ route('residents.index') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 {{ request()->routeIs('residents.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : '' }}">{{ __('Residents') }}</a>
            <a href="{{ route('leases.index') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 {{ request()->routeIs('leases.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : '' }}">{{ __('Leases') }}</a>
        </div>
    </div>
    
    {{-- Sales Dropdown --}}
    <div x-data="{ open: false }" @click.outside="open = false" class="relative z-50">
        <button @click="open = !open" class="{{ $navBase }} {{ request()->routeIs(['agents.*', 'contacts.*', 'agent-leads.*', 'property-viewings.*', 'agent-commissions.*']) ? $navActive : $navInactive }}">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"/><path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z"/></svg>
            {{ __('Sales') }}
            <svg class="w-3 h-3 transition-transform" :class="open ? 'rotate-180' : ''" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
        </button>
        <div x-show="open" x-cloak x-transition class="absolute top-full ltr:left-0 rtl:right-0 mt-1 bg-white border border-slate-200 rounded-lg shadow-lg z-[100] min-w-[200px] py-1">
            @can('viewAny', App\Models\Agent::class)
                <a href="{{ route('agents.index') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 {{ request()->routeIs('agents.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : '' }}">{{ __('Agents') }}</a>
            @endcan
            @can('viewAny', App\Models\Contact::class)
                <a href="{{ route('contacts.index') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 {{ request()->routeIs('contacts.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : '' }}">{{ __('Contacts') }}</a>
            @endcan
            <a href="{{ route('agent-leads.index') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 {{ request()->routeIs('agent-leads.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : '' }}">{{ __('Leads') }}</a>
            <a href="{{ route('property-viewings.index') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 {{ request()->routeIs('property-viewings.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : '' }}">{{ __('Viewings') }}</a>
            <a href="{{ route('agent-commissions.index') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 {{ request()->routeIs('agent-commissions.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : '' }}">{{ __('Commissions') }}</a>
        </div>
    </div>
    
    {{-- Maintenance --}}
    <a href="{{ route('maintenance.index') }}" class="{{ $navBase }} {{ request()->routeIs('maintenance.*') ? $navActive : $navInactive }}">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>
        {{ __('Maintenance') }}
    </a>
    
    {{-- Reports --}}
    <a href="{{ route('reports.index') }}" class="{{ $navBase }} {{ request()->routeIs('reports.*') ? $navActive : $navInactive }}">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/></svg>
        {{ __('Reports') }}
    </a>
    
    {{-- Settings Dropdown --}}
    <div x-data="{ open: false }" @click.outside="open = false" class="relative z-50">
        <button @click="open = !open" class="{{ $navBase }} {{ request()->routeIs(['settings.*', 'members.*', 'billing.*', 'permissions.*']) ? $navActive : $navInactive }}">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>
            {{ __('Settings') }}
            <svg class="w-3 h-3 transition-transform" :class="open ? 'rotate-180' : ''" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
        </button>
        <div x-show="open" x-cloak x-transition class="absolute top-full ltr:left-0 rtl:right-0 mt-1 bg-white border border-slate-200 rounded-lg shadow-lg z-[100] min-w-[200px] py-1">
            <a href="{{ route('members.index') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 {{ request()->routeIs('members.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : '' }}">{{ __('Members') }}</a>
            <a href="{{ route('billing.index') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 {{ request()->routeIs('billing.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : '' }}">{{ __('Billing') }}</a>
            @can('manage-permissions', $tenantCtx)
                <div x-data="{ open: false }" @click.outside="open = false" class="relative z-50">
                    <button @click="open = !open" class="block w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 {{ request()->routeIs('permissions.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : '' }}">
                        {{ __('Permissions') }}
                        <svg class="w-3 h-3 transition-transform float-left mt-0.5 mr-2" :class="open ? 'rotate-180' : ''" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="absolute top-full ltr:left-0 rtl:right-0 mt-1 bg-white border border-slate-200 rounded-lg shadow-lg z-[100] min-w-[200px] py-1">
                        <a href="{{ route('permissions.index') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 {{ request()->routeIs('permissions.index') ? 'bg-indigo-50 text-indigo-700 font-medium' : '' }}">{{ __('Manage Roles') }}</a>
                        @can('manage-roles', $tenantCtx)
                            <a href="{{ route('permissions.create') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">{{ __('Create Role') }}</a>
                        @endcan
                    </div>
                </div>
            @endcan
            <a href="{{ route('settings.edit') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 {{ request()->routeIs('settings.edit') ? 'bg-indigo-50 text-indigo-700 font-medium' : '' }}">{{ __('General Settings') }}</a>
        </div>
    </div>
@else
    <a href="{{ route('admin.index') }}" class="{{ $navBase }} {{ request()->routeIs('admin.index') ? $navActive : $navInactive }}">{{ __('Dashboard') }}</a>
    <a href="{{ route('admin.tenants.index') }}" class="{{ $navBase }} {{ request()->routeIs('admin.tenants.*') ? $navActive : $navInactive }}">{{ __('Tenants') }}</a>
    <a href="{{ route('admin.users.index') }}" class="{{ $navBase }} {{ request()->routeIs('admin.users.*') ? $navActive : $navInactive }}">{{ __('Users') }}</a>
    <a href="{{ route('admin.units.index') }}" class="{{ $navBase }} {{ request()->routeIs('admin.units.*') ? $navActive : $navInactive }}">{{ __('Units') }}</a>
    <a href="{{ route('admin.agents.index') }}" class="{{ $navBase }} {{ request()->routeIs('admin.agents.*') ? $navActive : $navInactive }}">{{ __('Agents') }}</a>
    <a href="{{ route('admin.contacts.index') }}" class="{{ $navBase }} {{ request()->routeIs('admin.contacts.*') ? $navActive : $navInactive }}">{{ __('Contacts') }}</a>
    <a href="{{ route('admin.categories.index') }}" class="{{ $navBase }} {{ request()->routeIs('admin.categories.*') ? $navActive : $navInactive }}">{{ __('Categories') }}</a>
    <a href="{{ route('admin.packages.index') }}" class="{{ $navBase }} {{ request()->routeIs('admin.packages.*') ? $navActive : $navInactive }}">{{ __('Packages') }}</a>
    <a href="{{ route('admin.addons.index') }}" class="{{ $navBase }} {{ request()->routeIs('admin.addons.*') ? $navActive : $navInactive }}">{{ __('Add-ons') }}</a>
    <a href="{{ route('admin.reports.index') }}" class="{{ $navBase }} {{ request()->routeIs('admin.reports.*') ? $navActive : $navInactive }}">{{ __('Reports') }}</a>
@endif
