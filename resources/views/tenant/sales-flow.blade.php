@extends('layouts.app')

@section('title', 'Sales Flow')

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';
        $tenantCtx = app(\App\Services\Tenancy\TenantManager::class)->tenant();
        $storyRoute = $tenantCtx ? route('tenant.sales-story') : route('sales-story');
        $printRoute = $tenantCtx ? route('tenant.sales-flow.print') : route('sales-flow.print');
    @endphp
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Space+Grotesk:wght@500;700&family=Cairo:wght@500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --ink: #0f172a;
            --muted: #475569;
            --paper: #f8fafc;
            --brand: #0f766e;
            --brand-2: #0ea5a4;
            --accent: #f97316;
            --accent-2: #f59e0b;
            --line: #e2e8f0;
            --shadow: 0 18px 45px -35px rgba(15, 23, 42, 0.45);
        }

        .flow-shell {
            font-family: "DM Sans", sans-serif;
            color: var(--ink);
            background: radial-gradient(circle at 15% 20%, rgba(14, 165, 164, 0.18), transparent 40%),
                        radial-gradient(circle at 85% 15%, rgba(245, 158, 11, 0.2), transparent 45%),
                        #f1f5f9;
        }

        .flow-shell h1,
        .flow-shell h2,
        .flow-shell h3 {
            font-family: "Space Grotesk", sans-serif;
        }

        .hero-grid {
            display: grid;
            gap: 2.5rem;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        }

        .hero-card {
            background: #fff;
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
            animation: fadeUp 0.8s ease both;
        }

        .hero-card::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(14, 165, 164, 0.08), rgba(249, 115, 22, 0.1));
            pointer-events: none;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(15, 118, 110, 0.12);
            color: var(--brand);
            border-radius: 999px;
            padding: 0.4rem 0.9rem;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }

        .hero-actions a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.55rem 1.1rem;
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.85rem;
            border: 1px solid transparent;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .hero-actions a:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 20px -15px rgba(15, 23, 42, 0.5);
        }

        .action-primary {
            background: var(--brand);
            color: #fff;
        }

        .action-ghost {
            background: #fff;
            color: var(--brand);
            border-color: rgba(15, 118, 110, 0.3);
        }

        .hero-title {
            font-size: clamp(2rem, 4vw, 3.5rem);
            line-height: 1.05;
            margin-top: 1rem;
        }

        .hero-summary {
            margin-top: 1rem;
            color: var(--muted);
            font-size: 1.05rem;
            max-width: 34rem;
        }

        .hero-ar {
            margin-top: 0.85rem;
            padding: 0.9rem 1.1rem;
            border-radius: 16px;
            background: rgba(14, 165, 164, 0.08);
            color: #0b534f;
            font-family: "Cairo", sans-serif;
            font-size: 1rem;
        }

        .hero-metrics {
            margin-top: 2rem;
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        }

        .metric-card {
            border-radius: 16px;
            border: 1px solid var(--line);
            padding: 1rem 1.2rem;
            background: #fff;
        }

        .metric-card span {
            color: var(--brand);
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.07em;
        }

        .metric-card p {
            margin-top: 0.4rem;
            color: var(--muted);
            font-size: 0.95rem;
        }

        .hero-visual {
            background: linear-gradient(145deg, #0f172a, #1e293b);
            border-radius: 24px;
            color: #fff;
            padding: 2rem;
            position: relative;
            box-shadow: var(--shadow);
            overflow: hidden;
            animation: fadeUp 0.8s ease both;
            animation-delay: 0.12s;
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            opacity: 0.7;
            filter: blur(0.5px);
        }

        .orb.one {
            width: 180px;
            height: 180px;
            background: rgba(14, 165, 164, 0.45);
            top: -40px;
            right: -50px;
        }

        .orb.two {
            width: 140px;
            height: 140px;
            background: rgba(249, 115, 22, 0.4);
            bottom: -50px;
            left: -30px;
        }

        .visual-step {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.8rem 1rem;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(255, 255, 255, 0.05);
            margin-bottom: 0.8rem;
        }

        .visual-step span {
            width: 28px;
            height: 28px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .flow-section {
            margin-top: 4rem;
        }

        .section-title {
            font-size: clamp(1.6rem, 3vw, 2.2rem);
            margin-bottom: 0.5rem;
        }

        .section-subtitle {
            color: var(--muted);
            margin-bottom: 2rem;
        }

        .section-ar {
            font-family: "Cairo", sans-serif;
            color: #0b534f;
            margin-bottom: 2rem;
        }

        .flow-grid {
            display: grid;
            gap: 1.5rem;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }

        .flow-step {
            background: #fff;
            border-radius: 18px;
            padding: 1.4rem;
            border: 1px solid var(--line);
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
            animation: rise 0.7s ease-out both;
            animation-delay: var(--delay, 0ms);
        }

        .flow-step::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(120deg, rgba(14, 165, 164, 0.1), transparent 55%);
            opacity: 0.4;
        }

        .flow-step h3 {
            font-size: 1.05rem;
            margin-bottom: 0.35rem;
        }

        .flow-step p {
            color: var(--muted);
            font-size: 0.92rem;
        }

        .flow-step .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--accent);
            letter-spacing: 0.08em;
        }

        .lane-grid {
            display: grid;
            gap: 1.5rem;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        }

        .lane-card {
            border-radius: 22px;
            padding: 1.8rem;
            background: #fff;
            border: 1px solid var(--line);
            box-shadow: var(--shadow);
        }

        .lane-card h3 {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
        }

        .lane-card ul {
            margin-top: 1rem;
            display: grid;
            gap: 0.7rem;
            color: var(--muted);
            font-size: 0.95rem;
        }

        .lane-card li {
            display: grid;
            grid-template-columns: 20px 1fr;
            gap: 0.7rem;
            align-items: start;
        }

        .lane-dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            margin-top: 0.3rem;
            background: var(--brand);
        }

        .lane-card.owner .lane-dot {
            background: var(--accent);
        }

        .journey-grid {
            display: grid;
            gap: 1.5rem;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        }

        .journey-card {
            background: #fff;
            border-radius: 18px;
            padding: 1.4rem;
            border: 1px solid var(--line);
            box-shadow: var(--shadow);
        }

        .journey-card span {
            font-size: 0.75rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--brand);
            font-weight: 700;
        }

        .journey-card p {
            margin-top: 0.5rem;
            color: var(--muted);
        }

        .magic-grid {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }

        .magic-card {
            background: #fff;
            border-radius: 18px;
            padding: 1.2rem 1.4rem;
            border: 1px solid var(--line);
            box-shadow: var(--shadow);
            font-family: "Cairo", sans-serif;
        }

        .magic-card span {
            display: block;
            color: var(--accent);
            font-weight: 700;
            font-size: 0.75rem;
            letter-spacing: 0.08em;
            margin-bottom: 0.4rem;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes rise {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .flow-step,
            .hero-card,
            .hero-visual {
                animation: none;
            }
        }
    </style>

    <div class="flow-shell py-12">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="hero-grid">
                <div class="hero-card">
                    <span class="pill">{{ $isArabic ? 'مخطط تدفق البيع' : 'Sales Flow Blueprint' }}</span>
                    <div class="hero-actions">
                        <a class="action-primary" href="{{ $storyRoute }}">{{ $isArabic ? 'قصة البيع' : 'Sales Story' }}</a>
                        <a class="action-ghost" href="{{ $printRoute }}">{{ $isArabic ? 'نسخة للطباعة' : 'Printable Version' }}</a>
                    </div>
                    <h1 class="hero-title">{{ $isArabic ? 'من الإعلان إلى إغلاق الصفقة... بسلاسة ووضوح.' : 'From listing to closed deal, without noise.' }}</h1>
                    <p class="hero-summary">{{ $isArabic ? 'هذا المسار يوضح رحلة المالك عند عرض وحدة للبيع خطوة بخطوة. مسار خفيف: العقار اختياري، والوسيط اختياري، والطلب هو نقطة البداية، وجاهز للعقود لاحقًا.' : 'This flow shows the exact path a property owner takes when listing a unit for sale. It keeps the process lean: property optional, agent optional, lead-first, and ready for contracts later.' }}</p>
                    @if(! $isArabic)
                        <p class="hero-ar" dir="rtl">كل استفسار فرصة، وكل متابعة تُقربك من التوقيع — هذا هو مسار البيع الذكي.</p>
                    @endif
                    <div class="hero-metrics">
                        <div class="metric-card">
                            <span>{{ $isArabic ? 'العقار اختياري' : 'Property Optional' }}</span>
                            <p>{{ $isArabic ? 'أنشئ وحدة مستقلة أو اربطها بعقار خلال ثوانٍ.' : 'Create a unit independently or link it to a property in seconds.' }}</p>
                        </div>
                        <div class="metric-card">
                            <span>{{ $isArabic ? 'الوسيط اختياري' : 'Agent Optional' }}</span>
                            <p>{{ $isArabic ? 'عيّن وسيطًا الآن أو أدر الطلبات بنفسك.' : 'Assign an agent now or handle leads directly as an owner.' }}</p>
                        </div>
                        <div class="metric-card">
                            <span>{{ $isArabic ? 'الطلبات هي المحرك' : 'Lead Driven' }}</span>
                            <p>{{ $isArabic ? 'كل تواصل يتحول تلقائيًا إلى طلب قابل للمتابعة.' : 'All buyer interest becomes a trackable lead automatically.' }}</p>
                        </div>
                    </div>
                </div>
                <div class="hero-visual">
                    <div class="orb one"></div>
                    <div class="orb two"></div>
                    <h2 class="text-2xl font-semibold mb-4">{{ $isArabic ? 'لمحة سريعة لمسار البيع' : 'Sale Journey Snapshot' }}</h2>
                    <div class="visual-step"><span>1</span> {{ $isArabic ? 'المالك ينشئ وحدة للبيع' : 'Owner creates unit listing' }}</div>
                    <div class="visual-step"><span>2</span> {{ $isArabic ? 'استفسار العميل يتحول إلى طلب' : 'Public inquiry becomes a lead' }}</div>
                    <div class="visual-step"><span>3</span> {{ $isArabic ? 'الوسيط أو المالك يتابع' : 'Agent or owner handles follow-up' }}</div>
                    <div class="visual-step"><span>4</span> {{ $isArabic ? 'معاينة، عرض، ثم إغلاق' : 'Viewing, offer, and close' }}</div>
                    <p class="text-sm text-white/70 mt-4">{{ $isArabic ? 'مسار واضح يوحّد الفريق ويقود النتيجة.' : 'A single, clear path that keeps teams aligned.' }}</p>
                </div>
            </div>

            <section class="flow-section">
                <h2 class="section-title">{{ $isArabic ? 'المسار الأساسي (قبل التقسيم)' : 'Core Flow (Before the split)' }}</h2>
                <p class="section-subtitle">{{ $isArabic ? 'كل إعلان بيع يبدأ بنفس الأساس. التقسيم يحدث فقط عند تعيين وسيط.' : 'Every sale listing follows the same foundation. The split happens only when assigning an agent.' }}</p>
                @if(! $isArabic)
                    <p class="section-ar" dir="rtl">أساس ثابت لكل إعلان، وبعدها تختار إن كان وسيطًا أو إدارة مباشرة.</p>
                @endif
                <div class="flow-grid">
                    <div class="flow-step" style="--delay: 0ms;">
                        <div class="badge">{{ $isArabic ? 'الخطوة 01' : 'Step 01' }}</div>
                        <h3>{{ $isArabic ? 'المالك أو الفريق ينشئ الوحدة' : 'Owner or staff creates the unit' }}</h3>
                        <p>{{ $isArabic ? 'نوع الإعلان للبيع، السعر والوسائط، والعقار اختياري.' : 'Listing type set to For Sale, price and media added, property optional.' }}</p>
                    </div>
                    <div class="flow-step" style="--delay: 80ms;">
                        <div class="badge">{{ $isArabic ? 'الخطوة 02' : 'Step 02' }}</div>
                        <h3>{{ $isArabic ? 'نشر الإعلان' : 'Publish the listing' }}</h3>
                        <p>{{ $isArabic ? 'تظهر الوحدة على بوابة الجمهور وتصبح قابلة للاكتشاف.' : 'The unit appears on the public portal and becomes discoverable.' }}</p>
                    </div>
                    <div class="flow-step" style="--delay: 160ms;">
                        <div class="badge">{{ $isArabic ? 'الخطوة 03' : 'Step 03' }}</div>
                        <h3>{{ $isArabic ? 'العميل يرسل استفسارًا' : 'Buyer submits inquiry' }}</h3>
                        <p>{{ $isArabic ? 'نموذج التواصل يتحول إلى طلب منظم تلقائيًا.' : 'Contact Agent form converts into a structured lead automatically.' }}</p>
                    </div>
                    <div class="flow-step" style="--delay: 240ms;">
                        <div class="badge">{{ $isArabic ? 'الخطوة 04' : 'Step 04' }}</div>
                        <h3>{{ $isArabic ? 'تحديد مالك الطلب' : 'Lead ownership is decided' }}</h3>
                        <p>{{ $isArabic ? 'إن وُجد وسيط يتولى الطلب، وإلا يعود للمالك.' : 'If an agent is assigned, they own the lead. Otherwise the owner does.' }}</p>
                    </div>
                </div>
            </section>

            <section class="flow-section">
                <h2 class="section-title">{{ $isArabic ? 'مساران، نتيجة واحدة' : 'Two lanes, one outcome' }}</h2>
                <p class="section-subtitle">{{ $isArabic ? 'النظام يدعم البيع عبر وسيط أو بدون وسيط دون تكرار.' : 'The system supports both agent-led and owner-led selling without duplication.' }}</p>
                <div class="lane-grid">
                    <div class="lane-card agent">
                        <h3>{{ $isArabic ? 'مع وسيط' : 'With Agent' }}</h3>
                        <p class="text-sm text-slate-500">{{ $isArabic ? 'الوسيط يملك الطلب ويقود المسار.' : 'Agent owns the lead and works the pipeline.' }}</p>
                        <ul>
                            <li><span class="lane-dot"></span>{{ $isArabic ? 'الوسيط يستلم الطلب ويتواصل مع العميل.' : 'Agent receives lead and contacts buyer.' }}</li>
                            <li><span class="lane-dot"></span>{{ $isArabic ? 'تحديد موعد معاينة ومتابعتها.' : 'Viewing scheduled and tracked.' }}</li>
                            <li><span class="lane-dot"></span>{{ $isArabic ? 'تحديثات العرض والتفاوض.' : 'Offer, negotiation, and status updates.' }}</li>
                            <li><span class="lane-dot"></span>{{ $isArabic ? 'إغلاق الصفقة وتجهيز العمولة.' : 'Sale closes, ready for contract and commission.' }}</li>
                        </ul>
                    </div>
                    <div class="lane-card owner">
                        <h3>{{ $isArabic ? 'بدون وسيط' : 'Without Agent' }}</h3>
                        <p class="text-sm text-slate-500">{{ $isArabic ? 'المالك أو الفريق يدير العميل مباشرة.' : 'Owner or staff handles the buyer directly.' }}</p>
                        <ul>
                            <li><span class="lane-dot"></span>{{ $isArabic ? 'المالك يستلم الطلب فورًا.' : 'Owner receives the lead instantly.' }}</li>
                            <li><span class="lane-dot"></span>{{ $isArabic ? 'متابعة يدوية وجدولة المواعيد.' : 'Manual follow-up and scheduling.' }}</li>
                            <li><span class="lane-dot"></span>{{ $isArabic ? 'المالك يفاوض ويحدث الحالة.' : 'Owner negotiates and updates lead status.' }}</li>
                            <li><span class="lane-dot"></span>{{ $isArabic ? 'إغلاق الصفقة مع إمكانية تعيين وسيط لاحقًا.' : 'Sale closes and can be assigned later if needed.' }}</li>
                        </ul>
                    </div>
                </div>
            </section>

            <section class="flow-section">
                <h2 class="section-title">{{ $isArabic ? 'ماذا يتتبع النشاط' : 'What the business tracks' }}</h2>
                <p class="section-subtitle">{{ $isArabic ? 'مؤشرات عملية تعرضها اللوحات والتقارير.' : 'Key data points you can surface in dashboards and reports.' }}</p>
                <div class="journey-grid">
                    <div class="journey-card">
                        <span>{{ $isArabic ? 'حجم الطلبات' : 'Lead Volume' }}</span>
                        <p>{{ $isArabic ? 'عدد الاستفسارات لكل وحدة ووكيل ومستأجر.' : 'Number of inquiries per unit, per agent, and per tenant.' }}</p>
                    </div>
                    <div class="journey-card">
                        <span>{{ $isArabic ? 'التحويلات' : 'Conversion' }}</span>
                        <p>{{ $isArabic ? 'نسبة الطلب إلى معاينة، ثم عرض، ثم إغلاق.' : 'Lead to viewing, viewing to offer, offer to close ratios.' }}</p>
                    </div>
                    <div class="journey-card">
                        <span>{{ $isArabic ? 'زمن الإغلاق' : 'Cycle Time' }}</span>
                        <p>{{ $isArabic ? 'متوسط الأيام من الإعلان إلى التوقيع.' : 'Average days between listing, inquiry, and final close.' }}</p>
                    </div>
                    <div class="journey-card">
                        <span>{{ $isArabic ? 'توزيع المسؤولية' : 'Ownership' }}</span>
                        <p>{{ $isArabic ? 'مقارنة أداء الوسيط مقابل المالك.' : 'Agent vs owner handled leads and performance outcome.' }}</p>
                    </div>
                </div>
            </section>

            <section class="flow-section">
                <h2 class="section-title">{{ $isArabic ? 'عبارات بيع قوية بالعربية' : 'Arabic Sales Magic Words' }}</h2>
                <p class="section-subtitle">{{ $isArabic ? 'استخدمها في الإعلان أو أول رد لتحويل أسرع.' : 'Use these lines in your listing or first reply for stronger conversion.' }}</p>
                <div class="magic-grid">
                    <div class="magic-card" dir="rtl">
                        <span>ثقة فورية</span>
                        <p>عرضك اليوم يصنع قرار الغد — اجعل أول انطباع هو الأقوى.</p>
                    </div>
                    <div class="magic-card" dir="rtl">
                        <span>صفقة أسرع</span>
                        <p>سعر واضح، قيمة واضحة، وطريق أقصر نحو التوقيع.</p>
                    </div>
                    <div class="magic-card" dir="rtl">
                        <span>فرصة مؤكدة</span>
                        <p>كل استفسار فرصة، وكل متابعة خطوة ثابتة نحو الصفقة.</p>
                    </div>
                    <div class="magic-card" dir="rtl">
                        <span>نتيجة محسوبة</span>
                        <p>بيعك ليس مغامرة — بل مسار واضح يقودك للنتيجة.</p>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
