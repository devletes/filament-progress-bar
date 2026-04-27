<?php

use Devletes\FilamentProgressBar\Support\ProgressBarResolver;

it('resolves percentage from numeric state and max value', function (): void {
    $resolver = new ProgressBarResolver;

    $data = $resolver->resolve(
        state: 25,
        maxValue: 50,
        thresholds: ['warning' => 70, 'danger' => 90],
        colors: ['success' => 'green', 'warning' => 'yellow', 'danger' => 'red'],
        labels: ['success' => null, 'warning' => null, 'danger' => null],
        showsPercentage: true,
        showsProgressValue: true,
        textPosition: 'outside',
        size: 'md',
    );

    expect($data->percentage)->toBe(50)
        ->and($data->status)->toBe('success')
        ->and($data->getProgressValueText())->toBe('25 / 50')
        ->and($data->getPercentageText())->toBe('50%');
});

it('supports structured state and chooses warning and danger thresholds by percentage', function (): void {
    $resolver = new ProgressBarResolver;

    $warning = $resolver->resolve(
        state: ['progress' => 78, 'total' => 100],
        maxValue: null,
        thresholds: ['warning' => 70, 'danger' => 90],
        colors: ['success' => 'green', 'warning' => 'yellow', 'danger' => 'red'],
        labels: ['success' => null, 'warning' => 'Watch', 'danger' => 'Critical'],
        showsPercentage: true,
        showsProgressValue: false,
        textPosition: 'outside',
        size: 'md',
    );

    $danger = $resolver->resolve(
        state: ['progress' => 95, 'total' => 100],
        maxValue: null,
        thresholds: ['warning' => 70, 'danger' => 90],
        colors: ['success' => 'green', 'warning' => 'yellow', 'danger' => 'red'],
        labels: ['success' => null, 'warning' => 'Watch', 'danger' => 'Critical'],
        showsPercentage: true,
        showsProgressValue: false,
        textPosition: 'outside',
        size: 'md',
    );

    expect($warning->status)->toBe('warning')
        ->and($warning->label)->toBe('Watch')
        ->and($danger->status)->toBe('danger')
        ->and($danger->label)->toBe('Critical');
});

it('clamps percentages and handles zero or empty totals safely', function (): void {
    $resolver = new ProgressBarResolver;

    $overflow = $resolver->resolveBaseData(['progress' => 200, 'total' => 100], null, ['warning' => 70, 'danger' => 90]);
    $zeroTotal = $resolver->resolveBaseData(['progress' => 20, 'total' => 0], null, ['warning' => 70, 'danger' => 90]);
    $negative = $resolver->resolveBaseData(['progress' => -10, 'total' => 100], null, ['warning' => 70, 'danger' => 90]);

    expect($overflow['percentage'])->toBe(100)
        ->and($zeroTotal['percentage'])->toBe(0)
        ->and($negative['current'])->toBe(0.0)
        ->and($negative['percentage'])->toBe(0);
});

it('supports fallback state keys', function (): void {
    $resolver = new ProgressBarResolver;

    $data = $resolver->resolveBaseData(
        ['value' => 6, 'quota' => 8],
        null,
        ['warning' => 70, 'danger' => 90],
    );

    expect($data['current'])->toBe(6.0)
        ->and($data['total'])->toBe(8.0)
        ->and($data['percentage'])->toBe(75)
        ->and($data['status'])->toBe('warning');
});

it('normalizes invalid threshold ordering', function (): void {
    $resolver = new ProgressBarResolver;

    $thresholds = $resolver->normalizeThresholds(['warning' => 85, 'danger' => 70]);

    expect($thresholds)->toBe([
        'warning' => 85,
        'danger' => 85,
    ]);
});

it('falls back to default thresholds when invalid values are provided', function (): void {
    $resolver = new ProgressBarResolver;

    $thresholds = $resolver->normalizeThresholds(['warning' => 'later', 'danger' => ['nope']]);

    expect($thresholds)->toBe([
        'warning' => 70,
        'danger' => 90,
    ]);
});

it('falls back to default colors and labels when invalid values are provided', function (): void {
    $resolver = new ProgressBarResolver;

    $data = $resolver->resolve(
        state: ['progress' => 95, 'total' => 100],
        maxValue: null,
        thresholds: ['warning' => 70, 'danger' => 90],
        colors: ['success' => '', 'warning' => ['bad'], 'danger' => ' '],
        labels: ['success' => [], 'warning' => '   ', 'danger' => 123],
        showsPercentage: true,
        showsProgressValue: true,
        textPosition: 'inside',
        size: 'sm',
    );

    expect($data->color)->toBe('var(--danger-500, var(--primary-500))')
        ->and($data->label)->toBeNull();
});

it('flips threshold comparison when direction is descending', function (): void {
    $resolver = new ProgressBarResolver;

    $low = $resolver->resolve(
        state: 5,
        maxValue: 100,
        thresholds: ['direction' => 'descending', 'warning' => 30, 'danger' => 10],
        colors: ['success' => 'green', 'warning' => 'yellow', 'danger' => 'red'],
        labels: ['success' => null, 'warning' => null, 'danger' => null],
        showsPercentage: true,
        showsProgressValue: false,
        textPosition: 'inside',
        size: 'sm',
    );

    $mid = $resolver->resolve(
        state: 25,
        maxValue: 100,
        thresholds: ['direction' => 'descending', 'warning' => 30, 'danger' => 10],
        colors: ['success' => 'green', 'warning' => 'yellow', 'danger' => 'red'],
        labels: ['success' => null, 'warning' => null, 'danger' => null],
        showsPercentage: true,
        showsProgressValue: false,
        textPosition: 'inside',
        size: 'sm',
    );

    $high = $resolver->resolve(
        state: 80,
        maxValue: 100,
        thresholds: ['direction' => 'descending', 'warning' => 30, 'danger' => 10],
        colors: ['success' => 'green', 'warning' => 'yellow', 'danger' => 'red'],
        labels: ['success' => null, 'warning' => null, 'danger' => null],
        showsPercentage: true,
        showsProgressValue: false,
        textPosition: 'inside',
        size: 'sm',
    );

    expect($low->status)->toBe('danger')
        ->and($mid->status)->toBe('warning')
        ->and($high->status)->toBe('success');
});

