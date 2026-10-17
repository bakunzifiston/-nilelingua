@php
    $founder = $page['founder'];
    $initials = collect(explode(' ', $founder['name']))->map(fn ($part) => mb_substr($part, 0, 1))->implode('');
@endphp

<x-page-hero :title="$page['headline']" :current="$page['title']" :eyebrow="$page['eyebrow']">
    <p class="nll-lead">{{ $page['intro'] }}</p>
    <div class="nll-actions">
        <a class="nll-button nll-button-gold" href="/contact">{{ __('Request a Quote') }} <span class="nll-arrow" aria-hidden="true">→</span></a>
        <a class="nll-button nll-button-ghost" href="/contact">{{ __('Talk to Our Team') }}</a>
    </div>

    <x-slot:aside>
        {{-- "Listening" illustration: a voice at the centre, the client's brief and our first question. --}}
        <div class="nll-listen" aria-hidden="true">
            <span class="nll-listen-ring nll-listen-ring-1"></span>
            <span class="nll-listen-ring nll-listen-ring-2"></span>
            <span class="nll-listen-ring nll-listen-ring-3"></span>
            <div class="nll-listen-core">
                <span class="nll-wave">
                    @foreach ([10, 18, 28, 16, 34, 22, 40, 24, 32, 14, 26, 18, 10] as $bar)
                        <i style="--h: {{ $bar }}px"></i>
                    @endforeach
                </span>
            </div>
            <div class="nll-listen-bubble nll-listen-brief">
                <span class="nll-listen-label">{{ __('Your brief') }}</span>
                <span class="nll-bar" style="--w: 92%"></span>
                <span class="nll-bar" style="--w: 70%"></span>
                <span class="nll-bar" style="--w: 80%"></span>
            </div>
            <div class="nll-listen-bubble nll-listen-question">
                <span class="nll-listen-label">{{ __('Our first question') }}</span>
                <b>{{ __('Who is this for?') }}</b>
            </div>
            <span class="nll-listen-tag nll-listen-tag-1">{{ __('Audience') }}</span>
            <span class="nll-listen-tag nll-listen-tag-2">{{ __('Context') }}</span>
            <span class="nll-listen-tag nll-listen-tag-3">{{ __('Tone') }}</span>
        </div>
    </x-slot:aside>
</x-page-hero>

{{-- Mission & Vision --}}
<section class="nll-section" aria-labelledby="mission-title">
    <div class="nll-wrap">
        <div class="nll-heading" data-reveal>
            <div>
                <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>{{ __('Why we exist') }}</span>
                <h2 id="mission-title">{!! __('Our Mission &amp; <em>Vision</em>') !!}</h2>
            </div>
        </div>
        <div class="nll-mv-grid">
            <article class="nll-mv" data-reveal>
                <span class="nll-icon"><x-icon name="compass"/></span>
                <h3>{{ __('Mission') }}</h3>
                <p>{{ $page['mission'] }}</p>
            </article>
            <article class="nll-mv is-dark" data-reveal>
                <span class="nll-icon"><x-icon name="sparkle"/></span>
                <h3>{{ __('Vision') }}</h3>
                <p>{{ $page['vision'] }}</p>
            </article>
        </div>
    </div>
</section>

{{-- Our Base in Kigali --}}
<section class="nll-section nll-section-tight nll-section-sand" aria-labelledby="base-title">
    <div class="nll-wrap nll-base" data-reveal>
        <span class="nll-icon nll-icon-gold"><x-icon name="pin"/></span>
        <div>
            <h2 class="nll-h2" id="base-title">{!! __('Our Base in <em>Kigali</em>') !!}</h2>
            <p>{{ $page['base'] }}</p>
        </div>
    </div>
</section>

{{-- What We Do --}}
<section class="nll-section" aria-labelledby="what-title">
    <div class="nll-wrap">
        <div class="nll-heading" data-reveal>
            <div>
                <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>{{ __('Four core service pillars') }}</span>
                <h2 id="what-title">{!! __('What We <em>Do</em>') !!}</h2>
            </div>
            <p class="nll-heading-aside">{{ $page['what_we_do_intro'] }}</p>
        </div>
        <div class="nll-about-pillars">
            @foreach ($page['pillars'] as $pillar)
                <article class="nll-about-pillar" data-reveal>
                    <span class="nll-icon"><x-icon :name="$pillar['icon']"/></span>
                    <h3><a href="{{ $pillar['href'] }}">{{ $pillar['label'] }}</a></h3>
                    <p>
                        @if (!empty($pillar['link_word']))
                            {!! str_replace(e($pillar['link_word']), '<a href="'.e($pillar['link_href']).'">'.e($pillar['link_word']).'</a>', e($pillar['text'])) !!}
                        @else
                            {{ $pillar['text'] }}
                        @endif
                    </p>
                    <a class="nll-link" href="{{ $pillar['href'] }}">{{ __('Learn more') }}<span class="visually-hidden">: {{ $pillar['label'] }}</span> <span class="nll-arrow" aria-hidden="true">→</span></a>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- Our Languages --}}
