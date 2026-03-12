# Unit Official Info (Gov / Land Registry) — Review Notes

Context: commit `cfcbd83` (“add gov info for units”) + migration `2026_01_19_112417_create_unit_official_infos_table`.

## What Was Added

- DB table: `unit_official_infos`
  - `unit_id` is `unique()` (1:1) and `cascadeOnDelete()`
  - Fields: `directorate`, `village`, `basin_number`, `basin_name`, `plot_number`, `apartment_number`, `areas` (JSON)
- Model: `app/Models/UnitOfficialInfo.php`
  - `$fillable` for the columns above
  - `areas` cast to `array`
- Relation: `Unit::officialInfo()` added in `app/Models/Unit.php`
- UI: “Official Property Info” section added to `resources/views/units/create.blade.php`

## Current Wiring (How It Works Now)

- UI posts fields under:
  - `official[directorate]`, `official[village]`, …
  - `official[areas][land_sqm]`, `official[areas][built_sqm]`, `official[areas][total_sqm]`, `official[areas][notes]`
- Controller (`app/Http/Controllers/UnitController.php`):
  - Validates official fields inside `store()`
  - Creates `Unit`
  - Calls `$unit->officialInfo()->updateOrCreate(['unit_id' => $unit->id], $officialData)`

## Issues / Gaps (Must Fix)

### 1) Official info save mismatch (data shape)

- Validation uses keys like `official.directorate`, `official.village`, etc, so the validated array structure is nested under `official`.
- But `UnitOfficialInfo::$fillable` expects **flat** attributes (`directorate`, `village`, …).
- Passing `$officialData` directly to `updateOrCreate()` will not map correctly (likely resulting in mostly-null columns).

### 2) Areas validation keys don’t match UI payload

- UI posts `official[areas][...]`
- Controller validates `areas.*` (missing the `official.` prefix), so areas won’t validate / capture as intended.

### 3) “Apartment Number (optional)” is required in backend

- Migration allows `apartment_number` nullable.
- UI labels apartment number as optional.
- Controller validates `official.apartment_number` as `required` — inconsistent UX + unnecessary failures.

### 4) No update support for official info

- `store()` attempts to create official info.
- `update()` does not update `officialInfo` yet, so edits to official fields won’t persist.

### 5) Alpine is disabled, but unit create relies on Alpine

- `resources/js/app.js` has Alpine (and bootstrap) commented out.
- The layout also has the Alpine CDN script commented.
- `units/create.blade.php` uses `x-data`, `x-init`, etc for subcategory + dynamic attributes, so those sections won’t work unless Alpine is re-enabled.

### 6) “Location” field is not persisted

- `units/create.blade.php` includes an input `name="location"`.
- `StoreUnitRequest` does not validate it, and `Unit::$fillable` doesn’t include `location`, so it will be dropped.

### 7) Dynamic attributes label binding is broken

- In `units/create.blade.php` the attribute label uses `x-text="field"` (will render as `[object Object]`).
- `_form-scripts.blade.php` already provides `getLabel(field)` to display a proper label.

## Suggested Fix Order (Smallest Safe Steps)

1. Decide whether official info is required or optional (per tenant? per listing type?).
2. Align validation keys with UI payload:
   - `official.areas.*` (instead of `areas.*`)
   - make `official.apartment_number` nullable if truly optional
3. Flatten the validated `official` array before saving:
   - save `directorate`, `village`, … + `areas` as JSON/array
4. Add official info handling to `UnitController@update` (and to the edit UI if needed).
5. Re-enable Alpine (Vite or CDN) so `subcatSetup` and `dynamicAttributes` work.
6. Decide what “location” should be:
   - remove it from UI if not needed, or add a real column / mapping if needed.
7. Fix the attribute label binding to use `getLabel(field)`.

## Questions To Confirm

- Should official info be mandatory for all units, or only when the user chooses “I have official data”?
- Should we store official info only for sale listings (or both rent + sale)?
- Do we need official info in `units/edit` as well (full CRUD)?
- What exactly should “Location” represent (string address, or city/state IDs, or map coords)?
