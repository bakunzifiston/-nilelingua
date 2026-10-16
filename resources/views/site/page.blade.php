@extends('layouts.site')

@if ($kind === 'services')
    @section('seo-title', $page['seo_title'])
@elseif ($kind === 'service' && !empty($page['seo_title']))
    @section('seo-title', $page['seo_title'])
@endif

@if ($kind === 'service' && !empty($page['localization_layout']))
    @section('body-class', 'localization-page')
    @section('page-styles')
        <link rel="stylesheet" href="{{ asset('css/localization.css') }}">
    @endsection
@endif

@section('content')
    <section class="page-hero {{ $kind === 'article' ? 'article-hero' : '' }} {{ $kind === 'services' ? 'services-overview-hero' : '' }} {{ $kind === 'service' && !empty($page['localization_layout']) ? 'localization-hero' : '' }}">
        <div class="wrap page-hero-inner">
            <div class="breadcrumbs">
                <a href="/">Home</a><span aria-hidden="true">/</span>
                @if ($kind === 'service')
                    <a href="/services">Services</a><span aria-hidden="true">/</span>
                @elseif ($kind === 'article')
                    <a href="/knowledge-center">Knowledge Center</a><span aria-hidden="true">/</span>
                @endif
                <span aria-current="page">{{ $page['title'] }}</span>
            </div>
            <span class="eyebrow"><span class="eyebrow-dot"></span>{{ $page['eyebrow'] ?? ($page['category'] ?? 'Nilelingua') }}</span>
            <h1>{{ $page['headline'] ?? $page['title'] }}</h1>
            @if ($kind === 'service' && !empty($page['localization_layout']))
                <h2 class="localization-hero-subtitle">{{ $page['intro'] }}</h2>
                <p class="page-lead localization-hero-copy">{{ $page['hero_copy'] }}</p>
            @else
                <p class="page-lead">{{ $page['intro'] }}</p>
            @endif
            @if ($kind === 'article')
                <div class="article-meta"><span>{{ $page['category'] }}</span><span class="meta-separator">·</span><span>{{ $page['read_time'] }}</span></div>
            @elseif ($kind === 'services')
                <p class="services-overview-supporting">{{ $page['supporting_copy'] }}</p>
                <div class="services-overview-actions">
                    <a class="button button-quote" href="/contact">Request a Quote <span aria-hidden="true">↗</span></a>
                    <a class="text-link" href="/contact">Book a Consultation <span aria-hidden="true">→</span></a>
                </div>
            @elseif ($kind === 'service')
                @if (!empty($page['localization_layout']))
                    <div class="localization-actions">
                        <a class="button button-quote" href="/contact">Request a Quote <span aria-hidden="true">↗</span></a>
                        <a class="text-link" href="/contact">Book a Consultation <span aria-hidden="true">→</span></a>
                    </div>
                @else
                    <a class="button button-quote" href="/contact">Request a quote <span aria-hidden="true">↗</span></a>
                @endif
            @endif
        </div>
        <div class="page-hero-shape" aria-hidden="true"><span>n.</span></div>
    </section>

    @if ($kind === 'service' && !empty($page['localization_layout']))
        <section class="localization-section wrap">
            <div class="localization-section-heading">
                <span class="eyebrow">Localization expertise</span>
                <h2>Core Localization Capabilities</h2>
            </div>
            <div class="localization-capability-grid">
                @foreach ($page['capabilities'] as $capability)
                    <article class="localization-capability-card">
                        <span class="localization-capability-index">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3>{{ $capability['title'] }}</h3>
                        <p>{{ $capability['body'] }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="localization-work">
            <div class="wrap">
                <div class="localization-section-heading">
                    <span class="eyebrow">A clear, careful process</span>
                    <h2>How We Localize</h2>
                </div>
                <ol class="localization-workflow">
                    @foreach ($page['workflow'] as $step)
                        <li>
                            <span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <h3>{{ $step['title'] }}</h3>
                            <p>{{ $step['body'] }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>

        <section class="localization-section wrap">
            <div class="localization-section-heading">
                <span class="eyebrow">Built around your markets and tools</span>
                <h2>Languages We Localize Into</h2>
            </div>
            <div class="localization-language-panel">
                <div>
                    <h3>Supported languages</h3>
                    <ul class="localization-language-list">
                        @foreach ($page['languages'] as $language)
                            <li>{{ $language }}</li>
                        @endforeach
                    </ul>
                    <p>Need another language? <a href="/contact">Ask us</a>.</p>
                    <p><strong>Source languages:</strong> {{ implode(' and ', $page['source_languages']) }}.</p>
                    <a class="text-link" href="/languages">See all supported languages <span aria-hidden="true">↗</span></a>
                </div>
                <div class="localization-tools">
                    <h3>Tools & File Formats</h3>
                    <p>We work in {{ implode(', ', $page['tools']) }} for translation memory and terminology management, and handle the formats your developers already use:</p>
                    <ul class="localization-format-list">
                        @foreach ($page['formats'] as $format)
                            <li>{{ $format }}</li>
                        @endforeach
                    </ul>
                    <p>Using a different tool or platform? We can adapt to your workflow.</p>
                </div>
            </div>
        </section>

        <section class="localization-partner">
            <div class="wrap localization-partner-inner">
                <span class="eyebrow eyebrow-light">Start Your Localization Project</span>
                <h2>Bring your product closer to its users.</h2>
                <p>{{ $page['cta'] }}</p>
                <div class="localization-actions">
                    <a class="button button-light" href="/contact">Request a Quote <span aria-hidden="true">↗</span></a>
                    <a class="text-link localization-whatsapp" href="https://wa.me/250788750971">WhatsApp +250 788 750 971 <span aria-hidden="true">↗</span></a>
                </div>
                <nav class="localization-related" aria-label="Related pages">
                    <span>Related:</span>
                    @foreach ($page['related_links'] as $link)
                        <a href="{{ $link['href'] }}">{{ $link['label'] }}</a>
                    @endforeach
                </nav>
            </div>
        </section>
    @elseif ($kind === 'services')
        <section class="services-overview-section wrap">
            <div class="services-overview-heading">
                <span class="eyebrow">Our Four Core Service Pillars</span>
                <h2>Language solutions, built around your work.</h2>
            </div>
            <div class="services-overview-table-wrap">
                <table class="services-overview-table">
                    <thead>
                        <tr>
                            <th scope="col">Service pillar</th>
                            <th scope="col">Key capabilities</th>
                            <th scope="col">Core deliverables</th>
                            <th scope="col">Typical clients</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($page['pillars'] as $pillar)
                            <tr>
                                <th scope="row"><a href="{{ $pillar['href'] }}">{{ $pillar['title'] }} <span aria-hidden="true">↗</span></a></th>
                                <td data-label="Key capabilities">{{ $pillar['capabilities'] }}</td>
                                <td data-label="Core deliverables">{{ $pillar['deliverables'] }}</td>
                                <td data-label="Typical clients">{{ $pillar['clients'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="services-overview-work">
            <div class="wrap">
                <div class="services-overview-heading">
                    <span class="eyebrow">A clear, careful process</span>
                    <h2>How We Work</h2>
                    <p>Every project follows the same four steps:</p>
                </div>
                <ol class="services-workflow">
                    @foreach ($page['workflow'] as $step)
                        <li>
                            <span class="services-workflow-number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <h3>{{ $step['title'] }}</h3>
                            <p>{{ $step['body'] }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>

        <section class="services-overview-section wrap">
            <div class="services-overview-heading">
                <span class="eyebrow">Across East Africa and beyond</span>
                <h2>Languages and Industries</h2>
            </div>
            <div class="services-context-grid">
                <article class="services-context-card">
                    <h3>Languages</h3>
                    <p>{{ implode(', ', $page['languages']) }}.</p>
                    <a class="text-link" href="/languages">See all languages <span aria-hidden="true">↗</span></a>
                </article>
                <article class="services-context-card">
                    <h3>Sectors</h3>
                    <p>{{ implode(', ', $page['industries']) }}.</p>
                    <a class="text-link" href="/industries">Explore industries <span aria-hidden="true">↗</span></a>
                </article>
            </div>
        </section>

        <section class="services-overview-partner">
            <div class="wrap services-overview-partner-inner">
                <span class="eyebrow eyebrow-light">Partner With Nile Lingua Lab</span>
                <h2>Let’s build the right language workflow.</h2>
                <p>{{ $page['partner_copy'] }}</p>
                <div class="services-overview-actions">
                    <a class="button button-light" href="/contact">Request a Quote <span aria-hidden="true">↗</span></a>
                    <a class="text-link text-link-light" href="/contact">Contact Our Team <span aria-hidden="true">→</span></a>
                </div>
                <p class="services-overview-contact">{{ $page['contact_note'] }}</p>
            </div>
        </section>
    @elseif ($kind === 'contact')
        <section class="contact-section wrap">
            <div class="contact-copy">
                <span class="eyebrow">A few details to get us started</span>
                <h2>Tell us a little<br><em>about your project.</em></h2>
                <p>We’ll use your details to understand what you need and consider the next step. Please don’t include confidential documents or sensitive personal information.</p>
                <div class="contact-aside"><span class="aside-icon">✳</span><span>Not sure what to ask for yet?<br>That’s a perfectly good place to start.</span></div>
            </div>
            <form class="contact-form" method="POST" action="{{ route('contact.submit') }}">
                @csrf
                <div class="form-row">
                    <label for="name">Your name <span>*</span>
                        <input id="name" name="name" type="text" autocomplete="name" value="{{ old('name') }}" required maxlength="120" placeholder="Jane Smith">
                        @error('name')<span class="field-error">{{ $message }}</span>@enderror
                    </label>
                    <label for="email">Work email <span>*</span>
                        <input id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" required maxlength="255" placeholder="jane@company.com">
                        @error('email')<span class="field-error">{{ $message }}</span>@enderror
                    </label>
                </div>
                <div class="form-row">
                    <label for="organization">Organization
                        <input id="organization" name="organization" type="text" autocomplete="organization" value="{{ old('organization') }}" maxlength="160" placeholder="Your company">
                        @error('organization')<span class="field-error">{{ $message }}</span>@enderror
                    </label>
                    <label for="service">What can we help with?
                        <select id="service" name="service">
                            <option value="">Choose a service</option>
                            @foreach (config('site.services') as $service)
                                <option value="{{ $service['title'] }}" @selected(old('service') === $service['title'])>{{ $service['title'] }}</option>
                            @endforeach
                            <option value="Something else" @selected(old('service') === 'Something else')>Something else</option>
                        </select>
                        @error('service')<span class="field-error">{{ $message }}</span>@enderror
                    </label>
                </div>
                <label for="message">A little about your project <span>*</span>
                    <textarea id="message" name="message" rows="5" required maxlength="5000" placeholder="Content, languages, timing—anything that will help us understand what you need.">{{ old('message') }}</textarea>
                    @error('message')<span class="field-error">{{ $message }}</span>@enderror
                </label>
                <button class="button form-submit" type="submit">Send your enquiry <span aria-hidden="true">↗</span></button>
                <p class="form-note">Fields marked <span>*</span> are required. By sending this form, you agree that we may use these details to respond to your enquiry.</p>
            </form>
        </section>
    @elseif ($kind === 'faq')
        <section class="section wrap faq-section">
            @foreach ($faqs as $faq)
                <details class="faq-item">
                    <summary>{{ $faq['question'] }}<span class="faq-plus" aria-hidden="true">+</span></summary>
                    <p>{{ $faq['answer'] }}</p>
                </details>
            @endforeach
            <div class="faq-outro"><p>Still have a question? We’d be glad to help.</p><a class="text-link" href="/contact">Get in touch <span aria-hidden="true">↗</span></a></div>
        </section>
    @elseif ($kind === 'article')
        <article class="article-body wrap">
            <div class="article-main">
                @foreach ($page['sections'] as $section)
                    <section class="article-section">
                        <h2>{{ $section['heading'] }}</h2>
                        <p>{{ $section['body'] }}</p>
                    </section>
                @endforeach
                <div class="article-endnote"><span>✳</span><p>Good language work starts with the right context. <a href="/contact">Let’s talk about yours.</a></p></div>
            </div>
            <aside class="article-aside">
                <span class="eyebrow">Keep exploring</span>
                <p>Ideas and practical guidance for multilingual work.</p>
                <a class="text-link" href="/knowledge-center">Visit the Knowledge Center <span aria-hidden="true">↗</span></a>
                <div class="aside-rule"></div>
                <span class="eyebrow">Have a project in mind?</span>
                <a class="text-link" href="/contact">Start a conversation <span aria-hidden="true">↗</span></a>
            </aside>
        </article>
    @else
        @if (!empty($page['notice']))
            <div class="notice wrap"><span class="notice-icon" aria-hidden="true">i</span><p>{{ $page['notice'] }}</p></div>
        @endif

        @if (count($cards))
            <section class="section wrap content-cards-section">
                @if ($kind === 'services' || $kind === 'knowledge-center')
                    <div class="section-heading compact-heading">
                        <div><span class="eyebrow">{{ $kind === 'services' ? 'Find your fit' : 'From the Knowledge Center' }}</span><h2>{{ $kind === 'services' ? 'Explore what we do.' : 'Notes for the curious.' }}</h2></div>
                    </div>
                @endif
                <div class="content-card-grid {{ $kind === 'knowledge-center' ? 'article-card-grid' : '' }}">
                    @foreach ($cards as $card)
                        <a class="content-card" href="{{ $card['href'] ?? ($kind === 'industries' ? '/contact' : '/contact') }}">
                            <span class="content-card-top">{{ $card['label'] ?? ($kind === 'industries' ? 'Industry' : 'Learn more') }} <span aria-hidden="true">↗</span></span>
                            <span class="content-card-title">{{ $card['title'] }}</span>
                            <span class="content-card-copy">{{ $card['body'] }}</span>
                            @if (empty($card['href']))
                                <span class="card-arrow" aria-hidden="true">↗</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        @if (!empty($page['sections']))
            <section class="section wrap text-sections {{ $kind === 'privacy-policy' ? 'legal-sections' : '' }}">
                @if (count($page['sections']) > 1 && $kind !== 'privacy-policy')
                    <div class="section-heading compact-heading">
                        <div><span class="eyebrow">A thoughtful approach</span><h2>Good work is in<br><em>the details.</em></h2></div>
                    </div>
                @endif
                @foreach ($page['sections'] as $section)
                    <div class="text-section">
                        <h2>{{ $section['heading'] }}</h2>
                        <p>{{ $section['body'] }}</p>
                    </div>
                @endforeach
            </section>
        @endif

        @if (!empty($page['cta']))
            <section class="inline-cta">
                <div class="wrap inline-cta-inner">
                    <p>{{ $page['cta'] }}</p>
                    <a class="button button-light" href="/contact">Let’s talk <span aria-hidden="true">↗</span></a>
                </div>
            </section>
        @endif
    @endif
@endsection
