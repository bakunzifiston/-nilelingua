@props([
    'title',
    'eyebrow' => __('Start a conversation'),
    'compact' => false,
])

<section {{ $attributes->class(['nll-cta', 'is-compact' => $compact]) }} aria-labelledby="cta-title">
    <svg class="nll-cta-art" viewBox="0 0 600 400" fill="none" aria-hidden="true">
        <path d="M0 220c40 0 40-60 80-60s40 120 80 120 40-180 80-180 40 240 80 240 40-200 80-200 40 120 80 120 40-40 60-40"/>
        <path d="M0 240c40 0 40-40 80-40s40 80 80 80 40-120 80-120 40 160 80 160 40-140 80-140 40 80 80 80 40-20 60-20"/>
        <circle cx="420" cy="200" r="150"/><circle cx="420" cy="200" r="210"/>
    </svg>
    <div class="nll-wrap nll-cta-inner" data-reveal>
        <span class="nll-eyebrow nll-eyebrow-light"><span class="nll-dot" aria-hidden="true"></span>{{ $eyebrow }}</span>
        <h2 id="cta-title">{!! $title !!}</h2>
        {{ $slot }}
    </div>
</section>
