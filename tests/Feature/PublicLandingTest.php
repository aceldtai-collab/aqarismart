<?php

namespace Tests\Feature;

use App\Models\SystemSetting;
use App\Services\PublicLandingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicLandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders_with_landing_data(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee(config('public_site_landing.hero.headline'));
        $response->assertSee(config('public_site_landing.pricing.plans.0.name'));
    }

    public function test_service_returns_configured_defaults(): void
    {
        $service = app(PublicLandingService::class);

        $landing = $service->forPublicDomain();

        $this->assertSame(config('public_site_landing.hero.headline'), $landing['hero']['headline']);
        $this->assertSame(config('public_site_landing.pricing.plans.1.name'), $landing['pricing']['plans'][1]['name']);
    }

    public function test_service_merges_database_overrides(): void
    {
        SystemSetting::setValue('landing', [
            'hero' => ['headline' => 'Custom Headline'],
        ]);

        $landing = app(PublicLandingService::class)->forPublicDomain();

        $this->assertSame('Custom Headline', $landing['hero']['headline']);
        $this->assertSame(config('public_site_landing.pricing.plans.0.name'), $landing['pricing']['plans'][0]['name']);
    }
}
