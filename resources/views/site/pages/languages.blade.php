@php
    $supported = config('site.pages.services.languages', []);
    $regionIcons = ['globe', 'compass', 'book', 'pin', 'people', 'sparkle'];

    // Approximate main region for each supported language (longitude, latitude).
    // Languages not listed here (e.g. English, French) are shown as international languages.
    $regions = [
        'Kinyarwanda' => ['Rwanda', 30.06, -1.95],
        'Kirundi' => ['Burundi', 29.92, -3.43],
        'Swahili' => ['Tanzania & Kenya', 35.75, -6.17],
        'Luganda' => ['Uganda', 32.58, 0.35],
        'Runyankore-Rukiga' => ['Western Uganda', 30.66, -0.61],
        'Luo' => ['Western Kenya', 34.76, -0.09],
        'Lingala' => ['DR Congo', 15.30, -4.32],
        'Kikongo' => ['DR Congo', 13.45, -5.82],
        'Tshiluba' => ['DR Congo', 22.40, -5.90],
        'Sango' => ['Central African Republic', 18.56, 4.36],
        'Dinka' => ['South Sudan', 31.56, 6.21],
        'Nuer' => ['South Sudan', 29.80, 9.23],
        'Somali' => ['Somalia', 45.34, 2.05],
    ];

    // Hand-placed label offsets (dx, dy, text-anchor) so names don't collide around the Great Lakes.
    $labelOffsets = [
        'Kinyarwanda' => [-5, 1.6, 'end'],
        'Kirundi' => [-5, 3.2, 'end'],
        'Runyankore-Rukiga' => [-5, -2.4, 'end'],
        'Luganda' => [0, -5.5, 'middle'],
        'Luo' => [5, 1.6, 'start'],
        'Swahili' => [5, 1.6, 'start'],
        'Lingala' => [0, -5.5, 'middle'],
        'Kikongo' => [0, 8, 'middle'],
        'Tshiluba' => [0, 8, 'middle'],
        'Sango' => [0, 8, 'middle'],
        'Dinka' => [5, 1.6, 'start'],
        'Nuer' => [5, 1.6, 'start'],
        'Somali' => [-5, 1.6, 'end'],
    ];

    // Same projection as the Africa outline symbol in partials/svg-defs.
    $project = fn (float $lon, float $lat) => [round(($lon + 18) * 5.5, 1), round((38 - $lat) * 5.6, 1)];

    $mapped = collect($supported)->filter(fn ($language) => isset($regions[$language]))->values();
    $international = collect($supported)->reject(fn ($language) => isset($regions[$language]))->values();
    $hq = $project(30.06, -1.95);

    // Orientation aids for the zoomed map: major lakes and faint country / ocean names.
    $lakes = [
        'Lake Victoria' => [[31.8, -1.0], [32.5, 0.3], [33.4, 0.4], [34.1, 0.0], [34.6, -0.4], [34.0, -1.2], [33.6, -2.3], [32.8, -2.6], [31.9, -2.0], [31.6, -1.4]],
        'Lake Tanganyika' => [[29.1, -3.4], [29.4, -3.4], [30.4, -5.5], [30.8, -7.0], [31.1, -8.6], [30.6, -8.7], [29.7, -6.9], [29.4, -5.0]],
        'Lake Kivu' => [[29.1, -1.6], [29.4, -1.8], [29.2, -2.5], [28.9, -2.4]],
        'Lake Turkana' => [[35.9, 4.5], [36.3, 4.3], [36.6, 3.0], [36.2, 2.4], [35.9, 3.5]],
    ];
    $places = [
        ['DR Congo', 21.5, -2.8], ['C.A.R.', 21.0, 6.8], ['South Sudan', 26.6, 7.4], ['Ethiopia', 39.5, 8.6],
        ['Uganda', 32.6, 3.4], ['Kenya', 38.6, 2.2], ['Tanzania', 33.4, -7.9], ['Somalia', 45.6, 5.6],
    ];
    $points = fn (array $coordinates) => collect($coordinates)->map(fn ($c) => implode(',', $project($c[0], $c[1])))->implode(' ');
@endphp

<x-page-hero :title="$page['headline']" :current="$page['title']" :eyebrow="$page['eyebrow']" :motif="false">
    <p class="nll-lead">{{ $page['intro'] }}</p>
    <div class="nll-actions">
        <a class="nll-button nll-button-gold" href="#supported-languages">See supported languages <span class="nll-arrow" aria-hidden="true">↓</span></a>
        <a class="nll-button nll-button-ghost" href="/contact">Ask about a language</a>
    </div>

    <x-slot:aside>
        <div class="nll-hero-stat" aria-hidden="true">
            <span class="nll-hero-stat-number">{{ count($supported) }}</span>
            <span class="nll-hero-stat-label">Languages<small>Native-speaker linguists</small></span>
        </div>
    </x-slot:aside>
</x-page-hero>

