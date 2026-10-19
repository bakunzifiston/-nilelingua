@php
    // Compare against the path without its language prefix, so highlighting works in every language.
    $currentPath = trim(\App\Support\Localization::stripPrefix('/'.ltrim(request()->path(), '/')), '/');
    $isCurrent = fn (string $href) => trim($href, '/') === $currentPath;
    $isSection = fn (string $href) => $currentPath === trim($href, '/') || str_starts_with($currentPath, trim($href, '/').'/');
@endphp

<header class="nll-header">
    <div class="nll-header-inner">
        <x-logo class="nll-brand"/>

        <nav class="nll-nav" id="site-navigation" aria-label="{{ __('Main navigation') }}" data-navigation>
            @foreach (config('site.navigation') as $item)
                @if (!empty($item['children']))
                    <details class="nll-nav-dropdown">
                        <summary class="{{ $isSection($item['href']) ? 'is-current' : '' }}">{{ $item['label'] }} <svg viewBox="0 0 12 12" aria-hidden="true"><path d="m3 4.5 3 3 3-3"/></svg></summary>
                        <div class="nll-dropdown-panel">
                            @foreach ($item['children'] as $child)
                                <a href="{{ $child['href'] }}" @if ($isCurrent($child['href'])) aria-current="page" @endif>{{ $child['label'] }}</a>
                            @endforeach
                            @if ($item['href'] === '/services')
                                <a class="nll-dropdown-all" href="/services">{{ __('All services') }} <span aria-hidden="true">→</span></a>
                            @endif
                        </div>
                    </details>
                @else
                    <a class="{{ $isCurrent($item['href']) ? 'is-current' : '' }}" href="{{ $item['href'] }}"
                       @if ($isCurrent($item['href'])) aria-current="page" @endif>{{ $item['label'] }}</a>
                @endif
            @endforeach
            @include('partials.language-switcher', ['variant' => 'menu'])
        </nav>

        <div class="nll-header-actions">
            @include('partials.language-switcher', ['variant' => 'compact'])
            <button class="nll-theme-toggle" type="button" aria-label="{{ __('Switch to dark mode') }}" aria-pressed="false" title="{{ __('Switch to dark mode') }}" data-theme-toggle data-label-dark="{{ __('Switch to dark mode') }}" data-label-light="{{ __('Switch to light mode') }}">
                <span aria-hidden="true">◐</span>
            </button>
            <a class="nll-button nll-button-gold nll-header-quote" href="/contact">{{ __('Request a Quote') }}</a>
            <button class="nll-menu-toggle" type="button" aria-expanded="false" aria-controls="site-navigation" aria-label="{{ __('Open menu') }}" data-menu-toggle>
                <span class="nll-menu-icon" aria-hidden="true"><i></i><i></i><i></i></span>
            </button>
        </div>
    </div>
</header>
