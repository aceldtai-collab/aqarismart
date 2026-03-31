@extends('mobile.layouts.app', ['title' => 'Edit Unit', 'subtitle' => $unit->code])

@section('content')
    <form id="mobile-unit-edit-form" class="space-y-4 rounded-3xl bg-emerald-300/10 backdrop-blur-sm p-6 shadow-lg ring-1 ring-emerald-300/30" enctype="multipart/form-data">
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-medium text-white">{{ app()->getLocale() === 'ar' ? 'العقار' : 'Property' }}</label>
                <select name="property_id" id="mobile-unit-edit-property" class="w-full rounded-2xl border border-white/20 bg-white/10 backdrop-blur-sm px-4 py-3 text-white focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30"></select>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-white">{{ app()->getLocale() === 'ar' ? 'التصنيف الفرعي' : 'Subcategory' }}</label>
                <select name="subcategory_id" id="mobile-unit-edit-subcategory" class="w-full rounded-2xl border border-white/20 bg-white/10 backdrop-blur-sm px-4 py-3 text-white focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30"></select>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-white">{{ app()->getLocale() === 'ar' ? 'العنوان (إنجليزي)' : 'Title (EN)' }}</label>
                <input name="title[en]" id="mobile-unit-edit-title-en" class="w-full rounded-2xl border border-white/20 bg-white/10 backdrop-blur-sm px-4 py-3 text-white placeholder-white/60 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-white">{{ app()->getLocale() === 'ar' ? 'العنوان (عربي)' : 'Title (AR)' }}</label>
                <input name="title[ar]" id="mobile-unit-edit-title-ar" class="w-full rounded-2xl border border-white/20 bg-white/10 backdrop-blur-sm px-4 py-3 text-white placeholder-white/60 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30">
            </div>
            <div class="sm:col-span-2">
                <label class="mb-2 block text-sm font-medium text-white">{{ app()->getLocale() === 'ar' ? 'الوصف (إنجليزي)' : 'Description (EN)' }}</label>
                <textarea name="description[en]" id="mobile-unit-edit-description-en" rows="3" class="w-full rounded-2xl border border-white/20 bg-white/10 backdrop-blur-sm px-4 py-3 text-white placeholder-white/60 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30"></textarea>
            </div>
            <div class="sm:col-span-2">
                <label class="mb-2 block text-sm font-medium text-white">{{ app()->getLocale() === 'ar' ? 'الوصف (عربي)' : 'Description (AR)' }}</label>
                <textarea name="description[ar]" id="mobile-unit-edit-description-ar" rows="3" class="w-full rounded-2xl border border-white/20 bg-white/10 backdrop-blur-sm px-4 py-3 text-white placeholder-white/60 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30"></textarea>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-white">{{ app()->getLocale() === 'ar' ? 'المدينة' : 'City' }}</label>
                <select name="city_id" id="mobile-unit-edit-city" class="w-full rounded-2xl border border-white/20 bg-white/10 backdrop-blur-sm px-4 py-3 text-white focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30"></select>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-white">{{ app()->getLocale() === 'ar' ? 'السعر' : 'Price' }}</label>
                <input name="price" id="mobile-unit-edit-price" type="number" min="0" step="0.01" class="w-full rounded-2xl border border-white/20 bg-white/10 backdrop-blur-sm px-4 py-3 text-white placeholder-white/60 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-white">{{ app()->getLocale() === 'ar' ? 'العملة' : 'Currency' }}</label>
                <select name="currency" id="mobile-unit-edit-currency" class="w-full rounded-2xl border border-white/20 bg-white/10 backdrop-blur-sm px-4 py-3 text-white focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30"><option value="JOD">JOD</option><option value="USD">USD</option></select>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-white">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</label>
                <select name="status" id="mobile-unit-edit-status" class="w-full rounded-2xl border border-white/20 bg-white/10 backdrop-blur-sm px-4 py-3 text-white focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30"><option value="vacant">{{ app()->getLocale() === 'ar' ? 'شاغر' : 'Vacant' }}</option><option value="occupied">{{ app()->getLocale() === 'ar' ? 'مشغول' : 'Occupied' }}</option><option value="sold">{{ app()->getLocale() === 'ar' ? 'مباع' : 'Sold' }}</option></select>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-white">{{ app()->getLocale() === 'ar' ? 'نوع الإعلان' : 'Listing type' }}</label>
                <select name="listing_type" id="mobile-unit-edit-listing-type" class="w-full rounded-2xl border border-white/20 bg-white/10 backdrop-blur-sm px-4 py-3 text-white focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30"><option value="rent">{{ app()->getLocale() === 'ar' ? 'إيجار' : 'Rent' }}</option><option value="sale">{{ app()->getLocale() === 'ar' ? 'بيع' : 'Sale' }}</option></select>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-white">{{ app()->getLocale() === 'ar' ? 'رابط الموقع' : 'Location URL' }}</label>
                <input name="location_url" id="mobile-unit-edit-location-url" class="w-full rounded-2xl border border-white/20 bg-white/10 backdrop-blur-sm px-4 py-3 text-white placeholder-white/60 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/30">
            </div>
            <div class="sm:col-span-2">
                <label class="mb-2 block text-sm font-medium text-white">{{ app()->getLocale() === 'ar' ? 'الصور' : 'Photos' }}</label>
                <input name="photos[]" type="file" multiple accept="image/*" class="w-full rounded-2xl border border-white/20 bg-white/10 backdrop-blur-sm px-4 py-3 text-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-white file:text-emerald-700 hover:file:bg-white/90">
            </div>
        </div>

        <button type="submit" class="w-full rounded-2xl bg-white text-emerald-700 px-4 py-3 text-sm font-semibold shadow-lg transition hover:bg-white/90 hover:shadow-xl">{{ app()->getLocale() === 'ar' ? 'تحديث الوحدة' : 'Update Unit' }}</button>
    </form>
    <pre id="mobile-unit-edit-result" class="mt-4 hidden overflow-auto rounded-2xl bg-gray-50 p-4 text-xs text-emerald-300"></pre>
