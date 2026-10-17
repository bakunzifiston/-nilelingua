@php
    $pipeline = [
        ['label' => __('Source content'), 'icon' => 'document', 'human' => false],
        ['label' => __('Translation memory & terminology'), 'icon' => 'layers', 'human' => false],
        ['label' => __('Native-speaker linguist'), 'icon' => 'people', 'human' => true],
        ['label' => __('Independent editing'), 'icon' => 'check', 'human' => true],
        ['label' => __('Automated QA checks'), 'icon' => 'shield', 'human' => false],
    ];
@endphp

<x-page-hero :title="$page['headline']" :current="$page['title']" :eyebrow="$page['eyebrow']">
    <h2 class="nll-subtitle">{{ $page['subheading'] }}</h2>
    @foreach ($page['paragraphs'] as $paragraph)
        <p class="nll-lead {{ $loop->first ? '' : 'nll-lead-secondary' }}">{{ $paragraph }}</p>
    @endforeach
    <div class="nll-actions">
        <a class="nll-button nll-button-gold" href="/contact">{{ __('Book a Consultation') }} <span class="nll-arrow" aria-hidden="true">→</span></a>
        <a class="nll-button nll-button-ghost" href="/contact">{{ __('Request a Quote') }}</a>
    </div>

    <x-slot:aside>
        <ol class="nll-pipeline" aria-label="{{ __('An example human-in-the-loop workflow') }}">
            @foreach ($pipeline as $stage)
                <li class="{{ $stage['human'] ? 'is-human' : '' }}">
                    <span class="nll-pipeline-icon"><x-icon :name="$stage['icon']"/></span>
                    <span>{{ $stage['label'] }}</span>
                    @if ($stage['human'])
                        <em>{{ __('Human') }}</em>
                    @endif
                </li>
            @endforeach
        </ol>
    </x-slot:aside>
</x-page-hero>

{{-- The Tools We Use --}}
<section class="nll-section nll-section-tight" aria-labelledby="tools-title">
    <div class="nll-wrap">
        <div class="nll-heading" data-reveal>
            <div>
                <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>{{ __('Our toolkit') }}</span>
                <h2 id="tools-title">{!! __('The Tools We <em>Use</em>') !!}</h2>
            </div>
        </div>
        <div class="nll-tools-grid">
            @foreach ($page['tool_groups'] as $group)
                <section class="nll-tool-group" aria-labelledby="tool-group-{{ $loop->iteration }}" data-reveal>
                    <span class="nll-icon nll-tool-icon" aria-hidden="true"><x-icon :name="$group['icon']"/></span>
                    <h3 id="tool-group-{{ $loop->iteration }}">{{ $group['area'] }}</h3>
                    @if (!empty($group['tools']))
                        <ul class="nll-tags">
                            @foreach ($group['tools'] as $tool)
                                <li>{{ $tool }}</li>
                            @endforeach
                        </ul>
                    @endif
                    @if (!empty($group['formats']))
                        <ul class="nll-tags nll-tags-format" aria-label="{{ __('File formats') }}">
                            @foreach ($group['formats'] as $format)
                                <li>{{ $format }}</li>
                            @endforeach
                        </ul>
                    @endif
                </section>
            @endforeach
        </div>
        <p class="nll-steps-note"><span aria-hidden="true">✳</span><span class="nll-note-text">{{ $page['tools_note'] }} {{ __('Not sure your files are supported?') }} <a href="{{ config('site.contact_info.whatsapp_url') }}" target="_blank" rel="noopener">{{ __('Message us on WhatsApp') }}</a>.</span></p>
    </div>
</section>

