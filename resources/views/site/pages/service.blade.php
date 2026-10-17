@php
    $slug = request()->segment(2);

    // Visual identity per service: hero illustration, accent and icons for the "at a glance" cards.
    $themes = [
        'legal-technical-translation' => ['visual' => 'document', 'variant' => 'terracotta', 'icons' => ['book', 'compass', 'scale']],
        'subtitling' => ['visual' => 'subtitles', 'variant' => 'gold', 'icons' => ['film', 'captions', 'layers']],
        'transcription' => ['visual' => 'transcript', 'variant' => 'gold', 'icons' => ['mic', 'book', 'message']],
        'ai-language-data-mtpe' => ['visual' => 'data', 'variant' => 'teal', 'icons' => ['check', 'layers', 'chart']],
    ];
    $theme = $themes[$slug] ?? ['visual' => 'document', 'variant' => 'teal', 'icons' => ['check', 'check', 'check']];

    $otherServices = collect(config('site.services'))->except($slug);
@endphp

<x-page-hero :title="$page['headline']" :current="$page['title']" :eyebrow="$page['eyebrow'] ?? null"
             :crumbs="[['label' => 'Services', 'href' => '/services']]">
    <p class="nll-lead">{{ $page['intro'] }}</p>
    <div class="nll-actions">
        <a class="nll-button nll-button-gold" href="/contact">Request a quote <span class="nll-arrow" aria-hidden="true">→</span></a>
        <a class="nll-button nll-button-ghost" href="#service-details">How it works</a>
    </div>

    <x-slot:aside>
        @switch($theme['visual'])
            @case('subtitles')
                <div class="nll-visual-card nll-visual-video" aria-hidden="true">
                    <div class="nll-video-frame">
                        <span class="nll-video-play">▶</span>
                        <span class="nll-video-caption">Subtitles that respect the speaker,<br>the screen and the viewer.</span>
                    </div>
                    <div class="nll-timeline-rows">
                        <div><code>00:01:12,400 → 00:01:15,000</code><span class="nll-bar" style="--w: 70%"></span></div>
                        <div><code>00:01:15,200 → 00:01:18,600</code><span class="nll-bar" style="--w: 52%"></span></div>
                    </div>
                    <div class="nll-visual-foot"><span class="nll-visual-badge">✓ Timing reviewed</span><span>SRT · WebVTT · EBU-STL</span></div>
                </div>
                @break
            @case('transcript')
                <div class="nll-visual-card" aria-hidden="true">
                    <div class="nll-visual-bar"><i></i><i></i><i></i><span>interview-transcript.docx</span></div>
                    <div class="nll-wave nll-wave-wide">
                        @foreach ([10, 18, 26, 14, 32, 22, 36, 16, 28, 12, 24, 30, 15, 9, 20, 12, 26, 18, 30, 14, 22, 10, 18, 26] as $bar)
                            <i style="--h: {{ $bar }}px"></i>
                        @endforeach
                    </div>
                    <div class="nll-visual-body">
                        <div class="nll-transcript-row"><b>Speaker 1</b><code>[00:02:14]</code><span class="nll-bar" style="--w: 88%"></span><span class="nll-bar" style="--w: 64%"></span></div>
                        <div class="nll-transcript-row"><b>Speaker 2</b><code>[00:02:31]</code><span class="nll-bar" style="--w: 76%"></span></div>
                    </div>
                    <div class="nll-visual-foot"><span class="nll-visual-badge">✓ Speaker labels</span><span>Timestamps</span></div>
                </div>
                @break
            @case('data')
                <div class="nll-visual-card" aria-hidden="true">
                    <div class="nll-visual-bar"><i></i><i></i><i></i><span>evaluation-batch</span></div>
                    <div class="nll-visual-body">
                        @foreach ([['Segment 014', 92, 'Accept'], ['Segment 015', 64, 'Post-edit'], ['Segment 016', 81, 'Accept']] as [$label, $score, $status])
                            <div class="nll-score-row">
                                <span>{{ $label }}</span>
                                <span class="nll-score-track"><span style="width: {{ $score }}%"></span></span>
                                <b class="{{ $status === 'Accept' ? 'is-ok' : 'is-edit' }}">{{ $status }}</b>
                            </div>
                        @endforeach
                    </div>
                    <div class="nll-visual-foot"><span class="nll-visual-badge">✓ Human-in-the-loop</span><span>Light · Full MTPE</span></div>
                </div>
                @break
            @default
                <div class="nll-visual-card nll-visual-doc" aria-hidden="true">
                    <div class="nll-doc-sheet">
                        <span class="nll-doc-title"></span>
                        <span class="nll-bar" style="--w: 92%"></span><span class="nll-bar" style="--w: 84%"></span>
                        <span class="nll-bar nll-bar-highlight" style="--w: 60%"></span>
                        <span class="nll-bar" style="--w: 88%"></span><span class="nll-bar" style="--w: 72%"></span>
                        <span class="nll-bar nll-bar-highlight" style="--w: 45%"></span>
                        <span class="nll-bar" style="--w: 80%"></span>
                    </div>
                    <div class="nll-term-card">
                        <span>Glossary</span>
                        <b>Terminology aligned</b>
                    </div>
                    <div class="nll-visual-foot"><span class="nll-visual-badge">✓ Independent review</span><span>Contracts · Manuals</span></div>
                </div>
        @endswitch
    </x-slot:aside>
