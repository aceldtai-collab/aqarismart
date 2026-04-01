@extends('mobile.layouts.app', ['title' => 'Create Unit', 'subtitle' => 'Full-parity mobile form'])

@section('content')
    <form id="mobile-unit-create-form" class="space-y-4 rounded-3xl bg-emerald-300/10 p-6 shadow-lg ring-1 ring-emerald-300/30" enctype="multipart/form-data">
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-medium text-white">{{ app()->getLocale() === 'ar' ? 'العقار' : 'Property' }}</label>
                <select name="property_id" id="mobile-unit-property" class="w-full rounded-2xl border border-emerald-300/20 bg-emerald-600/50 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30"></select>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-white">{{ app()->getLocale() === 'ar' ? 'التصنيف الفرعي' : 'Subcategory' }}</label>
                <select name="subcategory_id" id="mobile-unit-subcategory" class="w-full rounded-2xl border border-emerald-300/20 bg-emerald-600/50 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30"></select>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-white">{{ app()->getLocale() === 'ar' ? 'العنوان (إنجليزي)' : 'Title (EN)' }}</label>
                <input name="title_en" class="w-full rounded-2xl border border-emerald-300/20 bg-emerald-600/50 px-4 py-3 text-white placeholder-emerald-300/60 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-white">{{ app()->getLocale() === 'ar' ? 'العنوان (عربي)' : 'Title (AR)' }}</label>
                <input name="title_ar" class="w-full rounded-2xl border border-emerald-300/20 bg-emerald-600/50 px-4 py-3 text-white placeholder-emerald-300/60 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30">
            </div>
            <div class="sm:col-span-2">
                <label class="mb-2 block text-sm font-medium text-white">{{ app()->getLocale() === 'ar' ? 'الوصف (إنجليزي)' : 'Description (EN)' }}</label>
                <textarea name="description_en" rows="3" class="w-full rounded-2xl border border-emerald-300/20 bg-emerald-600/50 px-4 py-3 text-white placeholder-emerald-300/60 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30"></textarea>
            </div>
            <div class="sm:col-span-2">
                <label class="mb-2 block text-sm font-medium text-white">{{ app()->getLocale() === 'ar' ? 'الوصف (عربي)' : 'Description (AR)' }}</label>
                <textarea name="description_ar" rows="3" class="w-full rounded-2xl border border-emerald-300/20 bg-emerald-600/50 px-4 py-3 text-white placeholder-emerald-300/60 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30"></textarea>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-white">{{ app()->getLocale() === 'ar' ? 'المدينة' : 'City' }}</label>
                <select name="city_id" id="mobile-unit-city" class="w-full rounded-2xl border border-emerald-300/20 bg-emerald-600/50 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30"></select>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-white">{{ app()->getLocale() === 'ar' ? 'السعر' : 'Price' }}</label>
                <input name="price" type="number" step="0.01" class="w-full rounded-2xl border border-emerald-300/20 bg-emerald-600/50 px-4 py-3 text-white placeholder-emerald-300/60 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-white">{{ app()->getLocale() === 'ar' ? 'العملة' : 'Currency' }}</label>
                <select name="currency" id="mobile-unit-currency" class="w-full rounded-2xl border border-emerald-300/20 bg-emerald-600/50 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30"><option value="JOD">JOD</option><option value="USD">USD</option><option value="EUR">EUR</option></select>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-white">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</label>
                <select name="status" id="mobile-unit-status" class="w-full rounded-2xl border border-emerald-300/20 bg-emerald-600/50 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30"><option value="vacant">{{ app()->getLocale() === 'ar' ? 'شاغر' : 'Vacant' }}</option><option value="occupied">{{ app()->getLocale() === 'ar' ? 'مشغول' : 'Occupied' }}</option><option value="sold">{{ app()->getLocale() === 'ar' ? 'مباع' : 'Sold' }}</option></select>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-white">{{ app()->getLocale() === 'ar' ? 'نوع الإعلان' : 'Listing type' }}</label>
                <select name="listing_type" id="mobile-unit-listing-type" class="w-full rounded-2xl border border-emerald-300/20 bg-emerald-600/50 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30"><option value="rent">{{ app()->getLocale() === 'ar' ? 'إيجار' : 'Rent' }}</option><option value="sale">{{ app()->getLocale() === 'ar' ? 'بيع' : 'Sale' }}</option></select>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-white">{{ app()->getLocale() === 'ar' ? 'رابط الموقع' : 'Location URL' }}</label>
                <input name="location_url" class="w-full rounded-2xl border border-emerald-300/20 bg-emerald-600/50 px-4 py-3 text-white placeholder-emerald-300/60 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30">
            </div>
            <div class="sm:col-span-2">
                <label class="mb-2 block text-sm font-medium text-white">{{ app()->getLocale() === 'ar' ? 'الصور' : 'Photos' }}</label>
                <input name="photos[]" type="file" multiple accept="image/*" class="w-full rounded-2xl border border-emerald-300/20 bg-emerald-600/50 px-4 py-3 text-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-white file:text-emerald-700 hover:file:bg-white/90">
            </div>
        </div>

        <div class="grid gap-4 rounded-2xl bg-emerald-300/20 p-4 sm:grid-cols-2">
            <div><label class="mb-2 block text-sm font-medium text-white">{{ app()->getLocale() === 'ar' ? 'اللواء' : 'Directorate' }}</label><input name="official[directorate]" class="w-full rounded-2xl border border-emerald-300/20 bg-emerald-600/50 px-4 py-3 text-white placeholder-emerald-300/60 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30"></div>
            <div><label class="mb-2 block text-sm font-medium text-white">{{ app()->getLocale() === 'ar' ? 'القرية' : 'Village' }}</label><input name="official[village]" class="w-full rounded-2xl border border-emerald-300/20 bg-emerald-600/50 px-4 py-3 text-white placeholder-emerald-300/60 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30"></div>
            <div><label class="mb-2 block text-sm font-medium text-white">{{ app()->getLocale() === 'ar' ? 'رقم الحوض' : 'Basin number' }}</label><input name="official[basin_number]" class="w-full rounded-2xl border border-emerald-300/20 bg-emerald-600/50 px-4 py-3 text-white placeholder-emerald-300/60 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30"></div>
            <div><label class="mb-2 block text-sm font-medium text-white">{{ app()->getLocale() === 'ar' ? 'اسم الحوض' : 'Basin name' }}</label><input name="official[basin_name]" class="w-full rounded-2xl border border-emerald-300/20 bg-emerald-600/50 px-4 py-3 text-white placeholder-emerald-300/60 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30"></div>
            <div><label class="mb-2 block text-sm font-medium text-white">{{ app()->getLocale() === 'ar' ? 'رقم القطعة' : 'Plot number' }}</label><input name="official[plot_number]" class="w-full rounded-2xl border border-emerald-300/20 bg-emerald-600/50 px-4 py-3 text-white placeholder-emerald-300/60 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30"></div>
            <div><label class="mb-2 block text-sm font-medium text-white">{{ app()->getLocale() === 'ar' ? 'رقم الشقة' : 'Apartment number' }}</label><input name="official[apartment_number]" class="w-full rounded-2xl border border-emerald-300/20 bg-emerald-600/50 px-4 py-3 text-white placeholder-emerald-300/60 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30"></div>
        </div>

        <div class="grid gap-4 rounded-2xl bg-emerald-300/20 p-4 sm:grid-cols-2">
            <div><label class="mb-2 block text-sm font-medium text-white">{{ app()->getLocale() === 'ar' ? 'اسم المالك' : 'Owner name' }}</label><input name="owner[name]" class="w-full rounded-2xl border border-emerald-300/20 bg-emerald-600/50 px-4 py-3 text-white placeholder-emerald-300/60 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30"></div>
            <div><label class="mb-2 block text-sm font-medium text-white">{{ app()->getLocale() === 'ar' ? 'هاتف المالك' : 'Owner phone' }}</label><input name="owner[phone]" class="w-full rounded-2xl border border-emerald-300/20 bg-emerald-600/50 px-4 py-3 text-white placeholder-emerald-300/60 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30"></div>
            <div><label class="mb-2 block text-sm font-medium text-white">{{ app()->getLocale() === 'ar' ? 'بريد المالك' : 'Owner email' }}</label><input name="owner[email]" type="email" class="w-full rounded-2xl border border-emerald-300/20 bg-emerald-600/50 px-4 py-3 text-white placeholder-emerald-300/60 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30"></div>
            <div><label class="mb-2 block text-sm font-medium text-white">{{ app()->getLocale() === 'ar' ? 'ملاحظات المالك' : 'Owner notes' }}</label><input name="owner[notes]" class="w-full rounded-2xl border border-emerald-300/20 bg-emerald-600/50 px-4 py-3 text-white placeholder-emerald-300/60 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30"></div>
        </div>

        <button type="submit" class="w-full rounded-2xl bg-white text-emerald-700 px-4 py-3 text-sm font-semibold shadow-lg transition hover:bg-white/90 hover:shadow-xl">{{ app()->getLocale() === 'ar' ? 'إنشاء وحدة' : 'Create Unit' }}</button>
    </form>
    <pre id="mobile-unit-create-result" class="mt-4 hidden overflow-auto rounded-2xl bg-gray-50 p-4 text-xs text-emerald-300"></pre>
