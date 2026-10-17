@php
    $highlight = $section['highlight'] ?? '';
    $headline = e($section['headline'] ?? '');
    if ($highlight !== '') {
        $headline = str_replace(e($highlight), '<span class="nll-nowrap">'.e($highlight).'</span>', $headline);
    }
@endphp

<section class="nll-hero" aria-labelledby="home-hero-title">
    <div class="nll-wrap nll-hero-grid">
        <div class="nll-hero-copy">
            @if (!empty($section['eyebrow']))
                <span class="nll-eyebrow nll-eyebrow-light"><span class="nll-dot" aria-hidden="true"></span>{{ $section['eyebrow'] }}</span>
            @endif
            <h1 id="home-hero-title">{!! $headline !!}</h1>
            @if (!empty($section['subhead']))
                <p class="nll-hero-subhead">{{ $section['subhead'] }}</p>
            @endif
            @if (!empty($section['intro']))
                <p class="nll-hero-intro">{{ $section['intro'] }}</p>
            @endif
            @include('site.home.buttons', ['primary' => $section['primary_button'] ?? null, 'secondary' => $section['secondary_button'] ?? null])
            @if (!empty($section['note']))
                <p class="nll-hero-note"><span aria-hidden="true"></span>{{ $section['note'] }}</p>
            @endif
        </div>

        <div class="nll-hero-visual" aria-hidden="true">
            <div class="nll-hero-stage">
                <span class="nll-orbit nll-orbit-one"></span>
                <span class="nll-orbit nll-orbit-two"></span>

                <svg class="nll-map" viewBox="-10 -10 410 430" fill="none">
                    <use class="nll-map-shadow" href="#nll-africa"/>
                    <use class="nll-map-land" href="#nll-africa"/>
                    <use class="nll-map-outline" href="#nll-africa"/>
                    <ellipse cx="270" cy="210" rx="90" ry="78" fill="url(#nll-region-glow)"/>
                    <path class="nll-map-arc" d="M264 224C230 120 120 40-10 30"/>
                    <path class="nll-map-arc" d="M264 224C320 150 360 80 400 40"/>
                    <path class="nll-map-arc" d="M264 224C190 280 80 330-10 340"/>
                    <path class="nll-map-leader" d="m201 188-91-23m163 20 41-54m-10 89 37 10m-73-6 16 76"/>
                    <circle class="nll-map-node" cx="201" cy="188" r="4.5"/>
                    <circle class="nll-map-node" cx="273" cy="185" r="4.5"/>
                    <circle class="nll-map-node" cx="301" cy="220" r="4.5"/>
                    <circle class="nll-map-pulse" cx="264" cy="224" r="14"/>
                    <circle class="nll-map-hq" cx="264" cy="224" r="6.5"/>
                </svg>

                <span class="nll-chip nll-chip-sango">Sango</span>
                <span class="nll-chip nll-chip-dinka">Dinka</span>
                <span class="nll-chip nll-chip-swahili">Swahili</span>
                <span class="nll-chip nll-chip-kinyarwanda">Kinyarwanda</span>

                <div class="nll-float nll-float-translate">
                    <span class="nll-float-label">{{ __('EN') }} <span>→</span> RW</span>
                    <span class="nll-float-source">{{ __('Welcome') }}</span>
                    <span class="nll-float-target">Murakaza neza</span>
                    <span class="nll-float-check">{{ __('Human reviewed') }}</span>
                </div>

                <div class="nll-float nll-float-audio">
                    <span class="nll-float-label">{{ __('Speech data · Swahili') }}</span>
                    <span class="nll-wave">
                        @foreach ([9, 16, 24, 14, 30, 20, 34, 18, 26, 12, 22, 28, 15, 9, 19, 11] as $bar)
                            <i style="--h: {{ $bar }}px"></i>
                        @endforeach
                    </span>
                    <span class="nll-float-tags"><span>Karibu</span><span>{{ __('Annotated') }}</span></span>
                </div>
            </div>
            @if (!empty($section['caption']))
                <span class="nll-hero-caption">{{ $section['caption'] }}</span>
            @endif
        </div>
    </div>
    <div class="nll-wrap nll-hero-bottom">
        <span>{{ config('site.name') }} · {{ config('site.contact_info.address_line_2', __('Kigali, Rwanda')) }}</span>
        @if (!empty($section['scroll_label']) && $nextAnchor)
            <a href="#{{ $nextAnchor }}">{{ $section['scroll_label'] }} <span aria-hidden="true">↓</span></a>
        @endif
    </div>
</section>
