@php
    $locale = app()->getLocale();
@endphp

@extends('mobile.layouts.app', ['title' => 'Unit Details', 'subtitle' => $unit->code])

@section('content')
    <div class="space-y-4">
        <!-- Hero Section with Image Gallery -->
        <div class="relative h-64 overflow-hidden rounded-3xl bg-slate-100">
            @if($unit->photos && count($unit->photos) > 0)
            <div class="h-full w-full">
                <div class="flex h-full overflow-x-auto overflow-y-hidden scrollbar-hide" style="scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch; scroll-behavior: smooth;">
                    @foreach($unit->photos as $index => $photo)
                    <div class="w-full h-full flex-shrink-0" style="scroll-snap-align: center;">
                        <img src="{{ $photo }}" alt="{{ $unit->title->{$locale} ?? $unit->title->en ?? $unit->code }}" class="h-full w-full object-cover cursor-pointer" onclick="openFullscreen({{ $index }})">
                    </div>
                    @endforeach
                </div>
            </div>
            <!-- Image Gallery Indicator -->
            <div class="absolute bottom-4 right-4 bg-black/60 backdrop-blur-sm rounded-full px-3 py-1 text-xs font-medium text-white">
                1/{{ count($unit->photos) }}
            </div>
            <!-- Dots Indicator -->
            <div class="absolute bottom-4 left-4 flex gap-1" id="gallery-dots">
                @for($i = 0; $i < count($unit->photos); $i++)
                <div class="w-2 h-2 rounded-full {{ $i === 0 ? 'bg-white' : 'bg-white/50' }}" data-dot="{{ $i }}"></div>
                @endfor
            </div>
            @else
                <div class="flex h-full items-center justify-center bg-gradient-to-br from-emerald-600 to-emerald-800">
                    <svg class="h-16 w-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 22V12h6v10"/>
                    </svg>
                </div>
            @endif
            
            <!-- Listing Type Badge -->
            <div class="absolute left-4 top-4">
                <span class="inline-flex items-center rounded-xl bg-emerald-600/90 backdrop-blur-sm px-3 py-1.5 text-xs font-bold tracking-wider text-white shadow-lg">
                    {{ $unit->listing_type === 'sale' ? (app()->getLocale() === 'ar' ? 'للبيع' : 'For Sale') : (app()->getLocale() === 'ar' ? 'للإيجار' : 'For Rent') }}
                </span>
            </div>
            
            <!-- Featured Badge -->
            @if($unit->featured ?? false)
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center rounded-xl bg-amber-500/90 backdrop-blur-sm px-3 py-1.5 text-xs font-bold uppercase tracking-wider text-white shadow-lg">
                    {{ app()->getLocale() === 'ar' ? 'مميز' : 'Featured' }}
                </span>
            </div>
            @endif
        </div>

        <!-- Price and Title Card -->
        <div class="rounded-3xl bg-white p-6 shadow-lg ring-1 ring-slate-200">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="text-xs font-bold uppercase tracking-wider text-emerald-600">{{ $unit->subcategory->name ?? 'Property' }}</div>
                    <h2 class="mt-2 text-2xl font-bold text-slate-900">{{ $unit->title->{$locale} ?? $unit->title->en ?? $unit->code }}</h2>
                    <p class="mt-1 text-sm font-medium text-slate-500">{{ $unit->code }}</p>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-slate-900">{{ $unit->currency ?? 'JOD' }} {{ number_format($unit->price ?? 0) }}</div>
                    <div class="mt-1 text-xs font-semibold text-slate-400">
                        @if($unit->listing_type === 'rent')
                            {{ app()->getLocale() === 'ar' ? 'سنوياً' : 'per year' }}
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Specifications -->
        <div class="rounded-3xl bg-white p-6 shadow-lg ring-1 ring-slate-200">
            <h3 class="font-semibold text-slate-900 mb-4">{{ app()->getLocale() === 'ar' ? 'المواصفات' : 'Specifications' }}</h3>
            <div class="grid grid-cols-2 gap-4">
                @if($unit->bedrooms ?? $unit->beds ?? false)
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100">
                        <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-bold text-slate-800">{{ $unit->bedrooms ?? $unit->beds ?? 0 }}</div>
                        <div class="text-xs font-medium text-slate-400">{{ app()->getLocale() === 'ar' ? 'غرف نوم' : 'Bedrooms' }}</div>
                    </div>
                </div>
                @endif
                
                @if($unit->bathrooms ?? $unit->baths ?? false)
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100">
                        <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-bold text-slate-800">{{ $unit->bathrooms ?? $unit->baths ?? 0 }}</div>
                        <div class="text-xs font-medium text-slate-400">{{ app()->getLocale() === 'ar' ? 'حمامات' : 'Bathrooms' }}</div>
                    </div>
                </div>
                @endif
                
                @if($unit->area_m2 ?? $unit->sqft ?? $unit->area ?? false)
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100">
                        <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m-4 0l-5-5"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-bold text-slate-800">{{ $unit->area_m2 ?? $unit->area ?? 0 }} m²</div>
                        <div class="text-xs font-medium text-slate-400">{{ app()->getLocale() === 'ar' ? 'المساحة' : 'Area' }}</div>
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Dynamic Attributes -->
            @if($unit->unitAttributes && $unit->unitAttributes->count() > 0)
            <div class="mt-4 border-t border-slate-100 pt-4">
                <h4 class="text-sm font-semibold text-slate-900 mb-3">{{ app()->getLocale() === 'ar' ? 'مواصفات إضافية' : 'Additional Features' }}</h4>
                <div class="grid grid-cols-2 gap-3">
                    @foreach($unit->unitAttributes as $attribute)
                    @if($attribute->attribute_field ?? false)
                    <div class="flex items-center gap-2">
                        <div class="h-2 w-2 rounded-full bg-emerald-500"></div>
                        <span class="text-sm text-slate-700">
                            {{ $attribute->attribute_field->{'name_' . $locale} ?? $attribute->attribute_field->name_en ?? $attribute->attribute_field->name ?? '' }}:
                            {{ $attribute->string_value ?? $attribute->int_value ?? $attribute->decimal_value ?? ($attribute->bool_value ? (app()->getLocale() === 'ar' ? 'نعم' : 'Yes') : (app()->getLocale() === 'ar' ? 'لا' : 'No')) }}
                        </span>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Location Information -->
        <div class="rounded-3xl bg-white p-6 shadow-lg ring-1 ring-slate-200">
            <h3 class="font-semibold text-slate-900 mb-4">{{ app()->getLocale() === 'ar' ? 'الموقع' : 'Location' }}</h3>
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100">
                        <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="text-sm font-medium text-slate-900">{{ $unit->property->name ?? 'Property Name' }}</div>
                        <div class="text-xs text-slate-500">{{ app()->getLocale() === 'ar' ? 'اسم العقار' : 'Property' }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100">
                        <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="text-sm font-medium text-slate-900">{{ $unit->city->{'name_' . $locale} ?? $unit->city->name_en ?? 'City' }}</div>
                        <div class="text-xs text-slate-500">{{ app()->getLocale() === 'ar' ? 'المدينة' : 'City' }}</div>
                    </div>
                </div>
                @if($unit->location ?? false)
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100">
                        <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="text-sm font-medium text-slate-900">{{ $unit->location }}</div>
                        <div class="text-xs text-slate-500">{{ app()->getLocale() === 'ar' ? 'العنوان' : 'Address' }}</div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        
        <!-- Description -->
        @if($unit->description)
        <div class="rounded-3xl bg-white p-6 shadow-lg ring-1 ring-slate-200">
            <h3 class="font-semibold text-slate-900 mb-4">{{ app()->getLocale() === 'ar' ? 'الوصف' : 'Description' }}</h3>
            <div class="prose prose-sm max-w-none">
                <p class="text-sm leading-relaxed text-slate-600">
                    {{ $unit->description->{$locale} ?? $unit->description->en ?? $unit->description }}
                </p>
            </div>
        </div>
        @endif

        <!-- Agent Information -->
        @if($unit->agent ?? false)
        <div class="rounded-3xl bg-white p-6 shadow-lg ring-1 ring-slate-200">
            <h3 class="font-semibold text-slate-900 mb-4">{{ app()->getLocale() === 'ar' ? 'الوكيل' : 'Agent' }}</h3>
            <div class="flex items-center gap-4">
                <!-- Agent Avatar -->
                <div class="relative">
                    @if($unit->agent->avatar ?? false)
                    <img src="{{ $unit->agent->avatar }}" alt="{{ $unit->agent->name }}" class="h-16 w-16 rounded-full object-cover ring-2 ring-emerald-100">
                    @else
                    <div class="h-16 w-16 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center ring-2 ring-emerald-100">
                        <span class="text-xl font-bold text-white">
                            {{ substr($unit->agent->name, 0, 1) }}
                        </span>
                    </div>
                    @endif
                    <!-- Online Status -->
                    @if($unit->agent->available ?? true)
                    <div class="absolute bottom-0 right-0 h-4 w-4 rounded-full bg-green-500 ring-2 ring-white"></div>
                    @endif
                </div>
                
                <!-- Agent Details -->
                <div class="flex-1">
                    <div class="font-semibold text-slate-900">{{ $unit->agent->name }}</div>
                    <div class="text-sm text-slate-500">{{ $unit->agent->title ?? 'Real Estate Agent' }}</div>
                    <div class="mt-1 flex items-center gap-2">
                        @if($unit->agent->rating ?? false)
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                            <svg class="h-3 w-3 {{ $i <= $unit->agent->rating ? 'text-amber-400' : 'text-slate-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            @endfor
                            <span class="text-xs text-slate-500 ml-1">({{ $unit->agent->rating }})</span>
                        </div>
                        @endif
                        @if($unit->agent->verified ?? false)
                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-800">
                            {{ app()->getLocale() === 'ar' ? 'موثق' : 'Verified' }}
                        </span>
                        @endif
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="flex flex-col gap-2">
                    <a href="tel:{{ $unit->agent->phone ?? $unit->phone }}" class="inline-flex items-center justify-center rounded-lg bg-emerald-600 p-2 text-white transition hover:bg-emerald-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 2.493A1 1 0 008.28 9H17v1a1 1 0 001 1H20a2 2 0 002-2V5a2 2 0 00-2-2h-3.28a1 1 0 01-.948-.684l-1.498-2.493A1 1 0 008.72 3H5a2 2 0 00-2 2z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8h18"/>
                        </svg>
                    </a>
                    <a href="mailto:{{ $unit->agent->email ?? $unit->email }}" class="inline-flex items-center justify-center rounded-lg bg-slate-100 p-2 text-slate-700 transition hover:bg-slate-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </a>
                </div>
            </div>
            
            <!-- Agent Stats -->
            @if($unit->agent->properties_count ?? false)
            <div class="mt-4 grid grid-cols-3 gap-4 border-t border-slate-100 pt-4">
                <div class="text-center">
                    <div class="text-lg font-bold text-slate-900">{{ $unit->agent->properties_count ?? 0 }}</div>
                    <div class="text-xs text-slate-500">{{ app()->getLocale() === 'ar' ? 'عقارات' : 'Properties' }}</div>
                </div>
                <div class="text-center">
                    <div class="text-lg font-bold text-slate-900">{{ $unit->agent->experience_years ?? 0 }}</div>
                    <div class="text-xs text-slate-500">{{ app()->getLocale() === 'ar' ? 'سنوات خبرة' : 'Years Exp.' }}</div>
                </div>
                <div class="text-center">
                    <div class="text-lg font-bold text-slate-900">{{ $unit->agent->response_rate ?? 0 }}%</div>
                    <div class="text-xs text-slate-500">{{ app()->getLocale() === 'ar' ? 'معدل استجابة' : 'Response' }}</div>
                </div>
            </div>
            @endif
            
            <!-- Contact Agent Button -->
            <div class="mt-6 flex gap-3">
                <a href="tel:{{ $unit->agent->phone ?? $unit->phone }}" class="flex-1 inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow-lg transition hover:bg-emerald-700 hover:shadow-xl">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 2.493A1 1 0 008.28 9H17v1a1 1 0 001 1H20a2 2 0 002-2V5a2 2 0 00-2-2h-3.28a1 1 0 01-.948-.684l-1.498-2.493A1 1 0 008.72 3H5a2 2 0 00-2 2z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8h18"/>
                    </svg>
                    {{ app()->getLocale() === 'ar' ? 'اتصل بالوكيل' : 'Contact Agent' }}
                </a>
                <a href="mailto:{{ $unit->agent->email ?? $unit->email }}" class="inline-flex items-center justify-center rounded-2xl bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    {{ app()->getLocale() === 'ar' ? 'رسالة' : 'Message' }}
                </a>
            </div>
        </div>
        @else
        <!-- Fallback Contact Section (No Agent) -->
        <div class="rounded-3xl bg-white p-6 shadow-lg ring-1 ring-slate-200">
            <h3 class="font-semibold text-slate-900 mb-4">{{ app()->getLocale() === 'ar' ? 'معلومات الاتصال' : 'Contact Information' }}</h3>
            <div class="space-y-3">
                @if($unit->phone ?? false)
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100">
                        <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 2.493A1 1 0 008.28 9H17v1a1 1 0 001 1H20a2 2 0 002-2V5a2 2 0 00-2-2h-3.28a1 1 0 01-.948-.684l-1.498-2.493A1 1 0 008.72 3H5a2 2 0 00-2 2z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8h18"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="text-sm font-medium text-slate-900">{{ $unit->phone }}</div>
                        <div class="text-xs text-slate-500">{{ app()->getLocale() === 'ar' ? 'رقم الهاتف' : 'Phone' }}</div>
                    </div>
                </div>
                @endif
                @if($unit->email ?? false)
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100">
                        <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="text-sm font-medium text-slate-900">{{ $unit->email }}</div>
                        <div class="text-xs text-slate-500">{{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email' }}</div>
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Contact Buttons (No Agent) -->
            <div class="mt-6 flex gap-3">
                <a href="tel:{{ $unit->phone ?? '' }}" class="flex-1 inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow-lg transition hover:bg-emerald-700 hover:shadow-xl">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 2.493A1 1 0 008.28 9H17v1a1 1 0 001 1H20a2 2 0 002-2V5a2 2 0 00-2-2h-3.28a1 1 0 01-.948-.684l-1.498-2.493A1 1 0 008.72 3H5a2 2 0 00-2 2z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8h18"/>
                    </svg>
                    {{ app()->getLocale() === 'ar' ? 'اتصل الآن' : 'Call Now' }}
                </a>
                <a href="mailto:{{ $unit->email ?? 'info@example.com' }}" class="inline-flex items-center justify-center rounded-2xl bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    {{ app()->getLocale() === 'ar' ? 'رسالة' : 'Message' }}
                </a>
            </div>
        </div>
        @endif
    </div>
@endsection

<!-- Fullscreen Image Modal -->
<div id="fullscreen-modal" class="fixed inset-0 z-50 hidden bg-black/95 backdrop-blur-sm">
    <div class="relative h-full w-full">
        <!-- Close Button -->
        <button onclick="closeFullscreen()" class="absolute top-4 right-4 z-10 rounded-full bg-white/10 backdrop-blur-sm p-3 text-white hover:bg-white/20 transition-colors">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        
        <!-- Image Container -->
        <div class="h-full w-full flex items-center justify-center">
            <img id="fullscreen-image" src="" alt="" class="max-h-full max-w-full object-contain">
        </div>
        
        <!-- Navigation Buttons -->
        <button onclick="previousImage()" class="absolute left-4 top-1/2 -translate-y-1/2 rounded-full bg-white/10 backdrop-blur-sm p-3 text-white hover:bg-white/20 transition-colors">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>
        <button onclick="nextImage()" class="absolute right-4 top-1/2 -translate-y-1/2 rounded-full bg-white/10 backdrop-blur-sm p-3 text-white hover:bg-white/20 transition-colors">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
        
        <!-- Image Counter -->
        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 bg-black/60 backdrop-blur-sm rounded-full px-4 py-2 text-sm font-medium text-white">
            <span id="fullscreen-counter">1/3</span>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const gallery = document.querySelector('.overflow-x-auto');
    const dots = document.querySelectorAll('[data-dot]');
    const totalImages = {{ count($unit->photos) }};
    
    // Fullscreen slideshow variables
    let currentImageIndex = 0;
    const modal = document.getElementById('fullscreen-modal');
    const fullscreenImage = document.getElementById('fullscreen-image');
    const fullscreenCounter = document.getElementById('fullscreen-counter');
    
    // Array of all images
    const allImages = [
        @foreach($unit->photos as $photo)
        '{{ $photo }}',
        @endforeach
    ];
    
    // Fullscreen functions
    function openFullscreen(index) {
        currentImageIndex = index;
        updateFullscreenImage();
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    function closeFullscreen() {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    
    function updateFullscreenImage() {
        fullscreenImage.src = allImages[currentImageIndex];
        fullscreenCounter.textContent = `${currentImageIndex + 1}/${allImages.length}`;
    }
    
    function nextImage() {
        currentImageIndex = (currentImageIndex + 1) % allImages.length;
        updateFullscreenImage();
    }
    
    function previousImage() {
        currentImageIndex = (currentImageIndex - 1 + allImages.length) % allImages.length;
        updateFullscreenImage();
    }
    
    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (!modal.classList.contains('hidden')) {
            if (e.key === 'Escape') closeFullscreen();
            if (e.key === 'ArrowRight') nextImage();
            if (e.key === 'ArrowLeft') previousImage();
        }
    });
    
    // Touch swipe for fullscreen
    let touchStartX = 0;
    let touchEndX = 0;
    
    fullscreenImage.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
    });
    
    fullscreenImage.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    });
    
    function handleSwipe() {
        if (touchEndX < touchStartX - 50) nextImage();
        if (touchEndX > touchStartX + 50) previousImage();
    }
    
    // Make functions global
    window.openFullscreen = openFullscreen;
    window.closeFullscreen = closeFullscreen;
    window.nextImage = nextImage;
    window.previousImage = previousImage;
    
    // Original gallery functionality
    if (gallery && dots.length > 0) {
        let isScrolling = false;
        let scrollTimeout;
        
        // Update dots function
        function updateDots() {
            const scrollLeft = gallery.scrollLeft;
            const itemWidth = gallery.offsetWidth;
            const currentIndex = Math.round(scrollLeft / itemWidth);
            
            dots.forEach((dot, index) => {
                if (index === currentIndex) {
                    dot.classList.remove('bg-white/50');
                    dot.classList.add('bg-white');
                } else {
                    dot.classList.remove('bg-white');
                    dot.classList.add('bg-white/50');
                }
            });
            
            // Update counter
            const counter = document.querySelector('.absolute.bottom-4.right-4 span');
            if (counter) {
                counter.textContent = `${currentIndex + 1}/${totalImages}`;
            }
        }
        
        // Disable scroll-snap temporarily during touch
        gallery.addEventListener('touchstart', () => {
            gallery.style.scrollSnapType = 'none';
            isScrolling = true;
        });
        
        gallery.addEventListener('touchmove', (e) => {
            if (!isScrolling) return;
            updateDots(); // Update dots during scroll
        });
        
        gallery.addEventListener('touchend', () => {
            // Re-enable scroll-snap after a short delay
            setTimeout(() => {
                gallery.style.scrollSnapType = 'x mandatory';
                isScrolling = false;
                updateDots(); // Final update
            }, 100);
        });
        
        // Mouse events for desktop
        gallery.addEventListener('mousedown', () => {
            gallery.style.scrollSnapType = 'none';
            isScrolling = true;
        });
        
        gallery.addEventListener('mousemove', () => {
            if (isScrolling) {
                updateDots(); // Update dots during scroll
            }
        });
        
        gallery.addEventListener('mouseup', () => {
            setTimeout(() => {
                gallery.style.scrollSnapType = 'x mandatory';
                isScrolling = false;
                updateDots(); // Final update
            }, 100);
        });
        
        gallery.addEventListener('mouseleave', () => {
            if (isScrolling) {
                gallery.style.scrollSnapType = 'x mandatory';
                isScrolling = false;
                updateDots(); // Final update
            }
        });
        
        // Prevent snap back during scroll
        gallery.addEventListener('scroll', () => {
            if (isScrolling) {
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(() => {
                    if (!isScrolling) {
                        // Find nearest snap point with better calculation
                        const scrollLeft = gallery.scrollLeft;
                        const itemWidth = gallery.offsetWidth;
                        const currentIndex = Math.round(scrollLeft / itemWidth);
                        gallery.scrollTo({
                            left: currentIndex * itemWidth,
                            behavior: 'smooth'
                        });
                    }
                }, 150);
            }
            updateDots(); // Always update dots on scroll
        });
        
        // Add wheel event for desktop scrolling
        gallery.addEventListener('wheel', (e) => {
            if (Math.abs(e.deltaY) > Math.abs(e.deltaX)) {
                e.preventDefault();
                gallery.scrollLeft += e.deltaY;
            }
        });
        
        // Initialize dots
        updateDots();
    }
});
</script>
@endpush
