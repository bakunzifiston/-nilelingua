@php
    $supported = config('site.pages.services.languages', []);
    $regionIcons = ['globe', 'compass', 'book', 'pin', 'people', 'sparkle'];
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
        <div class="nll-wrap nll-coverage-band-grid">
            <div data-reveal>
                <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>Headquartered in Kigali, Rwanda</span>
                <h2 id="supported-title">Languages we <em>work in</em></h2>
                <ul class="nll-language-grid">
                    @foreach ($supported as $language)
                        <li>{{ $language }}</li>
                    @endforeach
                </ul>
                <p class="nll-coverage-band-note">Need another language? <a href="/contact">Ask us</a>.</p>
            </div>
            <div class="nll-coverage-band-map" aria-hidden="true" data-reveal>
                <svg class="nll-map" viewBox="-10 -10 410 430" fill="none">
                    <use class="nll-map-land" href="#nll-africa"/>
                    <use class="nll-map-outline" href="#nll-africa"/>
                    <ellipse cx="265" cy="205" rx="96" ry="84" fill="url(#nll-region-glow)"/>
                    <path class="nll-map-region" d="M196 150c26-34 92-44 128-18 30 22 32 76 6 108-24 30-78 36-108 14-30-22-48-70-26-104Z"/>
                    <circle class="nll-map-pulse" cx="264" cy="224" r="14"/>
                    <circle class="nll-map-hq" cx="264" cy="224" r="6.5"/>
                </svg>
                <span class="nll-hq-label">Kigali HQ</span>
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
