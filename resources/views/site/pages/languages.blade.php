@php
    $supported = config('site.pages.services.languages', []);

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
        'Kinyarwanda' => [-7, 2.5, 'end'],
        'Kirundi' => [6, 4.5, 'start'],
        'Runyankore-Rukiga' => [-7, -7, 'end'],
        'Luganda' => [2, -7.5, 'middle'],
        'Luo' => [7, 2.5, 'start'],
        'Swahili' => [7, 2.5, 'start'],
        'Lingala' => [0, -8, 'middle'],
        'Kikongo' => [-7, 7, 'end'],
        'Tshiluba' => [0, 12, 'middle'],
        'Sango' => [0, -8, 'middle'],
        'Dinka' => [7, 2.5, 'start'],
        'Nuer' => [7, 2.5, 'start'],
        'Somali' => [7, 2.5, 'start'],
    ];

    // Same projection as the Africa outline symbol in partials/svg-defs.
    $project = fn (float $lon, float $lat) => [round(($lon + 18) * 5.5, 1), round((38 - $lat) * 5.6, 1)];

    $mapped = collect($supported)->filter(fn ($language) => isset($regions[$language]))->values();
    $hq = $project(30.06, -1.95);

    // Orientation aids for the map: major lakes and faint country / ocean names.
    $lakes = [
        'Lake Victoria' => [[31.8, -1.0], [32.5, 0.3], [33.4, 0.4], [34.1, 0.0], [34.6, -0.4], [34.0, -1.2], [33.6, -2.3], [32.8, -2.6], [31.9, -2.0], [31.6, -1.4]],
        'Lake Tanganyika' => [[29.1, -3.4], [29.4, -3.4], [30.4, -5.5], [30.8, -7.0], [31.1, -8.6], [30.6, -8.7], [29.7, -6.9], [29.4, -5.0]],
        'Lake Kivu' => [[29.1, -1.6], [29.4, -1.8], [29.2, -2.5], [28.9, -2.4]],
        'Lake Turkana' => [[35.9, 4.5], [36.3, 4.3], [36.6, 3.0], [36.2, 2.4], [35.9, 3.5]],
    ];
    $places = [
        ['DR Congo', 24.5, -9.2], ['C.A.R.', 21.0, 7.6], ['South Sudan', 25.4, 5.4], ['Ethiopia', 39.5, 8.6],
        ['Kenya', 38.8, 1.2], ['Tanzania', 34.6, -9.0], ['Somalia', 45.6, 6.0],
    ];
    $points = fn (array $coordinates) => collect($coordinates)->map(fn ($c) => implode(',', $project($c[0], $c[1])))->implode(' ');
    $slug = fn (string $name) => \Illuminate\Support\Str::slug($name);
@endphp

<x-page-hero :title="$page['headline']" :current="$page['title']" :eyebrow="$page['eyebrow']" :motif="false">
    <h2 class="nll-subtitle">{{ $page['subheading'] }}</h2>
    @foreach ($page['paragraphs'] as $paragraph)
        <p class="nll-lead {{ $loop->first ? '' : 'nll-lead-secondary' }}">{{ $paragraph }}</p>
    @endforeach
    <div class="nll-actions">
        <a class="nll-button nll-button-gold" href="/services">{{ __('Explore Our Services') }} <span class="nll-arrow" aria-hidden="true">→</span></a>
        <a class="nll-button nll-button-ghost" href="/contact">{{ __('Discuss Your Language Needs') }}</a>
    </div>

    <x-slot:aside>
        <div class="nll-hero-stat" aria-hidden="true">
            <span class="nll-hero-stat-number">{{ count($supported) }}</span>
            <span class="nll-hero-stat-label">{{ __('Languages') }}<small>{{ __('One point of contact') }}</small></span>
        </div>
    </x-slot:aside>
</x-page-hero>

