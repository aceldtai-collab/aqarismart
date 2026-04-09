@extends('mobile.layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'أنشر عقارك' : 'Post Your Property')

@section('content')
<div class="min-h-screen bg-[#f8f9fa] pb-20" x-data="listingForm()">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="px-4 py-4">
            <div class="flex items-center justify-between">
                <a href="{{ route('mobile.my-listings.index') }}" class="text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h1 class="text-xl font-bold text-gray-900">{{ app()->getLocale() === 'ar' ? 'أنشر عقارك' : 'Post Your Property' }}</h1>
                <div class="w-6"></div>
            </div>
        </div>
    </div>

    <!-- Progress Steps -->
    <div class="bg-white px-4 py-3 border-b">
        <div class="flex items-center justify-between">
            <div class="flex-1 text-center" :class="step >= 1 ? 'text-blue-600' : 'text-gray-400'">
                <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center border-2" :class="step >= 1 ? 'border-blue-600 bg-blue-600 text-white' : 'border-gray-300'">1</div>
                <div class="text-xs mt-1">{{ app()->getLocale() === 'ar' ? 'التفاصيل' : 'Details' }}</div>
            </div>
            <div class="flex-1 border-t-2" :class="step >= 2 ? 'border-blue-600' : 'border-gray-300'"></div>
            <div class="flex-1 text-center" :class="step >= 2 ? 'text-blue-600' : 'text-gray-400'">
                <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center border-2" :class="step >= 2 ? 'border-blue-600 bg-blue-600 text-white' : 'border-gray-300'">2</div>
                <div class="text-xs mt-1">{{ app()->getLocale() === 'ar' ? 'الصور' : 'Photos' }}</div>
            </div>
            <div class="flex-1 border-t-2" :class="step >= 3 ? 'border-blue-600' : 'border-gray-300'"></div>
            <div class="flex-1 text-center" :class="step >= 3 ? 'text-blue-600' : 'text-gray-400'">
                <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center border-2" :class="step >= 3 ? 'border-blue-600 bg-blue-600 text-white' : 'border-gray-300'">3</div>
                <div class="text-xs mt-1">{{ app()->getLocale() === 'ar' ? 'المدة' : 'Duration' }}</div>
            </div>
        </div>
    </div>

    <!-- Form Container -->
    <div class="p-4">
        <!-- Step 1: Property Details -->
        <div x-show="step === 1" class="space-y-4">
            <div class="bg-white rounded-lg p-4 shadow-sm space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ app()->getLocale() === 'ar' ? 'نوع العقار *' : 'Property Type *' }}</label>
                    <select x-model="form.subcategory_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="">{{ app()->getLocale() === 'ar' ? 'اختر نوع العقار' : 'Select property type' }}</option>
                        <template x-for="sub in subcategories" :key="sub.id">
                            <option :value="sub.id" x-text="sub.name"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ app()->getLocale() === 'ar' ? 'نوع الإعلان *' : 'Listing Type *' }}</label>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" @click="form.listing_type = 'sale'" class="py-3 rounded-lg border-2 font-medium" :class="form.listing_type === 'sale' ? 'border-blue-600 bg-blue-50 text-blue-600' : 'border-gray-300 text-gray-700'">{{ app()->getLocale() === 'ar' ? 'للبيع' : 'For Sale' }}</button>
                        <button type="button" @click="form.listing_type = 'rent'" class="py-3 rounded-lg border-2 font-medium" :class="form.listing_type === 'rent' ? 'border-blue-600 bg-blue-50 text-blue-600' : 'border-gray-300 text-gray-700'">{{ app()->getLocale() === 'ar' ? 'للإيجار' : 'For Rent' }}</button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ app()->getLocale() === 'ar' ? 'العنوان (إنجليزي) *' : 'Title (English) *' }}</label>
                    <input type="text" x-model="form.title.en" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="{{ app()->getLocale() === 'ar' ? 'مثال: شقة 3 غرف في وسط المدينة' : 'e.g., Modern 3BR Apartment in City Center' }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ app()->getLocale() === 'ar' ? 'العنوان (عربي)' : 'Title (Arabic)' }}</label>
                    <input type="text" x-model="form.title.ar" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="عنوان العقار بالعربية">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ app()->getLocale() === 'ar' ? 'الوصف (إنجليزي) *' : 'Description (English) *' }}</label>
                    <textarea x-model="form.description.en" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="{{ app()->getLocale() === 'ar' ? 'وصف العقار...' : 'Describe your property...' }}"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ app()->getLocale() === 'ar' ? 'الوصف (عربي)' : 'Description (Arabic)' }}</label>
                    <textarea x-model="form.description.ar" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="وصف العقار بالعربية..."></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ app()->getLocale() === 'ar' ? 'المدينة *' : 'City *' }}</label>
                        <select x-model="form.city_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="">{{ app()->getLocale() === 'ar' ? 'اختر المدينة' : 'Select city' }}</option>
                            <template x-for="city in cities" :key="city.id">
                                <option :value="city.id" x-text="{{ app()->getLocale() === 'ar' ? 'city.name_ar || city.name_en' : 'city.name_en' }}"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ app()->getLocale() === 'ar' ? 'المنطقة' : 'Area' }}</label>
                        <select x-model="form.area_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="">{{ app()->getLocale() === 'ar' ? 'اختر المنطقة' : 'Select area' }}</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ app()->getLocale() === 'ar' ? 'الموقع' : 'Location' }}</label>
                    <input type="text" x-model="form.location" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="{{ app()->getLocale() === 'ar' ? 'مثال: قرب الحديقة المركزية' : 'e.g., Near Central Park' }}">
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
                    <input type="number" x-model="form.price" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="0">
                </div>
            </div>

            <button @click="nextStep()" class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold">
                {{ app()->getLocale() === 'ar' ? 'التالي: الصور' : 'Continue to Photos' }}
            </button>
        </div>

        <!-- Step 2: Photos -->
        <div x-show="step === 2" class="space-y-4">
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <h3 class="font-semibold text-gray-900 mb-4">{{ app()->getLocale() === 'ar' ? 'رفع الصور' : 'Upload Photos' }}</h3>
                
                <div class="grid grid-cols-3 gap-3 mb-4" id="photos-grid">
                    <!-- Photos will be added here -->
                </div>

                <label class="block w-full border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-blue-600">
                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span class="text-sm text-gray-600">{{ app()->getLocale() === 'ar' ? 'أضف صور' : 'Add Photos' }}</span>
                    <input type="file" accept="image/*" multiple class="hidden" @change="handlePhotoUpload">
                </label>

                <p class="text-xs text-gray-500 mt-2">{{ app()->getLocale() === 'ar' ? 'ارفع حتى 10 صور. الصورة الأولى ستكون الغلاف.' : 'Upload up to 10 photos. First photo will be the cover.' }}</p>
            </div>

            <div class="flex gap-3">
                <button @click="step = 1" class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-lg font-semibold">{{ app()->getLocale() === 'ar' ? 'رجوع' : 'Back' }}</button>
                <button @click="nextStep()" class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-semibold">{{ app()->getLocale() === 'ar' ? 'التالي' : 'Continue' }}</button>
            </div>
        </div>

        <!-- Step 3: Ad Duration -->
        <div x-show="step === 3" class="space-y-4">
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <h3 class="font-semibold text-gray-900 mb-4">{{ app()->getLocale() === 'ar' ? 'اختر مدة الإعلان' : 'Select Ad Duration' }}</h3>

                <div class="space-y-3">
                    @forelse($adDurations as $duration)
                        <div class="border-2 border-gray-300 rounded-lg p-4 cursor-pointer hover:border-blue-600 transition"
                             @click="form.ad_duration_id = {{ $duration->id }}; $el.classList.remove('border-gray-300'); $el.classList.add('border-blue-600', 'bg-blue-50'); $el.parentElement.querySelectorAll('div').forEach(el => { if(el !== $el) { el.classList.remove('border-blue-600', 'bg-blue-50'); el.classList.add('border-gray-300'); } })">
                            <div class="flex justify-between items-center">
                                <div>
                                    <div class="font-semibold text-gray-900">{{ app()->getLocale() === 'ar' ? ($duration->name_ar ?? $duration->name_en) : $duration->name_en }}</div>
                                    <div class="text-sm text-gray-500">{{ $duration->days }} {{ app()->getLocale() === 'ar' ? 'يوم' : 'days' }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-gray-900">{{ number_format($duration->price, 0) }} {{ $duration->currency }}</div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">{{ app()->getLocale() === 'ar' ? 'لا توجد مدد إعلان متاحة' : 'No ad durations available' }}</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm text-blue-900 font-medium">{{ app()->getLocale() === 'ar' ? 'سيتم مراجعة إعلانك وتفعيله بعد تأكيد الدفع.' : 'Your listing will be reviewed and activated after payment confirmation.' }}</p>
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button @click="step = 2" class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-lg font-semibold">{{ app()->getLocale() === 'ar' ? 'رجوع' : 'Back' }}</button>
                <button @click="submitListing()" :disabled="submitting" class="flex-1 bg-green-600 text-white py-3 rounded-lg font-semibold disabled:opacity-50">
                    <span x-show="!submitting">{{ app()->getLocale() === 'ar' ? 'إرسال الإعلان' : 'Submit Listing' }}</span>
                    <span x-show="submitting">{{ app()->getLocale() === 'ar' ? 'جاري الإرسال...' : 'Submitting...' }}</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function listingForm() {
        return {
            step: 1,
            submitting: false,
            subcategories: [],
            cities: [],
            form: {
                title: { en: '', ar: '' },
                description: { en: '', ar: '' },
                subcategory_id: '',
                city_id: '',
                area_id: '',
                bedrooms: 0,
                bathrooms: 1,
                area_m2: '',
                price: '',
                location: '',
                location_url: '',
                listing_type: 'sale',
                ad_duration_id: '',
                photos: []
            },

            async init() {
                const apiBase = window.__AQARI_API_BASE || '';
                try {
                    const res = await fetch(apiBase + '/api/mobile/listing-meta', {
                        headers: { 'Accept': 'application/json' }
                    });
                    if (res.ok) {
                        const data = await res.json();
                        this.subcategories = data.subcategories || [];
                        this.cities = data.cities || [];
                    }
                } catch (e) {
                    console.error('Failed to load listing meta', e);
                }
            },

            handlePhotoUpload(event) {
                const files = Array.from(event.target.files);
                files.forEach(file => {
                    if (this.form.photos.length >= 10) return;
                    
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.form.photos.push(e.target.result);
                        this.renderPhotos();
                    };
                    reader.readAsDataURL(file);
                });
            },

            renderPhotos() {
                const grid = document.getElementById('photos-grid');
                grid.innerHTML = '';
                
                this.form.photos.forEach((photo, index) => {
                    const div = document.createElement('div');
                    div.className = 'relative aspect-square';
                    div.innerHTML = `
                        <img src="${photo}" class="w-full h-full object-cover rounded-lg">
                        <button onclick="removePhoto(${index})" class="absolute top-1 right-1 w-6 h-6 bg-red-600 text-white rounded-full flex items-center justify-center">×</button>
                        ${index === 0 ? '<span class="absolute bottom-1 left-1 bg-blue-600 text-white text-xs px-2 py-1 rounded">Cover</span>' : ''}
                    `;
                    grid.appendChild(div);
                });
            },

            nextStep() {
                if (this.step === 1 && !this.validateStep1()) return;
                this.step++;
            },

            validateStep1() {
                if (!this.form.title.en || !this.form.description.en || !this.form.subcategory_id || 
                    !this.form.city_id || !this.form.listing_type || !this.form.price) {
                    alert(document.documentElement.lang.startsWith('ar') ? 'يرجى ملء جميع الحقول المطلوبة' : 'Please fill in all required fields');
                    return false;
                }
                return true;
            },

            async submitListing() {
                if (!this.form.ad_duration_id) {
                    alert(document.documentElement.lang.startsWith('ar') ? 'يرجى اختيار مدة الإعلان' : 'Please select an ad duration');
                    return;
                }

                this.submitting = true;
                const isAr = document.documentElement.lang.startsWith('ar');
                const apiBase = window.__AQARI_API_BASE || '';

                try {
                    const token = localStorage.getItem('aqari_mobile_token');
                    let response;

                    if (token) {
                        // Sanctum token: always call remote API base (production server in NativePHP)
                        response = await fetch(apiBase + '/api/mobile/resident-listings', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'Authorization': 'Bearer ' + token,
                            },
                            body: JSON.stringify(this.form)
                        });
                        if (response.status === 401) {
                            localStorage.removeItem('aqari_mobile_token');
                            response = null;
                        }
                    }

                    // Session auth fallback: only works in web browser mode (no remote API base)
                    if (!response && !apiBase) {
                        response = await fetch('/api/mobile/resident-listings/web', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify(this.form)
                        });
                    }

                    if (!response || response.status === 401) {
                        window.location.href = '{{ route('mobile.login') }}';
                        return;
                    }

                    if (!response.ok) {
                        const errData = await response.json();
                        if (errData.errors) {
                            const msgs = Object.values(errData.errors).flat().join('\n');
                            alert(msgs);
                        } else {
                            alert(errData.message || (isAr ? 'فشل إنشاء الإعلان.' : 'Failed to create listing.'));
                        }
                        this.submitting = false;
                        return;
                    }

                    alert(isAr ? 'تم إنشاء الإعلان بنجاح!' : 'Listing created successfully!');
                    window.location.href = '{{ route('mobile.my-listings.index') }}';
                } catch (error) {
                    console.error('Error:', error);
                    alert(isAr ? 'حدث خطأ في الاتصال. حاول مرة أخرى.' : 'Connection error. Please try again.');
                    this.submitting = false;
                }
            }
        };
    }

    function removePhoto(index) {
        const component = Alpine.$data(document.querySelector('[x-data]'));
        component.form.photos.splice(index, 1);
        component.renderPhotos();
    }
</script>
@endsection
