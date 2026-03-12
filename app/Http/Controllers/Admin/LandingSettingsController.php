<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Services\PublicLandingService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class LandingSettingsController extends Controller
{
    private function uploadLandingFile(\Illuminate\Http\UploadedFile $file, string $disk): string
    {
        $ext = $file->guessExtension() ?: $file->getClientOriginalExtension();
        $name = \Illuminate\Support\Str::random(40) . '.' . $ext;
        $path = 'landing/' . $name;
        Storage::disk($disk)->put($path, file_get_contents($file->getPathname()), 'public');
        return $disk === 'public' ? url('storage/' . $path) : Storage::disk($disk)->url($path);
    }

    public function edit(): View
    {
        $current = SystemSetting::getValue('landing', []);

        return view('admin.settings.landing', [
            'current' => is_array($current) ? $current : [],
            'currentJson' => json_encode($current, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '{}',
            'defaultsJson' => json_encode(config('public_site_landing'), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
        ]);
    }

    public function update(Request $request, PublicLandingService $landing): RedirectResponse
    {
        $data = $request->validate([
            'payload' => ['required', 'string'],
        ]);

        $payload = json_decode($data['payload'], true);

        if (! is_array($payload)) {
            return back()->withErrors([
                'payload' => __('The payload must be valid JSON (object/array).'),
            ])->withInput();
        }

        SystemSetting::setValue('landing', $payload);
        $landing->clearCache();

        return back()->with('status', __('Landing page content updated.'));
    }

    public function updateSection(Request $request, PublicLandingService $landing, string $section): RedirectResponse
    {
        $current = SystemSetting::getValue('landing', []);
        if (! is_array($current)) { $current = []; }

        switch ($section) {
            case 'assets':
                $validated = $request->validate([
                    'logo' => ['nullable','image','max:2048'],
                    'hero' => ['nullable','image','max:4096'],
                    'auth_screenshot' => ['nullable','image','max:4096'],
                    'gallery' => ['nullable','array','max:6'],
                    'gallery.*' => ['image','max:4096'],
                    'remove_gallery' => ['nullable','integer','min:0'],
                    'remove_gallery_url' => ['nullable','string','max:2048'],
                    'remove_logo' => ['nullable','boolean'],
                    'remove_hero' => ['nullable','boolean'],
                    'remove_auth_screenshot' => ['nullable','boolean'],
                ]);

                $disk = config('filesystems.default', 'local') === 'local' ? 'public' : config('filesystems.default');

                // Handle removal of a single gallery image by index or URL
                if ($request->filled('remove_gallery') || $request->filled('remove_gallery_url')) {
                    $gallery = Arr::get($current, 'assets.feature_images.gallery', []);
                    if ($request->filled('remove_gallery')) {
                        $idx = (int) $request->integer('remove_gallery');
                        if (isset($gallery[$idx])) {
                            Arr::forget($gallery, $idx);
                        }
                    } elseif ($request->filled('remove_gallery_url')) {
                        $toRemove = (string) $request->input('remove_gallery_url');
                        $gallery = array_values(array_filter($gallery, fn($url) => $url !== $toRemove));
                    }
                    Arr::set($current, 'assets.feature_images.gallery', array_values($gallery));
                    SystemSetting::setValue('landing', $current);
                    $landing->clearCache();
                    return back()->with('status', __('Image removed from gallery.'));
                }

                if ($request->boolean('remove_logo')) {
                    $current['assets']['logo_url'] = null;
                }
                if ($request->hasFile('logo')) {
                    $current['assets']['logo_url'] = $this->uploadLandingFile($request->file('logo'), $disk);
                }
                if ($request->boolean('remove_hero')) {
                    $current['assets']['hero_image'] = null;
                }
                if ($request->hasFile('hero')) {
                    $current['assets']['hero_image'] = $this->uploadLandingFile($request->file('hero'), $disk);
                }
                if ($request->boolean('remove_auth_screenshot')) {
                    $current['assets']['auth_screenshot'] = null;
                }
                if ($request->hasFile('auth_screenshot')) {
                    $current['assets']['auth_screenshot'] = $this->uploadLandingFile($request->file('auth_screenshot'), $disk);
                }
                if ($request->hasFile('gallery')) {
                    $gallery = Arr::get($current, 'assets.feature_images.gallery', []);
                    foreach ($request->file('gallery') as $file) {
                        $gallery[] = $this->uploadLandingFile($file, $disk);
                    }
                    Arr::set($current, 'assets.feature_images.gallery', array_values($gallery));
                }
                break;

            case 'video':
                $validated = $request->validate([
                    'video.headline.en' => ['nullable','string','max:160'],
                    'video.headline.ar' => ['nullable','string','max:160'],
                    'video.description.en' => ['nullable','string','max:300'],
                    'video.description.ar' => ['nullable','string','max:300'],
                    'video.youtube_url' => ['nullable','url','max:500'],
                    'video_poster' => ['nullable','image','max:4096'],
                    'remove_video_poster' => ['nullable','boolean'],
                ]);

                $disk = config('filesystems.default', 'local') === 'local' ? 'public' : config('filesystems.default');
                $videoData = Arr::get($current, 'video', []);
                if (! is_array($videoData)) { $videoData = []; }

                $videoData['headline'] = array_replace(($videoData['headline'] ?? []), Arr::get($validated, 'video.headline', []));
                $videoData['description'] = array_replace(($videoData['description'] ?? []), Arr::get($validated, 'video.description', []));
                $videoData['youtube_url'] = $validated['video']['youtube_url'] ?? ($videoData['youtube_url'] ?? null);

                if ($request->boolean('remove_video_poster')) {
                    $videoData['poster_image'] = null;
                }
                if ($request->hasFile('video_poster')) {
                    $videoData['poster_image'] = $this->uploadLandingFile($request->file('video_poster'), $disk);
                }

                $current['video'] = $videoData;
                break;

            case 'meta':
                $validated = $request->validate([
                    'meta.title.en' => ['required','string','max:120'],
                    'meta.title.ar' => ['nullable','string','max:120'],
                ]);
                $current['meta']['title'] = array_replace(($current['meta']['title'] ?? []), $validated['meta']['title']);
                break;

            case 'hero':
                $validated = $request->validate([
                    'hero.headline.en' => ['required','string','max:160'],
                    'hero.headline.ar' => ['nullable','string','max:160'],
                    'hero.subheadline.en' => ['required','string','max:260'],
                    'hero.subheadline.ar' => ['nullable','string','max:260'],
                ]);
                foreach (['headline','subheadline'] as $k) {
                    $current['hero'][$k] = array_replace(($current['hero'][$k] ?? []), $validated['hero'][$k]);
                }
                break;

            case 'cta':
                $validated = $request->validate([
                    'cta.primary.label.en' => ['required','string','max:60'],
                    'cta.primary.label.ar' => ['nullable','string','max:60'],
                    'cta.primary.href' => ['required','string','max:255'],
                    'cta.secondary.label.en' => ['nullable','string','max:60'],
                    'cta.secondary.label.ar' => ['nullable','string','max:60'],
                    'cta.secondary.href' => ['nullable','string','max:255'],
                ]);
                $current['cta']['primary']['label'] = array_replace(($current['cta']['primary']['label'] ?? []), $validated['cta']['primary']['label']);
                $current['cta']['primary']['href'] = $validated['cta']['primary']['href'];
                $current['cta']['secondary']['label'] = array_replace(($current['cta']['secondary']['label'] ?? []), $validated['cta']['secondary']['label'] ?? []);
                $current['cta']['secondary']['href'] = $validated['cta']['secondary']['href'] ?? ($current['cta']['secondary']['href'] ?? '');
                break;

            case 'seo':
                $validated = $request->validate([
                    'seo.title.en' => ['nullable','string','max:120'],
                    'seo.title.ar' => ['nullable','string','max:120'],
                    'seo.description.en' => ['nullable','string','max:300'],
                    'seo.description.ar' => ['nullable','string','max:300'],
                    'seo.robots' => ['nullable','string','max:50'],
                    'seo_og' => ['nullable','image','max:4096'],
                    'seo_twitter' => ['nullable','image','max:4096'],
                    'seo_favicon' => ['nullable','image','max:1024'],
                    'remove_seo_og' => ['nullable','boolean'],
                    'remove_seo_twitter' => ['nullable','boolean'],
                    'remove_seo_favicon' => ['nullable','boolean'],
                ]);

                $disk = config('filesystems.default', 'local') === 'local' ? 'public' : config('filesystems.default');

                $seo = Arr::get($current, 'seo', []);
                if (! is_array($seo)) {
                    $seo = [];
                }

                $seo['title'] = array_replace(($seo['title'] ?? []), Arr::get($validated, 'seo.title', []));
                $seo['description'] = array_replace(($seo['description'] ?? []), Arr::get($validated, 'seo.description', []));
                if (! empty($validated['seo']['robots'] ?? null)) {
                    $seo['robots'] = $validated['seo']['robots'];
                }

                if ($request->boolean('remove_seo_og')) {
                    $seo['og_image'] = null;
                }
                if ($request->boolean('remove_seo_twitter')) {
                    $seo['twitter_image'] = null;
                }
                if ($request->boolean('remove_seo_favicon')) {
                    $seo['favicon'] = null;
                }

                if ($request->hasFile('seo_og')) {
                    $path = $request->file('seo_og')->storePublicly('landing', ['disk' => $disk]);
                    $seo['og_image'] = $disk === 'public' ? url('storage/'.$path) : Storage::disk($disk)->url($path);
                }
                if ($request->hasFile('seo_twitter')) {
                    $path = $request->file('seo_twitter')->storePublicly('landing', ['disk' => $disk]);
                    $seo['twitter_image'] = $disk === 'public' ? url('storage/'.$path) : Storage::disk($disk)->url($path);
                }
                if ($request->hasFile('seo_favicon')) {
                    $path = $request->file('seo_favicon')->storePublicly('landing', ['disk' => $disk]);
                    $seo['favicon'] = $disk === 'public' ? url('storage/'.$path) : Storage::disk($disk)->url($path);
                }

                $current['seo'] = $seo;
                break;

            case 'navigation':
                if ($request->has('payload')) {
                    $data = $request->validate([
                        'payload' => ['required','string'],
                    ]);
                    $payload = json_decode($data['payload'], true);
                    if (! is_array($payload)) {
                        return back()->withErrors(['payload' => __('The payload must be valid JSON.')])->withInput();
                    }
                    $current['navigation'] = array_values($payload);
                } else {
                    $nav = array_values($request->input('navigation', []));
                    foreach ($nav as $i => $item) {
                        $request->validate([
                            "navigation.$i.label.en" => ['required','string','max:60'],
                            "navigation.$i.href" => ['required','string','max:255'],
                            "navigation.$i.variant" => ['nullable','string','max:40'],
                        ]);
                    }
                    $current['navigation'] = $nav;
                }
                break;

            case 'features':
                if ($request->has('payload')) {
                    $data = $request->validate([
                        'payload' => ['required','string'],
                    ]);
                    $payload = json_decode($data['payload'], true);
                    if (! is_array($payload)) {
                        return back()->withErrors(['payload' => __('The payload must be valid JSON.')])->withInput();
                    }
                    $current['features'] = $payload;
                } else {
                    $features = $request->input('features', []);
                    // Validate intros
                    $request->validate([
                        'features.intro.headline.en' => ['required','string','max:160'],
                        'features.intro.description.en' => ['required','string','max:260'],
                    ]);
                    $intro = [
                        'headline' => array_replace(($current['features']['intro']['headline'] ?? []), Arr::get($features, 'intro.headline', [])),
                        'description' => array_replace(($current['features']['intro']['description'] ?? []), Arr::get($features, 'intro.description', [])),
                    ];
                    // Items (optional)
                    $items = [];
                    foreach (Arr::get($features, 'intro.items', []) as $idx => $row) {
                        $items[] = [
                            'icon' => $row['icon'] ?? null,
                            'title' => $row['title'] ?? [],
                            'body' => $row['body'] ?? [],
                        ];
                    }
                    $intro['items'] = $items;
                    $current['features']['intro'] = $intro;

                    // Columns (optional)
                    $columnsIn = Arr::get($features, 'columns', []);
                    if (is_array($columnsIn)) {
                        $columns = [];
                        foreach (array_values($columnsIn) as $ci => $col) {
                            $title = $col['title'] ?? [];
                            $colItemsIn = $col['items'] ?? [];
                            $colItems = [];
                            if (is_array($colItemsIn)) {
                                foreach (array_values($colItemsIn) as $ii => $citem) {
                                    $colItems[] = [
                                        'title' => $citem['title'] ?? [],
                                        'body'  => $citem['body'] ?? [],
                                    ];
                                }
                            }
                            $columns[] = [
                                'title' => $title,
                                'items' => $colItems,
                            ];
                        }
                        $current['features']['columns'] = $columns;
                    }
                }
                break;

            case 'pricing':
                if ($request->has('payload')) {
                    $data = $request->validate([
                        'payload' => ['required','string'],
                    ]);
                    $payload = json_decode($data['payload'], true);
                    if (! is_array($payload)) {
                        return back()->withErrors(['payload' => __('The payload must be valid JSON.')])->withInput();
                    }
                    $current['pricing'] = $payload;
                } else {
                    $plans = array_values($request->input('pricing.plans', []));
                    $normalized = [];
                    foreach ($plans as $i => $plan) {
                        $request->validate([
                            "pricing.plans.$i.name.en" => ['required','string','max:80'],
                            "pricing.plans.$i.price" => ['nullable','string','max:60'],
                            "pricing.plans.$i.unit" => ['nullable','string','max:40'],
                            "pricing.plans.$i.cta.href" => ['nullable','string','max:255'],
                        ]);
                        $featuresText = $plan['features_text'] ?? '';
                        $featuresArray = array_values(array_filter(array_map('trim', preg_split("/\r?\n/", (string) $featuresText))));
                        $normalized[] = [
                            'name' => $plan['name'] ?? [],
                            'price' => $plan['price'] ?? null,
                            'unit' => $plan['unit'] ?? null,
                            'highlighted' => !empty($plan['highlighted']),
                            'features' => $featuresArray,
                            'cta' => [
                                'label' => $plan['cta']['label'] ?? [],
                                'href' => $plan['cta']['href'] ?? '#',
                            ],
                        ];
                    }
                    $current['pricing']['plans'] = $normalized;
                }
                break;

            case 'testimonials':
                if ($request->has('payload')) {
                    $data = $request->validate([
                        'payload' => ['required','string'],
                    ]);
                    $payload = json_decode($data['payload'], true);
                    if (! is_array($payload)) {
                        return back()->withErrors(['payload' => __('The payload must be valid JSON.')])->withInput();
                    }
                    $current['testimonials'] = $payload;
                } else {
                    $request->validate([
                        'testimonials.headline.en' => ['nullable','string','max:160'],
                    ]);
                    $items = array_values($request->input('testimonials.items', []));
                    $normalizedItems = [];
                    foreach ($items as $i => $row) {
                        $request->validate([
                            "testimonials.items.$i.quote.en" => ['required','string','max:280'],
                            "testimonials.items.$i.author.en" => ['required','string','max:120'],
                        ]);
                        $normalizedItems[] = [
                            'quote' => $row['quote'] ?? [],
                            'author' => $row['author'] ?? [],
                        ];
                    }
                    $current['testimonials']['headline'] = array_replace(($current['testimonials']['headline'] ?? []), $request->input('testimonials.headline', []));
                    $current['testimonials']['items'] = $normalizedItems;
                }
                break;

            default:
                return back()->withErrors(['section' => __('Unknown section')]);
        }

        SystemSetting::setValue('landing', $current);
        $landing->clearCache();

        return back()->with('status', __('Section updated: :section', ['section' => $section]));
    }
}
