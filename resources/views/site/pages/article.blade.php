@php
    use Illuminate\Support\Str;

    $currentKey = $articleSlug ?? request()->segment(2);
    $related = collect(config('site.articles'))->except($currentKey)->take(3);
    $closing = $page['closing'] ?? null;
    $anchor = fn (string $heading) => Str::slug($heading);
    $plain = fn (string $text) => trim(preg_replace(['/\*\*|`/', '/(?<![\w*])\*(?!\s)(.+?)(?<!\s)\*(?![\w*])/', '/\[([^\]]+)\]\([^)]+\)/'], ['', '$1', '$1'], $text));

    // Article + breadcrumb structured data (schema.org).
    $schema = [
        [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $page['headline'],
            'description' => $page['meta'],
            'author' => ['@type' => 'Organization', 'name' => 'Nile Lingua Lab', 'url' => url('/')],
            'publisher' => ['@type' => 'Organization', 'name' => 'Nile Lingua Lab Ltd'],
            'articleSection' => $page['category'],
            'mainEntityOfPage' => url()->current(),
            'inLanguage' => app()->getLocale(),
        ],
        [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => __('Home'), 'item' => url(\App\Support\Localization::path('/'))],
                ['@type' => 'ListItem', 'position' => 2, 'name' => __('Knowledge Center'), 'item' => url(\App\Support\Localization::path('/knowledge-center'))],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $page['title']],
            ],
        ],
    ];
@endphp

<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>

<x-page-hero class="nll-article-hero" :title="$page['headline']" :current="$page['title']" :eyebrow="$page['category']" :motif="false"
             :crumbs="[['label' => __('Knowledge Center'), 'href' => '/knowledge-center']]">
    <div class="nll-article-byline">
        <span class="nll-brand-mark" aria-hidden="true">n.</span>
        <span>
            <strong>{{ __('By Nile Lingua Lab') }} • {{ $page['category'] }} • {{ $page['read_time'] }}</strong>
            <span>{{ __('Published :date', ['date' => $page['published']]) }} · {{ __('Last updated :date', ['date' => $page['updated']]) }}</span>
        </span>
    </div>
</x-page-hero>

