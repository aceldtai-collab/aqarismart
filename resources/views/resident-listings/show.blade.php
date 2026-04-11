@php
    $isAr = app()->getLocale() === 'ar';
    $photos = array_values($residentListing->photos ?? []);
    $firstPhoto = $residentListing->first_photo;
    $title = $residentListing->translated_title ?: ($isAr ? 'عقار مباشر من المالك' : 'Direct owner property');
    $navLinks = [
        ['label' => $isAr ? 'الرئيسية' : 'Home', 'href' => route('home')],
        ['label' => $isAr ? 'السوق' : 'Marketplace', 'href' => route('public.marketplace')],
        ['label' => $isAr ? 'إعلانات الملاك' : 'Owner listings', 'href' => route('my-listings.index')],
    ];
    $navTx = ['brand' => $isAr ? 'عقاري سمارت' : 'Aqari Smart', 'login_cta' => $isAr ? 'تسجيل الدخول' : 'Sign in', 'register_cta' => $isAr ? 'إنشاء حساب' : 'Create account', 'sell_cta' => $isAr ? 'بيع معنا' : 'Sell with us', 'profile_cta' => $isAr ? 'الملف الشخصي' : 'Profile', 'menu_cta' => $isAr ? 'القائمة' : 'Menu', 'close_cta' => $isAr ? 'إغلاق' : 'Close', 'account_title' => $isAr ? 'حسابك' : 'Your Account', 'browse_title' => $isAr ? 'تصفح السوق' : 'Browse marketplace', 'dashboard_cta' => $isAr ? 'لوحة التحكم' : 'Dashboard', 'logout_cta' => $isAr ? 'تسجيل الخروج' : 'Log Out', 'welcome_cta' => $isAr ? 'أهلاً' : 'Welcome', 'guest_subtitle' => $isAr ? 'رحلة موحدة بين السوق والحساب.' : 'A unified journey between marketplace and account.', 'switch_language' => $isAr ? 'تغيير اللغة' : 'Switch language'];
    $sellWithUsUrl = Route::has('sales-flow') ? route('sales-flow') : '#';
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isAr ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | {{ config('app.name') }}</title>
    <x-vite-assets />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @include('resident-listings.partials.theme')
</head>
<body style="margin:0;background:radial-gradient(circle at top left, rgba(182,132,47,.14), transparent 22%),radial-gradient(circle at top right, rgba(15,90,70,.11), transparent 24%),linear-gradient(180deg, #eee2cc 0, #f7efdf 300px, #fbf7ef 100%);font-family:'{{ $isAr ? 'Cairo' : 'Manrope' }}','Manrope',system-ui,sans-serif;color:var(--rl-ink)">
    @include('public.partials.market-nav', compact('isAr', 'navLinks', 'navTx', 'sellWithUsUrl'))
    <main class="rl-shell" style="padding-top:8.5rem">
        <section class="rl-hero">
            <div class="rl-hero-copy">
                <div class="rl-kicker">{{ $isAr ? 'إعلان مباشر من المالك' : 'Direct-owner listing' }}</div>
                <div class="rl-ornament" style="margin-top:1rem"></div>
                <div style="display:grid;gap:1rem;grid-template-columns:minmax(0,1.1fr) minmax(320px,.9fr);align-items:end;margin-top:1.2rem">
                    <div>
                        <div class="rl-pill {{ $residentListing->is_expired ? 'warn' : ($residentListing->ad_status === 'active' ? 'success' : 'soft') }}">{{ $residentListing->is_expired ? ($isAr ? 'منتهي' : 'Expired') : ($residentListing->ad_status === 'active' ? ($isAr ? 'نشط' : 'Active') : ($isAr ? 'معلق' : 'Pending')) }}</div>
                        <h1 style="margin:.9rem 0 0;font-size:clamp(2rem,4vw,3.35rem);line-height:1.02;font-weight:900;letter-spacing:-.05em">{{ $title }}</h1>
                        <p style="margin:.9rem 0 0;font-size:1rem;line-height:1.85;color:rgba(255,255,255,.76)">{{ $residentListing->translated_description }}</p>
                    </div>
                    <div style="display:grid;gap:.75rem">
                        <div class="rl-stat"><div class="rl-stat-label">{{ $isAr ? 'السعر' : 'Price' }}</div><div class="rl-stat-value">{{ number_format((float) $residentListing->price, 0) }} {{ $residentListing->currency }}</div></div>
                        <div class="rl-stat"><div class="rl-stat-label">{{ $isAr ? 'النوع' : 'Type' }}</div><div class="rl-stat-value">{{ $residentListing->listing_type === 'sale' ? ($isAr ? 'للبيع' : 'For Sale') : ($isAr ? 'للإيجار' : 'For Rent') }}</div></div>
                        <div class="rl-stat"><div class="rl-stat-label">{{ $isAr ? 'الموقع' : 'Location' }}</div><div class="rl-stat-value">{{ $residentListing->location ?: ($residentListing->city?->{$isAr ? 'name_ar' : 'name_en'} ?? $residentListing->city?->name_en ?? ($isAr ? 'غير محدد' : 'Not set')) }}</div></div>
                    </div>
                </div>
            </div>
        </section>

        <div class="rl-grid" style="margin-top:1.25rem;grid-template-columns:minmax(0,1.15fr) minmax(320px,.85fr)">
            <section class="rl-card">
                <div class="rl-section-kicker">{{ $isAr ? 'المعرض' : 'Gallery' }}</div>
                <h2 class="rl-section-title">{{ $isAr ? 'صور العقار' : 'Property gallery' }}</h2>
                <div style="margin-top:1.25rem">
                    @if($firstPhoto)
                        <img src="{{ $firstPhoto }}" alt="{{ $title }}" style="width:100%;height:420px;object-fit:cover;border-radius:1.5rem;background:#e5e7eb">
                    @endif
                    @if(count($photos) > 1)
                        <div class="rl-gallery-grid" style="margin-top:.85rem">
                            @foreach($photos as $photo)
                                <img src="{{ $photo }}" alt="{{ $title }}" style="width:100%;height:160px;object-fit:cover;border-radius:1rem;background:#e5e7eb">
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>

            <aside class="rl-card">
                <div class="rl-section-kicker">{{ $isAr ? 'ملخص الإعلان' : 'Listing summary' }}</div>
                <h2 class="rl-section-title">{{ $isAr ? 'الحقائق الأساسية' : 'Key facts' }}</h2>
                <div class="rl-grid" style="margin-top:1.25rem">
                    <div class="rl-pill soft">{{ $residentListing->code }}</div>
                    <div style="display:flex;gap:1rem;flex-wrap:wrap;color:#5f655f;font-size:.95rem">
                        @if($residentListing->bedrooms > 0)<span>{{ $residentListing->bedrooms }} {{ $isAr ? 'غرف نوم' : 'bedrooms' }}</span>@endif
                        @if($residentListing->bathrooms > 0)<span>{{ $residentListing->bathrooms }} {{ $isAr ? 'حمامات' : 'bathrooms' }}</span>@endif
                        @if($residentListing->area_m2)<span>{{ number_format((float) $residentListing->area_m2, 0) }} m²</span>@endif
                    </div>
                    <div>
                        <div class="rl-label">{{ $isAr ? 'المدينة' : 'City' }}</div>
                        <div>{{ $residentListing->city?->{$isAr ? 'name_ar' : 'name_en'} ?? $residentListing->city?->name_en ?? ($isAr ? 'غير متاح' : 'Not available') }}</div>
                    </div>
                    @if($residentListing->area)
                        <div>
                            <div class="rl-label">{{ $isAr ? 'المنطقة / المحافظة' : 'Area / governorate' }}</div>
                            <div>{{ $residentListing->area?->{$isAr ? 'name_ar' : 'name_en'} ?? $residentListing->area?->name_en }}</div>
                        </div>
                    @endif
                    @if($residentListing->location_url)
                        <a href="{{ $residentListing->location_url }}" target="_blank" rel="noreferrer" class="rl-button rl-button-secondary">{{ $isAr ? 'افتح الخريطة' : 'Open map' }}</a>
                    @endif
                    @auth
                        @if(auth()->id() === $residentListing->user_id)
                            <a href="{{ route('my-listings.edit', $residentListing->code) }}" class="rl-button rl-button-primary">{{ $isAr ? 'تعديل الإعلان' : 'Edit listing' }}</a>
                        @endif
                    @endauth
                </div>
            </aside>
        </div>
    </main>
</body>
</html>
