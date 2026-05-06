<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">{{ __('Units') }}</h2>
            <p class="mt-1 text-xs text-slate-500">{{ __('Manage availability, pricing, and unit-level assignments.') }}</p>
        </div>
    </x-slot>
    @php
        $statusPalette = config('status.palette', [
            'success' => ['text' => 'text-emerald-700', 'bg' => 'bg-emerald-50'],
            'warning' => ['text' => 'text-amber-700', 'bg' => 'bg-amber-50'],
            'danger' => ['text' => 'text-rose-700', 'bg' => 'bg-rose-50'],
            'info' => ['text' => 'text-sky-700', 'bg' => 'bg-sky-50'],
        ]);
        $statusTone = [
            'vacant' => 'warning',
            'occupied' => 'success',
            'sold' => 'info',
        ];
        $activeFilterKeys = ['q', 'status', 'listing_type', 'agent_id', 'furnished', 'location', 'area', 'owner_name', 'owner_phone', 'min_price', 'max_price'];
    @endphp

    <div class="py-8">
        <div class="max-w-7xl mx-auto space-y-6 sm:px-6 lg:px-8">
            <x-flash-status />
            
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">{{ __('Units Management') }}</h1>
                    <p class="mt-2 text-slate-600">{{ __('Manage your rental units, track availability and pricing') }}</p>
                </div>
                @can('create', App\Models\Unit::class)
                    <a href="{{ route('units.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-50 text-sm font-semibold rounded-lg hover:bg-slate-800 focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        {{ __('Add New Unit') }}
                    </a>
                @endcan
            </div>
            
            <!-- Enhanced Collapsible Filters -->
            <div class="bg-gradient-to-r from-white to-slate-50/50 rounded-2xl border border-slate-200/60 shadow-lg overflow-hidden backdrop-blur-sm">
                <button 
                    onclick="toggleFilters()" 
                    class="w-full flex items-center justify-between p-6 text-left hover:bg-slate-50/50 transition-all duration-200"
                >
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            <div class="p-3 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                                </svg>
                            </div>
                            <div class="absolute -top-1 -right-1 w-4 h-4 bg-green-500 rounded-full border-2 border-white flex items-center justify-center" style="display: {{ request()->hasAny($activeFilterKeys) ? 'flex' : 'none' }}">
                                <div class="w-2 h-2 bg-white rounded-full"></div>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-900 mb-1">{{ __('Advanced Filters') }}</h3>
                            <p class="text-sm text-slate-600">{{ __('Filter units by various criteria') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-green-100 text-green-700 text-xs font-medium rounded-full border border-green-200" style="display: {{ request()->hasAny($activeFilterKeys) ? 'flex' : 'none' }}">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ __('Active') }}
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-slate-600" id="filter-toggle-text">{{ __('Show') }}</span>
                            <div class="p-2 rounded-lg bg-slate-100 transition-all duration-200" id="filter-toggle-bg">
                                <svg id="filter-chevron" class="w-5 h-5 text-slate-600 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </button>
                
                <div id="filters-content" class="hidden border-t border-slate-200/60 bg-white/80 backdrop-blur-sm">
                    <form method="get" class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            <!-- Search -->
                            <div class="xl:col-span-2">
                                <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-3">
                                    <svg class="w-4 h-4 brand-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    {{ __('Search') }}
                                </label>
                                <div class="relative">
                                    <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="{{ __('Search by unit name, address, or owner info...') }}" class="w-full pl-12 pr-4 py-3.5 border border-slate-300 rounded-xl focus:ring-2 focus:brand-border focus:brand-border bg-white shadow-sm transition-all hover:border-slate-400">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Status -->
                            <div>
                                <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-3">
                                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ __('Status') }}
                                </label>
                                <select name="status" class="w-full px-4 py-3.5 border border-slate-300 rounded-xl focus:ring-2 focus:brand-border focus:brand-border bg-white shadow-sm transition-all hover:border-slate-400">
                                    <option value="">{{ __('All Status') }}</option>
                                    @foreach(\App\Models\Unit::statusLabels() as $value => $label)
                                        <option value="{{ $value }}" {{ ($status ?? request('status')) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Listing Type -->
                            <div>
                                <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-3">
                                    <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                    {{ __('Listing Type') }}
                                </label>
                                <select name="listing_type" class="w-full px-4 py-3.5 border border-slate-300 rounded-xl focus:ring-2 focus:brand-border focus:brand-border bg-white shadow-sm transition-all hover:border-slate-400">
                                    <option value="">{{ __('All Types') }}</option>
                                    @foreach(\App\Models\Unit::listingTypeLabels() as $value => $label)
                                        <option value="{{ $value }}" {{ ($listing_type ?? request('listing_type')) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            @if(! auth()->user()?->agent_id && $agents->isNotEmpty())
                                <!-- Agent -->
                                <div>
                                    <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-3">
                                        <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-6a4 4 0 11-8 0 4 4 0 018 0zm8 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                        {{ __('Agent') }}
                                    </label>
                                    <select name="agent_id" class="w-full px-4 py-3.5 border border-slate-300 rounded-xl focus:ring-2 focus:brand-border focus:brand-border bg-white shadow-sm transition-all hover:border-slate-400">
                                        <option value="">{{ __('All Agents') }}</option>
                                        @foreach($agents as $id => $name)
                                            <option value="{{ $id }}" {{ (int) ($agent_id ?? request('agent_id')) === (int) $id ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            
                            <!-- Furnished Status -->
                            <div>
                                <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-3">
                                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v0"></path>
                                    </svg>
                                    {{ __('Furnished') }}
                                </label>
                                <select name="furnished" class="w-full px-4 py-3.5 border border-slate-300 rounded-xl focus:ring-2 focus:brand-border focus:brand-border bg-white shadow-sm transition-all hover:border-slate-400">
                                    <option value="">{{ __('All') }}</option>
                                    <option value="1" {{ request('furnished') == '1' ? 'selected' : '' }}>{{ __('Furnished') }}</option>
                                    <option value="0" {{ request('furnished') == '0' ? 'selected' : '' }}>{{ __('Unfurnished') }}</option>
                                </select>
                            </div>
                            
                            <!-- Location -->
                            <div>
                                <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-3">
                                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ __('Location') }}
                                </label>
                                <input type="text" name="location" value="{{ request('location') ?? '' }}" placeholder="{{ __('City, area, or address') }}" class="w-full px-4 py-3.5 border border-slate-300 rounded-xl focus:ring-2 focus:brand-border focus:brand-border bg-white shadow-sm transition-all hover:border-slate-400">
                            </div>
                            
                            <!-- Area -->
                            <div>
                                <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-3">
                                    <svg class="w-4 h-4 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                                    </svg>
                                    {{ __('Area (sqm)') }}
                                </label>
                                <input type="number" name="area" value="{{ request('area') ?? '' }}" min="0" placeholder="{{ __('Minimum area') }}" class="w-full px-4 py-3.5 border border-slate-300 rounded-xl focus:ring-2 focus:brand-border focus:brand-border bg-white shadow-sm transition-all hover:border-slate-400">
                            </div>
                            
                            <!-- Owner Name -->
                            <div>
                                <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-3">
                                    <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    {{ __('Owner Name') }}
                                </label>
                                <input type="text" name="owner_name" value="{{ request('owner_name') ?? '' }}" placeholder="{{ __('Property owner name') }}" class="w-full px-4 py-3.5 border border-slate-300 rounded-xl focus:ring-2 focus:brand-border focus:brand-border bg-white shadow-sm transition-all hover:border-slate-400">
                            </div>
                            
                            <!-- Owner Phone -->
                            <div>
                                <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-3">
                                    <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    {{ __('Owner Phone') }}
                                </label>
                                <input type="text" name="owner_phone" value="{{ request('owner_phone') ?? '' }}" placeholder="{{ __('Property owner phone') }}" class="w-full px-4 py-3.5 border border-slate-300 rounded-xl focus:ring-2 focus:brand-border focus:brand-border bg-white shadow-sm transition-all hover:border-slate-400">
                            </div>
                            
                            <!-- Price Range -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-3">
                                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                        {{ __('Min Price') }}
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <span class="text-slate-500 text-sm">$</span>
                                        </div>
                                        <input type="number" name="min_price" value="{{ $min_price ?? '' }}" min="0" class="w-full pl-8 pr-4 py-3.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white shadow-sm transition-all hover:border-slate-400" placeholder="0" />
                                    </div>
                                </div>
                                <div>
                                    <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-3">
                                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                        {{ __('Max Price') }}
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <span class="text-slate-500 text-sm">$</span>
                                        </div>
                                        <input type="number" name="max_price" value="{{ $max_price ?? '' }}" min="0" class="w-full pl-8 pr-4 py-3.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white shadow-sm transition-all hover:border-slate-400" placeholder="∞" />
                                    </div>
                                </div>
                            </div>

                            @if($filterableAttributes->isNotEmpty())
                            <!-- Custom Attributes Filter -->
                            <div class="xl:col-span-2">
                                <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-3">
                                    <svg class="w-4 h-4 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    {{ __('Custom Attributes') }}
                                </label>
                                <div x-data="attributeMultiSelect()" class="relative">
                                    <div @click="open = !open" class="w-full min-h-[3.5rem] px-4 py-2.5 border border-slate-300 rounded-xl bg-white shadow-sm cursor-pointer hover:border-slate-400 transition-all flex flex-wrap items-center gap-2">
                                        <template x-if="selected.length === 0">
                                            <span class="text-slate-400 text-sm">{{ __('Select attributes...') }}</span>
                                        </template>
                                        <template x-for="id in selected" :key="id">
                                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-teal-100 text-teal-700 text-xs font-medium rounded-lg">
                                                <span x-text="getLabel(id)"></span>
                                                <button type="button" @click.stop="toggle(id)" class="hover:text-teal-900">&times;</button>
                                            </span>
                                        </template>
                                    </div>
                                    <div x-show="open" @click.away="open = false" x-transition class="absolute z-50 mt-2 w-full bg-white border border-slate-200 rounded-xl shadow-xl max-h-60 overflow-hidden">
                                        <div class="p-2 border-b border-slate-100">
                                            <input type="text" x-model="search" placeholder="{{ __('Search...') }}" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                        </div>
                                        <div class="overflow-y-auto max-h-44 p-1">
                                            <template x-for="attr in filteredAttrs" :key="attr.id">
                                                <label class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-50 cursor-pointer transition-colors">
                                                    <input type="checkbox" :value="attr.id" :checked="selected.includes(String(attr.id))" @change="toggle(String(attr.id))" class="rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                                                    <span class="text-sm text-slate-700" x-text="attr.label"></span>
                                                </label>
                                            </template>
                                            <div x-show="filteredAttrs.length === 0" class="px-3 py-4 text-center text-sm text-slate-400">{{ __('No attributes found') }}</div>
                                        </div>
                                    </div>
                                    <template x-for="id in selected" :key="'input-'+id">
                                        <input type="hidden" name="attribute_filters[]" :value="id">
                                    </template>
                                </div>
                            </div>
                            @endif
                        </div>
                        
                        <div class="flex items-center justify-between mt-8 pt-6 border-t border-slate-200/60">
                            <div class="flex items-center gap-4">
                                <button type="submit" class="inline-flex items-center gap-3 px-8 py-3.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    {{ __('Apply Filters') }}
                                </button>
                                <a href="{{ route('units.index') }}" class="inline-flex items-center gap-3 px-6 py-3.5 text-slate-600 text-sm font-semibold rounded-xl border-2 border-slate-300 hover:bg-slate-50 hover:border-slate-400 transition-all duration-200 shadow-sm hover:shadow-md">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    {{ __('Reset') }}
                                </a>
                            </div>
                            
                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-2 px-4 py-2 bg-green-50 text-green-700 text-sm font-medium rounded-lg border border-green-200" style="display: {{ request()->hasAny($activeFilterKeys) ? 'flex' : 'none' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>{{ __('Filters applied') }}</span>
                                </div>
                                <div class="text-sm text-slate-500">
                                    <span class="font-medium">{{ $units->total() }}</span> {{ __('units found') }}
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <script>
                function toggleFilters() {
                    const content = document.getElementById('filters-content');
                    const chevron = document.getElementById('filter-chevron');
                    const toggleText = document.getElementById('filter-toggle-text');
                    const toggleBg = document.getElementById('filter-toggle-bg');
                    
                    if (content.classList.contains('hidden')) {
                        content.classList.remove('hidden');
                        chevron.style.transform = 'rotate(180deg)';
                        chevron.classList.add('text-indigo-600');
                        toggleText.textContent = '{{ __('Hide') }}';
                        toggleBg.classList.add('bg-indigo-100');
                        toggleBg.classList.remove('bg-slate-100');
                    } else {
                        content.classList.add('hidden');
                        chevron.style.transform = 'rotate(0deg)';
                        chevron.classList.remove('text-indigo-600');
                        toggleText.textContent = '{{ __('Show') }}';
                        toggleBg.classList.remove('bg-indigo-100');
                        toggleBg.classList.add('bg-slate-100');
                    }
                }

                function attributeMultiSelect() {
                    const locale = '{{ app()->getLocale() }}';
                    const attrs = @json($filterableAttributes->map(fn($f) => ['id' => (string)$f->id, 'label' => $f->label_translations[app()->getLocale()] ?? $f->label ?? $f->key]));
                    const initial = @json(array_map('strval', $attribute_filters ?? []));
                    return {
                        open: false,
                        search: '',
                        attrs: attrs,
                        selected: initial,
                        get filteredAttrs() {
                            const q = this.search.toLowerCase();
                            return q ? this.attrs.filter(a => a.label.toLowerCase().includes(q)) : this.attrs;
                        },
                        toggle(id) {
                            const idx = this.selected.indexOf(id);
                            if (idx > -1) { this.selected.splice(idx, 1); } else { this.selected.push(id); }
                        },
                        getLabel(id) {
                            const a = this.attrs.find(a => a.id === id);
                            return a ? a.label : id;
                        }
                    };
                }
            </script>
            <!-- Quick Filters -->
            <div class="flex items-center gap-3 overflow-x-auto pb-2">
                <span class="text-sm font-medium text-slate-700 whitespace-nowrap">{{ __('Quick Filters:') }}</span>
                <div class="flex gap-2">
                    <a href="?status=vacant" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 text-amber-700 text-xs font-medium rounded-full border border-amber-200 hover:bg-amber-100 transition-all">
                        <div class="w-2 h-2 bg-amber-400 rounded-full"></div>
                        {{ __('Vacant Units') }}
                    </a>
                    <a href="?status=occupied" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 text-xs font-medium rounded-full border border-emerald-200 hover:bg-emerald-100 transition-all">
                        <div class="w-2 h-2 bg-emerald-400 rounded-full"></div>
                        {{ __('Occupied Units') }}
                    </a>
                    <a href="?furnished=1" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-50 text-orange-700 text-xs font-medium rounded-full border border-orange-200 hover:bg-orange-100 transition-all">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v0"></path>
                        </svg>
                        {{ __('Furnished') }}
                    </a>
                    <a href="?furnished=0" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 text-slate-700 text-xs font-medium rounded-full border border-slate-200 hover:bg-slate-100 transition-all">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        {{ __('Unfurnished') }}
                    </a>
                    <a href="?listing_type=rent" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-purple-50 text-purple-700 text-xs font-medium rounded-full border border-purple-200 hover:bg-purple-100 transition-all">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                        {{ __('For Rent') }}
                    </a>
                    <a href="?listing_type=sale" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-50 text-green-700 text-xs font-medium rounded-full border border-green-200 hover:bg-green-100 transition-all">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21V9l9-6 9 6v12M9 21V9h6v12"></path>
                        </svg>
                        {{ __('For Sale') }}
                    </a>
                    <a href="?area=100" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-pink-50 text-pink-700 text-xs font-medium rounded-full border border-pink-200 hover:bg-pink-100 transition-all">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                        </svg>
                        {{ __('100+ sqm') }}
                    </a>
                </div>
            </div>
            
            <!-- Results Header -->
            <div class="flex items-center justify-between bg-white px-6 py-4 rounded-xl border border-slate-200/60 shadow-sm">
                <div class="flex items-center gap-4">
                    <h3 class="text-lg font-semibold text-slate-900">{{ $units->total() }} {{ __('Units Found') }}</h3>
                    <span class="text-sm text-slate-500">{{ __('Updated') }} {{ now()->diffForHumans() }}</span>
                </div>
            </div>
            
            <!-- Enhanced Table Section -->
            <div class="bg-white rounded-xl border border-slate-200/60 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200/70">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-slate-500">{{ __('Unit Name') }}</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-slate-500">{{ __('Property Type') }}</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-slate-500">{{ __('Type') }}</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-slate-500">{{ __('Price') }}</th>
                                @if(! auth()->user()?->agent_id)
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-slate-500">{{ __('Agent') }}</th>
                                @endif
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-slate-500">{{ __('Status') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/50 bg-white">
                        @forelse($units as $u)
                            @php
                                $assignedAgentNames = collect([$u->agent?->name])
                                    ->merge($u->agents->pluck('name'))
                                    ->filter()
                                    ->unique()
                                    ->values();
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 text-right">
                                    <div class="text-sm font-medium text-slate-900">{{ is_array($u->title) ? ($u->title['ar'] ?? $u->title['en'] ?? $u->code) : ($u->title ?? $u->code) }}</div>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                        {{ $u->subcategory?->name ?? $u->property?->category?->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if($u->listing_type === 'rent')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                            </svg>
                                            {{ __('For Rent') }}
                                        </span>
                                    @elseif($u->listing_type === 'sale')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21V9l9-6 9 6v12M9 21V9h6v12"></path>
                                            </svg>
                                            {{ __('For Sale') }}
                                        </span>
                                    @elseif($u->listing_type === 'both')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                            </svg>
                                            {{ __('Rent & Sale') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                            {{ \App\Models\Unit::listingTypeLabels()[$u->listing_type] ?? $u->listing_type }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="text-sm font-semibold text-slate-900">{{ number_format((float) $u->price, 2) }} {{ $u->currency ?? 'JOD' }}</div>
                                    <div class="text-xs text-slate-500">
                                        @if($u->listing_type === 'rent') {{ __('per year') }}
                                        @elseif($u->listing_type === 'both') {{ __('sale') }} @if($u->market_rent) · {{ number_format((float) $u->market_rent, 2) }} {{ __('rent/yr') }} @endif
                                        @else {{ __('sale price') }}
                                        @endif
                                    </div>
                                </td>
                                @if(! auth()->user()?->agent_id)
                                    <td class="px-4 py-3 text-right">
                                        @if($assignedAgentNames->isNotEmpty())
                                            <span class="text-xs text-slate-700">{{ $assignedAgentNames->join(', ') }}</span>
                                        @else
                                            <span class="text-xs text-slate-500">{{ __('Unassigned') }}</span>
                                        @endif
                                    </td>
                                @endif
                                <td class="px-4 py-3 text-right">
                                    @php
                                        $tone = $statusTone[$u->status] ?? 'info';
                                        $toneStyles = $statusPalette[$tone];
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $toneStyles['bg'] }} {{ $toneStyles['text'] }}">
                                        {{ \App\Models\Unit::statusLabels()[$u->status] ?? ucfirst($u->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-left">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('tenant.unit', $u->code) }}" target="_blank" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-100 transition-colors" title="{{ __('View as buyer') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                        @can('update', $u)
                                            <a href="{{ route('units.edit', $u) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">{{ __('Edit') }}</a>
                                        @endcan
                                        @can('delete', $u)
                                            <form method="post" action="{{ route('units.destroy', $u) }}" class="inline" onsubmit="return confirm('{{ __('Delete this unit?') }}')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 text-sm">{{ __('Delete') }}</button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-4 py-10 text-center text-slate-500" colspan="{{ auth()->user()?->agent_id ? 6 : 7 }}">{{ __('No units found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Enhanced Pagination -->
            @if($units->hasPages())
                <div class="flex items-center justify-between bg-white px-6 py-4 rounded-xl border border-slate-200/60 shadow-sm">
                    <div class="flex items-center gap-2 text-sm text-slate-600">
                        <span>{{ __('Showing') }}</span>
                        <span class="font-medium text-slate-900">{{ $units->firstItem() }}</span>
                        <span>{{ __('to') }}</span>
                        <span class="font-medium text-slate-900">{{ $units->lastItem() }}</span>
                        <span>{{ __('of') }}</span>
                        <span class="font-medium text-slate-900">{{ $units->total() }}</span>
                        <span>{{ __('results') }}</span>
                    </div>
                    <div class="flex items-center gap-1">
                        {{ $units->links() }}
                    </div>
                </div>
            @else
                <div class="mt-4">
                    {{ $units->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