{{-- Region overview table --}}
<section class="nll-section nll-section-tight" id="supported-languages" aria-labelledby="supported-title">
    <div class="nll-wrap">
        <div class="nll-heading" data-reveal>
            <div>
                <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>{{ __('Grouped by region') }}</span>
                <h2 id="supported-title">{!! __('The Languages We <em>Support</em>') !!}</h2>
            </div>
        </div>
        <div class="nll-table-wrap" data-reveal>
            <table class="nll-table nll-region-table">
                <thead>
                    <tr><th scope="col">{{ __('Region') }}</th><th scope="col">{{ __('Languages') }}</th><th scope="col">{{ __('Where they are spoken') }}</th></tr>
                </thead>
                <tbody>
                    @foreach ($page['region_table'] as $row)
                        <tr>
                            <th scope="row"><a href="#region-{{ $loop->iteration }}">{{ $row['region'] }}</a></th>
                            <td data-label="{{ __('Languages') }}">{{ $row['languages'] }}</td>
                            <td data-label="{{ __('Where they are spoken') }}">{{ $row['where'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="nll-steps-note"><span aria-hidden="true">✳</span><span class="nll-note-text">{{ __('Need a language that is not listed?') }} <a href="/contact">{{ __('Ask us') }}</a>. {{ __('We can often arrange it on request.') }}</span></p>
    </div>
</section>

{{-- Language descriptions beside the region map --}}
<section class="nll-section nll-section-sand" aria-label="{{ __('Language profiles by region') }}">
    <div class="nll-wrap nll-lang-layout" data-language-map>
        <figure class="nll-region-map nll-lang-map" data-reveal>
            <svg viewBox="-6 -4 396 420" role="img" aria-labelledby="region-map-title">
                <title id="region-map-title">{{ __('Map of Africa showing where the languages we support are mainly spoken') }}</title>
                <defs>
                    <pattern id="nll-dots-fine" width="4.5" height="4.5" patternUnits="userSpaceOnUse">
                        <circle cx="2.25" cy="2.25" r=".9"/>
                    </pattern>
                </defs>
                <rect class="nll-region-sea" x="-6" y="-4" width="396" height="420"/>
                <use class="nll-region-outline" href="#nll-africa" x="-10" y="-10" width="410" height="430"/>
                <use class="nll-region-land" href="#nll-africa" x="-10" y="-10" width="410" height="430"/>

                @foreach ($lakes as $lake => $coordinates)
                    <polygon class="nll-region-lake" points="{{ $points($coordinates) }}"><title>{{ __($lake) }}</title></polygon>
                @endforeach

                @foreach ($places as [$name, $lon, $lat])
                    @php([$px, $py] = $project($lon, $lat))
                    <text class="nll-region-place" x="{{ $px }}" y="{{ $py }}">{{ __($name) }}</text>
                @endforeach
                @php([$ox, $oy] = $project(46.5, -7.5))
                <text class="nll-region-place nll-region-ocean" x="{{ $ox }}" y="{{ $oy }}">{{ __('Indian Ocean') }}</text>
                @php([$ax, $ay] = $project(-8.5, -8))
                <text class="nll-region-place nll-region-ocean" x="{{ $ax }}" y="{{ $ay }}">{{ __('Atlantic Ocean') }}</text>

                <g class="nll-region-hq">
                    <circle class="nll-region-hq-ring" cx="{{ $hq[0] }}" cy="{{ $hq[1] }}" r="10"/>
                </g>

                @foreach ($mapped as $language)
                    @php([$x, $y] = $project($regions[$language][1], $regions[$language][2]))
                    @php([$dx, $dy, $anchor] = $labelOffsets[$language] ?? [7, 2.5, 'start'])
                    <g class="nll-region-marker" data-map-marker="{{ $slug($language) }}">
                        <title>{{ $language }}</title>
                        <circle class="nll-region-glow" cx="{{ $x }}" cy="{{ $y }}" r="8"/>
                        <circle class="nll-region-pin" cx="{{ $x }}" cy="{{ $y }}" r="3.6"/>
                        <text class="nll-region-label" x="{{ $x + $dx }}" y="{{ $y + $dy }}" text-anchor="{{ $anchor }}">{{ $language }}</text>
                    </g>
                @endforeach
            </svg>
            <figcaption>
                <span><i class="nll-legend-pin"></i>{{ __('Language region') }}</span>
                <span><i class="nll-legend-hq"></i>{{ __('Kigali HQ') }}</span>
                <span class="nll-legend-note">{{ __('Pins mark a representative area; many languages are spoken across borders.') }}</span>
            </figcaption>
        </figure>

        <div class="nll-lang-groups">
            @foreach ($page['groups'] as $group)
                <div class="nll-lang-group nll-anchor" id="region-{{ $loop->iteration }}">
                    <h2 class="nll-lang-group-title">{{ $group['heading'] }}</h2>
                    <div class="nll-lang-cards">
                        @foreach ($group['languages'] as $name => $description)
                            <article class="nll-lang-card" data-map-link="{{ $slug($name) }}" data-reveal>
                                <h3><span class="nll-language-icon" aria-hidden="true"><x-icon name="{{ isset($regions[$name]) ? 'pin' : 'globe' }}"/></span>{{ $name }}</h3>
                                <p>{{ $description }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- What you can expect --}}
<section class="nll-section" aria-labelledby="expect-title">
    <div class="nll-wrap">
        <div class="nll-heading" data-reveal>
            <div>
                <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>{{ __('Our standard, in every language') }}</span>
                <h2 id="expect-title">{!! __('What You Can Expect in <em>Every Language</em>') !!}</h2>
            </div>
        </div>
        <div class="nll-feature-grid">
            @foreach ($page['expectations'] as $item)
                <article class="nll-feature" data-reveal>
                    <div class="nll-feature-top">
                        <span class="nll-icon"><x-icon :name="$item['icon']"/></span>
                    </div>
                    <h3>{{ $item['label'] }}</h3>
                    <p>{{ $item['text'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

<x-cta-band compact eyebrow="{{ __('Start a language project') }}" :title="e($page['cta_heading'])">
    <p>{{ $page['cta'] }}</p>
    <div class="nll-actions">
        <a class="nll-button nll-button-gold" href="/contact">{{ __('Request a Quote') }} <span class="nll-arrow" aria-hidden="true">→</span></a>
        <a class="nll-button nll-button-ghost" href="{{ config('site.contact_info.whatsapp_url') }}" target="_blank" rel="noopener">WhatsApp {{ config('site.contact_info.phone') }} <span aria-hidden="true">↗</span></a>
    </div>
    <p class="nll-cta-note">{{ $page['cta_note'] }}</p>
    <nav class="nll-cta-related" aria-label="{{ __('Related services') }}">
        <span>{{ __('Related') }}</span>
        @foreach ($page['related_links'] as $link)
            <a href="{{ $link['href'] }}">{{ $link['label'] }}</a>
        @endforeach
    </nav>
</x-cta-band>
