@php
    $slug = request()->segment(2);

    // Visual identity per service: hero illustration and accent colour.
    $themes = [
        'legal-technical-translation' => ['visual' => 'document', 'variant' => 'terracotta'],
        'subtitling' => ['visual' => 'subtitles', 'variant' => 'gold'],
        'transcription' => ['visual' => 'transcript', 'variant' => 'gold'],
        'ai-language-data-mtpe' => ['visual' => 'data', 'variant' => 'teal'],
    ];
    $theme = $themes[$slug] ?? ['visual' => 'document', 'variant' => 'teal'];
    $languages = config('site.pages.services.languages', []);
    $quoteUrl = '/contact?service='.$slug.'#quote-form';
@endphp

<x-page-hero :title="$page['headline']" :current="$page['title']" :eyebrow="$page['eyebrow'] ?? null"
             :crumbs="[['label' => __('Services'), 'href' => '/services']]">
    @if (!empty($page['subheading']))
        <h2 class="nll-subtitle">{{ $page['subheading'] }}</h2>
    @endif
    <p class="nll-lead">{{ $page['intro'] }}</p>
    <div class="nll-actions">
        <a class="nll-button nll-button-gold" href="{{ $quoteUrl }}">{{ __('Request a Quote') }} <span class="nll-arrow" aria-hidden="true">→</span></a>
        <a class="nll-button nll-button-ghost" href="{{ $quoteUrl }}">{{ $page['secondary_cta'] ?? 'Book a Consultation' }}</a>
    </div>

    <x-slot:aside>
        @switch($theme['visual'])
            @case('subtitles')
                <div class="nll-visual-card nll-visual-video" aria-hidden="true">
                    <div class="nll-video-frame">
                        <span class="nll-video-play">▶</span>
                        <span class="nll-video-caption">{!! __('Subtitles in the languages<br>your viewers actually speak.') !!}</span>
                    </div>
                    <div class="nll-timeline-rows">
                        <div><code>00:01:12,400 → 00:01:15,000</code><span class="nll-bar" style="--w: 70%"></span></div>
                        <div><code>00:01:15,200 → 00:01:18,600</code><span class="nll-bar" style="--w: 52%"></span></div>
                    </div>
                    <div class="nll-visual-foot"><span class="nll-visual-badge">{{ __('✓ Timing reviewed') }}</span><span>SRT · VTT · STL · ASS</span></div>
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
                        <div class="nll-transcript-row"><b>{{ __('Speaker 1') }}</b><code>[00:02:14]</code><span class="nll-bar" style="--w: 88%"></span><span class="nll-bar" style="--w: 64%"></span></div>
                        <div class="nll-transcript-row"><b>{{ __('Speaker 2') }}</b><code>[00:02:31]</code><span class="nll-bar" style="--w: 76%"></span></div>
                    </div>
                    <div class="nll-visual-foot"><span class="nll-visual-badge">{{ __('✓ Speaker labels') }}</span><span>.docx · .txt · .csv</span></div>
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
                    <div class="nll-visual-foot"><span class="nll-visual-badge">{{ __('✓ Human-in-the-loop') }}</span><span>{{ __('Light · Full MTPE') }}</span></div>
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
                        <span>{{ __('Glossary') }}</span>
                        <b>{{ __('Terminology aligned') }}</b>
                    </div>
                    <div class="nll-visual-foot"><span class="nll-visual-badge">{{ __('✓ Independent editing') }}</span><span>{{ __('TM · Termbase') }}</span></div>
                </div>
        @endswitch
    </x-slot:aside>
</x-page-hero>

