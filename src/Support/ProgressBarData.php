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
        public readonly ?string $borderRadius = null,
        public readonly ?string $innerBorderRadius = null,
        public readonly ?string $height = null,
        public readonly ?string $trackColor = null,
    ) {}

    public function getBorderRadius(): ?string
    {
        return $this->borderRadius;
    }

    public function getInnerBorderRadius(): ?string
    {
        return $this->innerBorderRadius;
    }

    public function getHeight(): ?string
    {
        return $this->height;
    }

    public function getTrackColor(): ?string
    {
        return $this->trackColor;
    }

    /**
     * @return array<string, string>
     */
    public function getTrackCustomProperties(): array
    {
        return array_filter([
            '--fpb-radius' => $this->borderRadius,
            '--fpb-inner-radius' => $this->innerBorderRadius,
            '--fpb-height' => $this->height,
            '--fpb-track-color' => $this->trackColor,
        ], static fn (?string $value): bool => $value !== null);
    }

    public function getTrackStyle(): ?string
    {
        $declarations = [];

        foreach ($this->getTrackCustomProperties() as $property => $value) {
            $declarations[] = "{$property}: {$value};";
        }

        return $declarations === [] ? null : implode(' ', $declarations);
    }

    public function withoutLabel(): self
    {
        return new self(
            current: $this->current,
            total: $this->total,
            percentage: $this->percentage,
            status: $this->status,
            color: $this->color,
            label: null,
            showsPercentage: $this->showsPercentage,
            showsProgressValue: $this->showsProgressValue,
            textPosition: $this->textPosition,
            size: $this->size,
            borderRadius: $this->borderRadius,
            innerBorderRadius: $this->innerBorderRadius,
            height: $this->height,
            trackColor: $this->trackColor,
        );
    }

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
        return (! $this->hidesTextForSize()) && filled($this->getDisplayText());
    }

    /**
     * An `xs` bar is too short to stack text legibly on top of the fill, so its text is
     * suppressed unless it has been moved below the bar with `textPosition('outside')`.
     * The value still reaches screen readers through `aria-valuetext`.
     */
    public function hidesTextForSize(): bool
    {
        return $this->size === 'xs' && $this->isProgressTextInsideBar();
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
            'xs' => [
                'bar' => 'fpb-bar--xs',
                'text' => 'fpb-text--xs',
                'outsideText' => 'fpb-outside-text--xs',
            ],
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
