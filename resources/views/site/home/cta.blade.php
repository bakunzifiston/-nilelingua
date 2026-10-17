<section class="nll-cta" id="{{ $anchor }}" aria-labelledby="{{ $anchor }}-title">
    <svg class="nll-cta-art" viewBox="0 0 600 400" fill="none" aria-hidden="true">
        <path d="M0 220c40 0 40-60 80-60s40 120 80 120 40-180 80-180 40 240 80 240 40-200 80-200 40 120 80 120 40-40 60-40"/>
        <path d="M0 240c40 0 40-40 80-40s40 80 80 80 40-120 80-120 40 160 80 160 40-140 80-140 40 80 80 80 40-20 60-20"/>
        <circle cx="420" cy="200" r="150"/><circle cx="420" cy="200" r="210"/>
    </svg>
    <div class="nll-wrap nll-cta-inner" data-reveal>
        @if (!empty($section['eyebrow']))
            <span class="nll-eyebrow nll-eyebrow-light"><span class="nll-dot" aria-hidden="true"></span>{{ $section['eyebrow'] }}</span>
        @endif
        <h2 id="{{ $anchor }}-title">{!! \App\Support\Cms\Markup::inline($section['heading'] ?? '') !!}</h2>
        @if (!empty($section['text']))
            <p>{{ $section['text'] }}</p>
        @endif
        @include('site.home.buttons', ['primary' => $section['primary_button'] ?? null, 'secondary' => $section['secondary_button'] ?? null])
    </div>
</section>
