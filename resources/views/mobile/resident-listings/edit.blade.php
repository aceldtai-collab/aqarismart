@extends('mobile.layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'تعديل الإعلان' : 'Edit Listing')

@section('content')
<div class="min-h-screen bg-[#f8f9fa] pb-20" x-data="editListingForm()" x-init="loadListing()">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="px-4 py-4">
            <div class="flex items-center justify-between">
                <a href="{{ route('mobile.my-listings.index') }}" class="text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h1 class="text-xl font-bold text-gray-900">{{ app()->getLocale() === 'ar' ? 'تعديل الإعلان' : 'Edit Listing' }}</h1>
                <button @click="deleteListing()" class="text-red-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
    </div>

    <!-- Form -->
    <div x-show="!loading" class="p-4 space-y-4">
        <div class="bg-white rounded-lg p-4 shadow-sm space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ app()->getLocale() === 'ar' ? 'العنوان (إنجليزي) *' : 'Title (English) *' }}</label>
                <input type="text" x-model="form.title.en" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ app()->getLocale() === 'ar' ? 'العنوان (عربي)' : 'Title (Arabic)' }}</label>
                <input type="text" x-model="form.title.ar" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ app()->getLocale() === 'ar' ? 'الوصف (إنجليزي) *' : 'Description (English) *' }}</label>
                <textarea x-model="form.description.en" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ app()->getLocale() === 'ar' ? 'الوصف (عربي)' : 'Description (Arabic)' }}</label>
                <textarea x-model="form.description.ar" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ app()->getLocale() === 'ar' ? 'نوع الإعلان *' : 'Listing Type *' }}</label>
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" @click="form.listing_type = 'sale'" class="py-3 rounded-lg border-2 font-medium" :class="form.listing_type === 'sale' ? 'border-blue-600 bg-blue-50 text-blue-600' : 'border-gray-300 text-gray-700'">{{ app()->getLocale() === 'ar' ? 'للبيع' : 'For Sale' }}</button>
                    <button type="button" @click="form.listing_type = 'rent'" class="py-3 rounded-lg border-2 font-medium" :class="form.listing_type === 'rent' ? 'border-blue-600 bg-blue-50 text-blue-600' : 'border-gray-300 text-gray-700'">{{ app()->getLocale() === 'ar' ? 'للإيجار' : 'For Rent' }}</button>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ app()->getLocale() === 'ar' ? 'غرف النوم *' : 'Bedrooms *' }}</label>
                    <input type="number" x-model="form.bedrooms" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ app()->getLocale() === 'ar' ? 'الحمامات *' : 'Bathrooms *' }}</label>
                    <input type="number" x-model="form.bathrooms" min="0" step="0.5" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ app()->getLocale() === 'ar' ? 'المساحة (م²)' : 'Area (m²)' }}</label>
                    <input type="number" x-model="form.area_m2" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ app()->getLocale() === 'ar' ? 'السعر (IQD) *' : 'Price (IQD) *' }}</label>
                <input type="number" x-model="form.price" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ app()->getLocale() === 'ar' ? 'الموقع' : 'Location' }}</label>
                <input type="text" x-model="form.location" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
        </div>

        <button @click="saveChanges()" :disabled="saving" class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold disabled:opacity-50">
            <span x-show="!saving">{{ app()->getLocale() === 'ar' ? 'حفظ التعديلات' : 'Save Changes' }}</span>
            <span x-show="saving">{{ app()->getLocale() === 'ar' ? 'جاري الحفظ...' : 'Saving...' }}</span>
        </button>
    </div>
</div>

<script>
    function editListingForm() {
        return {
            loading: true,
            saving: false,
            listingCode: '{{ $residentListing->code }}',
            form: {
                title: { en: '', ar: '' },
                description: { en: '', ar: '' },
                bedrooms: 0,
                bathrooms: 1,
                area_m2: '',
                price: '',
                location: '',
                listing_type: 'sale'
            },

            async loadListing() {
                const token = localStorage.getItem('aqari_mobile_token');
                if (!token) { window.location.href = '{{ route("mobile.login") }}'; return; }

                try {
                    const response = await fetch(`${window.__AQARI_API_BASE || ''}/api/mobile/resident-listings/${this.listingCode}`, {
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json',
                        }
                    });

                    if (response.status === 401) { localStorage.removeItem('aqari_mobile_token'); window.location.href = '{{ route("mobile.login") }}'; return; }
                    if (!response.ok) throw new Error('Failed to load listing');

                    const result = await response.json();
                    const listing = result.data;

                    this.form = {
                        title: listing.title || { en: '', ar: '' },
                        description: listing.description || { en: '', ar: '' },
                        bedrooms: listing.bedrooms || 0,
                        bathrooms: listing.bathrooms || 1,
                        area_m2: listing.area_m2 || '',
                        price: listing.price || '',
                        location: listing.location || '',
                        listing_type: listing.listing_type || 'sale',
                        subcategory_id: listing.subcategory?.id || '',
                        city_id: listing.city?.id || '',
                        area_id: listing.area?.id || ''
                    };

                    this.loading = false;
                } catch (error) {
                    console.error('Error loading listing:', error);
                    alert(document.documentElement.lang.startsWith('ar') ? 'فشل تحميل الإعلان' : 'Failed to load listing');
                    window.location.href = '{{ route("mobile.my-listings.index") }}';
                }
            },

            async saveChanges() {
                this.saving = true;

                try {
                    const response = await fetch(`${window.__AQARI_API_BASE || ''}/api/mobile/resident-listings/{{ $residentListing->id }}`, {
                        method: 'PUT',
                        headers: {
                            'Authorization': `Bearer ${localStorage.getItem('aqari_mobile_token')}`,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(this.form)
                    });

                    if (!response.ok) throw new Error('Failed to update listing');

                    alert(document.documentElement.lang.startsWith('ar') ? 'تم تحديث الإعلان بنجاح!' : 'Listing updated successfully!');
                    window.location.href = '{{ route("mobile.my-listings.index") }}';
                } catch (error) {
                    console.error('Error:', error);
                    alert(document.documentElement.lang.startsWith('ar') ? 'فشل تحديث الإعلان. حاول مرة أخرى.' : 'Failed to update listing. Please try again.');
                } finally {
                    this.saving = false;
                }
            },

            async deleteListing() {
                if (!confirm(document.documentElement.lang.startsWith('ar') ? 'هل أنت متأكد من حذف هذا الإعلان؟ لا يمكن التراجع.' : 'Are you sure you want to delete this listing? This action cannot be undone.')) {
                    return;
                }

                try {
                    const response = await fetch(`${window.__AQARI_API_BASE || ''}/api/mobile/resident-listings/{{ $residentListing->id }}`, {
                        method: 'DELETE',
                        headers: {
                            'Authorization': `Bearer ${localStorage.getItem('aqari_mobile_token')}`,
                            'Accept': 'application/json',
                        }
                    });

                    if (!response.ok) throw new Error('Failed to delete listing');

                    alert(document.documentElement.lang.startsWith('ar') ? 'تم حذف الإعلان بنجاح' : 'Listing deleted successfully');
                    window.location.href = '{{ route("mobile.my-listings.index") }}';
                } catch (error) {
                    console.error('Error:', error);
                    alert(document.documentElement.lang.startsWith('ar') ? 'فشل حذف الإعلان. حاول مرة أخرى.' : 'Failed to delete listing. Please try again.');
                }
            }
        };
    }
</script>
@endsection
