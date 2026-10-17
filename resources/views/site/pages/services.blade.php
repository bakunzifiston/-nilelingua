@php
    $pillarIcons = ['globe', 'document', 'play', 'wave'];
    $pillarVariants = ['teal', 'terracotta', 'gold', 'navy'];
@endphp

<x-page-hero :title="$page['headline']" :current="$page['title']" :eyebrow="$page['eyebrow']">
    <p class="nll-lead">{{ $page['intro'] }}</p>
    <p class="nll-lead nll-lead-secondary">{{ $page['supporting_copy'] }}</p>
    <div class="nll-actions">
        <a class="nll-button nll-button-gold" href="/contact">{{ __('Request a Quote') }} <span class="nll-arrow" aria-hidden="true">→</span></a>
        <a class="nll-button nll-button-ghost" href="/contact">{{ __('Book a Consultation') }}</a>
    </div>

    <x-slot:aside>
        <nav class="nll-jump-card" aria-label="{{ __('Service pillars') }}">
            <span class="nll-jump-title">{{ __('Our Four Core Service Pillars') }}</span>
            <ol>
                @foreach ($page['pillars'] as $pillar)
                    <li>
                        <a href="#pillar-{{ $loop->iteration }}">
                            <span class="nll-jump-index">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <span>{{ $pillar['title'] }}</span>
                            <span class="nll-arrow" aria-hidden="true">↓</span>
                        </a>
                    </li>
                @endforeach
            </ol>
        </nav>
    </x-slot:aside>
</x-page-hero>

<section class="nll-section" aria-labelledby="pillars-title">
    <div class="nll-wrap">
        <div class="nll-heading" data-reveal>
            <div>
                <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>{{ __('Our Four Core Service Pillars') }}</span>
                <h2 id="pillars-title">{!! __('Language solutions, built around <em>your work.</em>') !!}</h2>
            </div>
        </div>

        <div class="nll-pillars">
            @foreach ($page['pillars'] as $pillar)
                <article class="nll-pillar nll-variant-{{ $pillarVariants[$loop->index] ?? 'teal' }}" id="pillar-{{ $loop->iteration }}" data-reveal>
                    <header class="nll-pillar-head">
                        <span class="nll-icon"><x-icon :name="$pillarIcons[$loop->index] ?? 'globe'"/></span>
                        <span class="nll-pillar-index" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3><a href="{{ $pillar['href'] }}">{{ $pillar['title'] }}</a></h3>
                    </header>

                    <dl class="nll-pillar-details">
                        <div>
                            <dt>{{ __('Key capabilities') }}</dt>
                            <dd>
                                <ul class="nll-tags">
                                    @foreach (array_map('trim', explode(',', $pillar['capabilities'])) as $capability)
                                        <li>{{ $capability }}</li>
                                    @endforeach
                                </ul>
                            </dd>
                        </div>
                        <div>
                            <dt>{{ __('Core deliverables') }}</dt>
                            <dd>{{ $pillar['deliverables'] }}</dd>
                        </div>
                        <div>
                            <dt>{{ __('Typical clients') }}</dt>
                            <dd>{{ $pillar['clients'] }}</dd>
                        </div>
                    </dl>

                    <footer class="nll-pillar-foot">
                        <a class="nll-link" href="{{ $pillar['href'] }}">{{ __('Explore service') }}<span class="visually-hidden">: {{ $pillar['title'] }}</span> <span class="nll-arrow" aria-hidden="true">→</span></a>
                        <a class="nll-link nll-link-muted" href="/contact?service={{ basename($pillar['href']) }}#quote-form">{{ __('Request a quote for :service', ['service' => $pillar['title']]) }} <span class="nll-arrow" aria-hidden="true">→</span></a>
                        @if ($pillar['href'] === '/services/subtitling')
                            <a class="nll-link nll-link-muted" href="/services/transcription">{{ __('Transcription') }} <span class="nll-arrow" aria-hidden="true">→</span></a>
                        @endif
                    </footer>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="nll-section nll-section-sand" aria-labelledby="process-title">
    <div class="nll-wrap">
        <div class="nll-heading" data-reveal>
            <div>
                <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>{{ __('A clear, careful process') }}</span>
                <h2 id="process-title">{!! __('How We <em>Work</em>') !!}</h2>
            </div>
            <p class="nll-heading-aside">{{ __('Every project follows the same four steps:') }}</p>
        </div>
        @include('site.partials.steps', ['steps' => $page['workflow']])
    </div>
</section>

<section class="nll-section" aria-labelledby="reach-title">
    <div class="nll-wrap">
        <div class="nll-heading" data-reveal>
            <div>
                <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>{{ __('Across East Africa and beyond') }}</span>
                <h2 id="reach-title">{!! __('Languages and <em>Industries</em>') !!}</h2>
            </div>
        </div>
        <div class="nll-split-panels">
            <article class="nll-panel" data-reveal>
                <span class="nll-icon"><x-icon name="message"/></span>
                <h3>{{ __('Languages') }}</h3>
                <ul class="nll-tags nll-tags-lg">
                    @foreach ($page['languages'] as $language)
                        <li>{{ $language }}</li>
                    @endforeach
                </ul>
                <a class="nll-link" href="/languages">{{ __('See all languages') }} <span class="nll-arrow" aria-hidden="true">→</span></a>
            </article>
            <article class="nll-panel" data-reveal>
                <span class="nll-icon nll-icon-gold"><x-icon name="compass"/></span>
                <h3>{{ __('Sectors') }}</h3>
                <ul class="nll-sector-list">
                    @foreach ($page['industries'] as $industry)
                        <li>{{ $industry }}</li>
                    @endforeach
                </ul>
                <a class="nll-link" href="/industries">{{ __('Explore industries') }} <span class="nll-arrow" aria-hidden="true">→</span></a>
            </article>
        </div>
    </div>
</section>

<x-cta-band :eyebrow="__('Partner With Nile Lingua Lab')" :title="__('Let’s build the right language <em>workflow.</em>')">
    <p>{{ $page['partner_copy'] }}</p>
    <div class="nll-actions">
        <a class="nll-button nll-button-gold" href="/contact">{{ __('Request a Quote') }} <span class="nll-arrow" aria-hidden="true">→</span></a>
        <a class="nll-button nll-button-ghost" href="/contact">{{ __('Contact Our Team') }}</a>
    </div>
    <p class="nll-cta-note">{{ $page['contact_note'] }}</p>
</x-cta-band>
