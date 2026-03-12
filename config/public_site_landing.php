<?php

return [
    'meta' => [
        'title' => [
            'en' => 'Aqari Smart — Professional Property Management',
            'ar' => 'Aqari Smart — إدارة العقارات الاحترافية',
        ],
    ],
    'theme' => [
        'brand' => '#0f172a',
        'secondary' => '#6366f1',
        'accent' => '#818cf8',
        'background' => '#ffffff',
    ],
    'assets' => [
        'logo_url' => null,
        'hero_image' => null,
        'auth_screenshot' => null,
        'feature_images' => [
            'gallery' => [],
        ],
        'logo_strip' => [],
    ],
    'video' => [
        'headline' => [
            'en' => 'See Aqari Smart in action',
            'ar' => 'شاهد Aqari Smart أثناء العمل',
        ],
        'description' => [
            'en' => 'Watch how property teams use Aqari Smart to streamline their daily operations.',
            'ar' => 'شاهد كيف تستخدم الفرق العقارية Aqari Smart لتبسيط عملياتها اليومية.',
        ],
        'youtube_url' => null,
        'poster_image' => null,
    ],
    'navigation' => [
        ['label' => ['en' => 'Features', 'ar' => 'المزايا'], 'href' => '#features'],
        ['label' => ['en' => 'Pricing', 'ar' => 'الأسعار'], 'href' => '#pricing'],
        ['label' => ['en' => 'Testimonials', 'ar' => 'الشهادات'], 'href' => '#testimonials'],
        ['label' => ['en' => 'Get Started', 'ar' => 'ابدأ الآن'], 'href' => '#cta', 'variant' => 'button-primary'],
    ],
    'hero' => [
        'headline' => [
            'en' => 'The smartest way to manage properties',
            'ar' => 'الطريقة الأذكى لإدارة العقارات',
        ],
        'subheadline' => [
            'en' => 'A complete property management platform — portfolios, leases, maintenance, billing, and resident portals — all from one unified dashboard.',
            'ar' => 'منصة متكاملة لإدارة العقارات — المحافظ والعقود والصيانة والفوترة وبوابات السكان — من لوحة تحكم واحدة.',
        ],
        'ctas' => [
            ['label' => ['en' => 'Start Free Trial', 'ar' => 'ابدأ التجربة المجانية'], 'href' => '#cta', 'style' => 'primary'],
            ['label' => ['en' => 'Explore Pricing', 'ar' => 'تعرّف على الأسعار'], 'href' => '#pricing', 'style' => 'outline'],
        ],
    ],
    'features' => [
        'intro' => [
            'headline' => [
                'en' => 'Everything you need to run your properties',
                'ar' => 'كل ما تحتاجه لإدارة عقاراتك',
            ],
            'description' => [
                'en' => 'Centralize properties, leases, billing, and maintenance workflows — so your team can focus on what matters.',
                'ar' => 'وحّد إدارة العقارات والعقود والفوترة والصيانة — ليتفرغ فريقك لما يهم.',
            ],
            'items' => [
                [
                    'icon' => 'layers',
                    'title' => ['en' => 'Multi-Property Portfolio', 'ar' => 'محفظة عقارات متعددة'],
                    'body' => ['en' => 'Manage unlimited properties and units with isolated subdomains and dedicated branding.', 'ar' => 'أدِر عقارات ووحدات غير محدودة مع نطاقات فرعية معزولة وهوية خاصة.'],
                ],
                [
                    'icon' => 'shield-check',
                    'title' => ['en' => 'Role-Based Access', 'ar' => 'صلاحيات مخصصة'],
                    'body' => ['en' => 'Granular permissions for owners, staff, agents, and residents keep data protected.', 'ar' => 'صلاحيات دقيقة للمالكين والموظفين والوكلاء والسكان تحافظ على سرية البيانات.'],
                ],
                [
                    'icon' => 'credit-card',
                    'title' => ['en' => 'Integrated Billing', 'ar' => 'فوترة متكاملة'],
                    'body' => ['en' => 'Subscriptions, invoices, and rent collection through a unified billing hub.', 'ar' => 'إدارة الاشتراكات والفواتير وتحصيل الإيجارات من مركز فوترة موحد.'],
                ],
                [
                    'icon' => 'wrench',
                    'title' => ['en' => 'Maintenance Tracking', 'ar' => 'تتبع الصيانة'],
                    'body' => ['en' => 'Work orders, vendor assignment, and status tracking from request to resolution.', 'ar' => 'أوامر العمل وتعيين المقاولين وتتبع الحالة من الطلب حتى الحل.'],
                ],
                [
                    'icon' => 'chart-bar',
                    'title' => ['en' => 'Financial Reports', 'ar' => 'تقارير مالية'],
                    'body' => ['en' => 'Revenue, occupancy, commissions, and expense reports at a glance.', 'ar' => 'تقارير الإيرادات والإشغال والعمولات والمصروفات بنظرة واحدة.'],
                ],
                [
                    'icon' => 'globe',
                    'title' => ['en' => 'Bilingual Support', 'ar' => 'دعم ثنائي اللغة'],
                    'body' => ['en' => 'Full Arabic and English support throughout the entire platform.', 'ar' => 'دعم كامل للعربية والإنجليزية في جميع أنحاء المنصة.'],
                ],
            ],
        ],
        'columns' => [
            [
                'title' => ['en' => 'Resident Experience', 'ar' => 'تجربة السكان'],
                'items' => [
                    ['title' => ['en' => 'Resident Portal', 'ar' => 'بوابة السكان'], 'body' => ['en' => 'Self-serve access to leases, payments, and maintenance requests.', 'ar' => 'وصول ذاتي للعقود والمدفوعات وطلبات الصيانة.']],
                    ['title' => ['en' => 'Lead Capture', 'ar' => 'التقاط العملاء المحتملين'], 'body' => ['en' => 'Capture and route inquiries to the right property team instantly.', 'ar' => 'التقط الاستفسارات ووجّهها للفريق المناسب فورًا.']],
                    ['title' => ['en' => 'Viewing Scheduling', 'ar' => 'جدولة المعاينات'], 'body' => ['en' => 'Let prospects book property viewings online with automatic confirmations.', 'ar' => 'اسمح للعملاء بحجز معاينات العقارات عبر الإنترنت مع تأكيدات تلقائية.']],
                ],
            ],
            [
                'title' => ['en' => 'Team Dashboard', 'ar' => 'لوحة فريق العمل'],
                'items' => [
                    ['title' => ['en' => 'Operational Metrics', 'ar' => 'مؤشرات تشغيلية'], 'body' => ['en' => 'Real-time dashboards across units, leases, maintenance, and billing.', 'ar' => 'لوحات معلومات فورية للوحدات والعقود والصيانة والفوترة.']],
                    ['title' => ['en' => 'Agent Commissions', 'ar' => 'عمولات الوكلاء'], 'body' => ['en' => 'Track and manage agent commissions with transparent reporting.', 'ar' => 'تتبع وإدارة عمولات الوكلاء مع تقارير شفافة.']],
                    ['title' => ['en' => 'Team Permissions', 'ar' => 'صلاحيات الفريق'], 'body' => ['en' => 'Fine-grained access control for every team member and role.', 'ar' => 'تحكم دقيق بالوصول لكل عضو في الفريق وكل دور.']],
                ],
            ],
        ],
        'screenshots' => [
            'headline' => ['en' => 'See Aqari Smart in action', 'ar' => 'شاهد Aqari Smart أثناء العمل'],
            'description' => ['en' => 'Purpose-built dashboards keep everyone aligned — from headquarters to on-site teams to residents.', 'ar' => 'لوحات مخصصة تُبقي الجميع على اطلاع — من المقر إلى الفرق الميدانية والسكان.'],
        ],
    ],
    'pricing' => [
        'headline' => ['en' => 'Simple, transparent pricing', 'ar' => 'أسعار بسيطة وشفافة'],
        'subheadline' => ['en' => 'Start small, grow confidently. No hidden fees.', 'ar' => 'ابدأ صغيراً وتوسّع بثقة. بدون رسوم مخفية.'],
        'plans' => [
            [
                'name' => ['en' => 'Starter', 'ar' => 'الأساسية'],
                'price' => '$49',
                'unit' => '/month',
                'features' => [
                    ['en' => 'Up to 50 units', 'ar' => 'حتى 50 وحدة'],
                    ['en' => 'Core modules included', 'ar' => 'الوحدات الأساسية متاحة'],
                    ['en' => 'Email support', 'ar' => 'دعم عبر البريد الإلكتروني'],
                    ['en' => 'Bilingual interface', 'ar' => 'واجهة ثنائية اللغة'],
                ],
                'cta' => ['label' => ['en' => 'Get Started', 'ar' => 'ابدأ الآن'], 'href' => '#cta'],
            ],
            [
                'name' => ['en' => 'Professional', 'ar' => 'الاحترافية'],
                'price' => '$129',
                'unit' => '/month',
                'highlighted' => true,
                'features' => [
                    ['en' => 'Up to 200 units', 'ar' => 'حتى 200 وحدة'],
                    ['en' => 'All modules included', 'ar' => 'جميع الوحدات متاحة'],
                    ['en' => 'Billing & analytics', 'ar' => 'فوترة وتحليلات'],
                    ['en' => 'Priority support', 'ar' => 'دعم أولوية'],
                    ['en' => 'Agent commissions', 'ar' => 'عمولات الوكلاء'],
                ],
                'cta' => ['label' => ['en' => 'Start Free Trial', 'ar' => 'ابدأ التجربة المجانية'], 'href' => '#cta'],
            ],
            [
                'name' => ['en' => 'Enterprise', 'ar' => 'الشركات'],
                'price' => 'Custom',
                'unit' => null,
                'features' => [
                    ['en' => 'Unlimited units', 'ar' => 'وحدات غير محدودة'],
                    ['en' => 'Dedicated success manager', 'ar' => 'مدير نجاح مخصص'],
                    ['en' => 'Advanced integrations', 'ar' => 'تكاملات متقدمة'],
                    ['en' => 'Custom SLA', 'ar' => 'اتفاقية مستوى خدمة مخصصة'],
                ],
                'cta' => ['label' => ['en' => 'Talk to Sales', 'ar' => 'تواصل مع المبيعات'], 'href' => '#cta'],
            ],
        ],
    ],
    'testimonials' => [
        'headline' => ['en' => 'Trusted by property teams across the region', 'ar' => 'موثوق من فرق عقارية في المنطقة'],
        'items' => [
            [
                'quote' => ['en' => 'We launched our resident portal in under a week and cut response times in half. Aqari Smart transformed our operations.', 'ar' => 'أطلقنا بوابة السكان خلال أسبوع وخفّضنا مدة الاستجابة إلى النصف. Aqari Smart غيّر عملياتنا.'],
                'author' => ['en' => 'Salma Haddad, VP Operations at Northwind Rentals', 'ar' => 'سلمى حداد، نائب الرئيس للعمليات في نورثويند رنتلز'],
            ],
            [
                'quote' => ['en' => 'Aqari Smart gave us the control we needed across regions without hiring more staff. The bilingual support is a game changer.', 'ar' => 'منحنا Aqari Smart التحكم المطلوب عبر المناطق دون توظيف المزيد. الدعم ثنائي اللغة نقلة نوعية.'],
                'author' => ['en' => 'Rami Qassem, COO at Cedar Living', 'ar' => 'رامي قاسم، مدير العمليات في سيدار ليفينج'],
            ],
        ],
    ],
    'cta' => [
        'primary' => [
            'label' => ['en' => 'Start Your Free Trial', 'ar' => 'ابدأ تجربتك المجانية'],
            'href' => '#register',
        ],
        'secondary' => [
            'label' => ['en' => 'Book a Demo', 'ar' => 'احجز عرضاً توضيحياً'],
            'href' => '/book-call',
        ],
    ],
    'footer' => [
        'copyright' => [
            'en' => '© :year Aqari Smart. All rights reserved.',
            'ar' => '© :year Aqari Smart. جميع الحقوق محفوظة.',
        ],
        'links' => [
            ['label' => ['en' => 'Privacy', 'ar' => 'الخصوصية'], 'href' => '#privacy'],
            ['label' => ['en' => 'Terms', 'ar' => 'الشروط'], 'href' => '#terms'],
            ['label' => ['en' => 'Contact', 'ar' => 'اتصل بنا'], 'href' => '#cta'],
        ],
    ],
    'seo' => [
        'title' => [
            'en' => 'Aqari Smart — Professional Property Management Platform',
            'ar' => 'Aqari Smart — منصة إدارة العقارات الاحترافية',
        ],
        'description' => [
            'en' => 'Manage your entire property portfolio from one platform. Resident portals, maintenance, billing, dashboards, and more — all with Aqari Smart.',
            'ar' => 'أدِر محفظتك العقارية بالكامل من منصة واحدة: بوابات السكان، الصيانة، الفوترة، ولوحات التحكم — مع Aqari Smart.',
        ],
        'robots' => 'index, follow',
        'og_image' => null,
        'twitter_image' => null,
        'favicon' => null,
    ],
];
