<?php

namespace Devletes\FilamentProgressBar\Concerns;

use Closure;
use Devletes\FilamentProgressBar\Support\ProgressBarData;
use Devletes\FilamentProgressBar\Support\ProgressBarResolver;
use Throwable;

trait HasProgressBar
{
    protected int|float|Closure|null $maxValue = null;

    /**
     * @var array<string, int|float> | Closure | null
     */
    protected array|Closure|null $thresholds = null;

    protected int|float|Closure|null $warningThreshold = 70;

    protected int|float|Closure|null $dangerThreshold = 90;

    protected string|Closure|null $successColor = null;

    protected string|Closure|null $warningColor = null;

    protected string|Closure|null $dangerColor = null;

    protected string|Closure|null $successLabel = null;

    protected string|Closure|null $warningLabel = null;

    protected string|Closure|null $dangerLabel = null;

    protected bool|Closure $isPercentageVisible = true;

    protected bool|Closure $isProgressValueVisible = true;

    protected string|Closure $textPosition = 'inside';

    protected string|Closure $size = 'sm';

    public function maxValue(int|float|Closure|null $value): static
    {
        $this->maxValue = $value;

        return $this;
    }

    /**
     * @param  array<string, int|float> | Closure | null  $thresholds
     */
    public function thresholds(array|Closure|null $thresholds): static
    {
        $this->thresholds = $thresholds;

        return $this;
    }

    public function warningThreshold(int|float|Closure|null $threshold): static
    {
        $this->warningThreshold = $threshold;

        return $this;
    }

    public function dangerThreshold(int|float|Closure|null $threshold): static
    {
        $this->dangerThreshold = $threshold;

        return $this;
    }

    public function successColor(string|Closure|null $color): static
    {
        $this->successColor = $color;

        return $this;
    }

    public function warningColor(string|Closure|null $color): static
    {
        $this->warningColor = $color;

        return $this;
    }

    public function dangerColor(string|Closure|null $color): static
    {
        $this->dangerColor = $color;

        return $this;
    }

    public function successLabel(string|Closure|null $label): static
    {
        $this->successLabel = $label;

        return $this;
    }

    public function warningLabel(string|Closure|null $label): static
    {
        $this->warningLabel = $label;

        return $this;
    }

    public function dangerLabel(string|Closure|null $label): static
    {
        $this->dangerLabel = $label;

        return $this;
    }

    public function showPercentage(bool|Closure $condition = true): static
    {
        $this->isPercentageVisible = $condition;

        return $this;
    }

    public function hidePercentage(bool|Closure $condition = true): static
    {
        $this->isPercentageVisible = fn (): bool => ! (bool) $this->evaluate($condition);

        return $this;
    }

    public function showProgressValue(bool|Closure $condition = true): static
    {
        $this->isProgressValueVisible = $condition;

        return $this;
    }

    public function hideProgressValue(bool|Closure $condition = true): static
    {
        $this->isProgressValueVisible = fn (): bool => ! (bool) $this->evaluate($condition);

        return $this;
    }

    public function textPosition(string|Closure $position): static
    {
        $this->textPosition = $position;

        return $this;
    }

    public function size(string|Closure $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function resolveProgressBarData(): ProgressBarData
    {
        $state = $this->getState();
        $resolver = app(ProgressBarResolver::class);
        $parameters = $this->resolveEvaluationParameters($state);
        $maxValue = $this->evaluate($this->maxValue, $parameters);
        $thresholds = $resolver->normalizeThresholds($this->resolveThresholdConfiguration($state));
        $base = $resolver->resolveBaseData($state, $maxValue, $thresholds);
        $parameters = $this->resolveEvaluationParameters($state, $base);

        return $resolver->resolve(
            state: $state,
            maxValue: $maxValue,
            thresholds: $thresholds,
            colors: [
                'success' => (string) $this->evaluate($this->successColor ?? 'var(--primary-500)', $parameters),
                'warning' => (string) $this->evaluate($this->warningColor ?? 'var(--warning-500)', $parameters),
                'danger' => (string) $this->evaluate($this->dangerColor ?? 'var(--danger-500)', $parameters),
            ],
            labels: [
                'success' => $this->evaluate($this->successLabel, $parameters),
                'warning' => $this->evaluate($this->warningLabel, $parameters),
                'danger' => $this->evaluate($this->dangerLabel, $parameters),
            ],
            showsPercentage: (bool) $this->evaluate($this->isPercentageVisible, $parameters),
            showsProgressValue: (bool) $this->evaluate($this->isProgressValueVisible, $parameters),
            textPosition: (string) $this->evaluate($this->textPosition, $parameters),
            size: (string) $this->evaluate($this->size, $parameters),
        );
    }

    /**
     * @return array<string, mixed>
     */
    protected function resolveThresholdConfiguration(mixed $state): array
    {
        $parameters = $this->resolveEvaluationParameters($state);
        $thresholds = $this->evaluate($this->thresholds, $parameters);

        return [
            'warning' => $thresholds['warning'] ?? $this->evaluate($this->warningThreshold, $parameters),
            'danger' => $thresholds['danger'] ?? $this->evaluate($this->dangerThreshold, $parameters),
        ];
    }

    /**
     * @param  array<string, mixed>  $base
     * @return array<string, mixed>
     */
    protected function resolveEvaluationParameters(mixed $state, array $base = []): array
    {
        $parameters = [
            'state' => $state,
            'current' => $base['current'] ?? null,
            'total' => $base['total'] ?? null,
            'percentage' => $base['percentage'] ?? null,
            'status' => $base['status'] ?? null,
        ];

        if (method_exists($this, 'getRecord')) {
            try {
                $parameters['record'] = $this->getRecord();
            } catch (Throwable) {
                $parameters['record'] = null;
            }
        }

        return $parameters;
    }
}
