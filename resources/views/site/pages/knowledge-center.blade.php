@php
    $articles = collect(config('site.articles'));
    $counts = $articles->countBy('category');

    // CollectionPage structured data (schema.org).
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'CollectionPage',
        'name' => $page['headline'],
        'description' => $page['meta'],
        'url' => url('/knowledge-center'),
        'hasPart' => $articles->map(fn ($article, $key) => ['@type' => 'Article', 'headline' => $article['title'], 'url' => url('/knowledge-center/'.$key)])->values()->all(),
    ];
@endphp

<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>

<x-page-hero :title="$page['headline']" :current="$page['title']" :eyebrow="$page['eyebrow']">
    <h2 class="nll-subtitle">{{ $page['subheading'] }}</h2>
    <p class="nll-lead">{{ $page['intro'] }}</p>
    <div class="nll-actions">
        <a class="nll-button nll-button-gold" href="#newsletter">{{ __('Subscribe to Updates') }} <span class="nll-arrow" aria-hidden="true">↓</span></a>
        <a class="nll-button nll-button-ghost" href="/contact#quote-form">{{ __('Discuss a Project') }}</a>
    </div>
</x-page-hero>

{{-- Latest Articles, with search and topic filters --}}
<section class="nll-section nll-section-sand nll-anchor" id="latest-articles" aria-labelledby="latest-title" data-article-filter>
    <div class="nll-wrap">
        <div class="nll-heading" data-reveal>
            <div>
                <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>{{ trans_choice(':count article|:count articles', $articles->count(), ['count' => $articles->count()]) }}</span>
                <h2 id="latest-title">{!! __('Latest <em>Articles</em>') !!}</h2>
            </div>
        </div>

        <div class="nll-filter-bar">
            <label class="visually-hidden" for="article-search">{{ __('Search articles') }}</label>
            <input class="nll-search" id="article-search" type="search" placeholder="{{ __('Search articles…') }}" data-article-search>
            <div class="nll-filter-chips" role="group" aria-label="{{ __('Filter by topic') }}">
                <button type="button" class="nll-chip-filter is-active" data-topic-filter="" aria-pressed="true">{{ __('All') }} <span>{{ $articles->count() }}</span></button>
                @foreach ($page['topics'] as $topic)
                    <button type="button" class="nll-chip-filter" data-topic-filter="{{ $topic['topic'] }}" aria-pressed="false">{{ $topic['topic'] }} <span>{{ $counts[$topic['topic']] ?? 0 }}</span></button>
                @endforeach
            </div>
        </div>

        <div class="nll-article-grid nll-article-list">
            @foreach ($articles as $key => $article)
                @include('site.partials.article-card', ['key' => $key, 'article' => $article])
            @endforeach
        </div>
        <p class="nll-filter-empty" data-article-empty hidden>{{ __('No articles match your search.') }} <button type="button" class="nll-link" data-topic-filter="">{{ __('Show all articles') }}</button></p>
    </div>
</section>

<x-cta-band compact eyebrow="{{ __('Talk to our team') }}" :title="e($page['help_heading'])">
    <p>{{ $page['help'] }}</p>
    <div class="nll-actions">
        <a class="nll-button nll-button-gold" href="/contact#quote-form">{{ __('Request a Quote') }} <span class="nll-arrow" aria-hidden="true">→</span></a>
        <a class="nll-button nll-button-ghost" href="{{ config('site.contact_info.whatsapp_url') }}" target="_blank" rel="noopener">WhatsApp {{ config('site.contact_info.phone') }} <span aria-hidden="true">↗</span></a>
    </div>
</x-cta-band>
