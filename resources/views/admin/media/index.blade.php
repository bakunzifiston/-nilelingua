@extends('admin.layout')

@section('title', 'Media library')

@section('crumbs')
    <span aria-hidden="true">/</span>
    <span aria-current="page">Media library</span>
@endsection

@section('content')
    <div class="a-page-head">
        <div>
            <span class="a-eyebrow">Library</span>
            <h1>Media library</h1>
            <p>Upload images once, then choose them in any photo field (for example the founder photo on the About page). Describe each image for visitors who use screen readers.</p>
        </div>
    </div>

    <section class="a-card" aria-labelledby="upload-title">
        <div class="a-card-head"><h2 id="upload-title">Upload an image</h2></div>
        <form class="a-card-body a-upload" method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" novalidate>
            @csrf
            <div class="a-field @error('file') has-error @enderror">
                <label class="a-label" for="file">Image (JPG, PNG, WebP, GIF or SVG, up to {{ min(\App\Http\Controllers\Admin\MediaController::MAX_KB / 1024, \App\Http\Controllers\Admin\MediaController::serverUploadLimitMb()) }} MB)</label>
                <input class="a-input" id="file" type="file" name="file" accept="image/jpeg,image/png,image/webp,image/gif,image/svg+xml,.svg" required @error('file') aria-invalid="true" aria-describedby="file-error" @enderror>
                @error('file')<p class="a-error" id="file-error">{{ $message }}</p>@enderror
            </div>
            <div class="a-field">
                <label class="a-label" for="alt_en"><span class="a-lang">EN</span> Description</label>
                <input class="a-input" id="alt_en" name="alt_en" value="{{ old('alt_en') }}" maxlength="255" placeholder="What the image shows">
            </div>
            <div class="a-field">
                <label class="a-label" for="alt_fr"><span class="a-lang">FR</span> Description</label>
                <input class="a-input" id="alt_fr" name="alt_fr" value="{{ old('alt_fr') }}" maxlength="255">
            </div>
            <button class="a-btn a-btn-primary" type="submit">@include('admin.partials.icon', ['name' => 'plus']) Upload</button>
        </form>
    </section>

    <form class="a-filters" method="GET" action="{{ route('admin.media.index') }}" role="search" style="margin-top: 22px">
        <div class="a-field a-search">
            <label class="a-label" for="q">Search images</label>
            <input class="a-input" id="q" type="search" name="q" value="{{ $search }}" placeholder="File name or description">
        </div>
        <button class="a-btn" type="submit">Search</button>
        @if ($search !== '')<a class="a-btn a-btn-quiet" href="{{ route('admin.media.index') }}">Clear</a>@endif
    </form>

    @if ($media->isEmpty())
        <section class="a-card"><div class="a-empty"><strong>{{ $search !== '' ? 'No images match this search' : 'No images yet' }}</strong>{{ $search !== '' ? 'Try another word.' : 'Uploaded images appear here.' }}</div></section>
    @else
        <div class="a-media-grid">
            @foreach ($media as $item)
                <article class="a-media">
                    <img class="a-media-img" src="{{ $item->url() }}" alt="{{ $item->alt_en }}" loading="lazy">
                    <div class="a-media-body">
                        <p class="a-media-name" title="{{ $item->original_name }}">{{ $item->original_name }}</p>
                        <p class="a-media-meta">{{ $item->width && $item->height ? $item->width.' × '.$item->height.' · ' : '' }}{{ number_format($item->size / 1024, 0) }} KB · {{ $item->created_at->format('j M Y') }}</p>
                        <form method="POST" action="{{ route('admin.media.update', $item) }}">
                            @csrf
                            @method('PUT')
                            <div class="a-field">
                                <label class="a-label" for="alt-en-{{ $item->id }}"><span class="a-lang">EN</span> Description</label>
                                <input class="a-input" id="alt-en-{{ $item->id }}" name="alt_en" value="{{ $item->alt_en }}" maxlength="255">
                            </div>
                            <div class="a-field">
                                <label class="a-label" for="alt-fr-{{ $item->id }}"><span class="a-lang">FR</span> Description</label>
                                <input class="a-input" id="alt-fr-{{ $item->id }}" name="alt_fr" value="{{ $item->alt_fr }}" maxlength="255">
                            </div>
                            <div class="a-media-actions">
                                <button class="a-btn a-btn-sm" type="submit">Save</button>
                                <button class="a-btn a-btn-sm a-btn-quiet" type="button" data-copy="{{ $item->url() }}">@include('admin.partials.icon', ['name' => 'copy']) Copy link</button>
                            </div>
                        </form>
                        <form method="POST" action="{{ route('admin.media.destroy', $item) }}" data-confirm="Delete “{{ $item->original_name }}”? This cannot be undone." data-confirm-title="Delete image?" data-confirm-button="Delete image" style="margin-top: 6px">
                            @csrf
                            @method('DELETE')
                            <button class="a-btn a-btn-sm a-btn-danger" type="submit">@include('admin.partials.icon', ['name' => 'trash']) Delete</button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>
        <section class="a-card" style="margin-top: 14px">@include('admin.partials.pagination', ['paginator' => $media])</section>
    @endif
@endsection