<div class="nll-wrap nll-reading">
    <aside class="nll-toc" aria-label="{{ __('On this page') }}">
        <span class="nll-toc-title">{{ __('On this page') }}</span>
        <ol>
            @foreach ($page['sections'] as $section)
                <li><a href="#{{ $anchor($section['heading']) }}">{{ $section['heading'] }}</a></li>
            @endforeach
            @if ($closing)
                <li><a href="#{{ $anchor($closing['heading']) }}">{{ $closing['heading'] }}</a></li>
            @endif
        </ol>
    </aside>

    <article class="nll-prose nll-prose-article">
        @foreach ($page['sections'] as $section)
            <section class="nll-prose-section nll-anchor" id="{{ $anchor($section['heading']) }}">
                <h2>{{ $section['heading'] }}</h2>
                @foreach ($section['blocks'] as $block)
                    @switch($block['type'])
                        @case('h3')
                            <h3>{{ $block['text'] }}</h3>
                            @break
                        @case('ul')
                            <ul class="nll-policy-list">
                                @foreach ($block['items'] as $item)
                                    <li><x-rich :text="$item"/></li>
                                @endforeach
                            </ul>
                            @break
                        @case('ol')
                            <ol class="nll-article-steps">
                                @foreach ($block['items'] as $item)
                                    <li><x-rich :text="$item"/></li>
                                @endforeach
                            </ol>
                            @break
                        @case('table')
                            <div class="nll-table-wrap nll-article-table">
                                <table class="nll-table">
                                    <thead>
                                        <tr>
                                            @foreach ($block['head'] as $head)
                                                <th scope="col">@if ($head !== ''){{ $head }}@else<span class="visually-hidden">{{ __('Aspect') }}</span>@endif</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($block['rows'] as $row)
                                            <tr>
                                                @foreach ($row as $cell)
                                                    @if ($loop->first)
                                                        <th scope="row"><x-rich :text="$cell"/></th>
                                                    @else
                                                        <td data-label="{{ $block['head'][$loop->index] ?? '' }}"><x-rich :text="$cell"/></td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @break
                        @case('note')
                            <p class="nll-article-note"><em>{{ $block['text'] }}</em></p>
                            @break
                        @default
                            <p><x-rich :text="$block['text']"/></p>
                    @endswitch
                @endforeach
            </section>
        @endforeach

        @if ($closing)
            <section class="nll-article-closing nll-anchor" id="{{ $anchor($closing['heading']) }}" aria-labelledby="{{ $anchor($closing['heading']) }}-title">
                <h2 id="{{ $anchor($closing['heading']) }}-title">{{ $closing['heading'] }}</h2>
                <p><x-rich :text="$closing['body']"/></p>
                <ul class="nll-article-contacts">
                    @php($contact = config('site.contact_info', []))
                    <li><strong>{{ __('Address:') }}</strong> {{ collect([$contact['address_line_1'] ?? '', $contact['address_line_2'] ?? ''])->filter()->implode(', ') }}</li>
                    <li><strong>{{ __('Phone / WhatsApp:') }}</strong> <a href="{{ \App\Support\Cms\Markup::tel($contact['phone'] ?? '') }}">{{ $contact['phone'] ?? '' }}</a></li>
                    <li><strong>{{ __('Email:') }}</strong> <a href="mailto:{{ $contact['email'] ?? '' }}">{{ $contact['email'] ?? '' }}</a></li>
                </ul>
                <div class="nll-actions">
                    @foreach ($closing['buttons'] as $button)
                        <a class="nll-button {{ $loop->first ? 'nll-button-gold' : 'nll-button-ghost' }}" href="{{ $button['href'] }}">{{ $button['label'] }}@if ($loop->first) <span class="nll-arrow" aria-hidden="true">→</span>@endif</a>
                    @endforeach
                </div>
                <nav class="nll-cta-related" aria-label="{{ $closing['related_label'] }}">
                    <span>{{ $closing['related_label'] }}</span>
                    @foreach ($closing['related'] as $link)
                        <a href="{{ $link['href'] }}">{{ $link['label'] }}</a>
                    @endforeach
                </nav>
            </section>
        @endif
    </article>

    <aside class="nll-reading-aside" aria-label="{{ __('Keep exploring') }}">
        <div class="nll-aside-card">
            <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>{{ __('Knowledge Center') }}</span>
            <p>{{ __('Guides and analysis on African language localization, translation and AI language data.') }}</p>
            <a class="nll-link" href="/knowledge-center">{{ __('All articles') }} <span class="nll-arrow" aria-hidden="true">→</span></a>
        </div>
        <div class="nll-aside-card is-dark">
            <span class="nll-eyebrow nll-eyebrow-light"><span class="nll-dot" aria-hidden="true"></span>{{ __('Have a project in mind?') }}</span>
            <a class="nll-button nll-button-gold" href="/contact#quote-form">{{ __('Request a Quote') }} <span class="nll-arrow" aria-hidden="true">→</span></a>
        </div>
    </aside>
</div>

@if ($related->isNotEmpty())
    <section class="nll-section nll-section-tight nll-section-sand" aria-labelledby="related-title">
        <div class="nll-wrap">
            <div class="nll-heading" data-reveal>
                <div>
                    <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>{{ __('Keep reading') }}</span>
                    <h2 id="related-title">{!! __('More from the <em>Knowledge Center</em>') !!}</h2>
                </div>
                <a class="nll-link" href="/knowledge-center">{{ __('All articles') }} <span class="nll-arrow" aria-hidden="true">→</span></a>
            </div>
            <div class="nll-article-grid">
                @foreach ($related as $key => $article)
                    @include('site.partials.article-card', ['key' => $key, 'article' => $article])
                @endforeach
            </div>
        </div>
    </section>
@endif
