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
            'innerBorderRadius' => null,
            'height' => null,
            'trackColor' => null,
        ], $overrides);

        $make = static fn (array $args, ?string $caption = null): array => [
            'data' => app(ProgressBarResolver::class)->resolve(...$base($args)),
            'caption' => $caption,
        ];

        $shape = static fn (array $args, ?string $caption = null): array => $make(
            array_merge(['colors' => ['success' => 'var(--primary-500)']], $args),
            $caption,
        );

        return [
            'sizes' => [
                $shape(['size' => 'xs'], "->size('xs') — text hidden"),
                $shape(['size' => 'sm'], "->size('sm')"),
                $shape(['size' => 'md'], "->size('md')"),
                $shape(['size' => 'lg'], "->size('lg')"),
                $shape(['height' => '3rem'], "->height('3rem')"),
            ],
            'text-position' => [
                $shape(['textPosition' => 'inside'], "->textPosition('inside')"),
                $shape(['textPosition' => 'outside'], "->textPosition('outside')"),
            ],
            'visibility' => [
                $shape([], 'default (value + percentage)'),
                $shape(['showsPercentage' => false], '->hidePercentage()'),
                $shape(['showsProgressValue' => false], '->hideProgressValue()'),
                $shape(['showsPercentage' => false, 'showsProgressValue' => false], '->hidePercentage()->hideProgressValue()'),
            ],
            'border-radius' => [
                $shape([], 'default (pill)'),
                $shape(['borderRadius' => '4px'], "->borderRadius('4px')"),
                $shape(['borderRadius' => '0'], "->borderRadius('0')"),
                $shape(['innerBorderRadius' => '0'], "->innerBorderRadius('0')"),
            ],
            'thresholds-default' => [
                $shape(['state' => ['progress' => 30, 'total' => 100]], '30% → success'),
                $shape(['state' => ['progress' => 75, 'total' => 100]], '75% → warning'),
                $shape(['state' => ['progress' => 95, 'total' => 100]], '95% → danger'),
            ],
            'thresholds-descending' => [
                $shape([
                    'state' => ['progress' => 80, 'total' => 100],
                    'thresholds' => ['direction' => 'descending', 'warning' => 30, 'danger' => 10],
                ], '80% → success (plenty left)'),
                $shape([
                    'state' => ['progress' => 25, 'total' => 100],
                    'thresholds' => ['direction' => 'descending', 'warning' => 30, 'danger' => 10],
                ], '25% → warning (running low)'),
                $shape([
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
                $shape([
                    'state' => ['progress' => 82, 'total' => 100],
                    'thresholds' => ['direction' => 'descending', 'warning' => 30, 'danger' => 10],
                ], 'Battery 82% — full'),
                $shape([
                    'state' => ['progress' => 22, 'total' => 100],
                    'thresholds' => ['direction' => 'descending', 'warning' => 30, 'danger' => 10],
                ], 'Battery 22% — low'),
                $shape([
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
                $shape([
                    'state' => ['progress' => 35, 'total' => 100],
                    'size' => 'md',
                    'borderRadius' => '4px',
                ], "->size('md')->borderRadius('4px')"),
                $shape([
                    'state' => ['progress' => 65, 'total' => 100],
                    'size' => 'md',
                    'borderRadius' => '4px',
                ], "->size('md')->borderRadius('4px')"),
                $shape([
                    'state' => ['progress' => 92, 'total' => 100],
                    'size' => 'md',
                    'borderRadius' => '4px',
                ], "->size('md')->borderRadius('4px')"),
            ],
            'recipe-compact' => [
                $shape([
                    'state' => ['progress' => 30, 'total' => 100],
                    'showsPercentage' => false,
                    'showsProgressValue' => false,
                ], '30% — color-only, no text'),
                $shape([
                    'state' => ['progress' => 75, 'total' => 100],
                    'showsPercentage' => false,
                    'showsProgressValue' => false,
                ], '75% — color-only, no text'),
                $shape([
                    'state' => ['progress' => 95, 'total' => 100],
                    'showsPercentage' => false,
                    'showsProgressValue' => false,
                ], '95% — color-only, no text'),
            ],
        ];
    }
}
