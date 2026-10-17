@php
    $industryIcons = ['cpu', 'scale', 'heart', 'chart', 'film', 'book'];
    $industryVariants = ['teal', 'terracotta', 'teal', 'gold', 'terracotta', 'gold'];
@endphp

<x-page-hero :title="$page['headline']" :current="$page['title']" :eyebrow="$page['eyebrow']">
    <p class="nll-lead">{{ $page['intro'] }}</p>
    <div class="nll-actions">
        <a class="nll-button nll-button-gold" href="#sectors">Browse sectors <span class="nll-arrow" aria-hidden="true">↓</span></a>
        <a class="nll-button nll-button-ghost" href="/contact">Discuss your project</a>
    </div>

    <x-slot:aside>
        {{-- The same words in three contexts: a user interface, a contract and a clinical document. --}}
        <div class="nll-contexts" aria-hidden="true">
            <div class="nll-context-card nll-context-ui">
                <span class="nll-context-head"><span class="nll-context-icon"><x-icon name="layers"/></span>User interface</span>
                <div class="nll-context-screen">
                    <span class="nll-bar" style="--w: 60%"></span>
                    <span class="nll-bar" style="--w: 85%"></span>
                    <span class="nll-context-button">Continue</span>
                </div>
            </div>

            <div class="nll-context-card nll-context-legal">
                <span class="nll-context-head"><span class="nll-context-icon"><x-icon name="scale"/></span>Contract</span>
                <span class="nll-bar" style="--w: 90%"></span>
                <span class="nll-bar nll-bar-highlight" style="--w: 55%"></span>
                <span class="nll-bar" style="--w: 80%"></span>
                <span class="nll-bar" style="--w: 68%"></span>
            </div>

            <div class="nll-context-card nll-context-clinical">
                <span class="nll-context-head"><span class="nll-context-icon"><x-icon name="heart"/></span>Clinical document</span>
                <span class="nll-bar" style="--w: 84%"></span>
                <span class="nll-bar" style="--w: 62%"></span>
                <span class="nll-bar nll-bar-highlight" style="--w: 46%"></span>
            </div>

            <span class="nll-context-tag">Same words · different weight</span>
        </div>
    </x-slot:aside>
</x-page-hero>

@if (count($cards))
    <section class="nll-section" id="sectors" aria-labelledby="sectors-title">
        <div class="nll-wrap">
            <div class="nll-heading" data-reveal>
                <div>
                    <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>Sectors we support</span>
                    <h2 id="sectors-title">Language work shaped by <em>its subject</em></h2>
                </div>
            </div>
            <div class="nll-industry-grid">
                @foreach ($cards as $card)
                    <a class="nll-industry nll-variant-{{ $industryVariants[$loop->index] ?? 'teal' }}" href="{{ $card['href'] ?? '/contact' }}" data-reveal>
                        <span class="nll-icon"><x-icon :name="$industryIcons[$loop->index] ?? 'compass'"/></span>
                        <h3>{{ $card['title'] }}</h3>
                        <p>{{ $card['body'] }}</p>
                        <span class="nll-industry-link">Discuss your project <span class="nll-arrow" aria-hidden="true">→</span></span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif

@if (!empty($page['sections']))
    <section class="nll-section nll-section-sand" aria-label="Preparing your brief">
        <div class="nll-wrap">
            @foreach ($page['sections'] as $section)
                <article class="nll-brief" data-reveal>
                    <div class="nll-brief-copy">
                        <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>Before you start</span>
                        <h2 class="nll-h2">{{ $section['heading'] }}</h2>
                        <p>{{ $section['body'] }}</p>
                        <a class="nll-button nll-button-primary" href="/contact">Share your brief <span class="nll-arrow" aria-hidden="true">→</span></a>
                    </div>
                    <ul class="nll-checklist" aria-label="Helpful details to include">
                        <li><x-icon name="check"/>What the content is for</li>
                        <li><x-icon name="check"/>Who will use it</li>
                        <li><x-icon name="check"/>Subject-matter reviewers</li>
                        <li><x-icon name="check"/>Existing terminology</li>
                    </ul>
                </article>
            @endforeach
        </div>
    </section>
@endif

@include('site.partials.page-cta')
