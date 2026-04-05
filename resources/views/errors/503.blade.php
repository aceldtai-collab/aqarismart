<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Aqari Smart | Maintenance</title>
    <style>
        :root {
            color-scheme: light;
            --sand: #f5ecdd;
            --cream: #fbf7ef;
            --paper: rgba(255, 250, 241, 0.9);
            --ink: #1d231d;
            --muted: #5a6256;
            --palm: #1f5a47;
            --palm-deep: #143f33;
            --brass: #b78a35;
            --clay: #9d5f3c;
            --line: rgba(31, 90, 71, 0.14);
            --shadow: 0 28px 90px rgba(18, 30, 23, 0.18);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at top left, rgba(183, 138, 53, 0.18), transparent 28rem),
                radial-gradient(circle at bottom right, rgba(157, 95, 60, 0.18), transparent 24rem),
                linear-gradient(160deg, #f7eedf 0%, #efe1ca 42%, #ead8ba 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        body[dir="rtl"] {
            font-family: Tahoma, "Segoe UI", Arial, sans-serif;
        }

        .shell {
            width: min(1080px, 100%);
            border-radius: 32px;
            overflow: hidden;
            background:
                linear-gradient(135deg, rgba(255, 252, 245, 0.96), rgba(249, 241, 227, 0.9));
            border: 1px solid rgba(183, 138, 53, 0.18);
            box-shadow: var(--shadow);
        }

        .iraq-band {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            height: 10px;
        }

        .iraq-band span:nth-child(1) {
            background: #c5362b;
        }

        .iraq-band span:nth-child(2) {
            background: #f8f1e6;
        }

        .iraq-band span:nth-child(3) {
            background: #181b18;
        }

        .content {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 0;
        }

        .main {
            padding: 40px 42px 42px;
            position: relative;
        }

        .main::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 12% 12%, rgba(183, 138, 53, 0.1), transparent 14rem),
                radial-gradient(circle at 88% 88%, rgba(31, 90, 71, 0.11), transparent 18rem);
            pointer-events: none;
        }

        .aside {
            position: relative;
            padding: 40px 34px;
            background:
                linear-gradient(180deg, rgba(31, 90, 71, 0.98), rgba(20, 63, 51, 0.96));
            color: #f9f3e8;
        }

        .aside::before {
            content: "";
            position: absolute;
            inset: 22px;
            border: 1px solid rgba(255, 244, 225, 0.16);
            border-radius: 26px;
            pointer-events: none;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 34px;
            position: relative;
            z-index: 1;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .brand img {
            width: 56px;
            height: 56px;
            border-radius: 18px;
            object-fit: cover;
            background: rgba(255, 255, 255, 0.92);
            padding: 6px;
            box-shadow: 0 12px 32px rgba(22, 33, 26, 0.14);
        }

        .brand-copy {
            min-width: 0;
        }

        .brand-copy strong,
        .brand-copy span {
            display: block;
            white-space: nowrap;
        }

        .brand-copy strong {
            font-size: 1.15rem;
            font-weight: 900;
            letter-spacing: -0.02em;
        }

        .brand-copy span {
            font-size: 0.78rem;
            color: var(--muted);
            letter-spacing: 0.14em;
            text-transform: uppercase;
        }

        .lang-toggle {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px;
            background: rgba(31, 90, 71, 0.07);
            border: 1px solid var(--line);
            border-radius: 999px;
            position: relative;
            z-index: 1;
        }

        .lang-toggle button {
            border: 0;
            background: transparent;
            color: var(--muted);
            font: inherit;
            font-size: 0.82rem;
            font-weight: 800;
            padding: 10px 16px;
            border-radius: 999px;
            cursor: pointer;
            transition: 160ms ease;
        }

        .lang-toggle button.is-active {
            background: var(--palm);
            color: #fbf7ef;
            box-shadow: 0 12px 28px rgba(31, 90, 71, 0.24);
        }

        .pane {
            display: none;
            position: relative;
            z-index: 1;
        }

        .pane.is-active {
            display: block;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: 999px;
            background: rgba(183, 138, 53, 0.12);
            color: var(--palm);
            font-size: 0.78rem;
            font-weight: 900;
            letter-spacing: 0.16em;
            text-transform: uppercase;
        }

        .eyebrow::before {
            content: "";
            width: 9px;
            height: 9px;
            border-radius: 999px;
            background: linear-gradient(135deg, var(--brass), var(--clay));
            box-shadow: 0 0 0 6px rgba(183, 138, 53, 0.14);
        }

        h1 {
            margin: 22px 0 16px;
            font-size: clamp(2.3rem, 4vw, 4.25rem);
            line-height: 0.98;
            letter-spacing: -0.05em;
            font-weight: 950;
            max-width: 11ch;
        }

        body[dir="rtl"] h1 {
            letter-spacing: 0;
            line-height: 1.15;
        }

        .lead {
            margin: 0;
            max-width: 42rem;
            font-size: 1.02rem;
            line-height: 1.9;
            color: var(--muted);
        }

        .metrics {
            margin-top: 28px;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .metric {
            padding: 18px 18px 16px;
            border-radius: 22px;
            background: var(--paper);
            border: 1px solid rgba(31, 90, 71, 0.12);
            box-shadow: 0 12px 34px rgba(31, 37, 28, 0.06);
        }

        .metric strong {
            display: block;
            font-size: 1.55rem;
            font-weight: 900;
            letter-spacing: -0.04em;
        }

        .metric span {
            display: block;
            margin-top: 8px;
            font-size: 0.85rem;
            color: var(--muted);
            line-height: 1.6;
        }

        .info-row {
            margin-top: 24px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 10px 14px;
            border-radius: 999px;
            border: 1px solid rgba(31, 90, 71, 0.12);
            background: rgba(255, 250, 241, 0.78);
            color: var(--ink);
            font-size: 0.88rem;
            font-weight: 700;
        }

        .pill::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: var(--palm);
        }

        .aside .kicker {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.16em;
            font-weight: 800;
            color: rgba(255, 244, 225, 0.76);
            margin: 4px 0 16px;
        }

        .aside h2 {
            margin: 0 0 16px;
            font-size: clamp(1.7rem, 2.4vw, 2.6rem);
            line-height: 1.08;
            font-weight: 950;
            letter-spacing: -0.04em;
        }

        body[dir="rtl"] .aside h2 {
            letter-spacing: 0;
            line-height: 1.25;
        }

        .aside p {
            margin: 0 0 24px;
            color: rgba(249, 243, 232, 0.82);
            line-height: 1.9;
        }

        .stack {
            display: grid;
            gap: 14px;
        }

        .stack-card {
            padding: 18px;
            border-radius: 20px;
            border: 1px solid rgba(255, 244, 225, 0.14);
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(10px);
        }

        .stack-card strong {
            display: block;
            font-size: 0.9rem;
            font-weight: 900;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: rgba(255, 245, 230, 0.84);
            margin-bottom: 8px;
        }

        .stack-card span {
            display: block;
            color: rgba(255, 244, 225, 0.92);
            line-height: 1.7;
            font-size: 0.94rem;
        }

        .footer-note {
            margin-top: 22px;
            padding-top: 18px;
            border-top: 1px solid rgba(255, 244, 225, 0.14);
            color: rgba(255, 244, 225, 0.72);
            font-size: 0.84rem;
            line-height: 1.8;
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        @media (max-width: 900px) {
            .content {
                grid-template-columns: 1fr;
            }

            .metrics {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            body {
                padding: 14px;
            }

            .main,
            .aside {
                padding: 26px 22px;
            }

            .topbar {
                flex-direction: column;
                align-items: stretch;
            }

            .lang-toggle {
                justify-content: center;
            }

            .brand {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <main class="shell" aria-labelledby="maintenance-title">
        <div class="iraq-band" aria-hidden="true">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <div class="content">
            <section class="main">
                <div class="topbar">
                    <div class="brand">
                        <img src="{{ asset('images/logotest.png') }}" alt="Aqari Smart">
                        <div class="brand-copy">
                            <strong>عقاري سمارت</strong>
                            <span>AQARI SMART</span>
                        </div>
                    </div>

                    <div class="lang-toggle" aria-label="Language switcher">
                        <button type="button" class="is-active" data-switch="en">EN</button>
                        <button type="button" data-switch="ar">AR</button>
                    </div>
                </div>

                <article class="pane is-active" data-lang="en">
                    <div class="eyebrow">Scheduled Maintenance</div>
                    <h1 id="maintenance-title">We are polishing the experience.</h1>
                    <p class="lead">
                        Aqari Smart is temporarily unavailable while we deploy updates, improve performance, and prepare the next release.
                        The service will be back shortly.
                    </p>

                    <div class="metrics">
                        <div class="metric">
                            <strong>503</strong>
                            <span>Maintenance mode is active for this release window.</span>
                        </div>
                        <div class="metric">
                            <strong>Secure</strong>
                            <span>Your data stays intact while the platform is being updated.</span>
                        </div>
                        <div class="metric">
                            <strong>Soon</strong>
                            <span>We expect normal access to return once deployment checks finish.</span>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="pill">Platform upgrade in progress</div>
                        <div class="pill">Public and dashboard access paused</div>
                        <div class="pill">Arabic and English supported</div>
                    </div>
                </article>

                <article class="pane" data-lang="ar">
                    <div class="eyebrow">صيانة مجدولة</div>
                    <h1 id="maintenance-title-ar">نعمل الآن على تحسين التجربة.</h1>
                    <p class="lead">
                        عقاري سمارت غير متاح مؤقتاً أثناء نشر التحديثات وتحسين الأداء وتجهيز الإصدار القادم.
                        ستعود الخدمة خلال وقت قصير.
                    </p>

                    <div class="metrics">
                        <div class="metric">
                            <strong>503</strong>
                            <span>وضع الصيانة مفعل خلال نافذة التحديث الحالية.</span>
                        </div>
                        <div class="metric">
                            <strong>آمن</strong>
                            <span>بياناتك تبقى محفوظة أثناء تنفيذ تحديثات المنصة.</span>
                        </div>
                        <div class="metric">
                            <strong>قريباً</strong>
                            <span>سيعود الوصول الطبيعي فور انتهاء التحقق من النشر.</span>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="pill">يجري الآن تحديث المنصة</div>
                        <div class="pill">تم إيقاف الوصول العام ولوحات التحكم مؤقتاً</div>
                        <div class="pill">الدعم متوفر بالعربية والإنجليزية</div>
                    </div>
                </article>
            </section>

            <aside class="aside">
                <article class="pane is-active" data-lang="en">
                    <div class="kicker">Aqari Smart</div>
                    <h2>Property operations, returning sharper.</h2>
                    <p>
                        This downtime window is being used to keep the platform stable, faster, and ready for the next publishing cycle.
                    </p>

                    <div class="stack">
                        <div class="stack-card">
                            <strong>What is happening</strong>
                            <span>Deploying application updates, refreshing caches, and running release checks.</span>
                        </div>
                        <div class="stack-card">
                            <strong>What stays protected</strong>
                            <span>Accounts, tenants, listings, and operational records are not being reset.</span>
                        </div>
                        <div class="stack-card">
                            <strong>Next step</strong>
                            <span>Refresh this page in a few minutes and the platform should be available again.</span>
                        </div>
                    </div>

                    <div class="footer-note">
                        If you are a deployment operator, the bypass secret remains available for approved release checks.
                    </div>
                </article>

                <article class="pane" data-lang="ar">
                    <div class="kicker">عقاري سمارت</div>
                    <h2>تشغيل عقاري يعود بشكل أكثر دقة وثباتاً.</h2>
                    <p>
                        يتم استخدام نافذة التوقف هذه للحفاظ على استقرار المنصة وتسريعها وتجهيزها لدورة النشر التالية.
                    </p>

                    <div class="stack">
                        <div class="stack-card">
                            <strong>ما الذي يحدث الآن</strong>
                            <span>نقوم بنشر تحديثات التطبيق وتجديد الكاش وتشغيل فحوصات الإصدار.</span>
                        </div>
                        <div class="stack-card">
                            <strong>ما الذي يبقى محفوظاً</strong>
                            <span>لن يتم حذف الحسابات أو المستأجرين أو العقارات أو السجلات التشغيلية.</span>
                        </div>
                        <div class="stack-card">
                            <strong>الخطوة التالية</strong>
                            <span>أعد تحميل الصفحة بعد بضع دقائق وسيعود النظام للعمل بشكل طبيعي.</span>
                        </div>
                    </div>

                    <div class="footer-note">
                        إذا كنت مسؤول نشر معتمد، يبقى رابط التجاوز السري متاحاً لاختبارات الإصدار المعتمدة.
                    </div>
                </article>
            </aside>
        </div>
    </main>

    <script>
        (function () {
            const available = ['en', 'ar'];
            const params = new URLSearchParams(window.location.search);
            const requested = (params.get('lang') || '').toLowerCase();
            const browser = (navigator.language || 'en').toLowerCase().startsWith('ar') ? 'ar' : 'en';
            const initial = available.includes(requested) ? requested : browser;

            const buttons = document.querySelectorAll('[data-switch]');
            const panes = document.querySelectorAll('.pane');

            function applyLanguage(lang) {
                document.documentElement.lang = lang;
                document.documentElement.dir = lang === 'ar' ? 'rtl' : 'ltr';
                document.body.setAttribute('dir', lang === 'ar' ? 'rtl' : 'ltr');
                document.title = lang === 'ar'
                    ? 'عقاري سمارت | تحت الصيانة'
                    : 'Aqari Smart | Maintenance';

                panes.forEach((pane) => {
                    pane.classList.toggle('is-active', pane.getAttribute('data-lang') === lang);
                });

                buttons.forEach((button) => {
                    button.classList.toggle('is-active', button.getAttribute('data-switch') === lang);
                });
            }

            buttons.forEach((button) => {
                button.addEventListener('click', function () {
                    const lang = this.getAttribute('data-switch');
                    const url = new URL(window.location.href);
                    url.searchParams.set('lang', lang);
                    window.history.replaceState({}, '', url.toString());
                    applyLanguage(lang);
                });
            });

            applyLanguage(initial);
        })();
    </script>
</body>
</html>
