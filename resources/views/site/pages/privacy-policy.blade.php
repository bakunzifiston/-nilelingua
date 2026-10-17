<x-page-hero compact :title="$page['headline']" :current="$page['title']" :eyebrow="$page['eyebrow']" :motif="false">
    <p class="nll-lead">{{ $page['intro'] }}</p>
</x-page-hero>

@if (!empty($page['notice']))
    <div class="nll-wrap nll-notice-wrap">
        <div class="nll-notice" role="note">
            <span class="nll-notice-icon" aria-hidden="true">!</span>
            <div><strong>Draft notice</strong><p>{{ $page['notice'] }}</p></div>
        </div>
    </div>
@endif

<div class="nll-wrap nll-reading nll-reading-legal">
    <aside class="nll-toc" aria-label="Contents">
        <span class="nll-toc-title">Contents</span>
        <ol>
            @foreach ($page['sections'] as $section)
                <li><a href="#policy-{{ $loop->iteration }}">{{ $section['heading'] }}</a></li>
            @endforeach
        </ol>
    </aside>

    <article class="nll-prose nll-prose-legal">
        @foreach ($page['sections'] as $section)
            <section class="nll-prose-section nll-anchor" id="policy-{{ $loop->iteration }}">
                <span class="nll-prose-index" aria-hidden="true">{{ $loop->iteration }}.</span>
                <h2>{{ $section['heading'] }}</h2>
                <p>{{ $section['body'] }}</p>
            </section>
        @endforeach
        <div class="nll-endnote">
            <span aria-hidden="true">✳</span>
            <p>Questions about how your information is handled? <a href="/contact">Contact us</a>.</p>
        </div>
    </article>
</div>
