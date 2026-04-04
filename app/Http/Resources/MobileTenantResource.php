<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MobileTenantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $settings = is_array($this->settings ?? null) ? $this->settings : [];
        $description = $settings['about']['description']
            ?? $settings['description']
            ?? $settings['public_description']
            ?? null;
        $logoUrl = $this->normalizePublicUrl($settings['logo_url'] ?? null);
        $faviconUrl = $this->normalizePublicUrl($settings['favicon_url'] ?? null);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'plan' => $this->plan,
            'url' => $this->url,
            'branding' => [
                'logo_url' => $logoUrl,
                'favicon_url' => $faviconUrl,
                'primary_color' => $settings['primary_color'] ?? null,
                'accent_color' => $settings['accent_color'] ?? null,
            ],
            'summary' => [
                'description' => $description,
                'city' => $settings['city'] ?? null,
                'coverage' => $settings['coverage'] ?? null,
                'address' => $settings['address'] ?? null,
                'phone' => $settings['phone'] ?? null,
                'email' => $settings['email'] ?? null,
                'website' => $settings['website'] ?? null,
            ],
            'stats' => [
                'units_count' => $this->whenCounted('units', fn () => $this->units_count),
                'active_units_count' => $this->when(isset($this->active_units_count), fn () => $this->active_units_count),
                'agents_count' => $this->when(isset($this->agents_count), fn () => $this->agents_count),
            ],
            'subscription' => [
                'status' => $this->activeSubscription?->status,
                'ends_at' => optional($this->activeSubscription?->ends_at)?->toISOString(),
                'package_name' => $this->activeSubscription?->package?->name,
            ],
        ];
    }

    private function normalizePublicUrl(?string $path): ?string
    {
        if (! is_string($path) || trim($path) === '') {
            return null;
        }

        $path = trim($path);

        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        if (str_starts_with($path, '/storage/') || str_starts_with($path, 'storage/')) {
            return url('/' . ltrim($path, '/'));
        }

        return Storage::disk('public')->url($path);
    }
}
