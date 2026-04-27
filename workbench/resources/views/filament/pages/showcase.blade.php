@php
    $demos = $this->getDemos();

    $sectionTitles = [
        'sizes' => 'Sizes',
        'text-position' => 'Text position',
        'visibility' => 'Show / hide value & percentage',
        'border-radius' => 'Border radius',
        'thresholds-default' => 'Default three-state thresholds',
        'thresholds-descending' => 'Descending threshold direction',
        'thresholds-map' => 'Threshold map',
        'recipe-battery' => 'Recipe — Battery / fuel (low is bad)',
        'recipe-quality' => 'Recipe — Multi-state quality score',
        'recipe-squared' => 'Recipe — Squared bars matching a card design',
        'recipe-compact' => 'Recipe — Compact column without text overlay',
    ];
@endphp

<x-filament-panels::page>
    <style>
        [data-demo] {
            padding: 1.5rem;
            border-radius: 0.75rem;
            background-color: #ffffff;
            border: 1px solid rgba(15, 23, 42, 0.08);
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        }
        [data-demo] .demo-title {
            font-weight: 600;
            font-size: 1rem;
            color: rgb(15, 23, 42);
            margin: 0 0 1.25rem;
        }
        [data-demo] .demo-row {
            margin-bottom: 1.25rem;
        }
        [data-demo] .demo-row:last-child {
            margin-bottom: 0;
        }
        [data-demo] .demo-caption {
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
            font-size: 0.75rem;
            color: rgb(100, 116, 139);
            margin: 0 0 0.5rem;
        }

        html.dark [data-demo] {
            background-color: rgb(17, 24, 39);
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.4);
        }
        html.dark [data-demo] .demo-title {
            color: rgb(241, 245, 249);
        }
        html.dark [data-demo] .demo-caption {
            color: rgb(148, 163, 184);
        }

        .demo-stack > * + * {
            margin-top: 2rem;
        }
    </style>

    <div class="demo-stack">
        @foreach ($demos as $sectionId => $bars)
            <section data-demo="{{ $sectionId }}">
                <h2 class="demo-title">
                    {{ $sectionTitles[$sectionId] ?? $sectionId }}
                </h2>

                @foreach ($bars as $bar)
                    <div class="demo-row">
                        @if (! empty($bar['caption']))
                            <p class="demo-caption">
                                {{ $bar['caption'] }}
                            </p>
                        @endif

                        @include('filament-progress-bar::components.progress-bar', ['data' => $bar['data']])
                    </div>
                @endforeach
            </section>
        @endforeach
    </div>
</x-filament-panels::page>
