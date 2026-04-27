<?php

namespace Devletes\FilamentProgressBar\Tables\Columns;

use Devletes\FilamentProgressBar\Concerns\HasProgressBar;
use Devletes\FilamentProgressBar\Support\ProgressBarData;
use Filament\Tables\Columns\Column;

class ProgressBarColumn extends Column
{
    use HasProgressBar {
        resolveProgressBarData as protected resolveBaseProgressBarData;
    }

    protected string $view = 'filament-progress-bar::tables.columns.progress-bar-column';

    public function resolveProgressBarData(): ProgressBarData
    {
        return $this->resolveBaseProgressBarData()->withoutLabel();
    }
}
