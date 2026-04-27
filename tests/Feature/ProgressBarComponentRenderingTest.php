<?php

use Devletes\FilamentProgressBar\Tests\Fixtures\DummySchemasComponent;
use Devletes\FilamentProgressBar\Tests\Fixtures\TestProgressBarColumn;
use Devletes\FilamentProgressBar\Tests\Fixtures\TestProgressBarEntry;
use Filament\Schemas\Schema;

it('renders the table column with shared progress markup', function (): void {
    $column = TestProgressBarColumn::fake('usage', ['progress' => 32, 'total' => 40])
        ->textPosition('outside')
        ->warningLabel(fn (int $percentage): string => "Usage {$percentage}%");

    $html = view('filament-progress-bar::tables.columns.progress-bar-column', [
        'column' => $column,
    ])->render();

    expect($html)->toContain('role="progressbar"')
        ->and($html)->not->toContain('Usage 80%')
        ->and($html)->toContain('32 / 40 (80%)');
});

it('renders the infolist entry and supports hiding text output', function (): void {
    $schema = Schema::make(new DummySchemasComponent);

    $entry = TestProgressBarEntry::make('usage')
        ->state(['progress' => 12, 'total' => 20])
        ->hideProgressValue()
        ->hidePercentage()
        ->container($schema);

    $html = $entry->toHtml();

    expect($html)->toContain('role="progressbar"')
        ->and($html)->not->toContain('12 / 20')
        ->and($html)->not->toContain('60%)')
        ->and($html)->not->toContain('12 / 20 (60%)');
});

it('renders dark mode friendly neutral track classes', function (): void {
    $html = view('filament-progress-bar::tables.columns.progress-bar-column', [
        'column' => TestProgressBarColumn::fake('usage', ['progress' => 10, 'total' => 20])->textPosition('outside'),
    ])->render();

    expect($html)->toContain('class="fpb-track fpb-bar--sm"')
        ->and($html)->toContain('class="fpb-outside-text fpb-outside-text--sm"');
});

it('supports closure based colors and labels', function (): void {
    $schema = Schema::make(new DummySchemasComponent);

    $entry = TestProgressBarEntry::make('usage')
        ->state(['progress' => 95, 'total' => 100])
        ->dangerColor(fn (): string => '#111111')
        ->dangerLabel(fn (float $current, ?float $total): string => 'High usage '.(int) $current.'/'.(int) $total)
        ->container($schema);

    $html = $entry->toHtml();

    expect($html)->toContain('High usage 95/100')
        ->and($html)->toContain('#111111');
});

it('supports infolist labels and inline labels through the filament wrapper', function (): void {
    $schema = Schema::make(new DummySchemasComponent);

    $entry = TestProgressBarEntry::make('usage')
        ->label('Usage')
        ->inlineLabel()
        ->state(['progress' => 8, 'total' => 10])
        ->container($schema);

    $html = $entry->toHtml();

    expect($html)->toContain('Usage')
        ->and($html)->toContain('fi-in-entry')
        ->and($html)->toContain('fi-in-entry-has-inline-label');
});

it('supports rendering an icon within the infolist label', function (): void {
    $schema = Schema::make(new DummySchemasComponent);

    $entry = TestProgressBarEntry::make('usage')
        ->label('Sick Leave')
        ->icon('heroicon-o-heart')
        ->inlineLabel()
        ->state(['progress' => 8, 'total' => 10])
        ->container($schema);

    $html = $entry->toHtml();

    expect($html)->toContain('Sick Leave')
        ->and($html)->toContain('fi-icon fi-size-sm h-4 w-4 shrink-0')
        ->and($html)->toContain('fpb-entry-label');
});

it('can render the progress text inside the bar', function (): void {
    $html = view('filament-progress-bar::tables.columns.progress-bar-column', [
        'column' => TestProgressBarColumn::fake('usage', ['progress' => 12, 'total' => 20]),
    ])->render();

    expect($html)->toContain('12 / 20 (60%)')
        ->and($html)->toContain('class="fpb-inside"')
        ->and($html)->not->toContain('class="fpb-outside-row"')
        ->and($html)->toContain('fpb-bar--sm');
});

