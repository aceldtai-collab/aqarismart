<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Services\Tenancy\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TenantSettingsController extends Controller
{
    public function __construct(protected TenantManager $tenants) {}

    public function edit(): View
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);

        $this->authorize('create', \App\Models\Agent::class);

        return view('settings.edit', compact('tenant'));
    }

    public function update(Request $request): RedirectResponse
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);

        $this->authorize('create', \App\Models\Agent::class); // admin/owner only

        $validated = $request->validate([
            'name' => ['required','string','max:255'],
            'slug' => ['required','alpha_dash','max:50', Rule::unique('tenants','slug')->ignore($tenant->id)],
            'timezone' => ['required', Rule::in(\DateTimeZone::listIdentifiers())],
            'primary_color' => ['nullable','regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/'],
            'accent_color' => ['nullable','regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/'],
            'font_color' => ['nullable','regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/'],
            'currency' => ['nullable', Rule::in(['USD','JD'])],
            'country' => ['nullable', Rule::in(['US','JO'])],
            'typography' => ['nullable', Rule::in(['system','serif','mono'])],
            'logo' => ['nullable','file','mimes:png,jpg,jpeg,svg','max:2048'],
            'favicon' => ['nullable','file','mimes:ico,png','max:1024'],
            'header_bg' => ['nullable','file','mimes:jpg,jpeg,png,webp,svg','max:4096'],
            'home_show_types' => ['nullable','boolean'],
            'home_show_cities' => ['nullable','boolean'],
            'home_show_latest' => ['nullable','boolean'],
            'home_show_search' => ['nullable','boolean'],
            'home_show_map' => ['nullable','boolean'],
            'tagline' => ['nullable','string','max:255'],
            'contact_email' => ['nullable','email','max:255'],
            'contact_phone' => ['nullable','string','max:50'],
            'footer_quote' => ['nullable','string','max:255'],
            'footer_links' => ['nullable','array'],
            'footer_links.*.label' => ['nullable','string','max:100'],
            'footer_links.*.href' => ['nullable','url','max:255'],
            'footer_social' => ['nullable','array'],
            'footer_social.*.label' => ['nullable','string','max:50'],
            'footer_social.*.url' => ['nullable','url','max:255'],
            'footer_show_newsletter' => ['nullable','boolean'],
            'footer_newsletter_text' => ['nullable','string','max:255'],
        ]);

        $oldSlug = $tenant->slug;
        $tenant->name = $request->string('name');
        $tenant->slug = $request->string('slug');
        $settings = $tenant->settings ?? [];
        $settings['timezone'] = $request->string('timezone');
        if (!empty($validated['primary_color'])) { $settings['primary_color'] = $validated['primary_color']; }
        if (!empty($validated['accent_color'])) { $settings['accent_color'] = $validated['accent_color']; }
        if (!empty($validated['font_color'])) { $settings['font_color'] = $validated['font_color']; }
        if (!empty($validated['currency'])) { $settings['currency'] = $validated['currency']; }
        if (!empty($validated['country'])) { $settings['country'] = $validated['country']; }
        if (!empty($validated['typography'])) { $settings['typography'] = $validated['typography']; }
        $settings['home_show_types'] = $request->boolean('home_show_types');
        $settings['home_show_cities'] = $request->boolean('home_show_cities');
        $settings['home_show_latest'] = $request->boolean('home_show_latest');
        $settings['home_show_search'] = $request->boolean('home_show_search');
        $settings['home_show_map'] = $request->boolean('home_show_map');
        if ($request->filled('tagline')) {
            $settings['tagline'] = trim((string) $request->input('tagline'));
        } else {
            unset($settings['tagline']);
        }

        if ($request->filled('contact_email')) {
            $settings['contact_email'] = trim((string) $request->input('contact_email'));
        } else {
            unset($settings['contact_email']);
        }

        if ($request->filled('contact_phone')) {
            $settings['contact_phone'] = trim((string) $request->input('contact_phone'));
        } else {
            unset($settings['contact_phone']);
        }

        $footerLinks = collect($request->input('footer_links', []))
            ->map(fn ($link) => [
                'label' => trim($link['label'] ?? ''),
                'href' => trim($link['href'] ?? ''),
            ])
            ->filter(fn ($link) => $link['label'] !== '' && $link['href'] !== '')
            ->values()
            ->all();

        $footerSocial = collect($request->input('footer_social', []))
            ->map(fn ($entry) => [
                'label' => trim($entry['label'] ?? ''),
                'url' => trim($entry['url'] ?? ''),
            ])
            ->filter(fn ($entry) => $entry['label'] !== '' && $entry['url'] !== '')
            ->values()
            ->all();

        $footerSettings = [
            'quote' => $request->filled('footer_quote') ? trim((string) $request->input('footer_quote')) : null,
            'links' => $footerLinks,
            'social' => $footerSocial,
            'show_newsletter' => $request->boolean('footer_show_newsletter'),
            'newsletter_text' => $request->filled('footer_newsletter_text') ? trim((string) $request->input('footer_newsletter_text')) : null,
        ];

        if (empty($footerSettings['quote'])) {
            unset($footerSettings['quote']);
        }
        if (empty($footerSettings['newsletter_text'])) {
            unset($footerSettings['newsletter_text']);
        }

        $settings['footer'] = $footerSettings;

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('tenants/'.$tenant->id.'/branding', 'public');
            $settings['logo_url'] = 'storage/'.$path;
        }
        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('tenants/'.$tenant->id.'/branding', 'public');
            $settings['favicon_url'] = 'storage/'.$path;
        }
        if ($request->hasFile('header_bg')) {
            $path = $request->file('header_bg')->store('tenants/'.$tenant->id.'/branding', 'public');
            $settings['header_bg_url'] = 'storage/'.$path;
        }
        $tenant->settings = $settings;
        $tenant->save();

        // If slug changed, redirect to new subdomain
        if ($oldSlug !== $tenant->slug) {
        $url = $this->tenants->tenantUrl($tenant, '/dashboard');
            return redirect()->away($url)->with('status', 'Settings saved');
        }

        return back()->with('status', 'Settings saved');
    }
}
