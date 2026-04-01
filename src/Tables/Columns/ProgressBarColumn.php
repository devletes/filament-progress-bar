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
        $data = $this->resolveBaseProgressBarData();

        return new ProgressBarData(
            current: $data->current,
            total: $data->total,
            percentage: $data->percentage,
            status: $data->status,
            color: $data->color,
            label: null,
            showsPercentage: $data->showsPercentage,
            showsProgressValue: $data->showsProgressValue,
            textPosition: $data->textPosition,
            size: $data->size,
        );
    }
}
