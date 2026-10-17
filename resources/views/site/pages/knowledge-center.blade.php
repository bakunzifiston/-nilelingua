@php
    $articles = collect(config('site.articles'));
    $featuredKey = $articles->keys()->first();
    $featured = $articles->first();
    $rest = $articles->except($featuredKey);
@endphp

<x-page-hero :title="$page['headline']" :current="$page['title']" :eyebrow="$page['eyebrow']">
    <p class="nll-lead">{{ $page['intro'] }}</p>
</x-page-hero>

@if ($featured)
    <section class="nll-section nll-section-tight" aria-labelledby="library-title">
        <div class="nll-wrap">
            <div class="nll-heading" data-reveal>
                <div>
                    <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>From the Knowledge Center</span>
                    <h2 id="library-title">Notes for <em>the curious.</em></h2>
                </div>
                <p class="nll-heading-aside">{{ $articles->count() }} practical guides on localization, language technology and multilingual communication.</p>
            </div>

            <a class="nll-featured-article" href="/knowledge-center/{{ $featuredKey }}" data-reveal>
                <div class="nll-featured-art" aria-hidden="true">
                    <svg viewBox="-10 -10 410 430" fill="none">
                        <use class="nll-map-land" href="#nll-africa"/>
                        <use class="nll-map-outline" href="#nll-africa"/>
                        <circle class="nll-map-hq" cx="264" cy="224" r="6.5"/>
                    </svg>
                </div>
                <div class="nll-featured-copy">
                    <span class="nll-article-meta"><span>Featured</span>{{ $featured['category'] }} · {{ $featured['read_time'] }}</span>
                    <h3>{{ $featured['title'] }}</h3>
                    <p>{{ $featured['intro'] }}</p>
                    <span class="nll-link">Read the guide <span class="nll-arrow" aria-hidden="true">→</span></span>
                </div>
            </a>

            <div class="nll-article-grid">
                @foreach ($rest as $key => $article)
                    @include('site.partials.article-card', ['key' => $key, 'article' => $article])
                @endforeach
            </div>
        </div>
    </section>
@endif

@include('site.partials.page-cta')