<section class="nll-section nll-section-tight nll-section-sand" aria-labelledby="languages-title">
    <div class="nll-wrap nll-service-languages" data-reveal>
        <div>
            <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>{{ __('15 languages') }}</span>
            <h2 class="nll-h2" id="languages-title">{!! __('Our <em>Languages</em>') !!}</h2>
            <a class="nll-link" href="/languages">{{ __('See all languages') }} <span class="nll-arrow" aria-hidden="true">→</span></a>
        </div>
        <div class="nll-panel">
            <p class="nll-panel-copy">{{ $page['languages_copy'] }}</p>
        </div>
    </div>
</section>

{{-- Our Values --}}
<section class="nll-section" aria-labelledby="values-title">
    <div class="nll-wrap nll-story">
        <div class="nll-story-intro" data-reveal>
            <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>{{ __('What guides us') }}</span>
            <h2 class="nll-h2" id="values-title">{!! __('Our <em>Values</em>') !!}</h2>
        </div>
        <div class="nll-story-rows">
            @foreach ($page['values'] as $value)
                <article class="nll-story-row" data-reveal>
                    <span class="nll-icon nll-story-icon"><x-icon :name="$value['icon']"/></span>
                    <h3>{{ $value['label'] }}</h3>
                    <p>{{ $value['text'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- Who We Serve --}}
<section class="nll-section nll-section-sand" aria-labelledby="serve-title">
    <div class="nll-wrap">
        <div class="nll-heading" data-reveal>
            <div>
                <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>{{ __('Our clients') }}</span>
                <h2 id="serve-title">{!! __('Who We <em>Serve</em>') !!}</h2>
            </div>
            <a class="nll-link" href="/industries">{{ __('Explore industries') }} <span class="nll-arrow" aria-hidden="true">→</span></a>
        </div>
        <div class="nll-principles nll-audiences">
            @foreach ($page['audiences'] as $audience)
                <article class="nll-principle" data-reveal>
                    <span class="nll-icon"><x-icon :name="$audience['icon']"/></span>
                    <h3>{{ $audience['label'] }}</h3>
                    <p>{{ $audience['text'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- Our Company + Founder Profile --}}
<section class="nll-section" aria-labelledby="founder-title">
    <div class="nll-wrap nll-founder-layout">
        <aside class="nll-founder-side" data-reveal>
            <div class="nll-founder-card">
                @if (!empty($founder['photo']))
                    <img class="nll-founder-photo" src="{{ asset($founder['photo']) }}" alt="{{ __('Portrait of :name, :role', ['name' => $founder['name'], 'role' => $founder['role']]) }}" width="320" height="320" loading="lazy">
                @else
                    <span class="nll-founder-monogram" aria-hidden="true">{{ $initials }}</span>
                @endif
                <strong>{{ $founder['name'] }}</strong>
                <span>{{ $founder['role'] }}</span>
                <ul class="nll-founder-facts">
                    @foreach ($founder['facts'] as $fact)
                        <li>{{ $fact }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="nll-company-card" aria-labelledby="company-title">
                <h2 id="company-title">{{ __('Our Company') }}</h2>
                <dl>
                    @foreach ($page['company'] as $item)
                        <div>
                            <dt>{{ $item['label'] }}</dt>
                            <dd>
                                @if (!empty($item['href']))
                                    <a href="{{ $item['href'] }}">{{ $item['text'] }}</a>
                                @else
                                    {{ $item['text'] }}
                                @endif
                            </dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </aside>

        <div class="nll-founder-main">
            <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>{{ __('Founder-led') }}</span>
            <h2 class="nll-h2" id="founder-title">{{ __('Founder Profile:') }} <em>{{ $founder['name'] }}</em></h2>
            <div class="nll-founder-bio">
                @foreach ($founder['paragraphs'] as $paragraph)
                    <p>{{ $paragraph }}@if ($loop->last && !empty($founder['linkedin'])) <a href="{{ $founder['linkedin'] }}" target="_blank" rel="noopener">{{ __('Connect with Adolphe on LinkedIn') }}</a>.@endif</p>
                @endforeach
            </div>

            <div class="nll-how-callout" data-reveal>
                <h2 id="how-title">{{ __('How We Work') }}</h2>
                <p>{{ $page['how_we_work'] }} <a href="/careers">{{ __('Careers page') }}</a>.</p>
            </div>
        </div>
    </div>
</section>

<x-cta-band compact eyebrow="{{ __('Start a conversation') }}" :title="e($page['cta_heading'])">
    <p>{{ $page['cta'] }}</p>
    <div class="nll-actions">
        <a class="nll-button nll-button-gold" href="/contact">{{ __('Request a Quote') }} <span class="nll-arrow" aria-hidden="true">→</span></a>
        <a class="nll-button nll-button-ghost" href="/contact">{{ __('Talk to Our Team') }}</a>
    </div>
    <p class="nll-cta-note">{{ $page['cta_note'] }}</p>
</x-cta-band>
