<?php

namespace App\Livewire\Admin;

use App\Models\SystemSetting;
use App\Services\PublicLandingService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
class LandingEditor extends Component
{
    use WithFileUploads;

    public string $previewMode = 'desktop';

    public array $form = [];

    public array $defaults = [];

    public array $locales = [];

    public string $activeLocale = 'en';

    protected string $baseLocale = 'en';

    public string $defaultsJson = '{}';

    public $logoUpload;

    public $heroUpload;

    public array $galleryUploads = [];

    public function mount(): void
    {
        $this->locales = config('app.supported_locales', [
            'en' => 'English',
            'ar' => 'العربية',
        ]);

        $this->baseLocale = array_key_first($this->locales) ?? 'en';
        $this->activeLocale = $this->baseLocale;

        $this->defaults = config('public_site_landing') ?? [];
        $stored = SystemSetting::getValue('landing', []);

        $this->form = $this->merge(
            $this->defaults,
            is_array($stored) ? $stored : []
        );

        $this->ensureCollections();

        $this->defaultsJson = $this->encodeJson($this->defaults);
    }

    public function save(): void
    {
        $landing = app(PublicLandingService::class);
        $this->ensureCollections();
        $this->validate($this->rules(), [], $this->validationAttributes());

        $this->handleUploads();

        SystemSetting::setValue('landing', $this->form);
        $landing->clearCache();

        session()->flash('status', __('Landing page content updated.'));
        $this->dispatch('notify', message: __('Landing page content updated.'), type: 'success');
    }

    public function saveSection(string $section): void
    {
        $landing = app(PublicLandingService::class);
        $this->ensureCollections();

        // Validate a subset of rules based on section
        $rules = $this->sectionRules($section) ?: $this->rules();
        $this->validate($rules, [], $this->validationAttributes());

        // Only process uploads for assets section to avoid unnecessary work
        if ($section === 'assets') {
            $this->handleUploads();
        }

        SystemSetting::setValue('landing', $this->form);
        $landing->clearCache();

        session()->flash('status', __('Section ":section" saved.', ['section' => $section]));
        // Notify the frontend (Alpine) to show a quick inline badge for this section
        $this->dispatch('section-saved', section: $section);
        // Also raise a toast notification
        $this->dispatch('notify', message: __('Section ":section" saved.', ['section' => $section]), type: 'success');
    }

    protected function sectionRules(string $section): array
    {
        $base = $this->baseLocale;
        return match ($section) {
            'assets' => [
                'form.assets.logo_url' => ['nullable','string','max:1024'],
                'form.assets.hero_image' => ['nullable','string','max:1024'],
                'form.assets.feature_images.gallery' => ['array'],
                'form.assets.feature_images.gallery.*' => ['string','max:1024'],
                'logoUpload' => ['nullable','image','max:2048'],
                'heroUpload' => ['nullable','image','max:4096'],
                'galleryUploads' => ['nullable','array','max:6'],
                'galleryUploads.*' => ['image','max:4096'],
            ],
            'meta' => [
                "form.meta.title.$base" => ['required','string','max:120'],
            ],
            'hero' => [
                "form.hero.headline.$base" => ['required','string','max:160'],
                "form.hero.subheadline.$base" => ['required','string','max:260'],
                'form.hero.ctas' => ['array','min:1'],
                "form.hero.ctas.*.label.$base" => ['required','string','max:60'],
                'form.hero.ctas.*.href' => ['required','string','max:255'],
                'form.hero.ctas.*.style' => ['nullable','in:primary,outline,text'],
            ],
            'navigation' => [
                'form.navigation' => ['array','min:1'],
                "form.navigation.*.label.$base" => ['required','string','max:60'],
                'form.navigation.*.href' => ['required','string','max:255'],
                'form.navigation.*.variant' => ['nullable','string','max:40'],
            ],
            'features' => [
                "form.features.intro.headline.$base" => ['required','string','max:160'],
                "form.features.intro.description.$base" => ['required','string','max:260'],
                'form.features.intro.items' => ['array','min:1'],
                'form.features.intro.items.*.icon' => ['nullable','string','max:40'],
                "form.features.intro.items.*.title.$base" => ['required','string','max:80'],
                "form.features.intro.items.*.body.$base" => ['required','string','max:220'],
                'form.features.columns' => ['array','min:1'],
                "form.features.columns.*.title.$base" => ['required','string','max:80'],
                'form.features.columns.*.items' => ['array','min:1'],
                "form.features.columns.*.items.*.title.$base" => ['required','string','max:80'],
                "form.features.columns.*.items.*.body.$base" => ['required','string','max:220'],
            ],
            'pricing' => [
                'form.pricing.plans' => ['array','min:1'],
                "form.pricing.plans.*.name.$base" => ['required','string','max:60'],
                'form.pricing.plans.*.price' => ['nullable','string','max:40'],
                'form.pricing.plans.*.unit' => ['nullable','string','max:40'],
                "form.pricing.plans.*.features.*.$base" => ['required','string','max:120'],
                "form.pricing.plans.*.cta.label.$base" => ['nullable','string','max:60'],
                'form.pricing.plans.*.cta.href' => ['required','string','max:255'],
                'form.pricing.plans.*.highlighted' => ['boolean'],
            ],
            'testimonials' => [
                "form.testimonials.headline.$base" => ['nullable','string','max:160'],
                'form.testimonials.items' => ['array','min:1'],
                "form.testimonials.items.*.quote.$base" => ['required','string','max:260'],
                "form.testimonials.items.*.author.$base" => ['required','string','max:160'],
            ],
            'cta' => [
                "form.cta.primary.label.$base" => ['required','string','max:60'],
                'form.cta.primary.href' => ['required','string','max:255'],
                "form.cta.secondary.label.$base" => ['nullable','string','max:60'],
                'form.cta.secondary.href' => ['nullable','string','max:255'],
            ],
            default => [],
        };
    }