{{-- Capabilities / specializations --}}
@if (!empty($page['capabilities']))
    <section class="nll-section" aria-labelledby="capabilities-title">
        <div class="nll-wrap">
            <div class="nll-heading" data-reveal>
                <div>
                    <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>{{ __('What we deliver') }}</span>
                    <h2 id="capabilities-title">{{ $page['capabilities_heading'] }}</h2>
                </div>
            </div>
            <div class="nll-capability-grid nll-variant-{{ $theme['variant'] }}">
                @foreach ($page['capabilities'] as $capability)
                    <article class="nll-capability {{ !empty($capability['link']) ? 'is-pointer' : '' }} {{ !empty($capability['wide']) ? 'is-wide' : '' }}" data-reveal>
                        <span class="nll-icon"><x-icon :name="$capability['icon'] ?? 'check'"/></span>
                        <h3>{{ $capability['title'] }}</h3>
                        <p>
                            {{ $capability['body'] }}
                            @if (!empty($capability['link']))
                                <a href="{{ $capability['link']['href'] }}">{{ $capability['link']['label'] }}</a>.
                            @endif
                        </p>
                        @if (!empty($capability['items']))
                            <ul class="nll-capability-items">
                                @foreach ($capability['items'] as $item)
                                    <li><strong>{{ $item['label'] }}:</strong> {{ $item['text'] }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif

{{-- Languages --}}
@if (!empty($page['languages_heading']))
    <section class="nll-section nll-section-tight nll-section-sand" aria-labelledby="languages-title">
        <div class="nll-wrap nll-service-languages" data-reveal>
            <div>
                <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>{{ __('Native-speaker linguists') }}</span>
                <h2 class="nll-h2" id="languages-title">{{ $page['languages_heading'] }}</h2>
                <a class="nll-link" href="/languages">{{ __('See all supported languages') }} <span class="nll-arrow" aria-hidden="true">→</span></a>
            </div>
            <div class="nll-panel">
                <p class="nll-panel-copy"><strong>{{ __('Languages:') }}</strong></p>
                <ul class="nll-tags nll-tags-lg">
                    @foreach ($languages as $language)
                        <li>{{ $language }}</li>
                    @endforeach
                </ul>
                <p class="nll-panel-note">{{ __('Need another language?') }} <a href="/contact">{{ __('Ask us') }}</a>.</p>
                @if (!empty($page['languages_extra']))
                    <p class="nll-panel-note"><strong>{{ $page['languages_extra']['label'] }}:</strong> {{ $page['languages_extra']['text'] }}</p>
                @endif
            </div>
        </div>
    </section>
@endif

{{-- Process --}}
@if (!empty($page['steps']))
    <section class="nll-section" aria-labelledby="steps-title">
        <div class="nll-wrap">
            <div class="nll-heading" data-reveal>
                <div>
                    <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>{{ __('A clear, careful process') }}</span>
                    <h2 id="steps-title">{{ $page['steps_heading'] }}</h2>
                </div>
            </div>
            @include('site.partials.steps', ['steps' => $page['steps']])
            @if (!empty($page['steps_note']))
                <p class="nll-steps-note"><span aria-hidden="true">✳</span>{{ $page['steps_note'] }}</p>
            @endif
        </div>
    </section>
@endif

{{-- Confidentiality / responsible data --}}
@foreach ($page['text_sections'] ?? [] as $section)
    <section class="nll-section nll-section-tight nll-section-sand" aria-labelledby="text-section-{{ $loop->iteration }}">
        <div class="nll-wrap">
            <div class="nll-assurance" data-reveal>
                <span class="nll-icon"><x-icon :name="$section['icon'] ?? 'shield'"/></span>
                <div>
                    <h2 id="text-section-{{ $loop->iteration }}">{{ $section['heading'] }}</h2>
                    <p>{{ $section['body'] }}</p>
                </div>
            </div>
        </div>
    </section>
@endforeach

<x-cta-band compact eyebrow="{{ __('Request a quote') }}" :title="e($page['cta_heading'] ?? __('Get a Quote'))">
    @if (!empty($page['cta']))
        <p>{{ $page['cta'] }}</p>
    @endif
    <div class="nll-actions">
        <a class="nll-button nll-button-gold" href="{{ $quoteUrl }}">{{ __('Request a Quote') }} <span class="nll-arrow" aria-hidden="true">→</span></a>
        <a class="nll-button nll-button-ghost" href="{{ config('site.contact_info.whatsapp_url') }}" target="_blank" rel="noopener">WhatsApp {{ config('site.contact_info.phone') }} <span aria-hidden="true">↗</span></a>
    </div>
    @if (!empty($page['related_links']))
        <nav class="nll-cta-related" aria-label="{{ __('Related pages') }}">
            <span>{{ __('Related') }}</span>
            @foreach ($page['related_links'] as $link)
                <a href="{{ $link['href'] }}">{{ $link['label'] }}</a>
            @endforeach
        </nav>
    @endif
</x-cta-band>
