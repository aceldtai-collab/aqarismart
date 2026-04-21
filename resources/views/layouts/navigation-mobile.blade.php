<!-- Mobile Menu Overlay -->
@if(!request()->is('admin*'))
<div x-show="$store.mobilemenu.open" x-cloak @click="$store.mobilemenu.open = false" x-transition:enter="transition-opacity ease-linear duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/50 z-40 lg:hidden"></div>

<!-- Mobile Sidebar -->
<div x-show="$store.mobilemenu.open" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="-translate-x-full rtl:translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full rtl:translate-x-full" class="fixed top-0 ltr:left-0 rtl:right-0 h-full w-80 bg-white shadow-2xl z-50 lg:hidden overflow-y-auto">
    
    <!-- Header -->
    <div class="flex items-center justify-between p-4 border-b border-slate-200">
        <button @click="$store.mobilemenu.open = false" class="p-2 hover:bg-slate-100 rounded-lg">
            <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        <div class="flex items-center gap-3">
            @if(!empty($theme['logo_url']))
                <img src="{{ \Illuminate\Support\Str::startsWith($theme['logo_url'], ['http://','https://']) ? $theme['logo_url'] : asset($theme['logo_url']) }}" alt="logo" class="h-8 w-auto rounded-md" />
            @else
                <div class="h-8 w-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-sm">{{ substr($tenantCtx?->name ?? config('app.name'), 0, 1) }}</span>
                </div>
            @endif
            <span class="font-semibold text-slate-900">{{ $tenantCtx?->name ?? config('app.name') }}</span>
        </div>
    </div>

    <!-- Navigation Items -->
    <div class="p-4 space-y-1">
        @php
            $navItemClass = 'flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors';
            $navActiveClass = 'bg-indigo-50 text-indigo-700';
            $navInactiveClass = 'text-slate-700 hover:bg-slate-50';
            
            // Check if we're in admin context (no tenant)
            $isAdminContext = !isset($tenantCtx) || $tenantCtx === null || request()->is('admin*');
            $dashboardRoute = $isAdminContext ? route('admin.index') : route('dashboard');
            $isDashboardActive = $isAdminContext ? request()->routeIs('admin.index') : request()->routeIs('dashboard');
        @endphp

        <a href="{{ $dashboardRoute }}" @click="$store.mobilemenu.open = false" class="{{ $navItemClass }} {{ $isDashboardActive ? $navActiveClass : $navInactiveClass }}">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
            {{ __('Dashboard') }}
        </a>

        <div class="pt-4 pb-2"><div class="px-4 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Properties') }}</div></div>
        @can('viewAny', App\Models\Property::class)
            @if($tenantCtx?->canUse('properties'))
                <a href="{{ route('properties.index') }}" @click="$store.mobilemenu.open = false" class="{{ $navItemClass }} {{ request()->routeIs('properties.*') ? $navActiveClass : $navInactiveClass }}">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm3 1h6v4H7V5zm6 6H7v2h6v-2z" clip-rule="evenodd"/></svg>
                    {{ __('Properties') }}
                </a>
            @endif
        @endcan
        @can('viewAny', App\Models\Unit::class)
            @if($tenantCtx?->canUse('units'))
                <a href="{{ route('units.index') }}" @click="$store.mobilemenu.open = false" class="{{ $navItemClass }} {{ request()->routeIs('units.*') ? $navActiveClass : $navInactiveClass }}">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/></svg>
                    {{ __('Units') }}
                </a>
            @endif
        @endcan
        @can('manage-attributes')
            @if($tenantCtx?->canUse('custom_attributes'))
                <a href="{{ route('custom-attributes.index') }}" @click="$store.mobilemenu.open = false" class="{{ $navItemClass }} {{ request()->routeIs('custom-attributes.*') ? $navActiveClass : $navInactiveClass }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    {{ __('Custom Attributes') }}
                </a>
            @endif
        @endcan

        <div class="pt-4 pb-2"><div class="px-4 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Residents') }}</div></div>
        <a href="{{ route('residents.index') }}" @click="$store.mobilemenu.open = false" class="{{ $navItemClass }} {{ request()->routeIs('residents.*') ? $navActiveClass : $navInactiveClass }}">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg>
            {{ __('Residents') }}
        </a>
        <a href="{{ route('leases.index') }}" @click="$store.mobilemenu.open = false" class="{{ $navItemClass }} {{ request()->routeIs('leases.*') ? $navActiveClass : $navInactiveClass }}">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/></svg>
            {{ __('Leases') }}
        </a>

        <div class="pt-4 pb-2"><div class="px-4 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Sales') }}</div></div>
        @can('viewAny', App\Models\Agent::class)
            <a href="{{ route('agents.index') }}" @click="$store.mobilemenu.open = false" class="{{ $navItemClass }} {{ request()->routeIs('agents.*') ? $navActiveClass : $navInactiveClass }}">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/></svg>
                {{ __('Agents') }}
            </a>
        @endcan
        @can('viewAny', App\Models\Contact::class)
            <a href="{{ route('contacts.index') }}" @click="$store.mobilemenu.open = false" class="{{ $navItemClass }} {{ request()->routeIs('contacts.*') ? $navActiveClass : $navInactiveClass }}">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"/><path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"/></svg>
                {{ __('Contacts') }}
            </a>
        @endcan
        <a href="{{ route('agent-leads.index') }}" @click="$store.mobilemenu.open = false" class="{{ $navItemClass }} {{ request()->routeIs('agent-leads.*') ? $navActiveClass : $navInactiveClass }}">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/></svg>
            {{ __('Leads') }}
        </a>
        <a href="{{ route('property-viewings.index') }}" @click="$store.mobilemenu.open = false" class="{{ $navItemClass }} {{ request()->routeIs('property-viewings.*') ? $navActiveClass : $navInactiveClass }}">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
            {{ __('Viewings') }}
        </a>
        <a href="{{ route('agent-commissions.index') }}" @click="$store.mobilemenu.open = false" class="{{ $navItemClass }} {{ request()->routeIs('agent-commissions.*') ? $navActiveClass : $navInactiveClass }}">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/></svg>
            {{ __('Commissions') }}
        </a>

        <div class="pt-4 pb-2"><div class="px-4 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Operations') }}</div></div>
        <a href="{{ route('maintenance.index') }}" @click="$store.mobilemenu.open = false" class="{{ $navItemClass }} {{ request()->routeIs('maintenance.*') ? $navActiveClass : $navInactiveClass }}">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>
            {{ __('Maintenance') }}
        </a>
        <a href="{{ route('reports.index') }}" @click="$store.mobilemenu.open = false" class="{{ $navItemClass }} {{ request()->routeIs('reports.*') ? $navActiveClass : $navInactiveClass }}">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/></svg>
            {{ __('Reports') }}
        </a>

        <div class="pt-4 pb-2"><div class="px-4 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('System') }}</div></div>
        <a href="{{ route('members.index') }}" @click="$store.mobilemenu.open = false" class="{{ $navItemClass }} {{ request()->routeIs('members.*') ? $navActiveClass : $navInactiveClass }}">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg>
            {{ __('Members') }}
        </a>
        <a href="{{ route('billing.index') }}" @click="$store.mobilemenu.open = false" class="{{ $navItemClass }} {{ request()->routeIs('billing.*') ? $navActiveClass : $navInactiveClass }}">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/><path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/></svg>
            {{ __('Billing') }}
        </a>
        <a href="{{ route('settings.edit') }}" @click="$store.mobilemenu.open = false" class="{{ $navItemClass }} {{ request()->routeIs('settings.*') ? $navActiveClass : $navInactiveClass }}">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>
            {{ __('Settings') }}
        </a>
    </div>

    <!-- User Section -->
    <div class="border-t border-slate-200 p-4 mt-4">
        @php
            $currentLang = app()->getLocale()==='ar' ? 'ar' : 'en';
            $langParam = config('locales.cookie_name', 'lang');
            $urlEn = request()->fullUrlWithQuery([$langParam => 'en']);
            $urlAr = request()->fullUrlWithQuery([$langParam => 'ar']);
        @endphp
        <div class="mb-4">
            <div class="px-4 text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">{{ __('Language') }}</div>
            <div class="flex items-center gap-2">
                <a href="{{ $urlEn }}" class="flex-1 text-center px-4 py-2 rounded-lg text-sm font-medium transition {{ $currentLang==='en' ? 'bg-gray-50 ' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">English</a>
                <a href="{{ $urlAr }}" class="flex-1 text-center px-4 py-2 rounded-lg text-sm font-medium transition {{ $currentLang==='ar' ? 'bg-gray-50 ' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">العربية</a>
            </div>
        </div>
        
        <div class="flex items-center gap-3 mb-3">
            <img alt="avatar" class="h-10 w-10 rounded-full ring-2 ring-slate-200" src="{{ 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=0f172a&color=fff' }}">
            <div class="flex-1 min-w-0">
                <div class="font-semibold text-slate-900 truncate">{{ Auth::user()->name }}</div>
                <div class="text-xs text-slate-500 truncate">{{ Auth::user()->email }}</div>
            </div>
        </div>
        <a href="{{ route('profile.edit') }}" @click="$store.mobilemenu.open = false" class="{{ $navItemClass }} {{ $navInactiveClass }}">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
            {{ __('Profile') }}
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="{{ $navItemClass }} {{ $navInactiveClass }} w-full text-left text-red-600 hover:bg-red-50">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"/></svg>
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</div>
@endif