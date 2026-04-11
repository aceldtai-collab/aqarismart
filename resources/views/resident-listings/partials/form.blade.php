@php
    $isAr = app()->getLocale() === 'ar';
    $listing = $residentListing ?? null;
    $photos = array_values($listing?->photos ?? []);
    $wizard = (bool) ($wizard ?? false);
@endphp

<div class="rl-grid">
    <div class="rl-card" @if($wizard) data-rl-step-panel="1" @endif>
        <div class="rl-section-kicker">{{ $isAr ? 'الهوية الأساسية' : 'Core details' }}</div>
        <h2 class="rl-section-title">{{ $isAr ? 'عرّف العقار بوضوح' : 'Define the property clearly' }}</h2>
        <p class="rl-section-text">{{ $isAr ? 'ابدأ بعنوان واضح ووصف صادق ونوع صحيح للعقار حتى يظهر إعلانك بشكل مهني.' : 'Start with a clear title, honest description, and the right property type so the listing reads professionally.' }}</p>

        <div class="rl-grid" style="margin-top:1.25rem;grid-template-columns:repeat(2,minmax(0,1fr))">
            <div style="grid-column:1/-1">
                <label class="rl-label">{{ $isAr ? 'نوع العقار' : 'Property type' }}</label>
                <select name="subcategory_id" class="rl-select" required>
                    <option value="">{{ $isAr ? 'اختر نوع العقار' : 'Select property type' }}</option>
                    @foreach($subcategories as $subcategory)
                        <option value="{{ $subcategory->id }}" @selected(old('subcategory_id', $listing?->subcategory_id) == $subcategory->id)>{{ $subcategory->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="rl-label">{{ $isAr ? 'نوع الإعلان' : 'Listing type' }}</label>
                <select name="listing_type" class="rl-select" required>
                    <option value="sale" @selected(old('listing_type', $listing?->listing_type ?? 'sale') === 'sale')>{{ $isAr ? 'للبيع' : 'For Sale' }}</option>
                    <option value="rent" @selected(old('listing_type', $listing?->listing_type) === 'rent')>{{ $isAr ? 'للإيجار' : 'For Rent' }}</option>
                </select>
            </div>
            <div>
                <label class="rl-label">{{ $isAr ? 'السعر' : 'Price' }}</label>
                <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $listing?->price) }}" class="rl-input" required>
                <div class="rl-help">{{ $isAr ? 'السعر المعتمد لهذا المسار هو بالدينار العراقي IQD.' : 'This flow publishes the price in Iraqi dinar (IQD).' }}</div>
            </div>
            <div>
                <label class="rl-label">{{ $isAr ? 'العنوان بالإنجليزية' : 'Title in English' }}</label>
                <input type="text" name="title[en]" value="{{ old('title.en', $listing?->title['en'] ?? '') }}" class="rl-input" required>
            </div>
            <div>
                <label class="rl-label">{{ $isAr ? 'العنوان بالعربية' : 'Title in Arabic' }}</label>
                <input type="text" name="title[ar]" value="{{ old('title.ar', $listing?->title['ar'] ?? '') }}" class="rl-input">
            </div>
            <div>
                <label class="rl-label">{{ $isAr ? 'الوصف بالإنجليزية' : 'Description in English' }}</label>
                <textarea name="description[en]" rows="5" class="rl-textarea" required>{{ old('description.en', $listing?->description['en'] ?? '') }}</textarea>
            </div>
            <div>
                <label class="rl-label">{{ $isAr ? 'الوصف بالعربية' : 'Description in Arabic' }}</label>
                <textarea name="description[ar]" rows="5" class="rl-textarea">{{ old('description.ar', $listing?->description['ar'] ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <div class="rl-card" @if($wizard) data-rl-step-panel="1" @endif>
        <div class="rl-section-kicker">{{ $isAr ? 'الموقع والمواصفات' : 'Location and specs' }}</div>
        <h2 class="rl-section-title">{{ $isAr ? 'أين يقع وما الذي يقدمه؟' : 'Where is it and what does it offer?' }}</h2>
        <div class="rl-grid" style="margin-top:1.25rem;grid-template-columns:repeat(3,minmax(0,1fr))">
            <div>
                <label class="rl-label">{{ $isAr ? 'المدينة' : 'City' }}</label>
                <select name="city_id" class="rl-select" required>
                    <option value="">{{ $isAr ? 'اختر المدينة' : 'Select city' }}</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}" @selected(old('city_id', $listing?->city_id) == $city->id)>{{ $isAr ? ($city->name_ar ?: $city->name_en) : $city->name_en }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="rl-label">{{ $isAr ? 'المنطقة / المحافظة' : 'Area / governorate' }}</label>
                <select name="area_id" class="rl-select">
                    <option value="">{{ $isAr ? 'اختياري' : 'Optional' }}</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}" @selected(old('area_id', $listing?->area_id) == $area->id)>{{ $isAr ? ($area->name_ar ?: $area->name_en) : $area->name_en }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="rl-label">{{ $isAr ? 'غرف النوم' : 'Bedrooms' }}</label>
                <input type="number" min="0" name="bedrooms" value="{{ old('bedrooms', $listing?->bedrooms ?? 0) }}" class="rl-input" required>
            </div>
            <div>
                <label class="rl-label">{{ $isAr ? 'الحمامات' : 'Bathrooms' }}</label>
                <input type="number" min="0" step="0.5" name="bathrooms" value="{{ old('bathrooms', $listing?->bathrooms ?? 1) }}" class="rl-input" required>
            </div>
            <div>
                <label class="rl-label">{{ $isAr ? 'المساحة م²' : 'Area m²' }}</label>
                <input type="number" min="0" step="0.01" name="area_m2" value="{{ old('area_m2', $listing?->area_m2) }}" class="rl-input">
            </div>
            <div style="grid-column:1/-1">
                <label class="rl-label">{{ $isAr ? 'وصف الموقع' : 'Location description' }}</label>
                <input type="text" name="location" value="{{ old('location', $listing?->location) }}" class="rl-input" placeholder="{{ $isAr ? 'مثال: الكرادة قرب شارع 62' : 'Example: Karrada near 62 Street' }}">
            </div>
            <div style="grid-column:1/-1">
                <label class="rl-label">{{ $isAr ? 'رابط الخريطة' : 'Map URL' }}</label>
                <input type="url" name="location_url" value="{{ old('location_url', $listing?->location_url) }}" class="rl-input" placeholder="https://maps.google.com/...">
            </div>
        </div>
    </div>

    <div class="rl-card" @if($wizard) data-rl-step-panel="2" style="display:none" @endif>
        <div class="rl-section-kicker">{{ $isAr ? 'الصور' : 'Photos' }}</div>
        <h2 class="rl-section-title">{{ $isAr ? 'أضف صوراً واضحة وصادقة' : 'Add clear and honest photos' }}</h2>
        <p class="rl-section-text">{{ $isAr ? 'يمكنك رفع حتى 10 صور. الصورة الأولى ستظهر كغلاف للإعلان.' : 'You can upload up to 10 photos. The first photo becomes the listing cover.' }}</p>

        @if($listing && count($photos))
            <div class="rl-gallery-grid" style="margin-top:1.25rem">
                @foreach($photos as $photo)
                    <label class="rl-photo-card">
                        <img src="{{ $photo }}" alt="Photo">
                        <input type="checkbox" name="existing_photos[]" value="{{ $photo }}" checked hidden>
                        <button type="button" class="rl-photo-remove" onclick="const checkbox=this.parentElement.querySelector('input'); checkbox.checked=!checkbox.checked; this.parentElement.style.opacity=checkbox.checked ? '1' : '.35'; this.textContent=checkbox.checked ? '×' : '+';">×</button>
                    </label>
                @endforeach
            </div>
            <div class="rl-help">{{ $isAr ? 'اضغط × لإزالة صورة قديمة من الإعلان قبل الحفظ.' : 'Click × on an existing image to remove it from the listing before saving.' }}</div>
        @endif

        <div class="rl-upload-panel" style="margin-top:1.25rem">
            <label class="rl-label">{{ $isAr ? 'ارفع صوراً جديدة' : 'Upload new photos' }}</label>
            <input type="file" name="photos[]" accept="image/*" multiple class="rl-input" data-rl-photo-input>
            <div class="rl-help">{{ $isAr ? 'يمكنك اختيار عدة صور دفعة واحدة. ستظهر معاينة مباشرة قبل الإرسال.' : 'You can choose multiple images at once. A live preview will appear before submission.' }}</div>
            <div class="rl-gallery-grid" data-rl-photo-preview style="margin-top:1rem"></div>
        </div>
    </div>

    <div class="rl-card" @if($wizard) data-rl-step-panel="3" style="display:none" @endif>
        <div class="rl-section-kicker">{{ $isAr ? 'المدة والنشر' : 'Duration and publish' }}</div>
        <h2 class="rl-section-title">{{ $isAr ? 'اختر مدة الإعلان ثم انشره فوراً' : 'Choose the duration and publish it right away' }}</h2>
        <p class="rl-section-text">{{ $isAr ? 'في هذا المسار يتم تفعيل الإعلان تلقائياً بعد الإرسال حتى يظهر في السوق مباشرة.' : 'In this flow the listing is activated automatically after submit so it appears in the marketplace right away.' }}</p>

        <div class="rl-grid" style="margin-top:1.25rem;grid-template-columns:repeat(2,minmax(0,1fr))">
            <div>
                <label class="rl-label">{{ $isAr ? 'مدة الإعلان' : 'Ad duration' }}</label>
                <select name="ad_duration_id" class="rl-select" required data-rl-duration-select>
                    <option value="">{{ $isAr ? 'اختر المدة' : 'Select duration' }}</option>
                    @foreach($adDurations as $duration)
                        <option value="{{ $duration->id }}"
                                data-days="{{ $duration->days }}"
                                data-price="{{ (float) $duration->price }}"
                                data-currency="{{ $duration->currency }}"
                                @selected(old('ad_duration_id', $listing?->ad_duration_id) == $duration->id)>
                            {{ $isAr ? ($duration->name_ar ?: $duration->name_en) : $duration->name_en }} · {{ $duration->days }} {{ $isAr ? 'يوم' : 'days' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="rl-label">{{ $isAr ? 'طريقة الدفع' : 'Payment method' }}</label>
                <div class="rl-grid">
                    @php
                        $selectedPaymentMethod = old('payment_method', $listing?->payment_method);
                        $paymentMethods = [
                            'card' => [$isAr ? 'بطاقة إلكترونية' : 'Online card', $isAr ? 'الدفع عبر البطاقة داخل بوابة الدفع.' : 'Pay with card through the payment gateway.'],
                            'bank_transfer' => [$isAr ? 'تحويل بنكي' : 'Bank transfer', $isAr ? 'أرسل التحويل وسيتم التحقق منه قبل التفعيل.' : 'Send a transfer and it will be verified before activation.'],
                            'cash' => [$isAr ? 'دفع نقدي / مكتب' : 'Cash / office payment', $isAr ? 'ادفع في المكتب وسيتم تفعيل الإعلان بعد التأكيد.' : 'Pay in person and the listing will be activated after confirmation.'],
                        ];
                    @endphp
                    @foreach($paymentMethods as $method => [$label, $desc])
                        <label class="rl-radio-card">
                            <input type="radio" name="payment_method" value="{{ $method }}" @checked($selectedPaymentMethod === $method)>
                            <div>
                                <div style="font-weight:900">{{ $label }}</div>
                                <div class="rl-help" style="margin-top:.15rem">{{ $desc }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
            <div>
                <label class="rl-label">{{ $isAr ? 'مرجع الدفع' : 'Payment reference' }}</label>
                <input type="text" name="payment_reference" value="{{ old('payment_reference', $listing?->payment_reference) }}" class="rl-input" placeholder="{{ $isAr ? 'اختياري، مثال: TRX-12345' : 'Optional, e.g. TRX-12345' }}">
            </div>
            <div class="rl-upload-panel" style="align-self:start">
                <div class="rl-label">{{ $isAr ? 'ملخص الطلب' : 'Checkout summary' }}</div>
                <div class="rl-summary-row"><span>{{ $isAr ? 'مدة الإعلان' : 'Duration' }}</span><strong data-rl-summary-days>{{ $isAr ? 'غير محدد' : 'Not selected' }}</strong></div>
                <div class="rl-summary-row"><span>{{ $isAr ? 'قيمة الخدمة' : 'Service price' }}</span><strong data-rl-summary-price>{{ $isAr ? 'غير محدد' : 'Not selected' }}</strong></div>
                <div class="rl-summary-row"><span>{{ $isAr ? 'الحالة بعد الإرسال' : 'Status after submit' }}</span><strong>{{ $isAr ? 'نشط بعد الإرسال' : 'Active after submit' }}</strong></div>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="rl-card" style="border-color:rgba(185,28,28,.2);background:#fff1f2">
            <div class="rl-section-title" style="font-size:1.1rem">{{ $isAr ? 'يوجد خطأ في البيانات' : 'There are validation issues' }}</div>
            <ul style="margin:1rem 0 0;padding-inline-start:1.2rem;color:#991b1b;line-height:1.8">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($wizard)
        <div style="display:flex;gap:.85rem;flex-wrap:wrap" data-rl-step-panel="1">
            <button type="button" class="rl-button rl-button-primary" data-rl-next>{{ $isAr ? 'التالي: الصور' : 'Next: Photos' }}</button>
            <a href="{{ $cancelUrl }}" class="rl-button rl-button-secondary">{{ $isAr ? 'إلغاء' : 'Cancel' }}</a>
        </div>
        <div style="display:flex;gap:.85rem;flex-wrap:wrap;display:none" data-rl-step-panel="2">
            <button type="button" class="rl-button rl-button-secondary" data-rl-prev>{{ $isAr ? 'رجوع' : 'Back' }}</button>
            <button type="button" class="rl-button rl-button-primary" data-rl-next>{{ $isAr ? 'التالي: المدة والدفع' : 'Next: Duration & Checkout' }}</button>
        </div>
        <div style="display:flex;gap:.85rem;flex-wrap:wrap;display:none" data-rl-step-panel="3">
            <button type="button" class="rl-button rl-button-secondary" data-rl-prev>{{ $isAr ? 'رجوع' : 'Back' }}</button>
            <button type="submit" class="rl-button rl-button-primary">{{ $submitLabel }}</button>
        </div>
    @else
        <div style="display:flex;gap:.85rem;flex-wrap:wrap">
            <button type="submit" class="rl-button rl-button-primary">{{ $submitLabel }}</button>
            <a href="{{ $cancelUrl }}" class="rl-button rl-button-secondary">{{ $isAr ? 'إلغاء' : 'Cancel' }}</a>
        </div>
    @endif
</div>

<script>
(() => {
    const form = document.currentScript.closest('form');
    if (!form) return;
    const input = form.querySelector('[data-rl-photo-input]');
    const preview = form.querySelector('[data-rl-photo-preview]');
    if (!input || !preview) return;

    const renderPreviews = () => {
        preview.innerHTML = '';
        Array.from(input.files || []).forEach((file, index) => {
            const card = document.createElement('div');
            card.className = 'rl-photo-card';
            const img = document.createElement('img');
            img.alt = file.name;
            img.src = URL.createObjectURL(file);
            img.onload = () => URL.revokeObjectURL(img.src);
            card.appendChild(img);

            const badge = document.createElement('div');
            badge.className = 'rl-photo-remove';
            badge.style.left = '.55rem';
            badge.style.right = 'auto';
            badge.textContent = `${index + 1}`;
            card.appendChild(badge);
            preview.appendChild(card);
        });
    };

    input.addEventListener('change', renderPreviews);
    renderPreviews();

    const durationSelect = form.querySelector('[data-rl-duration-select]');
    const daysSummary = form.querySelector('[data-rl-summary-days]');
    const priceSummary = form.querySelector('[data-rl-summary-price]');
    const updateSummary = () => {
        if (!durationSelect || !daysSummary || !priceSummary) return;
        const selected = durationSelect.options[durationSelect.selectedIndex];
        if (!selected || !selected.value) {
            daysSummary.textContent = @json($isAr ? 'غير محدد' : 'Not selected');
            priceSummary.textContent = @json($isAr ? 'غير محدد' : 'Not selected');
            return;
        }
        const days = selected.dataset.days || '';
        const price = selected.dataset.price || '';
        const currency = selected.dataset.currency || 'IQD';
        daysSummary.textContent = `${days} ${@json($isAr ? 'يوم' : 'days')}`;
        priceSummary.textContent = `${new Intl.NumberFormat('en-IQ').format(Number(price || 0))} ${currency}`;
    };
    durationSelect?.addEventListener('change', updateSummary);
    updateSummary();

    const wizardEnabled = @json($wizard);
    if (!wizardEnabled) return;

    const stepPanels = Array.from(form.querySelectorAll('[data-rl-step-panel]'));
    const stepControls = Array.from(form.querySelectorAll('button[data-rl-next], button[data-rl-prev]'));
    let currentStep = 1;

    const setStep = (step) => {
        currentStep = Math.max(1, Math.min(3, step));
        stepPanels.forEach((panel) => {
            panel.style.display = String(panel.dataset.rlStepPanel) === String(currentStep) ? '' : 'none';
        });
        document.querySelectorAll('[data-rl-step-pill]').forEach((pill) => {
            pill.classList.toggle('is-active', pill.dataset.rlStepPill === String(currentStep));
        });
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    const validateStep = (step) => {
        const requiredSelectors = step === 1
            ? ['select[name="subcategory_id"]','select[name="listing_type"]','input[name="price"]','input[name="title[en]"]','textarea[name="description[en]"]','select[name="city_id"]','input[name="bedrooms"]','input[name="bathrooms"]']
            : step === 3
                ? ['select[name="ad_duration_id"]']
                : [];

        for (const selector of requiredSelectors) {
            const nodes = form.querySelectorAll(selector);
            if (!nodes.length) continue;
            const field = nodes[0];
            if (!field.value) {
                field.focus();
                alert(@json($isAr ? 'أكمل الحقول المطلوبة قبل المتابعة.' : 'Complete the required fields before continuing.'));
                return false;
            }
        }
        return true;
    };

    stepControls.forEach((control) => {
        control.addEventListener('click', () => {
            if (control.hasAttribute('data-rl-next')) {
                if (!validateStep(currentStep)) return;
                setStep(currentStep + 1);
            } else {
                setStep(currentStep - 1);
            }
        });
    });

    setStep({{ $errors->any() ? '3' : '1' }});
})();
</script>

