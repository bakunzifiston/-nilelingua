@extends('layouts.site')

@section('content')
    <section class="page-hero {{ $kind === 'article' ? 'article-hero' : '' }}">
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
            <p class="page-lead">{{ $page['intro'] }}</p>
            @if ($kind === 'article')
                <div class="article-meta"><span>{{ $page['category'] }}</span><span class="meta-separator">·</span><span>{{ $page['read_time'] }}</span></div>
            @elseif ($kind === 'service')
                <a class="button button-quote" href="/contact">Request a quote <span aria-hidden="true">↗</span></a>
            @endif
        </div>
        <div class="page-hero-shape" aria-hidden="true"><span>n.</span></div>
    </section>

    @if ($kind === 'contact')
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
