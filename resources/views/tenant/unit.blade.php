@extends('layouts.app')

@php
    $propertyName = $unit->property?->name ?? ($unit->translated_title ?: __('Standalone listing'));
@endphp
@section('title', __('Unit :code - :property', ['code' => $unit->code, 'property' => $propertyName]))

@section('content')
    @php
        $tenantCtx = $tenantCtx ?? app(\App\Services\Tenancy\TenantManager::class)->tenant();
        $gallery = [];
        if (is_array($unit->photos) && count($unit->photos)) {
            $gallery = $unit->photos;
        } elseif (is_array($unit->property?->photos) && count($unit->property?->photos)) {
            $gallery = $unit->property->photos;
        }
        
        $isSale = ($unit->listing_type ?? \App\Models\Unit::LISTING_RENT) === \App\Models\Unit::LISTING_SALE;
        $unitCurr = $unit->currency ?? ($tenantCtx?->settings['currency'] ?? 'JOD');
        $displayPrice = $isSale
            ? ($unit->price ?? 0)
            : ($unit->market_rent && $unit->market_rent > 0 ? $unit->market_rent / 100 : ($unit->price ?? 0));
    @endphp

    <div class="min-h-screen bg-gray-50" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <!-- Hero Gallery Section - Clean Image Only -->
        <div x-data="{ 
            idx: 0, 
            imgs: @js($gallery),
            showGallery: false,
            galleryIdx: 0
        }" class="relative">
            
            <!-- Main Hero Image - Pure & Clean -->
            <div class="relative h-[60vh] sm:h-[70vh] lg:h-[85vh] bg-gradient-to-br from-gray-900 to-gray-700 overflow-hidden">
                <template x-if="imgs.length">
                    <img :src="(imgs[idx].startsWith('http') ? imgs[idx] : '{{ url('/') }}/' + (imgs[idx].startsWith('storage/') ? imgs[idx] : 'storage/' + imgs[idx]))"
                        onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 400 300%22%3E%3Crect fill=%22%23f3f4f6%22 width=%22400%22 height=%22300%22/%3E%3Ctext fill=%22%239ca3af%22 font-family=%22Arial%22 font-size=%2218%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22%3EImage not available%3C/text%3E%3C/svg%3E'"
                        class="absolute inset-0 h-full w-full object-cover transition-all duration-700" 
                        alt="{{ $unit->translated_title }}" />
                </template>
                
                <template x-if="!imgs.length">
                    <div class="h-full w-full bg-gradient-to-br brand-bg flex items-center justify-center">
                        <div class="text-center text-white">
                            <svg class="w-32 h-32 mx-auto mb-6 opacity-50" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                            </svg>
                            <p class="text-2xl font-medium opacity-75">{{ __('Property Photos Coming Soon') }}</p>
                        </div>
                    </div>
                </template>
                
                <!-- Minimal Navigation Arrows -->
                <template x-if="imgs.length > 1">
                    <div class="absolute inset-0 flex items-center justify-between px-4 sm:px-8 pointer-events-none">
                        <button @click="idx = (idx - 1 + imgs.length) % imgs.length" 
                            class="pointer-events-auto w-12 h-12 sm:w-16 sm:h-16 rounded-full bg-white/95 hover:bg-white shadow-2xl flex items-center justify-center transition-all hover:scale-110 backdrop-blur-sm">
                            <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <button @click="idx = (idx + 1) % imgs.length" 
                            class="pointer-events-auto w-12 h-12 sm:w-16 sm:h-16 rounded-full bg-white/95 hover:bg-white shadow-2xl flex items-center justify-center transition-all hover:scale-110 backdrop-blur-sm">
                            <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </template>

                <!-- Minimal Photo Counter (Top Right) -->
                <template x-if="imgs.length > 1">
                    <div class="absolute top-6 right-6 sm:top-8 sm:right-8">
                        <button @click="showGallery = true" 
                            class="bg-black/60 backdrop-blur-md text-white px-4 py-2 sm:px-5 sm:py-3 rounded-xl text-sm sm:text-base font-semibold hover:bg-black/80 transition-all flex items-center gap-2 shadow-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span x-text="(idx + 1) + ' / ' + imgs.length"></span>
                        </button>
                    </div>
                </template>

                <!-- Elegant Dots Indicator (Bottom Center) -->
                <template x-if="imgs.length > 1 && imgs.length <= 10">
                    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex gap-2.5 bg-black/40 backdrop-blur-md rounded-full px-5 py-3.5 shadow-xl">
                        <template x-for="(img,i) in imgs" :key="i">
                            <button @click="idx = i" 
                                class="transition-all duration-300 rounded-full" 
                                :class="idx === i ? 'bg-white w-10 h-3' : 'bg-white/60 hover:bg-white/90 w-3 h-3'">
                            </button>
                        </template>
                    </div>
                </template>
            </div>

            <!-- Thumbnail Gallery Modal -->
            <div x-show="showGallery" x-cloak 
                class="fixed inset-0 z-50 bg-black/95 backdrop-blur-sm flex items-center justify-center p-4"
                @click.self="showGallery = false">
                <div class="w-full max-w-6xl">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-bold text-white">{{ __('Property Gallery') }}</h3>
                        <button @click="showGallery = false" 
                            class="w-10 h-10 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center text-white transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <template x-for="(img, i) in imgs" :key="i">
                            <button @click="idx = i; showGallery = false" 
                                class="aspect-square rounded-xl overflow-hidden hover:scale-105 transition-transform">
                                <img :src="(img.startsWith('http') ? img : '{{ url('/') }}/' + (img.startsWith('storage/') ? img : 'storage/' + img))"
                                    class="w-full h-full object-cover" />
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Property Header Section - Below Image -->
        <div class="bg-white border-b border-gray-200 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-10">
                <!-- Status Badges -->
                <div class="flex flex-wrap items-center gap-2 sm:gap-3 mb-4 sm:mb-6">
                    <span class="inline-flex items-center gap-2 rounded-xl {{ $isSale ? 'bg-emerald-500' : 'brand-bg' }} text-white px-4 py-2 sm:px-6 sm:py-3 text-sm sm:text-base font-bold shadow-lg">
                        @if($isSale)
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                            </svg>
                            {{ __('For Sale') }}
                        @else
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/>
                            </svg>
                            {{ __('For Rent') }}
                        @endif
                    </span>
                    
                    @if($unit->subcategory)
                    <span class="inline-flex items-center rounded-xl bg-gray-100 text-gray-700 px-4 py-2 sm:px-5 sm:py-3 text-sm sm:text-base font-semibold">
                        {{ $unit->subcategory->name }}
                    </span>
                    @endif
                    
                    <span class="inline-flex items-center rounded-xl bg-gray-100 text-gray-700 px-4 py-2 sm:px-5 sm:py-3 text-sm sm:text-base font-semibold">
                        <svg class="w-4 h-4 ltr:mr-2 rtl:ml-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                        </svg>
                        {{ $unit->property?->city ?? __('Prime Location') }}
                    </span>
                </div>
                
                <!-- Title, Unit Code & Price -->
                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                    <div class="flex-1">
                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 mb-3 leading-tight">
                            {{ $unit->translated_title ?: $propertyName }}
                        </h1>
                        <div class="flex items-center gap-3 text-lg sm:text-xl text-gray-600">
                            <span class="font-semibold">{{ __('Unit') }} {{ $unit->code }}</span>
                            @if($unit->property)
                            <span class="text-gray-400">•</span>
                            <span>{{ $propertyName }}</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl sm:rounded-3xl p-6 sm:p-8 border-2 border-blue-100 lg:min-w-[280px]">
                        <div class="text-gray-600 text-sm sm:text-base font-medium mb-2">
                            {{ $isSale ? __('Asking Price') : __('Annual Rent') }}
                        </div>
                        <div class="text-3xl sm:text-4xl lg:text-5xl font-bold brand-text mb-1">
                            {{ $unitCurr }} {{ number_format($displayPrice, 0) }}
                        </div>
                        @if(!$isSale)
                            <div class="text-gray-500 text-sm">
                                {{ __('per year') }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Stats Bar -->
                <div class="mt-8 grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="flex items-center gap-3 p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl">
                        <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.25 18.75a.75.75 0 0 0 1.5 0V15h16.5v3.75a.75.75 0 0 0 1.5 0V6A2.25 2.25 0 0 0 19.5 3.75h-15A2.25 2.25 0 0 0 2.25 6v12.75Zm3-9a1.5 1.5 0 0 1 1.5-1.5H9A1.5 1.5 0 0 1 10.5 9.75v.75h-4.5v-.75Zm6 0A1.5 1.5 0 0 1 13.5 8.25H18a1.5 1.5 0 0 1 1.5 1.5v.75h-8.25v-.75Z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ $unit->beds }}</div>
                            <div class="text-sm text-gray-600 font-medium">{{ __('Bedrooms') }}</div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3 p-4 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl">
                        <div class="w-12 h-12 bg-emerald-500 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 9.75A2.25 2.25 0 0 1 5.25 7.5H12V6a3.75 3.75 0 1 1 7.5 0v.75a.75.75 0 0 1-1.5 0V6a2.25 2.25 0 1 0-4.5 0v1.5h4.5A2.25 2.25 0 0 1 21 9.75v4.5a4.5 4.5 0 0 1-4.5 4.5H7.5A4.5 4.5 0 0 1 3 14.25v-4.5Z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ $unit->baths }}</div>
                            <div class="text-sm text-gray-600 font-medium">{{ __('Bathrooms') }}</div>
                        </div>
                    </div>
                    
                    @if($unit->sqft)
                    <div class="flex items-center gap-3 p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl">
                        <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0v12h8V4H6z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ number_format($unit->sqft) }}</div>
                            <div class="text-sm text-gray-600 font-medium">{{ __('Sq Ft') }}</div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="flex items-center gap-3 p-4 bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl">
                        <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-lg font-bold text-gray-900">{{ $unit->property?->city ?? __('Prime') }}</div>
                            <div class="text-sm text-gray-600 font-medium">{{ __('Location') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-12">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-12">
                <!-- Left Column - Main Content -->
                <div class="lg:col-span-2 space-y-6 sm:space-y-8">
                    <!-- Description -->
                    <div class="bg-white rounded-2xl sm:rounded-3xl shadow-lg p-6 sm:p-8 border border-gray-100">
                        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4 sm:mb-6">{{ __('About This Property') }}</h2>
                        <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                            {{ $unit->translated_description ?: __('This beautiful property offers modern living in a prime location. Contact us to learn more about the amenities and features that make this unit special.') }}
                        </div>
                    </div>

                    <!-- Specifications -->
                    @if ($unit->unitAttributes->count() > 0)
                        <div class="bg-white rounded-2xl sm:rounded-3xl shadow-lg p-6 sm:p-8 border border-gray-100">
                            <h3 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4 sm:mb-6">{{ __('Property Features') }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ($unit->unitAttributes as $attr)
                                    @if ($attr->attributeField)
                                        <div class="flex items-center justify-between p-4 sm:p-5 bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl border border-gray-200 hover:shadow-md transition-all">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 brand-text" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div class="text-base sm:text-lg font-semibold text-gray-900">{{ $attr->attributeField->translated_label }}</div>
                                                </div>
                                            </div>
                                            <div class="text-lg sm:text-xl font-bold text-gray-900">{{ $attr->formatted_value ?? 'N/A' }}</div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Location Map Placeholder -->
                    <div class="bg-white rounded-2xl sm:rounded-3xl shadow-lg p-6 sm:p-8 border border-gray-100">
                        <h3 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4 sm:mb-6">{{ __('Location & Neighborhood') }}</h3>
                        <div class="aspect-video bg-gradient-to-br from-blue-100 to-indigo-100 rounded-2xl flex items-center justify-center">
                            <div class="text-center text-gray-600 px-4">
                                <svg class="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-4 brand-text" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-lg sm:text-xl font-medium">{{ __('Interactive Map Coming Soon') }}</p>
                                <p class="text-sm sm:text-base text-gray-500 mt-2">{{ __('Explore the neighborhood and nearby amenities') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Sidebar -->
                <div class="space-y-6">
                    <!-- Agent Card -->
                    <x-agent-card :unit="$unit" :tenant="$tenantCtx" />

                    <!-- Inquiry Form -->
                    <div class="bg-white rounded-2xl sm:rounded-3xl shadow-lg p-6 sm:p-8 border border-gray-100 lg:sticky lg:top-8">
                        <div class="text-center mb-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ __('Interested?') }}</h2>
                            <p class="text-gray-600">{{ __('Get in touch and we\'ll respond within 24 hours') }}</p>
                        </div>
                        
                        <form id="unit-inquiry-form" method="post" action="{{ route('tenant.inquire') }}" class="space-y-4">
                            @csrf
                            <input type="hidden" name="unit_id" value="{{ $unit->id }}" />
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Full Name') }}</label>
                                <input name="name" type="text" required
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:brand-border focus:ring-2 focus:brand-border transition-colors"
                                    placeholder="{{ __('Enter your full name') }}" />
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Email Address') }}</label>
                                <input name="email" type="email" required
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:brand-border focus:ring-2 focus:brand-border transition-colors"
                                    placeholder="{{ __('Enter your email') }}" />
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Phone Number') }}</label>
                                <input name="phone" type="tel"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:brand-border focus:ring-2 focus:brand-border transition-colors"
                                    placeholder="{{ __('Optional') }}" />
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Message') }}</label>
                                <textarea name="message" rows="4"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:brand-border focus:ring-2 focus:brand-border transition-colors resize-none"
                                    placeholder="{{ __('Tell us about your requirements...') }}"></textarea>
                            </div>
                            
                            <button type="submit"
                                class="w-full bg-gradient-to-r brand-bg text-white font-bold py-4 px-6 rounded-xl hover:opacity-90 focus:outline-none focus:ring-2 focus:brand-border focus:ring-offset-2 transition-all transform hover:scale-[1.02] shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                {{ __('Send Inquiry') }}
                            </button>
                        </form>
                        
                        <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                            <p class="text-sm text-gray-500 mb-3">{{ __('Or contact us directly') }}</p>
                            <div class="flex flex-col gap-2">
                                @if($tenantCtx?->settings['contact_phone'] ?? null)
                                <a href="tel:{{ $tenantCtx->settings['contact_phone'] }}" 
                                    class="inline-flex items-center justify-center gap-2 brand-text hover:opacity-80 font-medium">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                    </svg>
                                    {{ $tenantCtx->settings['contact_phone'] }}
                                </a>
                                @endif
                                
                                @if($tenantCtx?->settings['contact_email'] ?? null)
                                <a href="mailto:{{ $tenantCtx->settings['contact_email'] }}" 
                                    class="inline-flex items-center justify-center gap-2 brand-text hover:opacity-80 font-medium">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                    </svg>
                                    {{ $tenantCtx->settings['contact_email'] }}
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-100">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">{{ __('Quick Actions') }}</h3>
                        <div class="space-y-3">
                            <button class="w-full flex items-center gap-3 p-3 bg-white rounded-xl hover:shadow-md transition-all text-left">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 brand-text" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">{{ __('Share Property') }}</div>
                                    <div class="text-sm text-gray-600">{{ __('Send to friends & family') }}</div>
                                </div>
                            </button>
                            
                            <button class="w-full flex items-center gap-3 p-3 bg-white rounded-xl hover:shadow-md transition-all text-left">
                                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">{{ __('Save to Favorites') }}</div>
                                    <div class="text-sm text-gray-600">{{ __('Keep track of this listing') }}</div>
                                </div>
                            </button>
                            
                            <button class="w-full flex items-center gap-3 p-3 bg-white rounded-xl hover:shadow-md transition-all text-left">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">{{ __('Schedule Viewing') }}</div>
                                    <div class="text-sm text-gray-600">{{ __('Book a property tour') }}</div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection