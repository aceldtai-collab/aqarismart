@php
    $isAr = app()->getLocale() === 'ar';
    $navLinks = [
        ['label' => $isAr ? 'الرئيسية' : 'Home', 'href' => route('home')],
        ['label' => $isAr ? 'السوق' : 'Marketplace', 'href' => route('public.marketplace')],
        ['label' => $isAr ? 'إعلاناتي' : 'My Listings', 'href' => route('my-listings.index')],
    ];
    $navTx = ['brand' => $isAr ? 'عقاري سمارت' : 'Aqari Smart', 'login_cta' => $isAr ? 'تسجيل الدخول' : 'Sign in', 'register_cta' => $isAr ? 'إنشاء حساب' : 'Create account', 'sell_cta' => $isAr ? 'بيع معنا' : 'Sell with us', 'profile_cta' => $isAr ? 'الملف الشخصي' : 'Profile', 'menu_cta' => $isAr ? 'القائمة' : 'Menu', 'close_cta' => $isAr ? 'إغلاق' : 'Close', 'account_title' => $isAr ? 'حسابك' : 'Your Account', 'browse_title' => $isAr ? 'تصفح السوق' : 'Browse marketplace', 'dashboard_cta' => $isAr ? 'لوحة التحكم' : 'Dashboard', 'logout_cta' => $isAr ? 'تسجيل الخروج' : 'Log Out', 'welcome_cta' => $isAr ? 'أهلاً' : 'Welcome', 'guest_subtitle' => $isAr ? 'رحلة موحدة بين السوق والحساب.' : 'A unified journey between marketplace and account.', 'switch_language' => $isAr ? 'تغيير اللغة' : 'Switch language'];
    $sellWithUsUrl = Route::has('sales-flow') ? route('sales-flow') : '#';
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isAr ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $isAr ? 'تعديل الإعلان' : 'Edit Listing' }} | {{ config('app.name') }}</title>
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
                <div class="rl-kicker">{{ $isAr ? 'تعديل الإعلان' : 'Edit listing' }}</div>
                <div class="rl-ornament" style="margin-top:1rem"></div>
                <h1 style="margin:1.2rem 0 0;font-size:clamp(2rem,4vw,3.25rem);line-height:1.02;font-weight:900;letter-spacing:-.05em">{{ $residentListing->translated_title ?: ($isAr ? 'تعديل بيانات العقار' : 'Update listing details') }}</h1>
                <p style="margin:.9rem 0 0;max-width:56rem;font-size:1rem;line-height:1.85;color:rgba(255,255,255,.76)">{{ $isAr ? 'عدّل العنوان أو الصور أو تفاصيل الموقع والسعر مع الحفاظ على مسار الويب العام.' : 'Adjust title, photos, location, or price while staying inside the same public web journey.' }}</p>
            </div>
        </section>

        <form method="POST" action="{{ route('my-listings.update', $residentListing->code) }}" enctype="multipart/form-data" style="margin-top:1.25rem">
            @csrf
            @method('PATCH')
            @include('resident-listings.partials.form', [
                'residentListing' => $residentListing,
                'submitLabel' => $isAr ? 'حفظ التعديلات' : 'Save changes',
                'cancelUrl' => route('resident-listings.web.show', $residentListing->code),
            ])
        </form>
    </main>
</body>
</html>
