@php
    // Swap the {link} placeholder in an answer for its anchor; everything else is escaped.
    $withLink = function (?string $text, ?array $link): string {
        $html = e($text ?? '');
        if ($link) {
            $html = str_replace('{link}', '<a href="'.e($link['href']).'">'.e($link['label']).'</a>', $html);
        }

        return $html;
    };
    $plain = fn (?string $text, ?array $link) => str_replace('{link}', $link['label'] ?? '', $text ?? '');

    // FAQPage structured data (schema.org), built from the same content.
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => collect($faqs)->flatMap(fn ($group) => $group['questions'])->map(function ($item) use ($plain) {
            $parts = array_filter([
                $plain($item['answer'] ?? null, $item['link'] ?? null),
                collect($item['items'] ?? [])->map(fn ($i) => $i['label'].': '.$i['text'])->implode(' '),
                $plain($item['outro'] ?? null, $item['link'] ?? null),
            ]);

            return ['@type' => 'Question', 'name' => $item['question'], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => implode(' ', $parts)]];
        })->values()->all(),
    ];
    $counter = 0;
@endphp

<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>

<x-page-hero compact :title="$page['headline']" :current="$page['title']" :eyebrow="$page['eyebrow']">
    <h2 class="nll-subtitle">{{ $page['subheading'] }}</h2>
    <p class="nll-lead nll-lead-links">{!! $withLink($page['intro'], $page['intro_link'] ?? null) !!}</p>
</x-page-hero>

<section class="nll-section nll-section-tight" aria-label="{{ __('Questions and answers') }}">
    <div class="nll-wrap nll-faq">
        <div class="nll-faq-main">
            <nav class="nll-faq-topics" aria-label="{{ __('FAQ topics') }}">
                @foreach ($faqs as $group)
                    <a href="#faq-{{ $loop->iteration }}">{{ $group['heading'] }}</a>
                @endforeach
            </nav>

            @foreach ($faqs as $group)
                <div class="nll-faq-group nll-anchor" id="faq-{{ $loop->iteration }}">
                    <h2>{{ $group['heading'] }}</h2>
                    <div class="nll-accordion">
                        @foreach ($group['questions'] as $item)
                            @php($counter++)
                            <details class="nll-accordion-item" @if ($counter === 1) open @endif>
                                <summary>
                                    <span class="nll-accordion-index" aria-hidden="true">{{ str_pad($counter, 2, '0', STR_PAD_LEFT) }}</span>
                                    <h3 class="nll-accordion-question">{{ $item['question'] }}</h3>
                                    <span class="nll-accordion-icon" aria-hidden="true"></span>
                                </summary>
                                <div class="nll-accordion-body">
                                    @if (!empty($item['answer']))
                                        <p>{!! $withLink($item['answer'], $item['link'] ?? null) !!}</p>
                                    @endif
                                    @if (!empty($item['items']))
                                        <ul class="nll-sector-points">
                                            @foreach ($item['items'] as $point)
                                                <li><strong>{{ $point['label'] }}:</strong> {{ $point['text'] }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    @if (!empty($item['outro']))
                                        <p>{!! $withLink($item['outro'], $item['link'] ?? null) !!}</p>
                                    @endif
                                </div>
                            </details>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <aside class="nll-faq-aside">
            <div class="nll-aside-card is-dark">
                <h2 class="nll-faq-aside-title">{{ __('Still Have Questions?') }}</h2>
                <a class="nll-button nll-button-gold" href="/contact">{{ __('Request a Quote') }} <span class="nll-arrow" aria-hidden="true">→</span></a>
                @if (config('site.contact_info.whatsapp_url'))
                    <a class="nll-button nll-button-ghost" href="{{ config('site.contact_info.whatsapp_url') }}" target="_blank" rel="noopener">WhatsApp {{ config('site.contact_info.phone') }} <span aria-hidden="true">↗</span></a>
                @endif
            </div>
        </aside>
    </div>
</section>