    public function switchLocale(string $locale): void
    {
        if (array_key_exists($locale, $this->locales)) {
            $this->activeLocale = $locale;
        }
    }

    public function addNavigationLink(): void
    {
        $this->form['navigation'][] = [
            'label' => $this->blankTranslations('New Link'),
            'href' => '#',
            'variant' => null,
        ];
    }

    public function removeNavigationLink(int $index): void
    {
        if (isset($this->form['navigation'][$index])) {
            Arr::forget($this->form, "navigation.$index");
            $this->form['navigation'] = array_values($this->form['navigation']);
        }
    }

    public function addHeroCta(): void
    {
        $this->form['hero']['ctas'][] = [
            'label' => $this->blankTranslations(__('New CTA')),
            'href' => '#',
            'style' => 'primary',
        ];
    }

    public function removeHeroCta(int $index): void
    {
        if (isset($this->form['hero']['ctas'][$index])) {
            Arr::forget($this->form, "hero.ctas.$index");
            $this->form['hero']['ctas'] = array_values($this->form['hero']['ctas']);
        }

        if (empty($this->form['hero']['ctas'])) {
            $this->addHeroCta();
        }
    }

    public function addFeatureIntroItem(): void
    {
        $this->form['features']['intro']['items'][] = [
            'icon' => 'sparkles',
            'title' => $this->blankTranslations(__('New feature')),
            'body' => $this->blankTranslations(''),
        ];
    }

    public function removeFeatureIntroItem(int $index): void
    {
        if (isset($this->form['features']['intro']['items'][$index])) {
            Arr::forget($this->form, "features.intro.items.$index");
            $this->form['features']['intro']['items'] = array_values($this->form['features']['intro']['items']);
        }

        if (empty($this->form['features']['intro']['items'])) {
            $this->addFeatureIntroItem();
        }
    }

    public function addFeatureColumnItem(int $columnIndex): void
    {
        $this->form['features']['columns'][$columnIndex]['items'][] = [
            'title' => $this->blankTranslations(__('New benefit')),
            'body' => $this->blankTranslations(''),
        ];
    }

    public function removeFeatureColumnItem(int $columnIndex, int $itemIndex): void
    {
        if (isset($this->form['features']['columns'][$columnIndex]['items'][$itemIndex])) {
            Arr::forget($this->form, "features.columns.$columnIndex.items.$itemIndex");
            $items = $this->form['features']['columns'][$columnIndex]['items'] ?? [];
            $this->form['features']['columns'][$columnIndex]['items'] = array_values($items);
        }
    }

