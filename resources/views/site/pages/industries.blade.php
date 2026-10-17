@php
    $sectorIcons = ['people', 'layers', 'chip', 'film', 'scale', 'book'];
    $sectorVariants = ['teal', 'teal', 'navy', 'gold', 'terracotta', 'gold'];
@endphp

<x-page-hero :title="$page['headline']" :current="$page['title']" :eyebrow="$page['eyebrow']">
    <h2 class="nll-subtitle">{{ $page['subheading'] }}</h2>
    <p class="nll-lead">{{ $page['intro'] }}</p>
    <p class="nll-lead nll-lead-secondary">{{ $page['supporting_copy'] }}</p>
    <div class="nll-actions">
        <a class="nll-button nll-button-gold" href="/contact">{{ __('Request a Quote') }} <span class="nll-arrow" aria-hidden="true">→</span></a>
        <a class="nll-button nll-button-ghost" href="/contact">{{ __('Book a Consultation') }}</a>
    </div>

    <x-slot:aside>
        {{-- The intro's four examples: outreach guide, legal filing, mobile app and AI dataset. --}}
        <div class="nll-contexts" aria-hidden="true">
            <span class="nll-context-tag">{{ __('Never one-size-fits-all') }}</span>

            <div class="nll-context-card nll-context-ui">
                <span class="nll-context-head"><span class="nll-context-icon"><x-icon name="layers"/></span>{{ __('Mobile app') }}</span>
                <div class="nll-context-screen">
                    <span class="nll-bar" style="--w: 60%"></span>
                    <span class="nll-context-button">{{ __('Continue') }}</span>
                </div>
                <span class="nll-context-note">{{ __('Short, natural UI text') }}</span>
            </div>

            <div class="nll-context-card nll-context-legal">
                <span class="nll-context-head"><span class="nll-context-icon"><x-icon name="scale"/></span>{{ __('Legal filing') }}</span>
                <span class="nll-bar" style="--w: 90%"></span>
                <span class="nll-bar nll-bar-highlight" style="--w: 55%"></span>
                <span class="nll-bar" style="--w: 75%"></span>
                <span class="nll-context-note">{{ __('Faithful wording') }}</span>
            </div>

            <div class="nll-context-card nll-context-outreach">
                <span class="nll-context-head"><span class="nll-context-icon"><x-icon name="people"/></span>{{ __('Outreach guide') }}</span>
                <span class="nll-bar" style="--w: 84%"></span>
                <span class="nll-bar" style="--w: 62%"></span>
                <span class="nll-context-note">{{ __('Clear, everyday language') }}</span>
            </div>

            <div class="nll-context-card nll-context-data">
                <span class="nll-context-head"><span class="nll-context-icon"><x-icon name="chip"/></span>{{ __('AI dataset') }}</span>
                <span class="nll-context-labels"><i>NER</i><i>{{ __('Intent') }}</i><i>{{ __('Sentiment') }}</i></span>
                <span class="nll-context-note">{{ __('Consistent annotation') }}</span>
            </div>
        </div>
    </x-slot:aside>
</x-page-hero>

<section class="nll-section nll-section-tight" aria-labelledby="sectors-overview-title">
    <div class="nll-wrap">
        <div class="nll-heading" data-reveal>
            <div>
                <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>{{ __('Who we work with') }}</span>
                <h2 id="sectors-overview-title">{!! __('Our Six Core <em>Sectors</em>') !!}</h2>
            </div>
            <p class="nll-heading-aside">{{ __('Select a sector to jump to its solutions.') }}</p>
        </div>

        <div class="nll-sector-cards">
            @foreach ($page['sectors'] as $sector)
                <a class="nll-tool-group nll-sector-card nll-variant-{{ $sectorVariants[$loop->index] ?? 'teal' }}" href="#sector-{{ $loop->iteration }}" data-reveal>
                    <span class="nll-icon nll-tool-icon" aria-hidden="true"><x-icon :name="$sectorIcons[$loop->index] ?? 'compass'"/></span>
                    <h3>{{ $sector['name'] }}</h3>
                    <p>{{ $sector['summary'] }}</p>
                    <span class="nll-link">{{ __('View solutions') }}<span class="visually-hidden">: {{ $sector['name'] }}</span> <span class="nll-arrow" aria-hidden="true">↓</span></span>
                </a>
            @endforeach
        </div>
    </div>
</section>

<section class="nll-section nll-section-sand" aria-labelledby="sector-solutions-title">
    <div class="nll-wrap">
        <div class="nll-heading" data-reveal>
            <div>
                <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>{{ __('What we deliver in each sector') }}</span>
                <h2 id="sector-solutions-title">{!! __('Sector <em>Solutions</em>') !!}</h2>
            </div>
        </div>

        <div class="nll-sector-grid">
            @foreach ($page['sectors'] as $sector)
                <article class="nll-sector nll-variant-{{ $sectorVariants[$loop->index] ?? 'teal' }}" id="sector-{{ $loop->iteration }}" data-reveal>
                    <header class="nll-sector-head">
                        <span class="nll-icon"><x-icon :name="$sectorIcons[$loop->index] ?? 'compass'"/></span>
                        <h3>{{ $loop->iteration }}. {{ $sector['heading'] }}</h3>
                    </header>

                    <p class="nll-sector-challenge"><strong>{{ __('The challenge:') }}</strong> {{ $sector['challenge'] }}</p>

                    <ul class="nll-sector-points">
                        @foreach ($sector['points'] as $point)
                            <li><strong>{{ $point['label'] }}:</strong> {{ $point['text'] }}</li>
                        @endforeach
                    </ul>

                    <p class="nll-sector-links">
                        <span>{{ __('Learn more:') }}</span>
                        @foreach ($sector['links'] as $link)
                            <a class="nll-link" href="{{ $link['href'] }}">{{ $link['label'] }} <span class="nll-arrow" aria-hidden="true">→</span></a>
                        @endforeach
                    </p>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="nll-section" aria-labelledby="domain-fit-title">
    <div class="nll-wrap">
        <div class="nll-heading" data-reveal>
            <div>
                <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>{{ __('The difference it makes') }}</span>
                <h2 id="domain-fit-title">{!! __('Why Domain Fit <em>Matters</em>') !!}</h2>
            </div>
        </div>

        <div class="nll-table-wrap" data-reveal>
            <table class="nll-table nll-compare-table">
                <thead>
                    <tr><th scope="col">{{ __('Generic approach') }}</th><th scope="col">{{ __('Nile Lingua Lab approach') }}</th></tr>
                </thead>
                <tbody>
                    @foreach ($page['domain_fit'] as $row)
                        <tr>
                            <td data-label="{{ __('Generic approach') }}"><span class="nll-compare-x" aria-hidden="true">✕</span>{{ $row['generic'] }}</td>
                            <td data-label="{{ __('Nile Lingua Lab approach') }}"><span class="nll-compare-check" aria-hidden="true">✓</span>{{ $row['nll'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

<x-cta-band compact eyebrow="{{ __('Talk to us about your sector') }}" :title="e($page['cta_heading'])">
    <p>{{ $page['cta'] }}</p>
    <div class="nll-actions">
        <a class="nll-button nll-button-gold" href="/contact">{{ __('Request a Quote') }} <span class="nll-arrow" aria-hidden="true">→</span></a>
        <a class="nll-button nll-button-ghost" href="{{ config('site.contact_info.whatsapp_url') }}" target="_blank" rel="noopener">WhatsApp {{ config('site.contact_info.phone') }} <span aria-hidden="true">↗</span></a>
    </div>
    <p class="nll-cta-note">{{ $page['cta_note'] }}</p>
</x-cta-band>
