@props([
    'data',
])

@php
    $sizeClasses = $data->getSizeClasses();
    $borderRadius = $data->getBorderRadius();
    $trackStyle = $borderRadius !== null ? '--fpb-radius: '.$borderRadius.';' : null;
@endphp

<div class="fi-progress-bar fpb-root">
    @if ($data->label)
        <div class="fpb-label-row">
            <div class="fpb-label-wrap">
                <p class="fpb-label">
                    {{ $data->label }}
                </p>
            </div>
        </div>
    @endif

    <div
        class="fpb-track {{ $sizeClasses['bar'] }}"
        @if ($trackStyle) style="{{ $trackStyle }}" @endif
        role="progressbar"
        aria-valuemin="0"
        aria-valuemax="{{ $data->getAriaValueMax() }}"
        aria-valuenow="{{ $data->getAriaValueNow() }}"
        aria-valuetext="{{ $data->getDisplayText() ?? '' }}"
    >
        <div
            class="fpb-fill"
            style="width: {{ $data->percentage }}%; background-color: {{ $data->color }};"
        ></div>

        @if ($data->hasDisplayText() && $data->isProgressTextInsideBar())
            <div class="fpb-inside">
                <span class="fpb-inside-text {{ $sizeClasses['text'] }}">
                    {{ $data->getDisplayText() }}
                </span>
            </div>
        @endif
    </div>

    @if ($data->hasDisplayText() && (! $data->isProgressTextInsideBar()))
        <div class="fpb-outside-row">
            <span class="fpb-outside-text {{ $sizeClasses['outsideText'] }}">
                {{ $data->getDisplayText() }}
            </span>
        </div>
    @endif
</div>
