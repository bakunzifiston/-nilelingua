@extends('layouts.site')

@section('seo-title', 'African Language Services & AI Data | Nile Lingua Lab')
@section('body-class', 'home-page')

@section('page-styles')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endsection

@php
    // Presentation details for each homepage service, in the order defined in routes/web.php.
    $serviceThemes = [
        ['variant' => 'localization', 'kicker' => 'Digital adaptation'],
        ['variant' => 'translation', 'kicker' => 'Specialist translation'],
        ['variant' => 'audiovisual', 'kicker' => 'Audiovisual services'],
        ['variant' => 'data', 'kicker' => 'AI language technology'],
    ];
@endphp

@section('content')
    {{-- Hero --}}
    <section class="nll-hero" aria-labelledby="home-hero-title">
        <div class="nll-wrap nll-hero-grid">
            <div class="nll-hero-copy">
                <span class="nll-eyebrow nll-eyebrow-light"><span class="nll-dot" aria-hidden="true"></span>{{ $page['eyebrow'] }}</span>
                <h1 id="home-hero-title">{!! str_replace('Human-in-the-Loop', '<span class="nll-nowrap">Human-in-the-Loop</span>', e($page['headline'])) !!}</h1>
                <p class="nll-hero-subhead">{{ $page['intro'] }}</p>
                <p class="nll-hero-intro">Based in Kigali, Rwanda, Nile Lingua Lab (NLL) provides African language services that bridge linguistic divides across East Africa and beyond. We combine regional expertise with modern language technology to deliver enterprise translation, software and multimedia localization, and specialized language datasets for AI models.</p>
                <div class="nll-actions">
                    <a class="nll-button nll-button-gold" href="/contact">Request a Quote <span class="nll-arrow" aria-hidden="true">→</span></a>
                    <a class="nll-button nll-button-ghost" href="/services">Explore Our Services</a>
                </div>
                <p class="nll-hero-note"><span aria-hidden="true"></span>Regional expertise. Human-in-the-loop quality.</p>
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
                        <span class="nll-float-label">EN <span>→</span> RW</span>
                        <span class="nll-float-source">Welcome</span>
                        <span class="nll-float-target">Murakaza neza</span>
                        <span class="nll-float-check">Human reviewed</span>
                    </div>

                    <div class="nll-float nll-float-audio">
                        <span class="nll-float-label">Speech data · Swahili</span>
                        <span class="nll-wave">
                            @foreach ([9, 16, 24, 14, 30, 20, 34, 18, 26, 12, 22, 28, 15, 9, 19, 11] as $bar)
                                <i style="--h: {{ $bar }}px"></i>
                            @endforeach
                        </span>
                        <span class="nll-float-tags"><span>Karibu</span><span>Annotated</span></span>
                    </div>
                </div>
                <span class="nll-hero-caption">Language, culture &amp; connection</span>
            </div>
        </div>
        <div class="nll-wrap nll-hero-bottom">
            <span>Nile Lingua Lab · Kigali, Rwanda</span>
            <a href="#home-services">Discover our services <span aria-hidden="true">↓</span></a>
        </div>
    </section>

    {{-- Services --}}
    <section class="nll-section nll-services" id="home-services" aria-labelledby="home-services-title">
        <div class="nll-wrap">
            <div class="nll-heading" data-reveal>
                <div>
                    <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>African language expertise</span>
                    <h2 id="home-services-title">What We <em>Deliver</em></h2>
                </div>
                <p class="nll-heading-aside">Language, localization and human expertise to help your content reach African audiences.</p>
            </div>

            <div class="nll-service-grid">
                @foreach ($services as $service)
                    @php($theme = $serviceThemes[$loop->index] ?? $serviceThemes[0])
                    <article class="nll-service nll-service-{{ $theme['variant'] }}" data-reveal>
                        <div class="nll-service-top">
                            <span class="nll-service-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    @switch($theme['variant'])
                                        @case('localization')
                                            <circle cx="12" cy="12" r="8.5"/><path d="M3.8 9h16.4M3.8 15h16.4M12 3.6c2.1 2.3 3.1 5.1 3.1 8.4s-1 6.1-3.1 8.4c-2.1-2.3-3.1-5.1-3.1-8.4S9.9 5.9 12 3.6Z"/>
                                            @break
                                        @case('translation')
                                            <path d="M6 3.75h8l4.25 4.3v12.2H6z"/><path d="M14 3.9v4.5h4M9 12h6M9 15.5h6"/>
                                            @break
                                        @case('audiovisual')
                                            <rect x="3.5" y="5.25" width="17" height="13.5" rx="2"/><path d="m10 9 5 3-5 3V9Z"/>
                                            @break
                                        @default
                                            <path d="M4 13.5h2.5l2-6 3.5 11 3.5-13 2 8H20"/>
                                    @endswitch
                                </svg>
                            </span>
                            <span class="nll-service-kicker">{{ $theme['kicker'] }}</span>
                        </div>

                        <h3 class="nll-service-title">{{ $service['title'] }}</h3>
                        <p class="nll-service-copy">{{ $service['body'] }}</p>

                        @if (!empty($service['deliverables']))
                            <ul class="nll-deliverables">
                                @foreach ($service['deliverables'] as $deliverable)
                                    <li>
                                        @if (!empty($deliverable['href']))
                                            <a href="{{ $deliverable['href'] }}">
                                                <span class="nll-deliverable-title">{{ $deliverable['title'] }}</span>
                                                <span class="nll-deliverable-body">{{ $deliverable['body'] }}</span>
                                                <span class="nll-deliverable-link">Learn more <span class="nll-arrow" aria-hidden="true">→</span></span>
                                            </a>
                                        @else
                                            <span class="nll-deliverable-title">{{ $deliverable['title'] }}</span>
                                            <span class="nll-deliverable-body">{{ $deliverable['body'] }}</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        @if (!empty($service['href']))
                            <a class="nll-link" href="{{ $service['href'] }}">{{ $service['label'] }}<span class="visually-hidden">: {{ $service['title'] }}</span> <span class="nll-arrow" aria-hidden="true">→</span></a>
                        @endif

                        @if ($theme['variant'] === 'localization')
                            <svg class="nll-service-art" viewBox="0 0 220 120" fill="none" aria-hidden="true">
                                <rect x="20" y="16" width="120" height="84" rx="8"/><path d="M20 34h120M34 52h52M34 66h80M34 80h40"/>
                                <rect x="112" y="42" width="88" height="62" rx="8" class="nll-art-fill"/><path d="M126 62h44M126 76h60M126 90h30"/>
                            </svg>
                        @elseif ($theme['variant'] === 'translation')
                            <svg class="nll-service-art" viewBox="0 0 220 120" fill="none" aria-hidden="true">
                                <path d="M60 14h64l22 22v76H60z"/><path d="M124 14v22h22M78 56h50M78 70h50M78 84h32"/>
                                <circle cx="168" cy="92" r="18" class="nll-art-fill"/><path d="m160 92 6 6 11-12"/>
                            </svg>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Language coverage --}}
    <section class="nll-languages" id="home-languages" aria-labelledby="home-languages-title">
        <div class="nll-wrap nll-languages-grid">
            <div class="nll-languages-copy" data-reveal>
                <span class="nll-eyebrow nll-eyebrow-light"><span class="nll-dot" aria-hidden="true"></span>Regional Expertise, Continental Reach</span>
                <h2 id="home-languages-title">Africa speaks in <em>many voices.</em></h2>
                <p>We are headquartered in Kigali, Rwanda, and support 15 languages across East, Central and Northeast Africa, from Kinyarwanda and Swahili to Sango and Dinka, each with native-speaker linguists.</p>

                <div class="nll-stat">
                    <span class="nll-stat-number">15</span>
                    <span class="nll-stat-label">Languages<small>East, Central &amp; Northeast Africa</small></span>
                </div>

                <a class="nll-button nll-button-outline-light" href="/languages">See our languages <span class="nll-arrow" aria-hidden="true">→</span></a>
            </div>

            <div class="nll-coverage" data-reveal>
                <div class="nll-coverage-map">
                    <svg class="nll-map nll-map-large" viewBox="-10 -10 410 430" fill="none" aria-hidden="true">
                        <use class="nll-map-land" href="#nll-africa"/>
                        <use class="nll-map-outline" href="#nll-africa"/>
                        <ellipse cx="265" cy="205" rx="96" ry="84" fill="url(#nll-region-glow)"/>
                        <path class="nll-map-region" d="M196 150c26-34 92-44 128-18 30 22 32 76 6 108-24 30-78 36-108 14-30-22-48-70-26-104Z"/>
                        <path class="nll-map-leader" d="m201 188-91-23m163 20 41-54m-10 89 37 10m-73-6 16 76"/>
                        <circle class="nll-map-node" cx="201" cy="188" r="4.5"/>
                        <circle class="nll-map-node" cx="273" cy="185" r="4.5"/>
                        <circle class="nll-map-node" cx="301" cy="220" r="4.5"/>
                        <circle class="nll-map-pulse" cx="264" cy="224" r="14"/>
                        <circle class="nll-map-hq" cx="264" cy="224" r="6.5"/>
                    </svg>
                    <ul class="nll-coverage-labels" aria-label="Examples of supported languages">
                        <li class="nll-chip nll-chip-sango">Sango</li>
                        <li class="nll-chip nll-chip-dinka">Dinka</li>
                        <li class="nll-chip nll-chip-swahili">Swahili</li>
                        <li class="nll-chip nll-chip-kinyarwanda">Kinyarwanda</li>
                    </ul>
                    <span class="nll-hq-label" aria-hidden="true">Kigali HQ</span>
                </div>
                <p class="nll-coverage-note">And more across East, Central and Northeast Africa.</p>
            </div>
        </div>
    </section>

    {{-- Why organizations choose NLL --}}
    <section class="nll-section nll-why" aria-labelledby="home-why-title">
        <div class="nll-wrap nll-why-grid">
            <div class="nll-why-intro" data-reveal>
                <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>Built around people, language and trust</span>
                <h2 id="home-why-title">Why Organizations Choose <em>Nile Lingua Lab</em></h2>
            </div>

            <ol class="nll-why-list">
                <li data-reveal>
                    <span class="nll-why-index" aria-hidden="true">01</span>
                    <span class="nll-why-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="8.5"/><path d="M3.8 9h16.4M3.8 15h16.4M12 3.6c2.1 2.3 3.1 5.1 3.1 8.4s-1 6.1-3.1 8.4"/></svg></span>
                    <div>
                        <h3>Regional Expertise, Continental Reach</h3>
                        <p>Headquartered in Kigali, Rwanda, we support 15 languages across East, Central and Northeast Africa, with native-speaker linguists.</p>
                        <a class="nll-link" href="/languages">See our languages <span class="nll-arrow" aria-hidden="true">→</span></a>
                    </div>
                </li>
                <li data-reveal>
                    <span class="nll-why-index" aria-hidden="true">02</span>
                    <span class="nll-why-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 3 19 6v5c0 4.5-2.8 7.9-7 10-4.2-2.1-7-5.5-7-10V6l7-3Z"/><path d="m9 12 2 2 4-4"/></svg></span>
                    <div>
                        <h3>Human-in-the-Loop Quality Control</h3>
                        <p>Every project follows a multi-step workflow of translation, independent editing, proofreading and automated QA checks.</p>
                    </div>
                </li>
                <li data-reveal>
                    <span class="nll-why-index" aria-hidden="true">03</span>
                    <span class="nll-why-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="4" y="4" width="16" height="16" rx="3"/><path d="M8 12h8M12 8v8"/></svg></span>
                    <div>
                        <h3>Technology-Driven Efficiency</h3>
                        <p>We use Trados Studio, Wordfast Pro and MultiTerm for translation memory and terminology management, and Subtitle Edit and Audacity for audiovisual work. We can also adapt to your preferred tools and platforms.</p>
                        <a class="nll-link" href="/technology">Explore our technology <span class="nll-arrow" aria-hidden="true">→</span></a>
                    </div>
                </li>
                <li data-reveal>
                    <span class="nll-why-index" aria-hidden="true">04</span>
                    <span class="nll-why-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg></span>
                    <div>
                        <h3>Data Security &amp; Confidentiality</h3>
                        <p>We use secure file handling, encrypted file transfers and signed non-disclosure agreements (NDAs) for sensitive legal and enterprise materials.</p>
                    </div>
                </li>
            </ol>
        </div>
    </section>

    {{-- Tools & file compatibility --}}
    <section class="nll-section nll-tools" aria-labelledby="home-tools-title">
        <div class="nll-wrap">
            <div class="nll-heading" data-reveal>
                <div>
                    <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>Fits into the way you work</span>
                    <h2 id="home-tools-title">Tools &amp; File <em>Compatibility</em></h2>
                </div>
                <p class="nll-heading-aside">We work with industry-standard tools and the file formats your teams already use.</p>
            </div>

            <div class="nll-tools-grid">
                <section class="nll-tool-group" aria-labelledby="tools-tm" data-reveal>
                    <span class="nll-tool-index" aria-hidden="true">01</span>
                    <h3 id="tools-tm">Translation Memory &amp; Terminology</h3>
                    <ul class="nll-tags"><li>Trados Studio</li><li>Wordfast Pro</li><li>MultiTerm</li></ul>
                </section>
                <section class="nll-tool-group" aria-labelledby="tools-av" data-reveal>
                    <span class="nll-tool-index" aria-hidden="true">02</span>
                    <h3 id="tools-av">Audiovisual &amp; Subtitling</h3>
                    <ul class="nll-tags"><li>Subtitle Edit</li><li>Audacity</li></ul>
                    <ul class="nll-tags nll-tags-format" aria-label="Subtitle formats"><li>SRT</li><li>WebVTT</li><li>EBU-STL</li></ul>
                </section>
                <section class="nll-tool-group" aria-labelledby="tools-l10n" data-reveal>
                    <span class="nll-tool-index" aria-hidden="true">03</span>
                    <h3 id="tools-l10n">Localization Files</h3>
                    <ul class="nll-tags nll-tags-format"><li>JSON</li><li>XML</li><li>PO/POT</li><li>XLIFF</li><li>YAML</li><li>CSV</li><li>HTML5</li></ul>
                </section>
                <section class="nll-tool-group" aria-labelledby="tools-ai" data-reveal>
                    <span class="nll-tool-index" aria-hidden="true">04</span>
                    <h3 id="tools-ai">AI Language Data</h3>
                    <ul class="nll-tags"><li>Audio segmentation</li><li>Transcription</li><li>Text annotation</li></ul>
                </section>
            </div>

            <p class="nll-tools-note"><span aria-hidden="true">✳</span>Using a different platform or format? We can adapt to your workflow.</p>
        </div>
    </section>

    {{-- Final call to action --}}
    <section class="nll-cta" aria-labelledby="home-cta-title">
        <svg class="nll-cta-art" viewBox="0 0 600 400" fill="none" aria-hidden="true">
            <path d="M0 220c40 0 40-60 80-60s40 120 80 120 40-180 80-180 40 240 80 240 40-200 80-200 40 120 80 120 40-40 60-40"/>
            <path d="M0 240c40 0 40-40 80-40s40 80 80 80 40-120 80-120 40 160 80 160 40-140 80-140 40 80 80 80 40-20 60-20"/>
            <circle cx="420" cy="200" r="150"/><circle cx="420" cy="200" r="210"/>
        </svg>
        <div class="nll-wrap nll-cta-inner" data-reveal>
            <span class="nll-eyebrow nll-eyebrow-light"><span class="nll-dot" aria-hidden="true"></span>Ready to Expand Your Global Voice?</span>
            <h2 id="home-cta-title">Bring your message<br> <em>closer to home.</em></h2>
            <p>Whether you are localizing a digital platform, preparing subtitles, or building AI speech and language models in African languages, Nile Lingua Lab is your reliable technical and linguistic partner.</p>
            <div class="nll-actions">
                <a class="nll-button nll-button-gold" href="/contact">Request a Custom Quote <span class="nll-arrow" aria-hidden="true">→</span></a>
                <a class="nll-button nll-button-ghost" href="/contact">Contact Our Team</a>
            </div>
        </div>
    </section>
@endsection