    public function addPricingPlan(): void
    {
        $this->form['pricing']['plans'][] = [
            'name' => $this->blankTranslations(__('New plan')),
            'price' => '',
            'unit' => '/month',
            'highlighted' => false,
            'features' => [
                $this->blankTranslations(__('Plan feature')),
            ],
            'cta' => [
                'label' => $this->blankTranslations(__('Choose plan')),
                'href' => '#',
            ],
        ];
    }

    public function removePricingPlan(int $index): void
    {
        if (isset($this->form['pricing']['plans'][$index])) {
            Arr::forget($this->form, "pricing.plans.$index");
            $this->form['pricing']['plans'] = array_values($this->form['pricing']['plans']);
        }

        if (empty($this->form['pricing']['plans'])) {
            $this->addPricingPlan();
        }
    }

    public function addPlanFeature(int $planIndex): void
    {
        $this->form['pricing']['plans'][$planIndex]['features'][] = $this->blankTranslations(__('Additional feature'));
    }

    public function removePlanFeature(int $planIndex, int $featureIndex): void
    {
        if (isset($this->form['pricing']['plans'][$planIndex]['features'][$featureIndex])) {
            Arr::forget($this->form, "pricing.plans.$planIndex.features.$featureIndex");
            $features = $this->form['pricing']['plans'][$planIndex]['features'] ?? [];
            $this->form['pricing']['plans'][$planIndex]['features'] = array_values($features);
        }

        if (empty($this->form['pricing']['plans'][$planIndex]['features'])) {
            $this->addPlanFeature($planIndex);
        }
    }

    public function addTestimonial(): void
    {
        $this->form['testimonials']['items'][] = [
            'quote' => $this->blankTranslations(__('A wonderful testimonial.')),
            'author' => $this->blankTranslations(__('Customer name')),
        ];
    }

    public function removeTestimonial(int $index): void
    {
        if (isset($this->form['testimonials']['items'][$index])) {
            Arr::forget($this->form, "testimonials.items.$index");
            $this->form['testimonials']['items'] = array_values($this->form['testimonials']['items']);
        }

        if (empty($this->form['testimonials']['items'])) {
            $this->addTestimonial();
        }
    }

    public function removeLogo(): void
    {
        $this->form['assets']['logo_url'] = null;
    }

    public function removeHeroImage(): void
    {
        $this->form['assets']['hero_image'] = null;
    }

    public function removeGalleryImage(int $index): void
    {
        $gallery = $this->form['assets']['feature_images']['gallery'] ?? [];

        if (isset($gallery[$index])) {
            Arr::forget($this->form, "assets.feature_images.gallery.$index");
            $gallery = array_values($gallery);
            $this->form['assets']['feature_images']['gallery'] = $gallery;
        }
    }

    public function setPreviewMode(string $mode): void
    {
        if (in_array($mode, ['desktop', 'tablet', 'mobile'], true)) {
            $this->previewMode = $mode;
        }
    }

    public function refreshPreview(): void
    {
        // Trigger a re-render without mutating data.
        $this->dispatch('$refresh');
    }

    protected function ensureCollections(): void
    {
        $this->form['navigation'] = array_values($this->form['navigation'] ?? []);
        $this->form['hero']['ctas'] = array_values($this->form['hero']['ctas'] ?? []);
        $this->form['testimonials']['items'] = array_values($this->form['testimonials']['items'] ?? []);
        $this->form['features']['intro']['items'] = array_values($this->form['features']['intro']['items'] ?? []);
        $this->form['features']['columns'] = array_values($this->form['features']['columns'] ?? []);
        foreach ($this->form['features']['columns'] as $index => $column) {
            $items = $column['items'] ?? [];
            $this->form['features']['columns'][$index]['items'] = array_values($items);
        }
        $this->form['pricing']['plans'] = array_values($this->form['pricing']['plans'] ?? []);
        foreach ($this->form['pricing']['plans'] as $planIndex => $plan) {
            $features = $plan['features'] ?? [];
            $this->form['pricing']['plans'][$planIndex]['features'] = array_values($features);
        }
        $gallery = Arr::get($this->form, 'assets.feature_images.gallery', []);
        $this->form['assets']['feature_images']['gallery'] = array_values($gallery);

        $this->form['cta']['primary']['label'] = $this->form['cta']['primary']['label'] ?? $this->blankTranslations('');
        $this->form['cta']['primary']['href'] = $this->form['cta']['primary']['href'] ?? '#';
        $this->form['cta']['secondary']['label'] = $this->form['cta']['secondary']['label'] ?? $this->blankTranslations('');
        $this->form['cta']['secondary']['href'] = $this->form['cta']['secondary']['href'] ?? '';

        if (empty($this->form['navigation'])) {
            $this->addNavigationLink();
        }

        if (empty($this->form['hero']['ctas'])) {
            $this->addHeroCta();
        }

        if (empty($this->form['features']['intro']['items'])) {
            $this->addFeatureIntroItem();
        }

        if (empty($this->form['testimonials']['items'])) {
            $this->addTestimonial();
        }

        if (empty($this->form['pricing']['plans'])) {
            $this->addPricingPlan();
        }
    }