@endsection

@push('scripts')
<script>
const unitEditForm = document.getElementById('mobile-unit-edit-form');
const unitEditResult = document.getElementById('mobile-unit-edit-result');
const unitEditToken = localStorage.getItem('aqari_mobile_token');
const unitEditTenantSlug = localStorage.getItem('aqari_mobile_tenant_slug');
const unitCode = @json($unit->code);

async function loadUnitEditMeta() {
    if (!unitEditToken) return;
    const [metaResponse, unitResponse] = await Promise.all([
        fetch('/api/mobile/units/meta', {
            headers: {
                Accept: 'application/json',
                Authorization: `Bearer ${unitEditToken}`,
                'X-Tenant-Slug': unitEditTenantSlug || '',
            },
        }),
        fetch(`/api/mobile/units/${unitCode}`, {
            headers: {
                Accept: 'application/json',
                Authorization: `Bearer ${unitEditToken}`,
                'X-Tenant-Slug': unitEditTenantSlug || '',
            },
        })
    ]);

    const meta = await metaResponse.json();
    const payload = await unitResponse.json();
    const unit = payload.data || {};

    document.getElementById('mobile-unit-edit-property').innerHTML = '<option value="">No property</option>' + (meta.properties || []).map(item => `<option value="${item.id}" ${String(item.id) === String(unit.property_id) ? 'selected' : ''}>${item.name}</option>`).join('');
    document.getElementById('mobile-unit-edit-subcategory').innerHTML = (meta.subcategories || []).map(item => `<option value="${item.id}" ${String(item.id) === String(unit.subcategory_id) ? 'selected' : ''}>${item.name}</option>`).join('');
    document.getElementById('mobile-unit-edit-city').innerHTML = '<option value="">No city</option>' + (meta.cities || []).map(item => `<option value="${item.id}" ${String(item.id) === String(unit.city_id) ? 'selected' : ''}>${item.name_en}</option>`).join('');
    document.getElementById('mobile-unit-edit-title-en').value = unit.title?.en || '';
    document.getElementById('mobile-unit-edit-title-ar').value = unit.title?.ar || '';
    document.getElementById('mobile-unit-edit-description-en').value = unit.description?.en || '';
    document.getElementById('mobile-unit-edit-description-ar').value = unit.description?.ar || '';
    document.getElementById('mobile-unit-edit-price').value = unit.price || '';
    document.getElementById('mobile-unit-edit-currency').value = unit.currency || 'JOD';
    document.getElementById('mobile-unit-edit-status').value = unit.status || 'vacant';
    document.getElementById('mobile-unit-edit-listing-type').value = unit.listing_type || 'rent';
    document.getElementById('mobile-unit-edit-location-url').value = unit.location_url || '';
}

unitEditForm?.addEventListener('submit', async (event) => {
    event.preventDefault();
    const formData = new FormData(unitEditForm);
    formData.append('_method', 'PATCH');
    const response = await fetch(`/api/mobile/units/${unitCode}`, {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            Authorization: `Bearer ${unitEditToken}`,
            'X-Tenant-Slug': unitEditTenantSlug || '',
        },
        body: formData,
    });
    const data = await response.json();
    unitEditResult.textContent = JSON.stringify(data, null, 2);
    unitEditResult.classList.remove('hidden');
});

loadUnitEditMeta();
</script>
@endpush
