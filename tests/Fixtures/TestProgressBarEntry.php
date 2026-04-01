<?php

namespace Devletes\FilamentProgressBar\Tests\Fixtures;

use Devletes\FilamentProgressBar\Infolists\Components\ProgressBarEntry;

class TestProgressBarEntry extends ProgressBarEntry
{
    public function getEntryWrapperView(): string
    {
        return 'filament-infolists::entry-wrapper';
    }
}
