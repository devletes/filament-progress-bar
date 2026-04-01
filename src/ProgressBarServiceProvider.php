<?php

namespace Devletes\FilamentProgressBar;

use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ProgressBarServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-progress-bar';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasViews();
    }

    public function packageBooted(): void
    {
        FilamentAsset::register([
            Css::make('progress-bar', __DIR__.'/../resources/css/progress-bar.css'),
        ], 'devletes/filament-progress-bar');
    }
}
