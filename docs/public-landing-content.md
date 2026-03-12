# Public Landing Page Dynamic Plan

## 1. Content Structure

All landing content is now stored in `config/public_site_landing.php`. Sections include:
- `meta`: page title override.
- `theme`: brand colors and background colour.
- `assets`: hero image, logo, screenshot gallery, client logos.
- `navigation`: array of nav links (label, href, variant).
- `hero`: headline, subheadline, call-to-action buttons.
- `features`: intro cards, comparison columns, screenshot block.
- `pricing`: plans (name, price, features, highlighted flag).
- `social_proof`: testimonials and logo strip.
- `cta`: primary/secondary CTAs.
- `footer`: copyright and policy links.

## 2. Service Layer

`App\Services\PublicLandingService` merges defaults (and future overrides) and caches the payload. TODO: wire to super-admin persistence and bust cache on update.

## 3. Blade Layout

`resources/views/layouts/public-site.blade.php` consumes `$landing` for all sections; categories are injected separately. No additional partials required—the template is modular and data-driven.

## 4. Admin Editing

- `admin/settings/landing` (super admin only) exposes a JSON editor to override landing content.
- Payload is stored in the `system_settings` table with key `landing`.
- `PublicLandingService::clearCache()` is called after updates so changes reflect instantly.

## 5. Tests

- `tests/Feature/PublicLandingTest.php` covers rendering defaults and service behaviour.
- `tests/Feature/Admin/LandingSettingsControllerTest.php` covers super admin updates and validation.

## 6. Next Steps

- Enhance the admin UI with granular fields and previews.
- Add translations and image-upload support.
- Cache busting already in place; extend to tenant overrides if needed.
