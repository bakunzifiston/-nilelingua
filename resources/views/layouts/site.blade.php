<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $page['meta'] ?? config('site.tagline') }}">
    @if (!empty($page['keyphrase']))
        <meta name="keywords" content="{{ $page['keyphrase'] }}">
    @endif
    <meta name="theme-color" content="#FAF7F2">
    @hasSection('seo-title')
        <title>@yield('seo-title')</title>
    @else
        <title>{{ $page['title'] ?? config('site.name') }} · {{ config('site.name') }}</title>
    @endif
    <link rel="stylesheet" href="{{ asset('css/nilelingua.css') }}">
    @yield('page-styles')
    <script src="{{ asset('js/nilelingua.js') }}" defer></script>
</head>
<body class="@yield('body-class')">
    <a class="skip-link" href="#main">Skip to content</a>
    @hasSection('home-header')
        @yield('home-header')
    @else
    <header class="site-header">
        <div class="header-inner">
            <a class="brand" href="/" aria-label="Nilelingua home">
                <span class="brand-mark" aria-hidden="true">n.</span>
                <span class="brand-name">nilelingua<span class="brand-period">.</span></span>
            </a>
            <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-navigation" data-menu-toggle>
                <span class="menu-toggle-label">Menu</span>
                <span class="menu-icon" aria-hidden="true"><i></i><i></i></span>
            </button>
            <nav class="primary-navigation" id="primary-navigation" aria-label="Main navigation" data-navigation>
                @foreach (config('site.navigation') as $item)
                    @if (!empty($item['children']))
                        <details class="nav-dropdown">
                            <summary class="{{ request()->is(trim($item['href'], '/').'*') ? 'is-current' : '' }}">{{ $item['label'] }} <span aria-hidden="true">⌄</span></summary>
                            <div class="dropdown-panel">
                                @foreach ($item['children'] as $child)
                                    <a href="{{ $child['href'] }}">{{ $child['label'] }}</a>
                                @endforeach
                                @if ($item['href'] === '/services')
                                    <a class="dropdown-all" href="/services">All services <span aria-hidden="true">↗</span></a>
                                @endif
                            </div>
                        </details>
                    @else
                        <a class="{{ request()->is(trim($item['href'], '/')) && $item['href'] !== '/' ? 'is-current' : ($item['href'] === '/' && request()->is('/') ? 'is-current' : '') }}"
                           href="{{ $item['href'] }}">{{ $item['label'] }}</a>
                    @endif
                @endforeach
            </nav>
            <button class="theme-toggle" type="button" aria-label="Switch to dark mode" aria-pressed="false" title="Switch to dark mode" data-theme-toggle>
                <span aria-hidden="true">◐</span>
            </button>
            <a class="button button-small header-cta" href="/contact">Let’s talk <span aria-hidden="true">↗</span></a>
        </div>
    </header>
    @endif

    <main id="main">
        @yield('content')
    </main>

    @hasSection('home-footer')
        @yield('home-footer')
    @else
    <footer class="site-footer">
        <div class="footer-top wrap">
            <div class="footer-brand-block">
                <a class="brand brand-footer" href="/" aria-label="Nilelingua home">
                    <span class="brand-mark" aria-hidden="true">n.</span>
                    <span class="brand-name">nilelingua<span class="brand-period">.</span></span>
                </a>
                <p>Language that moves you forward.</p>
            </div>
            <div class="footer-column">
                <span class="footer-heading">Explore</span>
                <a href="/services">Services</a>
                <a href="/industries">Industries</a>
                <a href="/languages">Languages</a>
                <a href="/technology">Technology</a>
            </div>
            <div class="footer-column">
                <span class="footer-heading">Discover</span>
                <a href="/about">About</a>
                <a href="/knowledge-center">Knowledge Center</a>
                <a href="/careers">Careers</a>
                <a href="/contact">Contact</a>
            </div>
            <div class="footer-note">
                <span class="footer-heading">Have a project in mind?</span>
                <p>Let’s find the right words for wherever you want to go.</p>
                <a class="footer-link" href="/contact">Start a conversation <span aria-hidden="true">↗</span></a>
            </div>
        </div>
        <div class="footer-bottom wrap">
            <span>© {{ date('Y') }} Nilelingua. All rights reserved.</span>
            <div><a href="/faq">FAQ</a><a href="/privacy-policy">Privacy Policy</a></div>
            <span class="footer-location">Words connect worlds.</span>
        </div>
    </footer>
    @endif
</body>
</html>
