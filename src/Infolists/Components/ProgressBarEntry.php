<?php

namespace Devletes\FilamentProgressBar\Infolists\Components;

use Devletes\FilamentProgressBar\Concerns\HasProgressBar;
use Filament\Infolists\Components\Concerns\HasIcon;
use Filament\Infolists\Components\Concerns\HasIconColor;
use Filament\Infolists\Components\Entry;
use Filament\Infolists\View\Components\TextEntryComponent\ItemComponent\IconComponent;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Enums\IconSize;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use Illuminate\View\ComponentAttributeBag;

use function Filament\Support\generate_icon_html;

class ProgressBarEntry extends Entry implements HasEmbeddedView
{
    use HasIcon;
    use HasIconColor;
    use HasProgressBar;

    public function getLabel(): string|Htmlable|null
    {
        $label = parent::getLabel();
        $icon = $this->getIcon($this->getState());

        if (blank($icon) || blank($label)) {
            return $label;
        }

        $iconHtml = generate_icon_html(
            $icon,
            attributes: (new ComponentAttributeBag)
                ->class(['h-4 w-4 shrink-0'])
                ->color(IconComponent::class, $this->getIconColor($this->getState())),
            size: IconSize::Small,
        )?->toHtml();

        if (blank($iconHtml)) {
            return $label;
        }

        $labelHtml = $label instanceof Htmlable ? $label->toHtml() : e($label);

        return new HtmlString(
            '<span class="fpb-entry-label">'.$iconHtml.'<span>'.$labelHtml.'</span></span>',
        );
    }

    public function toEmbeddedHtml(): string
    {
        return $this->wrapEmbeddedHtml(
            view('filament-progress-bar::components.progress-bar', [
                'data' => $this->resolveProgressBarData(),
            ])->render(),
        );
    }
}
