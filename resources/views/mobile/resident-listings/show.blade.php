@extends('mobile.layouts.app')

@section('title', $residentListing->translated_title ?? (app()->getLocale() === 'ar' ? 'تفاصيل العقار' : 'Property Details'))

@section('content')
<div class="min-h-screen bg-[#f8f9fa] pb-20">
    <!-- Photo Gallery -->
    @php
        $photos = $residentListing->photos ?? [];
        $firstPhoto = is_array($photos) && count($photos) > 0 ? $photos[0] : null;
    @endphp
    
    <div class="relative h-64 bg-gray-200">
        @if($firstPhoto)
            <img src="{{ $firstPhoto }}" alt="{{ $residentListing->translated_title }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full flex items-center justify-center">
                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        @endif
        
        <a href="{{ route('mobile.marketplace') }}" class="absolute top-4 left-4 w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>

        @if(count($photos) > 1)
            <div class="absolute bottom-4 right-4 bg-black bg-opacity-60 text-white px-3 py-1 rounded-full text-sm">
                {{ count($photos) }} {{ app()->getLocale() === 'ar' ? 'صورة' : 'photos' }}
            </div>
        @endif
    </div>

    <!-- Direct from Owner Badge -->
    <div class="bg-gradient-to-r from-green-600 to-green-500 text-white px-4 py-3">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
            </svg>
            <span class="font-semibold">{{ app()->getLocale() === 'ar' ? 'مباشرة من المالك - بدون عمولة' : 'Direct from Owner - No Agent Fees' }}</span>
        </div>
    </div>

    <!-- Main Content -->
    <div class="p-4 space-y-4">
        <!-- Title and Price -->
        <div class="bg-white rounded-lg p-4 shadow-sm">
            <div class="flex items-start justify-between mb-3">
                <h1 class="text-2xl font-bold text-gray-900 flex-1">{{ $residentListing->translated_title }}</h1>
                @if($residentListing->is_expiring_soon)
                    <span class="px-3 py-1 bg-orange-100 text-orange-700 text-xs font-medium rounded-full">{{ app()->getLocale() === 'ar' ? 'قارب الانتهاء' : 'Expiring Soon' }}</span>
                @elseif($residentListing->ad_status === 'active')
                    <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">{{ app()->getLocale() === 'ar' ? 'نشط' : 'Active' }}</span>
                @endif
            </div>
            
            <div class="flex items-baseline gap-2 mb-2">
                <span class="text-3xl font-bold text-blue-600">{{ number_format($residentListing->price, 0) }}</span>
                <span class="text-lg text-gray-600">{{ $residentListing->currency }}</span>
                <span class="ml-2 px-2 py-1 bg-gray-100 text-gray-700 text-sm rounded">{{ $residentListing->listing_type === 'sale' ? (app()->getLocale() === 'ar' ? 'للبيع' : 'For Sale') : (app()->getLocale() === 'ar' ? 'للإيجار' : 'For Rent') }}</span>
            </div>

            <div class="flex items-center gap-4 text-sm text-gray-600">
                @if($residentListing->bedrooms > 0)
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        {{ $residentListing->bedrooms }} {{ app()->getLocale() === 'ar' ? 'غرف' : 'beds' }}
                    </span>
                @endif
                @if($residentListing->bathrooms > 0)
                    <span>{{ $residentListing->bathrooms }} {{ app()->getLocale() === 'ar' ? 'حمام' : 'baths' }}</span>
                @endif
                @if($residentListing->area_m2)
                    <span>{{ number_format($residentListing->area_m2, 0) }} m²</span>
                @endif
            </div>
        </div>

        <!-- Location -->
        @if($residentListing->location || $residentListing->city || $residentListing->area)
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <h2 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ app()->getLocale() === 'ar' ? 'الموقع' : 'Location' }}
                </h2>
                <p class="text-gray-700">
                    @if($residentListing->location)
                        {{ $residentListing->location }}<br>
                    @endif
                    @if($residentListing->city)
                        {{ app()->getLocale() === 'ar' ? ($residentListing->city->name_ar ?? $residentListing->city->name_en) : $residentListing->city->name_en }}
                        @if($residentListing->area), {{ app()->getLocale() === 'ar' ? ($residentListing->area->name_ar ?? $residentListing->area->name_en) : $residentListing->area->name_en }}@endif
                    @endif
                </p>
            </div>
        @endif

        <!-- Description -->
        <div class="bg-white rounded-lg p-4 shadow-sm">
            <h2 class="font-semibold text-gray-900 mb-3">{{ app()->getLocale() === 'ar' ? 'الوصف' : 'Description' }}</h2>
            <p class="text-gray-700 whitespace-pre-line">{{ $residentListing->translated_description }}</p>
        </div>

        <!-- Property Details -->
        <div class="bg-white rounded-lg p-4 shadow-sm">
            <h2 class="font-semibold text-gray-900 mb-3">{{ app()->getLocale() === 'ar' ? 'تفاصيل العقار' : 'Property Details' }}</h2>
            <div class="grid grid-cols-2 gap-4">
                @if($residentListing->subcategory)
                    <div>
                        <div class="text-sm text-gray-600">{{ app()->getLocale() === 'ar' ? 'النوع' : 'Type' }}</div>
                        <div class="font-medium text-gray-900">{{ $residentListing->subcategory->name }}</div>
                    </div>
                @endif
                <div>
                    <div class="text-sm text-gray-600">{{ app()->getLocale() === 'ar' ? 'رقم الإعلان' : 'Listing ID' }}</div>
                    <div class="font-medium text-gray-900">{{ $residentListing->code }}</div>
                </div>
                @if($residentListing->ad_expires_at)
                    <div>
                        <div class="text-sm text-gray-600">{{ app()->getLocale() === 'ar' ? 'ينتهي في' : 'Ad Expires' }}</div>
                        <div class="font-medium text-gray-900">{{ $residentListing->ad_expires_at->format('M d, Y') }}</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Owner Contact -->
        @if($residentListing->user)
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <h2 class="font-semibold text-gray-900 mb-3">{{ app()->getLocale() === 'ar' ? 'تواصل مع المالك' : 'Contact Owner' }}</h2>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-lg">
                        {{ strtoupper(substr($residentListing->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-medium text-gray-900">{{ $residentListing->user->name }}</div>
                        <div class="text-sm text-gray-600">{{ app()->getLocale() === 'ar' ? 'مالك العقار' : 'Property Owner' }}</div>
                    </div>
                </div>
                
                @if($residentListing->user->phone)
                    <a href="tel:{{ $residentListing->user->phone_country_code }}{{ $residentListing->user->phone }}" class="block w-full bg-green-600 text-white text-center py-3 rounded-lg font-semibold mb-3">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        {{ app()->getLocale() === 'ar' ? 'اتصل بالمالك' : 'Call Owner' }}
                    </a>
                @endif
            </div>
        @endif

        <!-- Photo Gallery Grid -->
        @if(count($photos) > 1)
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <h2 class="font-semibold text-gray-900 mb-3">{{ app()->getLocale() === 'ar' ? 'الصور' : 'Photos' }}</h2>
                <div class="grid grid-cols-3 gap-2">
                    @foreach($photos as $photo)
                        <div class="aspect-square">
                            <img src="{{ $photo }}" alt="Property photo" class="w-full h-full object-cover rounded-lg">
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
