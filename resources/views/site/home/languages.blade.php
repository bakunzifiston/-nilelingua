<section class="nll-languages" id="{{ $anchor }}" aria-labelledby="{{ $anchor }}-title">
    <div class="nll-wrap nll-languages-grid">
        <div class="nll-languages-copy" data-reveal>
            @if (!empty($section['eyebrow']))
                <span class="nll-eyebrow nll-eyebrow-light"><span class="nll-dot" aria-hidden="true"></span>{{ $section['eyebrow'] }}</span>
            @endif
            <h2 id="{{ $anchor }}-title">{!! \App\Support\Cms\Markup::inline($section['heading'] ?? '') !!}</h2>
            @if (!empty($section['text']))
                <p>{{ $section['text'] }}</p>
            @endif

            @if (!empty($section['stat_number']))
                <div class="nll-stat">
                    <span class="nll-stat-number">{{ $section['stat_number'] }}</span>
                    <span class="nll-stat-label">{{ $section['stat_label'] ?? '' }}<small>{{ $section['stat_caption'] ?? '' }}</small></span>
                </div>
            @endif

            @if (!empty($section['button']['label']) && !empty($section['button']['href']))
                <a class="nll-button nll-button-outline-light" href="{{ $section['button']['href'] }}">{{ $section['button']['label'] }} <span class="nll-arrow" aria-hidden="true">→</span></a>
            @endif
        </div>

        <div class="nll-coverage" data-reveal>
            <div class="nll-coverage-map">
                <svg class="nll-map nll-map-large" viewBox="-10 -10 410 430" fill="none" aria-hidden="true">
                    <use class="nll-map-land" href="#nll-africa"/>
                    <use class="nll-map-outline" href="#nll-africa"/>
                    <ellipse cx="265" cy="205" rx="96" ry="84" fill="url(#nll-region-glow)"/>
                    <path class="nll-map-region" d="M196 150c26-34 92-44 128-18 30 22 32 76 6 108-24 30-78 36-108 14-30-22-48-70-26-104Z"/>
                    <path class="nll-map-leader" d="m201 188-91-23m163 20 41-54m-10 89 37 10m-73-6 16 76"/>
                    <circle class="nll-map-node" cx="201" cy="188" r="4.5"/>
                    <circle class="nll-map-node" cx="273" cy="185" r="4.5"/>
                    <circle class="nll-map-node" cx="301" cy="220" r="4.5"/>
                    <circle class="nll-map-pulse" cx="264" cy="224" r="14"/>
                    <circle class="nll-map-hq" cx="264" cy="224" r="6.5"/>
                </svg>
                <ul class="nll-coverage-labels" aria-label="{{ __('Examples of supported languages') }}">
                    <li class="nll-chip nll-chip-sango">Sango</li>
                    <li class="nll-chip nll-chip-dinka">Dinka</li>
                    <li class="nll-chip nll-chip-swahili">Swahili</li>
                    <li class="nll-chip nll-chip-kinyarwanda">Kinyarwanda</li>
                </ul>
                <span class="nll-hq-label" aria-hidden="true">{{ __('Kigali HQ') }}</span>
            </div>
            @if (!empty($section['note']))
                <p class="nll-coverage-note">{{ $section['note'] }}</p>
            @endif
        </div>
    </div>
</section>