    protected function blankTranslations(string $fallback = ''): array
    {
        return collect($this->locales)
            ->keys()
            ->mapWithKeys(fn ($locale) => [$locale => $fallback])
            ->all();
    }

    protected function rules(): array
    {
        $base = $this->baseLocale;

        return [
            // Meta
            "form.meta.title.$base" => ['required', 'string', 'max:120'],
            'form.meta.title' => ['array'],

            // Navigation
            'form.navigation' => ['array', 'min:1'],
            "form.navigation.*.label.$base" => ['required', 'string', 'max:60'],
            'form.navigation.*.href' => ['required', 'string', 'max:255'],
            'form.navigation.*.variant' => ['nullable', 'string', 'max:40'],

            // Hero
            "form.hero.headline.$base" => ['required', 'string', 'max:160'],
            "form.hero.subheadline.$base" => ['required', 'string', 'max:260'],
            'form.hero.ctas' => ['array', 'min:1'],
            "form.hero.ctas.*.label.$base" => ['required', 'string', 'max:60'],
            'form.hero.ctas.*.href' => ['required', 'string', 'max:255'],
            'form.hero.ctas.*.style' => ['nullable', 'in:primary,outline,text'],

            // Features
            "form.features.intro.headline.$base" => ['required', 'string', 'max:160'],
            "form.features.intro.description.$base" => ['required', 'string', 'max:260'],
            'form.features.intro.items' => ['array', 'min:1'],
            'form.features.intro.items.*.icon' => ['nullable', 'string', 'max:40'],
            "form.features.intro.items.*.title.$base" => ['required', 'string', 'max:80'],
            "form.features.intro.items.*.body.$base" => ['required', 'string', 'max:220'],
            'form.features.columns' => ['array', 'min:1'],
            "form.features.columns.*.title.$base" => ['required', 'string', 'max:80'],
            'form.features.columns.*.items' => ['array', 'min:1'],
            "form.features.columns.*.items.*.title.$base" => ['required', 'string', 'max:80'],
            "form.features.columns.*.items.*.body.$base" => ['required', 'string', 'max:220'],

            // Pricing
            'form.pricing.plans' => ['array', 'min:1'],
            "form.pricing.plans.*.name.$base" => ['required', 'string', 'max:80'],
            'form.pricing.plans.*.price' => ['nullable', 'string', 'max:60'],
            'form.pricing.plans.*.unit' => ['nullable', 'string', 'max:40'],
            'form.pricing.plans.*.features' => ['array', 'min:1'],
            "form.pricing.plans.*.features.*.$base" => ['required', 'string', 'max:120'],
            "form.pricing.plans.*.cta.label.$base" => ['nullable', 'string', 'max:80'],
            'form.pricing.plans.*.cta.href' => ['nullable', 'string', 'max:255'],

            // Testimonials
            "form.testimonials.headline.$base" => ['nullable', 'string', 'max:160'],
            'form.testimonials.items' => ['array', 'min:1'],
            "form.testimonials.items.*.quote.$base" => ['required', 'string', 'max:280'],
            "form.testimonials.items.*.author.$base" => ['required', 'string', 'max:120'],

            // CTA
            "form.cta.primary.label.$base" => ['required', 'string', 'max:60'],
            'form.cta.primary.href' => ['required', 'string', 'max:255'],
            "form.cta.secondary.label.$base" => ['nullable', 'string', 'max:60'],
            'form.cta.secondary.href' => ['nullable', 'string', 'max:255'],

            // Uploads
            'logoUpload' => ['nullable', 'image', 'max:2048'],
            'heroUpload' => ['nullable', 'image', 'max:4096'],
            'galleryUploads' => ['nullable', 'array', 'max:6'],
            'galleryUploads.*' => ['image', 'max:4096'],
        ];
    }

