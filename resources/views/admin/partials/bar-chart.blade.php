{{--
    Grouped bar chart (inline SVG, no library).
    $chartId, $categories (x labels), $series: [['name' => …, 'color' => …, 'values' => [...]]], optional $unit.
--}}
@php
    $width = 640;
    $height = 240;
    $padLeft = 34;
    $padBottom = 28;
    $padTop = 12;
    $plotWidth = $width - $padLeft - 8;
    $plotHeight = $height - $padBottom - $padTop;

    $max = max(1, ...collect($series)->flatMap(fn ($s) => $s['values'])->all());
    $step = max(1, (int) ceil($max / 4));
    $top = $step * 4;

    $groupWidth = $plotWidth / max(1, count($categories));
    $barCount = max(1, count($series));
    $barWidth = min(26, ($groupWidth * 0.7) / $barCount);
    $gap = 3;
    $groupInner = $barCount * $barWidth + ($barCount - 1) * $gap;
@endphp

<figure class="a-chart">
    <svg viewBox="0 0 {{ $width }} {{ $height }}" role="img" aria-labelledby="{{ $chartId }}-desc" preserveAspectRatio="xMidYMid meet">
        <desc id="{{ $chartId }}-desc">{{ $description ?? 'Bar chart' }}</desc>
        @for ($i = 0; $i <= 4; $i++)
            @php $y = $padTop + $plotHeight - ($plotHeight * $i / 4); @endphp
            <line class="a-chart-gridline" x1="{{ $padLeft }}" x2="{{ $width - 8 }}" y1="{{ $y }}" y2="{{ $y }}"/>
            <text class="a-chart-axis" x="{{ $padLeft - 8 }}" y="{{ $y + 4 }}" text-anchor="end">{{ $step * $i }}</text>
        @endfor

        @foreach ($categories as $index => $category)
            @php $groupX = $padLeft + $groupWidth * $index + ($groupWidth - $groupInner) / 2; @endphp
            @foreach ($series as $s => $line)
                @php
                    $value = $line['values'][$index] ?? 0;
                    $barHeight = $plotHeight * $value / $top;
                    $x = $groupX + $s * ($barWidth + $gap);
                @endphp
                <rect class="a-chart-bar" x="{{ round($x, 1) }}" y="{{ round($padTop + $plotHeight - $barHeight, 1) }}" width="{{ round($barWidth, 1) }}" height="{{ round(max($barHeight, $value > 0 ? 2 : 0), 1) }}" rx="4" fill="{{ $line['color'] }}">
                    <title>{{ $line['name'] }} · {{ $category }}: {{ $value }}{{ isset($unit) ? ' '.$unit : '' }}</title>
                </rect>
            @endforeach
            <text class="a-chart-axis" x="{{ round($padLeft + $groupWidth * $index + $groupWidth / 2, 1) }}" y="{{ $height - 8 }}" text-anchor="middle">{{ $category }}</text>
        @endforeach
    </svg>

    @if (count($series) > 1)
        <figcaption class="a-chart-legend">
            @foreach ($series as $line)
                <span><i style="background: {{ $line['color'] }}"></i>{{ $line['name'] }} <b>{{ array_sum($line['values']) }}</b></span>
            @endforeach
        </figcaption>
    @endif

    {{-- The same numbers as a table, for screen readers. --}}
    <table class="visually-hidden">
        <caption>{{ $description ?? 'Bar chart data' }}</caption>
        <thead><tr><th scope="col">Period</th>@foreach ($series as $line)<th scope="col">{{ $line['name'] }}</th>@endforeach</tr></thead>
        <tbody>
            @foreach ($categories as $index => $category)
                <tr><th scope="row">{{ $category }}</th>@foreach ($series as $line)<td>{{ $line['values'][$index] ?? 0 }}</td>@endforeach</tr>
            @endforeach
        </tbody>
    </table>
</figure>
