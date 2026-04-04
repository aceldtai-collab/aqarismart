@extends('mobile.layouts.app', [
    'title' => app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Dashboard',
    'subtitle' => '',
    'show_back_button' => false,
    'body_class' => 'mobile-account-shell',
])

@php
    $isAr = app()->getLocale() === 'ar';
    $strings = [
        'loading' => $isAr ? 'جارٍ تحميل لوحة التحكم...' : 'Loading dashboard...',
        'guestKicker' => $isAr ? 'الوصول المحمي' : 'Protected access',
        'guestTitle' => $isAr ? 'سجّل الدخول لتصل إلى لوحة التحكم' : 'Sign in to access the dashboard',
        'guestText' => $isAr ? 'لوحة التحكم مخصصة للمستخدمين المرتبطين بوكالة أو عقد سكني. سجّل الدخول أولاً ثم أكمل الرحلة من الداخل.' : 'The dashboard is for users connected to a tenant workspace or resident lease. Sign in first, then continue the journey from inside.',
        'signIn' => $isAr ? 'تسجيل الدخول' : 'Sign In',
        'marketplace' => $isAr ? 'العودة إلى السوق' : 'Back to marketplace',
        'errorKicker' => $isAr ? 'تعذّر التحميل' : 'Unable to load',
        'errorTitle' => $isAr ? 'حدث خطأ أثناء جلب اللوحة' : 'Something went wrong while loading the dashboard',
        'errorText' => $isAr ? 'تأكد من تسجيل الدخول أو من وجود سياق وكالة صالح، ثم حاول مجدداً.' : 'Check that you are signed in and have a valid tenant context, then try again.',
        'tryMarketplace' => $isAr ? 'ارجع إلى السوق' : 'Return to marketplace',
        'heroKicker' => $isAr ? 'مركز الوكالة' : 'Workspace hub',
        'heroFallback' => $isAr ? 'واجهة تشغيل يومية تربطك بالمخزون والعقود والمتابعة السريعة من نفس الهاتف.' : 'A daily operating view that connects inventory, leases, and fast actions from the same phone.',
        'staffRole' => $isAr ? 'فريق العمل' : 'Staff',
        'residentRole' => $isAr ? 'مقيم' : 'Resident',
        'dashboard' => $isAr ? 'لوحة التحكم' : 'Dashboard',
        'profile' => $isAr ? 'ملفي' : 'My profile',
        'openWeb' => $isAr ? 'فتح لوحة الويب' : 'Open web dashboard',
        'openSite' => $isAr ? 'فتح الموقع العام' : 'Open public site',
        'heroWorkspace' => $isAr ? 'الوكالة' : 'Workspace',
        'heroAccess' => $isAr ? 'الوصول' : 'Access',
        'heroStatus' => $isAr ? 'الخطة' : 'Plan',
        'performanceKicker' => $isAr ? 'لقطة تشغيلية' : 'Performance snapshot',
        'performanceTitle' => $isAr ? 'قراءة سريعة للحركة الحالية' : 'A quick read on current activity',
        'performanceText' => $isAr ? 'الأرقام الأهم في الواجهة قبل النزول إلى التفاصيل.' : 'The most important numbers to scan before going deeper.',
        'properties' => $isAr ? 'العقارات' : 'Properties',
        'units' => $isAr ? 'الوحدات' : 'Units',
        'leases' => $isAr ? 'العقود' : 'Leases',
        'maintenance' => $isAr ? 'الصيانة' : 'Maintenance',
        'occupancy' => $isAr ? 'الإشغال' : 'Occupancy',
        'monthlyRent' => $isAr ? 'الدخل الشهري' : 'Monthly rent',
        'viewings' => $isAr ? 'المعاينات القادمة' : 'Upcoming viewings',
        'leads' => $isAr ? 'العملاء المحتملون' : 'Leads',
        'operationsTitle' => $isAr ? 'التشغيل والتحصيل' : 'Operations and rent flow',
        'operationsText' => $isAr ? 'ملخص الإشغال والتحصيل والصيانة المفتوحة.' : 'A quick summary of occupancy, rent flow, and open maintenance.',
        'leadTitle' => $isAr ? 'الحركة التجارية' : 'Pipeline pulse',
        'leadText' => $isAr ? 'إشارات سريعة عن العملاء والطلبات المفتوحة.' : 'Fast signals around lead flow and request volume.',
        'propertyMixTitle' => $isAr ? 'أداء العقارات' : 'Property performance',
        'propertyMixText' => $isAr ? 'أكثر العقارات اقتراباً من الامتلاء حالياً.' : 'The properties currently closest to full occupancy.',
        'noPropertyMix' => $isAr ? 'لا توجد عقارات كافية لعرض الأداء بعد.' : 'There is not enough property data to show performance yet.',
        'upcomingTitle' => $isAr ? 'عقود تنتهي قريباً' : 'Upcoming expirations',
        'upcomingText' => $isAr ? 'العقود التي تحتاج متابعة مبكرة خلال الفترة القادمة.' : 'Leases that need early follow-up in the coming period.',
        'noUpcoming' => $isAr ? 'لا توجد عقود قريبة الانتهاء حالياً.' : 'There are no imminent expirations right now.',
        'quickActionsTitle' => $isAr ? 'الانتقال السريع' : 'Quick navigation',
        'quickActionsText' => $isAr ? 'اختصارات سريعة لأكثر الوجهات استخداماً في الموبايل.' : 'Shortcuts to the most-used destinations in the mobile flow.',
        'residentOverviewTitle' => $isAr ? 'رحلتك داخل الوكالة' : 'Your journey inside this tenant',
        'residentOverviewText' => $isAr ? 'مركز بسيط يجمّع بياناتك وعقودك وأقصر طريق للعودة إلى السوق أو الملف.' : 'A simpler home base that brings together your identity, leases, and the fastest route back to the marketplace or profile.',
        'residentInfoTitle' => $isAr ? 'بياناتك الحالية' : 'Your current details',
        'residentLeasesTitle' => $isAr ? 'عقودك الحالية' : 'Your leases',
        'residentNoLeases' => $isAr ? 'لا توجد عقود مرتبطة بهذا الحساب حالياً.' : 'There are no leases attached to this account right now.',
        'residentActionsTitle' => $isAr ? 'إلى أين بعد ذلك؟' : 'Where next?',
        'residentActionsText' => $isAr ? 'تنقل سريع بين السوق والملف والموقع العام للوكالة.' : 'Move quickly between the marketplace, your profile, and the tenant public site.',
        'myUnits' => $isAr ? 'وحداتي' : 'My units',
        'addUnit' => $isAr ? 'إضافة وحدة' : 'Add unit',
        'inventoryText' => $isAr ? 'راجع المخزون الحالي' : 'Review current inventory',
        'newUnitText' => $isAr ? 'ابدأ إضافة وحدة جديدة' : 'Start a new unit',
        'marketplaceText' => $isAr ? 'تابع السوق العام' : 'Continue in the marketplace',
        'profileText' => $isAr ? 'ارجع إلى ملفك' : 'Return to your profile',
        'name' => $isAr ? 'الاسم' : 'Name',
        'email' => $isAr ? 'البريد الإلكتروني' : 'Email',
        'phone' => $isAr ? 'الهاتف' : 'Phone',
        'tenant' => $isAr ? 'الوكالة' : 'Tenant',
        'active' => $isAr ? 'نشط' : 'Active',
        'occupiedWord' => $isAr ? 'مشغول' : 'occupied',
        'openRequests' => $isAr ? 'طلبات مفتوحة' : 'Open requests',
        'averageAge' => $isAr ? 'متوسط العمر' : 'Average age',
        'expires' => $isAr ? 'ينتهي' : 'Expires',
        'maintenanceStatuses' => [
            'new' => $isAr ? 'جديد' : 'New',
            'open' => $isAr ? 'مفتوح' : 'Open',
            'in_progress' => $isAr ? 'قيد التنفيذ' : 'In progress',
            'resolved' => $isAr ? 'تم الحل' : 'Resolved',
            'completed' => $isAr ? 'مكتمل' : 'Completed',
        ],
        'fallback' => $isAr ? 'غير متاح' : 'Not available',
    ];
