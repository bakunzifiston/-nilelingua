@php
    $currentKey = request()->segment(2);
    $related = collect(config('site.articles'))->except($currentKey)->take(3);
@endphp

<x-page-hero class="nll-article-hero" :title="$page['headline']" :current="$page['title']" :eyebrow="$page['category']" :motif="false"
             :crumbs="[['label' => 'Knowledge Center', 'href' => '/knowledge-center']]">
    <p class="nll-lead nll-article-lead">{{ $page['intro'] }}</p>
    <div class="nll-article-byline">
        <span class="nll-brand-mark" aria-hidden="true">n.</span>
        <span><strong>Nile Lingua Lab</strong><span>{{ $page['category'] }} · {{ $page['read_time'] }}</span></span>
    </div>
</x-page-hero>

<div class="nll-wrap nll-reading">
    <aside class="nll-toc" aria-label="On this page">
        <span class="nll-toc-title">On this page</span>
        <ol>
            @foreach ($page['sections'] as $section)
                <li><a href="#section-{{ $loop->iteration }}">{{ $section['heading'] }}</a></li>
            @endforeach
        </ol>
    </aside>

    <article class="nll-prose">
        @foreach ($page['sections'] as $section)
            <section class="nll-prose-section nll-anchor" id="section-{{ $loop->iteration }}">
                <span class="nll-prose-index" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                <h2>{{ $section['heading'] }}</h2>
                <p>{{ $section['body'] }}</p>
            </section>
        @endforeach

        <div class="nll-endnote">
            <span aria-hidden="true">✳</span>
            <p>Good language work starts with the right context. <a href="/contact">Let’s talk about yours.</a></p>
        </div>
    </article>

    <aside class="nll-reading-aside" aria-label="Keep exploring">
        <div class="nll-aside-card">
            <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>Keep exploring</span>
            <p>Ideas and practical guidance for multilingual work.</p>
            <a class="nll-link" href="/knowledge-center">Visit the Knowledge Center <span class="nll-arrow" aria-hidden="true">→</span></a>
        </div>
        <div class="nll-aside-card is-dark">
            <span class="nll-eyebrow nll-eyebrow-light"><span class="nll-dot" aria-hidden="true"></span>Have a project in mind?</span>
            <a class="nll-button nll-button-gold" href="/contact">Start a conversation <span class="nll-arrow" aria-hidden="true">→</span></a>
        </div>
    </aside>
</div>

@if ($related->isNotEmpty())
    <section class="nll-section nll-section-tight nll-section-sand" aria-labelledby="related-title">
        <div class="nll-wrap">
            <div class="nll-heading" data-reveal>
                <div>
                    <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>Keep reading</span>
                    <h2 id="related-title">More from the <em>Knowledge Center</em></h2>
                </div>
                <a class="nll-link" href="/knowledge-center">All articles <span class="nll-arrow" aria-hidden="true">→</span></a>
            </div>
            <div class="nll-article-grid">
                @foreach ($related as $key => $article)
                    @include('site.partials.article-card', ['key' => $key, 'article' => $article])
                @endforeach
            </div>
        </div>
    </section>
@endif
