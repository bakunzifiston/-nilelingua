@php
    $isCurrent = fn (string $href) => $href === '/' ? request()->is('/') : request()->is(trim($href, '/'));
    $isSection = fn (string $href) => request()->is(trim($href, '/'), trim($href, '/').'/*');
@endphp

<header class="nll-header">
    <div class="nll-header-inner">
        <a class="nll-brand" href="/" aria-label="Nile Lingua Lab home">
            <span class="nll-brand-mark" aria-hidden="true">n.</span>
            <span>Nile Lingua Lab<span class="nll-brand-period">.</span></span>
        </a>

        <nav class="nll-nav" id="site-navigation" aria-label="Main navigation" data-navigation>
            @foreach (config('site.navigation') as $item)
                @if (!empty($item['children']))
                    <details class="nll-nav-dropdown">
                        <summary class="{{ $isSection($item['href']) ? 'is-current' : '' }}">{{ $item['label'] }} <svg viewBox="0 0 12 12" aria-hidden="true"><path d="m3 4.5 3 3 3-3"/></svg></summary>
                        <div class="nll-dropdown-panel">
                            @foreach ($item['children'] as $child)
                                <a href="{{ $child['href'] }}" @if ($isCurrent($child['href'])) aria-current="page" @endif>{{ $child['label'] }}</a>
                            @endforeach
                            @if ($item['href'] === '/services')
                                <a class="nll-dropdown-all" href="/services">All services <span aria-hidden="true">→</span></a>
                            @endif
                        </div>
                    </details>
                @else
                    <a class="{{ $isCurrent($item['href']) ? 'is-current' : '' }}" href="{{ $item['href'] }}"
                       @if ($isCurrent($item['href'])) aria-current="page" @endif>{{ $item['label'] }}</a>
                @endif
            @endforeach
        </nav>

        <div class="nll-header-actions">
            <button class="nll-theme-toggle" type="button" aria-label="Switch to dark mode" aria-pressed="false" title="Switch to dark mode" data-theme-toggle>
                <span aria-hidden="true">◐</span>
            </button>
            <a class="nll-button nll-button-gold nll-header-quote" href="/contact">Request a Quote</a>
            <button class="nll-menu-toggle" type="button" aria-expanded="false" aria-controls="site-navigation" aria-label="Open menu" data-menu-toggle>
                <span class="nll-menu-icon" aria-hidden="true"><i></i><i></i><i></i></span>
            </button>
        </div>
    </div>
</header>
