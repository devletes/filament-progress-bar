<?php

namespace Workbench\App\Filament\Pages;

use Devletes\FilamentProgressBar\Support\ProgressBarData;
use Devletes\FilamentProgressBar\Support\ProgressBarResolver;
use Filament\Pages\Page;

class Showcase extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationLabel = 'Showcase';

    protected static ?string $title = 'Progress Bar Showcase';

    protected string $view = 'filament.pages.showcase';

    /** @return array<string, array<int, array{data: ProgressBarData, caption: ?string}>> */
    public function getDemos(): array
    {
        $resolver = app(ProgressBarResolver::class);

        $base = static fn (array $overrides = []): array => array_merge([
            'state' => ['progress' => 50, 'total' => 100],
            'maxValue' => null,
            'thresholds' => [],
            'colors' => [],
            'labels' => [],
            'showsPercentage' => true,
            'showsProgressValue' => true,
            'textPosition' => 'inside',
            'size' => 'sm',
            'borderRadius' => null,
        ], $overrides);

        $make = static fn (array $args, ?string $caption = null): array => [
            'data' => app(ProgressBarResolver::class)->resolve(...$base($args)),
            'caption' => $caption,
        ];

        return [
            'sizes' => [
                $make(['size' => 'sm'], "->size('sm')"),
                $make(['size' => 'md'], "->size('md')"),
                $make(['size' => 'lg'], "->size('lg')"),
            ],
            'text-position' => [
                $make(['textPosition' => 'inside'], "->textPosition('inside')"),
                $make(['textPosition' => 'outside'], "->textPosition('outside')"),
            ],
            'visibility' => [
                $make([], 'default (value + percentage)'),
                $make(['showsPercentage' => false], '->hidePercentage()'),
                $make(['showsProgressValue' => false], '->hideProgressValue()'),
                $make(['showsPercentage' => false, 'showsProgressValue' => false], '->hidePercentage()->hideProgressValue()'),
            ],
            'border-radius' => [
                $make([], 'default (pill)'),
                $make(['borderRadius' => '4px'], "->borderRadius('4px')"),
                $make(['borderRadius' => '0'], "->borderRadius('0')"),
            ],
            'thresholds-default' => [
                $make(['state' => ['progress' => 30, 'total' => 100]], '30% → success'),
                $make(['state' => ['progress' => 75, 'total' => 100]], '75% → warning'),
                $make(['state' => ['progress' => 95, 'total' => 100]], '95% → danger'),
            ],
            'thresholds-descending' => [
                $make([
                    'state' => ['progress' => 80, 'total' => 100],
                    'thresholds' => ['direction' => 'descending', 'warning' => 30, 'danger' => 10],
                ], '80% → success (plenty left)'),
                $make([
                    'state' => ['progress' => 25, 'total' => 100],
                    'thresholds' => ['direction' => 'descending', 'warning' => 30, 'danger' => 10],
                ], '25% → warning (running low)'),
                $make([
                    'state' => ['progress' => 5, 'total' => 100],
                    'thresholds' => ['direction' => 'descending', 'warning' => 30, 'danger' => 10],
                ], '5% → danger (critical)'),
            ],
            'thresholds-map' => [
                $make([
                    'state' => ['progress' => 90, 'total' => 100],
                    'thresholds' => ['mode' => 'map', 'map' => [80 => 'success', 60 => 'warning', 40 => 'info', 0 => 'danger']],
                ], '90% → success'),
                $make([
                    'state' => ['progress' => 70, 'total' => 100],
                    'thresholds' => ['mode' => 'map', 'map' => [80 => 'success', 60 => 'warning', 40 => 'info', 0 => 'danger']],
                ], '70% → warning'),
                $make([
                    'state' => ['progress' => 50, 'total' => 100],
                    'thresholds' => ['mode' => 'map', 'map' => [80 => 'success', 60 => 'warning', 40 => 'info', 0 => 'danger']],
                ], '50% → info'),
                $make([
                    'state' => ['progress' => 20, 'total' => 100],
                    'thresholds' => ['mode' => 'map', 'map' => [80 => 'success', 60 => 'warning', 40 => 'info', 0 => 'danger']],
                ], '20% → danger'),
            ],
            'recipe-battery' => [
                $make([
                    'state' => ['progress' => 82, 'total' => 100],
                    'thresholds' => ['direction' => 'descending', 'warning' => 30, 'danger' => 10],
                ], 'Battery 82% — full'),
                $make([
                    'state' => ['progress' => 22, 'total' => 100],
                    'thresholds' => ['direction' => 'descending', 'warning' => 30, 'danger' => 10],
                ], 'Battery 22% — low'),
                $make([
                    'state' => ['progress' => 6, 'total' => 100],
                    'thresholds' => ['direction' => 'descending', 'warning' => 30, 'danger' => 10],
                ], 'Battery 6% — critical'),
            ],
            'recipe-quality' => [
                $make([
                    'state' => ['progress' => 92, 'total' => 100],
                    'thresholds' => ['mode' => 'map', 'map' => [90 => 'success', 70 => 'info', 40 => 'warning', 0 => 'danger']],
                ], 'Quality 92% — excellent'),
                $make([
                    'state' => ['progress' => 75, 'total' => 100],
                    'thresholds' => ['mode' => 'map', 'map' => [90 => 'success', 70 => 'info', 40 => 'warning', 0 => 'danger']],
                ], 'Quality 75% — good'),
                $make([
                    'state' => ['progress' => 55, 'total' => 100],
                    'thresholds' => ['mode' => 'map', 'map' => [90 => 'success', 70 => 'info', 40 => 'warning', 0 => 'danger']],
                ], 'Quality 55% — fair'),
                $make([
                    'state' => ['progress' => 22, 'total' => 100],
                    'thresholds' => ['mode' => 'map', 'map' => [90 => 'success', 70 => 'info', 40 => 'warning', 0 => 'danger']],
                ], 'Quality 22% — poor'),
            ],
            'recipe-squared' => [
                $make([
                    'state' => ['progress' => 35, 'total' => 100],
                    'size' => 'md',
                    'borderRadius' => '4px',
                ], "->size('md')->borderRadius('4px')"),
                $make([
                    'state' => ['progress' => 65, 'total' => 100],
                    'size' => 'md',
                    'borderRadius' => '4px',
                ], "->size('md')->borderRadius('4px')"),
                $make([
                    'state' => ['progress' => 92, 'total' => 100],
                    'size' => 'md',
                    'borderRadius' => '4px',
                ], "->size('md')->borderRadius('4px')"),
            ],
            'recipe-compact' => [
                $make([
                    'state' => ['progress' => 30, 'total' => 100],
                    'showsPercentage' => false,
                    'showsProgressValue' => false,
                ], '30% — color-only, no text'),
                $make([
                    'state' => ['progress' => 75, 'total' => 100],
                    'showsPercentage' => false,
                    'showsProgressValue' => false,
                ], '75% — color-only, no text'),
                $make([
                    'state' => ['progress' => 95, 'total' => 100],
                    'showsPercentage' => false,
                    'showsProgressValue' => false,
                ], '95% — color-only, no text'),
            ],
        ];
    }
}
