@extends('layouts.site')

@if (!empty($page['seo_title']))
    @section('seo-title', $page['seo_title'])
@endif

@php
    // Each kind of page gets a layout built for its purpose (see resources/views/site/pages).
    $template = match (true) {
        $kind === 'service' && !empty($page['localization_layout']) => 'localization',
        in_array($kind, ['service', 'services', 'article', 'about', 'industries', 'languages', 'technology', 'contact', 'careers', 'knowledge-center', 'faq', 'privacy-policy'], true) => $kind,
        default => 'generic',
    };
@endphp

@section('body-class', 'nll-page nll-page-'.$template)

@section('page-styles')
    <link rel="stylesheet" href="{{ \App\Support\VersionedAsset::url('css/pages.css') }}">
@endsection

@section('content')
    @include('site.pages.'.$template)
@endsection
