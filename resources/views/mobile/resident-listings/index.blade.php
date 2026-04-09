@extends('mobile.layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'إعلاناتي' : 'My Listings')

@section('content')
<div class="min-h-screen bg-[#f8f9fa] pb-20">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="px-4 py-4">
            <div class="flex items-center justify-between">
                <a href="{{ route('mobile.dashboard') }}" class="text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h1 class="text-xl font-bold text-gray-900">{{ app()->getLocale() === 'ar' ? 'إعلاناتي' : 'My Listings' }}</h1>
                <a href="{{ route('mobile.my-listings.create') }}" class="text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="p-4" id="stats-container">
        <div class="grid grid-cols-3 gap-3">
            <div class="bg-white rounded-lg p-4 text-center shadow-sm">
                <div class="text-2xl font-bold text-gray-900" id="stat-total">0</div>
                <div class="text-xs text-gray-600 mt-1">{{ app()->getLocale() === 'ar' ? 'الكل' : 'Total' }}</div>
            </div>
            <div class="bg-white rounded-lg p-4 text-center shadow-sm">
                <div class="text-2xl font-bold text-green-600" id="stat-active">0</div>
                <div class="text-xs text-gray-600 mt-1">{{ app()->getLocale() === 'ar' ? 'نشط' : 'Active' }}</div>
            </div>
            <div class="bg-white rounded-lg p-4 text-center shadow-sm">
                <div class="text-2xl font-bold text-orange-600" id="stat-expiring">0</div>
                <div class="text-xs text-gray-600 mt-1">{{ app()->getLocale() === 'ar' ? 'قارب الانتهاء' : 'Expiring' }}</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="px-4 pb-4">
        <div class="flex gap-2 overflow-x-auto pb-2">
            <button class="filter-btn active px-4 py-2 rounded-full bg-blue-600 text-white text-sm whitespace-nowrap" data-status="all">{{ app()->getLocale() === 'ar' ? 'الكل' : 'All' }}</button>
            <button class="filter-btn px-4 py-2 rounded-full bg-white text-gray-700 text-sm whitespace-nowrap border" data-status="active">{{ app()->getLocale() === 'ar' ? 'نشط' : 'Active' }}</button>
            <button class="filter-btn px-4 py-2 rounded-full bg-white text-gray-700 text-sm whitespace-nowrap border" data-status="expired">{{ app()->getLocale() === 'ar' ? 'منتهي' : 'Expired' }}</button>
            <button class="filter-btn px-4 py-2 rounded-full bg-white text-gray-700 text-sm whitespace-nowrap border" data-status="pending">{{ app()->getLocale() === 'ar' ? 'معلّق' : 'Pending' }}</button>
        </div>
    </div>

    <!-- Listings Container -->
    <div id="listings-container" class="px-4 space-y-3">
        <!-- Listings will be loaded here -->
    </div>

    <!-- Loading State -->
    <div id="loading-state" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
    </div>

    <!-- Empty State -->
    <div id="empty-state" class="hidden text-center py-12 px-4">
        <div class="text-gray-400 mb-4">
            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ app()->getLocale() === 'ar' ? 'لا توجد إعلانات بعد' : 'No listings yet' }}</h3>
        <p class="text-gray-600 mb-6">{{ app()->getLocale() === 'ar' ? 'ابدأ بنشر عقاراتك للوصول لآلاف المشترين!' : 'Start posting your properties to reach thousands of buyers!' }}</p>
        <a href="{{ route('mobile.my-listings.create') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold">
            {{ app()->getLocale() === 'ar' ? 'أنشر عقارك الأول' : 'Post Your First Property' }}
        </a>
    </div>
</div>

