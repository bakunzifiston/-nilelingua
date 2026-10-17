@php
    $toolIcons = ['layers', 'workflow', 'cpu', 'shield'];
    $localization = config('site.services.localization', []);
    $pipeline = [
        ['label' => 'Source content', 'icon' => 'document', 'human' => false],
        ['label' => 'Translation memory & terminology', 'icon' => 'layers', 'human' => false],
        ['label' => 'Native-speaker linguist', 'icon' => 'people', 'human' => true],
        ['label' => 'Independent editing', 'icon' => 'check', 'human' => true],
        ['label' => 'Automated QA checks', 'icon' => 'shield', 'human' => false],
    ];
@endphp

<x-page-hero :title="$page['headline']" :current="$page['title']" :eyebrow="$page['eyebrow']">
    <p class="nll-lead">{{ $page['intro'] }}</p>

    <x-slot:aside>
        <ol class="nll-pipeline" aria-label="An example human-in-the-loop workflow">
            @foreach ($pipeline as $stage)
                <li class="{{ $stage['human'] ? 'is-human' : '' }}">
                    <span class="nll-pipeline-icon"><x-icon :name="$stage['icon']"/></span>
                    <span>{{ $stage['label'] }}</span>
                    @if ($stage['human'])
                        <em>Human</em>
                    @endif
                </li>
            @endforeach
        </ol>
    </x-slot:aside>
</x-page-hero>

@if (count($cards))
    <section class="nll-section" aria-labelledby="toolkit-title">
        <div class="nll-wrap">
            <div class="nll-heading" data-reveal>
                <div>
                    <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>Our toolkit</span>
                    <h2 id="toolkit-title">Technology that <em>supports people</em></h2>
                </div>
            </div>
            <div class="nll-feature-grid">
                @foreach ($cards as $card)
                    <article class="nll-feature" data-reveal>
                        <div class="nll-feature-top">
                            <span class="nll-icon"><x-icon :name="$toolIcons[$loop->index] ?? 'cpu'"/></span>
                            <span class="nll-feature-index" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <h3>{{ $card['title'] }}</h3>
                        <p>{{ $card['body'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif

@if (!empty($localization['tools']))
    <section class="nll-section nll-section-tight nll-section-sand" aria-labelledby="formats-title">
        <div class="nll-wrap nll-formats" data-reveal>
            <div>
                <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>Tools &amp; file formats</span>
                <h2 class="nll-h2" id="formats-title">Fits into the way <em>you work</em></h2>
            </div>
            <div class="nll-formats-lists">
                <div>
                    <h3>Translation memory &amp; terminology</h3>
                    <ul class="nll-tags nll-tags-lg">
                        @foreach ($localization['tools'] as $tool)
                            <li>{{ $tool }}</li>
                        @endforeach
                    </ul>
                </div>
                <div>
                    <h3>File formats</h3>
                    <ul class="nll-tags nll-tags-format nll-tags-lg">
                        @foreach ($localization['formats'] as $format)
                            <li>{{ $format }}</li>
                        @endforeach
                    </ul>
                </div>
                <p class="nll-panel-note">Using a different tool or platform? We can adapt to your workflow.</p>
            </div>
        </div>
    </section>
@endif

@if (!empty($page['sections']))
    <section class="nll-section" aria-labelledby="principles-title">
        <div class="nll-wrap nll-story">
            <div class="nll-story-intro" data-reveal>
                <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>Our point of view</span>
                <h2 class="nll-h2" id="principles-title">Tools chosen <em>for the job</em></h2>
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

@include('site.partials.page-cta')
