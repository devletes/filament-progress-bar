<?php

namespace Devletes\FilamentProgressBar\Tests\Fixtures;

use Devletes\FilamentProgressBar\Tables\Columns\ProgressBarColumn;

class TestProgressBarColumn extends ProgressBarColumn
{
    protected mixed $fakeState = null;

    public static function fake(string $name, mixed $state): static
    {
        $static = app(static::class, ['name' => $name]);
        $static->configure();
        $static->fakeState = $state;

        return $static;
    }

    public function getState(): mixed
    {
        return $this->fakeState;
    }
}
