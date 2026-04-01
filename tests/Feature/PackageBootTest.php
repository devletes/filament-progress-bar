<?php

it('boots the package views', function (): void {
    expect(view()->exists('filament-progress-bar::components.progress-bar'))->toBeTrue();
});
