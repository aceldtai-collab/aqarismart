<article class="property-card group bg-white rounded-xl shadow-sm hover:shadow-lg transition-shadow duration-300 overflow-hidden" aria-labelledby="unit-{{ $unit->id }}-title">
    @php
        $isSale = ($unit->listing_type ?? \App\Models\Unit::LISTING_RENT) === \App\Models\Unit::LISTING_SALE;
        $displayPrice = $isSale ? ($unit->price ?? null) : (($unit->market_rent && $unit->market_rent > 0) ? $unit->market_rent / 100 : ($unit->price ?? null));
        $unitTitle = $unit->translated_title ?: ($unit->property?->name ?? __('Unit'));
        $categoryName = $unit->subcategory?->name ?? null;
        $rawDescription = $unit->description ?? $unit->property?->description ?? null;
        $description = is_string($rawDescription) ? $rawDescription : (is_array($rawDescription) ? ($rawDescription['en'] ?? $rawDescription['ar'] ?? null) : null);
        
        $tenantCtx = app(\App\Services\Tenancy\TenantManager::class)->tenant();
        $scheme = request()->getScheme();
        $port = request()->getPort();
        $base = config('tenancy.base_domain');
        $demo = config('tenancy.demo_slug', 'acme');
        $demoBase = $scheme.'://'.$demo.'.'.$base.($port && $port!=80 && $port!=443 ? ':' . $port : '');
        $tenantSlug = $tenantCtx?->slug ?? optional($unit->tenant)->slug;
        $unitLink = $tenantSlug ? route('tenant.unit', ['tenant_slug' => $tenantSlug, 'unit' => $unit]) : $demoBase;
    @endphp
    
    <!-- Image Section -->
    <a href="{{ $unitLink }}" class="block relative">
        <div x-data="{ idx: 0, imgs: @js($unit->photos ?? []) }" class="relative h-56 bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden">
            @if(count($unit->photos ?? []) > 0)
                <img x-bind:src="imgs[idx]" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" alt="{{ $unitTitle }}" 
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />
                <div class="h-full w-full bg-gradient-to-br from-blue-50 to-indigo-50 flex items-center justify-center" style="display: none;">
                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            @else
                <div class="h-full w-full bg-gradient-to-br from-blue-50 to-indigo-50 flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            @endif
            
            <!-- Status Badge -->
            <div class="absolute top-4 left-4">
                <span class="inline-flex items-center gap-1 rounded-full {{ $isSale ? 'bg-emerald-500' : 'bg-brand-600' }} text-white px-3 py-1.5 text-xs font-bold shadow-lg">
                    {{ $isSale ? __('For Sale') : __('For Rent') }}
                </span>
            </div>
            
            <!-- Quick Actions -->
            <div class="absolute top-4 right-4 flex gap-2">
                @if(count($unit->photos ?? []) > 1)
                <span class="inline-flex items-center gap-1 rounded-full bg-black/50 backdrop-blur-sm text-white px-2.5 py-1.5 text-xs font-medium">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                    </svg>
                    <span x-text="imgs.length"></span>
                </span>
                @endif
                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-white/90 hover:bg-white text-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </button>
            </div>
            
            <!-- Quick Nav Dots -->
            @if(count($unit->photos ?? []) > 1)
            <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-1.5">
                <template x-for="(img,i) in imgs.slice(0, 5)" :key="i">
                    <button @click.prevent="idx=i" type="button" 
                        class="h-1.5 rounded-full transition-all" 
                        :class="idx===i ? 'bg-white w-5' : 'bg-white/50 w-1.5'">
                    </button>
                </template>
            </div>
            @endif
        </div>
    </a>
    
    <!-- Content Section -->
    <div class="p-5">
        <!-- Title -->
        <a href="{{ $unitLink }}" class="block mb-2">
            <h3 id="unit-{{ $unit->id }}-title" class="text-lg font-bold text-gray-900 line-clamp-1 group-hover:brand-text transition-colors" title="{{ $unitTitle }}">
                {{ $unitTitle }}
            </h3>
        </a>
        
        <!-- Description -->
        @if($description && is_string($description))
        <p class="text-sm text-gray-600 line-clamp-2 mb-3">
            {{ Str::limit(strip_tags($description), 100) }}
        </p>
        @endif
        
        <!-- Features Row -->
        <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-100">
            <div class="flex items-center gap-4 text-sm">
                <div class="flex items-center gap-1.5 text-gray-700">
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                    </svg>
                    <span class="font-semibold">{{ $unit->beds }}</span>
                    <span class="text-gray-500 text-xs">{{ __('Bedrooms') }}</span>
                </div>
                <div class="flex items-center gap-1.5 text-gray-700">
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm3 1h6v2H7V5zm0 4h6v2H7V9zm0 4h6v2H7v-2z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-semibold">{{ $unit->baths }}</span>
                    <span class="text-gray-500 text-xs">{{ __('Bathrooms') }}</span>
                </div>
                @if($unit->sqft)
                <div class="flex items-center gap-1.5 text-gray-700">
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-semibold">{{ number_format($unit->sqft) }}</span>
                    <span class="text-gray-500 text-xs">{{ __('Sq Ft') }}</span>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Price & CTA -->
        <div class="flex items-center justify-between">
            @if($displayPrice !== null)
            <div>
                <div class="text-xs text-gray-500 mb-1">{{ $isSale ? __('For Sale') : __('For Rent') }}</div>
                <div class="text-2xl font-bold brand-text">
                    {{ $unit->currency ?? 'JOD' }} {{ number_format($displayPrice, 0) }}
                </div>
            </div>
            @endif
            
            <a href="{{ $unitLink }}" 
                class="inline-flex items-center justify-center px-5 py-2.5 brand-bg hover:opacity-90 text-white font-semibold rounded-lg transition-all hover:shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
        </div>
    </div>
</article>
