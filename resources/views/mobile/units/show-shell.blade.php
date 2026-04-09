<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ app()->getLocale() === 'ar' ? 'تفاصيل العقار' : 'Property Details' }}</title>
    <x-vite-assets />
    <style>
        body { font-family: sans-serif; background: #f8f9fa; margin: 0; }
        .shell-header { background: #fff; border-bottom: 1px solid #e5e7eb; padding: 16px; display: flex; align-items: center; gap: 12px; position: sticky; top: 0; z-index: 10; }
        .shell-back { color: #4b5563; text-decoration: none; font-size: 24px; line-height: 1; }
        .shell-title { font-size: 18px; font-weight: 700; color: #111827; flex: 1; }
        .shell-loading { display: flex; justify-content: center; align-items: center; min-height: 60vh; }
        .spinner { width: 40px; height: 40px; border: 3px solid #e5e7eb; border-top-color: #2563eb; border-radius: 50%; animation: spin 0.8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .unit-gallery img { width: 100%; height: 260px; object-fit: cover; }
        .unit-gallery .no-photo { width: 100%; height: 180px; background: #e5e7eb; display: flex; align-items: center; justify-content: center; color: #9ca3af; font-size: 14px; }
        .unit-body { padding: 16px; }
        .unit-price { font-size: 24px; font-weight: 800; color: #2563eb; }
        .unit-meta { color: #6b7280; font-size: 14px; margin: 4px 0 12px; }
        .unit-badge { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; margin-bottom: 12px; }
        .badge-sale { background: #fef3c7; color: #92400e; }
        .badge-rent { background: #dbeafe; color: #1e40af; }
        .unit-section { background: #fff; border-radius: 12px; padding: 16px; margin-bottom: 12px; box-shadow: 0 1px 3px rgba(0,0,0,.07); }
        .unit-section h3 { font-size: 16px; font-weight: 700; margin: 0 0 10px; color: #111827; }
        .unit-specs { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
        .spec { text-align: center; }
        .spec-val { font-size: 20px; font-weight: 700; color: #111827; }
        .spec-lbl { font-size: 11px; color: #6b7280; margin-top: 2px; }
        .unit-description { font-size: 14px; color: #374151; line-height: 1.6; white-space: pre-wrap; }
        .error-box { text-align: center; padding: 48px 16px; color: #6b7280; }
    </style>
</head>
<body>
<div class="shell-header">
    <a href="javascript:history.back()" class="shell-back">&#8592;</a>
    <span class="shell-title" id="page-title">{{ app()->getLocale() === 'ar' ? 'جاري التحميل...' : 'Loading...' }}</span>
</div>

<div id="loading" class="shell-loading"><div class="spinner"></div></div>
<div id="content" style="display:none"></div>
<div id="error" class="error-box" style="display:none">
    {{ app()->getLocale() === 'ar' ? 'فشل تحميل تفاصيل العقار.' : 'Failed to load property details.' }}
    <br><br>
    <a href="javascript:history.back()" style="color:#2563eb">{{ app()->getLocale() === 'ar' ? 'رجوع' : 'Go Back' }}</a>
</div>

<script>
(async function () {
    const apiBase = '{{ $apiBase }}' || (window.__AQARI_API_BASE || '');
    const code = '{{ $unitCode }}';
    const isAr = document.documentElement.lang.startsWith('ar');

    try {
        const res = await fetch(apiBase + '/api/mobile/units/' + code, {
            headers: { 'Accept': 'application/json' }
        });

        if (!res.ok) throw new Error('not found');

        const unit = await res.json();

        document.getElementById('page-title').textContent =
            (isAr ? unit.translated_title : unit.translated_title) || unit.code;

        const price = new Intl.NumberFormat('en-IQ').format(unit.price ?? 0);
        const currency = unit.currency ?? 'IQD';
        const listingType = unit.listing_type === 'sale'
            ? (isAr ? 'للبيع' : 'For Sale')
            : (isAr ? 'للإيجار' : 'For Rent');
        const badgeClass = unit.listing_type === 'sale' ? 'badge-sale' : 'badge-rent';

        const photos = unit.photos ?? [];
        const galleryHtml = photos.length
            ? `<div class="unit-gallery"><img src="${photos[0]}" alt="${unit.code}" loading="lazy"></div>`
            : `<div class="unit-gallery"><div class="no-photo">${isAr ? 'لا توجد صور' : 'No photos'}</div></div>`;

        const location = [
            unit.city ? (isAr ? unit.city.name_ar : unit.city.name_en) : null,
            unit.area ? (isAr ? unit.area.name_ar : unit.area.name_en) : null,
            unit.location ?? null,
        ].filter(Boolean).join(', ');

        const beds = unit.beds ?? unit.bedrooms ?? null;
        const baths = unit.baths ?? unit.bathrooms ?? null;
        const area = unit.area_m2 ?? null;

        const specsHtml = [
            beds !== null ? `<div class="spec"><div class="spec-val">${beds}</div><div class="spec-lbl">${isAr ? 'غرف نوم' : 'Bedrooms'}</div></div>` : '',
            baths !== null ? `<div class="spec"><div class="spec-val">${baths}</div><div class="spec-lbl">${isAr ? 'حمامات' : 'Bathrooms'}</div></div>` : '',
            area ? `<div class="spec"><div class="spec-val">${area}</div><div class="spec-lbl">م²</div></div>` : '',
        ].filter(Boolean).join('');

        const description = (isAr ? unit.translated_description : unit.translated_description) || '';

        const html = `
            ${galleryHtml}
            <div class="unit-body">
                <div class="unit-price">${price} ${currency}</div>
                <div class="unit-meta">${location}</div>
                <span class="unit-badge ${badgeClass}">${listingType}</span>
                ${specsHtml ? `<div class="unit-section"><h3>${isAr ? 'المواصفات' : 'Specs'}</h3><div class="unit-specs">${specsHtml}</div></div>` : ''}
                ${description ? `<div class="unit-section"><h3>${isAr ? 'الوصف' : 'Description'}</h3><p class="unit-description">${description}</p></div>` : ''}
                ${unit.tenant ? `<div class="unit-section"><h3>${isAr ? 'الوكالة' : 'Agency'}</h3><p style="color:#374151">${unit.tenant.name ?? ''}</p></div>` : ''}
            </div>`;

        document.getElementById('content').innerHTML = html;
        document.getElementById('loading').style.display = 'none';
        document.getElementById('content').style.display = 'block';

    } catch (e) {
        document.getElementById('loading').style.display = 'none';
        document.getElementById('error').style.display = 'block';
    }
})();
</script>
</body>
</html>