    protected function validationAttributes(): array
    {
        $base = $this->baseLocale;

        return [
            'form.meta.title.*' => __('meta title'),
            'form.navigation.*.label.*' => __('navigation label'),
            'form.navigation.*.href' => __('navigation link'),
            'form.hero.headline.*' => __('hero headline'),
            'form.hero.subheadline.*' => __('hero subheadline'),
            'form.hero.ctas.*.label.*' => __('hero CTA label'),
            'form.hero.ctas.*.href' => __('hero CTA link'),
            'form.cta.primary.label.*' => __('primary CTA label'),
            'form.cta.primary.href' => __('primary CTA link'),
            'form.features.intro.headline.*' => __('features intro headline'),
            'form.features.intro.description.*' => __('features intro description'),
            'form.features.intro.items.*.title.*' => __('feature card title'),
            'form.features.intro.items.*.body.*' => __('feature card description'),
            'form.features.columns.*.title.*' => __('feature column title'),
            'form.features.columns.*.items.*.title.*' => __('feature column item title'),
            'form.features.columns.*.items.*.body.*' => __('feature column item description'),
            'form.pricing.plans.*.name.*' => __('plan name'),
            'form.pricing.plans.*.price' => __('plan price'),
            "form.pricing.plans.*.features.*.$base" => __('plan feature'),
            'form.pricing.plans.*.cta.label.*' => __('plan CTA label'),
            'form.testimonials.headline.*' => __('testimonials headline'),
            'form.testimonials.items.*.quote.*' => __('testimonial quote'),
            'form.testimonials.items.*.author.*' => __('testimonial author'),
            'logoUpload' => __('logo'),
            'heroUpload' => __('hero image'),
            'galleryUploads.*' => __('gallery image'),
        ];
    }

    public function render(): View
    {
        $previewHtml = null;
        $previewError = null;

        try {
            $previewData = app(PublicLandingService::class)->preview($this->form);
            $previewHtml = view('home', [
                'landing' => $previewData,
                'categories' => collect(),
            ])->render();
        } catch (\Throwable $exception) {
            report($exception);
            $previewError = __('Preview unavailable: :message', ['message' => $exception->getMessage()]);
        }

        return view('livewire.admin.landing-editor', [
            'form' => $this->form,
            'locales' => $this->locales,
            'baseLocale' => $this->baseLocale,
            'previewHtml' => $previewHtml,
            'previewError' => $previewError,
        ]);
    }

    protected function encodeJson(array $value): string
    {
        $json = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return $json ?: '{}';
    }

    protected function merge(array $defaults, array $overrides): array
    {
        foreach ($overrides as $key => $value) {
            if (is_array($value) && isset($defaults[$key]) && is_array($defaults[$key])) {
                $defaults[$key] = $this->merge($defaults[$key], $value);
            } else {
                $defaults[$key] = $value;
            }
        }

        return $defaults;
    }

    protected function handleUploads(): void
    {
        if ($this->logoUpload) {
            $path = $this->logoUpload->storePublicly('landing', ['disk' => $this->uploadDisk()]);
            $this->form['assets']['logo_url'] = $this->storageUrl($path);
            $this->logoUpload = null;
        }

        if ($this->heroUpload) {
            $path = $this->heroUpload->storePublicly('landing', ['disk' => $this->uploadDisk()]);
            $this->form['assets']['hero_image'] = $this->storageUrl($path);
            $this->heroUpload = null;
        }

        if (! empty($this->galleryUploads)) {
            $existing = $this->form['assets']['feature_images']['gallery'] ?? [];
            foreach ($this->galleryUploads as $upload) {
                $path = $upload->storePublicly('landing', ['disk' => $this->uploadDisk()]);
                $existing[] = $this->storageUrl($path);
            }

            $this->form['assets']['feature_images']['gallery'] = $existing;
            $this->galleryUploads = [];
        }
    }

    protected function storageUrl(string $path): string
    {
        if ($this->uploadDisk() === 'public') {
            // Use request-aware absolute URL (preserves dev ports like :8000)
            return url('storage/'.$path);
        }

        return Storage::disk($this->uploadDisk())->url($path);
    }

    protected function uploadDisk(): string
    {
        $disk = config('filesystems.default', 'local');

        return $disk === 'local' ? 'public' : $disk;
    }
}
