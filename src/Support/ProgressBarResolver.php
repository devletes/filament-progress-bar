<?php

namespace Devletes\FilamentProgressBar\Support;

class ProgressBarResolver
{
    /**
     * @param  array<string, mixed>  $thresholds
     * @return array{current: float, total: ?float, percentage: int, status: string, thresholds: array<string, mixed>}
     */
    public function resolveBaseData(mixed $state, mixed $maxValue, array $thresholds): array
    {
        $current = $this->resolveCurrent($state);
        $total = $this->resolveTotal($state, $maxValue);
        $percentage = $this->resolvePercentage($current, $total);
        $config = $this->normalizeThresholdConfig($thresholds);

        return [
            'current' => $current,
            'total' => $total,
            'percentage' => $percentage,
            'status' => $this->resolveStatus($percentage, $config),
            'thresholds' => $config,
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
        ?string $borderRadius = null,
    ): ProgressBarData {
        $base = $this->resolveBaseData($state, $maxValue, $thresholds);
        $status = $base['status'];

        return new ProgressBarData(
            current: $base['current'],
            total: $base['total'],
            percentage: $base['percentage'],
            status: $status,
            color: $this->resolveColorForStatus($status, $colors),
            label: $this->resolveLabelForStatus($status, $labels),
            showsPercentage: $showsPercentage,
            showsProgressValue: $showsProgressValue,
            textPosition: $this->normalizeTextPosition($textPosition),
            size: $this->normalizeSize($size),
            borderRadius: $this->normalizeBorderRadius($borderRadius),
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
     * @param  array<string, mixed>  $thresholds
     * @return array{warning: int, danger: int}
     */
    public function normalizeThresholds(array $thresholds): array
    {
        $config = $this->normalizeThresholdConfig($thresholds);

        return [
            'warning' => $config['warning'] ?? 70,
            'danger' => $config['danger'] ?? 90,
        ];
    }

    /**
     * @param  array<string, mixed>  $thresholds
     * @return array<string, mixed>
     */
    public function normalizeThresholdConfig(array $thresholds): array
    {
        $direction = $this->normalizeDirection($thresholds['direction'] ?? 'ascending');
        $mode = $thresholds['mode'] ?? 'tiers';

        if ($mode === 'map') {
            return [
                'mode' => 'map',
                'direction' => $direction,
                'map' => $this->normalizeThresholdMap($thresholds['map'] ?? []),
            ];
        }

        $defaultWarning = $direction === 'descending' ? 30 : 70;
        $defaultDanger = $direction === 'descending' ? 10 : 90;

        $warning = $this->normalizeThreshold($thresholds['warning'] ?? null, $defaultWarning);
        $danger = $this->normalizeThreshold($thresholds['danger'] ?? null, $defaultDanger);

        if ($direction === 'descending') {
            if ($danger > $warning) {
                $danger = $warning;
            }
        } elseif ($danger < $warning) {
            $danger = $warning;
        }

        return [
            'mode' => 'tiers',
            'direction' => $direction,
            'warning' => $warning,
            'danger' => $danger,
        ];
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public function resolveStatus(int $percentage, array $config): string
    {
        if (($config['mode'] ?? 'tiers') === 'map') {
            return $this->resolveStatusFromMap($percentage, $config['map'] ?? []);
        }

        return $this->resolveStatusFromTiers(
            $percentage,
            (int) ($config['warning'] ?? 70),
            (int) ($config['danger'] ?? 90),
            (string) ($config['direction'] ?? 'ascending'),
        );
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

    public function normalizeBorderRadius(mixed $radius): ?string
    {
        if (! is_string($radius)) {
            return null;
        }

        $radius = trim($radius);

        if ($radius === '') {
            return null;
        }

        if (preg_match('/[;<>"\'{}]/', $radius)) {
            return null;
        }

        return $radius;
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

    protected function normalizeDirection(mixed $direction): string
    {
        return $direction === 'descending' ? 'descending' : 'ascending';
    }

    /**
     * @param  array<int|string, mixed>  $map
     * @return array<int, string>
     */
    protected function normalizeThresholdMap(array $map): array
    {
        $normalized = [];

        foreach ($map as $floor => $status) {
            if (! is_numeric($floor) || ! is_string($status)) {
                continue;
            }

            $status = trim($status);

            if ($status === '') {
                continue;
            }

            $normalized[$this->clampPercentage((float) $floor)] = $status;
        }

        krsort($normalized);

        return $normalized;
    }

    /**
     * @param  array<int, string>  $map
     */
    protected function resolveStatusFromMap(int $percentage, array $map): string
    {
        $lastStatus = 'success';

        foreach ($map as $floor => $status) {
            if ($percentage >= $floor) {
                return $status;
            }

            $lastStatus = $status;
        }

        return $lastStatus;
    }

    protected function resolveStatusFromTiers(int $percentage, int $warning, int $danger, string $direction): string
    {
        if ($direction === 'descending') {
            if ($percentage <= $danger) {
                return 'danger';
            }

            if ($percentage <= $warning) {
                return 'warning';
            }

            return 'success';
        }

        if ($percentage >= $danger) {
            return 'danger';
        }

        if ($percentage >= $warning) {
            return 'warning';
        }

        return 'success';
    }

    /**
     * @param  array<string, mixed>  $colors
     */
    protected function resolveColorForStatus(string $status, array $colors): string
    {
        return $this->normalizeColor($colors[$status] ?? null, $this->defaultColorForStatus($status));
    }

    /**
     * @param  array<string, mixed>  $labels
     */
    protected function resolveLabelForStatus(string $status, array $labels): ?string
    {
        return $this->normalizeLabel($labels[$status] ?? null);
    }

    protected function defaultColorForStatus(string $status): string
    {
        if ($status === 'success') {
            return 'var(--primary-500)';
        }

        if (! preg_match('/^[a-zA-Z][a-zA-Z0-9_-]*$/', $status)) {
            return 'var(--primary-500)';
        }

        return "var(--{$status}-500, var(--primary-500))";
    }
}
