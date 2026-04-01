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
