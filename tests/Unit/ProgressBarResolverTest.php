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

    expect($data->color)->toBe('var(--danger-500)')
        ->and($data->label)->toBeNull();
});
