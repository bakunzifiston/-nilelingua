@extends('admin.layout')

@php use App\Support\Localization; @endphp

@section('title', 'History · '.$label)

@section('crumbs')
    <span aria-hidden="true">/</span>
    <a href="{{ route('admin.entries.edit', [$entry, 'lang' => $locale]) }}">{{ $label }}</a>
    <span aria-hidden="true">/</span>
    <span aria-current="page">Version history</span>
@endsection

@section('content')
    <div class="a-page-head">
        <div>
            <span class="a-eyebrow">Version history</span>
            <h1>{{ $label }}</h1>
            <p>Every time this page is published, the version is kept here. Restoring a version puts it back as a draft so you can review it before publishing.</p>
        </div>
        <a class="a-btn" href="{{ route('admin.entries.edit', [$entry, 'lang' => $locale]) }}">Back to the editor</a>
    </div>

    @if (count($locales) > 1)
        <nav class="a-tabs" aria-label="Content language">
            @foreach ($locales as $code)
                <a href="{{ route('admin.entries.history', [$entry, 'lang' => $code]) }}" @if ($code === $locale) aria-current="page" @endif><span class="a-lang">{{ strtoupper($code) }}</span> {{ Localization::LOCALES[$code]['name'] }}</a>
            @endforeach
        </nav>
    @endif

    <section class="a-card">
        @if ($revisions->isEmpty())
            <div class="a-empty"><strong>No earlier versions yet</strong>Versions appear here after the first publish from the dashboard.</div>
        @else
            <ul class="a-list">
                @foreach ($revisions as $revision)
                    <li>
                        <div class="a-list-main">
                            <strong>{{ $revision->created_at->format('j M Y, H:i') }}</strong>
                            <span class="a-list-meta">Published {{ $revision->created_at->diffForHumans() }}{{ $revision->user ? ' by '.$revision->user->name : '' }}@if ($loop->first && $revisions->onFirstPage()) · current live version @endif</span>
                        </div>
                        <form method="POST" action="{{ route('admin.entries.restore-revision', [$entry, $revision]) }}" data-confirm="Replace the current draft with this version? Nothing changes on the website until you publish." data-confirm-title="Restore this version?" data-confirm-button="Restore as draft">
                            @csrf
                            <button class="a-btn a-btn-sm" type="submit">@include('admin.partials.icon', ['name' => 'restore']) Restore as draft</button>
                        </form>
                    </li>
                @endforeach
            </ul>
            @include('admin.partials.pagination', ['paginator' => $revisions])
        @endif
    </section>
@endsection
