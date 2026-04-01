<?php

namespace Devletes\FilamentProgressBar\Support;

class ProgressBarResolver
{
    /**
     * @param  array<string, mixed>  $thresholds
     * @return array{current: float, total: ?float, percentage: int, status: string, thresholds: array{warning: int, danger: int}}
     */
    public function resolveBaseData(mixed $state, mixed $maxValue, array $thresholds): array
    {
        $current = $this->resolveCurrent($state);
        $total = $this->resolveTotal($state, $maxValue);
        $percentage = $this->resolvePercentage($current, $total);
        $thresholds = $this->normalizeThresholds($thresholds);

        return [
            'current' => $current,
            'total' => $total,
            'percentage' => $percentage,
            'status' => $this->resolveStatus($percentage, $thresholds),
            'thresholds' => $thresholds,
        ];
    }

    /**
     * @param  array<string, mixed>  $thresholds
     * @param  array<string, mixed>  $colors
     * @param  array<string, mixed>  $labels
     */
    public function resolve(
        mixed $state,
        mixed $maxValue,
        array $thresholds,
        array $colors,
        array $labels,
        bool $showsPercentage,
        bool $showsProgressValue,
        string $textPosition,
        string $size,
    ): ProgressBarData {
        $base = $this->resolveBaseData($state, $maxValue, $thresholds);
        $status = $base['status'];
        $colors = $this->normalizeColors($colors);
        $labels = $this->normalizeLabels($labels);

        return new ProgressBarData(
            current: $base['current'],
            total: $base['total'],
            percentage: $base['percentage'],
            status: $status,
            color: $colors[$status],
            label: $labels[$status] ?? null,
            showsPercentage: $showsPercentage,
            showsProgressValue: $showsProgressValue,
            textPosition: $this->normalizeTextPosition($textPosition),
            size: $this->normalizeSize($size),
        );
    }

    public function selectByStatus(string $status, mixed $success, mixed $warning, mixed $danger): mixed
    {
        return match ($status) {
            'danger' => $danger,
            'warning' => $warning,
            default => $success,
        };
    }

    /**
     * @param  array<string, mixed>  $thresholds
     * @return array{warning: int, danger: int}
     */
    public function normalizeThresholds(array $thresholds): array
    {
        $warning = $this->normalizeThreshold($thresholds['warning'] ?? null, 70);
        $danger = $this->normalizeThreshold($thresholds['danger'] ?? null, 90);

        if ($danger < $warning) {
            $danger = $warning;
        }

        return [
            'warning' => $warning,
            'danger' => $danger,
        ];
    }

    /**
     * @param  array<string, mixed>  $colors
     * @return array{success: string, warning: string, danger: string}
     */
    public function normalizeColors(array $colors): array
    {
        return [
            'success' => $this->normalizeColor($colors['success'] ?? null, 'var(--primary-500)'),
            'warning' => $this->normalizeColor($colors['warning'] ?? null, 'var(--warning-500)'),
            'danger' => $this->normalizeColor($colors['danger'] ?? null, 'var(--danger-500)'),
        ];
    }

    /**
     * @param  array<string, mixed>  $labels
     * @return array{success: ?string, warning: ?string, danger: ?string}
     */
    public function normalizeLabels(array $labels): array
    {
        return [
            'success' => $this->normalizeLabel($labels['success'] ?? null),
            'warning' => $this->normalizeLabel($labels['warning'] ?? null),
            'danger' => $this->normalizeLabel($labels['danger'] ?? null),
        ];
    }

    /**
     * @param  array{warning: int, danger: int}  $thresholds
     */
    public function resolveStatus(int $percentage, array $thresholds): string
    {
        if ($percentage >= $thresholds['danger']) {
            return 'danger';
        }

        if ($percentage >= $thresholds['warning']) {
            return 'warning';
        }

        return 'success';
    }

    public function resolvePercentage(float $current, ?float $total): int
    {
        if (($total === null) || ($total <= 0)) {
            return 0;
        }

        return $this->clampPercentage(($current / $total) * 100);
    }

    public function clampPercentage(float $percentage): int
    {
        return (int) round(max(0, min(100, $percentage)));
    }

    public function normalizeTextPosition(mixed $position): string
    {
        return in_array($position, ['inside', 'outside'], true) ? $position : 'inside';
    }

    public function normalizeSize(mixed $size): string
    {
        return in_array($size, ['sm', 'md', 'lg'], true) ? $size : 'sm';
    }

    public function normalizeColor(mixed $color, string $default): string
    {
        if (! is_string($color)) {
            return $default;
        }

        $color = trim($color);

        return $color !== '' ? $color : $default;
    }

    public function normalizeLabel(mixed $label): ?string
    {
        if (! is_string($label)) {
            return null;
        }

        $label = trim($label);

        return $label !== '' ? $label : null;
    }

    public function normalizeThreshold(mixed $threshold, int $default): int
    {
        if (! is_numeric($threshold)) {
            return $default;
        }

        return $this->clampPercentage((float) $threshold);
    }

    public function resolveCurrent(mixed $state): float
    {
        if (is_numeric($state)) {
            return max(0.0, (float) $state);
        }

        if (! is_array($state)) {
            return 0.0;
        }

        foreach (['progress', 'current', 'value', 'used'] as $key) {
            if (is_numeric($state[$key] ?? null)) {
                return max(0.0, (float) $state[$key]);
            }
        }

        return 0.0;
    }

    public function resolveTotal(mixed $state, mixed $maxValue): ?float
    {
        if (is_array($state)) {
            foreach (['total', 'max', 'available', 'quota'] as $key) {
                if (is_numeric($state[$key] ?? null)) {
                    $value = (float) $state[$key];

                    return $value > 0 ? $value : null;
                }
            }
        }

        if (! is_numeric($maxValue)) {
            return null;
        }

        $maxValue = (float) $maxValue;

        return $maxValue > 0 ? $maxValue : null;
    }
}
