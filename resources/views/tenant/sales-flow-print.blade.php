@php
    $isArabic = app()->getLocale() === 'ar';
    $tenantCtx = app(\App\Services\Tenancy\TenantManager::class)->tenant();
    $brandName = optional($tenantCtx)->name ?? config('app.name');
    $flowRoute = $tenantCtx ? route('tenant.sales-flow') : route('sales-flow');
@endphp
<!DOCTYPE html>
<html lang="{{ $isArabic ? 'ar' : 'en' }}" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $brandName }} | Sales Flow</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;700&family=Sora:wght@400;500;600&family=Noto+Kufi+Arabic:wght@500;700&display=swap" rel="stylesheet">
        <style>
            :root {
                --ink: #0f172a;
                --muted: #475569;
                --brand: #0f766e;
                --accent: #f59e0b;
                --paper: #f8fafc;
                --line: #e2e8f0;
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                font-family: "Sora", sans-serif;
                color: var(--ink);
                background: var(--paper);
            }

            .print-actions {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 1rem;
                padding: 1.25rem 1.5rem;
                border-bottom: 1px solid var(--line);
                background: #fff;
                position: sticky;
                top: 0;
                z-index: 10;
            }

            .print-actions a,
            .print-actions button {
                border: 1px solid rgba(15, 118, 110, 0.25);
                background: #fff;
                color: var(--brand);
                padding: 0.55rem 1rem;
                border-radius: 999px;
                font-weight: 600;
                font-size: 0.85rem;
                cursor: pointer;
                text-decoration: none;
            }

            .print-shell {
                max-width: 980px;
                margin: 2.5rem auto 3.5rem;
                background: #fff;
                border-radius: 18px;
                border: 1px solid var(--line);
                padding: 2.5rem 2.8rem;
                box-shadow: 0 18px 40px -30px rgba(15, 23, 42, 0.35);
            }

            .print-header {
                display: flex;
                justify-content: space-between;
                gap: 1.5rem;
                border-bottom: 1px solid var(--line);
                padding-bottom: 1.5rem;
                margin-bottom: 1.5rem;
            }

            .print-header h1 {
                font-family: "Fraunces", serif;
                font-size: 2.4rem;
                margin: 0 0 0.4rem;
            }

            .print-header p {
                margin: 0;
                color: var(--muted);
                font-size: 1rem;
            }

            .arabic-tag {
                font-family: "Noto Kufi Arabic", sans-serif;
                background: rgba(15, 118, 110, 0.1);
                padding: 0.6rem 1rem;
                border-radius: 12px;
                color: #0b534f;
                font-size: 0.95rem;
            }

            .flow-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
                gap: 1.5rem;
                margin-top: 1.5rem;
            }

            .flow-card {
                border: 1px solid var(--line);
                border-radius: 16px;
                padding: 1.4rem;
            }

            .flow-card h2 {
                margin: 0 0 0.5rem;
                font-size: 1.2rem;
            }

            .flow-card ol {
                margin: 0.6rem 0 0;
                padding: 0 0 0 1.1rem;
                color: var(--muted);
                line-height: 1.6;
            }

            .flow-card .script {
                margin-top: 1rem;
                padding: 0.85rem 1rem;
                border-radius: 12px;
                background: rgba(245, 158, 11, 0.12);
                color: #7c3f00;
                font-size: 0.92rem;
            }

            .flow-card .script.ar {
                font-family: "Noto Kufi Arabic", sans-serif;
            }

            .checklist {
                margin-top: 2rem;
                border-top: 1px dashed var(--line);
                padding-top: 1.5rem;
                display: grid;
                gap: 0.8rem;
            }

            .checklist h3 {
                margin: 0;
                font-size: 1.1rem;
            }

            .checklist ul {
                margin: 0;
                padding: 0;
                list-style: none;
                display: grid;
                gap: 0.6rem;
                color: var(--muted);
            }

            .checklist li::before {
                content: "●";
                color: var(--accent);
                margin-right: 0.5rem;
            }

            .footer-note {
                margin-top: 2rem;
                font-size: 0.85rem;
                color: var(--muted);
                text-align: center;
            }

            @media print {
                body {
                    background: #fff;
                }

                .print-actions {
                    display: none;
                }

                .print-shell {
                    box-shadow: none;
                    border: none;
                    margin: 0;
                    padding: 0;
                }
            }
        </style>
    </head>
    <body>
        <div class="print-actions">
            <strong>{{ $brandName }}</strong>
            <div class="actions">
                <a href="{{ $flowRoute }}">Back to flow</a>
                <button type="button" onclick="window.print()">Print / اطبع</button>
            </div>
        </div>

        <main class="print-shell">
            <section class="print-header">
                <div>
                    <h1>Sales Flow Playbook</h1>
                    <p>Give every owner and client a clear journey, a clear promise, and a clear next step.</p>
                </div>
                <div class="arabic-tag" dir="rtl">
                    خطة بيع مرتبة توصلك من أول مكالمة إلى عقد موثوق وتسليم مرتب.
                </div>
            </section>

            <section class="flow-grid">
                <article class="flow-card">
                    <h2>Rent Journey</h2>
                    <ol>
                        <li>Qualify the tenant and confirm budget + move-in date.</li>
                        <li>Show the best-fit unit and highlight lifestyle value.</li>
                        <li>Collect documents, reserve the unit, and confirm terms.</li>
                        <li>Sign the lease, collect first payment, and schedule handover.</li>
                        <li>Follow up for referrals and long-term retention.</li>
                    </ol>
                    <div class="script">
                        “This unit matches your timeline and budget. If we reserve it today, you can move in with zero stress.”
                    </div>
                    <div class="script ar" dir="rtl">
                        «هذا العقار مناسب لوقتك وميزانيتك. لو نحجزه اليوم، تنتقل بدون أي ضغط.»
                    </div>
                </article>

                <article class="flow-card">
                    <h2>Sale Journey</h2>
                    <ol>
                        <li>Confirm ownership goals and target price range.</li>
                        <li>Stage and position the unit with a premium story.</li>
                        <li>Run qualified viewings and capture buyer intent.</li>
                        <li>Negotiate with confidence and lock the agreement.</li>
                        <li>Close, document, and celebrate the handover.</li>
                    </ol>
                    <div class="script">
                        “We position your property as the smartest choice in this market, then we close with certainty.”
                    </div>
                    <div class="script ar" dir="rtl">
                        «نبرز عقارك كأفضل خيار في السوق، وبثقة نغلق الصفقة.»
                    </div>
                </article>
            </section>

            <section class="checklist">
                <h3>Confidence Checklist</h3>
                <ul>
                    <li>Every client knows the next step before leaving the meeting.</li>
                    <li>Every viewing ends with a clear action and a time promise.</li>
                    <li>Every close includes a referral request and thank-you ritual.</li>
                </ul>
                <div class="arabic-tag" dir="rtl">
                    كل خطوة فيها رسالة واضحة، وكل إغلاق فيه سبب للترشيحات.
                </div>
            </section>

            <p class="footer-note">Prepared for {{ $brandName }} — use this as your daily sales ritual.</p>
        </main>
    </body>
</html>