@if (count($supported))
    <section class="nll-coverage-band" id="supported-languages" aria-labelledby="supported-title">
        <div class="nll-wrap">
            <div class="nll-heading" data-reveal>
                <div>
                    <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>Headquartered in Kigali, Rwanda</span>
                    <h2 id="supported-title">Languages we <em>work in</em></h2>
                </div>
                <p class="nll-heading-aside">Native-speaker linguists across East, Central and Northeast Africa. Hover a language to see where it is mainly spoken.</p>
            </div>

            <div class="nll-coverage-band-grid" data-language-map>
                <figure class="nll-region-map" data-reveal>
                    <svg viewBox="150 135 225 140" role="img" aria-labelledby="region-map-title">
                        <title id="region-map-title">Map of East and Central Africa showing the main regions of the languages we work in</title>
                        <defs>
                            <pattern id="nll-dots-fine" width="3" height="3" patternUnits="userSpaceOnUse">
                                <circle cx="1.5" cy="1.5" r=".55"/>
                            </pattern>
                        </defs>
                        <rect class="nll-region-sea" x="150" y="135" width="225" height="140"/>
                        <use class="nll-region-outline" href="#nll-africa" x="-10" y="-10" width="410" height="430"/>
                        <use class="nll-region-land" href="#nll-africa" x="-10" y="-10" width="410" height="430"/>

                        @foreach ($lakes as $lake => $coordinates)
                            <polygon class="nll-region-lake" points="{{ $points($coordinates) }}"><title>{{ $lake }}</title></polygon>
                        @endforeach

                        @foreach ($places as [$name, $lon, $lat])
                            @php([$px, $py] = $project($lon, $lat))
                            <text class="nll-region-place" x="{{ $px }}" y="{{ $py }}">{{ $name }}</text>
                        @endforeach
                        @php([$ox, $oy] = $project(43.5, -5.5))
                        <text class="nll-region-place nll-region-ocean" x="{{ $ox }}" y="{{ $oy }}">Indian Ocean</text>

                        <g class="nll-region-hq">
                            <circle class="nll-region-hq-ring" cx="{{ $hq[0] }}" cy="{{ $hq[1] }}" r="6"/>
                        </g>

                        @foreach ($mapped as $language)
                            @php([$x, $y] = $project($regions[$language][1], $regions[$language][2]))
                            @php([$dx, $dy, $anchor] = $labelOffsets[$language] ?? [5, 1.6, 'start'])
                            <g class="nll-region-marker" data-map-marker="{{ $loop->iteration }}">
                                <title>{{ $language }} · {{ $regions[$language][0] }}</title>
                                <circle class="nll-region-glow" cx="{{ $x }}" cy="{{ $y }}" r="5"/>
                                <circle class="nll-region-pin" cx="{{ $x }}" cy="{{ $y }}" r="2.3"/>
                                <text class="nll-region-label" x="{{ $x + $dx }}" y="{{ $y + $dy }}" text-anchor="{{ $anchor }}">{{ $language }}</text>
                            </g>
                        @endforeach
                    </svg>
                    <figcaption>
                        <span><i class="nll-legend-pin"></i>Language region</span>
                        <span><i class="nll-legend-hq"></i>Kigali HQ</span>
                        <span class="nll-legend-note">Locations are approximate; many languages are spoken across borders.</span>
                    </figcaption>
                </figure>

                <div data-reveal>
                    <ol class="nll-language-grid">
                        @foreach ($mapped as $language)
                            <li data-map-link="{{ $loop->iteration }}">
                                <span class="nll-language-icon" aria-hidden="true"><x-icon name="pin"/></span>
                                <span><b>{{ $language }}</b><small>{{ $regions[$language][0] }}</small></span>
                            </li>
                        @endforeach
                    </ol>
                    @if ($international->isNotEmpty())
                        <p class="nll-language-international"><strong>International languages:</strong> {{ $international->implode(' · ') }}</p>
                    @endif
                    <p class="nll-coverage-band-note">Need another language? <a href="/contact">Ask us</a>.</p>
                </div>
            </div>
        </div>
    </section>
@endif

@if (count($cards))
    <section class="nll-section" aria-labelledby="regions-title">
        <div class="nll-wrap">
            <div class="nll-heading" data-reveal>
                <div>
                    <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>Planning by region</span>
                    <h2 id="regions-title">Choosing the right <em>language and locale</em></h2>
                </div>
            </div>
            <div class="nll-region-grid">
                @foreach ($cards as $card)
                    <a class="nll-region {{ $loop->last ? 'is-accent' : '' }}" href="{{ $card['href'] ?? '/contact' }}" data-reveal>
                        <span class="nll-icon"><x-icon :name="$regionIcons[$loop->index] ?? 'globe'"/></span>
                        <h3>{{ $card['title'] }}</h3>
                        <p>{{ $card['body'] }}</p>
                        <span class="nll-industry-link">Discuss your languages <span class="nll-arrow" aria-hidden="true">→</span></span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif

@if (!empty($page['sections']))
    <section class="nll-section nll-section-sand" aria-label="Planning guidance">
        <div class="nll-wrap nll-guidance-grid">
            @foreach ($page['sections'] as $section)
                <article class="nll-guidance" data-reveal>
                    <span class="nll-guidance-index" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <h2>{{ $section['heading'] }}</h2>
                    <p>{{ $section['body'] }}</p>
                </article>
            @endforeach
        </div>
    </section>
@endif

@include('site.partials.page-cta')
