@php
    $isAr = app()->getLocale() === 'ar';
    $navLinks = [
        ['label' => $isAr ? 'الرئيسية' : 'Home', 'href' => route('home')],
        ['label' => $isAr ? 'السوق' : 'Marketplace', 'href' => route('public.marketplace')],
        ['label' => $isAr ? 'الملف الشخصي' : 'Profile', 'href' => route('profile.edit')],
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
    <title>{{ $isAr ? 'إعلاناتي' : 'My Listings' }} | {{ config('app.name') }}</title>
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
                <div class="rl-kicker">{{ $isAr ? 'إعلانات المالك المباشر' : 'Direct-owner listings' }}</div>
                <div class="rl-ornament" style="margin-top:1rem"></div>
                <div style="display:flex;justify-content:space-between;gap:1rem;align-items:end;flex-wrap:wrap;margin-top:1.2rem">
                    <div style="max-width:56rem">
                        <h1 style="margin:0;font-size:clamp(2rem,4vw,3.25rem);line-height:1.02;font-weight:900;letter-spacing:-.05em">{{ $isAr ? 'أدر إعلاناتك من مكان واحد' : 'Manage your listings from one place' }}</h1>
                        <p style="margin:.9rem 0 0;font-size:1rem;line-height:1.85;color:rgba(255,255,255,.76)">{{ $isAr ? 'أنشئ إعلاناً جديداً، راجع الحالات، وحرّك عقاراتك المباشرة بنفس لغة التصميم العامة للسوق.' : 'Create new listings, review statuses, and move your direct-owner properties through the same polished public journey.' }}</p>
                    </div>
                    <a href="{{ route('my-listings.create') }}" class="rl-button rl-button-primary">{{ $isAr ? 'أضف إعلاناً' : 'Create listing' }}</a>
                </div>
                <div class="rl-stat-grid" style="margin-top:1.25rem">
                    <div class="rl-stat"><div class="rl-stat-label">{{ $isAr ? 'الإجمالي' : 'Total' }}</div><div class="rl-stat-value">{{ $stats['total'] }}</div></div>
                    <div class="rl-stat"><div class="rl-stat-label">{{ $isAr ? 'النشط' : 'Active' }}</div><div class="rl-stat-value">{{ $stats['active'] }}</div></div>
                    <div class="rl-stat"><div class="rl-stat-label">{{ $isAr ? 'قارب الانتهاء' : 'Expiring' }}</div><div class="rl-stat-value">{{ $stats['expiring_soon'] }}</div></div>
                    <div class="rl-stat"><div class="rl-stat-label">{{ $isAr ? 'المنتهي' : 'Expired' }}</div><div class="rl-stat-value">{{ $stats['expired'] }}</div></div>
                </div>
            </div>
        </section>

        <section class="rl-card" style="margin-top:1.25rem">
            @if (session('status') === 'listing-created-payment-required')
                <div class="rl-card" style="margin-bottom:1rem;border-color:rgba(182,132,47,.22);background:rgba(255,248,235,.9);padding:1rem 1.1rem">
                    <div class="rl-section-kicker">{{ $isAr ? 'الدفع والتفعيل' : 'Payment and activation' }}</div>
                    <div class="rl-section-title" style="font-size:1.15rem">{{ $isAr ? 'تم إنشاء الإعلان وهو بانتظار الدفع والتفعيل' : 'The listing was created and is waiting for payment and activation' }}</div>
                    <p class="rl-section-text">{{ $isAr ? 'تم حفظ المدة المطلوبة، لكن الإعلان لن يظهر للعامة حتى يكتمل الدفع ويصبح في حالة نشطة.' : 'Your selected duration was saved, but the listing will not appear publicly until payment is completed and the ad becomes active.' }}</p>
                </div>
            @endif

            <div style="display:flex;justify-content:space-between;gap:1rem;align-items:end;flex-wrap:wrap">
                <div>
                    <div class="rl-section-kicker">{{ $isAr ? 'لوحة الإعلانات' : 'Listing desk' }}</div>
                    <h2 class="rl-section-title">{{ $isAr ? 'كل إعلاناتك الحالية' : 'All your current listings' }}</h2>
                    <p class="rl-section-text">{{ $isAr ? 'راجع الحالات، افتح الإعلان العام، أو عدّل البيانات قبل نشر المزيد.' : 'Review statuses, open the public listing page, or edit details before publishing more.' }}</p>
                </div>
                <div style="display:flex;gap:.65rem;flex-wrap:wrap">
                    <a href="{{ route('my-listings.index') }}" class="rl-pill soft">{{ $isAr ? 'الكل' : 'All' }}</a>
                    <a href="{{ route('my-listings.index', ['status' => 'active']) }}" class="rl-pill success">{{ $isAr ? 'نشط' : 'Active' }}</a>
                    <a href="{{ route('my-listings.index', ['status' => 'expired']) }}" class="rl-pill warn">{{ $isAr ? 'منتهي' : 'Expired' }}</a>
                </div>
            </div>

            <div class="rl-grid" style="margin-top:1.25rem">
                @forelse($listings as $listing)
                    @php
                        $title = $listing->translated_title ?: ($isAr ? 'إعلان بدون عنوان' : 'Untitled listing');
                        $photo = $listing->first_photo;
                        $statusClass = $listing->is_expired ? 'warn' : ($listing->ad_status === 'active' ? 'success' : 'soft');
                        $statusLabel = $listing->is_expired ? ($isAr ? 'منتهي' : 'Expired') : ($listing->ad_status === 'active' ? ($isAr ? 'نشط' : 'Active') : ($isAr ? 'معلق' : 'Pending'));
                    @endphp
                    <article class="rl-listing-card" style="grid-template-columns:240px minmax(0,1fr)">
                        <div>
                            @if($photo)
                                <img src="{{ $photo }}" alt="{{ $title }}" class="rl-thumb">
                            @else
                                <div class="rl-thumb" style="display:flex;align-items:center;justify-content:center;color:#6b7280">{{ $isAr ? 'بدون صورة' : 'No photo' }}</div>
                            @endif
                        </div>
                        <div style="display:grid;gap:.8rem">
                            <div style="display:flex;justify-content:space-between;gap:1rem;align-items:flex-start;flex-wrap:wrap">
                                <div>
                                    <div class="rl-pill {{ $statusClass }}">{{ $statusLabel }}</div>
                                    <h3 style="margin:.7rem 0 0;font-size:1.45rem;line-height:1.1;font-weight:900">{{ $title }}</h3>
                                    <p style="margin:.45rem 0 0;color:#5f655f">{{ $listing->code }} · {{ $listing->listing_type === 'sale' ? ($isAr ? 'للبيع' : 'For Sale') : ($isAr ? 'للإيجار' : 'For Rent') }}</p>
                                </div>
                                <div style="text-align:{{ $isAr ? 'left' : 'right' }}">
                                    <div style="font-size:1.4rem;font-weight:900;color:var(--rl-palm)">{{ number_format((float) $listing->price, 0) }} {{ $listing->currency }}</div>
                                    @if($listing->is_expiring_soon && ! $listing->is_expired)
                                        <div style="margin-top:.35rem;font-size:.8rem;color:var(--rl-brass)">{{ $isAr ? 'ينتهي خلال' : 'Expires in' }} {{ $listing->days_until_expiration }} {{ $isAr ? 'يوم' : 'days' }}</div>
                                    @endif
                                </div>
                            </div>
                            <div style="display:flex;gap:1rem;flex-wrap:wrap;color:#5f655f;font-size:.92rem">
                                <span>{{ $listing->city?->{$isAr ? 'name_ar' : 'name_en'} ?? $listing->city?->name_en ?? ($isAr ? 'غير محدد' : 'Not set') }}</span>
                                @if($listing->bedrooms > 0)<span>{{ $listing->bedrooms }} {{ $isAr ? 'غرف' : 'beds' }}</span>@endif
                                @if($listing->bathrooms > 0)<span>{{ $listing->bathrooms }} {{ $isAr ? 'حمامات' : 'baths' }}</span>@endif
                                @if($listing->area_m2)<span>{{ number_format((float) $listing->area_m2, 0) }} m²</span>@endif
                            </div>
                            <div style="display:flex;gap:.65rem;flex-wrap:wrap">
                                <a href="{{ route('resident-listings.web.show', $listing->code) }}" class="rl-button rl-button-secondary">{{ $isAr ? 'عرض الإعلان' : 'View listing' }}</a>
                                <a href="{{ route('my-listings.edit', $listing->code) }}" class="rl-button rl-button-secondary">{{ $isAr ? 'تعديل' : 'Edit' }}</a>
                                @if($listing->payment_status === 'pending' || $listing->ad_status === 'pending')
                                    <a href="{{ route('my-listings.edit', $listing->code) }}" class="rl-button rl-button-primary">{{ $isAr ? 'أكمل الدفع' : 'Continue checkout' }}</a>
                                @endif
                                <form method="POST" action="{{ route('my-listings.destroy', $listing->code) }}" onsubmit="return confirm('{{ $isAr ? 'هل أنت متأكد من حذف هذا الإعلان؟' : 'Are you sure you want to delete this listing?' }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rl-button rl-button-secondary" style="color:var(--rl-clay)">{{ $isAr ? 'حذف' : 'Delete' }}</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rl-card" style="background:rgba(255,248,235,.82);text-align:center">
                        <div class="rl-section-title" style="font-size:1.25rem">{{ $isAr ? 'لا توجد إعلانات بعد' : 'No listings yet' }}</div>
                        <p class="rl-section-text">{{ $isAr ? 'ابدأ بإضافة أول عقار لك ليظهر مباشرة ضمن تجربة السوق.' : 'Start by creating your first property listing so it appears inside the marketplace journey.' }}</p>
                        <div style="margin-top:1rem"><a href="{{ route('my-listings.create') }}" class="rl-button rl-button-primary">{{ $isAr ? 'أنشئ إعلانك الأول' : 'Post your first property' }}</a></div>
                    </div>
                @endforelse
            </div>

            @if($listings->hasPages())
                <div style="margin-top:1.25rem">{{ $listings->links() }}</div>
            @endif
        </section>
    </main>
</body>
</html>
