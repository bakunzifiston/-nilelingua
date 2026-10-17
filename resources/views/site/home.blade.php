@extends('layouts.site')

@section('seo-title', $page['title'])
@section('body-class', 'home-page')

@section('page-styles')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endsection

@php
    // Homepage sections are managed (and ordered) in the admin dashboard; hidden ones are skipped.
    $known = ['hero', 'services', 'languages', 'why', 'tools', 'cta', 'text'];
    $visible = collect($sections ?? [])
        ->filter(fn ($section) => in_array($section['type'] ?? null, $known, true) && ($section['visible'] ?? true))
        ->values();

    $seen = [];
    $anchors = $visible->map(function ($section) use (&$seen) {
        $type = $section['type'];
        $seen[$type] = ($seen[$type] ?? 0) + 1;

        return 'home-'.$type.($seen[$type] > 1 ? '-'.$seen[$type] : '');
    })->all();
@endphp

@section('content')
    @foreach ($visible as $index => $section)
        @include('site.home.'.$section['type'], [
            'section' => $section,
            'anchor' => $anchors[$index],
            'nextAnchor' => $anchors[$index + 1] ?? null,
        ])
    @endforeach
@endsection
