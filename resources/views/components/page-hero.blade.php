@props([
    'title',
    'current',
    'eyebrow' => null,
    'crumbs' => [],
    'dark' => true,
    'compact' => false,
    'motif' => true,
])

<section {{ $attributes->class(['nll-page-hero', 'is-dark' => $dark, 'is-compact' => $compact]) }} aria-labelledby="page-title">
    @if ($motif && !isset($aside))
        <div class="nll-page-hero-motif" aria-hidden="true">
            <svg viewBox="-10 -10 410 430" fill="none">
                <use class="nll-motif-land" href="#nll-africa"/>
                <use class="nll-motif-outline" href="#nll-africa"/>
                <circle class="nll-map-pulse" cx="264" cy="224" r="14"/>
                <circle class="nll-map-hq" cx="264" cy="224" r="6"/>
            </svg>
        </div>
    @endif

    <div class="nll-wrap nll-page-hero-grid">
        <div class="nll-page-hero-copy">
            <nav class="nll-breadcrumbs" aria-label="{{ __('Breadcrumb') }}">
                <ol>
                    <li><a href="/">{{ __('Home') }}</a></li>
                    @foreach ($crumbs as $crumb)
                        <li><a href="{{ $crumb['href'] }}">{{ $crumb['label'] }}</a></li>
                    @endforeach
                    <li aria-current="page">{{ $current }}</li>
                </ol>
            </nav>

            @if ($eyebrow)
                <span class="nll-eyebrow nll-page-hero-eyebrow"><span class="nll-dot" aria-hidden="true"></span>{{ $eyebrow }}</span>
            @endif

            <h1 id="page-title">{{ $title }}</h1>

            {{ $slot }}
        </div>

        @isset($aside)
            <div class="nll-page-hero-aside">{{ $aside }}</div>
        @endisset
    </div>
</section>