</x-page-hero>

@if (!empty($page['notice']))
    <div class="nll-wrap nll-notice-wrap" data-reveal>
        <div class="nll-notice" role="note">
            <span class="nll-notice-icon" aria-hidden="true">i</span>
            <div><strong>Please note</strong><p>{{ $page['notice'] }}</p></div>
        </div>
    </div>
@endif

@if (count($cards))
    <section class="nll-section nll-section-tight" aria-labelledby="glance-title">
        <div class="nll-wrap">
            <div class="nll-heading" data-reveal>
                <div>
                    <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>At a glance</span>
                    <h2 id="glance-title">What this service <em>covers</em></h2>
                </div>
            </div>
            <div class="nll-glance-grid nll-variant-{{ $theme['variant'] }}">
                @foreach ($cards as $card)
                    <article class="nll-glance" data-reveal>
                        <span class="nll-icon"><x-icon :name="$theme['icons'][$loop->index] ?? 'check'"/></span>
                        <h3>{{ $card['title'] }}</h3>
                        <p>{{ $card['body'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif

@if (!empty($page['sections']))
    <section class="nll-section nll-section-sand" id="service-details" aria-labelledby="details-title">
        <div class="nll-wrap nll-detail-layout">
            <div class="nll-detail-main">
                <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>How we approach it</span>
                <h2 class="nll-h2" id="details-title">The details that <em>shape the work</em></h2>
                <div class="nll-detail-list">
                    @foreach ($page['sections'] as $section)
                        <article class="nll-detail" data-reveal>
                            <span class="nll-detail-index" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <div>
                                <h3>{{ $section['heading'] }}</h3>
                                <p>{{ $section['body'] }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            <aside class="nll-detail-aside" aria-label="Next steps">
                <div class="nll-quote-card">
                    <span class="nll-eyebrow nll-eyebrow-light"><span class="nll-dot" aria-hidden="true"></span>Request a quote</span>
                    <p>{{ $page['cta'] }}</p>
                    <a class="nll-button nll-button-gold" href="/contact">Request a quote <span class="nll-arrow" aria-hidden="true">→</span></a>
                    <a class="nll-quote-alt" href="https://wa.me/250788750971" target="_blank" rel="noopener">Or message us on WhatsApp <span aria-hidden="true">↗</span></a>
                </div>
                <nav class="nll-related-card" aria-label="Other services">
                    <span class="nll-related-title">Other services</span>
                    @foreach ($otherServices as $key => $service)
                        <a href="/services/{{ $key }}">{{ $service['title'] }} <span class="nll-arrow" aria-hidden="true">→</span></a>
                    @endforeach
                    <a class="nll-related-all" href="/services">All services <span class="nll-arrow" aria-hidden="true">→</span></a>
                </nav>
            </aside>
        </div>
    </section>
@endif

@if (!empty($page['cta']))
    <x-cta-band compact eyebrow="Ready when you are" :title="e($page['cta'])">
        <div class="nll-actions">
            <a class="nll-button nll-button-gold" href="/contact">Let’s talk <span class="nll-arrow" aria-hidden="true">→</span></a>
            <a class="nll-button nll-button-ghost" href="/services">Explore all services</a>
        </div>
    </x-cta-band>
@endif