it('clamps descending thresholds so danger never exceeds warning', function (): void {
    $resolver = new ProgressBarResolver;

    $config = $resolver->normalizeThresholdConfig([
        'direction' => 'descending',
        'warning' => 30,
        'danger' => 50,
    ]);

    expect($config['warning'])->toBe(30)
        ->and($config['danger'])->toBe(30);
});

it('resolves status from a threshold map with custom statuses', function (): void {
    $resolver = new ProgressBarResolver;

    $thresholds = [
        'mode' => 'map',
        'map' => [
            80 => 'success',
            60 => 'warning',
            40 => 'info',
            0 => 'danger',
        ],
    ];

    $excellent = $resolver->resolve(
        state: 90,
        maxValue: 100,
        thresholds: $thresholds,
        colors: ['success' => 'green', 'warning' => 'orange', 'info' => 'blue', 'danger' => 'red'],
        labels: ['info' => 'Watch'],
        showsPercentage: false,
        showsProgressValue: false,
        textPosition: 'inside',
        size: 'sm',
    );

    $info = $resolver->resolve(
        state: 50,
        maxValue: 100,
        thresholds: $thresholds,
        colors: ['success' => 'green', 'warning' => 'orange', 'info' => 'blue', 'danger' => 'red'],
        labels: ['info' => 'Watch'],
        showsPercentage: false,
        showsProgressValue: false,
        textPosition: 'inside',
        size: 'sm',
    );

    $danger = $resolver->resolve(
        state: 10,
        maxValue: 100,
        thresholds: $thresholds,
        colors: ['success' => 'green', 'warning' => 'orange', 'info' => 'blue', 'danger' => 'red'],
        labels: ['info' => 'Watch'],
        showsPercentage: false,
        showsProgressValue: false,
        textPosition: 'inside',
        size: 'sm',
    );

    expect($excellent->status)->toBe('success')
        ->and($excellent->color)->toBe('green')
        ->and($info->status)->toBe('info')
        ->and($info->color)->toBe('blue')
        ->and($info->label)->toBe('Watch')
        ->and($danger->status)->toBe('danger')
        ->and($danger->color)->toBe('red');
});

it('extends the lowest defined status down to 0 when the map omits a 0-floor', function (): void {
    $resolver = new ProgressBarResolver;

    $thresholds = [
        'mode' => 'map',
        'map' => [
            80 => 'success',
            60 => 'warning',
            10 => 'info',
        ],
    ];

    $belowGap = $resolver->resolve(
        state: 5,
        maxValue: 100,
        thresholds: $thresholds,
        colors: [],
        labels: [],
        showsPercentage: false,
        showsProgressValue: false,
        textPosition: 'inside',
        size: 'sm',
    );

    $atGap = $resolver->resolve(
        state: 10,
        maxValue: 100,
        thresholds: $thresholds,
        colors: [],
        labels: [],
        showsPercentage: false,
        showsProgressValue: false,
        textPosition: 'inside',
        size: 'sm',
    );

    expect($belowGap->status)->toBe('info')
        ->and($atGap->status)->toBe('info');
});

it('derives a default color from the status name so Filament panel colors just work', function (): void {
    $resolver = new ProgressBarResolver;

    $data = $resolver->resolve(
        state: 55,
        maxValue: 100,
        thresholds: ['mode' => 'map', 'map' => [50 => 'info', 0 => 'danger']],
        colors: [],
        labels: [],
        showsPercentage: false,
        showsProgressValue: false,
        textPosition: 'inside',
        size: 'sm',
    );

    expect($data->status)->toBe('info')
        ->and($data->color)->toBe('var(--info-500, var(--primary-500))');
});

it('falls back to primary when the status name contains unsafe characters', function (): void {
    $resolver = new ProgressBarResolver;

    $data = $resolver->resolve(
        state: 50,
        maxValue: 100,
        thresholds: ['mode' => 'map', 'map' => [0 => 'oops); color: red']],
        colors: [],
        labels: [],
        showsPercentage: false,
        showsProgressValue: false,
        textPosition: 'inside',
        size: 'sm',
    );

    expect($data->color)->toBe('var(--primary-500)');
});

it('passes through valid border radius values and rejects unsafe ones', function (): void {
    $resolver = new ProgressBarResolver;

    expect($resolver->normalizeBorderRadius('4px'))->toBe('4px')
        ->and($resolver->normalizeBorderRadius('  0.5rem '))->toBe('0.5rem')
        ->and($resolver->normalizeBorderRadius('calc(var(--r) * 2)'))->toBe('calc(var(--r) * 2)')
        ->and($resolver->normalizeBorderRadius('4px; color: red'))->toBeNull()
        ->and($resolver->normalizeBorderRadius('"><script>'))->toBeNull()
        ->and($resolver->normalizeBorderRadius(null))->toBeNull()
        ->and($resolver->normalizeBorderRadius(''))->toBeNull();
});
