# Changelog

All notable changes to `devletes/filament-progress-bar` will be documented in this file.

## [1.2.0] - 2026-09-10

No breaking API changes from `1.1.x`. One deliberate visual change — see **Changed**.

### Added

- `size('xs')`, a bar-only preset at `1rem`. There is no room to stack text legibly on a bar that short, so an `xs` bar hides its text unless `textPosition('outside')` moves it below the bar; the value still reaches screen readers via `aria-valuetext`. The `sm`/`md`/`lg` presets are unchanged.
- `height(string)` accepting any valid CSS length (`'0.5rem'`, `'48px'`, `'clamp(1rem, 4vw, 4rem)'`, etc.) to set the bar height beyond the `size()` presets. `size()` keeps driving the text sizing; `height()` overrides only the bar height.
- `innerBorderRadius(string)` shaping the fill independently of the track. Defaults to whatever `borderRadius()` resolves to, so existing bars are unchanged; `innerBorderRadius('0')` ends the fill in a flat vertical edge inside a still-rounded track.
- `trackColor(string)` accepting any valid CSS color value — including custom properties such as `var(--primary-100)` — to replace the neutral gray track. A custom track color applies in both light and dark mode; pass a per-mode CSS variable to keep the track theme-aware.
- All three accept a closure and are sanitized against inline-style injection the same way `borderRadius()` is.

### Changed

- The `success` status now defaults to `var(--success-500)` instead of `var(--primary-500)`, so a healthy bar renders in Filament's semantic success color (green on a default panel) rather than the panel's brand color. `warning` and `danger` are unchanged. To keep the previous look, pass `->successColor('var(--primary-500)')` (or `->statusColors(['success' => 'var(--primary-500)'])`).
- `ProgressBarResolver::defaultColorForStatus()` no longer special-cases `success`; it now resolves through the same `var(--{status}-500, var(--primary-500))` path as every other status name.

### Docs

- README documents the new options alongside their siblings: `height(...)` in **Size**, `innerBorderRadius(...)` in **Border radius**, and `trackColor(...)` in **Colors**, plus matching API reference rows. The **Size** and **Border radius** screenshots were regenerated to include a custom-height and an inner-radius example.
- The workbench table and infolist integration examples exercise all three new options.
- The new `success` color shows up in the two screenshots whose example names `'success'` in a threshold map (**Threshold map** and the **Multi-state quality score** recipe), where it also keeps `success` distinct from the `info` state's blue. The remaining demos pin `success` to the panel's brand color, so screenshots about shape rather than status look unchanged.

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
