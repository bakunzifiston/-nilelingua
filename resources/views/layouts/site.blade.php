<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $page['meta'] ?? config('site.tagline') }}">
    @if (!empty($page['keyphrase']))
        <meta name="keywords" content="{{ $page['keyphrase'] }}">
    @endif
    <meta name="theme-color" content="#FBF8F3">
    @hasSection('seo-title')
        <title>@yield('seo-title')</title>
    @else
        <title>{{ $page['title'] ?? config('site.name') }} · {{ config('site.name') }}</title>
    @endif
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
    @yield('page-styles')
    <script src="{{ asset('js/nilelingua.js') }}" defer></script>
</head>
<body class="@yield('body-class')">
    <a class="nll-skip-link" href="#main">Skip to content</a>
    @include('partials.svg-defs')
    @include('partials.header')

    <main id="main">
        @yield('content')
    </main>

    @include('partials.footer')
</body>
</html>
