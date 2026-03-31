@extends('mobile.layouts.app', ['title' => 'Aqari Smart', 'subtitle' => 'Your Property Management', 'show_back_button' => false])

@section('content')
    @php
        $locale = app()->getLocale() === 'ar' ? 'ar' : 'en';
        $tenant = app(\App\Services\Tenancy\TenantManager::class)->tenant();
        abort_if(! $tenant, 404);
        
        // Get tenant's units
        $units = \App\Models\Unit::where('tenant_id', $tenant->id)
            ->with(['property', 'category', 'subcategory', 'photos'])
            ->where('status', \App\Models\Unit::STATUS_VACANT)
            ->orderByDesc('created_at')
            ->limit(12)
            ->get();
            
        $featuredUnits = $units->take(4);
        $allUnits = $units;
        
        // Get tenant stats
        $stats = [
            'total_units' => \App\Models\Unit::where('tenant_id', $tenant->id)->count(),
            'vacant_units' => \App\Models\Unit::where('tenant_id', $tenant->id)->where('status', \App\Models\Unit::STATUS_VACANT)->count(),
            'occupied_units' => \App\Models\Unit::where('tenant_id', $tenant->id)->where('status', \App\Models\Unit::STATUS_OCCUPIED)->count(),
        ];
    @endphp

    <!-- Hero Section -->
    <section class="overflow-hidden bg-gradient-to-br from-emerald-700 via-emerald-800 to-emerald-900 text-white shadow-md">
        <div class="space-y-6 px-5 py-8">
            <div class="space-y-3">
                <h1 class="text-3xl font-bold leading-tight">{{ $tenant->name }}</h1>
                <p class="max-w-xl text-base leading-relaxed text-emerald-100/90">
                    {{ app()->getLocale() === 'ar' ? 'شريكتك العقاري الخاص بك' : 'Your property management system' }}
                </p>
            </div>
            
            <!-- Stats -->
            <div class="grid grid-cols-3 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold">{{ $stats['total_units'] }}</div>
                    <div class="text-sm text-emerald-100/80">{{ app()->locale === 'ar' ? 'إجمالي' : 'Total' }}</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold">{{ $stats['vacant_units'] }}</div>
                    <div class="text-sm text-emerald-100/80">{{ app()->locale === 'ar' ? 'شاغر' : 'Vacant' }}</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold">{{ $stats['occupied_units'] }}</div>
                    <div class="text-sm text-emerald-100/80">{{ app()->locale === 'ar' ? 'مشغول' : 'Occupied' }}</div>
                </div>
            </div>
        </section>
    </section>

    <!-- Featured Properties -->
    <section class="space-y-4 px-5 pt-6">
        <div class="flex items-end justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-800">{{ app()->getLocale() === 'ar' ? 'العقارات المميزة' : 'Featured Properties' }}</h2>
                <p class="mt-1 text-sm font-medium text-slate-500">{{ app()->getLocale() === 'ar' ? 'أحدث إضافاتنا' : 'Our latest listings' }}</p>
            </div>
        </div>
        
        <div class="grid gap-4 sm:grid-cols-2">
            @foreach ($featuredUnits as $unit)
                <a href="/mobile/units/{{ $unit->code }}" class="group flex overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 transition-all hover:-translate-y-1 hover:shadow-md hover:ring-emerald-400">
                    <div class="w-2/5 shrink-0 bg-slate-100">
                        @if($unit->photos && count($unit->photos) > 0)
                            <img src="{{ $unit->photos[0] }}" alt="{{ $unit->title }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                        @else
                            <div class="h-full w-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center">
                                <svg class="h-12 w-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7m7 7V5a2 2 0 012-2h-2a2 2 0 01-2-2V6a2 2 0 012-2h2a2 2 0 012 2v2m7 7V10a2 2 0 01-2 2H9a2 2 0 01-2-2V5a2 2 0 012-2h2a2 2 0 012 2v2z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex flex-col justify-between p-4 w-3/5">
                        <div>
                            <h3 class="font-semibold text-slate-900 line-clamp-2">{{ $unit->title }}</h3>
                            <p class="text-sm text-slate-500 line-clamp-1">{{ $unit->property->name ?? '' }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-emerald-600">
                                {{ $unit->currency ?? 'JOD' }} {{ number_format($unit->price) }}
                            </div>
                            <div class="text-xs text-slate-400">
                                {{ $unit->listing_type === 'sale' ? (app()->getLocale() === 'ar' ? 'للبيع' : 'For Sale') : (app()->getLocale() === 'ar' ? 'للإيجار' : 'For Rent') }}
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <!-- All Properties -->
    <section class="space-y-4 px-5 pt-2">
        <div class="flex items-end justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-800">{{ app()->getLocale() === 'ar' ? 'كل العقارات' : 'All Properties' }}</h2>
                <p class="mt-1 text-sm font-medium text-slate-500">{{ app()->getLocale() === 'ar' ? 'استعرض جميع الإعلانات' : 'Browse all listings' }}</p>
            </div>
        </div>
        
        <div class="grid gap-4">
            @foreach ($allUnits as $unit)
                <a href="/mobile/units/{{ $unit->code }}" class="group flex overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 transition-all hover:-translate-y-1 hover:shadow-md hover:ring-emerald-400">
                    <div class="w-2/5 shrink-0 bg-slate-100">
                        @if($unit->photos && count($unit->photos) > 0)
                            <img src="{{ $unit->photos[0] }}" alt="{{ $unit->title }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                        @else
                            <div class="h-full w-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center">
                                <svg class="h-12 w-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7m7 7V5a2 2 0 012-2h-2a2 2 0 01-2-2V6a2 2 0 012-2h2a2 2 0 012 2v2m7 7V10a2 2 0 01-2 2H9a2 2 0 01-2-2V5a2 2 0 012-2h2a2 2 0 012 2v2z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex flex-col justify-between p-4 w-3/5">
                        <div>
                            <h3 class="font-semibold text-slate-900 line-clamp-2">{{ $unit->title }}</h3>
                            <p class="text-sm text-slate-500 line-clamp-1">{{ $unit->property->name ?? '' }}</p>
                            @if($unit->bedrooms)
                                <div class="flex items-center gap-2 text-xs text-slate-400">
                                    <span>{{ $unit->bedrooms }} {{ app()->getLocale() === 'ar' ? 'غرف نوم' : 'Bedrooms' }}</span>
                                    @if($unit->bathrooms)
                                        <span>• {{ $unit->bathrooms }} {{ app()->getLocale() === 'ar' ? 'حمام' : 'Bathrooms' }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-emerald-600">
                                {{ $unit->currency ?? 'JOD' }} {{ number_format($unit->price) }}
                            </div>
                            <div class="text-xs text-slate-400">
                                {{ $unit->listing_type === 'sale' ? (app()->getLocale() === 'ar' ? 'للبيع' : 'For Sale') : (app()->getLocale() === 'ar' ? 'للإيجار' : 'For Rent') }}
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <!-- Quick Actions -->
    <section class="space-y-4 px-5 pt-2">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-800">{{ app()->getLocale() === 'ar' ? 'إجراءات سريعة' : 'Quick Actions' }}</h2>
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-4">
            <a href="/mobile/dashboard" class="flex items-center justify-center gap-3 rounded-xl bg-emerald-600 text-white px-4 py-3 font-semibold shadow-lg transition-all hover:bg-emerald-700 hover:shadow-xl">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7m7 7V5a2 2 0 012-2h-2a2 2 0 01-2-2V6a2 2 0 012-2h2a2 2 0 012 2v2z"/>
                </svg>
                <span>{{ app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Dashboard' }}</span>
            </a>
            
            <a href="/mobile/units" class="flex items-center justify-center gap-3 rounded-xl bg-white border border-slate-200 text-slate-700 px-4 py-3 font-semibold shadow-sm transition-all hover:bg-slate-50 hover:border-slate-300">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ app()->getLocale() === 'ar' ? 'العقارات' : 'Properties' }}</span>
            </a>
        </div>
    </section>
@endsection
