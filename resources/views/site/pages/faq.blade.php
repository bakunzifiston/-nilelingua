<x-page-hero compact :title="$page['headline']" :current="$page['title']" :eyebrow="$page['eyebrow']">
    <p class="nll-lead">{{ $page['intro'] }}</p>
</x-page-hero>

<section class="nll-section nll-section-tight" aria-label="Questions and answers">
    <div class="nll-wrap nll-faq">
        <aside class="nll-faq-aside">
            <div class="nll-aside-card is-dark">
                <span class="nll-eyebrow nll-eyebrow-light"><span class="nll-dot" aria-hidden="true"></span>Can’t find your answer?</span>
                <p>Still have a question? We’d be glad to help.</p>
                <a class="nll-button nll-button-gold" href="/contact">Get in touch <span class="nll-arrow" aria-hidden="true">→</span></a>
                <a class="nll-quote-alt" href="https://wa.me/250788750971" target="_blank" rel="noopener">WhatsApp +250 788 750 971 <span aria-hidden="true">↗</span></a>
            </div>
        </aside>

        <div class="nll-accordion">
            @foreach ($faqs as $faq)
                <details class="nll-accordion-item" @if ($loop->first) open @endif>
                    <summary>
                        <span class="nll-accordion-index" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="nll-accordion-question">{{ $faq['question'] }}</span>
                        <span class="nll-accordion-icon" aria-hidden="true"></span>
                    </summary>
                    <p>{{ $faq['answer'] }}</p>
                </details>
            @endforeach
        </div>
    </div>
</section>
