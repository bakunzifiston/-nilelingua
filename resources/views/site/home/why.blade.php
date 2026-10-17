<section class="nll-section nll-why" id="{{ $anchor }}" aria-labelledby="{{ $anchor }}-title">
    <div class="nll-wrap nll-why-grid">
        <div class="nll-why-intro" data-reveal>
            @if (!empty($section['eyebrow']))
                <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>{{ $section['eyebrow'] }}</span>
            @endif
            <h2 id="{{ $anchor }}-title">{!! \App\Support\Cms\Markup::inline($section['heading'] ?? '') !!}</h2>
        </div>

        <ol class="nll-why-list">
            @foreach ($section['items'] ?? [] as $item)
                <li data-reveal>
                    <span class="nll-why-index" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="nll-why-icon" aria-hidden="true"><x-icon :name="$item['icon'] ?? 'check'"/></span>
                    <div>
                        <h3>{{ $item['title'] ?? '' }}</h3>
                        @if (!empty($item['text']))
                            <p>{{ $item['text'] }}</p>
                        @endif
                        @if (!empty($item['link']['label']) && !empty($item['link']['href']))
                            <a class="nll-link" href="{{ $item['link']['href'] }}">{{ $item['link']['label'] }} <span class="nll-arrow" aria-hidden="true">→</span></a>
                        @endif
                    </div>
                </li>
            @endforeach
        </ol>
    </div>
</section>