it('supports progress bar sizing', function (): void {
    $html = view('filament-progress-bar::tables.columns.progress-bar-column', [
        'column' => TestProgressBarColumn::fake('usage', ['progress' => 5, 'total' => 10])
            ->size('lg')
            ->textPosition('outside'),
    ])->render();

    expect($html)->toContain('fpb-bar--lg')
        ->and($html)->toContain('fpb-outside-text--lg');
});

it('falls back to default values when invalid size or text position values are provided', function (): void {
    $html = view('filament-progress-bar::tables.columns.progress-bar-column', [
        'column' => TestProgressBarColumn::fake('usage', ['progress' => 5, 'total' => 10])
            ->size('huge')
            ->textPosition('middle'),
    ])->render();

    expect($html)->toContain('fpb-bar--sm')
        ->and($html)->toContain('class="fpb-inside"')
        ->and($html)->not->toContain('fpb-bar--lg');
});

it('emits a custom border radius via the --fpb-radius css variable', function (): void {
    $html = view('filament-progress-bar::tables.columns.progress-bar-column', [
        'column' => TestProgressBarColumn::fake('usage', ['progress' => 5, 'total' => 10])
            ->borderRadius('4px'),
    ])->render();

    expect($html)->toContain('--fpb-radius: 4px;');
});

it('omits the border radius style attribute when no radius is set', function (): void {
    $html = view('filament-progress-bar::tables.columns.progress-bar-column', [
        'column' => TestProgressBarColumn::fake('usage', ['progress' => 5, 'total' => 10]),
    ])->render();

    expect($html)->not->toContain('--fpb-radius');
});

it('strips unsafe border radius values to prevent style injection', function (): void {
    $html = view('filament-progress-bar::tables.columns.progress-bar-column', [
        'column' => TestProgressBarColumn::fake('usage', ['progress' => 5, 'total' => 10])
            ->borderRadius('4px; color: red'),
    ])->render();

    expect($html)->not->toContain('--fpb-radius')
        ->and($html)->not->toContain('color: red');
});

it('treats a low percentage as danger when threshold direction is descending', function (): void {
    $html = view('filament-progress-bar::tables.columns.progress-bar-column', [
        'column' => TestProgressBarColumn::fake('fuel', ['progress' => 5, 'total' => 100])
            ->thresholdDirection('descending')
            ->warningThreshold(30)
            ->dangerThreshold(10)
            ->dangerColor('rgb(220 38 38)'),
    ])->render();

    expect($html)->toContain('rgb(220 38 38)');
});

it('lets statusColors override the named color setter for the same status', function (): void {
    $html = view('filament-progress-bar::tables.columns.progress-bar-column', [
        'column' => TestProgressBarColumn::fake('usage', ['progress' => 95, 'total' => 100])
            ->dangerColor('rgb(0 0 0)')
            ->statusColors(['danger' => 'rgb(220 38 38)']),
    ])->render();

    expect($html)->toContain('rgb(220 38 38)')
        ->and($html)->not->toContain('rgb(0 0 0)');
});

it('falls back to the named color setter when statusColors omits the status', function (): void {
    $html = view('filament-progress-bar::tables.columns.progress-bar-column', [
        'column' => TestProgressBarColumn::fake('usage', ['progress' => 95, 'total' => 100])
            ->dangerColor('rgb(0 0 0)')
            ->statusColors(['warning' => 'rgb(249 115 22)']),
    ])->render();

    expect($html)->toContain('rgb(0 0 0)');
});

it('uses a threshold map with custom statuses and statusColors', function (): void {
    $html = view('filament-progress-bar::tables.columns.progress-bar-column', [
        'column' => TestProgressBarColumn::fake('score', ['progress' => 55, 'total' => 100])
            ->thresholds([
                80 => 'success',
                60 => 'warning',
                40 => 'info',
                0 => 'danger',
            ])
            ->statusColors([
                'success' => 'rgb(16 185 129)',
                'warning' => 'rgb(249 115 22)',
                'info' => 'rgb(59 130 246)',
                'danger' => 'rgb(220 38 38)',
            ]),
    ])->render();

    expect($html)->toContain('rgb(59 130 246)');
});
