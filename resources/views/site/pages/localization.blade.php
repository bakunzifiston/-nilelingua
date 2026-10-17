@php
    $capabilityIcons = ['layers', 'globe', 'people', 'compass'];
@endphp

<x-page-hero :title="$page['headline']" :current="$page['title']" :eyebrow="$page['eyebrow']"
             :crumbs="[['label' => __('Services'), 'href' => '/services']]">
    <h2 class="nll-subtitle">{{ $page['intro'] }}</h2>
    <p class="nll-lead">{{ $page['hero_copy'] }}</p>
    <div class="nll-actions">
        <a class="nll-button nll-button-gold" href="/contact?service=localization#quote-form">{{ __('Request a Quote') }} <span class="nll-arrow" aria-hidden="true">→</span></a>
        <a class="nll-button nll-button-ghost" href="/contact">{{ __('Book a Consultation') }}</a>
    </div>

    <x-slot:aside>
        <div class="nll-visual-card" aria-hidden="true">
            <div class="nll-visual-bar"><i></i><i></i><i></i><span>strings.json</span></div>
            <div class="nll-visual-body">
                <div class="nll-string-row"><code>"onboarding.welcome"</code><span class="nll-string-pair"><s>{{ __('Welcome') }}</s><b>Murakaza neza</b></span></div>
                <div class="nll-string-row"><code>"checkout.greeting"</code><span class="nll-string-pair"><s>{{ __('Welcome') }}</s><b>Karibu</b></span></div>
                <div class="nll-string-row is-muted"><code>"format.date"</code><span class="nll-string-pair"><b>DD/MM/YYYY</b></span></div>
            </div>
            <div class="nll-visual-foot"><span class="nll-visual-badge">{{ __('✓ Placeholders verified') }}</span><span>XLIFF · JSON · PO</span></div>
        </div>
    </x-slot:aside>
</x-page-hero>

<section class="nll-section" aria-labelledby="capabilities-title">
    <div class="nll-wrap">
        <div class="nll-heading" data-reveal>
            <div>
                <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>{{ __('Localization expertise') }}</span>
                <h2 id="capabilities-title">{!! __('Core Localization <em>Capabilities</em>') !!}</h2>
            </div>
        </div>
        <div class="nll-feature-grid">
            @foreach ($page['capabilities'] as $capability)
                <article class="nll-feature" data-reveal>
                    <div class="nll-feature-top">
                        <span class="nll-icon"><x-icon :name="$capabilityIcons[$loop->index] ?? 'globe'"/></span>
                        <span class="nll-feature-index" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <h3>{{ $capability['title'] }}</h3>
                    <p>{{ $capability['body'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="nll-section nll-section-sand" aria-labelledby="workflow-title">
    <div class="nll-wrap">
        <div class="nll-heading" data-reveal>
            <div>
                <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>{{ __('A clear, careful process') }}</span>
                <h2 id="workflow-title">{!! __('How We <em>Localize</em>') !!}</h2>
            </div>
        </div>
        @include('site.partials.steps', ['steps' => $page['workflow']])
    </div>
</section>

<section class="nll-section" aria-labelledby="languages-title">
    <div class="nll-wrap">
        <div class="nll-heading" data-reveal>
            <div>
                <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>{{ __('Built around your markets and tools') }}</span>
                <h2 id="languages-title">{!! __('Languages We <em>Localize Into</em>') !!}</h2>
            </div>
        </div>
        <div class="nll-split-panels">
            <article class="nll-panel" data-reveal>
                <span class="nll-icon"><x-icon name="message"/></span>
                <h3>{{ __('Supported languages') }}</h3>
                <ul class="nll-tags nll-tags-lg">
                    @foreach ($page['languages'] as $language)
                        <li>{{ $language }}</li>
                    @endforeach
                </ul>
                <p class="nll-panel-note">{{ __('Need another language?') }} <a href="/contact">{{ __('Ask us') }}</a>.</p>
                <p class="nll-panel-note"><strong>{{ __('Source languages:') }}</strong> {{ implode(' '.__('and').' ', $page['source_languages']) }}.</p>
                <a class="nll-link" href="/languages">{{ __('See all supported languages') }} <span class="nll-arrow" aria-hidden="true">→</span></a>
            </article>
            <article class="nll-panel" data-reveal>
                <span class="nll-icon nll-icon-gold"><x-icon name="layers"/></span>
                <h3>{{ __('Tools & File Formats') }}</h3>
                <p class="nll-panel-copy">We work in {{ implode(', ', $page['tools']) }} for translation memory and terminology management, and handle the formats your developers already use:</p>
                <ul class="nll-tags">
                    @foreach ($page['tools'] as $tool)
                        <li>{{ $tool }}</li>
                    @endforeach
                </ul>
                <ul class="nll-tags nll-tags-format nll-tags-spaced" aria-label="{{ __('File formats') }}">
                    @foreach ($page['formats'] as $format)
                        <li>{{ $format }}</li>
                    @endforeach
                </ul>
                <p class="nll-panel-note">{{ __('Using a different tool or platform? We can adapt to your workflow.') }}</p>
            </article>
        </div>
    </div>
</section>

<x-cta-band :eyebrow="__('Start Your Localization Project')" :title="__('Bring your product <em>closer to its users.</em>')">
    <p>{{ $page['cta'] }}</p>
    <div class="nll-actions">
        <a class="nll-button nll-button-gold" href="/contact?service=localization#quote-form">{{ __('Request a Quote') }} <span class="nll-arrow" aria-hidden="true">→</span></a>
        <a class="nll-button nll-button-ghost" href="{{ config('site.contact_info.whatsapp_url') }}" target="_blank" rel="noopener">WhatsApp {{ config('site.contact_info.phone') }} <span aria-hidden="true">↗</span></a>
    </div>
    <nav class="nll-cta-related" aria-label="{{ __('Related pages') }}">
        <span>{{ __('Related') }}</span>
        @foreach ($page['related_links'] as $link)
            <a href="{{ $link['href'] }}">{{ $link['label'] }}</a>
        @endforeach
    </nav>
</x-cta-band>
