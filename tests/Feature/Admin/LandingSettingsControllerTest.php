<?php

namespace Tests\Feature\Admin;

use App\Models\SystemSetting;
use App\Models\User;
use App\Services\PublicLandingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingSettingsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_update_landing_settings(): void
    {
        $user = User::factory()->create(['email' => 'owner@example.com']);
        config(['auth.super_admin_emails' => [$user->email]]);

        $this->actingAs($user)
            ->post(route('admin.settings.landing.update'), [
                'payload' => json_encode(['hero' => ['headline' => 'Updated Headline']]),
            ])
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseHas('system_settings', [
            'key' => 'landing',
        ]);

        $service = app(PublicLandingService::class);
        $service->clearCache();
        $landing = $service->forPublicDomain();

        $this->assertSame('Updated Headline', $landing['hero']['headline']);
    }

    public function test_invalid_json_returns_error(): void
    {
        $user = User::factory()->create(['email' => 'owner@example.com']);
        config(['auth.super_admin_emails' => [$user->email]]);

        $response = $this->actingAs($user)
            ->from(route('admin.settings.landing.edit'))
            ->post(route('admin.settings.landing.update'), ['payload' => 'not-json']);

        $response->assertRedirect(route('admin.settings.landing.edit'));
        $response->assertSessionHasErrors('payload');
        $this->assertDatabaseCount('system_settings', 0);
    }
}