@endphp

@push('head')
    @include('mobile.partials.account-theme')
@endpush

@section('content')
    <div class="mpa-page">
        <div class="mpa-shell">
            <div id="dash-loading" class="mpa-state">
                <div class="mpa-spinner"></div>
                <p class="mt-4 text-sm font-semibold text-[#6d726c]">{{ $strings['loading'] }}</p>
            </div>

            <div id="dash-no-auth" class="hidden space-y-5">
                <section class="mpa-hero">
                    <div class="mpa-hero-copy px-5 py-6 sm:px-6 sm:py-7">
                        <div class="mpa-kicker">{{ $strings['guestKicker'] }}</div>
                        <div class="mt-4 mpa-ornament"></div>
                        <div class="mt-5 space-y-3">
                            <h1 class="text-[2rem] font-black leading-[1.02] tracking-[-0.05em] text-[#fff8ea] sm:text-[2.25rem]">{{ $strings['guestTitle'] }}</h1>
                            <p class="max-w-xl text-[0.96rem] leading-8 text-white/78">{{ $strings['guestText'] }}</p>
                        </div>
                        <div class="mt-5 flex flex-wrap gap-2.5">
                            <div class="mpa-chip">{{ $isAr ? 'وكالة أو مقيم' : 'Tenant or resident' }}</div>
                            <div class="mpa-chip">{{ $isAr ? 'رحلة داخلية' : 'Protected flow' }}</div>
                        </div>
                        <div class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <a href="{{ route('mobile.login') }}" class="mpa-button mpa-button-primary">{{ $strings['signIn'] }}</a>
                            <a href="{{ route('mobile.marketplace') }}" class="mpa-button mpa-button-secondary">{{ $strings['marketplace'] }}</a>
                        </div>
                    </div>
                </section>
            </div>

            <div id="dash-error" class="hidden space-y-5">
                <section class="mpa-card p-5">
                    <div class="mpa-section-head">
                        <div>
                            <div class="mpa-section-kicker">{{ $strings['errorKicker'] }}</div>
                            <h2 class="mpa-section-title" id="dash-error-title">{{ $strings['errorTitle'] }}</h2>
                        </div>
                    </div>
                    <p class="mpa-section-text" id="dash-error-msg">{{ $strings['errorText'] }}</p>
                    <div class="mt-5">
                        <a href="{{ route('mobile.marketplace') }}" class="mpa-button mpa-button-secondary">{{ $strings['tryMarketplace'] }}</a>
                    </div>
                </section>
            </div>

            <div id="dash-content" class="hidden space-y-5 pb-8">
                <section class="mpa-hero">
                    <div class="mpa-hero-copy px-5 py-6 sm:px-6 sm:py-7">
                        <div class="mpa-kicker" id="dash-hero-kicker">{{ $strings['heroKicker'] }}</div>
                        <div class="mt-4 mpa-ornament"></div>

                        <div class="mt-5 flex items-start gap-4">
                            <div id="dash-tenant-logo" class="mpa-avatar"></div>
                            <div class="min-w-0 flex-1">
                                <h1 id="dash-tenant-name" class="truncate text-[1.9rem] font-black leading-[1.02] tracking-[-0.05em] text-[#fff8ea]"></h1>
                                <p id="dash-user-info" class="mt-2 text-sm font-semibold text-white/78"></p>
                                <p id="dash-hero-summary" class="mt-3 text-[0.95rem] leading-8 text-white/74"></p>
                            </div>
                        </div>

                        <div id="dash-hero-chips" class="mt-5 flex flex-wrap gap-2.5"></div>
                        <div id="dash-hero-stats" class="mt-5 grid grid-cols-3 gap-3"></div>
                        <div id="dash-hero-actions" class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2"></div>
                    </div>
                </section>

                <div id="dash-staff" class="hidden space-y-5">
                    <section class="mpa-card p-5">
                        <div class="mpa-section-head">
                            <div>
                                <div class="mpa-section-kicker">{{ $strings['performanceKicker'] }}</div>
                                <h2 class="mpa-section-title">{{ $strings['performanceTitle'] }}</h2>
                            </div>
                        </div>
                        <p class="mpa-section-text">{{ $strings['performanceText'] }}</p>
                        <div id="dash-metric-grid" class="mpa-metric-grid mt-5"></div>
                    </section>

                    <section class="mpa-card p-5">
                        <div class="mpa-section-head">
                            <div>
                                <div class="mpa-section-kicker">{{ $strings['operationsTitle'] }}</div>
                                <h2 class="mpa-section-title">{{ $strings['operationsTitle'] }}</h2>
                            </div>
                        </div>
                        <p class="mpa-section-text">{{ $strings['operationsText'] }}</p>
                        <div id="dash-ops-stack" class="mpa-stack mt-5"></div>
                    </section>

                    <section class="mpa-card p-5">
                        <div class="mpa-section-head">
                            <div>
                                <div class="mpa-section-kicker">{{ $strings['leadTitle'] }}</div>
                                <h2 class="mpa-section-title">{{ $strings['leadTitle'] }}</h2>
                            </div>
                        </div>
                        <p class="mpa-section-text">{{ $strings['leadText'] }}</p>
                        <div id="dash-lead-stack" class="mpa-stack mt-5"></div>
                    </section>

                    <section class="mpa-card p-5">
                        <div class="mpa-section-head">
                            <div>
                                <div class="mpa-section-kicker">{{ $strings['propertyMixTitle'] }}</div>
                                <h2 class="mpa-section-title">{{ $strings['propertyMixTitle'] }}</h2>
                            </div>
                        </div>
                        <p class="mpa-section-text">{{ $strings['propertyMixText'] }}</p>
                        <div id="dash-property-mix" class="mpa-progress mt-5"></div>
                        <div id="dash-property-mix-empty" class="mpa-note mt-5 hidden">{{ $strings['noPropertyMix'] }}</div>
                    </section>

                    <section class="mpa-card p-5">
                        <div class="mpa-section-head">
                            <div>
                                <div class="mpa-section-kicker">{{ $strings['upcomingTitle'] }}</div>
                                <h2 class="mpa-section-title">{{ $strings['upcomingTitle'] }}</h2>
                            </div>
                        </div>
                        <p class="mpa-section-text">{{ $strings['upcomingText'] }}</p>
                        <div id="dash-upcoming" class="mpa-timeline mt-5"></div>
                        <div id="dash-upcoming-empty" class="mpa-note mt-5 hidden">{{ $strings['noUpcoming'] }}</div>
                    </section>

                    <section class="mpa-card p-5">
                        <div class="mpa-section-head">
                            <div>
                                <div class="mpa-section-kicker">{{ $strings['quickActionsTitle'] }}</div>
                                <h2 class="mpa-section-title">{{ $strings['quickActionsTitle'] }}</h2>
                            </div>
                        </div>
                        <p class="mpa-section-text">{{ $strings['quickActionsText'] }}</p>
                        <div id="dash-staff-actions" class="mpa-action-grid mt-5"></div>
                    </section>
                </div>

                <div id="dash-resident" class="hidden space-y-5">
                    <section class="mpa-card p-5">
                        <div class="mpa-section-head">
                            <div>
                                <div class="mpa-section-kicker">{{ $strings['residentOverviewTitle'] }}</div>
                                <h2 class="mpa-section-title">{{ $strings['residentOverviewTitle'] }}</h2>
                            </div>
                        </div>
                        <p class="mpa-section-text">{{ $strings['residentOverviewText'] }}</p>
                        <div id="dash-resident-stats" class="mpa-metric-grid mt-5"></div>
                    </section>

                    <section class="mpa-card p-5">
                        <div class="mpa-section-head">
                            <div>
                                <div class="mpa-section-kicker">{{ $strings['residentInfoTitle'] }}</div>
                                <h2 class="mpa-section-title">{{ $strings['residentInfoTitle'] }}</h2>
                            </div>
                        </div>
                        <div id="dash-resident-info" class="mpa-list mt-5"></div>
                    </section>

                    <section class="mpa-card p-5">
                        <div class="mpa-section-head">
                            <div>
                                <div class="mpa-section-kicker">{{ $strings['residentLeasesTitle'] }}</div>
                                <h2 class="mpa-section-title">{{ $strings['residentLeasesTitle'] }}</h2>
                            </div>
                        </div>
                        <div id="dash-leases" class="mpa-timeline mt-5"></div>
                        <div id="dash-leases-empty" class="mpa-note mt-5 hidden">{{ $strings['residentNoLeases'] }}</div>
                    </section>

                    <section class="mpa-card p-5">
                        <div class="mpa-section-head">
                            <div>
                                <div class="mpa-section-kicker">{{ $strings['residentActionsTitle'] }}</div>
                                <h2 class="mpa-section-title">{{ $strings['residentActionsTitle'] }}</h2>
                            </div>
                        </div>
                        <p class="mpa-section-text">{{ $strings['residentActionsText'] }}</p>
                        <div id="dash-resident-actions" class="mpa-action-grid mt-5"></div>
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('mobile.partials.dashboard-script', ['strings' => $strings])
@endpush
