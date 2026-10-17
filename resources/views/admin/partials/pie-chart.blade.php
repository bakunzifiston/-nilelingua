{{--
    Donut / pie chart (inline SVG, no library).
    $chartId, $segments: [['label' => …, 'value' => int, 'color' => …]], $centerLabel.
--}}
@php
    $segments = collect($segments)->filter(fn ($segment) => $segment['value'] > 0)->values();
    $total = $segments->sum('value');
    $offset = 25; // start at 12 o'clock
@endphp

<figure class="a-chart a-pie">
    <svg viewBox="0 0 42 42" role="img" aria-labelledby="{{ $chartId }}-desc">
        <desc id="{{ $chartId }}-desc">{{ $description ?? 'Pie chart' }}: {{ $segments->map(fn ($s) => $s['label'].' '.$s['value'])->implode(', ') }}</desc>
        <circle class="a-pie-track" cx="21" cy="21" r="15.915" fill="none" stroke-width="6"/>
        @foreach ($segments as $segment)
            @php $share = $total ? $segment['value'] / $total * 100 : 0; @endphp
            <circle cx="21" cy="21" r="15.915" fill="none" stroke="{{ $segment['color'] }}" stroke-width="6"
                    stroke-dasharray="{{ round($share, 3) }} {{ round(100 - $share, 3) }}" stroke-dashoffset="{{ round($offset, 3) }}">
                <title>{{ $segment['label'] }}: {{ $segment['value'] }} ({{ round($share) }}%)</title>
            </circle>
            @php $offset -= $share; @endphp
        @endforeach
        <text class="a-pie-total" x="21" y="21.5" text-anchor="middle">{{ $total }}</text>
        <text class="a-pie-caption" x="21" y="26.5" text-anchor="middle">{{ $centerLabel ?? 'total' }}</text>
    </svg>

    <figcaption class="a-pie-legend">
        @foreach ($segments as $segment)
            <span>
                <i style="background: {{ $segment['color'] }}"></i>
                @if (!empty($segment['href']))
                    <a class="a-pie-name" href="{{ $segment['href'] }}">{{ $segment['label'] }}</a>
                @else
                    <span class="a-pie-name">{{ $segment['label'] }}</span>
                @endif
                <b>{{ $segment['value'] }}</b>
                <small>{{ $total ? round($segment['value'] / $total * 100) : 0 }}%</small>
            </span>
        @endforeach
    </figcaption>
</figure>