<script>
    const apiBase = window.__AQARI_API_BASE || '';
    const lang = document.documentElement.lang.startsWith('ar') ? 'ar' : 'en';
    let currentFilter = 'all';

    async function loadListings() {
        const token = localStorage.getItem('aqari_mobile_token');

        try {
            document.getElementById('loading-state').classList.remove('hidden');
            document.getElementById('empty-state').classList.add('hidden');
            document.getElementById('listings-container').innerHTML = '';

            const statusParam = currentFilter !== 'all' ? `?status=${currentFilter}` : '';
            let response;

            if (token) {
                response = await fetch(`${apiBase}/api/mobile/my-listings${statusParam}`, {
                    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
                });
                if (response.status === 401) {
                    localStorage.removeItem('aqari_mobile_token');
                    response = null;
                }
            }

            // Session auth fallback: only in web browser mode (no remote API base)
            if (!response && !apiBase) {
                response = await fetch(`/api/mobile/web/my-listings${statusParam}`, {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                });
            }

            if (response.status === 401) {
                document.getElementById('loading-state').classList.add('hidden');
                document.getElementById('listings-container').innerHTML = `
                    <div class="text-center py-12 px-4">
                        <p class="text-gray-600 mb-4">${lang === 'ar' ? 'سجّل الدخول لإدارة إعلاناتك' : 'Sign in to manage your listings.'}</p>
                        <a href="{{ route('mobile.login') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold">${lang === 'ar' ? 'تسجيل الدخول' : 'Sign In'}</a>
                    </div>`;
                return;
            }

            if (!response.ok) throw new Error('Failed to load listings');

            const result = await response.json();
            
            // Update stats
            if (result.stats) {
                document.getElementById('stat-total').textContent = result.stats.total || 0;
                document.getElementById('stat-active').textContent = result.stats.active || 0;
                document.getElementById('stat-expiring').textContent = result.stats.expiring_soon || 0;
            }

            document.getElementById('loading-state').classList.add('hidden');

            if (result.data && result.data.length > 0) {
                renderListings(result.data);
            } else {
                document.getElementById('empty-state').classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error loading listings:', error);
            document.getElementById('loading-state').classList.add('hidden');
            document.getElementById('listings-container').innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                    <p class="text-red-800">${lang === 'ar' ? 'فشل تحميل الإعلانات. حاول مرة أخرى.' : 'Failed to load listings. Please try again.'}</p>
                </div>
            `;
        }
    }

    function renderListings(listings) {
        const container = document.getElementById('listings-container');
        
        listings.forEach(listing => {
            const card = document.createElement('div');
            card.className = 'bg-white rounded-lg shadow-sm overflow-hidden';
            
            const statusBadge = getStatusBadge(listing);
            const expirationWarning = getExpirationWarning(listing);
            const photo = listing.first_photo || listing.photos?.[0] || '';
            const title = (lang === 'ar' ? listing.title?.ar : listing.title?.en) || listing.title?.en || listing.title?.ar || (lang === 'ar' ? 'بدون عنوان' : 'Untitled');
            const price = new Intl.NumberFormat('en-IQ').format(listing.price);
            
            card.innerHTML = `
                <div class="flex gap-3 p-3">
                    <div class="w-24 h-24 flex-shrink-0">
                        ${photo ? `<img src="${photo}" alt="${title}" class="w-full h-full object-cover rounded-lg">` : `
                            <div class="w-full h-full bg-gray-200 rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        `}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between mb-1">
                            <h3 class="font-semibold text-gray-900 truncate">${title}</h3>
                            ${statusBadge}
                        </div>
                        <p class="text-sm text-gray-600 mb-2">${listing.code}</p>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-lg font-bold text-blue-600">${price} ${listing.currency}</span>
                            <span class="text-xs text-gray-500">${listing.listing_type === 'sale' ? (lang === 'ar' ? 'للبيع' : 'For Sale') : (lang === 'ar' ? 'للإيجار' : 'For Rent')}</span>
                        </div>
                        ${expirationWarning}
                        <div class="flex gap-2 mt-3">
                            <a href="/mobile/my-listings/${listing.code}/edit" class="text-sm text-blue-600 font-medium">${lang === 'ar' ? 'تعديل' : 'Edit'}</a>
                            <span class="text-gray-300">|</span>
                            <a href="/mobile/resident-listings/${listing.code}" class="text-sm text-gray-600 font-medium">${lang === 'ar' ? 'عرض' : 'View'}</a>
                            ${listing.is_expired ? `
                                <span class="text-gray-300">|</span>
                                <button onclick="renewListing('${listing.code}')" class="text-sm text-green-600 font-medium">${lang === 'ar' ? 'تجديد' : 'Renew'}</button>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
            
            container.appendChild(card);
        });
    }

    function getStatusBadge(listing) {
        if (listing.ad_status === 'expired' || listing.is_expired) {
            return `<span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-medium rounded">${lang === 'ar' ? 'منتهي' : 'Expired'}</span>`;
        }
        if (listing.ad_status === 'active') {
            return `<span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded">${lang === 'ar' ? 'نشط' : 'Active'}</span>`;
        }
        if (listing.ad_status === 'pending') {
            return `<span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-medium rounded">${lang === 'ar' ? 'معلّق' : 'Pending'}</span>`;
        }
        return '';
    }

    function getExpirationWarning(listing) {
        if (listing.is_expiring_soon && !listing.is_expired) {
            return `<div class="text-xs text-orange-600 font-medium">⚠️ ${lang === 'ar' ? `ينتهي خلال ${listing.days_until_expiration} يوم` : `Expires in ${listing.days_until_expiration} days`}</div>`;
        }
        if (listing.ad_expires_at && !listing.is_expired) {
            const days = listing.days_until_expiration;
            return `<div class="text-xs text-gray-500">${lang === 'ar' ? `${days} يوم متبقي` : `${days} days remaining`}</div>`;
        }
        return '';
    }

    async function renewListing(code) {
        if (!confirm(lang === 'ar' ? 'هل تريد تجديد هذا الإعلان؟' : 'Would you like to renew this listing?')) return;
        
        // Redirect to create page with renewal flow
        window.location.href = '{{ route("mobile.my-listings.create") }}?renew=' + code;
    }

    // Filter functionality
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.filter-btn').forEach(b => {
                b.classList.remove('active', 'bg-blue-600', 'text-white');
                b.classList.add('bg-white', 'text-gray-700', 'border');
            });
            
            btn.classList.remove('bg-white', 'text-gray-700', 'border');
            btn.classList.add('active', 'bg-blue-600', 'text-white');
            
            currentFilter = btn.dataset.status;
            loadListings();
        });
    });

    // Load listings on page load
    loadListings();
</script>
@endsection
