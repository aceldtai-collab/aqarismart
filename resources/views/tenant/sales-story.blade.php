@extends('layouts.app')

@section('title', 'Sales Story')

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';
        $tenantCtx = app(\App\Services\Tenancy\TenantManager::class)->tenant();
        $flowRoute = $tenantCtx ? route('tenant.sales-flow') : route('sales-flow');
        $printRoute = $tenantCtx ? route('tenant.sales-flow.print') : route('sales-flow.print');
    @endphp
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Outfit:wght@500;700&family=Tajawal:wght@500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --ink: #0f172a;
            --muted: #475569;
            --brand: #0f766e;
            --brand-2: #14b8a6;
            --accent: #f97316;
            --accent-2: #f59e0b;
            --paper: #f8fafc;
            --line: #e2e8f0;
            --shadow: 0 24px 60px -40px rgba(15, 23, 42, 0.55);
        }

        .story-shell {
            font-family: "Manrope", sans-serif;
            color: var(--ink);
            background: radial-gradient(circle at 10% 20%, rgba(20, 184, 166, 0.18), transparent 42%),
                        radial-gradient(circle at 90% 0%, rgba(249, 115, 22, 0.16), transparent 45%),
                        #eef2f7;
            padding: 2.5rem 1.5rem 4rem;
        }

        .story-shell h1,
        .story-shell h2,
        .story-shell h3 {
            font-family: "Outfit", sans-serif;
        }

        .story-wrap {
            max-width: 1100px;
            margin: 0 auto;
        }

        .story-hero {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2.5rem;
            align-items: center;
        }

        .hero-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.4rem 0.9rem;
            border-radius: 999px;
            background: rgba(15, 118, 110, 0.12);
            color: var(--brand);
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .hero-title {
            font-size: clamp(2.1rem, 4vw, 3.4rem);
            line-height: 1.05;
            margin: 1rem 0 0.7rem;
        }

        .hero-copy p {
            font-size: 1.05rem;
            color: var(--muted);
        }

        .hero-ar {
            margin-top: 0.9rem;
            font-family: "Tajawal", sans-serif;
            background: rgba(20, 184, 166, 0.12);
            color: #0b534f;
            padding: 0.9rem 1.1rem;
            border-radius: 14px;
        }

        .bilingual-ar {
            margin-top: 0.35rem;
            font-family: "Tajawal", sans-serif;
            color: #0f3d3a;
            font-size: 0.92rem;
            display: block;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.8rem;
            margin-top: 1.6rem;
        }

        .hero-actions a {
            border-radius: 999px;
            padding: 0.6rem 1.2rem;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            border: 1px solid transparent;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .hero-actions a:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -20px rgba(15, 23, 42, 0.5);
        }

        .btn-primary {
            background: var(--brand);
            color: #fff;
        }

        .btn-ghost {
            border-color: rgba(15, 118, 110, 0.35);
            color: var(--brand);
            background: #fff;
        }

        .hero-card {
            background: #0f172a;
            color: #fff;
            border-radius: 24px;
            padding: 2rem;
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
            animation: floatIn 0.9s ease both;
        }

        .hero-card::after {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 20% 15%, rgba(20, 184, 166, 0.35), transparent 45%),
                        radial-gradient(circle at 80% 90%, rgba(249, 115, 22, 0.3), transparent 45%);
            opacity: 0.85;
            pointer-events: none;
        }

        .hero-card .metric {
            position: relative;
            z-index: 1;
            display: grid;
            gap: 0.8rem;
        }

        .hero-card .bilingual-ar {
            color: rgba(255, 255, 255, 0.75);
            font-size: 0.85rem;
        }

        .metric strong {
            font-size: 1.6rem;
            display: block;
        }

        .metric span {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.75);
        }

        .promise-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }

        .promise-card {
            background: #fff;
            border-radius: 16px;
            padding: 1rem 1.2rem;
            border: 1px solid var(--line);
            animation: floatIn 0.9s ease both;
        }

        .promise-card h4 {
            margin: 0 0 0.3rem;
            font-size: 1rem;
        }

        .promise-card p {
            margin: 0;
            color: var(--muted);
            font-size: 0.9rem;
        }

        .story-grid {
            display: grid;
            gap: 1.5rem;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            margin-top: 3rem;
        }

        .story-card {
            background: #fff;
            border-radius: 20px;
            padding: 1.8rem;
            border: 1px solid var(--line);
            box-shadow: var(--shadow);
            animation: floatIn 0.9s ease both;
        }

        .story-card.rent {
            border-top: 6px solid var(--brand-2);
        }

        .story-card.sale {
            border-top: 6px solid var(--accent);
        }

        .story-card h3 {
            margin-top: 0.2rem;
            font-size: 1.35rem;
        }

        .story-card p {
            color: var(--muted);
        }

        .story-steps {
            margin: 1.2rem 0 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: 0.9rem;
        }

        .story-step {
            display: grid;
            gap: 0.3rem;
            padding: 0.8rem 0.9rem;
            border-radius: 12px;
            background: rgba(15, 118, 110, 0.08);
        }

        .story-card.sale .story-step {
            background: rgba(249, 115, 22, 0.12);
        }

        .story-step strong {
            font-size: 0.95rem;
        }

        .story-step span {
            color: var(--muted);
            font-size: 0.88rem;
        }

        .story-step .ar {
            font-family: "Tajawal", sans-serif;
            color: #0f3d3a;
            font-size: 0.9rem;
        }

        .magic-grid {
            margin-top: 3rem;
            display: grid;
            gap: 1.5rem;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        }

        .magic-card {
            background: #0f172a;
            color: #fff;
            border-radius: 18px;
            padding: 1.6rem;
            position: relative;
            overflow: hidden;
        }

        .magic-card::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(20, 184, 166, 0.3), rgba(249, 115, 22, 0.25));
            opacity: 0.7;
            pointer-events: none;
        }

        .magic-card h4 {
            position: relative;
            margin-top: 0;
            z-index: 1;
        }

        .magic-card p {
            position: relative;
            z-index: 1;
            color: rgba(255, 255, 255, 0.85);
        }

        .magic-card .ar {
            font-family: "Tajawal", sans-serif;
            color: #e0f2fe;
        }

        .closing {
            margin-top: 3rem;
            background: #fff;
            border-radius: 20px;
            padding: 2rem;
            border: 1px dashed var(--line);
            display: grid;
            gap: 1rem;
            text-align: center;
        }

        .closing h2 {
            margin: 0;
        }

        .closing .ar {
            font-family: "Tajawal", sans-serif;
            color: #0f3d3a;
        }

        @keyframes floatIn {
            from {
                opacity: 0;
                transform: translateY(16px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 720px) {
            .hero-actions {
                flex-direction: column;
            }

            .hero-card {
                text-align: center;
            }
        }
    </style>

    <section class="story-shell">
        <div class="story-wrap">
            <div class="story-hero">
                <div class="hero-copy">
                    <span class="hero-tag">{{ $isArabic ? 'قصة البيع' : 'Sales Story' }}</span>
                    <h1 class="hero-title">
                        {{ $isArabic ? 'حوّل كل عقار إلى دعوة خاصة — وإغلاق بثقة هادئة.' : 'Position every listing as a private invitation — and close with quiet certainty.' }}
                    </h1>
                    @if($isArabic)
                        <p class="hero-ar" dir="rtl">لغة بيعية راقية تبني الثقة وتُظهر القيمة وتحوّل الاهتمام إلى قرار محسوم.</p>
                        <p class="hero-ar" dir="rtl">دليل مختصر يجعل كل مكالمة مميزة، وكل معاينة مُقنعة، وكل إغلاق محسوب.</p>
                    @else
                        <p>A refined script that builds trust, showcases value, and converts interest into signed intent.</p>
                        <p>Use this playbook to make every call memorable, every viewing persuasive, and every close assured.</p>
                    @endif
                    <div class="hero-actions">
                        <a href="{{ $flowRoute }}" class="btn-primary">{{ $isArabic ? 'عرض مسار البيع' : 'View Sales Flow' }}</a>
                        <a href="{{ $printRoute }}" class="btn-ghost" target="_blank">{{ $isArabic ? 'نسخة للطباعة' : 'Printable Playbook' }}</a>
                    </div>
                    <div class="promise-grid">
                        <div class="promise-card" style="animation-delay: 0.1s;">
                            <h4>{{ $isArabic ? 'هيبة هادئة' : 'Poised Authority' }}</h4>
                            @if($isArabic)
                                <p class="bilingual-ar" dir="rtl">ابدأ بثقة راقية ووضوح ناضج منذ اللحظة الأولى.</p>
                            @else
                                <p>Open with refined confidence and calm authority from minute one.</p>
                            @endif
                        </div>
                        <div class="promise-card" style="animation-delay: 0.2s;">
                            <h4>{{ $isArabic ? 'عرض مُنتقى' : 'Curated Presentation' }}</h4>
                            @if($isArabic)
                                <p class="bilingual-ar" dir="rtl">قدّم العقار كخبرة حياة: هدوء، مكانة، وراحة.</p>
                            @else
                                <p>Present the property as an experience: comfort, prestige, and ease.</p>
                            @endif
                        </div>
                        <div class="promise-card" style="animation-delay: 0.3s;">
                            <h4>{{ $isArabic ? 'خطوة أنيقة' : 'Elegant Next Step' }}</h4>
                            @if($isArabic)
                                <p class="bilingual-ar" dir="rtl">كل حديث ينتهي بخطوة واضحة وتوقيت راقٍ.</p>
                            @else
                                <p>Every conversation ends with a clear action and a graceful timeline.</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="hero-card">
                    <div class="metric">
                        <div>
                            <strong>{{ $isArabic ? '٣ لحظات' : '3 Moments' }}</strong>
                            @if($isArabic)
                                <span class="bilingual-ar" dir="rtl">جذب · إثبات · إغلاق</span>
                            @else
                                <span>Invite · Reveal · Secure</span>
                            @endif
                        </div>
                        <div>
                            <strong>{{ $isArabic ? 'مساران' : '2 Paths' }}</strong>
                            @if($isArabic)
                                <span class="bilingual-ar" dir="rtl">إيجار + بيع بطقس واحد</span>
                            @else
                                <span>Rent + Sale, one refined ritual</span>
                            @endif
                        </div>
                        <div>
                            <strong>{{ $isArabic ? 'وعد واحد' : '1 Promise' }}</strong>
                            @if($isArabic)
                                <span class="bilingual-ar" dir="rtl">كل لقاء ينتهي بخطوة مكتوبة</span>
                            @else
                                <span>Every meeting ends with a written next step</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="story-grid">
                <article class="story-card rent" style="animation-delay: 0.1s;">
                    <span class="hero-tag" style="background: rgba(20, 184, 166, 0.16); color: var(--brand);">{{ $isArabic ? 'قصة الإيجار' : 'Rent Story' }}</span>
                    <h3>{{ $isArabic ? 'من الانطباع الأول إلى المفاتيح — برحلة راقية.' : 'From first impression to keys — in a refined journey.' }}</h3>
                    @if($isArabic)
                        <p class="bilingual-ar" dir="rtl">مثالي لمن يريد منزلا الآن؛ ابنِ الراحة بثقة وحرّك القرار بذكاء.</p>
                    @else
                        <p>Ideal for clients who want a home now; build comfort, create urgency, and secure the unit.</p>
                    @endif
                    <ul class="story-steps">
                        <li class="story-step">
                            <strong>{{ $isArabic ? 'جذب: ثبّت القرار مبكرًا.' : 'Invite: Anchor the decision early.' }}</strong>
                            @if($isArabic)
                                <span class="ar" dir="rtl">«هذه الوحدة تليق بذوقك وميزانيتك — نثبتها اليوم بكل راحة.»</span>
                            @else
                                <span>“This home fits your taste and timing — we can reserve it today with ease.”</span>
                            @endif
                        </li>
                        <li class="story-step">
                            <strong>{{ $isArabic ? 'إثبات: اربحهم بالتفاصيل اليومية.' : 'Reveal: Win with daily benefits.' }}</strong>
                            @if($isArabic)
                                <span class="ar" dir="rtl">«إضاءة جميلة، هدوء، وخدمات قريبة — حياة مريحة بلا تنازل.»</span>
                            @else
                                <span>“Light, calm, and everything within minutes — effortless living without compromise.”</span>
                            @endif
                        </li>
                        <li class="story-step">
                            <strong>{{ $isArabic ? 'إغلاق: ثبّت الاستلام.' : 'Secure: Lock the move-in.' }}</strong>
                            @if($isArabic)
                                <span class="ar" dir="rtl">«نثبت الحجز الآن ونحدد موعد الاستلام بكل وضوح.»</span>
                            @else
                                <span>“Let’s confirm the reservation and set the move‑in date with clarity.”</span>
                            @endif
                        </li>
                    </ul>
                </article>

                <article class="story-card sale" style="animation-delay: 0.2s;">
                    <span class="hero-tag" style="background: rgba(249, 115, 22, 0.18); color: var(--accent);">{{ $isArabic ? 'قصة البيع' : 'Sale Story' }}</span>
                    <h3>{{ $isArabic ? 'من الرؤية إلى التملك — بقيمة راقية.' : 'From vision to ownership — with refined value.' }}</h3>
                    @if($isArabic)
                        <p class="bilingual-ar" dir="rtl">مثالي لمن يبحث عن قيمة طويلة الأمد؛ أثبت المكسب وثبّت القرار.</p>
                    @else
                        <p>Ideal for clients seeking long‑term value; prove the upside and secure the deal.</p>
                    @endif
                    <ul class="story-steps">
                        <li class="story-step">
                            <strong>{{ $isArabic ? 'جذب: ثبّت الاستثمار.' : 'Invite: Anchor the investment.' }}</strong>
                            @if($isArabic)
                                <span class="ar" dir="rtl">«هذا العقار يحمي القيمة اليوم ويزداد بمرور الوقت.»</span>
                            @else
                                <span>“This property protects value today and appreciates with time.”</span>
                            @endif
                        </li>
                        <li class="story-step">
                            <strong>{{ $isArabic ? 'إثبات: اعرض المكسب.' : 'Reveal: Show the upside.' }}</strong>
                            @if($isArabic)
                                <span class="ar" dir="rtl">«الطلب قوي، السعر مدروس، والمقارنات تُؤكده.»</span>
                            @else
                                <span>“Demand is strong, pricing is deliberate, and comparables confirm it.”</span>
                            @endif
                        </li>
                        <li class="story-step">
                            <strong>{{ $isArabic ? 'إغلاق: ثبّت الاتفاق.' : 'Secure: Confirm the agreement.' }}</strong>
                            @if($isArabic)
                                <span class="ar" dir="rtl">«نثبت العرض وننتقل فورًا للخطوة القانونية.»</span>
                            @else
                                <span>“We confirm the offer and move directly to the legal step.”</span>
                            @endif
                        </li>
                    </ul>
                </article>
            </div>

            <div class="magic-grid">
                <div class="magic-card">
                    <h4>{{ $isArabic ? 'كلمات سحرية: أول اتصال' : 'Magic Words: First Call' }}</h4>
                    @if($isArabic)
                        <p class="ar" dir="rtl">«اليوم ستشعر بالفارق — سأعرض لك أفضل خيار وخطوة واضحة بعدها.»</p>
                    @else
                        <p>“You’ll feel the difference today — I’ll show you the finest option and the next step.”</p>
                    @endif
                </div>
                <div class="magic-card">
                    <h4>{{ $isArabic ? 'كلمات سحرية: المعاينة' : 'Magic Words: Viewing' }}</h4>
                    @if($isArabic)
                        <p class="ar" dir="rtl">«تخيّل يومك هنا — كل التفاصيل تعمل لصالحك.»</p>
                    @else
                        <p>“Picture your day here — every detail works in your favor.”</p>
                    @endif
                </div>
                <div class="magic-card">
                    <h4>{{ $isArabic ? 'كلمات سحرية: الإغلاق' : 'Magic Words: Close' }}</h4>
                    @if($isArabic)
                        <p class="ar" dir="rtl">«خلّينا نثبتها الآن قبل ما ترتفع المنافسة.»</p>
                    @else
                        <p>“Let’s secure it now before the market shifts.”</p>
                    @endif
                </div>
            </div>

            <div class="closing">
                <h2>{{ $isArabic ? 'كل عقار يستحق قصة تنتهي بتوقيع أنيق.' : 'Every listing deserves a story that ends with a refined signature.' }}</h2>
                @if($isArabic)
                    <p class="ar" dir="rtl">الآن لدى فريقك اللغة والإيقاع والإغلاق الراقي.</p>
                @else
                    <p>Now your team has the language, the rhythm, and the premium close.</p>
                @endif
            </div>
        </div>
    </section>
@endsection
