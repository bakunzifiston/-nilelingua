@extends('layouts.site')

@section('seo-title', 'African Language Services & AI Data | Nile Lingua Lab')
@section('body-class', 'home-page')

@section('page-styles')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endsection

@section('home-header')
    <header class="home-header">
        <div class="home-header-inner">
            <a class="home-brand" href="/" aria-label="Nile Lingua Lab home">
                <span class="home-brand-mark" aria-hidden="true">n.</span>
                <span>Nile Lingua Lab<span class="home-brand-period">.</span></span>
            </a>

            <button class="menu-toggle home-menu-toggle" type="button" aria-expanded="false" aria-controls="home-navigation" data-menu-toggle>
                <span class="menu-toggle-label">Menu</span>
                <span class="menu-icon" aria-hidden="true"><i></i><i></i></span>
            </button>

            <nav class="home-navigation" id="home-navigation" aria-label="Main navigation" data-navigation>
                @foreach (config('site.navigation') as $item)
                    @if (!empty($item['children']))
                        <details class="home-nav-dropdown">
                            <summary>{{ $item['label'] }} <span aria-hidden="true">⌄</span></summary>
                            <div class="home-dropdown-panel">
                                @foreach ($item['children'] as $child)
                                    <a href="{{ $child['href'] }}">{{ $child['label'] }}</a>
                                @endforeach
                                @if ($item['href'] === '/services')
                                    <a class="home-dropdown-all" href="/services">All services <span aria-hidden="true">↗</span></a>
                                @endif
                            </div>
                        </details>
                    @else
                        <a class="{{ request()->is(trim($item['href'], '/')) && $item['href'] !== '/' ? 'is-current' : ($item['href'] === '/' && request()->is('/') ? 'is-current' : '') }}"
                           href="{{ $item['href'] }}">{{ $item['label'] }}</a>
                    @endif
                @endforeach
            </nav>

            <button class="theme-toggle home-theme-toggle" type="button" aria-label="Switch to dark mode" aria-pressed="false" title="Switch to dark mode" data-theme-toggle>
                <span aria-hidden="true">◐</span>
            </button>
            <a class="home-quote-button" href="/contact">Request a Quote <span aria-hidden="true">↗</span></a>
        </div>
    </header>
@endsection

