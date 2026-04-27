# Changelog

All notable changes to `devletes/filament-progress-bar` will be documented in this file.

## [1.1.0] - 2026-04-26

Fully backwards-compatible with `1.0.x` — no breaking API or visual changes.

### Added

- `thresholdDirection('ascending'|'descending')` for low-is-bad metrics like fuel, stock, or battery.
- Threshold map form: `thresholds([90 => 'success', 60 => 'warning', 0 => 'danger'])` supporting any number of states with custom status names. When the map omits a `0`-floor, the lowest-defined status extends down to `0` instead of falling back to `success`.
- New status names from the threshold map (anything other than `success`/`warning`/`danger`) auto-resolve their default color from your Filament panel via `var(--{status}-500)` — including custom panel colors registered via `->colors([...])`. The three legacy statuses keep their `1.0.x` defaults.
- `statusColors([...])` and `statusLabels([...])` for one-off colors and labels on arbitrary status names.
- `borderRadius(string)` accepting any valid CSS length (`'4px'`, `'0.5rem'`, `'calc(var(--r) * 2)'`, etc.) with sanitization against inline-style injection.

### Changed

- `ProgressBarResolver::resolveStatus()` now accepts a richer config (with `mode`, `direction`, and either `warning`/`danger` or `map` keys). The previous `['warning' => N, 'danger' => N]` input shape still works unchanged.
- `ProgressBarResolver::resolveBaseData()` returns the same outer keys as before; `thresholds` now contains the canonical config rather than the flat tier pair.

### Docs

- README rewritten with light + dark side-by-side screenshots under every feature heading and recipe.
- Workbench gained a dedicated `Showcase` page (`/admin/showcase`) and a Playwright capture script (`scripts/capture-screenshots.mjs`) for regenerating the screenshots.

## [1.0.0] - 2026-03-31

### Added

- Filament 5 progress bar table column for visual progress tracking.
- Matching infolist entry with the same API and resolver behavior.
- Package stylesheet registered through Filament's asset manager for self-contained rendering.
- Compact table column output with richer infolist label and icon support.
- Shared progress resolver covering thresholds, clamping, labels, colors, and safe totals.
- Dark mode friendly Blade views with accessible progress semantics.
- Testbench workbench app for local verification in tables and infolists.
- Pest coverage for resolver behavior, component rendering, and package bootstrapping.
- README screenshots for table and infolist usage.
