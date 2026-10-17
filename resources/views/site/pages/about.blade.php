@php
    $principleIcons = ['compass', 'cpu', 'message'];
@endphp

<x-page-hero :title="$page['headline']" :current="$page['title']" :eyebrow="$page['eyebrow']">
    <p class="nll-lead">{{ $page['intro'] }}</p>
    <div class="nll-hero-facts">
        <span><x-icon name="pin"/> Kigali, Rwanda</span>
        <span><x-icon name="people"/> Native-speaker review</span>
        <span><x-icon name="lock"/> Strict confidentiality</span>
    </div>
</x-page-hero>

@if (!empty($page['sections']))
    <section class="nll-section" aria-labelledby="story-title">
        <div class="nll-wrap nll-story">
            <div class="nll-story-intro" data-reveal>
                <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>How we work with you</span>
                <h2 class="nll-h2" id="story-title">Good work is in <em>the details.</em></h2>
            </div>
            <div class="nll-story-rows">
                @foreach ($page['sections'] as $section)
                    <article class="nll-story-row" data-reveal>
                        <span class="nll-story-index" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3>{{ $section['heading'] }}</h3>
                        <p>{{ $section['body'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif

@if (count($cards))
    <section class="nll-section nll-section-sand" aria-labelledby="principles-title">
        <div class="nll-wrap">
            <div class="nll-heading" data-reveal>
                <div>
                    <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>What guides us</span>
                    <h2 id="principles-title">Our working <em>principles</em></h2>
                </div>
            </div>
            <div class="nll-principles">
                @foreach ($cards as $card)
                    <article class="nll-principle" data-reveal>
                        <span class="nll-icon"><x-icon :name="$principleIcons[$loop->index] ?? 'sparkle'"/></span>
                        <h3>{{ $card['title'] }}</h3>
                        <p>{{ $card['body'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif

@include('site.partials.page-cta')