@section('content')
    <section class="home-hero">
        <div class="home-hero-grid">
            <div class="home-hero-copy">
                <span class="home-eyebrow"><span></span>{{ $page['eyebrow'] }}</span>
                <h1>{{ $page['headline'] }}</h1>
                <p class="home-hero-subhead">{{ $page['intro'] }}</p>
                <p class="home-hero-intro">Based in Kigali, Rwanda, Nile Lingua Lab (NLL) provides African language services that bridge linguistic divides across East Africa and beyond. We combine regional expertise with modern language technology to deliver enterprise translation, software and multimedia localization, and specialized language datasets for AI models.</p>
                <div class="home-hero-actions">
                    <a class="home-button home-button-accent" href="/contact">Request a Quote <span aria-hidden="true">↗</span></a>
                    <a class="home-text-link home-text-link-light" href="/services">Explore Our Services <span aria-hidden="true">→</span></a>
                </div>
                <div class="home-hero-note"><span></span>Regional expertise. Human-in-the-loop quality.</div>
            </div>

        </div>
        <div class="home-hero-bottom"><span>Nile Lingua Lab · Kigali, Rwanda</span><a href="#home-services">Discover our services <span aria-hidden="true">↓</span></a></div>
    </section>

    <section class="home-section home-section-wrap" id="home-services">
        <div class="home-section-heading">
            <div><span class="home-eyebrow">African language expertise</span><h2>What We <em>Deliver</em></h2></div>
            <p class="home-section-aside">Language, localization and human expertise to help your content reach African audiences.</p>
        </div>
        <div class="home-service-grid">
            @foreach ($services as $service)
                <article class="home-service-card {{ !empty($service['deliverables']) ? 'home-service-card-detailed' : '' }}">
                    <span class="home-card-index">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }} <span>/</span> {{ str_pad(count($services), 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="home-service-icon" aria-hidden="true">{{ $service['icon'] }}</span>
                    <h3 class="home-service-title">{{ $service['title'] }}</h3>
                    <p class="home-service-copy">{{ $service['body'] }}</p>

                    @if (!empty($service['deliverables']))
                        <ul class="home-deliverables">
                            @foreach ($service['deliverables'] as $deliverable)
                                <li>
                                    @if (!empty($deliverable['href']))
                                        <a href="{{ $deliverable['href'] }}">
                                            <span class="home-deliverable-title">{{ $deliverable['title'] }}</span>
                                            <span>{{ $deliverable['body'] }}</span>
                                            <span class="home-deliverable-link">Learn more <span aria-hidden="true">↗</span></span>
                                        </a>
                                    @else
                                        <span class="home-deliverable-title">{{ $deliverable['title'] }}</span>
                                        <span>{{ $deliverable['body'] }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                        @if (!empty($service['label']))
                            <a class="home-card-link" href="{{ $service['href'] }}">{{ $service['label'] }} <span aria-hidden="true">↗</span></a>
                        @endif
                    @else
                        <a class="home-card-link" href="{{ $service['href'] }}">{{ $service['label'] }} <span aria-hidden="true">↗</span></a>
                    @endif
                </article>
            @endforeach
        </div>
    </section>

    <section class="home-languages">
        <div class="home-section-wrap home-languages-inner">
            <div class="home-languages-copy">
                <span class="home-eyebrow home-eyebrow-light">Regional Expertise, Continental Reach</span>
                <h2>Africa speaks in <em>many voices.</em></h2>
                <p>We are headquartered in Kigali, Rwanda, and support 15 languages across East, Central and Northeast Africa, from Kinyarwanda and Swahili to Sango and Dinka, each with native-speaker linguists.</p>
                <a class="home-button home-button-outline" href="/languages">See our languages <span aria-hidden="true">↗</span></a>
            </div>
            <div class="home-language-groups" aria-label="Examples of supported languages">
                <div class="home-language-heading"><span>LANGUAGE COVERAGE</span><span>15 LANGUAGES</span></div>
                <div class="home-language-list">
                    <span>Kinyarwanda</span>
                    <span>Swahili</span>
                    <span>Sango</span>
                    <span>Dinka</span>
                </div>
                <p>And more across East, Central and Northeast Africa.</p>
            </div>
        </div>
    </section>

    <section class="home-section home-section-wrap home-why">
        <div class="home-section-heading">
            <div><span class="home-eyebrow">Built around people, language and trust</span><h2>Why Organizations Choose <em>Nile Lingua Lab</em></h2></div>
        </div>
        <div class="home-strength-grid">
            <article class="home-strength-card">
                <span class="home-strength-index">01</span>
                <h3>Regional Expertise, Continental Reach</h3>
                <p>Headquartered in Kigali, Rwanda, we support 15 languages across East, Central and Northeast Africa, with native-speaker linguists.</p>
                <a class="home-card-link" href="/languages">See our languages <span aria-hidden="true">↗</span></a>
            </article>
            <article class="home-strength-card">
                <span class="home-strength-index">02</span>
                <h3>Human-in-the-Loop Quality Control</h3>
                <p>Every project follows a multi-step workflow of translation, independent editing, proofreading and automated QA checks.</p>
            </article>
            <article class="home-strength-card">
                <span class="home-strength-index">03</span>
                <h3>Technology-Driven Efficiency</h3>
                <p>We use Trados Studio, Wordfast Pro and MultiTerm for translation memory and terminology management, and Subtitle Edit and Audacity for audiovisual work. We can also adapt to your preferred tools and platforms.</p>
                <a class="home-card-link" href="/technology">Explore our technology <span aria-hidden="true">↗</span></a>
            </article>
            <article class="home-strength-card">
                <span class="home-strength-index">04</span>
                <h3>Data Security & Confidentiality</h3>
                <p>We use secure file handling, encrypted file transfers and signed non-disclosure agreements (NDAs) for sensitive legal and enterprise materials.</p>
            </article>
        </div>
    </section>

    <section class="home-tools">
        <div class="home-section-wrap">
            <div class="home-section-heading">
                <div><span class="home-eyebrow">Fits into the way you work</span><h2>Tools & File <em>Compatibility</em></h2></div>
                <p class="home-section-aside">We work with industry-standard tools and the file formats your teams already use.</p>
            </div>
            <div class="home-tools-table-wrap">
                <table class="home-tools-table">
                    <thead><tr><th scope="col">Category</th><th scope="col">Tools & Formats</th></tr></thead>
                    <tbody>
                        <tr><th scope="row">Translation Memory & Terminology</th><td>Trados Studio, Wordfast Pro, MultiTerm</td></tr>
                        <tr><th scope="row">Audiovisual & Subtitling</th><td>Subtitle Edit, Audacity; SRT, WebVTT, EBU-STL</td></tr>
                        <tr><th scope="row">Localization Files</th><td>JSON, XML, PO/POT, XLIFF, YAML, CSV, HTML5</td></tr>
                        <tr><th scope="row">AI Language Data</th><td>Audio segmentation, transcription, text annotation</td></tr>
                    </tbody>
                </table>
            </div>
            <p class="home-tools-note"><span aria-hidden="true">✳</span>Using a different platform or format? We can adapt to your workflow.</p>
        </div>
    </section>

    <section class="home-final-cta">
        <div class="home-section-wrap home-final-cta-inner">
            <span class="home-eyebrow home-eyebrow-light"><span></span>Ready to Expand Your Global Voice?</span>
            <h2>Bring your message<br>closer to home.</h2>
            <p>Whether you are localizing a digital platform, preparing subtitles, or building AI speech and language models in African languages, Nile Lingua Lab is your reliable technical and linguistic partner.</p>
            <div class="home-hero-actions">
                <a class="home-button home-button-accent" href="/contact">Request a Custom Quote <span aria-hidden="true">↗</span></a>
                <a class="home-text-link home-text-link-light" href="/contact">Contact Our Team <span aria-hidden="true">→</span></a>
            </div>
            <span class="home-final-mark" aria-hidden="true">NLL</span>
        </div>
    </section>
@endsection

@section('home-footer')
    <footer class="home-footer">
        <div class="home-section-wrap">
            <div class="home-footer-main">
                <div class="home-footer-brand">
                    <a class="home-brand home-brand-footer" href="/" aria-label="Nile Lingua Lab home">
                        <span class="home-brand-mark" aria-hidden="true">n.</span>
                        <span>Nile Lingua Lab<span class="home-brand-period">.</span></span>
                    </a>
                    <p>African language services from Kigali, Rwanda.</p>
                    <span class="home-footer-assurance">Native-speaker review · Strict confidentiality</span>
                </div>
                <div class="home-footer-column">
                    <span>Explore</span>
                    <a href="/services">Services</a><a href="/languages">Languages</a>
                    <a href="/technology">Technology</a><a href="/knowledge-center">Knowledge Center</a>
                </div>
                <div class="home-footer-column">
                    <span>Company</span>
                    <a href="/about">About</a><a href="/industries">Industries</a>
                    <a href="/careers">Careers</a><a href="/contact">Contact</a>
                </div>
                <div class="home-footer-contact">
                    <span>Contact</span>
                    <a href="https://maps.google.com/?q=Gatenga%2C+Kicukiro%2C+Kigali%2C+Rwanda">Gatenga, Kicukiro,<br>Kigali, Rwanda</a>
                    <a href="tel:+250788750971">+250 788 750 971</a>
                    <a href="https://wa.me/250788750971">WhatsApp <span aria-hidden="true">↗</span></a>
                    <a href="mailto:contact@nilelingua.com">contact@nilelingua.com</a>
                    <p>Monday to Friday<br>08:00 to 18:00 CAT</p>
                </div>
            </div>
            <div class="home-footer-bottom">
                <span>© {{ date('Y') }} Nile Lingua Lab Ltd · Kigali, Rwanda</span>
                <span>Native-speaker review · Strict confidentiality</span>
                <div><a href="/faq">FAQ</a><a href="/privacy-policy">Privacy Policy</a></div>
            </div>
        </div>
    </footer>
@endsection
