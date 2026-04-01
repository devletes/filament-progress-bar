@php
    $data = $column->resolveProgressBarData();
@endphp

@include('filament-progress-bar::components.progress-bar', ['data' => $data])
