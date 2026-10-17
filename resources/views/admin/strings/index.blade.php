@extends('admin.layout')

@section('title', 'Interface text')

@section('crumbs')
    <span aria-hidden="true">/</span>
    <span>Site settings</span>
    <span aria-hidden="true">/</span>
    <span aria-current="page">Interface text</span>
@endsection

@section('content')
    <div class="a-page-head">
        <div>
            <span class="a-eyebrow">Site settings</span>
            <h1>Interface text</h1>
            <p>Section headings, buttons, labels and form messages that are part of the page layouts. Leave a field empty to use the original text. Changes are live as soon as you save.</p>
        </div>
    </div>

    <form class="a-filters" method="GET" action="{{ route('admin.strings.index') }}" role="search">
        <div class="a-field a-search">
            <label class="a-label" for="q">Search</label>
            <input class="a-input" id="q" type="search" name="q" value="{{ $filters['q'] }}" placeholder="Find text in English or French">
        </div>
        <div class="a-field">
            <label class="a-label" for="area">Page</label>
            <select class="a-select" id="area" name="area">
                <option value="">All pages</option>
                @foreach ($areas as $area)
                    <option value="{{ $area }}" @selected($filters['area'] === $area)>{{ $area }}</option>
                @endforeach
            </select>
        </div>
        <div class="a-field">
            <label class="a-label" for="status">Show</label>
            <select class="a-select" id="status" name="status">
                <option value="">All text</option>
                <option value="missing" @selected($filters['status'] === 'missing')>Missing French</option>
                <option value="edited" @selected($filters['status'] === 'edited')>Edited</option>
            </select>
        </div>
        <button class="a-btn" type="submit">Filter</button>
        @if (array_filter($filters))
            <a class="a-btn a-btn-quiet" href="{{ route('admin.strings.index') }}">Clear</a>
        @endif
    </form>

    @if ($strings->isEmpty())
        <section class="a-card"><div class="a-empty"><strong>No text matches these filters</strong>Try another search or page.</div></section>
    @else
        <form method="POST" action="{{ route('admin.strings.update') }}" data-dirty-form>
            @csrf
            @method('PUT')
            <div class="a-strings">
                @foreach ($strings as $row)
                    @php($id = 's'.substr($row['hash'], 0, 12))
                    <div class="a-string">
                        <input type="hidden" name="strings[{{ $row['hash'] }}][key]" value="{{ $row['key'] }}">
                        <div class="a-string-head">
                            <p class="a-string-key">Original: <code>{{ $row['key'] }}</code></p>
                            @foreach ($row['areas'] as $area)<span class="a-badge a-badge-plain a-badge-off">{{ $area }}</span>@endforeach
                            @if ($row['en_edited'] || $row['fr_edited'])<span class="a-badge a-badge-info">Edited</span>@endif
                            @if ($row['missing_fr'])<span class="a-badge a-badge-warn">Missing French</span>@endif
                        </div>
                        <div class="a-form-row">
                            <div class="a-field">
                                <label class="a-label" for="{{ $id }}-en"><span class="a-lang">EN</span> English</label>
                                <textarea class="a-textarea" id="{{ $id }}-en" name="strings[{{ $row['hash'] }}][en]" rows="2" placeholder="{{ $row['key'] }}" data-autosize>{{ $row['en'] }}</textarea>
                            </div>
                            <div class="a-field">
                                <label class="a-label" for="{{ $id }}-fr"><span class="a-lang">FR</span> French</label>
                                <textarea class="a-textarea" id="{{ $id }}-fr" name="strings[{{ $row['hash'] }}][fr]" rows="2" data-autosize>{{ $row['fr'] }}</textarea>
                            </div>
                        </div>
                        @if ($row['has_html'])
                            <p class="a-help" style="margin-top: 8px">This text uses formatting tags: keep &lt;em&gt;…&lt;/em&gt; around the highlighted words and &lt;br&gt; for line breaks.</p>
                        @elseif (str_contains($row['key'], ':'))
                            <p class="a-help" style="margin-top: 8px">Keep words starting with a colon (such as :date or :service) unchanged; they are replaced automatically.</p>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="a-actionbar">
                <span class="a-actionbar-status" data-dirty-status>{{ $strings->total() }} {{ $strings->total() === 1 ? 'text' : 'texts' }} · page {{ $strings->currentPage() }} of {{ $strings->lastPage() }}</span>
                <div class="a-actionbar-buttons">
                    <button class="a-btn a-btn-primary" type="submit">@include('admin.partials.icon', ['name' => 'check']) Save changes</button>
                </div>
            </div>
        </form>
        <section class="a-card" style="margin-top: 14px">
            @include('admin.partials.pagination', ['paginator' => $strings])
        </section>
    @endif
@endsection
