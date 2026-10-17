<x-page-hero compact :title="$page['headline']" :current="$page['title']" :eyebrow="$page['eyebrow']" :motif="false">
    <dl class="nll-policy-meta">
        @if (!empty($page['effective_date']))
            <div><dt>Effective date</dt><dd>{{ $page['effective_date'] }}</dd></div>
        @endif
        @if (!empty($page['company']))
            <div><dt>Company</dt><dd>{{ $page['company'] }}</dd></div>
        @endif
    </dl>
</x-page-hero>

<div class="nll-wrap nll-reading nll-reading-legal">
    <aside class="nll-toc" aria-label="Contents">
        <span class="nll-toc-title">Contents</span>
        <ol>
            @foreach ($page['sections'] as $section)
                <li><a href="#policy-{{ $loop->iteration }}">{{ $loop->iteration }}. {{ $section['heading'] }}</a></li>
            @endforeach
            <li><a href="#policy-contact">Contact us</a></li>
        </ol>
    </aside>

    <article class="nll-prose nll-prose-legal">
        @foreach ($page['sections'] as $section)
            <section class="nll-prose-section nll-anchor" id="policy-{{ $loop->iteration }}">
                <span class="nll-prose-index" aria-hidden="true">{{ $loop->iteration }}.</span>
                <h2><span class="visually-hidden">{{ $loop->iteration }}. </span>{{ $section['heading'] }}</h2>

                @foreach ($section['paragraphs'] ?? [] as $paragraph)
                    <p>{{ $paragraph }}</p>
                @endforeach

                @if (!empty($section['body']))
                    <p>{{ $section['body'] }}</p>
                @endif

                @if (!empty($section['items']))
                    <ul class="nll-policy-list">
                        @foreach ($section['items'] as $item)
                            <li>
                                @if (!empty($item['label']))
                                    <strong>{{ $item['label'] }}:</strong>
                                @endif
                                {{ $item['text'] }}
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if (!empty($section['subsections']))
                    <div class="nll-policy-subsections">
                        @foreach ($section['subsections'] as $subsection)
                            <div class="nll-policy-subsection">
                                <h3>{{ $subsection['heading'] }}</h3>
                                <p>{{ $subsection['body'] }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if (!empty($section['note']))
                    <p class="nll-policy-note">{{ $section['note'] }}</p>
                @endif
            </section>
        @endforeach

        <section class="nll-policy-contact nll-anchor" id="policy-contact" aria-labelledby="policy-contact-title">
            <h2 id="policy-contact-title">Contact us</h2>
            <p>To ask about your data, request a copy, a correction or deletion, or to withdraw consent, contact us:</p>
            <ul class="nll-policy-channels">
                <li><x-icon name="mail"/><a href="mailto:contact@nilelingua.com">contact@nilelingua.com</a></li>
                <li><x-icon name="phone"/><a href="tel:+250788750971">+250 788 750 971</a></li>
                <li><x-icon name="whatsapp"/><a href="https://wa.me/250788750971" target="_blank" rel="noopener">WhatsApp +250 788 750 971</a></li>
                <li><x-icon name="pin"/><span>{{ $page['company'] ?? 'Gatenga, Kicukiro, Kigali, Rwanda' }}</span></li>
            </ul>
        </section>
    </article>
</div>
