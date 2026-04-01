<?php

namespace Devletes\FilamentProgressBar\Support;

class ProgressBarData
{
    public function __construct(
        public readonly float $current,
        public readonly ?float $total,
        public readonly int $percentage,
        public readonly string $status,
        public readonly string $color,
        public readonly ?string $label,
        public readonly bool $showsPercentage,
        public readonly bool $showsProgressValue,
        public readonly string $textPosition,
        public readonly string $size,
    ) {}

    public function getAriaValueNow(): int
    {
        return $this->percentage;
    }

    public function getAriaValueMax(): int
    {
        return 100;
    }

    public function getProgressValueText(): ?string
    {
        if (! $this->showsProgressValue) {
            return null;
        }

        if ($this->total !== null) {
            return sprintf('%s / %s', self::formatNumber($this->current), self::formatNumber($this->total));
        }

        return self::formatNumber($this->current);
    }

    public function getPercentageText(): ?string
    {
        if (! $this->showsPercentage) {
            return null;
        }

        return "{$this->percentage}%";
    }

    public function getDisplayText(): ?string
    {
        $value = $this->getProgressValueText();
        $percentage = $this->getPercentageText();

        if ($value && $percentage) {
            return "{$value} ({$percentage})";
        }

        return $value ?? $percentage;
    }

    public function hasDisplayText(): bool
    {
        return filled($this->getDisplayText());
    }

    public function isProgressTextInsideBar(): bool
    {
        return $this->textPosition === 'inside';
    }

    /**
     * @return array{bar: string, text: string, outsideText: string}
     */
    public function getSizeClasses(): array
    {
        return match ($this->size) {
            'sm' => [
                'bar' => 'fpb-bar--sm',
                'text' => 'fpb-text--sm',
                'outsideText' => 'fpb-outside-text--sm',
            ],
            'lg' => [
                'bar' => 'fpb-bar--lg',
                'text' => 'fpb-text--lg',
                'outsideText' => 'fpb-outside-text--lg',
            ],
            default => [
                'bar' => 'fpb-bar--md',
                'text' => 'fpb-text--md',
                'outsideText' => 'fpb-outside-text--md',
            ],
        };
    }

    public static function formatNumber(float|int $value): string
    {
        $value = (float) $value;
        $decimals = fmod($value, 1.0) === 0.0 ? 0 : 2;

        return number_format($value, $decimals, '.', '');
    }
}
