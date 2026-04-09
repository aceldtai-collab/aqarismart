<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ app()->getLocale() === 'ar' ? 'تفاصيل الإعلان' : 'Listing Details' }}</title>
    <x-vite-assets />
    <style>
        body { font-family: sans-serif; background: #f8f9fa; margin: 0; }
        .shell-header { background: #fff; border-bottom: 1px solid #e5e7eb; padding: 16px; display: flex; align-items: center; gap: 12px; position: sticky; top: 0; z-index: 10; }
        .shell-back { color: #4b5563; font-size: 24px; line-height: 1; text-decoration: none; }
        .shell-title { font-size: 18px; font-weight: 700; color: #111827; flex: 1; }
        .shell-loading { display: flex; justify-content: center; align-items: center; min-height: 60vh; }
        .spinner { width: 40px; height: 40px; border: 3px solid #e5e7eb; border-top-color: #2563eb; border-radius: 50%; animation: spin 0.8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .no-photo { width: 100%; height: 200px; background: #e5e7eb; display: flex; align-items: center; justify-content: center; color: #9ca3af; }
        .listing-body { padding: 16px; space-y: 12px; }
        .card { background: #fff; border-radius: 12px; padding: 16px; margin-bottom: 12px; box-shadow: 0 1px 3px rgba(0,0,0,.07); }
        .price { font-size: 28px; font-weight: 800; color: #2563eb; }
        .meta { color: #6b7280; font-size: 14px; margin: 4px 0; }
        .badge { display: inline-block; padding: 3px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; margin: 8px 0; }
        .badge-sale { background: #fef3c7; color: #92400e; }
        .badge-rent { background: #dbeafe; color: #1e40af; }
        .specs { display: flex; gap: 20px; margin: 12px 0; }
        .spec { text-align: center; }
        .spec-val { font-size: 20px; font-weight: 700; color: #111827; }
        .spec-lbl { font-size: 11px; color: #6b7280; }
        h2 { font-size: 16px; font-weight: 700; margin: 0 0 8px; color: #111827; }
        .description { font-size: 14px; color: #374151; line-height: 1.6; }
        .contact-btn { display: block; width: 100%; background: #16a34a; color: #fff; text-align: center; padding: 14px; border-radius: 10px; font-weight: 700; font-size: 16px; text-decoration: none; margin-top: 8px; }
        .error-box { text-align: center; padding: 48px 16px; color: #6b7280; }
    </style>
</head>
<body>
<div class="shell-header">
    <a href="javascript:history.back()" class="shell-back">&#8592;</a>
    <span class="shell-title" id="page-title">{{ app()->getLocale() === 'ar' ? 'جاري التحميل...' : 'Loading...' }}</span>
</div>

<div id="loading" class="shell-loading"><div class="spinner"></div></div>
<div id="content"></div>
<div id="error" class="error-box" style="display:none">
    {{ app()->getLocale() === 'ar' ? 'فشل تحميل تفاصيل الإعلان.' : 'Failed to load listing details.' }}
    <br><br><a href="javascript:history.back()" style="color:#2563eb">{{ app()->getLocale() === 'ar' ? 'رجوع' : 'Go Back' }}</a>
</div>

<script>
(async function () {
    const apiBase = window.__AQARI_API_BASE || '';
    const code = '{{ $listingCode }}';
    const isAr = document.documentElement.lang.startsWith('ar');

    try {
        const res = await fetch(apiBase + '/api/mobile/resident-listings/' + code, {
            headers: { 'Accept': 'application/json' }
        });
        if (!res.ok) throw new Error('not found');
        const d = await res.json();

        document.getElementById('page-title').textContent =
            (isAr ? d.title?.ar : d.title?.en) || code;

        const price = new Intl.NumberFormat('en-IQ').format(d.price ?? 0);
        const currency = d.currency ?? 'IQD';
        const listingType = d.listing_type === 'sale'
            ? (isAr ? 'للبيع' : 'For Sale')
            : (isAr ? 'للإيجار' : 'For Rent');
        const badgeClass = d.listing_type === 'sale' ? 'badge-sale' : 'badge-rent';

        const photo = d.first_photo || (d.photos?.[0] ?? null);
        const photoHtml = photo
            ? `<img src="${photo}" style="width:100%;height:220px;object-fit:cover;" alt="${code}">`
            : `<div class="no-photo">${isAr ? 'لا توجد صور' : 'No photos'}</div>`;

        const location = [
            d.city ? (isAr ? d.city.name_ar : d.city.name_en) : null,
            d.area ? (isAr ? d.area.name_ar : d.area.name_en) : null,
            d.location ?? null,
        ].filter(Boolean).join(', ');

        const beds = d.bedrooms ?? null;
        const baths = d.bathrooms ?? null;
        const area = d.area_m2 ?? null;
        const specsHtml = [
            beds ? `<div class="spec"><div class="spec-val">${beds}</div><div class="spec-lbl">${isAr ? 'غرف' : 'Beds'}</div></div>` : '',
            baths ? `<div class="spec"><div class="spec-val">${baths}</div><div class="spec-lbl">${isAr ? 'حمامات' : 'Baths'}</div></div>` : '',
            area ? `<div class="spec"><div class="spec-val">${area}</div><div class="spec-lbl">m²</div></div>` : '',
        ].filter(Boolean).join('');

        const title = (isAr ? d.title?.ar : d.title?.en) || '';
        const desc = (isAr ? d.description?.ar : d.description?.en) || '';

        const html = `
            ${photoHtml}
            <div class="listing-body">
                <div class="card">
                    <div class="price">${price} ${currency}</div>
                    <span class="badge ${badgeClass}">${listingType}</span>
                    ${location ? `<div class="meta">📍 ${location}</div>` : ''}
                    ${specsHtml ? `<div class="specs">${specsHtml}</div>` : ''}
                    <div class="meta">${isAr ? 'رقم الإعلان' : 'ID'}: ${d.code}</div>
                </div>
                ${desc ? `<div class="card"><h2>${isAr ? 'الوصف' : 'Description'}</h2><p class="description">${desc}</p></div>` : ''}
                ${d.subcategory ? `<div class="card"><h2>${isAr ? 'نوع العقار' : 'Type'}</h2><p class="description">${d.subcategory.name}</p></div>` : ''}
            </div>`;

        document.getElementById('content').innerHTML = html;
        document.getElementById('loading').style.display = 'none';
    } catch (e) {
        document.getElementById('loading').style.display = 'none';
        document.getElementById('error').style.display = 'block';
    }
})();
</script>
</body>
</html>
