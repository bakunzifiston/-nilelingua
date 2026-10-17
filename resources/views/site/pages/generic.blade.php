{{-- Fallback for any page added to config/site.php without a dedicated layout. --}}
<x-page-hero :title="$page['headline'] ?? $page['title']" :current="$page['title']" :eyebrow="$page['eyebrow'] ?? null">
    <p class="nll-lead">{{ $page['intro'] }}</p>
</x-page-hero>

@if (!empty($page['notice']))
    <div class="nll-wrap nll-notice-wrap">
        <div class="nll-notice" role="note">
            <span class="nll-notice-icon" aria-hidden="true">i</span>
            <p>{{ $page['notice'] }}</p>
        </div>
    </div>
@endif

@if (count($cards))
    <section class="nll-section nll-section-tight">
        <div class="nll-wrap nll-principles">
            @foreach ($cards as $card)
                <article class="nll-principle" data-reveal>
                    <h3>{{ $card['title'] }}</h3>
                    <p>{{ $card['body'] }}</p>
                    @if (!empty($card['href']))
                        <a class="nll-link" href="{{ $card['href'] }}">{{ $card['label'] ?? 'Learn more' }} <span class="nll-arrow" aria-hidden="true">→</span></a>
                    @endif
                </article>
            @endforeach
        </div>
    </section>
@endif

@if (!empty($page['sections']))
    <section class="nll-section nll-section-sand">
        <div class="nll-wrap nll-story-rows">
            @foreach ($page['sections'] as $section)
                <article class="nll-story-row" data-reveal>
                    <span class="nll-story-index" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <h3>{{ $section['heading'] }}</h3>
                    <p>{{ $section['body'] }}</p>
                </article>
            @endforeach
        </div>
    </section>
@endif

@include('site.partials.page-cta')