@endsection

@push('scripts')
<script>
const unitCreateForm = document.getElementById('mobile-unit-create-form');
const unitCreateResult = document.getElementById('mobile-unit-create-result');
const token = localStorage.getItem('aqari_mobile_token');
const tenantSlug = localStorage.getItem('aqari_mobile_tenant_slug');

async function loadUnitMeta() {
    if (!token) return;
    const response = await fetch((window.__AQARI_API_BASE || '') + '/api/mobile/units/meta', {
        headers: {
            Accept: 'application/json',
            Authorization: `Bearer ${token}`,
            'X-Tenant-Slug': tenantSlug || '',
        },
    });
    const data = await response.json();
    document.getElementById('mobile-unit-property').innerHTML = '<option value="">No property</option>' + (data.properties || []).map(item => `<option value="${item.id}">${item.name}</option>`).join('');
    document.getElementById('mobile-unit-subcategory').innerHTML = (data.subcategories || []).map(item => `<option value="${item.id}">${item.name}</option>`).join('');
    document.getElementById('mobile-unit-city').innerHTML = '<option value="">No city</option>' + (data.cities || []).map(item => `<option value="${item.id}">${item.name_en}</option>`).join('');
}

unitCreateForm?.addEventListener('submit', async (event) => {
    event.preventDefault();
    const formData = new FormData(unitCreateForm);
    const response = await fetch((window.__AQARI_API_BASE || '') + '/api/mobile/units', {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            Authorization: `Bearer ${token}`,
            'X-Tenant-Slug': tenantSlug || '',
        },
        body: formData,
    });
    const data = await response.json();
    unitCreateResult.textContent = JSON.stringify(data, null, 2);
    unitCreateResult.classList.remove('hidden');
});

loadUnitMeta();
</script>
@endpush
