@php($locale = \App\Support\Localization::current())
<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $page['meta'] ?? config('site.tagline') }}">
    @if (!empty($page['keyphrase']))
        <meta name="keywords" content="{{ $page['keyphrase'] }}">
    @endif
    <meta name="theme-color" content="#FFFFFF">
    @hasSection('seo-title')
        <title>@yield('seo-title')</title>
    @else
        <title>{{ isset($page['title']) ? $page['title'].' | '.config('site.name') : config('site.name') }}</title>
    @endif
    @foreach (\App\Support\Localization::LOCALES as $code => $language)
        <link rel="alternate" hreflang="{{ $code }}" href="{{ url(\App\Support\Localization::switchUrl($code)) }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ url(\App\Support\Localization::switchUrl(\App\Support\Localization::DEFAULT)) }}">
    <meta property="og:locale" content="{{ \App\Support\Localization::LOCALES[$locale]['og'] }}">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
    @yield('page-styles')
    <script src="{{ asset('js/nilelingua.js') }}" defer></script>
</head>
<body class="@yield('body-class')">
    <a class="nll-skip-link" href="#main">{{ __('Skip to content') }}</a>
    @include('partials.svg-defs')
    @include('partials.header')

    <main id="main">
        @yield('content')
    </main>

    @include('partials.footer')
</body>
</html>