{{-- Technical Capabilities --}}
<section class="nll-section nll-section-sand" aria-labelledby="capabilities-title">
    <div class="nll-wrap">
        <div class="nll-heading" data-reveal>
            <div>
                <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>{{ __('What our tools make possible') }}</span>
                <h2 id="capabilities-title">{!! __('Technical <em>Capabilities</em>') !!}</h2>
            </div>
        </div>
        <div class="nll-capability-grid nll-variant-teal">
            @foreach ($page['capabilities'] as $capability)
                <article class="nll-capability" data-reveal>
                    <span class="nll-icon"><x-icon :name="$capability['icon']"/></span>
                    <h3>{{ $loop->iteration }}. {{ $capability['heading'] }}</h3>
                    <p>{{ $capability['intro'] }}</p>
                    <ul class="nll-sector-points">
                        @foreach ($capability['items'] as $item)
                            <li><strong>{{ $item['label'] }}:</strong> {{ $item['text'] }}</li>
                        @endforeach
                    </ul>
                    @if (!empty($capability['link']))
                        <p class="nll-sector-links"><span>{{ __('Learn more:') }}</span> <a class="nll-link" href="{{ $capability['link']['href'] }}">{{ $capability['link']['label'] }} <span class="nll-arrow" aria-hidden="true">→</span></a></p>
                    @endif
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- Data Protection & Confidentiality --}}
<section class="nll-section" aria-labelledby="protection-title">
    <div class="nll-wrap">
        <div class="nll-protection" data-reveal>
            <div class="nll-protection-intro">
                <span class="nll-eyebrow nll-eyebrow-light"><span class="nll-dot" aria-hidden="true"></span>{{ __('Security by default') }}</span>
                <h2 id="protection-title">{{ __('Data Protection & Confidentiality') }}</h2>
                <p>{{ $page['protection_intro'] }}</p>
            </div>
            <ul class="nll-protection-list">
                @foreach ($page['protection'] as $item)
                    <li>
                        <span class="nll-icon"><x-icon :name="$item['icon']"/></span>
                        <span><strong>{{ $item['label'] }}:</strong> {{ $item['text'] }}@if (!empty($item['link'])) <a href="{{ $item['link']['href'] }}">{{ $item['link']['label'] }}</a>.@endif</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>

{{-- How Our Workflow Compares --}}
<section class="nll-section nll-section-tight nll-section-sand" aria-labelledby="compare-title">
    <div class="nll-wrap">
        <div class="nll-heading" data-reveal>
            <div>
                <span class="nll-eyebrow"><span class="nll-dot" aria-hidden="true"></span>{{ __('The difference it makes') }}</span>
                <h2 id="compare-title">{!! __('How Our Workflow <em>Compares</em>') !!}</h2>
            </div>
        </div>
        <div class="nll-table-wrap" data-reveal>
            <table class="nll-table nll-compare-table">
                <thead>
                    <tr><th scope="col">{{ __('Without dedicated tools') }}</th><th scope="col">{{ __('Nile Lingua Lab workflow') }}</th></tr>
                </thead>
                <tbody>
                    @foreach ($page['comparison'] as $row)
                        <tr>
                            <td data-label="{{ __('Without dedicated tools') }}"><span class="nll-compare-x" aria-hidden="true">✕</span>{{ $row['without'] }}</td>
                            <td data-label="{{ __('Nile Lingua Lab workflow') }}"><span class="nll-compare-check" aria-hidden="true">✓</span>{{ $row['with'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

<x-cta-band compact eyebrow="{{ __('Talk to our team') }}" :title="e($page['cta_heading'])">
    <p>{{ $page['cta'] }}</p>
    <div class="nll-actions">
        <a class="nll-button nll-button-gold" href="/contact">{{ __('Book a Consultation') }} <span class="nll-arrow" aria-hidden="true">→</span></a>
        <a class="nll-button nll-button-ghost" href="/contact">{{ __('Request a Quote') }}</a>
    </div>
    <p class="nll-cta-note">{{ $page['cta_note'] }}</p>
    <nav class="nll-cta-related" aria-label="{{ __('Related pages') }}">
        <span>{{ __('Related') }}</span>
        @foreach ($page['related_links'] as $link)
            <a href="{{ $link['href'] }}">{{ $link['label'] }}</a>
        @endforeach
    </nav>
</x-cta-band>
