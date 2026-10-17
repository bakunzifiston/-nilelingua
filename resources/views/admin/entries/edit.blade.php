@extends('admin.layout')

@php
    use App\Support\Cms\Registry;
    use App\Support\Localization;

    $language = Localization::LOCALES[$locale]['name'];
    $isCollection = Registry::isCollection($entry);
    $panels = $schema->panels($content);
    $updateUrl = route('admin.entries.update', [$entry, 'lang' => $locale]);
@endphp

@section('title', $label)

@section('crumbs')
    <span aria-hidden="true">/</span>
    @if ($isCollection)
        <a href="{{ route('admin.collections.index', $entry->type) }}">{{ Registry::COLLECTIONS[$entry->type]['label'] }}</a>
        <span aria-hidden="true">/</span>
    @elseif ($entry->type === 'settings')
        <span>Site settings</span>
        <span aria-hidden="true">/</span>
    @else
        <span>Website pages</span>
        <span aria-hidden="true">/</span>
    @endif
    <span aria-current="page">{{ $label }}</span>
@endsection

@section('content')
    <div class="a-page-head">
        <div>
            <span class="a-eyebrow">{{ Registry::typeLabel($entry->type) }}@if ($publicUrl) · <span style="text-transform: none; letter-spacing: 0">{{ Registry::publicPath($entry) }}</span>@endif</span>
            <h1>{{ $label }}</h1>
            @if ($entry->type === 'settings')
                <p>{{ match ($entry->slug) {
                    'general' => 'The site name, contact details shown in the footer and on several pages, and social media links.',
                    'navigation' => 'The links in the main menu. Each item can have a dropdown of child links.',
                    'forms' => 'The options visitors choose from in the quote and application forms. These values are stored with each submission, so they are kept in English; their French labels are managed under Interface text.',
                    default => '',
                } }}</p>
            @endif
        </div>
        <div class="a-head-actions">
            @if ($publicUrl)
                <a class="a-btn" href="{{ route('admin.entries.preview', [$entry, 'lang' => $locale]) }}" target="_blank" rel="noopener">@include('admin.partials.icon', ['name' => 'eye']) Preview</a>
                @if ($row->published !== null)
                    <a class="a-btn a-btn-quiet" href="{{ $publicUrl }}" target="_blank" rel="noopener">@include('admin.partials.icon', ['name' => 'external']) View live</a>
                @endif
            @endif
        </div>
    </div>

    @if (count($locales) > 1)
        <nav class="a-tabs" aria-label="Content language">
            @foreach ($locales as $code)
                @php $tabRow = $entry->contentFor($code); @endphp
                <a href="{{ route('admin.entries.edit', [$entry, 'lang' => $code]) }}" @if ($code === $locale) aria-current="page" @endif>
                    <span class="a-lang">{{ strtoupper($code) }}</span> {{ Localization::LOCALES[$code]['name'] }}
                    @if ($tabRow->draft !== null)<span class="a-tab-flag" title="Unpublished changes"></span><span class="visually-hidden">(unpublished changes)</span>@endif
                </a>
            @endforeach
        </nav>
    @endif

    @if ($reference !== null)
        <div class="a-flash a-flash-info">@include('admin.partials.icon', ['name' => 'globe'])
            <span>You are editing the <strong>French</strong> version. The English text is shown under each field for reference; English content is not changed here.
                @if (!$row->exists) This page has no French version yet: fill in the fields, then publish. @endif</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="a-flash a-flash-error" role="alert">@include('admin.partials.icon', ['name' => 'alert'])<span>{{ $errors->count() === 1 ? 'One field needs' : $errors->count().' fields need' }} attention. Look for the messages in red below.</span></div>
    @endif

    <div class="a-editor">
        <form class="a-editor-main" method="POST" action="{{ $updateUrl }}" data-content-form novalidate>
            @csrf
            @method('PUT')
            <input type="hidden" name="c_json" value="" data-json-field>

            @foreach ($panels as $panel)
                <details class="a-panel" id="panel-{{ $panel['id'] }}" open>
                    <summary>
                        <div style="flex: 1">
                            <h2>{{ $panel['title'] }}</h2>
                            @if ($panel['description'])<p>{{ $panel['description'] }}</p>@endif
                        </div>
                        <svg class="a-chevron" viewBox="0 0 24 24" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                    </summary>
                    <div class="a-panel-body">
                        @foreach ($panel['keys'] as $key)
                            @include('admin.entries.node', [
                                'value' => $content[$key],
                                'key' => $key,
                                'name' => 'c['.$key.']',
                                'path' => (string) $key,
                                'pattern' => (string) $key,
                                'depth' => 0,
                                'asItem' => count($panel['keys']) === 1 && is_array($content[$key]) && !array_is_list($content[$key]),
                            ])
                        @endforeach
                    </div>
                </details>
            @endforeach

            <div class="a-actionbar">
                <span class="a-actionbar-status" data-dirty-status>
                    @if ($row->draft !== null)
                        Draft saved {{ $row->draft_updated_at?->diffForHumans() }} · not live yet
                    @elseif ($row->published !== null)
                        Up to date with the live website
                    @else
                        Not published yet
                    @endif
                </span>
                <div class="a-actionbar-buttons">
                    <button class="a-btn" type="submit" name="action" value="save">Save draft</button>
                    @if ($publicUrl)
                        <button class="a-btn" type="submit" name="action" value="preview" formtarget="_blank">@include('admin.partials.icon', ['name' => 'eye']) Save &amp; preview</button>
                    @endif
                    <button class="a-btn a-btn-primary" type="submit" name="action" value="publish">@include('admin.partials.icon', ['name' => 'check']) {{ $row->published === null ? 'Publish' : 'Save & publish' }} {{ count($locales) > 1 ? strtoupper($locale) : '' }}</button>
                </div>
            </div>
        </form>

        <aside class="a-editor-side" aria-label="Status and actions">
            <section class="a-side-card">
                <h2>Status</h2>
                <dl>
                    @foreach ($locales as $code)
                        <div><dt>{{ Localization::LOCALES[$code]['name'] }}</dt><dd>@include('admin.partials.state', ['row' => $entry->contentFor($code), 'short' => true])</dd></div>
                    @endforeach
                    <div><dt>Last saved</dt><dd>{{ ($row->draft_updated_at ?? $row->updated_at)?->format('j M Y, H:i') ?? '—' }}</dd></div>
                    <div><dt>Published</dt><dd>{{ $row->published_at?->format('j M Y, H:i') ?? '—' }}</dd></div>
                    @if ($row->editor)
                        <div><dt>Edited by</dt><dd>{{ $row->editor->name }}</dd></div>
                    @endif
                </dl>
            </section>

            <section class="a-side-card">
                <h2>{{ $language }} version</h2>
                @if ($row->draft !== null)
                    <p>This language has saved changes that visitors cannot see yet.</p>
                    <form method="POST" action="{{ route('admin.entries.publish', [$entry, 'lang' => $locale]) }}">
                        @csrf
                        <button class="a-btn a-btn-primary" type="submit">Publish saved draft</button>
                    </form>
                    <form method="POST" action="{{ route('admin.entries.discard', [$entry, 'lang' => $locale]) }}" data-confirm="Discard the saved draft? The {{ $language }} page goes back to its published version." data-confirm-title="Discard draft?" data-confirm-button="Discard draft">
                        @csrf
                        <button class="a-btn a-btn-danger" type="submit">Discard draft</button>
                    </form>
                @else
                    <p>No pending changes in {{ $language }}.</p>
                @endif
                <a class="a-btn" href="{{ route('admin.entries.history', [$entry, 'lang' => $locale]) }}">@include('admin.partials.icon', ['name' => 'history']) Version history ({{ $revisionCount }})</a>
            </section>

            @if (count($locales) > 1)
                <section class="a-side-card">
                    <h2>Translation</h2>
                    @if ($gaps['missing'] === 0 && !$gaps['unpublished'] && !$gaps['structure'])
                        <p><span class="a-badge a-badge-live">Complete</span> Every English field has a French version.</p>
                    @else
                        <ul>
                            @if ($gaps['unpublished'])<li>The French version is not published.</li>@endif
                            @if ($gaps['missing'])<li>{{ $gaps['missing'] }} French {{ $gaps['missing'] === 1 ? 'field is' : 'fields are' }} empty.</li>@endif
                            @if ($gaps['structure'])<li>Some lists have a different number of items in French and English.</li>@endif
                        </ul>
                        @if ($locale !== 'fr')
                            <a class="a-btn" style="margin-top: 10px" href="{{ route('admin.entries.edit', [$entry, 'lang' => 'fr']) }}">Open the French version</a>
                        @endif
                    @endif
                </section>
            @endif

            @if (count($panels) > 2)
                <section class="a-side-card">
                    <h2>On this page</h2>
                    <ul class="a-side-nav">
                        @foreach ($panels as $panel)
                            <li><a href="#panel-{{ $panel['id'] }}">{{ $panel['title'] }}</a></li>
                        @endforeach
                    </ul>
                </section>
            @endif

            @if ($isCollection)
                <section class="a-side-card">
                    <h2>Visibility</h2>
                    @if ($row->published !== null)
                        <form method="POST" action="{{ route('admin.entries.unpublish', [$entry, 'lang' => $locale]) }}" data-confirm="Take this {{ Registry::COLLECTIONS[$entry->type]['singular'] }} offline in {{ $language }}? Its content is kept as a draft." data-confirm-title="Unpublish?" data-confirm-button="Unpublish">
                            @csrf
                            <button class="a-btn" type="submit">Unpublish {{ $language }}</button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('admin.entries.destroy', $entry) }}" data-confirm="Move “{{ $label }}” to the bin? It disappears from the website in every language. You can restore it later." data-confirm-title="Delete this {{ Registry::COLLECTIONS[$entry->type]['singular'] }}?" data-confirm-button="Delete">
                        @csrf
                        @method('DELETE')
                        <button class="a-btn a-btn-danger" type="submit">@include('admin.partials.icon', ['name' => 'trash']) Delete</button>
                    </form>
                </section>
            @elseif ($entry->is_required)
                <section class="a-side-card">
                    <h2>Protected</h2>
                    <p>This is a core part of the website, so it can be edited but not deleted.</p>
                </section>
            @endif
        </aside>
    </div>
@endsection
